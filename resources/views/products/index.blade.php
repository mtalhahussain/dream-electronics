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
                    <select class="form-select form-select-sm" name="category_id" id="categoryFilter">
                        <option value="">All Categories</option>
                        <!-- Categories will be loaded dynamically -->
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

<!-- Toast is now handled by the global admin layout -->

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
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">Choose category</option>
                                    <!-- Categories will be loaded dynamically -->
                                </select>
                                <label for="category_id">Category <span class="text-danger">*</span></label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="price" class="form-label">Selling Price <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" placeholder="0.00" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="purchase_cost" class="form-label">Purchase Cost</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number" class="form-control" id="purchase_cost" name="purchase_cost" step="0.01" min="0" placeholder="0.00">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="purchased_from" name="purchased_from" placeholder="Purchased From">
                                <label for="purchased_from">Purchased From</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="sku" name="sku" placeholder="SKU">
                                <label for="sku">SKU</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="serial_number" name="serial_number" placeholder="Serial Number">
                                <label for="serial_number">Serial Number</label>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" min="0" placeholder="Stock Quantity" required>
                                <label for="stock_quantity">Quantity <span class="text-danger">*</span></label>
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
    
    // Load categories
    loadCategories();
    
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
                if (data.ok) {
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
            
            if (e.target.closest('.delete-product')) {
                const btn = e.target.closest('.delete-product');
                deleteProduct(btn);
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
                updateProductsTable(data.data, data.pagination, data.permissions);
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
    
    function loadCategories() {
        fetch('/categories/active', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateCategoryDropdowns(data.categories);
            } else {
                console.error('Failed to load categories');
            }
        })
        .catch(error => {
            console.error('Error loading categories:', error);
        });
    }
    
    function populateCategoryDropdowns(categories) {
        const modalSelect = document.getElementById('category_id');
        const filterSelect = document.getElementById('categoryFilter');
        
        // Clear existing options (except the first "Choose/All" option)
        modalSelect.innerHTML = '<option value="">Choose category</option>';
        filterSelect.innerHTML = '<option value="">All Categories</option>';
        
        // Add category options
        categories.forEach(category => {
            const modalOption = document.createElement('option');
            modalOption.value = category.id;
            modalOption.textContent = category.name;
            modalSelect.appendChild(modalOption);
            
            const filterOption = document.createElement('option');
            filterOption.value = category.id;
            filterOption.textContent = category.name;
            filterSelect.appendChild(filterOption);
        });
    }
    
    function updateProductsTable(products, pagination, permissions = {}) {
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
                            <th>SKU</th>
                            <th>Serial No.</th>
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
                    <td>
                        ${product.sku ? `<span class="badge bg-secondary">${product.sku}</span>` : '<span class="text-muted">—</span>'}
                    </td>
                    <td>
                        ${product.serial_number ? `<code class="text-primary">${product.serial_number}</code>` : '<span class="text-muted">—</span>'}
                    </td>
                    <td>
                        ${(() => {
                            const categoryDisplay = product.category_display;
                            if (categoryDisplay.badge_class === 'custom') {
                                return `<span class="badge" style="background-color: ${categoryDisplay.color}; color: white;">
                                    ${categoryDisplay.icon ? `<i class="bi ${categoryDisplay.icon} me-1"></i>` : ''}
                                    ${categoryDisplay.name}
                                </span>`;
                            } else {
                                return `<span class="badge ${categoryDisplay.badge_class}">${categoryDisplay.name}</span>`;
                            }
                        })()}
                    </td>
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
                                data-category-id="${product.category_id || ''}"
                                data-name="${product.name}"
                                data-model="${product.model}"
                                data-brand="${product.brand}"
                                data-price="${product.price}"
                                data-purchase-cost="${product.purchase_cost || ''}"
                                data-purchased-from="${product.purchased_from || ''}"
                                data-sku="${product.sku || ''}"
                                data-serial-number="${product.serial_number || ''}"
                                data-stock="${product.stock_quantity}"
                                data-description="${product.description || ''}"
                                data-active="${product.active ? '1' : '0'}">>>
                            <i class="bi bi-pencil"></i>
                        </button>
                        ${product.purchase_invoice ? `
                        <a href="/storage/${product.purchase_invoice}" target="_blank" 
                           class="btn btn-outline-info btn-sm me-1" title="View Invoice">
                            <i class="bi bi-file-text"></i>
                        </a>` : ''}
                        ${permissions.can_delete ? `
                        <button type="button" class="btn btn-outline-danger btn-sm delete-product" 
                                data-product-id="${product.id}"
                                data-product-name="${product.name}">
                            <i class="bi bi-trash"></i>
                        </button>` : ''}
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
        
        // Handle pagination clicks
        document.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = this.getAttribute('data-page');
                if (page) {
                    loadProductsPage(page);
                }
            });
        });
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
        document.getElementById('category_id').value = btn.getAttribute('data-category-id');
        document.getElementById('name').value = btn.getAttribute('data-name');
        document.getElementById('model').value = btn.getAttribute('data-model');
        document.getElementById('brand').value = btn.getAttribute('data-brand');
        document.getElementById('price').value = btn.getAttribute('data-price');
        document.getElementById('purchase_cost').value = btn.getAttribute('data-purchase-cost');
        document.getElementById('purchased_from').value = btn.getAttribute('data-purchased-from');
        document.getElementById('sku').value = btn.getAttribute('data-sku');
        document.getElementById('serial_number').value = btn.getAttribute('data-serial-number');
        document.getElementById('stock_quantity').value = btn.getAttribute('data-stock');
        document.getElementById('description').value = btn.getAttribute('data-description');
        document.getElementById('active').checked = btn.getAttribute('data-active') === '1';
        
        // Update modal title
        document.getElementById('productModalLabel').textContent = 'Edit Product';
        
        // Show modal
        new bootstrap.Modal(document.getElementById('productModal')).show();
    }
    
    function deleteProduct(btn) {
        const productId = btn.getAttribute('data-product-id');
        const productName = btn.getAttribute('data-product-name');
        
        if (confirm(`Are you sure you want to delete the product "${productName}"? This action cannot be undone.`)) {
            // Get CSRF token
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            fetch(`/products/${productId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    loadProducts(); // Reload the products table
                } else {
                    showToast(data.message || 'Failed to delete product', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An unexpected error occurred while deleting the product', 'error');
            });
        }
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
                    checkbox.checked = true;
                } else {
                    badge.classList.remove('bg-success');
                    badge.classList.add('bg-secondary');
                    badge.textContent = 'Inactive';
                    checkbox.checked = false;
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
    
    // Toast function is now handled by the global admin layout
    
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
                updateProductsTable(data.data, data.pagination, data.permissions);
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