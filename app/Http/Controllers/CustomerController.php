<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use App\Models\Branch;
use App\Models\Guarantor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view-customers')->only(['index', 'show']);
        $this->middleware('can:create-customers')->only(['create', 'store']);
        $this->middleware('can:edit-customers')->only(['edit', 'update']);
        $this->middleware('can:delete-customers')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Customer::with(['branch', 'guarantors'])
            ->withCount(['sales', 'guarantors']);

        // Filter by branch
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Filter by status (using is_active field)
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter by registration date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('cnic', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('name')->paginate(15);
        $branches = Branch::where('is_active', true)->orderBy('name')->get();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('customers.table', compact('customers'))->render(),
                'pagination' => $customers->links()->render()
            ]);
        }

        return view('customers.index', compact('customers', 'branches'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(StoreCustomerRequest $request)
    {
        try {
            
            $data = $request->validated();

            $guarantorData = [];
            if (!empty($data['guarantor_name'])) {
                $guarantorData = [
                    'name' => $data['guarantor_name'],
                    'cnic' => $data['guarantor_cnic'] ?? null,
                    'phone' => $data['guarantor_phone'] ?? null,
                    'address' => $data['guarantor_address'] ?? null,
                    'relationship' => $data['guarantor_relation'] ?? null,
                ];
                
                // Remove guarantor fields from customer data
                unset($data['guarantor_name'], $data['guarantor_cnic'], $data['guarantor_phone'], 
                      $data['guarantor_address'], $data['guarantor_relation']);
            }

            // Handle biometric file upload
            if ($request->hasFile('biometric')) {
                $data['biometric_path'] = $request->file('biometric')
                    ->store('customer-biometrics', 'public');
            }

            // Handle face photo upload
            if ($request->hasFile('face_photo')) {
                $data['face_path'] = $request->file('face_photo')
                    ->store('customer-faces', 'public');
            }

            $customer = Customer::create($data);

            // Create guarantor if data exists
            if (!empty($guarantorData)) {
                $customer->guarantors()->create($guarantorData);
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Customer created successfully',
                    'customer' => $customer->load(['branch', 'guarantors'])
                ]);
            }

            return redirect()->route('customers.index')
                ->with('success', 'Customer created successfully');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create customer: ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withErrors(['error' => 'Failed to create customer: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(Customer $customer)
    {
        $customer->load(['guarantors', 'sales.saleItems', 'branch']);
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        return view('customers.show', compact('customer', 'branches'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(StoreCustomerRequest $request, Customer $customer)
    {
        try {
            $data = $request->validated();

            // Extract guarantor data
            $guarantorData = [];
            if (!empty($data['guarantor_name'])) {
                $guarantorData = [
                    'name' => $data['guarantor_name'],
                    'cnic' => $data['guarantor_cnic'] ?? null,
                    'phone' => $data['guarantor_phone'] ?? null,
                    'address' => $data['guarantor_address'] ?? null,
                    'relationship' => $data['guarantor_relation'] ?? null,
                ];
                
                // Remove guarantor fields from customer data
                unset($data['guarantor_name'], $data['guarantor_cnic'], $data['guarantor_phone'], 
                      $data['guarantor_address'], $data['guarantor_relation']);
            }

            // Handle biometric file upload
            if ($request->hasFile('biometric')) {
                // Delete old file if exists
                if ($customer->biometric_path) {
                    Storage::disk('public')->delete($customer->biometric_path);
                }
                
                $data['biometric_path'] = $request->file('biometric')
                    ->store('customer-biometrics', 'public');
            }

            // Handle face photo upload
            if ($request->hasFile('face_photo')) {
                // Delete old file if exists
                if ($customer->face_path) {
                    Storage::disk('public')->delete($customer->face_path);
                }
                
                $data['face_path'] = $request->file('face_photo')
                    ->store('customer-faces', 'public');
            }

            $customer->update($data);

            // Update or create guarantor
            if (!empty($guarantorData)) {
                $customer->guarantors()->updateOrCreate([], $guarantorData);
            } else {
                // Remove guarantor if no data provided
                $customer->guarantors()->delete();
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Customer updated successfully',
                    'customer' => $customer->load(['branch', 'guarantors'])
                ]);
            }

            return redirect()->route('customers.index')
                ->with('success', 'Customer updated successfully');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update customer: ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withErrors(['error' => 'Failed to update customer: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function getCustomer(Customer $customer, Request $request)
    {
        if ($request->ajax()) {
            $customer->load(['branch', 'guarantors']);
            $guarantor = $customer->guarantors->first();
            
            return response()->json([
                'success' => true,
                'customer' => [
                    'id' => $customer->id,
                    'branch_id' => $customer->branch_id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'cnic' => $customer->cnic,
                    'address' => $customer->address,
                    'is_active' => $customer->is_active,
                    'guarantor_name' => $guarantor?->guarantor_name,
                    'guarantor_phone' => $guarantor?->guarantor_phone,
                    'guarantor_cnic' => $guarantor?->guarantor_cnic,
                    'guarantor_address' => $guarantor?->guarantor_address,
                    'guarantor_relation' => $guarantor?->guarantor_relation,
                ]
            ]);
        }
        
        return redirect()->route('customers.index');
    }

    public function destroy(Customer $customer)
    {
        try {
            // Check if customer has sales
            if ($customer->sales()->exists()) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cannot delete customer with existing sales'
                    ], 422);
                }
                
                return redirect()->back()
                    ->withErrors(['error' => 'Cannot delete customer with existing sales']);
            }

            // Delete files if they exist
            if ($customer->biometric_path) {
                Storage::disk('public')->delete($customer->biometric_path);
            }
            
            if ($customer->face_path) {
                Storage::disk('public')->delete($customer->face_path);
            }

            $customer->delete();

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Customer deleted successfully'
                ]);
            }

            return redirect()->route('customers.index')
                ->with('success', 'Customer deleted successfully');

        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete customer: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withErrors(['error' => 'Failed to delete customer: ' . $e->getMessage()]);
        }
    }
}