@extends('layouts.admin')

@section('title', 'Employees')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-person-badge me-2"></i>Employees</h1>
    <div>
       
        <a href="{{ route('employees.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Add Employee
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form id="filterForm">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           placeholder="Name, email, phone, position..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" id="role" name="role">
                        <option value="">All Roles</option>
                        @foreach(\App\Models\Employee::getRoles() as $key => $label)
                            <option value="{{ $key }}" {{ request('role') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="branch_id" class="form-label">Branch</label>
                    <select class="form-select" id="branch_id" name="branch_id">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-primary me-2" onclick="loadEmployees()">
                        <i class="bi bi-search me-2"></i>Search
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                        <i class="bi bi-x-circle me-2"></i>Clear
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div id="loading" class="text-center py-4" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Role</th>
                        <th>Branch</th>
                        <th>Phone</th>
                        <th>Salary</th>
                        <th>Hire Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="employeesTable">
                    @include('employees.table', ['employees' => $employees])
                </tbody>
            </table>
        </div>

        <div id="pagination">
            {{ $employees->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function loadEmployees(page = 1) {
    const loading = document.getElementById('loading');
    const table = document.getElementById('employeesTable');
    const pagination = document.getElementById('pagination');
    
    loading.style.display = 'block';
    table.style.opacity = '0.5';
    
    const formData = new FormData(document.getElementById('filterForm'));
    const params = new URLSearchParams(formData);
    params.append('page', page);
    
    fetch(`{{ route('employees.index') }}?${params}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            table.innerHTML = data.html;
            pagination.innerHTML = data.pagination;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to load employees', 'error');
    })
    .finally(() => {
        loading.style.display = 'none';
        table.style.opacity = '1';
    });
}

function clearFilters() {
    document.getElementById('filterForm').reset();
    loadEmployees();
}

function deleteEmployee(id) {
    if (!confirm('Are you sure you want to delete this employee?')) {
        return;
    }
    
    fetch(`{{ url('employees') }}/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            loadEmployees();
        } else {
            showToast(data.message || 'Failed to delete employee', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Failed to delete employee', 'error');
    });
}

// Auto-search on input
document.getElementById('search').addEventListener('input', function() {
    clearTimeout(this.searchTimeout);
    this.searchTimeout = setTimeout(() => loadEmployees(), 500);
});

// Auto-search on select change
document.getElementById('role').addEventListener('change', () => loadEmployees());
document.getElementById('branch_id').addEventListener('change', () => loadEmployees());

// Handle pagination clicks
document.addEventListener('click', function(e) {
    if (e.target.closest('.pagination a')) {
        e.preventDefault();
        const url = e.target.closest('.pagination a').href;
        const page = new URL(url).searchParams.get('page');
        loadEmployees(page);
    }
});
</script>
@endpush