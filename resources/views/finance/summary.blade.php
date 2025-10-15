@extends('layouts.admin')

@section('title', 'Finance Summary - Dream Electronics')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-chart-line me-2"></i>Finance Summary</h1>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('finance.summary') }}">
            <div class="row">
                <div class="col-md-3">
                    <label for="from" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="from" name="from" value="{{ $summary['filters']['from'] }}">
                </div>
                <div class="col-md-3">
                    <label for="to" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="to" name="to" value="{{ $summary['filters']['to'] }}">
                </div>
                <div class="col-md-3">
                    <label for="branch_id" class="form-label">Branch</label>
                    <select class="form-select" id="branch_id" name="branch_id">
                        <option value="">All Branches</option>
                        <!-- Add branch options here -->
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="make" class="form-label">Product Make</label>
                    <input type="text" class="form-control" id="make" name="make" value="{{ $summary['filters']['make'] }}" placeholder="Brand or Model">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i>Apply Filters
                    </button>
                    <a href="{{ route('finance.summary') }}" class="btn btn-secondary">
                        <i class="fas fa-undo me-1"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Income</h5>
                        <h3>Rs. {{ number_format($summary['in_total'], 2) }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-arrow-up fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Total Expenses</h5>
                        <h3>Rs. {{ number_format($summary['out_total'], 2) }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-arrow-down fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card {{ $summary['net_total'] >= 0 ? 'bg-info' : 'bg-warning' }} text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title">Net Total</h5>
                        <h3>Rs. {{ number_format($summary['net_total'], 2) }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas {{ $summary['net_total'] >= 0 ? 'fa-chart-line' : 'fa-chart-line-down' }} fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Breakdown -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-arrow-up text-success me-1"></i>Income Breakdown</h5>
            </div>
            <div class="card-body">
                @if($summary['in_breakdown']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($summary['in_breakdown'] as $item)
                                <tr>
                                    <td>{{ $item->category }}</td>
                                    <td class="text-end">Rs. {{ number_format($item->total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No income transactions found.</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-arrow-down text-danger me-1"></i>Expense Breakdown</h5>
            </div>
            <div class="card-body">
                @if($summary['out_breakdown']->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($summary['out_breakdown'] as $item)
                                <tr>
                                    <td>{{ $item->category }}</td>
                                    <td class="text-end">Rs. {{ number_format($item->total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No expense transactions found.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection