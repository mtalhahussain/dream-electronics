@extends('layouts.admin')

@section('title', 'Categories Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Categories Management</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
        <i class="bi bi-plus-lg"></i> Add New Category
    </button>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-body">
        @if($categories->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Icon</th>
                            <th>Color</th>
                            <th>Products</th>
                            <th>Sort Order</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($category->icon)
                                        <i class="bi {{ $category->icon }} me-2" style="color: {{ $category->color }}"></i>
                                    @endif
                                    <strong>{{ $category->name }}</strong>
                                </div>
                            </td>
                            <td>{{ Str::limit($category->description, 50) ?? '-' }}</td>
                            <td>
                                @if($category->icon)
                                    <code>{{ $category->icon }}</code>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge" style="background-color: {{ $category->color }}; color: white;">
                                    {{ $category->color }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $category->products_count ?? 0 }} products</span>
                            </td>
                            <td>{{ $category->sort_order }}</td>
                            <td>
                                @if($category->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        onclick="editCategory({{ $category->id }}, '{{ $category->name }}', '{{ $category->description }}', '{{ $category->icon }}', '{{ $category->color }}', {{ $category->is_active ? 'true' : 'false' }}, {{ $category->sort_order }})">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                @if(auth()->user()->hasRole('admin') || auth()->user()->can('delete-categories'))
                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                        onclick="deleteCategory({{ $category->id }}, '{{ $category->name }}')">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $categories->links() }}
        @else
            <div class="text-center py-5">
                <i class="bi bi-tags fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No categories found</h5>
                <p class="text-muted">Start by adding your first product category.</p>
            </div>
        @endif
    </div>
</div>

<!-- Category Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="categoryForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="categoryId" name="category_id">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="invalid-feedback" id="nameError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        <div class="invalid-feedback" id="descriptionError"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="icon" class="form-label">Icon (Bootstrap Icons)</label>
                                <input type="text" class="form-control" id="icon" name="icon" placeholder="bi-smartphone">
                                <div class="form-text">Examples: bi-smartphone, bi-laptop, bi-tv, bi-house</div>
                                <div class="invalid-feedback" id="iconError"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="color" class="form-label">Color</label>
                                <input type="color" class="form-control form-control-color" id="color" name="color" value="#007bff">
                                <div class="invalid-feedback" id="colorError"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sort_order" class="form-label">Sort Order</label>
                                <input type="number" class="form-control" id="sort_order" name="sort_order" value="0" min="0">
                                <div class="invalid-feedback" id="sort_orderError"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                    <label class="form-check-label" for="is_active">
                                        Active Category
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Icon Preview -->
                    <div class="mb-3" id="iconPreview" style="display: none;">
                        <label class="form-label">Preview</label>
                        <div class="p-3 border rounded">
                            <span class="badge fs-6" id="previewBadge">
                                <i id="previewIcon"></i>
                                <span id="previewName">Category Name</span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span class="spinner-border spinner-border-sm d-none" id="spinner"></span>
                        Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let isEditing = false;

function editCategory(id, name, description, icon, color, is_active, sort_order) {
    isEditing = true;
    document.getElementById('modalTitle').textContent = 'Edit Category';
    document.getElementById('categoryId').value = id;
    document.getElementById('name').value = name;
    document.getElementById('description').value = description || '';
    document.getElementById('icon').value = icon || '';
    document.getElementById('color').value = color;
    document.getElementById('is_active').checked = is_active;
    document.getElementById('sort_order').value = sort_order;
    
    updatePreview();
    new bootstrap.Modal(document.getElementById('categoryModal')).show();
}

function deleteCategory(id, name) {
    if (confirm(`Are you sure you want to delete the category "${name}"? This action cannot be undone.`)) {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                     '{{ csrf_token() }}';
        
        fetch(`/categories/${id}`, {
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
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showToast(data.message || 'Failed to delete category', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An unexpected error occurred while deleting the category', 'error');
        });
    }
}

document.getElementById('categoryModal').addEventListener('hidden.bs.modal', function () {
    isEditing = false;
    document.getElementById('modalTitle').textContent = 'Add New Category';
    document.getElementById('categoryForm').reset();
    document.getElementById('categoryId').value = '';
    document.getElementById('color').value = '#007bff';
    document.getElementById('iconPreview').style.display = 'none';
    clearValidationErrors();
});

// Real-time preview updates
document.getElementById('name').addEventListener('input', updatePreview);
document.getElementById('icon').addEventListener('input', updatePreview);
document.getElementById('color').addEventListener('input', updatePreview);

function updatePreview() {
    const name = document.getElementById('name').value || 'Category Name';
    const icon = document.getElementById('icon').value;
    const color = document.getElementById('color').value;
    
    const previewDiv = document.getElementById('iconPreview');
    const previewBadge = document.getElementById('previewBadge');
    const previewIcon = document.getElementById('previewIcon');
    const previewName = document.getElementById('previewName');
    
    if (name.trim() || icon.trim()) {
        previewDiv.style.display = 'block';
        previewBadge.style.backgroundColor = color;
        previewBadge.style.color = 'white';
        previewIcon.className = icon ? `bi ${icon}` : '';
        previewName.textContent = name;
    } else {
        previewDiv.style.display = 'none';
    }
}

document.getElementById('categoryForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const spinner = document.getElementById('spinner');
    
    submitBtn.disabled = true;
    spinner.classList.remove('d-none');
    
    clearValidationErrors();
    
    const formData = new FormData(this);
    const isActive = document.getElementById('is_active').checked ? 1 : 0;
    formData.set('is_active', isActive);
    const categoryId = document.getElementById('categoryId').value;
    
    const url = isEditing ? `/categories/${categoryId}` : '/categories';
    const method = isEditing ? 'PUT' : 'POST';
    
    if (isEditing) {
        formData.append('_method', 'PUT');
    }
    
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
            setTimeout(() => {
                window.location.reload();
            }, 1000);
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
        spinner.classList.add('d-none');
    });
});

function clearValidationErrors() {
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.classList.remove('is-invalid');
    });
    
    const errorDivs = document.querySelectorAll('.invalid-feedback');
    errorDivs.forEach(div => {
        div.textContent = '';
    });
}

function showValidationErrors(errors) {
    for (const [field, messages] of Object.entries(errors)) {
        const input = document.getElementById(field);
        const errorDiv = document.getElementById(field + 'Error');
        
        if (input && errorDiv) {
            input.classList.add('is-invalid');
            errorDiv.textContent = messages[0];
        }
    }
}

// Toast function is now handled by the global admin layout
</script>
@endpush