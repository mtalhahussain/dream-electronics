@extends('layouts.admin')

@section('title', 'Customers')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="bi bi-people me-2"></i>Customers Management
        </h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#customerModal">
            <i class="bi bi-plus-circle me-2"></i>Add New Customer
        </button>
    </div>
    
    <!-- Filter Toolbar -->
    <div class="card-body border-bottom bg-light">
        <div class="row g-3">
            <div class="col-md-3">
                <select class="form-select form-select-sm" id="branchFilter">
                    <option value="">All Branches</option>
                    <!-- Will be populated via server-side data -->
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-sm" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" class="form-control form-control-sm" id="dateFilter" placeholder="Registration Date">
            </div>
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" placeholder="Search customers..." id="searchFilter">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        @if(($customers ?? collect())->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>CNIC</th>
                            <th>Address</th>
                            <th>Guarantor</th>
                            <th>Registration</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                        {{ strtoupper(substr($customer->name ?? '', 0, 1)) }}
                                    </div>
                                    <div>
                                        <strong>{{ $customer->name ?? 'N/A' }}</strong>
                                        @if($customer->email ?? false)
                                            <br><small class="text-muted">{{ $customer->email }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="tel:{{ $customer->phone ?? '' }}" class="text-decoration-none">
                                    {{ $customer->phone ?? 'N/A' }}
                                </a>
                            </td>
                            <td>
                                <code class="small">{{ $customer->cnic ?? 'N/A' }}</code>
                            </td>
                            <td>
                                <small>{{ Str::limit($customer->address ?? 'N/A', 30) }}</small>
                            </td>
                            <td>
                                @if($customer->guarantor_name ?? false)
                                    <strong>{{ $customer->guarantor_name }}</strong>
                                    @if($customer->guarantor_phone ?? false)
                                        <br><small class="text-muted">{{ $customer->guarantor_phone }}</small>
                                    @endif
                                @else
                                    <span class="text-muted">No guarantor</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ \Carbon\Carbon::parse($customer->created_at ?? now())->format('d-M-Y') }}</small>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('customers.show', $customer) }}">
                                            <i class="bi bi-eye me-2"></i>View Details
                                        </a></li>
                                        <li><a class="dropdown-item" href="#" onclick="editCustomer({{ $customer->id }})">
                                            <i class="bi bi-pencil me-2"></i>Edit
                                        </a></li>
                                        <li><a class="dropdown-item" href="#" onclick="viewSales({{ $customer->id }})">
                                            <i class="bi bi-cart3 me-2"></i>View Sales
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteCustomer({{ $customer->id }})">
                                            <i class="bi bi-trash me-2"></i>Delete
                                        </a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-people display-4 text-muted"></i>
                <p class="text-muted mt-3">No customers found</p>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#customerModal">
                    <i class="bi bi-plus-circle me-2"></i>Add First Customer
                </button>
            </div>
        @endif
    </div>
    
    @if(isset($customers) && method_exists($customers, 'hasPages') && $customers->hasPages())
    <div class="card-footer bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Showing {{ $customers->firstItem() }} to {{ $customers->lastItem() }} of {{ $customers->total() }} results
            </div>
            {{ $customers->links() }}
        </div>
    </div>
    @endif
</div>

<!-- Customer Modal -->
<div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customerModalLabel">Add New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="customerForm" action="{{ route('customers.store') }}" method="POST">
                <div class="modal-body">
                    @csrf
                    <div class="row g-3">
                        <!-- Customer Information -->
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">Customer Information</h6>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="name" name="name" placeholder="Full Name" required>
                                <label for="name">Full Name <span class="text-danger">*</span></label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email Address">
                                <label for="email">Email Address</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">+92</span>
                                <input type="tel" class="form-control" id="phone" name="phone" placeholder="3001234567" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="cnic" class="form-label">CNIC <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="cnic" name="cnic" placeholder="12345-6789012-3" pattern="[0-9]{5}-[0-9]{7}-[0-9]{1}" required>
                            <div class="form-text">Format: 12345-6789012-3</div>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea class="form-control" id="address" name="address" placeholder="Complete Address" style="height: 80px" required></textarea>
                                <label for="address">Complete Address <span class="text-danger">*</span></label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <!-- Guarantor Information -->
                        <div class="col-12 mt-4">
                            <h6 class="text-secondary border-bottom pb-2 mb-3">Guarantor Information</h6>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="guarantor_name" name="guarantor_name" placeholder="Guarantor Name">
                                <label for="guarantor_name">Guarantor Name</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="guarantor_phone" class="form-label">Guarantor Phone</label>
                            <div class="input-group">
                                <span class="input-group-text">+92</span>
                                <input type="tel" class="form-control" id="guarantor_phone" name="guarantor_phone" placeholder="3001234567">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="guarantor_cnic" class="form-label">Guarantor CNIC</label>
                            <input type="text" class="form-control" id="guarantor_cnic" name="guarantor_cnic" placeholder="12345-6789012-3" pattern="[0-9]{5}-[0-9]{7}-[0-9]{1}">
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="guarantor_relation" name="guarantor_relation" placeholder="Relation">
                                <label for="guarantor_relation">Relation to Customer</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea class="form-control" id="guarantor_address" name="guarantor_address" placeholder="Guarantor Address" style="height: 60px"></textarea>
                                <label for="guarantor_address">Guarantor Address</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Save Customer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Warning!</strong> Deleting this customer will also affect their sales records and installments.
                </div>
                <p>Are you sure you want to delete this customer? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Customer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function editCustomer(customerId) {
        // Implementation for editing customer
        showToast('Edit functionality will be implemented soon', 'info');
    }
    
    function viewSales(customerId) {
        // Navigate to sales filtered by customer
        window.location.href = `/sales?customer_id=${customerId}`;
    }
    
    function deleteCustomer(customerId) {
        document.getElementById('deleteForm').action = `/customers/${customerId}`;
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
    
    // Form submission with validation
    document.getElementById('customerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Customer saved successfully!', 'success');
                bootstrap.Modal.getInstance(document.getElementById('customerModal')).hide();
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('Error: ' + (data.message || 'Failed to save customer'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred while saving the customer.', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Save Customer';
        });
    });
    
    // CNIC formatting
    document.getElementById('cnic').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 5) {
            value = value.substring(0, 5) + '-' + value.substring(5);
        }
        if (value.length >= 13) {
            value = value.substring(0, 13) + '-' + value.substring(13, 14);
        }
        e.target.value = value;
    });
    
    document.getElementById('guarantor_cnic').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 5) {
            value = value.substring(0, 5) + '-' + value.substring(5);
        }
        if (value.length >= 13) {
            value = value.substring(0, 13) + '-' + value.substring(13, 14);
        }
        e.target.value = value;
    });
    
    // Phone formatting
    document.getElementById('phone').addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/\D/g, '').substring(0, 10);
    });
    
    document.getElementById('guarantor_phone').addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/\D/g, '').substring(0, 10);
    });
</script>
@endpush