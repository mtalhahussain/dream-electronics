<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\PayInstallmentRequest;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Installment;
use App\Models\Payment;
use App\Models\FinanceTransaction;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SaleController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:create-sales')->only(['create', 'store']);
        $this->middleware('can:view-sales')->only(['index', 'show', 'installments', 'print']);
        $this->middleware('can:pay-installments')->only(['payInstallment']);
    }

    public function index(Request $request)
    {
        $query = Sale::with(['customer', 'branch', 'saleItems.product'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('duration_months')) {
            $query->where('duration_months', $request->duration_months);
        }

        if ($request->filled('sale_date')) {
            $query->whereDate('sale_date', $request->sale_date);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                // Search by sale ID
                if (is_numeric($searchTerm)) {
                    $q->where('id', $searchTerm);
                }
                
                // Search by customer name or phone
                $q->orWhereHas('customer', function ($customerQuery) use ($searchTerm) {
                    $customerQuery->where('name', 'like', '%' . $searchTerm . '%')
                                  ->orWhere('phone', 'like', '%' . $searchTerm . '%')
                                  ->orWhere('cnic', 'like', '%' . $searchTerm . '%');
                });
            });
        }

        $sales = $query->paginate(15)->appends($request->query());
        $branches = \App\Models\Branch::where('is_active', true)->orderBy('name')->get();

        return view('sales.index', compact('sales', 'branches'));
    }

    public function create()
    {
        $branches = \App\Models\Branch::where('is_active', true)->get();
        $customers = \App\Models\Customer::orderBy('name')->where('is_active', true)->get();
        $products = \App\Models\Product::orderBy('name')->where('active', true)->get();
        
        return view('sales.create', compact('branches', 'customers', 'products'));
    }

    public function store(StoreSaleRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();

            // Calculate net total
            $totalPrice = $request->total_price;
            $discountPercent = $request->discount_percent ?? 0;
            $discountAmount = ($totalPrice * $discountPercent) / 100;
            $netTotal = $totalPrice - $discountAmount;
            $advanceReceived = $request->advance_received ?? 0;
            $remainingBalance = $netTotal - $advanceReceived;
            $monthlyInstallment = $remainingBalance / $request->duration_months;

            // Create sale
            $sale = Sale::create([
                'branch_id' => $request->branch_id,
                'customer_id' => $request->customer_id,
                'total_price' => $totalPrice,
                'discount_percent' => $discountPercent,
                'net_total' => $netTotal,
                'advance_received' => $advanceReceived,
                'remaining_balance' => $remainingBalance,
                'duration_months' => $request->duration_months,
                'monthly_installment' => $monthlyInstallment,
                'status' => $remainingBalance > 0 ? 'pending' : 'completed',
                'sale_date' => now()->toDateString(),
            ]);

            // Create sale items and update stock
            foreach ($request->items as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);

                // Decrement product stock
                $product = Product::find($item['product_id']);
                $product->decrement('stock_quantity', $item['quantity']);
            }

            // Generate installments if there's remaining balance
            if ($remainingBalance > 0) {
                $startDate = $request->start_date ? Carbon::parse($request->start_date) : now()->addMonth();
                
                for ($i = 1; $i <= $request->duration_months; $i++) {
                    Installment::create([
                        'sale_id' => $sale->id,
                        'installment_number' => $i,
                        'amount' => $monthlyInstallment,
                        'due_date' => $startDate->copy()->addMonths($i - 1),
                        'paid_amount' => 0,
                        'status' => 'unpaid',
                    ]);
                }
            }

            // Create finance transaction for advance payment
            if ($advanceReceived > 0) {
                FinanceTransaction::create([
                    'branch_id' => $request->branch_id,
                    'type' => 'in',
                    'category' => 'Advance Received',
                    'amount' => $advanceReceived,
                    'description' => "Advance payment for Sale #{$sale->id}",
                    'transaction_date' => now()->toDateString(),
                    'reference_id' => $sale->id,
                    'reference_type' => 'sale',
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Sale created successfully',
                'data' => $sale->load(['customer', 'saleItems.product', 'installments'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create sale: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Sale $sale)
    {
        $sale->load(['customer', 'branch', 'saleItems.product', 'installments.payments']);
        return view('sales.show', compact('sale'));
    }

    public function payInstallment(PayInstallmentRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();

            $installment = Installment::with('sale')->findOrFail($request->installment_id);
            $paymentAmount = $request->amount;
            $paymentDate = $request->payment_date ?? now()->toDateString();

            // Create payment record
            $payment = Payment::create([
                'installment_id' => $installment->id,
                'amount' => $paymentAmount,
                'payment_date' => $paymentDate,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
            ]);

            // Update installment
            $newPaidAmount = $installment->paid_amount + $paymentAmount;
            $installment->update([
                'paid_amount' => $newPaidAmount,
                'status' => $this->calculateInstallmentStatus($installment->amount, $newPaidAmount),
                'payment_date' => $newPaidAmount >= $installment->amount ? $paymentDate : $installment->payment_date,
            ]);

            // Update sale remaining balance
            $sale = $installment->sale;
            $sale->decrement('remaining_balance', $paymentAmount);
            
            // Update sale status if fully paid
            if ($sale->remaining_balance <= 0) {
                $sale->update(['status' => 'completed']);
            }

            // Create finance transaction
            FinanceTransaction::create([
                'branch_id' => $sale->branch_id,
                'type' => 'in',
                'category' => 'Installment Payment',
                'amount' => $paymentAmount,
                'description' => "Installment payment for Sale #{$sale->id}, Installment #{$installment->installment_number}",
                'transaction_date' => $paymentDate,
                'reference_id' => $payment->id,
                'reference_type' => 'payment',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'data' => $payment->load('installment.sale')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function installments(Request $request)
    {
        $query = Installment::with(['sale.customer', 'sale.branch', 'payments'])
            ->orderBy('due_date', 'asc');

        // Apply filters
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->branch_id) {
            $query->whereHas('sale', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        if ($request->customer_id) {
            $query->whereHas('sale', function ($q) use ($request) {
                $q->where('customer_id', $request->customer_id);
            });
        }

        if ($request->date_from) {
            $query->where('due_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->where('due_date', '<=', $request->date_to);
        }

        if ($request->search) {
            $query->whereHas('sale.customer', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        $installments = $query->paginate(20);
        $branches = \App\Models\Branch::where('is_active', true)->get();

        return view('sales.installments', compact('installments', 'branches'));
    }

    public function print(Sale $sale)
    {
        $sale->load(['customer', 'branch', 'saleItems.product', 'installments.payments']);
        
        return view('sales.print', compact('sale'));
    }

    private function calculateInstallmentStatus($amount, $paidAmount): string
    {
        $amount = (float) $amount;
        $paidAmount = (float) $paidAmount;
        
        if ($paidAmount == 0) {
            return 'unpaid';
        } elseif ($paidAmount >= $amount) {
            return 'paid';
        } else {
            return 'partial';
        }
    }
}