<?php

namespace App\Http\Controllers;

use App\Models\SalaryPayment;
use App\Models\Employee;
use App\Models\Branch;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SalaryPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = SalaryPayment::with(['employee.branch']);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('branch_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        if ($request->filled('payment_month')) {
            $query->where('payment_month', $request->payment_month);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $salaryPayments = $query->orderBy('payment_date', 'desc')->paginate(20);
        $employees = Employee::where('is_active', true)->with('branch')->get();
        $branches = Branch::where('is_active', true)->get();

        return view('salary-payments.index', compact('salaryPayments', 'employees', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_month' => 'required|string',
            'notes' => 'nullable|string|max:500',
            'status' => 'required|in:paid,pending,partial'
        ]);

        // Check if payment already exists for this month
        $existingPayment = SalaryPayment::where('employee_id', $request->employee_id)
            ->where('payment_month', $request->payment_month)
            ->first();

        if ($existingPayment) {
            return response()->json([
                'success' => false,
                'message' => 'Salary payment for this month already exists!'
            ], 422);
        }

        SalaryPayment::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Salary payment recorded successfully!'
        ]);
    }

    public function update(Request $request, SalaryPayment $salaryPayment)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_month' => 'required|string',
            'notes' => 'nullable|string|max:500',
            'status' => 'required|in:paid,pending,partial'
        ]);

        // Check if payment already exists for this month (excluding current record)
        $existingPayment = SalaryPayment::where('employee_id', $request->employee_id)
            ->where('payment_month', $request->payment_month)
            ->where('id', '!=', $salaryPayment->id)
            ->first();

        if ($existingPayment) {
            return response()->json([
                'success' => false,
                'message' => 'Salary payment for this month already exists!'
            ], 422);
        }

        $salaryPayment->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Salary payment updated successfully!'
        ]);
    }

    public function destroy(SalaryPayment $salaryPayment)
    {
        $salaryPayment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Salary payment deleted successfully!'
        ]);
    }

    public function getSalaryPayment(SalaryPayment $salaryPayment)
    {
        return response()->json([
            'success' => true,
            'salaryPayment' => $salaryPayment->load(['employee.branch'])
        ]);
    }

    public function generateMonthlyPayments(Request $request)
    {
        $request->validate([
            'payment_month' => 'required|string',
            'branch_id' => 'nullable|exists:branches,id'
        ]);

        $query = Employee::where('is_active', true);
        
        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        $employees = $query->get();
        $created = 0;

        foreach ($employees as $employee) {
            // Check if payment already exists
            $existingPayment = SalaryPayment::where('employee_id', $employee->id)
                ->where('payment_month', $request->payment_month)
                ->first();

            if (!$existingPayment) {
                SalaryPayment::create([
                    'employee_id' => $employee->id,
                    'amount' => $employee->salary,
                    'payment_date' => Carbon::now(),
                    'payment_month' => $request->payment_month,
                    'status' => 'pending'
                ]);
                $created++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Generated {$created} salary payments for {$request->payment_month}"
        ]);
    }
}