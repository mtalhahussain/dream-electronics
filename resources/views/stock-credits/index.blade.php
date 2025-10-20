@extends('layouts.admin')

@section('title', 'Stock Credit Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-box me-2"></i>Stock Credit Management</h1>
    <button type="button" class="btn btn-primary" onclick="openCreateModal()">
        <i class="bi bi-plus-circle me-2"></i>Add Stock Credit
    </button>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('stock-credits.index') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="product_id" class="form-label">Product</label>
                    <select class="form-select" id="product_id" name="product_id">
                        <option value="">All Products</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="supplier" class="form-label">Supplier</label>
                    <input type="text" class="form-control" id="supplier" name="supplier" value="{{ request('supplier') }}" placeholder="Supplier name">
                </div>
                <div class="col-md-2">
                    <label for="from" class="form-label">From Date</label>
                    <input type="date" class="form-control" id="from" name="from" value="{{ request('from') }}">
                </div>
                <div class="col-md-2">
                    <label for="to" class="form-label">To Date</label>
                    <input type="date" class="form-control" id="to" name="to" value="{{ request('to') }}">
                </div>
                <div class="col-md-3">
                    <label for="submit" class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary d-block w-100">
                        <i class="bi bi-search me-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Stock Credits Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Supplier</th>
                        <th>Quantity</th>
                        <th>Unit Cost</th>
                        <th>Total Cost</th>
                        <th>Invoice #</th>
                        <th>Actions</th>
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
                        <td><span class="badge bg-info">{{ $credit->quantity }}</span></td>
                        <td>Rs. {{ number_format($credit->unit_cost, 0) }}</td>
                        <td><strong>Rs. {{ number_format($credit->total_cost, 0) }}</strong></td>
                        <td>{{ $credit->invoice_number ?? 'N/A' }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="editStockCredit({{ $credit->id }})">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteStockCredit({{ $credit->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No stock credits found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($stockCredits->hasPages())
    <div class="card-footer">
        {{ $stockCredits->links() }}
    </div>
    @endif
</div>

<!-- Stock Credit Modal -->
<div class="modal fade" id="stockCreditModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="stockCreditModalLabel">Add Stock Credit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="stockCreditForm">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="stock_credit_id" name="stock_credit_id">
                    <input type="hidden" id="form_method" name="_method" value="">
                    
                    <div class="mb-3">
                        <label for="product_id_form" class="form-label">Product <span class="text-danger">*</span></label>
                        <select class="form-select" id="product_id_form" name="product_id" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }} - {{ $product->model ?? 'N/A' }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="unit_cost" class="form-label">Unit Cost <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rs.</span>
                                    <input type="number" class="form-control" id="unit_cost" name="unit_cost" step="0.01" min="0" required>
                                </div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="supplier" class="form-label">Supplier <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="supplier" name="supplier" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label for="invoice_number" class="form-label">Invoice Number</label>
                        <input type="text" class="form-control" id="invoice_number" name="invoice_number">
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label for="purchase_date" class="form-label">Purchase Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="purchase_date" name="purchase_date" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Total Cost</label>
                        <div class="input-group">
                            <span class="input-group-text">Rs.</span>
                            <input type="text" class="form-control" id="total_cost_display" readonly>
                        </div>
                        <div class="form-text">Calculated automatically (Quantity × Unit Cost)</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>Save Stock Credit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openCreateModal() {
        document.getElementById('stockCreditModalLabel').textContent = 'Add Stock Credit';
        document.getElementById('stockCreditForm').action = '{{ route('stock-credits.store') }}';
        document.getElementById('form_method').value = '';
        document.getElementById('stock_credit_id').value = '';
        document.getElementById('stockCreditForm').reset();
        clearValidationErrors();
        
        const modal = new bootstrap.Modal(document.getElementById('stockCreditModal'));
        modal.show();
    }

    function editStockCredit(creditId) {
        fetch(`/stock-credits/${creditId}/get`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const credit = data.stockCredit;
                
                document.getElementById('stockCreditModalLabel').textContent = 'Edit Stock Credit';
                document.getElementById('stockCreditForm').action = `/stock-credits/${creditId}`;
                document.getElementById('form_method').value = 'PUT';
                document.getElementById('stock_credit_id').value = creditId;
                
                document.getElementById('product_id_form').value = credit.product_id || '';
                document.getElementById('quantity').value = credit.quantity || '';
                document.getElementById('unit_cost').value = credit.unit_cost || '';
                document.getElementById('supplier').value = credit.supplier || '';
                document.getElementById('invoice_number').value = credit.invoice_number || '';
                document.getElementById('purchase_date').value = credit.purchase_date || '';
                
                calculateTotalCost();
                
                const modal = new bootstrap.Modal(document.getElementById('stockCreditModal'));
                modal.show();
            }
        });
    }

    function deleteStockCredit(creditId) {
        if (confirm('Are you sure you want to delete this stock credit? This will also reduce the product quantity.')) {
            fetch(`/stock-credits/${creditId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                }
            });
        }
    }

    function calculateTotalCost() {
        const quantity = parseFloat(document.getElementById('quantity').value) || 0;
        const unitCost = parseFloat(document.getElementById('unit_cost').value) || 0;
        const totalCost = quantity * unitCost;
        
        document.getElementById('total_cost_display').value = totalCost.toLocaleString();
    }

    // Auto-calculate total cost
    document.getElementById('quantity').addEventListener('input', calculateTotalCost);
    document.getElementById('unit_cost').addEventListener('input', calculateTotalCost);

    function clearValidationErrors() {
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        document.querySelectorAll('.invalid-feedback').forEach(el => {
            el.textContent = '';
        });
    }

    function displayValidationErrors(errors) {
        Object.keys(errors).forEach(field => {
            const input = document.querySelector(`[name="${field}"]`);
            const feedback = input?.parentElement.querySelector('.invalid-feedback');
            
            if (input && feedback) {
                input.classList.add('is-invalid');
                feedback.textContent = errors[field][0];
            }
        });
    }

    document.getElementById('stockCreditForm').addEventListener('submit', function(e) {
        e.preventDefault();
        clearValidationErrors();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        const isEdit = document.getElementById('stock_credit_id').value !== '';
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>' + (isEdit ? 'Updating...' : 'Saving...');
        
        if (isEdit) {
            formData.append('_method', 'PUT');
        }
        
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
                showToast(data.message, 'success');
                bootstrap.Modal.getInstance(document.getElementById('stockCreditModal')).hide();
                setTimeout(() => location.reload(), 1000);
            } else {
                if (data.errors) {
                    displayValidationErrors(data.errors);
                }
                showToast(data.message || 'Failed to save stock credit', 'error');
            }
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });

    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }
</script>
@endpush