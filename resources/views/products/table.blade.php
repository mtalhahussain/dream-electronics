@if(($products ?? collect())->count() > 0)
    <div class="table-responsive">
        <table class="table table-sm table-hover table-striped align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Model</th>
                    <th>Brand</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Branch</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr data-product-id="{{ $product->id }}">
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="bg-light d-flex align-items-center justify-content-center rounded me-2" style="width: 40px; height: 40px;">
                                <i class="bi bi-box text-muted"></i>
                            </div>
                            <div>
                                <strong>{{ $product->name }}</strong>
                                @if($product->description)
                                    <br><small class="text-muted">{{ Str::limit($product->description, 30) }}</small>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>{{ $product->model }}</td>
                    <td>{{ $product->brand }}</td>
                    <td>
                        <span class="badge bg-info">{{ $product->category }}</span>
                    </td>
                    <td>
                        <strong>Rs. {{ number_format($product->price, 2) }}</strong>
                    </td>
                    <td>
                        <span class="badge bg-{{ $product->stock_quantity > 10 ? 'success' : ($product->stock_quantity > 0 ? 'warning' : 'danger') }}">
                            {{ $product->stock_quantity }} units
                        </span>
                    </td>
                    <td>
                        <div class="form-check form-switch">
                            <input class="form-check-input toggle-status" type="checkbox" 
                                   {{ $product->active ? 'checked' : '' }}
                                   data-product-id="{{ $product->id }}">
                            <label class="form-check-label">
                                <span class="badge bg-{{ $product->active ? 'success' : 'secondary' }} status-badge">
                                    {{ $product->active ? 'Active' : 'Inactive' }}
                                </span>
                            </label>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-info text-dark" data-branch-id="{{ $product->branch_id ?? '' }}">
                            {{ $product->branch->name ?? 'No Branch' }}
                        </span>
                    </td>
                    <td>
                        <button type="button" class="btn btn-outline-primary btn-sm me-1 edit-product" 
                                data-product-id="{{ $product->id }}"
                                data-branch-id="{{ $product->branch_id }}"
                                data-name="{{ $product->name }}"
                                data-model="{{ $product->model }}"
                                data-brand="{{ $product->brand }}"
                                data-category="{{ $product->category }}"
                                data-price="{{ $product->price }}"
                                data-stock="{{ $product->stock_quantity }}"
                                data-description="{{ $product->description }}"
                                data-active="{{ $product->active ? '1' : '0' }}">>
                            <i class="bi bi-pencil"></i>
                        </button>
                        @if($product->purchase_invoice_path)
                        <a href="{{ Storage::url($product->purchase_invoice_path) }}" target="_blank" 
                           class="btn btn-outline-info btn-sm" title="View Invoice">
                            <i class="bi bi-file-text"></i>
                        </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="card-footer bg-white border-top">
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted">
                Showing {{ $products->firstItem() ?? 0 }} to {{ $products->lastItem() ?? 0 }} of {{ $products->total() ?? 0 }} results
            </div>
            <div>
                {{ $products->links() }}
            </div>
        </div>
    </div>
@else
    <div class="text-center py-5">
        <i class="bi bi-box-seam fs-1 text-muted mb-3"></i>
        <h5 class="text-muted">No products found</h5>
        <p class="text-muted">Start by adding your first product to the inventory.</p>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
            <i class="bi bi-plus-circle me-2"></i>Add Product
        </button>
    </div>
@endif