@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<!-- KPI Cards -->
<div class="row g-4 mb-4">
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3 class="h2 mb-0 text-success">Rs. {{ number_format($revenueThisMonth ?? 0, 0) }}</h3>
                        <p class="text-muted mb-0 small">Total Revenue</p>
                        <small class="text-muted">This Month</small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-currency-rupee fs-1 text-success opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3 class="h2 mb-0 text-danger">Rs. {{ number_format($expensesThisMonth ?? 0, 0) }}</h3>
                        <p class="text-muted mb-0 small">Expenses</p>
                        <small class="text-muted">This Month</small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-arrow-down-circle fs-1 text-danger opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3 class="h2 mb-0 text-warning">Rs. {{ number_format($outstandingBalance ?? 0, 0) }}</h3>
                        <p class="text-muted mb-0 small">Outstanding Balance</p>
                        <small class="text-muted">Total Unpaid</small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-clock-history fs-1 text-warning opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-md-6 col-xl-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3 class="h2 mb-0 text-info">{{ $dueThisWeekCount ?? 0 }}</h3>
                        <p class="text-muted mb-0 small">Installments Due</p>
                        <small class="text-muted">This Week</small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-calendar-week fs-1 text-info opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-4 mb-4">
    <!-- Collections vs Expenses Chart -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="card-title mb-0">
                    <i class="bi bi-bar-chart me-2"></i>Collections vs Expenses (Last 6 Months)
                </h5>
            </div>
            <div class="card-body">
                @if(empty($monthsLabels ?? []))
                    <div class="text-center py-5">
                        <i class="bi bi-graph-up display-4 text-muted"></i>
                        <p class="text-muted mt-3">No data available yet</p>
                        <a href="{{ route('sales.create') }}" class="btn btn-primary btn-sm">Create First Sale</a>
                    </div>
                @else
                    <canvas id="barCollections" height="100"></canvas>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Sales Status Pie Chart -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="card-title mb-0">
                    <i class="bi bi-pie-chart me-2"></i>Sales Status Split
                </h5>
            </div>
            <div class="card-body">
                @if(($salesStatusSplit['active'] ?? 0) + ($salesStatusSplit['completed'] ?? 0) === 0)
                    <div class="text-center py-4">
                        <i class="bi bi-pie-chart display-4 text-muted"></i>
                        <p class="text-muted mt-3">No sales data</p>
                    </div>
                @else
                    <canvas id="pieSalesStatus" height="150"></canvas>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Installments -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-calendar-check me-2"></i>Upcoming Installments
                </h5>
                <a href="{{ route('sales.index') }}" class="btn btn-outline-primary btn-sm">View All Sales</a>
            </div>
            
            <div class="card-body p-0">
                @if(empty($upcomingInstallments ?? []) || count($upcomingInstallments) === 0)
                    <div class="text-center py-5">
                        <i class="bi bi-calendar-x display-4 text-muted"></i>
                        <p class="text-muted mt-3">No upcoming installments</p>
                        <a href="{{ route('sales.create') }}" class="btn btn-primary btn-sm">Create New Sale</a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Sale #</th>
                                    <th>Customer</th>
                                    <th>Branch</th>
                                    <th>Due Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($upcomingInstallments as $installment)
                                <tr>
                                    <td>
                                        <a href="{{ route('sales.show', $installment->sale_id) }}" class="text-decoration-none">
                                            #{{ $installment->sale_id }}
                                        </a>
                                    </td>
                                    <td>{{ $installment->customer_name }}</td>
                                    <td>{{ $installment->branch_name }}</td>
                                    <td>
                                        <span class="badge bg-{{ \Carbon\Carbon::parse($installment->due_date)->isPast() ? 'danger' : 'secondary' }}">
                                            {{ \Carbon\Carbon::parse($installment->due_date)->format('d-M-Y') }}
                                        </span>
                                    </td>
                                    <td>Rs. {{ number_format($installment->amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $installment->status === 'paid' ? 'success' : ($installment->status === 'partial' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($installment->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($installment->status !== 'paid')
                                            <button class="btn btn-success btn-sm" onclick="markPaidModal({{ $installment->id }}, {{ $installment->amount - ($installment->paid_amount ?? 0) }})">
                                                Mark Paid
                                            </button>
                                        @else
                                            <span class="text-muted">Completed</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Mark Installment Paid Modal -->
<div class="modal fade" id="markPaidModal" tabindex="-1" aria-labelledby="markPaidModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="markPaidModalLabel">Mark Installment Paid</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="markPaidForm">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="installmentId" name="installment_id">
                    
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="amount" name="amount" step="0.01" required>
                        <label for="amount">Amount <span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <select class="form-select" id="paymentMethod" name="payment_method" required>
                            <option value="">Choose method</option>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                        </select>
                        <label for="paymentMethod">Payment Method <span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="date" class="form-control" id="paidAt" name="payment_date" required>
                        <label for="paidAt">Payment Date <span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Mark as Paid</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Chart initialization
    @if(!empty($monthsLabels ?? []))
    const barCtx = document.getElementById('barCollections');
    if (barCtx) {
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: @json($monthsLabels ?? []),
                datasets: [
                    {
                        label: 'Collections',
                        data: @json($collectionsSeries ?? []),
                        backgroundColor: 'rgba(25, 135, 84, 0.7)',
                        borderColor: 'rgba(25, 135, 84, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Expenses',
                        data: @json($expensesSeries ?? []),
                        backgroundColor: 'rgba(220, 53, 69, 0.7)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rs. ' + new Intl.NumberFormat().format(value);
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rs. ' + new Intl.NumberFormat().format(context.parsed.y);
                            }
                        }
                    }
                }
            }
        });
    }
    @endif

    @if(($salesStatusSplit['active'] ?? 0) + ($salesStatusSplit['completed'] ?? 0) > 0)
    const pieCtx = document.getElementById('pieSalesStatus');
    if (pieCtx) {
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Active', 'Completed'],
                datasets: [{
                    data: [
                        {{ $salesStatusSplit['active'] ?? 0 }},
                        {{ $salesStatusSplit['completed'] ?? 0 }}
                    ],
                    backgroundColor: [
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(25, 135, 84, 0.8)'
                    ],
                    borderColor: [
                        'rgba(255, 193, 7, 1)',
                        'rgba(25, 135, 84, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
    @endif

    // Mark paid modal functions
    function markPaidModal(installmentId, remainingAmount) {
        document.getElementById('installmentId').value = installmentId;
        document.getElementById('amount').value = remainingAmount.toFixed(2);
        document.getElementById('amount').max = remainingAmount.toFixed(2);
        document.getElementById('paidAt').value = new Date().toISOString().split('T')[0];
        
        const modal = new bootstrap.Modal(document.getElementById('markPaidModal'));
        modal.show();
    }

    // Form submission
    document.getElementById('markPaidForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
        
        fetch('{{ route("sales.pay-installment") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Payment recorded successfully!', 'success');
                bootstrap.Modal.getInstance(document.getElementById('markPaidModal')).hide();
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast('Error: ' + (data.message || 'Payment failed'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred while processing the payment.', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Mark as Paid';
        });
    });
</script>
@endpush
