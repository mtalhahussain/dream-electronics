@extends('layouts.admin')

@section('title', 'Employees - Dream Electronics')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-user-tie me-2"></i>Employees</h1>
    @can('create-employees')
    <a href="{{ route('employees.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Add Employee
    </a>
    @endcan
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Branch</th>
                        <th>Phone</th>
                        <th>Salary</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                    <tr>
                        <td>{{ $employee->name }}</td>
                        <td>{{ $employee->position }}</td>
                        <td>{{ $employee->branch->name }}</td>
                        <td>{{ $employee->phone }}</td>
                        <td>Rs. {{ number_format($employee->salary, 2) }}</td>
                        <td>
                            <span class="badge {{ $employee->is_active ? 'bg-success' : 'bg-danger' }}">
                                {{ $employee->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('employees.show', $employee) }}" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('edit-employees')
                                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('delete-employees')
                                <form method="POST" action="{{ route('employees.destroy', $employee) }}" class="d-inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this employee?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No employees found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{ $employees->links() }}
    </div>
</div>
@endsection