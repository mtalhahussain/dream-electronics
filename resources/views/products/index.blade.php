@extends('layouts.admin')

@section('title', 'Products')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="bi bi-box-seam me-2"></i>Products Management
        </h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
            <i class="bi bi-plus-circle me-2"></i>Add New Product
        </button>
    </div>
    
    <!-- Filter Toolbar -->
    <div class="card-body border-bottom bg-light">
        <form id="filterForm">
            <div class="row g-3">
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="category" id="categoryFilter">
                        <option value="">All Categories</option>
                        <option value="Mobile">Mobile</option>
                        <option value="Laptop">Laptop</option>
                        <option value="TV">TV</option>
                        <option value="Refrigerator">Refrigerator</option>
                        <option value="Washing Machine">Washing Machine</option>
                        <option value="Air Conditioner">Air Conditioner</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="status" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="branch_id" id="branchFilter">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" placeholder="Search by name, model, brand..." name="search" id="searchFilter">
                        <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm w-100" id="clearFilters">
                        <i class="bi bi-arrow-clockwise me-1"></i>Clear
                    </button>
                </div>
            </div>
        </form>
        
    </div>
    
    <div class="card-body p-0" id="productsContainer">
        <!-- Loading Spinner -->
        <div id="loadingSpinner" class="text-center py-5" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading products...</p>
        </div>

        <!-- Products Table -->
        <div id="productsTable">
            @include('products.table', ['products' => $products])
        </div>
    </div>

<!-- Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3">
    <div id="toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <i class="bi bi-check-circle-fill text-success me-2" id="toastIcon"></i>
            <strong class="me-auto" id="toastTitle">Success</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body" id="toastMessage"></div>
    </div>
</div>

<!-- Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="productForm" action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="branch_id" name="branch_id" required>
                                    <option value="">Choose branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                                <label for="branch_id">Branch <span class="text-danger">*</span></label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="name" name="name" placeholder="Product Name" required>
                                <label for="name">Product Name <span class="text-danger">*</span></label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="model" name="model" placeholder="Product Model" required>
                                <label for="model">Model <span class="text-danger">*</span></label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="brand" name="brand" placeholder="Product Brand" required>
                                <label for="brand">Brand <span class="text-danger">*</span></label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="category" name="category" required>
                                    <option value="">Choose category</option>
                                    <option value="Mobile">Mobile</option>
                                    <option value="Laptop">Laptop</option>
                                    <option value="TV">TV</option>
                                    <option value="Refrigerator">Refrigerator</option>
                                    <option value="Washing Machine">Washing Machine</option>
                                    <option value="Air Conditioner">Air Conditioner</option>
                                    <option value="Other">Other</option>
                                </select>
                                <label for="category">Category <span class="text-danger">*</span></label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" placeholder="0.00" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" min="0" placeholder="Stock Quantity" required>
                                <label for="stock_quantity">Stock Quantity <span class="text-danger">*</span></label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea class="form-control" id="description" name="description" placeholder="Product Description" style="height: 100px"></textarea>
                                <label for="description">Description</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="purchase_invoice" class="form-label">Purchase Invoice</label>
                            <input type="file" class="form-control" id="purchase_invoice" name="purchase_invoice" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">Max file size: 2MB. Formats: PDF, JPG, PNG</div>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-check form-switch mt-4">
                                <input class="form-check-input" type="checkbox" id="active" name="active" value="1" checked>
                                <label class="form-check-label" for="active">
                                    Active Product
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Save Product
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
                <p>Are you sure you want to delete this product? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentEditingId = null;
    
    // Initialize filters
    initializeFilters();
    
    // Initialize form handlers
    initializeFormHandlers();
    
    // Initialize toggle handlers
    initializeToggleHandlers();
    
    // Initialize edit handlers
    initializeEditHandlers();
    
    function initializeFilters() {
        const filterForm = document.getElementById('filterForm');
        const searchInput = document.getElementById('searchFilter');
        let searchTimeout;
        
        // Handle filter form submission
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            loadProducts();
        });
        
        // Handle search button click
        document.getElementById('searchBtn').addEventListener('click', function() {
            loadProducts();
        });
        
        // Handle real-time search
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                loadProducts();
            }, 500);
        });
        
        // Handle other filter changes
        ['categoryFilter', 'statusFilter', 'branchFilter'].forEach(filterId => {
            document.getElementById(filterId).addEventListener('change', function() {
                loadProducts();
            });
        });
        
        // Clear filters
        document.getElementById('clearFilters').addEventListener('click', function() {
            filterForm.reset();
            loadProducts();
        });
    }
    
    function initializeFormHandlers() {
        const form = document.getElementById('productForm');
        const modal = document.getElementById('productModal');
        
        // Reset form when modal opens
        modal.addEventListener('show.bs.modal', function() {
            if (!currentEditingId) {
                form.reset();
                document.getElementById('productModalLabel').textContent = 'Add New Product';
            }
        });
        
        // Clear editing state when modal closes
        modal.addEventListener('hidden.bs.modal', function() {
            currentEditingId = null;
            form.reset();
            clearValidationErrors();
        });
        
        // Handle form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
            
            clearValidationErrors();
            
            const formData = new FormData(form);
            const url = currentEditingId ? `/products/${currentEditingId}` : '/products';
            
            if (currentEditingId) {
                formData.append('_method', 'PUT');
            }
            
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    bootstrap.Modal.getInstance(modal).hide();
                    loadProducts(); // Reload table without page refresh
                } else {
                    if (data.errors) {
                        showValidationErrors(data.errors);
                    } else {
                        showToast(data.message || 'An error occurred', 'error');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An unexpected error occurred', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }
    
    function initializeToggleHandlers() {
        // Use event delegation for toggle switches
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('toggle-status')) {
                const productId = e.target.getAttribute('data-product-id');
                toggleProductStatus(productId, e.target);
            }
        });
    }
    
    function initializeEditHandlers() {
        // Use event delegation for edit buttons
        document.addEventListener('click', function(e) {
            if (e.target.closest('.edit-product')) {
                const btn = e.target.closest('.edit-product');
                editProduct(btn);
            }
        });
    }
    
    function loadProducts() {
        const formData = new FormData(document.getElementById('filterForm'));
        const params = new URLSearchParams(formData);
        
        showLoading();
        
        fetch(`/products?${params}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateProductsTable(data.data, data.pagination);
            } else {
                showToast('Failed to load products', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error loading products', 'error');
        })
        .finally(() => {
            hideLoading();
        });
    }
    
    function updateProductsTable(products, pagination) {
        const container = document.getElementById('productsTable');
        
        if (products.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-box-seam fs-1 text-muted mb-3"></i>
                    <h5 class="text-muted">No products found</h5>
                    <p class="text-muted">Try adjusting your filters or add a new product.</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
                        <i class="bi bi-plus-circle me-2"></i>Add Product
                    </button>
                </div>
            `;
            return;
        }

        let tableHTML = `
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Model</th>
                            <th>Brand</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Branch</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        products.forEach(product => {
            const stockBadge = product.stock_quantity > 10 ? 'success' : (product.stock_quantity > 0 ? 'warning' : 'danger');
            const statusBadge = product.active ? 'success' : 'secondary';
            const statusText = product.active ? 'Active' : 'Inactive';
            
            tableHTML += `
                <tr data-product-id="${product.id}">
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="bg-light d-flex align-items-center justify-content-center rounded me-2" style="width: 40px; height: 40px;">
                                <i class="bi bi-box text-muted"></i>
                            </div>
                            <div>
                                <strong>${product.name}</strong>
                                ${product.description ? `<br><small class="text-muted">${product.description.substring(0, 30)}${product.description.length > 30 ? '...' : ''}</small>` : ''}
                            </div>
                        </div>
                    </td>
                    <td>${product.model}</td>
                    <td>${product.brand}</td>
                    <td><span class="badge bg-info">${product.category}</span></td>
                    <td><strong>Rs. ${parseFloat(product.price).toLocaleString('en-US', {minimumFractionDigits: 2})}</strong></td>
                    <td><span class="badge bg-${stockBadge}">${product.stock_quantity} units</span></td>
                    <td>
                        <div class="form-check form-switch">
                            <input class="form-check-input toggle-status" type="checkbox" 
                                   ${product.active ? 'checked' : ''}
                                   data-product-id="${product.id}">
                            <label class="form-check-label">
                                <span class="badge bg-${statusBadge} status-badge">${statusText}</span>
                            </label>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-info text-dark" data-branch-id="${product.branch_id || ''}">
                            ${product.branch ? product.branch.name : 'No Branch'}
                        </span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-outline-primary btn-sm me-1 edit-product" 
                                data-product-id="${product.id}"
                                data-branch-id="${product.branch_id || ''}"
                                data-name="${product.name}"
                                data-model="${product.model}"
                                data-brand="${product.brand}"
                                data-category="${product.category}"
                                data-price="${product.price}"
                                data-stock="${product.stock_quantity}"
                                data-description="${product.description || ''}"
                                data-active="${product.active ? '1' : '0'}">>
                            <i class="bi bi-pencil"></i>
                        </button>
                        ${product.purchase_invoice_path ? `
                        <a href="/storage/${product.purchase_invoice_path}" target="_blank" 
                           class="btn btn-outline-info btn-sm" title="View Invoice">
                            <i class="bi bi-file-text"></i>
                        </a>` : ''}
                    </td>
                </tr>
            `;
        });

        tableHTML += `
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        Showing ${((pagination.current_page - 1) * pagination.per_page) + 1} to ${Math.min(pagination.current_page * pagination.per_page, pagination.total)} of ${pagination.total} results
                    </div>
                    <div>
                        ${generatePaginationLinks(pagination)}
                    </div>
                </div>
            </div>
        `;

        container.innerHTML = tableHTML;
        
        // Re-initialize event listeners for the new content
        initializeEventDelegation();
    }
    
    function generatePaginationLinks(pagination) {
        if (pagination.last_page <= 1) return '';
        
        let paginationHTML = '<nav><ul class="pagination pagination-sm mb-0">';
        
        // Previous button
        if (pagination.current_page > 1) {
            paginationHTML += `<li class="page-item">
                <a class="page-link" href="#" data-page="${pagination.current_page - 1}">Previous</a>
            </li>`;
        }
        
        // Page numbers
        const startPage = Math.max(1, pagination.current_page - 2);
        const endPage = Math.min(pagination.last_page, pagination.current_page + 2);
        
        for (let i = startPage; i <= endPage; i++) {
            paginationHTML += `<li class="page-item ${i === pagination.current_page ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>`;
        }
        
        // Next button
        if (pagination.current_page < pagination.last_page) {
            paginationHTML += `<li class="page-item">
                <a class="page-link" href="#" data-page="${pagination.current_page + 1}">Next</a>
            </li>`;
        }
        
        paginationHTML += '</ul></nav>';
        return paginationHTML;
    }
    
    function editProduct(btn) {
        currentEditingId = btn.getAttribute('data-product-id');
        
        // Fill form with product data
        document.getElementById('branch_id').value = btn.getAttribute('data-branch-id');
        document.getElementById('name').value = btn.getAttribute('data-name');
        document.getElementById('model').value = btn.getAttribute('data-model');
        document.getElementById('brand').value = btn.getAttribute('data-brand');
        document.getElementById('category').value = btn.getAttribute('data-category');
        document.getElementById('price').value = btn.getAttribute('data-price');
        document.getElementById('stock_quantity').value = btn.getAttribute('data-stock');
        document.getElementById('description').value = btn.getAttribute('data-description');
        document.getElementById('active').checked = btn.getAttribute('data-active') === '1';
        
        // Update modal title
        document.getElementById('productModalLabel').textContent = 'Edit Product';
        
        // Show modal
        new bootstrap.Modal(document.getElementById('productModal')).show();
    }
    
    function toggleProductStatus(productId, checkbox) {
        fetch(`/products/${productId}/toggle-active`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const badge = checkbox.closest('td').querySelector('.status-badge');
                if (data.active) {
                    badge.classList.remove('bg-secondary');
                    badge.classList.add('bg-success');
                    badge.textContent = 'Active';
                } else {
                    badge.classList.remove('bg-success');
                    badge.classList.add('bg-secondary');
                    badge.textContent = 'Inactive';
                }
                showToast(data.message, 'success');
            } else {
                // Revert checkbox if failed
                checkbox.checked = !checkbox.checked;
                showToast(data.message || 'Failed to update product status', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Revert checkbox if failed
            checkbox.checked = !checkbox.checked;
            showToast('An error occurred while updating product status', 'error');
        });
    }
    
    function showLoading() {
        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('productsTable').style.opacity = '0.5';
    }
    
    function hideLoading() {
        document.getElementById('loadingSpinner').style.display = 'none';
        document.getElementById('productsTable').style.opacity = '1';
    }
    
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const toastIcon = document.getElementById('toastIcon');
        const toastTitle = document.getElementById('toastTitle');
        const toastMessage = document.getElementById('toastMessage');
        
        // Update icon and title based on type
        if (type === 'success') {
            toastIcon.className = 'bi bi-check-circle-fill text-success me-2';
            toastTitle.textContent = 'Success';
            toast.className = 'toast';
        } else {
            toastIcon.className = 'bi bi-exclamation-triangle-fill text-danger me-2';
            toastTitle.textContent = 'Error';
            toast.className = 'toast';
        }
        
        toastMessage.textContent = message;
        
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
    }
    
    function clearValidationErrors() {
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        document.querySelectorAll('.invalid-feedback').forEach(el => {
            el.textContent = '';
        });
    }
    
    function showValidationErrors(errors) {
        for (const [field, messages] of Object.entries(errors)) {
            const input = document.getElementById(field);
            const feedback = input?.nextElementSibling;
            
            if (input && feedback) {
                input.classList.add('is-invalid');
                feedback.textContent = messages[0];
            }
        }
    }
    
    function initializeEventDelegation() {
        // Event delegation is already handled in initializeEditHandlers
        // and other global event listeners
    }
    
    function loadProductsPage(page) {
        const formData = new FormData(document.getElementById('filterForm'));
        formData.append('page', page);
        const params = new URLSearchParams(formData);
        
        showLoading();
        
        fetch(`/products?${params}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateProductsTable(data.data, data.pagination);
            } else {
                showToast('Failed to load products', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error loading products', 'error');
        })
        .finally(() => {
            hideLoading();
        });
    }
});
</script>
@endpush