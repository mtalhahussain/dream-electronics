@if(($products ?? collect())->count() > 0)
    <div class="table-responsive">
        <table class="table table-sm table-hover table-striped align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Model</th>
                    <th>SKU</th>
                    <th>Serial No.</th>
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
                    <td>
                        @if($product->sku)
                            <span class="badge bg-secondary">{{ $product->sku }}</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($product->serial_number)
                            <code class="text-primary">{{ $product->serial_number }}</code>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @php $categoryDisplay = $product->category_display; @endphp
                        @if($categoryDisplay['badge_class'] === 'custom')
                            <span class="badge" style="background-color: {{ $categoryDisplay['color'] }}; color: white;">
                                @if($categoryDisplay['icon'])
                                    <i class="bi {{ $categoryDisplay['icon'] }} me-1"></i>
                                @endif
                                {{ $categoryDisplay['name'] }}
                            </span>
                        @else
                            <span class="badge {{ $categoryDisplay['badge_class'] }}">{{ $categoryDisplay['name'] }}</span>
                        @endif
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
                                data-branch-id="{{ $product->branch_id ?? '' }}"
                                data-category-id="{{ $product->category_id ?? '' }}"
                                data-name="{{ $product->name }}"
                                data-model="{{ $product->model }}"
                                data-brand="{{ $product->brand }}"
                                data-price="{{ $product->price }}"
                                data-purchase-cost="{{ $product->purchase_cost ?? '' }}"
                                data-purchased-from="{{ $product->purchased_from ?? '' }}"
                                data-sku="{{ $product->sku ?? '' }}"
                                data-serial-number="{{ $product->serial_number ?? '' }}"
                                data-stock="{{ $product->stock_quantity }}"
                                data-description="{{ $product->description ?? '' }}"
                                data-active="{{ $product->active ? '1' : '0' }}">
                            <i class="bi bi-pencil"></i>
                        </button>
                        @if($product->purchase_invoice)
                        <a href="{{ Storage::url($product->purchase_invoice) }}" target="_blank" 
                           class="btn btn-outline-info btn-sm me-1" title="View Invoice">
                            <i class="bi bi-file-text"></i>
                        </a>
                        @endif
                        @can('delete-products')
                        <button type="button" class="btn btn-outline-danger btn-sm delete-product" 
                                data-product-id="{{ $product->id }}"
                                data-product-name="{{ $product->name }}">
                            <i class="bi bi-trash"></i>
                        </button>
                        @endcan
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