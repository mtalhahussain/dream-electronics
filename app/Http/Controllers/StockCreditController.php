<?php

namespace App\Http\Controllers;

use App\Models\StockCredit;
use App\Models\Product;
use Illuminate\Http\Request;

class StockCreditController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = StockCredit::with('product');

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('supplier')) {
            $query->where('supplier', 'LIKE', "%{$request->supplier}%");
        }

        if ($request->filled('from')) {
            $query->where('purchase_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->where('purchase_date', '<=', $request->to);
        }

        $stockCredits = $query->orderBy('purchase_date', 'desc')->paginate(20);
        $products = Product::where('active', true)->get();

        return view('stock-credits.index', compact('stockCredits', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'required|numeric|min:0',
            'supplier' => 'required|string|max:255',
            'invoice_number' => 'nullable|string|max:255',
            'purchase_date' => 'required|date'
        ]);

        $totalCost = $request->quantity * $request->unit_cost;

        StockCredit::create([
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'unit_cost' => $request->unit_cost,
            'total_cost' => $totalCost,
            'supplier' => $request->supplier,
            'invoice_number' => $request->invoice_number,
            'purchase_date' => $request->purchase_date
        ]);

        // Update product quantity
        $product = Product::find($request->product_id);
        $product->increment('stock_quantity', $request->quantity);

        return response()->json([
            'success' => true,
            'message' => 'Stock credit added successfully!'
        ]);
    }

    public function update(Request $request, StockCredit $stockCredit)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'required|numeric|min:0',
            'supplier' => 'required|string|max:255',
            'invoice_number' => 'nullable|string|max:255',
            'purchase_date' => 'required|date'
        ]);

        $oldQuantity = $stockCredit->quantity;
        $newQuantity = $request->quantity;
        $totalCost = $newQuantity * $request->unit_cost;

        // Update product quantity
        $product = Product::find($request->product_id);
        $quantityDiff = $newQuantity - $oldQuantity;
        
        if ($quantityDiff > 0) {
            $product->increment('stock_quantity', $quantityDiff);
        } else if ($quantityDiff < 0) {
            $product->decrement('stock_quantity', abs($quantityDiff));
        }

        $stockCredit->update([
            'product_id' => $request->product_id,
            'quantity' => $newQuantity,
            'unit_cost' => $request->unit_cost,
            'total_cost' => $totalCost,
            'supplier' => $request->supplier,
            'invoice_number' => $request->invoice_number,
            'purchase_date' => $request->purchase_date
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Stock credit updated successfully!'
        ]);
    }

    public function destroy(StockCredit $stockCredit)
    {
        // Decrease product quantity
        $product = Product::find($stockCredit->product_id);
        $product->decrement('stock_quantity', $stockCredit->quantity);

        $stockCredit->delete();

        return response()->json([
            'success' => true,
            'message' => 'Stock credit deleted successfully!'
        ]);
    }

    public function getStockCredit(StockCredit $stockCredit)
    {
        return response()->json([
            'success' => true,
            'stockCredit' => $stockCredit->load('product')
        ]);
    }
}