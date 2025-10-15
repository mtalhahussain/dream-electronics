@extends('layouts.admin')

@section('title', 'Sales')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">
            <i class="bi bi-cart3 me-2"></i>Sales Management
        </h5>
        <a href="{{ route('sales.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>Create New Sale
        </a>
    </div>
    
    <!-- Filter Toolbar -->
    <div class="card-body border-bottom bg-light">
        <div class="row g-3">
            <div class="col-md-3">
                <select class="form-select form-select-sm" id="branchFilter">
                    <option value="">All Branches</option>
                    <!-- Will be populated via server-side data -->
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select form-select-sm" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select form-select-sm" id="durationFilter">
                    <option value="">All Duration</option>
                    <option value="6">6 Months</option>
                    <option value="10">10 Months</option>
                    <option value="12">12 Months</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control form-control-sm" id="dateFilter" placeholder="Sale Date">
            </div>
            <div class="col-md-3">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" placeholder="Search sales..." id="searchFilter">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        @if(($sales ?? collect())->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Sale #</th>
                            <th>Customer</th>
                            <th>Branch</th>
                            <th>Items</th>
                            <th>Total Amount</th>
                            <th>Monthly Installment</th>
                            <th>Status</th>
                            <th>Sale Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $sale)
                        <tr>
                            <td>
                                <a href="{{ route('sales.show', $sale) }}" class="text-decoration-none fw-bold">
                                    #{{ $sale->id }}
                                </a>
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $sale->customer->name ?? 'N/A' }}</strong>
                                    @if($sale->customer->phone ?? false)
                                        <br><small class="text-muted">{{ $sale->customer->phone }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $sale->branch->name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <div>
                                    <span class="fw-bold">{{ $sale->saleItems->count() ?? 0 }} items</span>
                                    @if($sale->saleItems->count() > 0)
                                        <br><small class="text-muted">{{ $sale->saleItems->first()->product->name ?? 'N/A' }}{{ $sale->saleItems->count() > 1 ? ' +' . ($sale->saleItems->count() - 1) . ' more' : '' }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div>
                                    <strong>Rs. {{ number_format($sale->net_total ?? 0, 2) }}</strong>
                                    @if(($sale->discount_percent ?? 0) > 0)
                                        <br><small class="text-success">{{ $sale->discount_percent }}% discount</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div>
                                    <strong>Rs. {{ number_format($sale->monthly_installment ?? 0, 2) }}</strong>
                                    <br><small class="text-muted">{{ $sale->duration_months ?? 0 }} months</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ ($sale->status ?? '') === 'completed' ? 'success' : 'warning' }}">
                                    {{ ucfirst($sale->status ?? 'N/A') }}
                                </span>
                                @if(($sale->remaining_balance ?? 0) > 0)
                                    <br><small class="text-danger">Rs. {{ number_format($sale->remaining_balance, 2) }} remaining</small>
                                @endif
                            </td>
                            <td>
                                <small>{{ \Carbon\Carbon::parse($sale->sale_date ?? $sale->created_at)->format('d-M-Y') }}</small>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('sales.show', $sale) }}">
                                            <i class="bi bi-eye me-2"></i>View Details
                                        </a></li>
                                        @if(($sale->status ?? '') === 'pending')
                                            <li><a class="dropdown-item" href="#" onclick="recordPayment({{ $sale->id }})">
                                                <i class="bi bi-credit-card me-2"></i>Record Payment
                                            </a></li>
                                        @endif
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="#" onclick="printInvoice({{ $sale->id }})">
                                            <i class="bi bi-printer me-2"></i>Print Invoice
                                        </a></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteSale({{ $sale->id }})">
                                            <i class="bi bi-trash me-2"></i>Delete
                                        </a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-cart3 display-4 text-muted"></i>
                <p class="text-muted mt-3">No sales found</p>
                <a href="{{ route('sales.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Create First Sale
                </a>
            </div>
        @endif
    </div>
    
    @if(isset($sales) && method_exists($sales, 'hasPages') && $sales->hasPages())
    <div class="card-footer bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Showing {{ $sales->firstItem() }} to {{ $sales->lastItem() }} of {{ $sales->total() }} results
            </div>
            {{ $sales->links() }}
        </div>
    </div>
    @endif
</div>

<!-- Quick Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Record Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="paymentForm">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="saleId" name="sale_id">
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Quick Payment:</strong> This will record a payment for the next due installment.
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="number" class="form-control" id="paymentAmount" name="amount" step="0.01" required>
                        <label for="paymentAmount">Payment Amount <span class="text-danger">*</span></label>
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
                        <input type="date" class="form-control" id="paymentDate" name="payment_date" required>
                        <label for="paymentDate">Payment Date <span class="text-danger">*</span></label>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="form-floating">
                        <textarea class="form-control" id="paymentNotes" name="notes" placeholder="Payment Notes" style="height: 60px"></textarea>
                        <label for="paymentNotes">Notes (Optional)</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-2"></i>Record Payment
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
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Warning!</strong> Deleting this sale will also remove all related installments and payment records.
                </div>
                <p>Are you sure you want to delete this sale? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Sale</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function recordPayment(saleId) {
        document.getElementById('saleId').value = saleId;
        document.getElementById('paymentDate').value = new Date().toISOString().split('T')[0];
        
        const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
        paymentModal.show();
    }
    
    function printInvoice(saleId) {
        // Open print view in new window
        window.open(`/sales/${saleId}/print`, '_blank');
    }
    
    function deleteSale(saleId) {
        document.getElementById('deleteForm').action = `/sales/${saleId}`;
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
    
    // Payment form submission
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
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
                bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
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
            submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Record Payment';
        });
    });
    
    // Filter functionality
    document.getElementById('searchFilter').addEventListener('input', function() {
        // Implementation for search filter
    });
    
    document.getElementById('branchFilter').addEventListener('change', function() {
        // Implementation for branch filter
    });
    
    document.getElementById('statusFilter').addEventListener('change', function() {
        // Implementation for status filter
    });
    
    document.getElementById('durationFilter').addEventListener('change', function() {
        // Implementation for duration filter
    });
    
    document.getElementById('dateFilter').addEventListener('change', function() {
        // Implementation for date filter
    });
</script>
@endpush