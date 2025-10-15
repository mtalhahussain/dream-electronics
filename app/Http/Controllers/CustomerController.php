<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
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

    public function index()
    {
        $customers = Customer::withCount(['sales', 'guarantors'])
            ->orderBy('name')
            ->paginate(15);

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(StoreCustomerRequest $request)
    {
        try {
            $data = $request->validated();

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

            return redirect()->route('customers.index')
                ->with('success', 'Customer created successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create customer: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(Customer $customer)
    {
        $customer->load(['guarantors', 'sales.installments']);
        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(StoreCustomerRequest $request, Customer $customer)
    {
        try {
            $data = $request->validated();

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

            return redirect()->route('customers.index')
                ->with('success', 'Customer updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update customer: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(Customer $customer)
    {
        try {
            // Check if customer has sales
            if ($customer->sales()->exists()) {
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

            return redirect()->route('customers.index')
                ->with('success', 'Customer deleted successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to delete customer: ' . $e->getMessage()]);
        }
    }
}