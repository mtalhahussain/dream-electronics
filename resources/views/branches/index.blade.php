@extends('layouts.admin')

@section('title', 'Branches Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Branches Management</h2>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#branchModal">
        <i class="fas fa-plus"></i> Add New Branch
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
        @if($branches->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Manager</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($branches as $branch)
                        <tr>
                            <td>
                                <strong>{{ $branch->name }}</strong>
                            </td>
                            <td>{{ $branch->location }}</td>
                            <td>{{ $branch->manager_name }}</td>
                            <td>{{ $branch->phone }}</td>
                            <td>
                                @if($branch->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        onclick="editBranch({{ $branch->id }}, '{{ $branch->name }}', '{{ $branch->location }}', '{{ $branch->manager_name }}', '{{ $branch->phone }}', {{ $branch->is_active ? 'true' : 'false' }})">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $branches->links() }}
        @else
            <div class="text-center py-5">
                <i class="fas fa-building fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No branches found</h5>
                <p class="text-muted">Start by adding your first branch location.</p>
            </div>
        @endif
    </div>
</div>

<!-- Branch Modal -->
<div class="modal fade" id="branchModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Add New Branch</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="branchForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="branchId" name="branch_id">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Branch Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="invalid-feedback" id="nameError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="location" class="form-label">Location</label>
                        <input type="text" class="form-control" id="location" name="location" required>
                        <div class="invalid-feedback" id="locationError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="manager_name" class="form-label">Manager Name</label>
                        <input type="text" class="form-control" id="manager_name" name="manager_name" required>
                        <div class="invalid-feedback" id="manager_nameError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone" name="phone" required>
                        <div class="invalid-feedback" id="phoneError"></div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">
                                Active Branch
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span class="spinner-border spinner-border-sm d-none" id="spinner"></span>
                        Save Branch
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

function editBranch(id, name, location, manager_name, phone, is_active) {
    isEditing = true;
    document.getElementById('modalTitle').textContent = 'Edit Branch';
    document.getElementById('branchId').value = id;
    document.getElementById('name').value = name;
    document.getElementById('location').value = location;
    document.getElementById('manager_name').value = manager_name;
    document.getElementById('phone').value = phone;
    document.getElementById('is_active').checked = is_active;
    
    new bootstrap.Modal(document.getElementById('branchModal')).show();
}

// Reset form when modal is hidden
document.getElementById('branchModal').addEventListener('hidden.bs.modal', function () {
    isEditing = false;
    document.getElementById('modalTitle').textContent = 'Add New Branch';
    document.getElementById('branchForm').reset();
    document.getElementById('branchId').value = '';
    clearValidationErrors();
});

// Form submission
document.getElementById('branchForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const spinner = document.getElementById('spinner');
    
    submitBtn.disabled = true;
    spinner.classList.remove('d-none');
    
    clearValidationErrors();
    
    const formData = new FormData(this);
    const branchId = document.getElementById('branchId').value;
    
    const url = isEditing ? `/branches/${branchId}` : '/branches';
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
            showAlert('success', data.message);
            bootstrap.Modal.getInstance(document.getElementById('branchModal')).hide();
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            if (data.errors) {
                showValidationErrors(data.errors);
            } else {
                showAlert('danger', data.message || 'An error occurred');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An unexpected error occurred');
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

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    const alertContainer = document.querySelector('.container-fluid');
    alertContainer.insertAdjacentHTML('afterbegin', alertHtml);
}
</script>
@endpush