<?php

namespace App\Http\Controllers;

use App\Models\FinanceTransaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view-finance')->only(['summary', 'index']);
    }

    public function index()
    {
        $transactions = FinanceTransaction::with('branch')
            ->orderBy('transaction_date', 'desc')
            ->paginate(20);

        return view('finance.index', compact('transactions'));
    }

    public function summary(Request $request)
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'branch_id' => 'nullable|exists:branches,id',
            'make' => 'nullable|string',
        ]);

        $query = FinanceTransaction::query();

        // Apply filters
        if ($request->from) {
            $query->where('transaction_date', '>=', $request->from);
        }

        if ($request->to) {
            $query->where('transaction_date', '<=', $request->to);
        }

        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        // Filter by product make if provided
        if ($request->make) {
            $productIds = Product::where('model', 'LIKE', "%{$request->make}%")
                ->orWhere('brand', 'LIKE', "%{$request->make}%")
                ->pluck('id');

            $saleIds = DB::table('sale_items')
                ->whereIn('product_id', $productIds)
                ->pluck('sale_id');

            $query->where(function ($q) use ($saleIds) {
                $q->where(function ($subQ) use ($saleIds) {
                    $subQ->where('reference_type', 'sale')
                        ->whereIn('reference_id', $saleIds);
                })
                ->orWhere(function ($subQ) use ($saleIds) {
                    $paymentIds = DB::table('payments')
                        ->join('installments', 'payments.installment_id', '=', 'installments.id')
                        ->whereIn('installments.sale_id', $saleIds)
                        ->pluck('payments.id');
                    
                    $subQ->where('reference_type', 'payment')
                        ->whereIn('reference_id', $paymentIds);
                });
            });
        }

        // Calculate totals
        $inTotal = (clone $query)->where('type', 'in')->sum('amount');
        $outTotal = (clone $query)->where('type', 'out')->sum('amount');
        $netTotal = $inTotal - $outTotal;

        // Get category-wise breakdown
        $inBreakdown = (clone $query)->where('type', 'in')
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();

        $outBreakdown = (clone $query)->where('type', 'out')
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();

        $summary = [
            'in_total' => $inTotal,
            'out_total' => $outTotal,
            'net_total' => $netTotal,
            'in_breakdown' => $inBreakdown,
            'out_breakdown' => $outBreakdown,
            'filters' => [
                'from' => $request->from,
                'to' => $request->to,
                'branch_id' => $request->branch_id,
                'make' => $request->make,
            ]
        ];

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => $summary
            ]);
        }

        return view('finance.summary', compact('summary'));
    }
}