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
        <form method="GET" action="{{ route('sales.index') }}" id="filterForm">
            <div class="row g-3">
                <div class="col-md-3">
                    <select class="form-select form-select-sm" name="branch_id" id="branchFilter">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select form-select-sm" name="duration_months" id="durationFilter">
                        <option value="">All Duration</option>
                        <option value="6" {{ request('duration_months') == '6' ? 'selected' : '' }}>6 Months</option>
                        <option value="10" {{ request('duration_months') == '10' ? 'selected' : '' }}>10 Months</option>
                        <option value="12" {{ request('duration_months') == '12' ? 'selected' : '' }}>12 Months</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control form-control-sm" name="sale_date" id="dateFilter" 
                        value="{{ request('sale_date') }}" placeholder="Sale Date">
                </div>
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" name="search" placeholder="Search by customer name, phone, or sale ID..." 
                            id="searchFilter" value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary btn-sm w-100" title="Clear Filters">
                        <i class="bi bi-x-circle"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Active Filters Display -->
    @if(request()->hasAny(['branch_id', 'duration_months', 'sale_date', 'search']))
        <div class="card-body border-bottom py-2">
            <div class="d-flex align-items-center flex-wrap gap-2">
                <small class="text-muted me-2">Active filters:</small>
                
                @if(request('branch_id'))
                    @php $selectedBranch = $branches->find(request('branch_id')) @endphp
                    <span class="badge bg-primary">
                        Branch: {{ $selectedBranch ? $selectedBranch->name : 'Unknown' }}
                        <a href="{{ request()->fullUrlWithQuery(['branch_id' => null]) }}" class="text-white ms-1">×</a>
                    </span>
                @endif
                
                @if(request('duration_months'))
                    <span class="badge bg-info">
                        Duration: {{ request('duration_months') }} months
                        <a href="{{ request()->fullUrlWithQuery(['duration_months' => null]) }}" class="text-white ms-1">×</a>
                    </span>
                @endif
                
                @if(request('sale_date'))
                    <span class="badge bg-warning">
                        Date: {{ \Carbon\Carbon::parse(request('sale_date'))->format('M d, Y') }}
                        <a href="{{ request()->fullUrlWithQuery(['sale_date' => null]) }}" class="text-white ms-1">×</a>
                    </span>
                @endif
                
                @if(request('search'))
                    <span class="badge bg-success">
                        Search: "{{ request('search') }}"
                        <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="text-white ms-1">×</a>
                    </span>
                @endif
                
                <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary btn-sm ms-2">
                    <i class="bi bi-x-circle me-1"></i>Clear All
                </a>
            </div>
        </div>
    @endif
    
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
                @if(request()->hasAny(['branch_id', 'duration_months', 'sale_date', 'search']))
                    <h5 class="text-muted mt-3">No sales found matching your filters</h5>
                    <p class="text-muted">Try adjusting your search criteria or clearing filters</p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>Clear Filters
                        </a>
                        <a href="{{ route('sales.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Create New Sale
                        </a>
                    </div>
                @else
                    <h5 class="text-muted mt-3">No sales found</h5>
                    <p class="text-muted">Get started by creating your first sale</p>
                    <a href="{{ route('sales.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Create First Sale
                    </a>
                @endif
            </div>
        @endif
    </div>
    
    @if(isset($sales) && method_exists($sales, 'hasPages') && $sales->hasPages())
    <div class="card-footer bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Showing {{ $sales->firstItem() }} to {{ $sales->lastItem() }} of {{ $sales->total() }} results
                @if(request()->hasAny(['branch_id', 'duration_months', 'sale_date', 'search']))
                    <span class="text-primary">(filtered)</span>
                @endif
            </div>
            {{ $sales->links() }}
        </div>
    </div>
    @elseif($sales->total() > 0)
    <div class="card-footer bg-white">
        <div class="text-muted small">
            {{ $sales->total() }} result{{ $sales->total() > 1 ? 's' : '' }} found
            @if(request()->hasAny(['branch_id', 'duration_months', 'sale_date', 'search']))
                <span class="text-primary">(filtered)</span>
            @endif
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
    const salesPrintBaseUrl = '{{ url("sales") }}';
    
    function recordPayment(saleId) {
        document.getElementById('saleId').value = saleId;
        document.getElementById('paymentDate').value = new Date().toISOString().split('T')[0];
        
        const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
        paymentModal.show();
    }
    
    function printInvoice(saleId) {
        if (!saleId) {
            console.error('Sale ID is required for printing');
            return;
        }
        // Open print view in new window
        const printUrl = `${salesPrintBaseUrl}/${saleId}/print`;
        window.open(printUrl, '_blank');
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
    
    // Auto-submit form when filters change
    document.getElementById('branchFilter').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
    
    document.getElementById('durationFilter').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
    
    document.getElementById('dateFilter').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });
    
    // Submit search on Enter key press
    document.getElementById('searchFilter').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            document.getElementById('filterForm').submit();
        }
    });
</script>
@endpush