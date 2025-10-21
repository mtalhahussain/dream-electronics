@extends('layouts.admin')

@section('title', 'Customer Details')

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- Customer Information Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-person me-2"></i>Customer Information
                </h5>
                <div>
                    @if($customer->is_active)
                        <span class="badge bg-success me-2">Active</span>
                    @else
                        <span class="badge bg-danger me-2">Inactive</span>
                    @endif
                    @can('edit-customers')
                    <button class="btn btn-primary btn-sm" onclick="editCustomer({{ $customer->id }})">
                        <i class="bi bi-pencil me-1"></i>Edit
                    </button>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Name:</th>
                                <td>{{ $customer->name }}</td>
                            </tr>
                            <tr>
                                <th>CNIC:</th>
                                <td><code>{{ $customer->cnic }}</code></td>
                            </tr>
                            <tr>
                                <th>Phone:</th>
                                <td><a href="tel:{{ $customer->phone }}" class="text-decoration-none">{{ $customer->phone }}</a></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td>
                                    @if($customer->email)
                                        <a href="mailto:{{ $customer->email }}" class="text-decoration-none">{{ $customer->email }}</a>
                                    @else
                                        <span class="text-muted">Not provided</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Branch:</th>
                                <td>
                                    @if($customer->branch)
                                        <span class="badge bg-primary">{{ $customer->branch->name }}</span>
                                    @else
                                        <span class="text-muted">No branch assigned</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Address:</th>
                                <td>{{ $customer->address }}</td>
                            </tr>
                            <tr>
                                <th>Registration Date:</th>
                                <td>{{ $customer->created_at->format('d M Y, g:i A') }}</td>
                            </tr>
                            <tr>
                                <th>Last Updated:</th>
                                <td>{{ $customer->updated_at->format('d M Y, g:i A') }}</td>
                            </tr>
                            <tr>
                                <th>Total Sales:</th>
                                <td>
                                    <span class="badge bg-info">{{ $customer->sales->count() }} Sales</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    @if($customer->is_active)
                                        <span class="text-success"><i class="bi bi-check-circle"></i> Active</span>
                                    @else
                                        <span class="text-danger"><i class="bi bi-x-circle"></i> Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- Photos Section -->
                @if($customer->biometric_path || $customer->face_path)
                <hr>
                <h6 class="text-muted mb-3">Photos</h6>
                <div class="row">
                    @if($customer->biometric_path)
                    <div class="col-md-6">
                        <div class="text-center">
                            <p class="small text-muted mb-2">Biometric Photo</p>
                            <img src="{{ Storage::url($customer->biometric_path) }}" alt="Biometric" class="img-thumbnail" style="max-width: 200px;">
                        </div>
                    </div>
                    @endif
                    @if($customer->face_path)
                    <div class="col-md-6">
                        <div class="text-center">
                            <p class="small text-muted mb-2">Face Photo</p>
                            <img src="{{ Storage::url($customer->face_path) }}" alt="Face Photo" class="img-thumbnail" style="max-width: 200px;">
                        </div>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Sales History -->
        @if($customer->sales->count() > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="card-title mb-0">
                    <i class="bi bi-cart3 me-2"></i>Sales History
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Sale ID</th>
                                <th>Date</th>
                                <th>Items</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customer->sales as $sale)
                            <tr>
                                <td><code>#{{ $sale->id }}</code></td>
                                <td>{{ $sale->created_at->format('d M Y') }}</td>
                                <td>{{ $sale->saleItems->count() }} items</td>
                                <td><strong>Rs. {{ number_format($sale->total_amount, 2) }}</strong></td>
                                <td>
                                    <span class="badge bg-{{ $sale->status === 'completed' ? 'success' : 'warning' }}">
                                        {{ ucfirst($sale->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('sales.show', $sale) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-md-4">
        <!-- Guarantor Information -->
        @if($customer->guarantors->count() > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="card-title mb-0">
                    <i class="bi bi-shield-check me-2"></i>Guarantor Information
                </h5>
            </div>
            <div class="card-body">
                @foreach($customer->guarantors as $guarantor)
                <div class="mb-3 pb-3 @if(!$loop->last) border-bottom @endif">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <th width="35%">Name:</th>
                            <td>{{ $guarantor->guarantor_name }}</td>
                        </tr>
                        @if($guarantor->guarantor_phone)
                        <tr>
                            <th>Phone:</th>
                            <td><a href="tel:{{ $guarantor->guarantor_phone }}" class="text-decoration-none">{{ $guarantor->guarantor_phone }}</a></td>
                        </tr>
                        @endif
                        @if($guarantor->guarantor_cnic)
                        <tr>
                            <th>CNIC:</th>
                            <td><code>{{ $guarantor->guarantor_cnic }}</code></td>
                        </tr>
                        @endif
                        @if($guarantor->guarantor_relation)
                        <tr>
                            <th>Relation:</th>
                            <td>{{ $guarantor->guarantor_relation }}</td>
                        </tr>
                        @endif
                        @if($guarantor->guarantor_address)
                        <tr>
                            <th>Address:</th>
                            <td>{{ $guarantor->guarantor_address }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Quick Actions -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @can('create-sales')
                    <a href="{{ route('sales.create') }}?customer_id={{ $customer->id }}" class="btn btn-success">
                        <i class="bi bi-plus-circle me-2"></i>New Sale
                    </a>
                    @endcan
                    
                    @can('edit-customers')
                    <button class="btn btn-primary" onclick="editCustomer({{ $customer->id }})">
                        <i class="bi bi-pencil me-2"></i>Edit Customer
                    </button>
                    @endcan
                    
                    <a href="tel:{{ $customer->phone }}" class="btn btn-outline-success">
                        <i class="bi bi-telephone me-2"></i>Call Customer
                    </a>
                    
                    @if($customer->email)
                    <a href="mailto:{{ $customer->email }}" class="btn btn-outline-primary">
                        <i class="bi bi-envelope me-2"></i>Send Email
                    </a>
                    @endif
                    
                    <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Customers
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Customer Modal -->
@include('customers.modal', ['branches' => $branches])

@endsection

@push('scripts')
<script>
    function clearValidationErrors() {
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        document.querySelectorAll('.invalid-feedback').forEach(el => {
            el.textContent = '';
        });
    }

    function displayValidationErrors(errors) {
        Object.keys(errors).forEach(field => {
            const input = document.querySelector(`[name="${field}"]`);
            const feedback = input?.parentElement.querySelector('.invalid-feedback');
            
            if (input && feedback) {
                input.classList.add('is-invalid');
                feedback.textContent = errors[field][0];
            }
        });
    }

    // Form submission handler
    document.getElementById('customerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        clearValidationErrors();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        const isEdit = document.getElementById('customer_id').value !== '';
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>' + (isEdit ? 'Updating...' : 'Saving...');
        
        // Set method for Laravel
        const method = isEdit ? 'PUT' : 'POST';
        if (isEdit) {
            formData.append('_method', 'PUT');
        }
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Customer updated successfully!', 'success');
                bootstrap.Modal.getInstance(document.getElementById('customerModal')).hide();
                setTimeout(() => location.reload(), 1000);
            } else {
                if (data.errors) {
                    displayValidationErrors(data.errors);
                }
                showToast(data.message || 'Failed to update customer', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred while updating the customer.', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });

    // CNIC and phone formatting
    function formatCNIC(input) {
        let value = input.value.replace(/\D/g, '');
        if (value.length >= 5) {
            value = value.substring(0, 5) + '-' + value.substring(5);
        }
        if (value.length >= 13) {
            value = value.substring(0, 13) + '-' + value.substring(13, 14);
        }
        input.value = value;
    }

    function formatPhone(input) {
        input.value = input.value.replace(/\D/g, '').substring(0, 10);
    }

    document.getElementById('cnic').addEventListener('input', function(e) {
        formatCNIC(e.target);
    });
    
    if (document.getElementById('guarantor_1_cnic')) {
        document.getElementById('guarantor_1_cnic').addEventListener('input', function(e) {
            formatCNIC(e.target);
        });
    }
    
    if (document.getElementById('guarantor_2_cnic')) {
        document.getElementById('guarantor_2_cnic').addEventListener('input', function(e) {
            formatCNIC(e.target);
        });
    }
    
    document.getElementById('phone').addEventListener('input', function(e) {
        formatPhone(e.target);
    });
    
    if (document.getElementById('guarantor_1_phone')) {
        document.getElementById('guarantor_1_phone').addEventListener('input', function(e) {
            formatPhone(e.target);
        });
    }
    
    if (document.getElementById('guarantor_2_phone')) {
        document.getElementById('guarantor_2_phone').addEventListener('input', function(e) {
            formatPhone(e.target);
        });
    }

    // Reset form when modal is closed
    document.getElementById('customerModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('customerForm').reset();
        clearValidationErrors();
        // Reset to first step
        currentStep = 1;
        showStep(currentStep);
    });
    function editCustomer(customerId) {
        // Fetch customer data
        fetch(`/customers/${customerId}/get`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const customer = data.customer;
                
                // Reset to first step
                currentStep = 1;
                showStep(currentStep);
                
                // Set form action and method
                document.getElementById('customerForm').action = `/customers/${customerId}`;
                document.getElementById('form_method').value = 'PUT';
                document.getElementById('customer_id').value = customerId;
                
                // Populate form fields
                document.getElementById('branch_id').value = customer.branch_id || '';
                document.getElementById('name').value = customer.name || '';
                document.getElementById('email').value = customer.email || '';
                document.getElementById('phone').value = customer.phone || '';
                document.getElementById('cnic').value = customer.cnic || '';
                document.getElementById('profession').value = customer.profession || '';
                document.getElementById('father_husband_name').value = customer.father_husband_name || '';
                document.getElementById('address').value = customer.address || '';
                document.getElementById('is_active').value = customer.is_active ? '1' : '0';
                
                // Populate guarantor fields
                if (customer.guarantors && customer.guarantors.length > 0) {
                    const guarantor1 = customer.guarantors[0];
                    document.getElementById('guarantor_1_name').value = guarantor1.name || '';
                    document.getElementById('guarantor_1_email').value = guarantor1.email || '';
                    document.getElementById('guarantor_1_phone').value = guarantor1.phone || '';
                    document.getElementById('guarantor_1_cnic').value = guarantor1.cnic || '';
                    document.getElementById('guarantor_1_profession').value = guarantor1.profession || '';
                    document.getElementById('guarantor_1_father_husband_name').value = guarantor1.father_husband_name || '';
                    document.getElementById('guarantor_1_relation').value = guarantor1.relation || '';
                    document.getElementById('guarantor_1_address').value = guarantor1.address || '';
                    
                    if (customer.guarantors.length > 1) {
                        const guarantor2 = customer.guarantors[1];
                        document.getElementById('guarantor_2_name').value = guarantor2.name || '';
                        document.getElementById('guarantor_2_email').value = guarantor2.email || '';
                        document.getElementById('guarantor_2_phone').value = guarantor2.phone || '';
                        document.getElementById('guarantor_2_cnic').value = guarantor2.cnic || '';
                        document.getElementById('guarantor_2_profession').value = guarantor2.profession || '';
                        document.getElementById('guarantor_2_father_husband_name').value = guarantor2.father_husband_name || '';
                        document.getElementById('guarantor_2_relation').value = guarantor2.relation || '';
                        document.getElementById('guarantor_2_address').value = guarantor2.address || '';
                    }
                }
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('customerModal'));
                modal.show();
            } else {
                showToast('Failed to load customer data', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred while loading customer data', 'error');
        });
    }
</script>
@endpush