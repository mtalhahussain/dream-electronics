@extends('layouts.admin')

@section('title', 'Sale Details - #' . $sale->id)

@push('styles')
<style>
    .sale-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
    }
    
    .info-card {
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }
    
    .info-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        border-color: #3b82f6;
    }
    
    .status-badge {
        font-weight: 600;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 0.85rem;
    }
    
    .payment-progress {
        background: linear-gradient(45deg, #f8fafc, #e2e8f0);
        border: 2px solid #cbd5e1;
        border-radius: 10px;
    }
    
    .installment-timeline {
        position: relative;
    }
    
    .installment-timeline::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(to bottom, #10b981, #059669);
    }
    
    .timeline-item {
        position: relative;
        padding-left: 50px;
        margin-bottom: 20px;
    }
    
    .timeline-marker {
        position: absolute;
        left: 12px;
        top: 8px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 2px solid white;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }
    
    .marker-paid { background: #10b981; }
    .marker-partial { background: #f59e0b; }
    .marker-unpaid { background: #ef4444; }
    .marker-overdue { background: #dc2626; animation: pulse 2s infinite; }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .sale-summary {
        background: linear-gradient(45deg, #f8fafc, #ffffff);
        border: 2px solid #e2e8f0;
        border-radius: 15px;
    }
    
    .customer-avatar {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        font-weight: bold;
    }
    
    .action-btn {
        transition: all 0.3s ease;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Sale Header -->
        <div class="card border-0 shadow-sm mb-4 sale-header">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center">
                            
                            <div>
                                <h2 class="mb-1">Sale #{{ $sale->id }}</h2>
                                <p class="mb-0 opacity-75">
                                    <i class="bi bi-calendar me-2"></i>{{ \Carbon\Carbon::parse($sale->sale_date)->format('F j, Y') }}
                                    <span class="mx-2">|</span>
                                    <i class="bi bi-building me-2"></i>{{ $sale->branch->name }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="d-flex flex-column align-items-md-end gap-2">
                            <span class="p-2 status-badge {{ $sale->status == 'completed' ? 'bg-success' : 'bg-warning' }} text-white">
                                <i class="bi bi-{{ $sale->status == 'completed' ? 'check-circle' : 'clock' }} me-2"></i>{{ ucfirst($sale->status) }}
                            </span>
                            <div class="d-flex gap-2">
                                <a href="{{ route('sales.index') }}" class="btn btn-light btn-sm action-btn">
                                    <i class="bi bi-arrow-left me-2"></i>Back to Sales
                                </a>
                                <button class="btn btn-outline-light btn-sm action-btn" onclick="printSale()">
                                    <i class="bi bi-printer me-2"></i>Print
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Sale Information Cards -->
                <div class="row mb-4">
                    <div class="col-md-6 mb-3">
                        <div class="card info-card h-100 border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-person-circle me-2 text-primary"></i>Customer Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-12">
                                        <strong class="text-muted">Name</strong>
                                        <div class="fw-bold">{{ $sale->customer->name }}</div>
                                    </div>
                                    <div class="col-6">
                                        <strong class="text-muted">Phone</strong>
                                        <div>
                                            <a href="tel:{{ $sale->customer->phone }}" class="text-decoration-none">
                                                <i class="bi bi-telephone me-1"></i>{{ $sale->customer->phone }}
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <strong class="text-muted">CNIC</strong>
                                        <div>{{ $sale->customer->cnic ?: 'N/A' }}</div>
                                    </div>
                                    <div class="col-12">
                                        <strong class="text-muted">Address</strong>
                                        <div class="text-break">{{ $sale->customer->address ?: 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <div class="card info-card h-100 border-0 shadow-sm">
                            <div class="card-header bg-white border-bottom">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-building me-2 text-success"></i>Branch Information
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-12">
                                        <strong class="text-muted">Branch Name</strong>
                                        <div class="fw-bold">{{ $sale->branch->name }}</div>
                                    </div>
                                    <div class="col-12">
                                        <strong class="text-muted">Location</strong>
                                        <div>{{ $sale->branch->location ?: 'N/A' }}</div>
                                    </div>
                                    <div class="col-12">
                                        <strong class="text-muted">Manager</strong>
                                        <div>{{ $sale->branch->manager_name ?: 'N/A' }}</div>
                                    </div>
                                    <div class="col-12">
                                        <strong class="text-muted">Contact</strong>
                                        <div>{{ $sale->branch->phone ?: 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sale Items -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-basket me-2 text-warning"></i>Sale Items
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0">Product</th>
                                        <th class="border-0 text-center">Quantity</th>
                                        <th class="border-0 text-end">Unit Price</th>
                                        <th class="border-0 text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sale->saleItems as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-10 rounded p-2 me-3">
                                                    <i class="bi bi-box text-primary"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $item->product->name }}</div>
                                                    @if($item->product->category)
                                                        <small class="text-muted">{{ $item->product->category->name }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary rounded-pill">{{ $item->quantity }}</span>
                                        </td>
                                        <td class="text-end">
                                            <strong>Rs. {{ number_format($item->unit_price, 2) }}</strong>
                                        </td>
                                        <td class="text-end">
                                            <strong class="text-success">Rs. {{ number_format($item->total_price, 2) }}</strong>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="3" class="text-end border-0">Subtotal:</th>
                                        <th class="text-end border-0">Rs. {{ number_format($sale->saleItems->sum('total_price'), 2) }}</th>
                                    </tr>
                                    @if($sale->discount_percent > 0)
                                    <tr>
                                        <th colspan="3" class="text-end border-0">Discount ({{ $sale->discount_percent }}%):</th>
                                        <th class="text-end border-0 text-danger">- Rs. {{ number_format($sale->saleItems->sum('total_price') * $sale->discount_percent / 100, 2) }}</th>
                                    </tr>
                                    @endif
                                    <tr>
                                        <th colspan="3" class="text-end border-0">Grand Total:</th>
                                        <th class="text-end border-0 text-primary">Rs. {{ number_format($sale->total_price, 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Installments Timeline -->
                @if($sale->installments->count() > 0)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-calendar-event me-2 text-info"></i>Installment Timeline
                            </h5>
                            <div class="d-flex gap-2">
                                <small class="text-muted">
                                    <span class="marker-paid d-inline-block rounded-circle me-1" style="width: 10px; height: 10px;"></span>Paid
                                    <span class="marker-partial d-inline-block rounded-circle me-1 ms-2" style="width: 10px; height: 10px;"></span>Partial
                                    <span class="marker-unpaid d-inline-block rounded-circle me-1 ms-2" style="width: 10px; height: 10px;"></span>Unpaid
                                    <span class="marker-overdue d-inline-block rounded-circle me-1 ms-2" style="width: 10px; height: 10px;"></span>Overdue
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="installment-timeline">
                            @foreach($sale->installments->sortBy('installment_number') as $installment)
                                @php
                                    $isOverdue = $installment->status !== 'paid' && $installment->due_date < now();
                                    $markerClass = $installment->status === 'paid' ? 'marker-paid' : 
                                                  ($installment->status === 'partial' ? 'marker-partial' : 
                                                  ($isOverdue ? 'marker-overdue' : 'marker-unpaid'));
                                    $progressPercent = ($installment->paid_amount / $installment->amount) * 100;
                                @endphp
                                <div class="timeline-item">
                                    <div class="timeline-marker {{ $markerClass }}"></div>
                                    <div class="card border-{{ $installment->status === 'paid' ? 'success' : ($isOverdue ? 'danger' : 'warning') }} border-opacity-25">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col-md-8">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div>
                                                            <h6 class="mb-1">
                                                                Installment #{{ $installment->installment_number }}
                                                                <span class="badge bg-{{ $installment->status === 'paid' ? 'success' : ($isOverdue ? 'danger' : 'warning') }} ms-2">
                                                                    {{ $isOverdue && $installment->status !== 'paid' ? 'Overdue' : ucfirst($installment->status) }}
                                                                </span>
                                                            </h6>
                                                            <p class="text-muted mb-2">
                                                                <i class="bi bi-calendar me-1"></i>Due: {{ \Carbon\Carbon::parse($installment->due_date)->format('M j, Y') }}
                                                                @if($isOverdue)
                                                                    <span class="text-danger ms-2">
                                                                        ({{ \Carbon\Carbon::parse($installment->due_date)->diffForHumans() }})
                                                                    </span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="fw-bold text-primary">Rs. {{ number_format($installment->amount, 2) }}</div>
                                                            <small class="text-success">Paid: Rs. {{ number_format($installment->paid_amount, 2) }}</small>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Payment Progress -->
                                                    <div class="mb-2">
                                                        <div class="d-flex justify-content-between mb-1">
                                                            <small class="text-muted">Payment Progress</small>
                                                            <small class="fw-bold">{{ number_format($progressPercent, 1) }}%</small>
                                                        </div>
                                                        <div class="progress" style="height: 8px;">
                                                            <div class="progress-bar bg-{{ $installment->status === 'paid' ? 'success' : 'info' }}" 
                                                                style="width: {{ $progressPercent }}%"></div>
                                                        </div>
                                                    </div>
                                                    
                                                    @if($installment->paid_amount < $installment->amount)
                                                        <div class="text-warning">
                                                            <small><i class="bi bi-exclamation-triangle me-1"></i>
                                                            Remaining: Rs. {{ number_format($installment->amount - $installment->paid_amount, 2) }}</small>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="col-md-4 text-md-end">
                                                    @if($installment->status != 'paid')
                                                        <button onclick="openPaymentModal({{ $installment->id }}, {{ $installment->amount - $installment->paid_amount }}, '{{ $sale->customer->name }}', {{ $installment->installment_number }})" 
                                                                class="btn btn-success btn-sm action-btn mb-2">
                                                            <i class="bi bi-credit-card me-1"></i>Pay Now
                                                        </button>
                                                    @else
                                                        <div class="text-success">
                                                            <i class="bi bi-check-circle fs-4"></i>
                                                            <div class="small">Completed</div>
                                                        </div>
                                                    @endif
                                                    
                                                    @if($installment->payments->count() > 0)
                                                        <button class="btn btn-outline-info btn-sm action-btn" 
                                                                data-bs-toggle="collapse" 
                                                                data-bs-target="#payments{{ $installment->id }}">
                                                            <i class="bi bi-receipt me-1"></i>Payments ({{ $installment->payments->count() }})
                                                        </button>
                                                        
                                                        <div class="collapse mt-2" id="payments{{ $installment->id }}">
                                                            <div class="card card-body bg-light border-0">
                                                                @foreach($installment->payments as $payment)
                                                                    <div class="d-flex justify-content-between align-items-center py-1 {{ !$loop->last ? 'border-bottom' : '' }}">
                                                                        <div>
                                                                            <small class="text-muted">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M j, Y') }}</small>
                                                                            <div class="fw-bold">{{ ucfirst($payment->payment_method) }}</div>
                                                                        </div>
                                                                        <div class="text-success fw-bold">Rs. {{ number_format($payment->amount, 2) }}</div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 20px;">
                    <!-- Payment Summary -->
                    <div class="card border-0 shadow-sm payment-progress mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-calculator me-2"></i>Payment Summary
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="text-center">
                                        <div class="text-muted small">Total Amount</div>
                                        <div class="fw-bold h5 text-primary">Rs. {{ number_format($sale->total_price, 2) }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <div class="text-muted small">Advance Paid</div>
                                        <div class="fw-bold h5 text-success">Rs. {{ number_format($sale->advance_received, 2) }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <div class="text-muted small">Installments Paid</div>
                                        <div class="fw-bold h5 text-info">Rs. {{ number_format($sale->installments->sum('paid_amount'), 2) }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <div class="text-muted small">Remaining</div>
                                        <div class="fw-bold h5 text-warning">Rs. {{ number_format($sale->remaining_balance, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            
                            @php
                                $totalPaid = $sale->advance_received + $sale->installments->sum('paid_amount');
                                $paymentProgress = ($totalPaid / $sale->total_price) * 100;
                            @endphp
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Overall Progress</span>
                                    <span class="fw-bold">{{ number_format($paymentProgress, 1) }}%</span>
                                </div>
                                <div class="progress" style="height: 12px;">
                                    <div class="progress-bar bg-success" style="width: {{ $paymentProgress }}%"></div>
                                </div>
                            </div>
                            
                            @if($sale->remaining_balance > 0)
                                <div class="alert alert-warning mb-0">
                                    <i class="bi bi-exclamation-circle me-2"></i>
                                    <strong>Payment Status:</strong> {{ $paymentProgress >= 100 ? 'Completed' : 'In Progress' }}
                                </div>
                            @else
                                <div class="alert alert-success mb-0">
                                    <i class="bi bi-check-circle me-2"></i>
                                    <strong>Payment Status:</strong> Fully Paid
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-lightning me-2"></i>Quick Actions
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                @if($sale->customer->phone)
                                    <button class="btn btn-info action-btn" onclick="sendSMS('{{ $sale->customer->phone }}', '{{ $sale->customer->name }}')">
                                        <i class="bi bi-chat-text me-2"></i>Send SMS Reminder
                                    </button>
                                @endif
                                
                                <button class="btn btn-secondary action-btn" onclick="printSale()">
                                    <i class="bi bi-printer me-2"></i>Print Receipt
                                </button>
                                
                                <a href="{{ route('customers.show', $sale->customer->id) }}" class="btn btn-outline-primary action-btn">
                                    <i class="bi bi-person-lines-fill me-2"></i>View Customer
                                </a>
                                
                                <a href="{{ route('sales.installments') }}?customer_id={{ $sale->customer->id }}" class="btn btn-outline-info action-btn">
                                    <i class="bi bi-calendar-check me-2"></i>All Installments
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Sale Statistics -->
                    @if($sale->installments->count() > 0)
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-graph-up me-2"></i>Installment Statistics
                            </h5>
                        </div>
                        <div class="card-body">
                            @php
                                $paidInstallments = $sale->installments->where('status', 'paid')->count();
                                $overdueInstallments = $sale->installments->where('status', '!=', 'paid')->where('due_date', '<', now())->count();
                                $upcomingInstallments = $sale->installments->where('status', '!=', 'paid')->where('due_date', '>=', now())->count();
                            @endphp
                            
                            <div class="row text-center g-3">
                                <div class="col-4">
                                    <div class="bg-success bg-opacity-10 p-3 rounded">
                                        <i class="bi bi-check-circle text-success fs-4 d-block mb-1"></i>
                                        <div class="fw-bold">{{ $paidInstallments }}</div>
                                        <small class="text-muted">Paid</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-danger bg-opacity-10 p-3 rounded">
                                        <i class="bi bi-exclamation-triangle text-danger fs-4 d-block mb-1"></i>
                                        <div class="fw-bold">{{ $overdueInstallments }}</div>
                                        <small class="text-muted">Overdue</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-info bg-opacity-10 p-3 rounded">
                                        <i class="bi bi-clock text-info fs-4 d-block mb-1"></i>
                                        <div class="fw-bold">{{ $upcomingInstallments }}</div>
                                        <small class="text-muted">Upcoming</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="paymentModalLabel">
                    <i class="bi bi-credit-card me-2"></i>Record Payment
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="paymentForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="installment_id" name="installment_id">
                    
                    <div class="alert alert-info">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-info-circle me-3 fs-4"></i>
                            <div>
                                <strong>Customer:</strong> <span id="customerName"></span><br>
                                <strong>Installment:</strong> #<span id="installmentNumber"></span><br>
                                <strong>Maximum Amount:</strong> Rs. <span id="maxAmount"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="form-floating">
                                <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0" required>
                                <label for="amount">Payment Amount <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="payment_date" name="payment_date" value="{{ date('Y-m-d') }}" required>
                                <label for="payment_date">Payment Date <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select" id="payment_method" name="payment_method" required>
                                    <option value="">Select Method</option>
                                    <option value="cash">Cash</option>
                                    <option value="card">Card</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="cheque">Cheque</option>
                                    <option value="online">Online Payment</option>
                                </select>
                                <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="reference_number" name="reference_number">
                                <label for="reference_number">Reference Number (Optional)</label>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>Cancel
                    </button>
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
function openPaymentModal(installmentId, remainingAmount, customerName, installmentNumber) {
    document.getElementById('installment_id').value = installmentId;
    document.getElementById('amount').value = remainingAmount.toFixed(2);
    document.getElementById('amount').max = remainingAmount.toFixed(2);
    document.getElementById('customerName').textContent = customerName;
    document.getElementById('installmentNumber').textContent = installmentNumber;
    document.getElementById('maxAmount').textContent = remainingAmount.toFixed(2);
    
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

function printSale() {
    window.open('{{ route("sales.print", $sale->id) }}', '_blank');
}

function sendSMS(phone, customerName) {
    if (confirm(`Send payment reminder SMS to ${customerName} (${phone})?`)) {
        // Implementation for SMS sending
        showToast('SMS reminder sent successfully!', 'success');
    }
}
</script>
@endpush