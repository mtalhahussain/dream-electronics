@extends('layouts.admin')

@section('title', 'Roles & Permissions')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-shield-check me-2"></i>Roles & Permissions Overview</h1>
    <a href="{{ route('employees.index') }}" class="btn btn-primary">
        <i class="bi bi-people me-2"></i>Manage Employees
    </a>
</div>

<!-- Role Statistics -->
<div class="row mb-4">
    @foreach(\App\Models\Employee::getRoles() as $roleKey => $roleLabel)
        @php
            $count = \App\Models\Employee::where('role', $roleKey)->where('is_active', true)->count();
            $badgeClass = $roleKey == 'admin' ? 'bg-danger' : ($roleKey == 'product_manager' || $roleKey == 'sales_manager' ? 'bg-warning' : 'bg-secondary');
        @endphp
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <div class="d-flex justify-content-center mb-2">
                        <span class="badge {{ $badgeClass }} fs-6 px-3 py-2">{{ $roleLabel }}</span>
                    </div>
                    <h4 class="card-title">{{ $count }}</h4>
                    <p class="card-text text-muted">Active {{ $roleLabel }}{{ $count != 1 ? 's' : '' }}</p>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Roles and Permissions Matrix -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Roles & Permissions Matrix</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Permission</th>
                        <th class="text-center">Employee</th>
                        <th class="text-center">Product Manager</th>
                        <th class="text-center">Sales Manager</th>
                        <th class="text-center">Admin</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\App\Models\Employee::PERMISSIONS as $key => $label)
                        <tr>
                            <td><strong>{{ $label }}</strong></td>
                            @foreach(['employee', 'product_manager', 'sales_manager', 'admin'] as $role)
                                <td class="text-center">
                                    @if(in_array($key, \App\Models\Employee::getDefaultPermissions($role)))
                                        <i class="bi bi-check-circle text-success fs-5"></i>
                                    @else
                                        <i class="bi bi-x-circle text-muted fs-5"></i>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Active Employees by Role -->
<div class="row mt-4">
    @foreach(\App\Models\Employee::getRoles() as $roleKey => $roleLabel)
        @php
            $employees = \App\Models\Employee::with('branch')
                ->where('role', $roleKey)
                ->where('is_active', true)
                ->get();
        @endphp
        @if($employees->count() > 0)
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">{{ $roleLabel }}s ({{ $employees->count() }})</h6>
                    <span class="badge {{ $roleKey == 'admin' ? 'bg-danger' : ($roleKey == 'product_manager' || $roleKey == 'sales_manager' ? 'bg-warning' : 'bg-secondary') }}">
                        {{ $roleLabel }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($employees as $employee)
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <strong>{{ $employee->name }}</strong>
                                    <br><small class="text-muted">{{ $employee->position }} - {{ $employee->branch->name ?? 'N/A' }}</small>
                                </div>
                                <div>
                                    <a href="{{ route('employees.show', $employee) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endforeach
</div>
@endsection