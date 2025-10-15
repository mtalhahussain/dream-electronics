@extends('layouts.admin')

@section('title', 'Sale Details')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Sale #{{ $sale->id }}</h1>
        <div class="flex space-x-2">
            <a href="{{ route('sales.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                Back to Sales
            </a>
        </div>
    </div>

    <!-- Sale Information -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Sale Information</h3>
            <div class="space-y-2">
                <p><span class="font-medium">Sale Date:</span> {{ $sale->sale_date }}</p>
                <p><span class="font-medium">Status:</span> 
                    <span class="px-2 py-1 rounded-full text-xs font-medium {{ $sale->status == 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst($sale->status) }}
                    </span>
                </p>
                <p><span class="font-medium">Branch:</span> {{ $sale->branch->name }}</p>
            </div>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Customer Information</h3>
            <div class="space-y-2">
                <p><span class="font-medium">Name:</span> {{ $sale->customer->name }}</p>
                <p><span class="font-medium">Phone:</span> {{ $sale->customer->phone }}</p>
                <p><span class="font-medium">Address:</span> {{ $sale->customer->address }}</p>
                <p><span class="font-medium">CNIC:</span> {{ $sale->customer->cnic }}</p>
            </div>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Payment Information</h3>
            <div class="space-y-2">
                <p><span class="font-medium">Total Price:</span> Rs. {{ number_format($sale->total_price, 2) }}</p>
                <p><span class="font-medium">Discount:</span> {{ $sale->discount_percent }}%</p>
                <p><span class="font-medium">Net Total:</span> Rs. {{ number_format($sale->net_total, 2) }}</p>
                <p><span class="font-medium">Advance:</span> Rs. {{ number_format($sale->advance_received, 2) }}</p>
                <p><span class="font-medium">Remaining:</span> Rs. {{ number_format($sale->remaining_balance, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Sale Items -->
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Sale Items</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($sale->saleItems as $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $item->product->name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $item->quantity }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Rs. {{ number_format($item->unit_price, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Rs. {{ number_format($item->total_price, 2) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Installments -->
    @if($sale->installments->count() > 0)
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Installments</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Installment #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paid Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($sale->installments as $installment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            #{{ $installment->installment_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($installment->due_date)->format('d-M-Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Rs. {{ number_format($installment->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $installment->status == 'paid' ? 'bg-green-100 text-green-800' : ($installment->status == 'partial' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($installment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Rs. {{ number_format($installment->paid_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            @if($installment->status != 'paid')
                            <button onclick="openPaymentModal({{ $installment->id }}, {{ $installment->amount - $installment->paid_amount }})" 
                                    class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs">
                                Pay
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Payment History -->
    @if($sale->installments->pluck('payments')->flatten()->count() > 0)
    <div>
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment History</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Installment #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($sale->installments->pluck('payments')->flatten() as $payment)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($payment->payment_date)->format('d-M-Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            #{{ $payment->installment->installment_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            Rs. {{ number_format($payment->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ ucfirst($payment->payment_method) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">Record Payment</h3>
            <form id="paymentForm">
                @csrf
                <input type="hidden" id="installment_id" name="installment_id">
                
                <div class="mb-4">
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                    <input type="number" id="amount" name="amount" step="0.01" min="0" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>

                <div class="mb-4">
                    <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-1">Payment Date</label>
                    <input type="date" id="payment_date" name="payment_date" value="{{ date('Y-m-d') }}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                </div>

                <div class="mb-4">
                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                    <select id="payment_method" name="payment_method" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="cheque">Cheque</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closePaymentModal()" 
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                        Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openPaymentModal(installmentId, remainingAmount) {
    document.getElementById('installment_id').value = installmentId;
    document.getElementById('amount').value = remainingAmount.toFixed(2);
    document.getElementById('amount').max = remainingAmount.toFixed(2);
    document.getElementById('paymentModal').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}

document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
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
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while processing the payment.');
    });
});
</script>
@endsection