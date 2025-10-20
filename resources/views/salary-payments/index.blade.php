@extends('layouts.admin')

@section('title', 'Salary Payments Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-people me-2"></i>Salary Payments Management</h1>
    <div>
        <button type="button" class="btn btn-success me-2" onclick="openGenerateModal()">
            <i class="bi bi-calendar-plus me-2"></i>Generate Monthly Payments
        </button>
        <button type="button" class="btn btn-primary" onclick="openCreateModal()">
            <i class="bi bi-plus-circle me-2"></i>Add Salary Payment
        </button>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('salary-payments.index') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="employee_id" class="form-label">Employee</label>
                    <select class="form-select" id="employee_id" name="employee_id">
                        <option value="">All Employees</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }} ({{ $employee->branch->name ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
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
                <div class="col-md-2">
                    <label for="payment_month" class="form-label">Payment Month</label>
                    <input type="month" class="form-control" id="payment_month" name="payment_month" value="{{ request('payment_month') }}">
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Status</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="submit" class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary d-block w-100">
                        <i class="bi bi-search me-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Salary Payments Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Employee</th>
                        <th>Branch</th>
                        <th>Payment Month</th>
                        <th>Payment Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salaryPayments as $payment)
                    <tr>
                        <td>
                            <strong>{{ $payment->employee->name }}</strong>
                            @if($payment->employee->position)
                                <br><small class="text-muted">{{ $payment->employee->position }}</small>
                            @endif
                        </td>
                        <td>{{ $payment->employee->branch->name ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::createFromFormat('Y-m', $payment->payment_month)->format('M Y') }}</td>
                        <td>{{ $payment->payment_date->format('d-M-Y') }}</td>
                        <td><strong>Rs. {{ number_format($payment->amount, 0) }}</strong></td>
                        <td>
                            @switch($payment->status)
                                @case('paid')
                                    <span class="badge bg-success">Paid</span>
                                    @break
                                @case('pending')
                                    <span class="badge bg-warning">Pending</span>
                                    @break
                                @case('partial')
                                    <span class="badge bg-info">Partial</span>
                                    @break
                            @endswitch
                        </td>
                        <td>{{ $payment->notes ?: 'N/A' }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="editSalaryPayment({{ $payment->id }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteSalaryPayment({{ $payment->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No salary payments found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($salaryPayments->hasPages())
    <div class="card-footer">
        {{ $salaryPayments->links() }}
    </div>
    @endif
</div>

<!-- Salary Payment Modal -->
<div class="modal fade" id="salaryPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="salaryPaymentModalLabel">Add Salary Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="salaryPaymentForm">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="salary_payment_id" name="salary_payment_id">
                    <input type="hidden" id="form_method" name="_method" value="">
                    
                    <div class="mb-3">
                        <label for="employee_id_form" class="form-label">Employee <span class="text-danger">*</span></label>
                        <select class="form-select" id="employee_id_form" name="employee_id" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" data-salary="{{ $employee->salary }}">
                                    {{ $employee->name }} - {{ $employee->position }} (Rs. {{ number_format($employee->salary, 0) }})
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_month_form" class="form-label">Payment Month <span class="text-danger">*</span></label>
                                <input type="month" class="form-control" id="payment_month_form" name="payment_month" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="payment_date" name="payment_date" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="amount_form" class="form-label">Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rs.</span>
                            <input type="number" class="form-control" id="amount_form" name="amount" step="0.01" min="0" required>
                            <button type="button" class="btn btn-outline-secondary" onclick="fillEmployeeSalary()">
                                Use Employee Salary
                            </button>
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label for="status_form" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="status_form" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="partial">Partial</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Save Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Generate Monthly Payments Modal -->
<div class="modal fade" id="generateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate Monthly Salary Payments</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="generateForm">
                <div class="modal-body">
                    @csrf
                    <div class="mb-3">
                        <label for="generate_payment_month" class="form-label">Payment Month <span class="text-danger">*</span></label>
                        <input type="month" class="form-control" id="generate_payment_month" name="payment_month" required>
                        <div class="form-text">This will create pending salary payments for all active employees</div>
                    </div>

                    <div class="mb-3">
                        <label for="generate_branch_id" class="form-label">Branch (Optional)</label>
                        <select class="form-select" id="generate_branch_id" name="branch_id">
                            <option value="">All Branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Leave empty to generate for all branches</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-calendar-plus me-2"></i>Generate Payments
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
        document.getElementById('salaryPaymentModalLabel').textContent = 'Add Salary Payment';
        document.getElementById('salaryPaymentForm').action = '{{ route('salary-payments.store') }}';
        document.getElementById('form_method').value = '';
        document.getElementById('salary_payment_id').value = '';
        document.getElementById('salaryPaymentForm').reset();
        clearValidationErrors();
        
        const modal = new bootstrap.Modal(document.getElementById('salaryPaymentModal'));
        modal.show();
    }

    function openGenerateModal() {
        const modal = new bootstrap.Modal(document.getElementById('generateModal'));
        modal.show();
    }

    function fillEmployeeSalary() {
        const employeeSelect = document.getElementById('employee_id_form');
        const amountInput = document.getElementById('amount_form');
        const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];
        
        if (selectedOption.dataset.salary) {
            amountInput.value = selectedOption.dataset.salary;
        }
    }

    function editSalaryPayment(paymentId) {
        fetch(`/salary-payments/${paymentId}/get`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const payment = data.salaryPayment;
                
                document.getElementById('salaryPaymentModalLabel').textContent = 'Edit Salary Payment';
                document.getElementById('salaryPaymentForm').action = `/salary-payments/${paymentId}`;
                document.getElementById('form_method').value = 'PUT';
                document.getElementById('salary_payment_id').value = paymentId;
                
                document.getElementById('employee_id_form').value = payment.employee_id || '';
                document.getElementById('payment_month_form').value = payment.payment_month || '';
                document.getElementById('payment_date').value = payment.payment_date || '';
                document.getElementById('amount_form').value = payment.amount || '';
                document.getElementById('status_form').value = payment.status || '';
                document.getElementById('notes').value = payment.notes || '';
                
                const modal = new bootstrap.Modal(document.getElementById('salaryPaymentModal'));
                modal.show();
            }
        });
    }

    function deleteSalaryPayment(paymentId) {
        if (confirm('Are you sure you want to delete this salary payment?')) {
            fetch(`/salary-payments/${paymentId}`, {
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

    document.getElementById('salaryPaymentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        clearValidationErrors();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        const isEdit = document.getElementById('salary_payment_id').value !== '';
        
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
                bootstrap.Modal.getInstance(document.getElementById('salaryPaymentModal')).hide();
                setTimeout(() => location.reload(), 1000);
            } else {
                if (data.errors) {
                    displayValidationErrors(data.errors);
                }
                showToast(data.message || 'Failed to save payment', 'error');
            }
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });

    document.getElementById('generateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Generating...';
        
        fetch('{{ route('salary-payments.generate') }}', {
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
                bootstrap.Modal.getInstance(document.getElementById('generateModal')).hide();
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(data.message || 'Failed to generate payments', 'error');
            }
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });

    function showToast(message, type) {
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