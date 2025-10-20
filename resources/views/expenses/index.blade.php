@extends('layouts.admin')

@section('title', 'Office Expenses Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-receipt me-2"></i>Office Expenses Management</h1>
    <button type="button" class="btn btn-primary" onclick="openCreateModal()">
        <i class="bi bi-plus-circle me-2"></i>Add New Expense
    </button>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('expenses.index') }}">
            <div class="row g-3">
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
                <div class="col-md-3">
                    <label for="category" class="form-label">Category</label>
                    <select class="form-select" id="category" name="category">
                        <option value="">All Categories</option>
                        <option value="Office Supplies" {{ request('category') == 'Office Supplies' ? 'selected' : '' }}>Office Supplies</option>
                        <option value="Utilities" {{ request('category') == 'Utilities' ? 'selected' : '' }}>Utilities</option>
                        <option value="Rent" {{ request('category') == 'Rent' ? 'selected' : '' }}>Rent</option>
                        <option value="Maintenance" {{ request('category') == 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                        <option value="Transportation" {{ request('category') == 'Transportation' ? 'selected' : '' }}>Transportation</option>
                        <option value="Other" {{ request('category') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="from" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="from" name="from" value="{{ request('from') }}">
                </div>
                <div class="col-md-2">
                    <label for="to" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="to" name="to" value="{{ request('to') }}">
                </div>
                <div class="col-md-2">
                    <label for="submit" class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary d-block w-100">
                        <i class="bi bi-search me-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Expenses Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Branch</th>
                        <th>Amount</th>
                        <th>Receipt</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                    <tr>
                        <td>{{ $expense->expense_date->format('d-M-Y') }}</td>
                        <td><span class="badge bg-secondary">{{ $expense->category }}</span></td>
                        <td>{{ $expense->description }}</td>
                        <td>{{ $expense->branch->name ?? 'N/A' }}</td>
                        <td><strong>Rs. {{ number_format($expense->amount, 0) }}</strong></td>
                        <td>
                            @if($expense->receipt_path)
                                <a href="{{ Storage::url($expense->receipt_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            @else
                                <span class="text-muted">No receipt</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="editExpense({{ $expense->id }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteExpense({{ $expense->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No expenses found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($expenses->hasPages())
    <div class="card-footer">
        {{ $expenses->links() }}
    </div>
    @endif
</div>

<!-- Expense Modal -->
<div class="modal fade" id="expenseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="expenseModalLabel">Add New Expense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="expenseForm" enctype="multipart/form-data">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="expense_id" name="expense_id">
                    <input type="hidden" id="form_method" name="_method" value="">
                    
                    <div class="mb-3">
                        <label for="branch_id_form" class="form-label">Branch <span class="text-danger">*</span></label>
                        <select class="form-select" id="branch_id_form" name="branch_id" required>
                            <option value="">Select Branch</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label for="category_form" class="form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-select" id="category_form" name="category" required>
                            <option value="">Select Category</option>
                            <option value="Office Supplies">Office Supplies</option>
                            <option value="Utilities">Utilities</option>
                            <option value="Rent">Rent</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Transportation">Transportation</option>
                            <option value="Other">Other</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rs.</span>
                            <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0" required>
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label for="expense_date" class="form-label">Expense Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="expense_date" name="expense_date" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label for="receipt" class="form-label">Receipt (Optional)</label>
                        <input type="file" class="form-control" id="receipt" name="receipt" accept="image/*">
                        <div class="form-text">Upload receipt image (JPG, PNG, max 2MB)</div>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Save Expense
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openCreateModal() {
        document.getElementById('expenseModalLabel').textContent = 'Add New Expense';
        document.getElementById('expenseForm').action = '{{ route('expenses.store') }}';
        document.getElementById('form_method').value = '';
        document.getElementById('expense_id').value = '';
        document.getElementById('expenseForm').reset();
        clearValidationErrors();
        
        const modal = new bootstrap.Modal(document.getElementById('expenseModal'));
        modal.show();
    }

    function editExpense(expenseId) {
        fetch(`/expenses/${expenseId}/get`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const expense = data.expense;
                
                document.getElementById('expenseModalLabel').textContent = 'Edit Expense';
                document.getElementById('expenseForm').action = `/expenses/${expenseId}`;
                document.getElementById('form_method').value = 'PUT';
                document.getElementById('expense_id').value = expenseId;
                
                document.getElementById('branch_id_form').value = expense.branch_id || '';
                document.getElementById('category_form').value = expense.category || '';
                document.getElementById('description').value = expense.description || '';
                document.getElementById('amount').value = expense.amount || '';
                document.getElementById('expense_date').value = expense.expense_date || '';
                
                const modal = new bootstrap.Modal(document.getElementById('expenseModal'));
                modal.show();
            }
        });
    }

    function deleteExpense(expenseId) {
        if (confirm('Are you sure you want to delete this expense?')) {
            fetch(`/expenses/${expenseId}`, {
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
                    setTimeout(() => location.reload(), 1000);
                }
            });
        }
    }

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

    document.getElementById('expenseForm').addEventListener('submit', function(e) {
        e.preventDefault();
        clearValidationErrors();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        const isEdit = document.getElementById('expense_id').value !== '';
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>' + (isEdit ? 'Updating...' : 'Saving...');
        
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
                showToast(data.message, 'success');
                bootstrap.Modal.getInstance(document.getElementById('expenseModal')).hide();
                setTimeout(() => location.reload(), 1000);
            } else {
                if (data.errors) {
                    displayValidationErrors(data.errors);
                }
                showToast(data.message || 'Failed to save expense', 'error');
            }
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });

    function showToast(message, type) {
        // Simple toast implementation
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
</script>
@endpush