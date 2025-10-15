<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view-employees')->only(['index', 'show']);
        $this->middleware('can:create-employees')->only(['create', 'store']);
        $this->middleware('can:edit-employees')->only(['edit', 'update']);
        $this->middleware('can:delete-employees')->only(['destroy']);
    }

    public function index()
    {
        $employees = Employee::with('branch')
            ->orderBy('name')
            ->paginate(15);

        return view('employees.index', compact('employees'));
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        // Implementation for store method
        return redirect()->route('employees.index')
            ->with('success', 'Employee created successfully');
    }

    public function show(Employee $employee)
    {
        $employee->load('branch');
        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee)
    {
        // Implementation for update method
        return redirect()->route('employees.index')
            ->with('success', 'Employee updated successfully');
    }

    public function destroy(Employee $employee)
    {
        // Implementation for destroy method
        return redirect()->route('employees.index')
            ->with('success', 'Employee deleted successfully');
    }
}