@extends('layouts.admin')

@section('title', 'Create Sale')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Create New Sale</h1>
        <a href="{{ route('sales.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
            Back to Sales
        </a>
    </div>

    <form id="saleForm" action="{{ route('sales.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Basic Information -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Branch Selection -->
            <div>
                <label for="branch_id" class="block text-sm font-medium text-gray-700 mb-1">Branch</label>
                <select name="branch_id" id="branch_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Select Branch</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
                @error('branch_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Customer Selection -->
            <div>
                <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                <select name="customer_id" id="customer_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Select Customer</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }} - {{ $customer->phone }}
                        </option>
                    @endforeach
                </select>
                @error('customer_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Duration -->
            <div>
                <label for="duration_months" class="block text-sm font-medium text-gray-700 mb-1">Installment Duration</label>
                <select name="duration_months" id="duration_months" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Select Duration</option>
                    <option value="6" {{ old('duration_months') == '6' ? 'selected' : '' }}>6 Months</option>
                    <option value="10" {{ old('duration_months') == '10' ? 'selected' : '' }}>10 Months</option>
                    <option value="12" {{ old('duration_months') == '12' ? 'selected' : '' }}>12 Months</option>
                </select>
                @error('duration_months')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Sale Items -->
        <div>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Sale Items</h3>
                <button type="button" id="addItem" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg">
                    Add Item
                </button>
            </div>

            <div id="itemsContainer" class="space-y-4">
                <!-- Items will be added here dynamically -->
            </div>
            
            @error('items')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Pricing Information -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 bg-gray-50 p-4 rounded-lg">
            <div>
                <label for="subtotal" class="block text-sm font-medium text-gray-700 mb-1">Subtotal</label>
                <input type="text" id="subtotal" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-100" readonly>
            </div>

            <div>
                <label for="discount_percent" class="block text-sm font-medium text-gray-700 mb-1">Discount (%)</label>
                <input type="number" name="discount_percent" id="discount_percent" min="0" max="100" step="0.01" value="{{ old('discount_percent', 0) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('discount_percent')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="total_price" class="block text-sm font-medium text-gray-700 mb-1">Total Price</label>
                <input type="number" name="total_price" id="total_price" step="0.01" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-100" readonly>
                @error('total_price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="advance_received" class="block text-sm font-medium text-gray-700 mb-1">Advance Received</label>
                <input type="number" name="advance_received" id="advance_received" min="0" step="0.01" value="{{ old('advance_received', 0) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('advance_received')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Start Date -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="{{ old('start_date', date('Y-m-d')) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('start_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('sales.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">
                Cancel
            </a>
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg">
                Create Sale
            </button>
        </div>
    </form>
</div>

<!-- Item Template (Hidden) -->
<template id="itemTemplate">
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 p-4 border border-gray-200 rounded-lg item-row">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Product</label>
            <select name="items[INDEX][product_id]" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 product-select" required>
                <option value="">Select Product</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                        {{ $product->name }} - Rs. {{ number_format($product->price, 2) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
            <input type="number" name="items[INDEX][quantity]" min="1" value="1" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 quantity-input" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Unit Price</label>
            <input type="number" name="items[INDEX][unit_price]" step="0.01" min="0" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 price-input" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Total</label>
            <input type="text" class="w-full border border-gray-300 rounded-lg px-3 py-2 bg-gray-100 item-total" readonly>
        </div>

        <div class="flex items-end">
            <button type="button" class="bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded-lg remove-item">
                Remove
            </button>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = 0;
    const itemsContainer = document.getElementById('itemsContainer');
    const addItemButton = document.getElementById('addItem');
    const itemTemplate = document.getElementById('itemTemplate');

    // Add first item by default
    addItem();

    addItemButton.addEventListener('click', addItem);

    function addItem() {
        const template = itemTemplate.content.cloneNode(true);
        
        // Replace INDEX with actual index
        template.innerHTML = template.innerHTML.replace(/INDEX/g, itemIndex);
        
        itemsContainer.appendChild(template);
        
        // Add event listeners to the new item
        const newItem = itemsContainer.lastElementChild;
        setupItemEventListeners(newItem);
        
        itemIndex++;
    }

    function setupItemEventListeners(itemElement) {
        const productSelect = itemElement.querySelector('.product-select');
        const quantityInput = itemElement.querySelector('.quantity-input');
        const priceInput = itemElement.querySelector('.price-input');
        const itemTotal = itemElement.querySelector('.item-total');
        const removeButton = itemElement.querySelector('.remove-item');

        productSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.dataset.price) {
                priceInput.value = selectedOption.dataset.price;
                calculateItemTotal();
            }
        });

        quantityInput.addEventListener('input', calculateItemTotal);
        priceInput.addEventListener('input', calculateItemTotal);

        removeButton.addEventListener('click', function() {
            if (itemsContainer.children.length > 1) {
                itemElement.remove();
                calculateGrandTotal();
            }
        });

        function calculateItemTotal() {
            const quantity = parseFloat(quantityInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            const total = quantity * price;
            itemTotal.value = total.toFixed(2);
            calculateGrandTotal();
        }
    }

    function calculateGrandTotal() {
        const itemTotals = document.querySelectorAll('.item-total');
        let subtotal = 0;
        
        itemTotals.forEach(function(item) {
            subtotal += parseFloat(item.value) || 0;
        });

        document.getElementById('subtotal').value = subtotal.toFixed(2);
        
        const discountPercent = parseFloat(document.getElementById('discount_percent').value) || 0;
        const discountAmount = (subtotal * discountPercent) / 100;
        const totalPrice = subtotal - discountAmount;
        
        document.getElementById('total_price').value = totalPrice.toFixed(2);
    }

    // Recalculate when discount changes
    document.getElementById('discount_percent').addEventListener('input', calculateGrandTotal);
});
</script>
@endsection