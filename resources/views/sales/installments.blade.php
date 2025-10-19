@extends('layouts.admin')

@section('title', 'Installment Management')

@push('styles')
<style>
    .installment-card {
        transition: all 0.3s ease;
        border-left: 4px solid #e5e7eb;
    }
    
    .installment-card.overdue {
        border-left-color: #dc2626;
        background: linear-gradient(45deg, #fef2f2, #ffffff);
    }
    
    .installment-card.due-soon {
        border-left-color: #f59e0b;
        background: linear-gradient(45deg, #fffbeb, #ffffff);
    }
    
    .installment-card.paid {
        border-left-color: #10b981;
        background: linear-gradient(45deg, #f0fdf4, #ffffff);
    }
    
    .installment-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    .payment-status {
        font-weight: 600;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
    }
    
    .status-unpaid { background: #fee2e2; color: #991b1b; }
    .status-partial { background: #fef3c7; color: #92400e; }
    .status-paid { background: #d1fae5; color: #065f46; }
    .status-overdue { background: #fecaca; color: #7f1d1d; }
    
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .filter-section {
        background: linear-gradient(45deg, #f8fafc, #e2e8f0);
        border: 1px solid #cbd5e1;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-0">
                        <i class="bi bi-calendar-check me-2 text-primary"></i>Installment Management
                    </h4>
                    <p class="text-muted mb-0 mt-1">Track and manage customer installment payments</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Sales
                    </a>
                    <button class="btn btn-success" onclick="exportInstallments()">
                        <i class="bi bi-download me-2"></i>Export
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stats-card border-0 shadow-sm">
                    <div class="card-body text-center">
                        <i class="bi bi-exclamation-triangle-fill fs-1 mb-2"></i>
                        <h4 id="overdueCount">{{ $installments->where('status', 'unpaid')->where('due_date', '<', now())->count() }}</h4>
                        <p class="mb-0">Overdue</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white;">
                    <div class="card-body text-center">
                        <i class="bi bi-clock-history fs-1 mb-2"></i>
                        <h4 id="dueSoonCount">{{ $installments->where('status', 'unpaid')->where('due_date', '>=', now())->where('due_date', '<=', now()->addDays(7))->count() }}</h4>
                        <p class="mb-0">Due This Week</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white;">
                    <div class="card-body text-center">
                        <i class="bi bi-check-circle-fill fs-1 mb-2"></i>
                        <h4 id="paidCount">{{ $installments->where('status', 'paid')->count() }}</h4>
                        <p class="mb-0">Paid</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white;">
                    <div class="card-body text-center">
                        <i class="bi bi-currency-dollar fs-1 mb-2"></i>
                        <h4 id="totalAmount">Rs. {{ number_format($installments->sum('amount'), 0) }}</h4>
                        <p class="mb-0">Total Amount</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4 filter-section">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-2">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Status</option>
                            <option value="unpaid" {{ request('status') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                            <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Partial</option>
                            <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="branch_id" class="form-select form-select-sm">
                            <option value="">All Branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_from" class="form-control form-control-sm" 
                            value="{{ request('date_from') }}" placeholder="From Date">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="date_to" class="form-control form-control-sm" 
                            value="{{ request('date_to') }}" placeholder="To Date">
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control form-control-sm" 
                            value="{{ request('search') }}" placeholder="Search customer...">
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Installments List -->
        <div class="row" id="installmentsList">
            @forelse($installments as $installment)
                @php
                    $isOverdue = $installment->status !== 'paid' && $installment->due_date < now();
                    $isDueSoon = $installment->status !== 'paid' && $installment->due_date >= now() && $installment->due_date <= now()->addDays(7);
                    $cardClass = $installment->status === 'paid' ? 'paid' : ($isOverdue ? 'overdue' : ($isDueSoon ? 'due-soon' : ''));
                @endphp
                
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="card installment-card {{ $cardClass }} h-100 border-0 shadow-sm">
                        <div class="card-header bg-transparent border-bottom-0 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">{{ $installment->sale->customer->name }}</h6>
                                <small class="text-muted">{{ $installment->sale->branch->name }}</small>
                            </div>
                            <span class="payment-status status-{{ $installment->status === 'unpaid' && $isOverdue ? 'overdue' : $installment->status }}">
                                {{ $installment->status === 'unpaid' && $isOverdue ? 'Overdue' : ucfirst($installment->status) }}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <small class="text-muted">Installment #</small>
                                    <div class="fw-bold">{{ $installment->installment_number }}</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Due Date</small>
                                    <div class="fw-bold {{ $isOverdue ? 'text-danger' : ($isDueSoon ? 'text-warning' : '') }}">
                                        {{ \Carbon\Carbon::parse($installment->due_date)->format('M d, Y') }}
                                    </div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Amount</small>
                                    <div class="fw-bold">Rs. {{ number_format($installment->amount, 2) }}</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Paid</small>
                                    <div class="fw-bold text-success">Rs. {{ number_format($installment->paid_amount, 2) }}</div>
                                </div>
                            </div>
                            
                            @if($installment->paid_amount < $installment->amount)
                                <div class="mb-3">
                                    <small class="text-muted">Remaining</small>
                                    <div class="fw-bold text-primary">Rs. {{ number_format($installment->amount - $installment->paid_amount, 2) }}</div>
                                </div>
                            @endif

                            <!-- Payment Progress Bar -->
                            <div class="mb-3">
                                @php
                                    $progressPercent = ($installment->paid_amount / $installment->amount) * 100;
                                @endphp
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted">Payment Progress</small>
                                    <small class="fw-bold">{{ number_format($progressPercent, 1) }}%</small>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar {{ $installment->status === 'paid' ? 'bg-success' : 'bg-primary' }}" 
                                        style="width: {{ $progressPercent }}%"></div>
                                </div>
                            </div>

                            <!-- Customer Contact -->
                            <div class="mb-3">
                                <small class="text-muted d-block">Contact</small>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-telephone me-2"></i>
                                    <span>{{ $installment->sale->customer->phone }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent border-top">
                            <div class="d-flex gap-2">
                                @if($installment->paid_amount < $installment->amount)
                                    <button class="btn btn-success btn-sm flex-grow-1" 
                                        onclick="openPaymentModal({{ $installment->id }}, {{ $installment->amount - $installment->paid_amount }}, '{{ $installment->sale->customer->name }}')">
                                        <i class="bi bi-credit-card me-1"></i>Pay Now
                                    </button>
                                @endif
                                <a href="{{ route('sales.show', $installment->sale->id) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-eye me-1"></i>View Sale
                                </a>
                                @if($installment->sale->customer->phone)
                                    <button class="btn btn-outline-info btn-sm" onclick="sendReminder({{ $installment->id }})">
                                        <i class="bi bi-chat-text"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-calendar-x fs-1 text-muted mb-3"></i>
                            <h5 class="text-muted">No installments found</h5>
                            <p class="text-muted">Try adjusting your filters or create new sales to see installments here.</p>
                            <a href="{{ route('sales.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Create New Sale
                            </a>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($installments->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $installments->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Record Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="paymentForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="installmentId" name="installment_id">
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Customer:</strong> <span id="customerName"></span><br>
                        <strong>Remaining Amount:</strong> Rs. <span id="remainingAmount"></span>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-floating">
                                <input type="number" class="form-control" id="paymentAmount" name="amount" step="0.01" required>
                                <label for="paymentAmount">Payment Amount <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-floating">
                                <select class="form-select" id="paymentMethod" name="payment_method" required>
                                    <option value="">Select Method</option>
                                    <option value="cash">Cash</option>
                                    <option value="card">Card</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="cheque">Cheque</option>
                                </select>
                                <label for="paymentMethod">Payment Method <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="paymentDate" name="payment_date" required>
                                <label for="paymentDate">Payment Date <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="referenceNumber" name="reference_number">
                                <label for="referenceNumber">Reference Number (Optional)</label>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea class="form-control" id="notes" name="notes" style="height: 80px"></textarea>
                                <label for="notes">Notes (Optional)</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="submitPayment">
                        <i class="bi bi-check-circle me-2"></i>Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Initialize payment date to today
document.getElementById('paymentDate').value = new Date().toISOString().split('T')[0];

function openPaymentModal(installmentId, remainingAmount, customerName) {
    document.getElementById('installmentId').value = installmentId;
    document.getElementById('remainingAmount').textContent = remainingAmount.toFixed(2);
    document.getElementById('customerName').textContent = customerName;
    document.getElementById('paymentAmount').value = remainingAmount.toFixed(2);
    
    const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    modal.show();
}

document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitPayment');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
    
    const formData = new FormData(this);
    
    fetch('{{ route("sales.pay-installment") }}', {
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
            showToast('Payment recorded successfully!', 'success');
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showToast(data.message || 'Failed to record payment', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred while processing payment', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
        bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
    });
});

function sendReminder(installmentId) {
    if (confirm('Send payment reminder SMS to customer?')) {
        fetch('/sms/send-reminder', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                installment_id: installmentId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Reminder sent successfully!', 'success');
            } else {
                showToast(data.message || 'Failed to send reminder', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred while sending reminder', 'error');
        });
    }
}

function exportInstallments() {
    const params = new URLSearchParams(window.location.search);
    params.append('export', 'excel');
    window.open(`{{ route('sales.installments') }}?${params.toString()}`, '_blank');
}
</script>
@endpush