<?php

namespace App\Http\Controllers;

use App\Models\FinanceTransaction;
use App\Models\Product;
use App\Models\Branch;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Expense;
use App\Models\StockCredit;
use App\Models\Employee;
use App\Models\SalaryPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:view-finance')->only(['summary', 'index']);
    }

    public function index(Request $request)
    {
        $request->validate([
            'from' => 'nullable|date',
            'to' => 'nullable|date|after_or_equal:from',
            'branch_id' => 'nullable|exists:branches,id',
            'make' => 'nullable|string',
            'section' => 'nullable|in:products,expenses,stock_credit,salary',
        ]);

        $branches = Branch::where('is_active', true)->get();
        
        // Date filter defaults
        $from = $request->from ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $to = $request->to ?? Carbon::now()->format('Y-m-d');
        $branchId = $request->branch_id;
        $make = $request->make;
        $activeSection = $request->section ?? 'products';

        // Products In/Out Section
        $productsIn = $this->getProductsIn($from, $to, $branchId, $make);
        $productsOut = $this->getProductsOut($from, $to, $branchId, $make);
        
        // Office Expenses Section
        $officeExpenses = $this->getOfficeExpenses($from, $to, $branchId);
        
        // Stock Credit Section
        $stockCredits = $this->getStockCredits($from, $to, $make);
        
        // Salary Section
        $salaryExpenses = $this->getSalaryExpenses($from, $to, $branchId);

        // Summary calculations
        $summary = [
            'products_in_total' => $productsIn->sum('total_value'),
            'products_out_total' => $productsOut->sum('total_price'),
            'expenses_total' => $officeExpenses->sum('amount'),
            'stock_credit_total' => $stockCredits->sum('total_cost'),
            'salary_total' => $salaryExpenses->sum('amount'),
            'net_profit' => $productsOut->sum('total_price') - $productsIn->sum('total_value') - $officeExpenses->sum('amount') - $salaryExpenses->sum('amount'),
        ];

        return view('finance.index', compact(
            'branches',
            'productsIn',
            'productsOut', 
            'officeExpenses',
            'stockCredits',
            'salaryExpenses',
            'summary',
            'from',
            'to',
            'branchId',
            'make',
            'activeSection'
        ));
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

    private function getProductsIn($from, $to, $branchId = null, $make = null)
    {
        $query = StockCredit::with('product')
            ->whereBetween('purchase_date', [$from, $to]);

        if ($make) {
            $query->whereHas('product', function ($q) use ($make) {
                $q->where('model', 'LIKE', "%{$make}%")
                  ->orWhere('brand', 'LIKE', "%{$make}%");
            });
        }

        return $query->orderBy('purchase_date', 'desc')->get();
    }

    private function getProductsOut($from, $to, $branchId = null, $make = null)
    {
        $query = SaleItem::with(['product', 'sale.branch', 'sale.customer'])
            ->whereHas('sale', function ($q) use ($from, $to, $branchId) {
                $q->whereBetween('sale_date', [$from, $to]);
                if ($branchId) {
                    $q->where('branch_id', $branchId);
                }
            });

        if ($make) {
            $query->whereHas('product', function ($q) use ($make) {
                $q->where('model', 'LIKE', "%{$make}%")
                  ->orWhere('brand', 'LIKE', "%{$make}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    private function getOfficeExpenses($from, $to, $branchId = null)
    {
        $query = Expense::with('branch')
            ->whereBetween('expense_date', [$from, $to]);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query->orderBy('expense_date', 'desc')->get();
    }

    private function getStockCredits($from, $to, $make = null)
    {
        $query = StockCredit::with('product')
            ->whereBetween('purchase_date', [$from, $to])
            ->where('total_cost', 0); // Items received without payment

        if ($make) {
            $query->whereHas('product', function ($q) use ($make) {
                $q->where('model', 'LIKE', "%{$make}%")
                  ->orWhere('brand', 'LIKE', "%{$make}%");
            });
        }

        return $query->orderBy('purchase_date', 'desc')->get();
    }

    private function getSalaryExpenses($from, $to, $branchId = null)
    {
        $query = SalaryPayment::with(['employee.branch'])
            ->whereBetween('payment_date', [$from, $to]);

        if ($branchId) {
            $query->whereHas('employee', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        return $query->orderBy('payment_date', 'desc')->get();
    }
}