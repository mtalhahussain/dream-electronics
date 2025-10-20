@extends('layouts.admin')

@section('title', 'Finance Management')

@push('styles')
<style>
    .finance-section {
        margin-bottom: 2rem;
    }
    
    .section-tabs .nav-link {
        border-radius: 0;
        border-bottom: 3px solid transparent;
    }
    
    .section-tabs .nav-link.active {
        border-bottom-color: #007bff;
        background: none;
        border-top: none;
        border-left: none;
        border-right: none;
    }
    
    .summary-card {
        border-left: 4px solid #007bff;
        transition: transform 0.2s;
    }
    
    .summary-card:hover {
        transform: translateY(-2px);
    }
    
    .profit-positive {
        border-left-color: #28a745 !important;
    }
    
    .profit-negative {
        border-left-color: #dc3545 !important;
    }
    
    .expense-card {
        border-left-color: #ffc107 !important;
    }
    
    .income-card {
        border-left-color: #28a745 !important;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-graph-up me-2"></i>Finance Management</h1>
    <div>
        <div class="btn-group me-2" role="group">
            <a href="{{ route('expenses.index') }}" class="btn btn-outline-warning">
                <i class="bi bi-receipt me-2"></i>Manage Expenses
            </a>
            <a href="{{ route('stock-credits.index') }}" class="btn btn-outline-info">
                <i class="bi bi-box me-2"></i>Manage Stock Credits
            </a>
            <a href="{{ route('salary-payments.index') }}" class="btn btn-outline-success">
                <i class="bi bi-people me-2"></i>Manage Salaries
            </a>
        </div>
        <a href="{{ route('finance.summary') }}" class="btn btn-outline-primary">
            <i class="bi bi-bar-chart me-2"></i>View Summary
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Filters</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('finance.index') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="from" class="form-label">Date Range</label>
                    <div class="input-group">
                        <span class="input-group-text">From</span>
                        <input type="date" class="form-control" id="from" name="from" value="{{ $from }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="to" class="form-label">&nbsp;</label>
                    <div class="input-group">
                        <span class="input-group-text">To</span>
                        <input type="date" class="form-control" id="to" name="to" value="{{ $to }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <label for="make" class="form-label">Make/Brand</label>
                    <input type="text" class="form-control" id="make" name="make" value="{{ $make }}" placeholder="Brand or Model">
                </div>
                <div class="col-md-2">
                    <label for="branch_id" class="form-label">Shop Location</label>
                    <select class="form-select" id="branch_id" name="branch_id">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $branchId == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="section" class="form-label">Section</label>
                    <select class="form-select" id="section" name="section">
                        <option value="products" {{ $activeSection == 'products' ? 'selected' : '' }}>Products</option>
                        <option value="expenses" {{ $activeSection == 'expenses' ? 'selected' : '' }}>Expenses</option>
                        <option value="stock_credit" {{ $activeSection == 'stock_credit' ? 'selected' : '' }}>Stock Credit</option>
                        <option value="salary" {{ $activeSection == 'salary' ? 'selected' : '' }}>Salary</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-search me-2"></i>Apply Filters
                    </button>
                    <a href="{{ route('finance.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-clockwise me-2"></i>Reset
                    </a>
                    <button type="button" class="btn btn-success ms-2" onclick="exportData()">
                        <i class="bi bi-download me-2"></i>Export
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card summary-card income-card">
            <div class="card-body text-center">
                <i class="bi bi-arrow-up-circle text-success fs-2"></i>
                <h6 class="mt-2">Products In</h6>
                <h5>Rs. {{ number_format($summary['products_in_total'], 0) }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card summary-card">
            <div class="card-body text-center">
                <i class="bi bi-arrow-down-circle text-info fs-2"></i>
                <h6 class="mt-2">Products Out</h6>
                <h5>Rs. {{ number_format($summary['products_out_total'], 0) }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card summary-card expense-card">
            <div class="card-body text-center">
                <i class="bi bi-receipt text-warning fs-2"></i>
                <h6 class="mt-2">Office Expenses</h6>
                <h5>Rs. {{ number_format($summary['expenses_total'], 0) }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card summary-card">
            <div class="card-body text-center">
                <i class="bi bi-box text-secondary fs-2"></i>
                <h6 class="mt-2">Stock Credit</h6>
                <h5>Rs. {{ number_format($summary['stock_credit_total'], 0) }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card summary-card expense-card">
            <div class="card-body text-center">
                <i class="bi bi-people text-warning fs-2"></i>
                <h6 class="mt-2">Salary</h6>
                <h5>Rs. {{ number_format($summary['salary_total'], 0) }}</h5>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card summary-card {{ $summary['net_profit'] >= 0 ? 'profit-positive' : 'profit-negative' }}">
            <div class="card-body text-center">
                <i class="bi bi-graph-up {{ $summary['net_profit'] >= 0 ? 'text-success' : 'text-danger' }} fs-2"></i>
                <h6 class="mt-2">Net Profit</h6>
                <h5 class="{{ $summary['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                    Rs. {{ number_format($summary['net_profit'], 0) }}
                </h5>
            </div>
        </div>
    </div>
</div>

<!-- Section Tabs -->
<ul class="nav nav-tabs section-tabs mb-4" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $activeSection == 'products' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#products-tab" type="button">
            <i class="bi bi-box-seam me-2"></i>Products (In / Out)
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $activeSection == 'expenses' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#expenses-tab" type="button">
            <i class="bi bi-receipt me-2"></i>Office Expenses
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $activeSection == 'stock_credit' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#stock-credit-tab" type="button">
            <i class="bi bi-box me-2"></i>Stock Credit
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $activeSection == 'salary' ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#salary-tab" type="button">
            <i class="bi bi-people me-2"></i>Salary
        </button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content">
    <!-- Products Tab -->
    <div class="tab-pane fade {{ $activeSection == 'products' ? 'show active' : '' }}" id="products-tab">
        <div class="row">
            <!-- Products In -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="bi bi-arrow-up-circle me-2"></i>Products In (Stock Received)</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($productsIn as $item)
                                    <tr>
                                        <td>{{ $item->purchase_date->format('d-M') }}</td>
                                        <td>
                                            <strong>{{ $item->product->name ?? 'N/A' }}</strong>
                                            @if($item->product->model ?? false)
                                                <br><small class="text-muted">{{ $item->product->model }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>Rs. {{ number_format($item->total_cost, 0) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No products received in this period</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Products Out -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="bi bi-arrow-down-circle me-2"></i>Products Out (Sales)</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Product</th>
                                        <th>Customer</th>
                                        <th>Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($productsOut as $item)
                                    <tr>
                                        <td>{{ $item->sale->sale_date->format('d-M') }}</td>
                                        <td>
                                            <strong>{{ $item->product->name ?? 'N/A' }}</strong>
                                            <br><small class="text-muted">Qty: {{ $item->quantity }}</small>
                                        </td>
                                        <td>{{ $item->sale->customer->name ?? 'N/A' }}</td>
                                        <td>Rs. {{ number_format($item->total_price, 0) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No products sold in this period</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Office Expenses Tab -->
    <div class="tab-pane fade {{ $activeSection == 'expenses' ? 'show active' : '' }}" id="expenses-tab">
        <div class="card">
            <div class="card-header bg-warning">
                <h6 class="mb-0"><i class="bi bi-receipt me-2"></i>Office Expenses</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Branch</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($officeExpenses as $expense)
                            <tr>
                                <td>{{ $expense->expense_date->format('d-M-Y') }}</td>
                                <td><span class="badge bg-secondary">{{ $expense->category }}</span></td>
                                <td>{{ $expense->description }}</td>
                                <td>{{ $expense->branch->name ?? 'N/A' }}</td>
                                <td><strong>Rs. {{ number_format($expense->amount, 0) }}</strong></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No office expenses recorded in this period</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Stock Credit Tab -->
    <div class="tab-pane fade {{ $activeSection == 'stock_credit' ? 'show active' : '' }}" id="stock-credit-tab">
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0"><i class="bi bi-box me-2"></i>Stock Credit (Items received without payment)</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Supplier</th>
                                <th>Quantity</th>
                                <th>Invoice #</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stockCredits as $credit)
                            <tr>
                                <td>{{ $credit->purchase_date->format('d-M-Y') }}</td>
                                <td>
                                    <strong>{{ $credit->product->name ?? 'N/A' }}</strong>
                                    @if($credit->product->model ?? false)
                                        <br><small class="text-muted">{{ $credit->product->model }}</small>
                                    @endif
                                </td>
                                <td>{{ $credit->supplier ?? 'N/A' }}</td>
                                <td>{{ $credit->quantity }}</td>
                                <td>{{ $credit->invoice_number ?? 'N/A' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No stock credit items in this period</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Salary Tab -->
    <div class="tab-pane fade {{ $activeSection == 'salary' ? 'show active' : '' }}" id="salary-tab">
        <div class="card">
            <div class="card-header bg-warning">
                <h6 class="mb-0"><i class="bi bi-people me-2"></i>Employee Salaries</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Position</th>
                                <th>Branch</th>
                                <th>Payment Date</th>
                                <th>Amount Paid</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($salaryExpenses as $payment)
                            <tr>
                                <td>
                                    <strong>{{ $payment->employee->name }}</strong>
                                    @if($payment->employee->phone)
                                        <br><small class="text-muted">{{ $payment->employee->phone }}</small>
                                    @endif
                                </td>
                                <td><span class="badge bg-primary">{{ $payment->employee->position }}</span></td>
                                <td>{{ $payment->employee->branch->name ?? 'N/A' }}</td>
                                <td>{{ $payment->payment_date->format('d-M-Y') }}</td>
                                <td><strong>Rs. {{ number_format($payment->amount, 0) }}</strong></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No salary payments found in this period</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function exportData() {
        const params = new URLSearchParams(window.location.search);
        params.set('export', 'true');
        window.open(`{{ route('finance.index') }}?${params.toString()}`, '_blank');
    }

    // Update section parameter when tab is clicked
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (e) {
            const target = e.target.getAttribute('data-bs-target');
            let section = 'products';
            
            switch(target) {
                case '#expenses-tab': section = 'expenses'; break;
                case '#stock-credit-tab': section = 'stock_credit'; break;
                case '#salary-tab': section = 'salary'; break;
            }
            
            const form = document.querySelector('form');
            const sectionInput = form.querySelector('[name="section"]');
            sectionInput.value = section;
        });
    });
</script>
@endpush