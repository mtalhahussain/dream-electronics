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
                  ->orWhere('account_number', 'like', "%{$search}%")
                  ->orWhere('profession', 'like', "%{$search}%")
                  ->orWhere('father_husband_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('name')->paginate(15);
        $branches = Branch::where('is_active', true)->orderBy('name')->get();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('customers.table', compact('customers'))->render(),
                'pagination' => $customers->links()->toHtml()
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

            // Extract guarantor data before creating customer
            $guarantors = [];
            
            // Handle Guarantor 1
            if (!empty($data['guarantor_1_name'])) {
                $guarantor1Data = [
                    'name' => $data['guarantor_1_name'],
                    'cnic' => $data['guarantor_1_cnic'] ?? null,
                    'phone' => $data['guarantor_1_phone'] ?? null,
                    'address' => $data['guarantor_1_address'] ?? null,
                    'relationship' => $data['guarantor_1_relationship'] ?? null,
                    'profession' => $data['guarantor_1_profession'] ?? null,
                    'father_husband_name' => $data['guarantor_1_father_husband_name'] ?? null,
                ];
                
                // Handle guarantor 1 biometric upload
                if ($request->hasFile('guarantor_1_biometric')) {
                    $guarantor1Data['biometric_path'] = $request->file('guarantor_1_biometric')
                        ->store('guarantor-biometrics', 'public');
                }
                
                $guarantors[] = $guarantor1Data;
            }
            
            // Handle Guarantor 2
            if (!empty($data['guarantor_2_name'])) {
                $guarantor2Data = [
                    'name' => $data['guarantor_2_name'],
                    'cnic' => $data['guarantor_2_cnic'] ?? null,
                    'phone' => $data['guarantor_2_phone'] ?? null,
                    'address' => $data['guarantor_2_address'] ?? null,
                    'relationship' => $data['guarantor_2_relationship'] ?? null,
                    'profession' => $data['guarantor_2_profession'] ?? null,
                    'father_husband_name' => $data['guarantor_2_father_husband_name'] ?? null,
                ];
                
                // Handle guarantor 2 biometric upload
                if ($request->hasFile('guarantor_2_biometric')) {
                    $guarantor2Data['biometric_path'] = $request->file('guarantor_2_biometric')
                        ->store('guarantor-biometrics', 'public');
                }
                
                $guarantors[] = $guarantor2Data;
            }

            // Legacy guarantor support (for backward compatibility)
            if (!empty($data['guarantor_name']) && empty($guarantors)) {
                $guarantorData = [
                    'name' => $data['guarantor_name'],
                    'cnic' => $data['guarantor_cnic'] ?? null,
                    'phone' => $data['guarantor_phone'] ?? null,
                    'address' => $data['guarantor_address'] ?? null,
                    'relationship' => $data['guarantor_relation'] ?? null,
                ];
                $guarantors[] = $guarantorData;
            }

            // Remove guarantor fields from customer data
            $customerData = array_filter($data, function($key) {
                return !str_starts_with($key, 'guarantor_');
            }, ARRAY_FILTER_USE_KEY);

            // Handle biometric file upload
            if ($request->hasFile('biometric')) {
                $customerData['biometric_path'] = $request->file('biometric')
                    ->store('customer-biometrics', 'public');
            }

            // Handle face photo upload
            if ($request->hasFile('face_photo')) {
                $customerData['face_path'] = $request->file('face_photo')
                    ->store('customer-faces', 'public');
            }

            $customer = Customer::create($customerData);

            // Create guarantors
            foreach ($guarantors as $guarantorData) {
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

            // Extract guarantor data before updating customer
            $guarantors = [];
            
            // Handle Guarantor 1
            if (!empty($data['guarantor_1_name'])) {
                $guarantor1Data = [
                    'name' => $data['guarantor_1_name'],
                    'cnic' => $data['guarantor_1_cnic'] ?? null,
                    'phone' => $data['guarantor_1_phone'] ?? null,
                    'address' => $data['guarantor_1_address'] ?? null,
                    'relationship' => $data['guarantor_1_relationship'] ?? null,
                    'profession' => $data['guarantor_1_profession'] ?? null,
                    'father_husband_name' => $data['guarantor_1_father_husband_name'] ?? null,
                ];
                
                // Handle guarantor 1 biometric upload
                if ($request->hasFile('guarantor_1_biometric')) {
                    $guarantor1Data['biometric_path'] = $request->file('guarantor_1_biometric')
                        ->store('guarantor-biometrics', 'public');
                }
                
                $guarantors[] = $guarantor1Data;
            }
            
            // Handle Guarantor 2
            if (!empty($data['guarantor_2_name'])) {
                $guarantor2Data = [
                    'name' => $data['guarantor_2_name'],
                    'cnic' => $data['guarantor_2_cnic'] ?? null,
                    'phone' => $data['guarantor_2_phone'] ?? null,
                    'address' => $data['guarantor_2_address'] ?? null,
                    'relationship' => $data['guarantor_2_relationship'] ?? null,
                    'profession' => $data['guarantor_2_profession'] ?? null,
                    'father_husband_name' => $data['guarantor_2_father_husband_name'] ?? null,
                ];
                
                // Handle guarantor 2 biometric upload
                if ($request->hasFile('guarantor_2_biometric')) {
                    $guarantor2Data['biometric_path'] = $request->file('guarantor_2_biometric')
                        ->store('guarantor-biometrics', 'public');
                }
                
                $guarantors[] = $guarantor2Data;
            }

            // Legacy guarantor support (for backward compatibility)
            if (!empty($data['guarantor_name']) && empty($guarantors)) {
                $guarantorData = [
                    'name' => $data['guarantor_name'],
                    'cnic' => $data['guarantor_cnic'] ?? null,
                    'phone' => $data['guarantor_phone'] ?? null,
                    'address' => $data['guarantor_address'] ?? null,
                    'relationship' => $data['guarantor_relation'] ?? null,
                ];
                $guarantors[] = $guarantorData;
            }

            // Remove guarantor fields from customer data
            $customerData = array_filter($data, function($key) {
                return !str_starts_with($key, 'guarantor_');
            }, ARRAY_FILTER_USE_KEY);

            // Handle biometric file upload
            if ($request->hasFile('biometric')) {
                // Delete old file if exists
                if ($customer->biometric_path) {
                    Storage::disk('public')->delete($customer->biometric_path);
                }
                
                $customerData['biometric_path'] = $request->file('biometric')
                    ->store('customer-biometrics', 'public');
            }

            // Handle face photo upload
            if ($request->hasFile('face_photo')) {
                // Delete old file if exists
                if ($customer->face_path) {
                    Storage::disk('public')->delete($customer->face_path);
                }
                
                $customerData['face_path'] = $request->file('face_photo')
                    ->store('customer-faces', 'public');
            }

            $customer->update($customerData);

            // Delete existing guarantors and create new ones
            $customer->guarantors()->delete();
            
            // Create new guarantors
            foreach ($guarantors as $guarantorData) {
                $customer->guarantors()->create($guarantorData);
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
            
            return response()->json([
                'success' => true,
                'customer' => [
                    'id' => $customer->id,
                    'branch_id' => $customer->branch_id,
                    'account_number' => $customer->account_number,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'cnic' => $customer->cnic,
                    'profession' => $customer->profession,
                    'father_husband_name' => $customer->father_husband_name,
                    'address' => $customer->address,
                    'is_active' => $customer->is_active,
                    'guarantors' => $customer->guarantors->map(function ($guarantor) {
                        return [
                            'id' => $guarantor->id,
                            'account_number' => $guarantor->account_number,
                            'name' => $guarantor->name,
                            'email' => $guarantor->email,
                            'phone' => $guarantor->phone,
                            'cnic' => $guarantor->cnic,
                            'profession' => $guarantor->profession,
                            'father_husband_name' => $guarantor->father_husband_name,
                            'relation' => $guarantor->relation,
                            'address' => $guarantor->address,
                            'biometric_path' => $guarantor->biometric_path,
                        ];
                    })
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