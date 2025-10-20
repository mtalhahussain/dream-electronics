@extends('layouts.admin')

@section('title', 'Employee Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-person-circle me-2"></i>Employee Details</h1>
    <div>
        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning me-2">
            <i class="bi bi-pencil me-2"></i>Edit Employee
        </a>
        <a href="{{ route('employees.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Employees
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Employee Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Personal Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold">Full Name:</td>
                                <td>{{ $employee->name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">CNIC:</td>
                                <td>{{ $employee->cnic }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Phone:</td>
                                <td>{{ $employee->phone }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Email:</td>
                                <td>{{ $employee->email ?: 'Not provided' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold">Position:</td>
                                <td>{{ $employee->position }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Role:</td>
                                <td>
                                    <span class="badge {{ $employee->role == 'admin' ? 'bg-danger' : ($employee->role == 'product_manager' || $employee->role == 'sales_manager' ? 'bg-warning' : 'bg-secondary') }}">
                                        {{ $employee->role_display }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Branch:</td>
                                <td>{{ $employee->branch->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Monthly Salary:</td>
                                <td><strong>Rs. {{ number_format($employee->salary, 0) }}</strong></td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Hire Date:</td>
                                <td>{{ $employee->hire_date->format('d-M-Y') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Salary Payment History -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-cash-stack me-2"></i>Salary Payment History</h5>
                <a href="{{ route('salary-payments.index', ['employee_id' => $employee->id]) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-eye me-2"></i>View All Payments
                </a>
            </div>
            <div class="card-body">
                @if($employee->salaryPayments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Payment Month</th>
                                    <th>Payment Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employee->salaryPayments->take(5) as $payment)
                                <tr>
                                    <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $payment->payment_month)->format('M Y') }}</td>
                                    <td>{{ $payment->payment_date->format('d-M-Y') }}</td>
                                    <td><strong>Rs. {{ number_format($payment->amount, 0) }}</strong></td>
                                    <td>
                                        @switch($payment->status)
                                            @case('paid')
                                                <span class="badge bg-success">Paid</span>
                                                @break
                                            @case('pending')
                                                <span class="badge bg-warning">Pending</span>
                                                @break
                                            @case('partial')
                                                <span class="badge bg-info">Partial</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>{{ $payment->notes ?: 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if($employee->salaryPayments->count() > 5)
                            <p class="text-muted text-center">Showing latest 5 payments. <a href="{{ route('salary-payments.index', ['employee_id' => $employee->id]) }}">View all payments</a></p>
                        @endif
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-cash-stack fs-1 d-block mb-2"></i>
                        No salary payments recorded yet
                    </div>
                @endif
            </div>
        </div>

        <!-- Employee Permissions -->
        @if($employee->role !== 'employee' && $employee->permissions)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-shield-check me-2"></i>Permissions & Access</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($employee->permissions as $permission)
                        @if(isset(\App\Models\Employee::PERMISSIONS[$permission]))
                            <div class="col-md-12 mb-2">
                                <i class="bi bi-check-circle text-success me-2"></i>
                                {{ \App\Models\Employee::PERMISSIONS[$permission] }}
                            </div>
                        @endif
                    @endforeach
                </div>
                @if($employee->role === 'admin')
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Admin users have access to all system features automatically.</strong>
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-md-4">
        <!-- Employee Status -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Employee Status</h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <div class="mb-3">
                        <span class="badge fs-6 {{ $employee->is_active ? 'bg-success' : 'bg-danger' }}">
                            {{ $employee->is_active ? 'ACTIVE' : 'INACTIVE' }}
                        </span>
                    </div>
                    <p class="mb-0">
                        <strong>Employment Duration:</strong><br>
                        {{ $employee->hire_date->diffForHumans() }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-2"></i>Edit Employee
                    </a>
                    <a href="{{ route('salary-payments.index', ['employee_id' => $employee->id]) }}" class="btn btn-success">
                        <i class="bi bi-cash-stack me-2"></i>View Salary Payments
                    </a>
                    <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Are you sure you want to delete this employee? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-trash me-2"></i>Delete Employee
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Summary</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li><i class="bi bi-calendar-check text-primary me-2"></i>
                        <strong>{{ $employee->salaryPayments->where('status', 'paid')->count() }}</strong> Paid Salaries
                    </li>
                    <li><i class="bi bi-calendar-x text-warning me-2"></i>
                        <strong>{{ $employee->salaryPayments->where('status', 'pending')->count() }}</strong> Pending Salaries
                    </li>
                    <li><i class="bi bi-cash text-success me-2"></i>
                        <strong>Rs. {{ number_format($employee->salaryPayments->sum('amount'), 0) }}</strong> Total Paid
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection