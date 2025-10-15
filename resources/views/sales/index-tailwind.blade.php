@extends('layouts.admin')

@section('title', 'Sales - Dream Electronics')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-shopping-cart me-2"></i>Sales</h1>
    @can('create-sales')
    <a href="{{ route('sales.create') }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i>New Sale
    </a>
    @endcan
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Sale #</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Branch</th>
                        <th>Total</th>
                        <th>Remaining</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                    <tr>
                        <td>#{{ $sale->id }}</td>
                        <td>{{ $sale->sale_date->format('d-M-Y') }}</td>
                        <td>{{ $sale->customer->name }}</td>
                        <td>{{ $sale->branch->name }}</td>
                        <td>Rs. {{ number_format($sale->net_total, 2) }}</td>
                        <td>Rs. {{ number_format($sale->remaining_balance, 2) }}</td>
                        <td>
                            <span class="badge {{ $sale->status == 'completed' ? 'bg-success' : 'bg-warning' }}">
                                {{ ucfirst($sale->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('sales.show', $sale) }}" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($sale->status == 'pending')
                                    @can('pay-installments')
                                    <button type="button" class="btn btn-sm btn-outline-success" 
                                            onclick="openPaymentModal({{ $sale->id }})">
                                        <i class="fas fa-money-bill"></i>
                                    </button>
                                    @endcan
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">No sales found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{ $sales->links() }}
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Make Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="paymentForm">
                @csrf
                <div class="modal-body">
                    <div id="installmentsList"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Process Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openPaymentModal(saleId) {
    fetch(`/api/sales/${saleId}/installments`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayInstallments(data.installments);
                new bootstrap.Modal(document.getElementById('paymentModal')).show();
            }
        });
}

function displayInstallments(installments) {
    const container = document.getElementById('installmentsList');
    container.innerHTML = installments.map(installment => `
        <div class="card mb-2">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <strong>Installment #${installment.installment_number}</strong><br>
                        <small>Due: ${installment.due_date}</small><br>
                        <small>Amount: Rs. ${installment.amount}</small><br>
                        <small>Paid: Rs. ${installment.paid_amount}</small>
                    </div>
                    <div>
                        <span class="badge bg-${installment.status === 'paid' ? 'success' : installment.status === 'partial' ? 'warning' : 'danger'}">
                            ${installment.status}
                        </span>
                    </div>
                </div>
                ${installment.status !== 'paid' ? `
                <button type="button" class="btn btn-sm btn-primary mt-2" 
                        onclick="makePayment(${installment.id}, ${installment.amount - installment.paid_amount})">
                    Pay Rs. ${installment.amount - installment.paid_amount}
                </button>
                ` : ''}
            </div>
        </div>
    `).join('');
}

function makePayment(installmentId, amount) {
    // This would typically open another modal or redirect to payment form
    window.location.href = `/sales/installments/${installmentId}/pay`;
}
</script>
@endpush
@endsection