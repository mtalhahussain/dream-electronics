<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale Invoice #{{ $sale->id }} - {{ config('app.name', 'Dream Electronics') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            .print-break { page-break-before: always; }
            body { font-size: 12px; }
            .container { max-width: 100% !important; }
        }
        
        .invoice-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .company-logo {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: bold;
        }
        
        .invoice-details {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
        }
        
        .table-invoice {
            border: 2px solid #dee2e6;
        }
        
        .table-invoice th {
            background: #e9ecef;
            border: 1px solid #dee2e6;
            font-weight: 600;
        }
        
        .table-invoice td {
            border: 1px solid #dee2e6;
        }
        
        .payment-summary {
            background: linear-gradient(45deg, #f8f9fa, #e9ecef);
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
        }
        
        .signature-section {
            border-top: 2px solid #dee2e6;
            margin-top: 40px;
            padding-top: 30px;
        }
        
        .footer-note {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            font-size: 12px;
            color: #6c757d;
        }
    </style>
</head>
<body class="bg-white">
    <div class="container mt-4">
        <!-- Print Controls -->
        <div class="no-print mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4>Sale Invoice Preview</h4>
                <div>
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="bi bi-printer me-2"></i>Print Invoice
                    </button>
                    <a href="{{ route('sales.show', $sale) }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Sale
                    </a>
                </div>
            </div>
            <hr>
        </div>

        <!-- Invoice Header -->
        <div class="invoice-header">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <div class="company-logo">
                        DE
                    </div>
                </div>
                <div class="col-md-6">
                    <h2 class="mb-1">{{ config('app.name', 'Dream Electronics') }}</h2>
                    <p class="mb-0 opacity-75">{{ $sale->branch->location ?? 'Electronics & Appliances' }}</p>
                    <p class="mb-0 opacity-75">Phone: {{ $sale->branch->phone ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <h3 class="mb-1">INVOICE</h3>
                    <p class="mb-0"><strong>Invoice #{{ $sale->id }}</strong></p>
                    <p class="mb-0">Date: {{ \Carbon\Carbon::parse($sale->sale_date)->format('F j, Y') }}</p>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Customer Information -->
            <div class="col-md-6 mb-4">
                <div class="invoice-details h-100">
                    <h5 class="mb-3 text-primary">
                        <i class="bi bi-person-circle me-2"></i>Bill To
                    </h5>
                    <div class="customer-info">
                        <p class="mb-2"><strong>{{ $sale->customer->name }}</strong></p>
                        <p class="mb-1">
                            <i class="bi bi-telephone me-2"></i>{{ $sale->customer->phone }}
                        </p>
                        @if($sale->customer->cnic)
                            <p class="mb-1">
                                <i class="bi bi-credit-card me-2"></i>CNIC: {{ $sale->customer->cnic }}
                            </p>
                        @endif
                        @if($sale->customer->address)
                            <p class="mb-0">
                                <i class="bi bi-geo-alt me-2"></i>{{ $sale->customer->address }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sale Information -->
            <div class="col-md-6 mb-4">
                <div class="invoice-details h-100">
                    <h5 class="mb-3 text-success">
                        <i class="bi bi-building me-2"></i>Branch Information
                    </h5>
                    <div class="branch-info">
                        <p class="mb-2"><strong>{{ $sale->branch->name }}</strong></p>
                        @if($sale->branch->location)
                            <p class="mb-1">
                                <i class="bi bi-geo-alt me-2"></i>{{ $sale->branch->location }}
                            </p>
                        @endif
                        @if($sale->branch->manager_name)
                            <p class="mb-1">
                                <i class="bi bi-person me-2"></i>Manager: {{ $sale->branch->manager_name }}
                            </p>
                        @endif
                        @if($sale->branch->phone)
                            <p class="mb-0">
                                <i class="bi bi-telephone me-2"></i>{{ $sale->branch->phone }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sale Items -->
        <div class="mb-4">
            <h5 class="mb-3 text-primary">
                <i class="bi bi-basket me-2"></i>Items Purchased
            </h5>
            <div class="table-responsive">
                <table class="table table-invoice mb-0">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="45%">Product Description</th>
                            <th width="15%" class="text-center">Quantity</th>
                            <th width="15%" class="text-end">Unit Price</th>
                            <th width="20%" class="text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->saleItems as $index => $item)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $item->product->name }}</strong>
                                @if($item->product->category)
                                    <br><small class="text-muted">Category: {{ $item->product->category->name }}</small>
                                @endif
                            </td>
                            <td class="text-center">{{ $item->quantity }}</td>
                            <td class="text-end">Rs. {{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-end"><strong>Rs. {{ number_format($item->total_price, 2) }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Subtotal:</td>
                            <td class="text-end fw-bold">Rs. {{ number_format($sale->saleItems->sum('total_price'), 2) }}</td>
                        </tr>
                        @if($sale->discount_percent > 0)
                        <tr>
                            <td colspan="4" class="text-end">Discount ({{ $sale->discount_percent }}%):</td>
                            <td class="text-end text-success">- Rs. {{ number_format($sale->saleItems->sum('total_price') * $sale->discount_percent / 100, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="table-primary">
                            <td colspan="4" class="text-end fw-bold fs-5">Grand Total:</td>
                            <td class="text-end fw-bold fs-5">Rs. {{ number_format($sale->total_price, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="payment-summary h-100">
                    <h5 class="mb-3 text-warning">
                        <i class="bi bi-credit-card me-2"></i>Payment Summary
                    </h5>
                    <div class="row g-2">
                        <div class="col-6">
                            <small class="text-muted">Total Amount:</small>
                            <div class="fw-bold">Rs. {{ number_format($sale->total_price, 2) }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Advance Paid:</small>
                            <div class="fw-bold text-success">Rs. {{ number_format($sale->advance_received, 2) }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Installments Paid:</small>
                            <div class="fw-bold text-info">Rs. {{ number_format($sale->installments->sum('paid_amount'), 2) }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Remaining Balance:</small>
                            <div class="fw-bold text-danger">Rs. {{ number_format($sale->remaining_balance, 2) }}</div>
                        </div>
                    </div>
                    
                    @php
                        $totalPaid = $sale->advance_received + $sale->installments->sum('paid_amount');
                        $paymentProgress = $sale->total_price > 0 ? ($totalPaid / $sale->total_price) * 100 : 0;
                    @endphp
                    
                    <div class="mt-3">
                        <small class="text-muted">Payment Progress: {{ number_format($paymentProgress, 1) }}%</small>
                        <div class="progress mt-1" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: {{ $paymentProgress }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="payment-summary h-100">
                    <h5 class="mb-3 text-info">
                        <i class="bi bi-calendar-event me-2"></i>Installment Plan
                    </h5>
                    @if($sale->installments->count() > 0)
                        <div class="row g-2">
                            <div class="col-6">
                                <small class="text-muted">Duration:</small>
                                <div class="fw-bold">{{ $sale->duration_months }} months</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Monthly Amount:</small>
                                <div class="fw-bold">Rs. {{ number_format($sale->monthly_installment, 2) }}</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Start Date:</small>
                                <div class="fw-bold">{{ \Carbon\Carbon::parse($sale->installments->first()->due_date)->format('M d, Y') }}</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">End Date:</small>
                                <div class="fw-bold">{{ \Carbon\Carbon::parse($sale->installments->last()->due_date)->format('M d, Y') }}</div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            @php
                                $paidInstallments = $sale->installments->where('status', 'paid')->count();
                                $totalInstallments = $sale->installments->count();
                            @endphp
                            <small class="text-muted">Installments Paid: {{ $paidInstallments }}/{{ $totalInstallments }}</small>
                            <div class="progress mt-1" style="height: 8px;">
                                <div class="progress-bar bg-info" style="width: {{ $totalInstallments > 0 ? ($paidInstallments / $totalInstallments) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    @else
                        <p class="text-muted">No installment plan</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Terms and Conditions -->
        <div class="footer-note mb-4">
            <h6 class="mb-2">Terms & Conditions:</h6>
            <ul class="mb-0 small">
                <li>All installments must be paid on or before the due date.</li>
                <li>Late payment charges may apply after the due date.</li>
                <li>This invoice is computer generated and requires no signature.</li>
                <li>For any queries, please contact the branch mentioned above.</li>
                <li>Goods once sold cannot be returned without proper documentation.</li>
            </ul>
        </div>

        <!-- Signature Section -->
        <div class="signature-section no-print">
            <div class="row">
                <div class="col-md-4 text-center">
                    <div style="border-top: 2px solid #000; margin-top: 60px; padding-top: 10px;">
                        <strong>Customer Signature</strong>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div style="border-top: 2px solid #000; margin-top: 60px; padding-top: 10px;">
                        <strong>Sales Representative</strong>
                    </div>
                </div>
                <div class="col-md-4 text-center">
                    <div style="border-top: 2px solid #000; margin-top: 60px; padding-top: 10px;">
                        <strong>Authorized Signature</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-4 text-muted small">
            <p class="mb-0">Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
            <p class="mb-0">{{ config('app.name', 'Dream Electronics') }} - Multi-Branch Electronics Store</p>
        </div>
    </div>

    <script>
        // Auto-print when opened in new window (optional)
        window.addEventListener('load', function() {
            if (window.location.search.includes('auto_print=true')) {
                setTimeout(() => window.print(), 1000);
            }
        });
    </script>
</body>
</html>