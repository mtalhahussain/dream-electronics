@extends('layouts.admin')

@section('title', 'Customers')

@push('styles')
<style>
    /* Fix dropdown positioning in table */
    .table-responsive {
        overflow-x: auto;
        overflow-y: visible;
    }
    
    .table .dropdown-menu {
        position: fixed !important;
        z-index: 1060 !important;
        transform: none !important;
    }
    
    /* Ensure dropdown appears above modal */
    .modal .dropdown-menu {
        z-index: 1070 !important;
    }
</style>
@endpush

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="bi bi-people me-2"></i>Customers Management
        </h5>
        <button type="button" class="btn btn-primary" onclick="openCreateModal()">
            <i class="bi bi-plus-circle me-2"></i>Add New Customer
        </button>
    </div>
    
    <!-- Filter Toolbar -->
    <div class="card-body border-bottom bg-light">
        <div class="row g-3">
            <div class="col-md-3">
                <select class="form-select form-select-sm" id="branchFilter">
                    <option value="">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
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
                    <button class="btn btn-outline-secondary" type="button" onclick="applyFilters()">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-body p-0" id="customersTableContainer">
        @include('customers.table', ['customers' => $customers])
    </div>
    
    @if(isset($customers) && method_exists($customers, 'hasPages') && $customers->hasPages())
    <div class="card-footer bg-white" id="paginationContainer">
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
            <form id="customerForm" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="customer_id" name="customer_id">
                    <input type="hidden" id="form_method" name="_method" value="">
                    <div class="row g-3">
                        <!-- Customer Information -->
                        <div class="col-12">
                            <h6 class="text-primary border-bottom pb-2 mb-3">Customer Information</h6>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="branch_id" name="branch_id">
                                    <option value="">Select Branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                                <label for="branch_id">Branch</label>
                                <div class="invalid-feedback"></div>
                            </div>
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
                        
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="is_active" name="is_active">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                <label for="is_active">Status</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="biometric" class="form-label">Biometric Photo</label>
                            <input type="file" class="form-control" id="biometric" name="biometric" accept="image/*">
                            <div class="form-text">Upload customer biometric image (JPG, PNG, max 1MB)</div>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="face_photo" class="form-label">Face Photo</label>
                            <input type="file" class="form-control" id="face_photo" name="face_photo" accept="image/*">
                            <div class="form-text">Upload customer face photo (JPG, PNG, max 1MB)</div>
                            <div class="invalid-feedback"></div>
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
    // Filtering functionality
    function applyFilters() {
        const filters = {
            branch_id: document.getElementById('branchFilter').value,
            status: document.getElementById('statusFilter').value,
            date: document.getElementById('dateFilter').value,
            search: document.getElementById('searchFilter').value
        };

        const params = new URLSearchParams();
        Object.keys(filters).forEach(key => {
            if (filters[key]) {
                params.append(key, filters[key]);
            }
        });

        // include credentials so session cookies (auth) are sent; parse as text first to avoid json parse errors
        fetch(`{{ route('customers.index') }}?${params.toString()}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        })
        .then(async response => {
            const text = await response.text();

            // Try to parse JSON; if parsing fails, assume server returned an HTML fragment (e.g., view or redirect page)
            try {
                const data = JSON.parse(text);

                if (data.success) {
                    // update HTML fragments if provided
                    if (data.html) {
                        document.getElementById('customersTableContainer').innerHTML = data.html;
                    }
                    if (data.pagination) {
                        const pagEl = document.getElementById('paginationContainer');
                        if (pagEl) pagEl.innerHTML = data.pagination;
                    }
                    showToast(data.message || 'Filters applied successfully', 'success');
                } else {
                    if (data.errors) {
                        console.warn('Filter errors:', data.errors);
                    }
                    showToast(data.message || 'Failed to apply filters', 'error');
                }
            } catch (err) {
                // Not JSON: likely an HTML fragment (table) — inject it directly
                document.getElementById('customersTableContainer').innerHTML = text;
                // If server returned full page (e.g., login), keep console for debugging
                showToast('Filters applied', 'success');
            }
        })
        .catch(error => {
            console.error('Filter error:', error);
            showToast('Error applying filters', 'error');
        });
    }

    // Auto-apply filters on change
    document.getElementById('branchFilter').addEventListener('change', applyFilters);
    document.getElementById('statusFilter').addEventListener('change', applyFilters);
    document.getElementById('dateFilter').addEventListener('change', applyFilters);
    
    // Search on Enter key
    document.getElementById('searchFilter').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            applyFilters();
        }
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
                
                // Update modal title
                document.getElementById('customerModalLabel').textContent = 'Edit Customer';
                
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
                document.getElementById('address').value = customer.address || '';
                document.getElementById('is_active').value = customer.is_active ? '1' : '0';
                document.getElementById('guarantor_name').value = customer.guarantor_name || '';
                document.getElementById('guarantor_phone').value = customer.guarantor_phone || '';
                document.getElementById('guarantor_cnic').value = customer.guarantor_cnic || '';
                document.getElementById('guarantor_address').value = customer.guarantor_address || '';
                document.getElementById('guarantor_relation').value = customer.guarantor_relation || '';
                
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

    function openCreateModal() {
        // Reset form for create mode
        document.getElementById('customerModalLabel').textContent = 'Add New Customer';
        document.getElementById('customerForm').action = '{{ route('customers.store') }}';
        document.getElementById('form_method').value = '';
        document.getElementById('customer_id').value = '';
        document.getElementById('customerForm').reset();
        clearValidationErrors();
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('customerModal'));
        modal.show();
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

    // Clear validation errors
    function clearValidationErrors() {
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        document.querySelectorAll('.invalid-feedback').forEach(el => {
            el.textContent = '';
        });
    }

    // Display validation errors
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
    
    // Form submission with validation
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
                showToast(data.message || (isEdit ? 'Customer updated successfully!' : 'Customer saved successfully!'), 'success');
                bootstrap.Modal.getInstance(document.getElementById('customerModal')).hide();
                this.reset();
                setTimeout(() => location.reload(), 1000);
            } else {
                if (data.errors) {
                    displayValidationErrors(data.errors);
                }
                showToast(data.message || (isEdit ? 'Failed to update customer' : 'Failed to save customer'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred while ' + (isEdit ? 'updating' : 'saving') + ' the customer.', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });

    // Delete form submission
    document.getElementById('deleteForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Deleting...';
        
        fetch(this.action, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Customer deleted successfully!', 'success');
                bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message || 'Failed to delete customer', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred while deleting the customer.', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
    
    // CNIC formatting
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

    document.getElementById('cnic').addEventListener('input', function(e) {
        formatCNIC(e.target);
    });
    
    document.getElementById('guarantor_cnic').addEventListener('input', function(e) {
        formatCNIC(e.target);
    });
    
    // Phone formatting
    function formatPhone(input) {
        input.value = input.value.replace(/\D/g, '').substring(0, 10);
    }

    document.getElementById('phone').addEventListener('input', function(e) {
        formatPhone(e.target);
    });
    
    document.getElementById('guarantor_phone').addEventListener('input', function(e) {
        formatPhone(e.target);
    });

    // Reset form when modal is closed
    document.getElementById('customerModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('customerForm').reset();
        document.getElementById('customerModalLabel').textContent = 'Add New Customer';
        document.getElementById('customerForm').action = '{{ route('customers.store') }}';
        document.getElementById('form_method').value = '';
        document.getElementById('customer_id').value = '';
        clearValidationErrors();
    });

    // Fix dropdown positioning in table
    document.addEventListener('DOMContentLoaded', function() {
        // Handle dropdown positioning
        document.querySelectorAll('.dropdown-toggle').forEach(function(dropdown) {
            dropdown.addEventListener('click', function(e) {
                setTimeout(function() {
                    const dropdownMenu = dropdown.nextElementSibling;
                    if (dropdownMenu && dropdownMenu.classList.contains('dropdown-menu')) {
                        const rect = dropdown.getBoundingClientRect();
                        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                        const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;
                        
                        dropdownMenu.style.position = 'fixed';
                        dropdownMenu.style.top = (rect.bottom + scrollTop) + 'px';
                        dropdownMenu.style.left = (rect.left + scrollLeft) + 'px';
                        dropdownMenu.style.zIndex = '1060';
                    }
                }, 10);
            });
        });
    });
</script>
@endpush