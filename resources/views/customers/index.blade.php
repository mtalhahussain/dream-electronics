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
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="customerModalLabel">
                    <i class="bi bi-person-plus me-2"></i>Add New Customer
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="customerForm" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                    @csrf
                    <input type="hidden" id="customer_id" name="customer_id">
                    <input type="hidden" id="form_method" name="_method" value="">
                    
                    <!-- Progress Steps -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="progress" style="height: 4px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 33%" id="progressBar"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <small class="text-primary fw-bold" id="step1">1. Customer Info</small>
                                <small class="text-muted" id="step2">2. Guarantor 1</small>
                                <small class="text-muted" id="step3">3. Guarantor 2</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 1: Customer Information -->
                    <div class="step-content" id="stepContent1">
                        <div class="row g-3">
                            <!-- Customer Information Card -->
                            <div class="col-12">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0">
                                            <i class="bi bi-person me-2"></i>Customer Information
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
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
                                            
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="profession" name="profession" placeholder="Profession">
                                                    <label for="profession">Profession</label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="father_husband_name" name="father_husband_name" placeholder="Father/Husband Name">
                                                    <label for="father_husband_name">Father/Husband Name</label>
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
                                            
                                            <div class="col-12">
                                                <div class="form-floating">
                                                    <textarea class="form-control" id="address" name="address" placeholder="Complete Address" style="height: 80px" required></textarea>
                                                    <label for="address">Complete Address <span class="text-danger">*</span></label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Customer Photos Card -->
                            <div class="col-12">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0">
                                            <i class="bi bi-camera me-2"></i>Customer Photos (Optional)
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
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
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Navigation Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary" disabled id="prevBtn">
                                <i class="bi bi-arrow-left me-2"></i>Previous
                            </button>
                            <button type="button" class="btn btn-primary" onclick="nextStep()" id="nextBtn">
                                Next<i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Step 2: Guarantor 1 Information -->
                    <div class="step-content d-none" id="stepContent2">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="card border-secondary">
                                    <div class="card-header bg-secondary text-white">
                                        <h6 class="mb-0">
                                            <i class="bi bi-person-check me-2"></i>Guarantor 1 Information
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="guarantor_1_name" name="guarantor_1_name" placeholder="Guarantor 1 Name">
                                                    <label for="guarantor_1_name">Guarantor Name</label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="email" class="form-control" id="guarantor_1_email" name="guarantor_1_email" placeholder="Guarantor 1 Email">
                                                    <label for="guarantor_1_email">Email Address</label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label for="guarantor_1_phone" class="form-label">Phone Number</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">+92</span>
                                                    <input type="tel" class="form-control" id="guarantor_1_phone" name="guarantor_1_phone" placeholder="3001234567">
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label for="guarantor_1_cnic" class="form-label">CNIC</label>
                                                <input type="text" class="form-control" id="guarantor_1_cnic" name="guarantor_1_cnic" placeholder="12345-6789012-3" pattern="[0-9]{5}-[0-9]{7}-[0-9]{1}">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="guarantor_1_profession" name="guarantor_1_profession" placeholder="Guarantor 1 Profession">
                                                    <label for="guarantor_1_profession">Profession</label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="guarantor_1_father_husband_name" name="guarantor_1_father_husband_name" placeholder="Father/Husband Name">
                                                    <label for="guarantor_1_father_husband_name">Father/Husband Name</label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="guarantor_1_relation" name="guarantor_1_relation" placeholder="Relation">
                                                    <label for="guarantor_1_relation">Relation to Customer</label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label for="guarantor_1_biometric" class="form-label">Biometric Photo</label>
                                                <input type="file" class="form-control" id="guarantor_1_biometric" name="guarantor_1_biometric" accept="image/*">
                                                <div class="form-text">Upload guarantor biometric image</div>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            
                                            <div class="col-12">
                                                <div class="form-floating">
                                                    <textarea class="form-control" id="guarantor_1_address" name="guarantor_1_address" placeholder="Guarantor 1 Address" style="height: 80px"></textarea>
                                                    <label for="guarantor_1_address">Complete Address</label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Navigation Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary" onclick="prevStep()" id="prevBtn2">
                                <i class="bi bi-arrow-left me-2"></i>Previous
                            </button>
                            <button type="button" class="btn btn-primary" onclick="nextStep()">
                                Next<i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Step 3: Guarantor 2 Information -->
                    <div class="step-content d-none" id="stepContent3">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-dark">
                                        <h6 class="mb-0">
                                            <i class="bi bi-person-plus me-2"></i>Guarantor 2 Information (Optional)
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle me-2"></i>
                                            Adding a second guarantor is optional but recommended for better security.
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="guarantor_2_name" name="guarantor_2_name" placeholder="Guarantor 2 Name">
                                                    <label for="guarantor_2_name">Guarantor Name</label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="email" class="form-control" id="guarantor_2_email" name="guarantor_2_email" placeholder="Guarantor 2 Email">
                                                    <label for="guarantor_2_email">Email Address</label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label for="guarantor_2_phone" class="form-label">Phone Number</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">+92</span>
                                                    <input type="tel" class="form-control" id="guarantor_2_phone" name="guarantor_2_phone" placeholder="3001234567">
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label for="guarantor_2_cnic" class="form-label">CNIC</label>
                                                <input type="text" class="form-control" id="guarantor_2_cnic" name="guarantor_2_cnic" placeholder="12345-6789012-3" pattern="[0-9]{5}-[0-9]{7}-[0-9]{1}">
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="guarantor_2_profession" name="guarantor_2_profession" placeholder="Guarantor 2 Profession">
                                                    <label for="guarantor_2_profession">Profession</label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="guarantor_2_father_husband_name" name="guarantor_2_father_husband_name" placeholder="Father/Husband Name">
                                                    <label for="guarantor_2_father_husband_name">Father/Husband Name</label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" id="guarantor_2_relation" name="guarantor_2_relation" placeholder="Relation">
                                                    <label for="guarantor_2_relation">Relation to Customer</label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label for="guarantor_2_biometric" class="form-label">Biometric Photo</label>
                                                <input type="file" class="form-control" id="guarantor_2_biometric" name="guarantor_2_biometric" accept="image/*">
                                                <div class="form-text">Upload guarantor biometric image</div>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                            
                                            <div class="col-12">
                                                <div class="form-floating">
                                                    <textarea class="form-control" id="guarantor_2_address" name="guarantor_2_address" placeholder="Guarantor 2 Address" style="height: 80px"></textarea>
                                                    <label for="guarantor_2_address">Complete Address</label>
                                                    <div class="invalid-feedback"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Navigation Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary" onclick="prevStep()">
                                <i class="bi bi-arrow-left me-2"></i>Previous
                            </button>
                            <button type="submit" class="btn btn-success" id="submitBtn">
                                <i class="bi bi-check-circle me-2"></i>Save Customer
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <div class="d-flex justify-content-between w-100 align-items-center">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            All fields marked with <span class="text-danger">*</span> are required
                        </small>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </button>
                    </div>
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

    function openCreateModal() {
        // Reset form for create mode
        document.getElementById('customerForm').action = '{{ route('customers.store') }}';
        document.getElementById('form_method').value = '';
        document.getElementById('customer_id').value = '';
        document.getElementById('customerForm').reset();
        clearValidationErrors();
        
        // Reset to first step
        currentStep = 1;
        showStep(currentStep);
        
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
    
    document.getElementById('guarantor_1_cnic').addEventListener('input', function(e) {
        formatCNIC(e.target);
    });
    
    document.getElementById('guarantor_2_cnic').addEventListener('input', function(e) {
        formatCNIC(e.target);
    });
    
    // Phone formatting
    function formatPhone(input) {
        input.value = input.value.replace(/\D/g, '').substring(0, 10);
    }

    document.getElementById('phone').addEventListener('input', function(e) {
        formatPhone(e.target);
    });
    
    document.getElementById('guarantor_1_phone').addEventListener('input', function(e) {
        formatPhone(e.target);
    });
    
    document.getElementById('guarantor_2_phone').addEventListener('input', function(e) {
        formatPhone(e.target);
    });

    // Reset form when modal is closed
    document.getElementById('customerModal').addEventListener('hidden.bs.modal', function() {
        document.getElementById('customerForm').reset();
        document.getElementById('customerForm').action = '{{ route('customers.store') }}';
        document.getElementById('form_method').value = '';
        document.getElementById('customer_id').value = '';
        clearValidationErrors();
        
        // Reset to first step
        currentStep = 1;
        showStep(currentStep);
    });

    // Step navigation functions
    let currentStep = 1;
    const totalSteps = 3;
    
    function updateProgressBar() {
        const progress = (currentStep / totalSteps) * 100;
        document.getElementById('progressBar').style.width = progress + '%';
        
        // Update step indicators
        for (let i = 1; i <= totalSteps; i++) {
            const stepElement = document.getElementById('step' + i);
            if (i <= currentStep) {
                stepElement.classList.remove('text-muted');
                stepElement.classList.add('text-primary', 'fw-bold');
            } else {
                stepElement.classList.remove('text-primary', 'fw-bold');
                stepElement.classList.add('text-muted');
            }
        }
    }
    
    function showStep(step) {
        // Hide all steps
        document.querySelectorAll('.step-content').forEach(function(content) {
            content.classList.add('d-none');
        });
        
        // Show current step
        document.getElementById('stepContent' + step).classList.remove('d-none');
        
        // Update modal title
        const titles = {
            1: '<i class="bi bi-person-plus me-2"></i>Add New Customer - Step 1 of 3',
            2: '<i class="bi bi-person-check me-2"></i>Add New Customer - Step 2 of 3',
            3: '<i class="bi bi-person-plus-fill me-2"></i>Add New Customer - Step 3 of 3'
        };
        document.getElementById('customerModalLabel').innerHTML = titles[step];
        
        updateProgressBar();
    }
    
    function nextStep() {
        if (currentStep < totalSteps) {
            // Validate current step before proceeding
            if (validateCurrentStep()) {
                currentStep++;
                showStep(currentStep);
            }
        }
    }
    
    function prevStep() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    }
    
    function validateCurrentStep() {
        let isValid = true;
        const currentStepElement = document.getElementById('stepContent' + currentStep);
        const requiredFields = currentStepElement.querySelectorAll('[required]');
        
        requiredFields.forEach(function(field) {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            showToast('Please fill in all required fields before proceeding', 'error');
        }
        
        return isValid;
    }

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