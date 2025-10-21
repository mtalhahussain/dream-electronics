<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view-products')->only(['index', 'show']);
        $this->middleware('can:create-products')->only(['create', 'store']);
        $this->middleware('can:edit-products')->only(['edit', 'update']);
        $this->middleware('can:delete-products')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Product::with(['branch']);

        // Only load category relationship for products that have category_id
        $query->with(['category' => function($q) {
            $q->select('id', 'name', 'color', 'icon');
        }]);

        // Apply filters
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('active', false);
            }
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('purchased_from', 'like', "%{$search}%");
            });
        }

        // Legacy search field support
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        // Legacy category filter support (for old static categories)
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('active')) {
            $query->where('active', $request->boolean('active'));
        }

        $products = $query->orderBy('name')->paginate(15)->withQueryString();
        $branches = Branch::orderBy('name')->get(['id', 'name']);

        // For AJAX requests, return JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $products->items(),
                'permissions' => [
                    'can_delete' => auth()->user()->can('delete-products')
                ],
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'last_page' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                    'has_more_pages' => $products->hasMorePages(),
                    'next_page_url' => $products->nextPageUrl(),
                    'prev_page_url' => $products->previousPageUrl()
                ]
            ]);
        }

        return view('products.index', compact('products', 'branches'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(StoreProductRequest $request)
    {
        try {
            \DB::beginTransaction();
            
            $data = $request->validated();

            if ($request->hasFile('purchase_invoice')) {
                $data['purchase_invoice'] = $request->file('purchase_invoice')
                    ->store('product-invoices', 'public');
            }

            $product = Product::create($data);

            // Create StockCredit record if initial stock quantity is provided
            if ($product->stock_quantity > 0) {
                $unitCost = $product->purchase_cost ?? 0;
                $totalCost = $product->stock_quantity * $unitCost;
                
                \App\Models\StockCredit::create([
                    'product_id' => $product->id,
                    'quantity' => $product->stock_quantity,
                    'unit_cost' => $unitCost,
                    'total_cost' => $totalCost,
                    'supplier' => $product->purchased_from ?? 'Initial Stock',
                    'invoice_number' => null,
                    'purchase_date' => now()->toDateString()
                ]);

                // Create finance transaction if there's a cost
                if ($totalCost > 0) {
                    \App\Models\FinanceTransaction::create([
                        'branch_id' => $product->branch_id,
                        'type' => 'out',
                        'category' => 'Inventory Purchase',
                        'amount' => $totalCost,
                        'description' => "Initial stock for {$product->name} ({$product->model})",
                        'transaction_date' => now()->toDateString(),
                        'reference_id' => $product->id,
                        'reference_type' => 'product',
                    ]);
                }
            }

            \DB::commit();

            // For AJAX requests, return JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'ok' => true,
                    'message' => 'Product saved',
                    'product' => $product->load('branch')
                ]);
            }

            return redirect()->route('products.index')
                ->with('success', 'Product created successfully');

        } catch (\Exception $e) {
            \DB::rollBack();
            
            // For AJAX requests, return JSON error
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Failed to create product: ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withErrors(['error' => 'Failed to create product: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        try {
            $data = $request->validated();

            // Handle file upload
            if ($request->hasFile('purchase_invoice')) {
                // Delete old file if exists
                if ($product->purchase_invoice) {
                    Storage::disk('public')->delete($product->purchase_invoice);
                }
                
                $data['purchase_invoice'] = $request->file('purchase_invoice')
                    ->store('product-invoices', 'public');
            }

            $product->update($data);

            // For AJAX requests, return JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'ok' => true,
                    'message' => 'Product saved',
                    'product' => $product->fresh()->load('branch')
                ]);
            }

            return redirect()->route('products.index')
                ->with('success', 'Product updated successfully');

        } catch (\Exception $e) {
            // For AJAX requests, return JSON error
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Failed to update product: ' . $e->getMessage()
                ], 422);
            }

            return redirect()->back()
                ->withErrors(['error' => 'Failed to update product: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(Request $request, Product $product)
    {
        try {
            // Check if product has sales
            if ($product->saleItems()->exists()) {
                $message = 'Cannot delete product with existing sales';
                
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message
                    ], 422);
                }
                
                return redirect()->back()->withErrors(['error' => $message]);
            }

            // Delete file if exists
            if ($product->purchase_invoice) {
                Storage::disk('public')->delete($product->purchase_invoice);
            }

            $productName = $product->name;
            $product->delete();

            $message = "Product '{$productName}' deleted successfully";
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message
                ]);
            }

            return redirect()->route('products.index')->with('success', $message);

        } catch (\Exception $e) {
            $message = 'Failed to delete product: ' . $e->getMessage();
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 500);
            }
            
            return redirect()->back()->withErrors(['error' => $message]);
        }
    }

    public function toggle(Product $product)
    {
        try {
            $product->update(['active' => !$product->active]);
            
            $status = $product->active ? 'activated' : 'deactivated';
            
            return response()->json([
                'success' => true,
                'message' => "Product {$status} successfully!",
                'active' => $product->active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product status: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkStock(Product $product)
    {
        try {
            return response()->json([
                'success' => true,
                'data' => [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'stock_quantity' => $product->stock_quantity ?? 0,
                    'price' => $product->price,
                    'has_stock' => ($product->stock_quantity ?? 0) > 0
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check stock: ' . $e->getMessage()
            ], 500);
        }
    }
}