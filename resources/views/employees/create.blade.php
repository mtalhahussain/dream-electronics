@extends('layouts.admin')

@section('title', 'Add Employee')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-person-plus me-2"></i>Add Employee</h1>
    <a href="{{ route('employees.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Employees
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Employee Information</h5>
            </div>
            <div class="card-body">
                <form id="employeeForm" action="{{ route('employees.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cnic" class="form-label">CNIC <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('cnic') is-invalid @enderror" 
                                       id="cnic" name="cnic" value="{{ old('cnic') }}" 
                                       placeholder="12345-6789012-3" required>
                                @error('cnic')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone') }}" 
                                       placeholder="03xxxxxxxxx" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" 
                                       placeholder="employee@example.com">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="position" class="form-label">Position <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('position') is-invalid @enderror" 
                                       id="position" name="position" value="{{ old('position') }}" 
                                       placeholder="Sales Manager, Technician, etc." required>
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select @error('role') is-invalid @enderror" 
                                        id="role" name="role" required onchange="updatePermissions()">
                                    <option value="">Select Role</option>
                                    @foreach(\App\Models\Employee::getRoles() as $key => $label)
                                        <option value="{{ $key }}" {{ old('role') == $key ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="branch_id" class="form-label">Branch <span class="text-danger">*</span></label>
                                <select class="form-select @error('branch_id') is-invalid @enderror" 
                                        id="branch_id" name="branch_id" required>
                                    <option value="">Select Branch</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('branch_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="salary" class="form-label">Monthly Salary <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control @error('salary') is-invalid @enderror" 
                                           id="salary" name="salary" value="{{ old('salary') }}" 
                                           step="0.01" min="0" required>
                                </div>
                                @error('salary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="hire_date" class="form-label">Hire Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('hire_date') is-invalid @enderror" 
                               id="hire_date" name="hire_date" value="{{ old('hire_date') }}" required>
                        @error('hire_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Permissions Section -->
                    <div class="mb-3" id="permissions-section" style="display: none;">
                        <label class="form-label">Permissions</label>
                        <div class="row">
                            @foreach(\App\Models\Employee::PERMISSIONS as $key => $label)
                                <div class="col-md-6 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input permission-checkbox" type="checkbox" 
                                               id="permission_{{ $key }}" name="permissions[]" value="{{ $key }}"
                                               {{ in_array($key, old('permissions', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="permission_{{ $key }}">
                                            {{ $label }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active Employee
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <a href="{{ route('employees.index') }}" class="btn btn-secondary me-2">Cancel</a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bi bi-check-circle me-2"></i>Create Employee
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Instructions</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li><i class="bi bi-info-circle text-primary me-2"></i>All fields marked with * are required</li>
                    <li><i class="bi bi-info-circle text-primary me-2"></i>CNIC should be in format: 12345-6789012-3</li>
                    <li><i class="bi bi-info-circle text-primary me-2"></i>Phone number should include country code</li>
                    <li><i class="bi bi-info-circle text-primary me-2"></i>Email address must be unique</li>
                    <li><i class="bi bi-info-circle text-primary me-2"></i>Salary is in Pakistani Rupees (PKR)</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const rolePermissions = {
        'employee': @json(\App\Models\Employee::getDefaultPermissions('employee')),
        'product_manager': @json(\App\Models\Employee::getDefaultPermissions('product_manager')),
        'sales_manager': @json(\App\Models\Employee::getDefaultPermissions('sales_manager')),
        'admin': @json(\App\Models\Employee::getDefaultPermissions('admin'))
    };

    function updatePermissions() {
        const role = document.getElementById('role').value;
        const permissionsSection = document.getElementById('permissions-section');
        const checkboxes = document.querySelectorAll('.permission-checkbox');
        
        if (role && role !== 'employee') {
            permissionsSection.style.display = 'block';
            
            // Uncheck all first
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Check default permissions for selected role
            if (rolePermissions[role]) {
                rolePermissions[role].forEach(permission => {
                    const checkbox = document.getElementById('permission_' + permission);
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });
            }
        } else {
            permissionsSection.style.display = 'none';
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updatePermissions();
    });

    // AJAX form submission
    document.getElementById('employeeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;
        
        // Disable button and show loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Creating...';
        
        // Clear previous errors
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.style.display = 'none');
        
        // Create FormData from form
        const formData = new FormData(this);
        
        fetch(this.action, {
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
                // Show success message
                showToast(data.message, 'success');
                
                // Redirect to employees index
                setTimeout(() => {
                    window.location.href = '{{ route("employees.index") }}';
                }, 1500);
            } else {
                // Handle validation errors
                if (data.errors) {
                    Object.keys(data.errors).forEach(field => {
                        const input = document.querySelector(`[name="${field}"]`);
                        if (input) {
                            input.classList.add('is-invalid');
                            const feedback = input.parentNode.querySelector('.invalid-feedback');
                            if (feedback) {
                                feedback.textContent = data.errors[field][0];
                                feedback.style.display = 'block';
                            }
                        }
                    });
                }
                showToast(data.message || 'Validation failed', 'error');
                
                // Re-enable button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred while creating the employee', 'error');
            
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
</script>
@endpush