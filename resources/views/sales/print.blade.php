<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sale Invoice #{{ $sale->id }} - {{ $globalCompanyName ?? 'Dream Electronics' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            .print-break { page-break-before: always; }
            body { font-size: 11px; }
            .container { max-width: 100% !important; }
            .invoice-page { border: 2px solid #000; }
        }
        
        body { 
            font-family: Arial, sans-serif; 
            font-size: 12px;
            line-height: 1.3;
        }
        
        .invoice-page {
            border: 1px solid #000;
            padding: 15px;
            margin: 10px auto;
            max-width: 800px;
            background: white;
        }
        
        .company-header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        
        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .company-address {
            font-size: 12px;
            margin-bottom: 3px;
        }
        
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .invoice-details-box {
            border: 1px solid #000;
            padding: 8px;
            width: 48%;
        }
        
        .section-title {
            font-weight: bold;
            background: #f0f0f0;
            padding: 3px 5px;
            border-bottom: 1px solid #000;
            margin-bottom: 5px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
            border-bottom: 1px dotted #ccc;
        }
        
        .detail-label {
            font-weight: bold;
            width: 40%;
        }
        
        .detail-value {
            width: 60%;
            text-align: right;
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        .products-table th,
        .products-table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        
        .products-table th {
            background: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .payment-terms {
            border: 1px solid #000;
            padding: 10px;
            margin-top: 15px;
        }
        
        .signature-section {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            border-top: 1px solid #000;
            width: 200px;
            text-align: center;
            padding-top: 5px;
        }
    </style>
</head>
<body>
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

    <div class="invoice-page">
        <!-- Company Header -->
        <div class="company-header">
            <div class="company-address d-none">{{ \App\Models\Setting::get('company_name', 'DREAM ELECTRONICS') }}</div>
            <div class="company-name ">{{ $sale->branch->location ?? $sale->branch->name ?? 'Head Office' }}</div>
            <div class="company-address d-none">Contact: {{ $sale->branch->phone ?? \App\Models\Setting::get('company_phone', '+92-300-1234567') }}</div>
            @if(\App\Models\Setting::get('company_email'))
            <div class="company-address d-none">Email: {{ \App\Models\Setting::get('company_email') }}</div>
            @endif
            <div style="margin-top: 10px;">
                <strong>Delivery Slip</strong>
            </div>
        </div>

        <!-- Invoice Information Row -->
        <div class="invoice-info">
            <!-- Account Details -->
            <div class="invoice-details-box">
                <div class="section-title">Account Details</div>
                <div class="detail-row">
                    <span class="detail-label">Account No:</span>
                    <span class="detail-value">{{ $sale->customer->account_number ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Customer Name:</span>
                    <span class="detail-value">{{ $sale->customer->name ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">S/O D/O:</span>
                    <span class="detail-value">{{ $sale->customer->father_husband_name ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Address:</span>
                    <span class="detail-value">{{ Str::limit($sale->customer->address ?? 'N/A', 30) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Mobile No:</span>
                    <span class="detail-value">{{ $sale->customer->phone ?? 'N/A' }}</span>
                </div>
            </div>

            <!-- Invoice Details -->
            <div class="invoice-details-box">
                <div class="section-title">Invoice Details</div>
                <div class="detail-row">
                    <span class="detail-label">Delivery Date:</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Total Price:</span>
                    <span class="detail-value">{{ number_format($sale->total_price) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Duration:</span>
                    <span class="detail-value">{{ $sale->duration_months }} Months</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Actual Advance:</span>
                    <span class="detail-value">{{ number_format($sale->advance_received) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Processing Fee:</span>
                    <span class="detail-value">{{ number_format($sale->processing_fee ?? 0) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Monthly Installment:</span>
                    <span class="detail-value">{{ number_format($sale->monthly_installment) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Balance:</span>
                    <span class="detail-value">{{ number_format($sale->remaining_balance) }}</span>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <table class="products-table">
            <thead>
                <tr>
                    <th style="width: 8%;">Sr#</th>
                    <th style="width: 25%;">Items</th>
                    <th style="width: 15%;">Model</th>
                    <th style="width: 12%;">Brand</th>
                    <th style="width: 8%;">Qty</th>
                    <th style="width: 12%;">Unit Price</th>
                    <th style="width: 12%;">Total</th>
                    <th style="width: 8%;">Serial No</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->saleItems as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->product->name ?? 'N/A' }}</td>
                    <td>{{ $item->product->model ?? 'N/A' }}</td>
                    <td>{{ $item->product->brand ?? 'Generic' }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price) }}</td>
                    <td class="text-right">{{ number_format($item->total_price) }}</td>
                    <td class="text-center">{{ $item->product->serial_number ?? 'N/A' }}</td>
                </tr>
                @endforeach
                @if($sale->saleItems->count() > 0)
                <tr style="border-top: 2px solid #000; font-weight: bold;">
                    <td colspan="6" class="text-right"><strong>Grand Total:</strong></td>
                    <td class="text-right"><strong>{{ number_format($sale->total_price) }}</strong></td>
                    <td></td>
                </tr>
                @if($sale->discount_percent > 0)
                <tr>
                    <td colspan="6" class="text-right">Discount ({{ $sale->discount_percent }}%):</td>
                    <td class="text-right">-{{ number_format(($sale->total_price * $sale->discount_percent) / 100) }}</td>
                    <td></td>
                </tr>
                <tr style="font-weight: bold;">
                    <td colspan="6" class="text-right"><strong>Net Total:</strong></td>
                    <td class="text-right"><strong>{{ number_format($sale->net_total ?? $sale->total_price) }}</strong></td>
                    <td></td>
                </tr>
                @endif
                @endif
            </tbody>
        </table>

        <!-- Delivery Information -->
        <div style="margin: 15px 0;">
            <strong>Delivered By:</strong> ________________________________
            <span style="margin-left: 50px;"><strong>Date & Time:</strong> {{ \Carbon\Carbon::now()->format('d-M-Y H:i') }}</span>
        </div>

        <!-- Urdu Terms and Conditions -->
        <div style="font-size: 10px; margin: 10px 0; padding: 5px; border: 1px solid #ccc; background: #f9f9f9;">
            <strong>شرائط و ضوابط:</strong> یہ ادھار کی فروخت کا معاہدہ ہے۔ ماہانہ قسط کی ادائیگی میں تاخیر کی صورت میں پینالٹی عائد ہوگی۔ گارنٹی اور وارنٹی کمپنی کے اصل شرائط کے مطابق ہوگی۔ رقم کی واپسی صرف خراب سامان کی صورت میں ہوگی۔
        </div>

        <!-- Payment Terms -->
        <div class="payment-terms">
            <div style="text-align: center; font-weight: bold; margin-bottom: 10px;">Payment Schedule</div>
            <div class="detail-row">
                <span class="detail-label">Monthly Installment:</span>
                <span class="detail-value"><strong>Rs. {{ number_format($sale->monthly_installment) }}</strong></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Due Date:</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($sale->sale_date)->addMonth()->format('d M Y') }} (Monthly)</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Remaining Balance:</span>
                <span class="detail-value"><strong>Rs. {{ number_format($sale->remaining_balance) }}</strong></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Total Duration:</span>
                <span class="detail-value">{{ $sale->duration_months }} Months</span>
            </div>
        </div>

        <!-- Guarantor Information -->
        @if($sale->customer->guarantors->count() > 0)
        <div style="margin-top: 15px; border: 1px solid #000; padding: 8px;">
            <div class="section-title">Guarantor Information</div>
            @foreach($sale->customer->guarantors->take(2) as $index => $guarantor)
            <div style="margin-bottom: 8px;">
                <strong>Guarantor {{ $index + 1 }}:</strong> {{ $guarantor->name }} - {{ $guarantor->phone }} - {{ $guarantor->cnic }}
                <br><small>Address: {{ $guarantor->address }}</small>
            </div>
            @endforeach
        </div>
        @endif

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <strong>Customer Signature</strong>
            </div>
            <div class="signature-box">
                <strong>Salesman Signature</strong>
            </div>
            <div class="signature-box">
                <strong>Manager Signature</strong>
            </div>
        </div>

        <!-- Footer Note -->
        <div style="text-align: center; margin-top: 20px; font-size: 10px; color: #666;">
            <strong>Thank you for choosing {{ \App\Models\Setting::get('company_name', 'Dream Electronics') }}!</strong><br>
            For support: {{ $sale->branch->phone ?? \App\Models\Setting::get('company_phone', '+92-300-1234567') }} | Manager: {{ $sale->branch->manager_name ?? 'Branch Manager' }}
        </div>
    </div>
</body>
</html>