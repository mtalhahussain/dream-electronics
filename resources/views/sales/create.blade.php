@extends('layouts.admin')

@section('title', 'Create Sale')

@push('styles')
<style>
    .sale-summary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .item-card {
        border: 2px solid #e5e7eb;
        transition: all 0.3s ease;
    }
    
    .item-card:hover {
        border-color: #3b82f6;
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.1);
    }
    
    .remove-item:hover {
        transform: scale(1.05);
    }
    
    .calculation-card {
        background: linear-gradient(45deg, #f8fafc, #e2e8f0);
        border: 1px solid #cbd5e1;
    }
    
    .btn-add-item {
        background: linear-gradient(45deg, #10b981, #059669);
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-add-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);
        background: linear-gradient(45deg, #059669, #047857);
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Header -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="card-title mb-0">
                        <i class="bi bi-cart-plus me-2 text-primary"></i>Create New Sale
                    </h4>
                    <p class="text-muted mb-0 mt-1">Create a new sale with installment plan</p>
                </div>
                <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Sales
                </a>
            </div>
        </div>

        <form id="saleForm" action="{{ route('sales.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <!-- Main Sale Form -->
                <div class="col-lg-8">
                    <!-- Customer & Branch Selection -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-person-check me-2"></i>Sale Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <select name="branch_id" id="branch_id" class="form-select @error('branch_id') is-invalid @enderror" required>
                                            <option value="">Select Branch</option>
                                            @foreach($branches as $branch)
                                                <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}
                                                    data-location="{{ $branch->location }}" data-manager="{{ $branch->manager_name }}">
                                                    {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <label for="branch_id">Branch <span class="text-danger">*</span></label>
                                        @error('branch_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div id="branchInfo" class="small text-muted mt-1"></div>
                                </div>

                                <div class="col-md-5">
                                    <div class="form-floating">
                                        <select name="customer_id" id="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                                            <option value="">Select Customer</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}
                                                    data-phone="{{ $customer->phone }}" data-cnic="{{ $customer->cnic }}" 
                                                    data-branch="{{ $customer->branch?->name }}">
                                                    {{ $customer->name }} - {{ $customer->phone }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <label for="customer_id">Customer <span class="text-danger">*</span></label>
                                        @error('customer_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div id="customerInfo" class="small text-muted mt-1"></div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-floating">
                                        <select name="duration_months" id="duration_months" class="form-select @error('duration_months') is-invalid @enderror" required>
                                            <option value="">Select Duration</option>
                                            <option value="6" {{ old('duration_months') == '6' ? 'selected' : '' }}>6 Months</option>
                                            <option value="10" {{ old('duration_months') == '10' ? 'selected' : '' }}>10 Months</option>
                                            <option value="12" {{ old('duration_months') == '12' ? 'selected' : '' }}>12 Months</option>
                                        </select>
                                        <label for="duration_months">Duration <span class="text-danger">*</span></label>
                                        @error('duration_months')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sale Items -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-basket me-2"></i>Sale Items
                            </h5>
                            <button type="button" id="addItem" class="btn btn-success btn-add-item">
                                <i class="bi bi-plus-circle me-2"></i>Add Item
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="itemsContainer" class="space-y-3">
                                <!-- Items will be added here dynamically -->
                            </div>
                            @error('items')
                                <div class="alert alert-danger mt-3">
                                    <i class="bi bi-exclamation-triangle me-2"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-calendar me-2"></i>Payment Schedule
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="start_date" class="form-label">Installment Start Date</label>
                                    <input type="date" name="start_date" id="start_date" 
                                        value="{{ old('start_date', date('Y-m-d', strtotime('+1 month'))) }}" 
                                        class="form-control @error('start_date') is-invalid @enderror">
                                    <div class="form-text">First installment due date</div>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Monthly Installment</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rs.</span>
                                        <input type="text" id="monthlyInstallment" class="form-control bg-light" readonly>
                                    </div>
                                    <div class="form-text">Calculated automatically</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sale Summary Sidebar -->
                <div class="col-lg-4">
                    <div class="sticky-top" style="top: 20px;">
                        <!-- Calculation Summary -->
                        <div class="card border-0 shadow-sm mb-4 calculation-card">
                            <div class="card-header sale-summary text-center">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-calculator me-2"></i>Sale Summary
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <label for="subtotal" class="form-label small">Subtotal</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rs.</span>
                                            <input type="text" id="subtotal" class="form-control bg-light" readonly>
                                        </div>
                                    </div>
                                    
                                    <div class="col-6">
                                        <label for="discount_percent" class="form-label small">Discount %</label>
                                        <input type="number" name="discount_percent" id="discount_percent" 
                                            min="0" max="100" step="0.01" value="{{ old('discount_percent', 0) }}" 
                                            class="form-control form-control-sm @error('discount_percent') is-invalid @enderror">
                                        @error('discount_percent')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-6">
                                        <label for="discountAmount" class="form-label small">Discount Amount</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rs.</span>
                                            <input type="text" id="discountAmount" class="form-control bg-light" readonly>
                                        </div>
                                    </div>
                                    
                                    <div class="col-6">
                                        <label for="total_price" class="form-label small">Total Price</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rs.</span>
                                            <input type="number" name="total_price" id="total_price" step="0.01" 
                                                class="form-control bg-light @error('total_price') is-invalid @enderror" readonly>
                                        </div>
                                        @error('total_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-12">
                                        <hr>
                                        <label for="advance_received" class="form-label small">Advance Payment</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rs.</span>
                                            <input type="number" name="advance_received" id="advance_received" 
                                                min="0" step="0.01" value="{{ old('advance_received', 0) }}" 
                                                class="form-control @error('advance_received') is-invalid @enderror">
                                        </div>
                                        @error('advance_received')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-6">
                                        <label class="form-label small">Remaining Balance</label>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">Rs.</span>
                                            <input type="text" id="remainingBalance" class="form-control bg-warning bg-opacity-25" readonly>
                                        </div>
                                    </div>
                                    
                                    <div class="col-6">
                                        <label class="form-label small">Status</label>
                                        <div id="paymentStatus" class="form-control-sm text-center">
                                            <span class="badge bg-warning">Pending</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <button type="submit" id="submitBtn" class="btn btn-primary btn-lg">
                                        <i class="bi bi-check-circle me-2"></i>Create Sale
                                    </button>
                                    <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-2"></i>Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Item Template (Hidden) -->
<template id="itemTemplate">
    <div class="card item-card mb-3">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <div class="form-floating">
                        <select name="items[INDEX][product_id]" class="form-select product-select" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->price }}" 
                                    data-stock="{{ $product->stock_quantity ?? 0 }}">
                                    {{ $product->name }} - Rs. {{ number_format($product->price, 2) }}
                                    @if(isset($product->stock_quantity))
                                        (Stock: {{ $product->stock_quantity }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <label>Product <span class="text-danger">*</span></label>
                    </div>
                    <div class="product-info small text-muted mt-1"></div>
                </div>

                <div class="col-md-2">
                    <div class="form-floating">
                        <input type="number" name="items[INDEX][quantity]" min="1" value="1" 
                            class="form-control quantity-input" required>
                        <label>Quantity <span class="text-danger">*</span></label>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-floating">
                        <input type="number" name="items[INDEX][unit_price]" step="0.01" min="0" 
                            class="form-control price-input" required>
                        <label>Unit Price <span class="text-danger">*</span></label>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-floating">
                        <input type="text" class="form-control item-total bg-light" readonly>
                        <label>Total</label>
                    </div>
                </div>

                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-item w-100">
                        <i class="bi bi-trash me-1"></i>Remove
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = 0;
    const itemsContainer = document.getElementById('itemsContainer');
    const addItemButton = document.getElementById('addItem');
    const itemTemplate = document.getElementById('itemTemplate');

    // Add first item by default
    addItem();

    addItemButton.addEventListener('click', addItem);

    // Branch info display
    document.getElementById('branch_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const branchInfo = document.getElementById('branchInfo');
        
        if (selectedOption.value) {
            branchInfo.innerHTML = `
                <i class="bi bi-geo-alt me-1"></i>${selectedOption.dataset.location || 'N/A'} | 
                <i class="bi bi-person me-1"></i>Manager: ${selectedOption.dataset.manager || 'N/A'}
            `;
        } else {
            branchInfo.innerHTML = '';
        }
    });

    // Customer info display
    document.getElementById('customer_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const customerInfo = document.getElementById('customerInfo');
        
        if (selectedOption.value) {
            customerInfo.innerHTML = `
                <i class="bi bi-telephone me-1"></i>${selectedOption.dataset.phone || 'N/A'} | 
                <i class="bi bi-credit-card me-1"></i>${selectedOption.dataset.cnic || 'N/A'}
                ${selectedOption.dataset.branch ? ` | <i class="bi bi-building me-1"></i>${selectedOption.dataset.branch}` : ''}
            `;
        } else {
            customerInfo.innerHTML = '';
        }
    });

    function addItem() {
        const template = itemTemplate.content.cloneNode(true);
        
        // Replace INDEX with actual index
        const tempDiv = document.createElement('div');
        tempDiv.appendChild(template);
        tempDiv.innerHTML = tempDiv.innerHTML.replace(/INDEX/g, itemIndex);
        
        itemsContainer.appendChild(tempDiv.firstElementChild);
        
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
        const productInfo = itemElement.querySelector('.product-info');

        productSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.dataset.price) {
                priceInput.value = selectedOption.dataset.price;
                const stock = selectedOption.dataset.stock || 0;
                productInfo.innerHTML = stock ? `<i class="bi bi-box me-1"></i>Available Stock: ${stock}` : '';
                
                // Update quantity input max attribute and validate current quantity
                quantityInput.setAttribute('max', stock);
                if (parseInt(quantityInput.value) > parseInt(stock)) {
                    quantityInput.value = stock;
                    showToast(`Quantity adjusted to available stock (${stock})`, 'warning');
                }
                
                validateQuantity();
                calculateItemTotal();
            } else {
                priceInput.value = '';
                productInfo.innerHTML = '';
                quantityInput.removeAttribute('max');
            }
        });

        quantityInput.addEventListener('input', function() {
            validateQuantity();
            calculateItemTotal();
        });
        
        priceInput.addEventListener('input', calculateItemTotal);

        function validateQuantity() {
            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const availableStock = parseInt(selectedOption.dataset.stock) || 0;
            const requestedQuantity = parseInt(quantityInput.value) || 0;
            
            // Remove any existing validation classes
            quantityInput.classList.remove('is-invalid', 'is-valid');
            
            if (selectedOption.value && availableStock > 0) {
                if (requestedQuantity > availableStock) {
                    quantityInput.classList.add('is-invalid');
                    productInfo.innerHTML = `<i class="bi bi-exclamation-triangle text-danger me-1"></i>Requested quantity (${requestedQuantity}) exceeds available stock (${availableStock})`;
                } else if (requestedQuantity > 0) {
                    quantityInput.classList.add('is-valid');
                    productInfo.innerHTML = `<i class="bi bi-box text-success me-1"></i>Available Stock: ${availableStock}`;
                }
            }
        }

        removeButton.addEventListener('click', function() {
            if (itemsContainer.children.length > 1) {
                itemElement.remove();
                calculateGrandTotal();
            } else {
                showToast('At least one item is required', 'error');
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

        const discountPercent = parseFloat(document.getElementById('discount_percent').value) || 0;
        const discountAmount = (subtotal * discountPercent) / 100;
        const totalPrice = subtotal - discountAmount;
        const advanceReceived = parseFloat(document.getElementById('advance_received').value) || 0;
        const remainingBalance = Math.max(0, totalPrice - advanceReceived);
        const durationMonths = parseInt(document.getElementById('duration_months').value) || 1;
        const monthlyInstallment = remainingBalance / durationMonths;

        // Update display
        document.getElementById('subtotal').value = subtotal.toFixed(2);
        document.getElementById('discountAmount').value = discountAmount.toFixed(2);
        document.getElementById('total_price').value = totalPrice.toFixed(2);
        document.getElementById('remainingBalance').value = remainingBalance.toFixed(2);
        document.getElementById('monthlyInstallment').value = monthlyInstallment.toFixed(2);

        // Update payment status
        const statusElement = document.getElementById('paymentStatus');
        if (remainingBalance <= 0) {
            statusElement.innerHTML = '<span class="badge bg-success">Paid Full</span>';
        } else if (advanceReceived > 0) {
            statusElement.innerHTML = '<span class="badge bg-warning">Partial Payment</span>';
        } else {
            statusElement.innerHTML = '<span class="badge bg-danger">No Payment</span>';
        }
    }

    // Recalculate when discount or advance changes
    document.getElementById('discount_percent').addEventListener('input', calculateGrandTotal);
    document.getElementById('advance_received').addEventListener('input', calculateGrandTotal);
    document.getElementById('duration_months').addEventListener('change', calculateGrandTotal);

    // Form submission
    document.getElementById('saleForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate stock before submission
        const invalidQuantities = document.querySelectorAll('.quantity-input.is-invalid');
        if (invalidQuantities.length > 0) {
            showToast('Please correct the invalid quantities before submitting', 'error');
            return;
        }
        
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating Sale...';
        
        const formData = new FormData(this);
        
        fetch(this.action, {
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
                showToast('Sale created successfully!', 'success');
                setTimeout(() => {
                    window.location.href = '{{ route("sales.index") }}';
                }, 1500);
            } else {
                // Handle stock validation errors from server
                if (data.errors && Array.isArray(data.errors)) {
                    data.errors.forEach(error => {
                        showToast(error, 'error');
                    });
                } else {
                    showToast(data.message || 'Failed to create sale', 'error');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred while creating the sale', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
});
</script>
@endpush