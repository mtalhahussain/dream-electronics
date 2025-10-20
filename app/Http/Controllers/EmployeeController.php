<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Branch;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Employee::with('branch');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('position', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $employees = $query->orderBy('name')->paginate(20);
        $branches = \App\Models\Branch::all();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('employees.table', compact('employees'))->render(),
                'pagination' => (string) $employees->links()
            ]);
        }

        return view('employees.index', compact('employees', 'branches'));
    }

    public function create()
    {
        $branches = Branch::all();
        return view('employees.create', compact('branches'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'cnic' => 'required|string|max:15|unique:employees,cnic',
                'phone' => 'required|string|max:15',
                'email' => 'nullable|email|unique:employees,email',
                'position' => 'required|string|max:255',
                'salary' => 'required|numeric|min:0',
                'hire_date' => 'required|date',
                'branch_id' => 'required|exists:branches,id',
                'role' => 'required|in:employee,product_manager,sales_manager,admin',
            ]);

            // Set default permissions based on role
            $permissions = Employee::getDefaultPermissions($request->role);

            $employee = Employee::create([
                'name' => $request->name,
                'cnic' => $request->cnic,
                'phone' => $request->phone,
                'email' => $request->email,
                'position' => $request->position,
                'salary' => $request->salary,
                'hire_date' => $request->hire_date,
                'branch_id' => $request->branch_id,
                'role' => $request->role,
                'permissions' => $permissions,
                'is_active' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Employee created successfully!'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create employee: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Employee $employee)
    {
        $employee->load('branch', 'salaryPayments');
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $branches = Branch::all();
        return view('employees.edit', compact('employee', 'branches'));
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'cnic' => 'required|string|max:15|unique:employees,cnic,' . $employee->id,
            'phone' => 'required|string|max:15',
            'email' => 'nullable|email|max:255|unique:employees,email,' . $employee->id,
            'position' => 'required|string|max:255',
            'role' => 'required|in:employee,product_manager,sales_manager,admin',
            'salary' => 'required|numeric|min:0',
            'hire_date' => 'required|date',
            'branch_id' => 'required|exists:branches,id',
            'is_active' => 'boolean',
            'permissions' => 'array',
            'permissions.*' => 'string'
        ]);

        // Get default permissions for role if none selected
        $permissions = $request->permissions ?? Employee::getDefaultPermissions($request->role);

        $employee->update([
            'name' => $request->name,
            'cnic' => $request->cnic,
            'phone' => $request->phone,
            'email' => $request->email,
            'position' => $request->position,
            'role' => $request->role,
            'permissions' => $permissions,
            'salary' => $request->salary,
            'hire_date' => $request->hire_date,
            'branch_id' => $request->branch_id,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('employees.index')
            ->with('success', 'Employee updated successfully');
    }

    public function destroy(Employee $employee)
    {
        try {
            $employee->delete();

            return response()->json([
                'success' => true,
                'message' => 'Employee deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete employee: ' . $e->getMessage()
            ], 500);
        }
    }

    public function roles()
    {
        return view('employees.roles');
    }
}