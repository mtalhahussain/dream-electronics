@extends('layouts.admin')

@section('title', 'Customers - Dream Electronics')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-users me-2"></i>Customers</h1>
    @can('create-customers')
    <a href="{{ route('customers.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>Add Customer
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
                        <th>CNIC</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Sales</th>
                        <th>Guarantors</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->cnic }}</td>
                        <td>{{ $customer->phone }}</td>
                        <td>{{ $customer->email ?? 'N/A' }}</td>
                        <td>
                            <span class="badge bg-info">{{ $customer->sales_count }}</span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $customer->guarantors_count }}</span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('edit-customers')
                                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                @can('delete-customers')
                                <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="d-inline" 
                                      onsubmit="return confirm('Are you sure you want to delete this customer?')">
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
                        <td colspan="7" class="text-center">No customers found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{ $customers->links() }}
    </div>
</div>
@endsection