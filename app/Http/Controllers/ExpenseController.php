<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Branch;
use App\Models\FinanceTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Expense::with('branch');

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('from')) {
            $query->where('expense_date', '>=', $request->from);
        }

        if ($request->filled('to')) {
            $query->where('expense_date', '<=', $request->to);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->paginate(20);
        $branches = Branch::where('is_active', true)->get();

        return view('expenses.index', compact('expenses', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'category' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'receipt' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        $expense = Expense::create([
            'branch_id' => $request->branch_id,
            'category' => $request->category,
            'description' => $request->description,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'receipt_path' => $receiptPath
        ]);

        // Create corresponding finance transaction
        FinanceTransaction::create([
            'branch_id' => $request->branch_id,
            'type' => 'out',
            'category' => $request->category,
            'description' => $request->description,
            'amount' => $request->amount,
            'reference_type' => 'App\Models\Expense',
            'reference_id' => $expense->id,
            'transaction_date' => $request->expense_date
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Expense added successfully!'
        ]);
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'category' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'receipt' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $receiptPath = $expense->receipt_path;
        if ($request->hasFile('receipt')) {
            // Delete old receipt
            if ($receiptPath) {
                Storage::disk('public')->delete($receiptPath);
            }
            $receiptPath = $request->file('receipt')->store('receipts', 'public');
        }

        $expense->update([
            'branch_id' => $request->branch_id,
            'category' => $request->category,
            'description' => $request->description,
            'amount' => $request->amount,
            'expense_date' => $request->expense_date,
            'receipt_path' => $receiptPath
        ]);

        // Update corresponding finance transaction
        $financeTransaction = FinanceTransaction::where('reference_type', 'App\Models\Expense')
            ->where('reference_id', $expense->id)
            ->first();

        if ($financeTransaction) {
            $financeTransaction->update([
                'branch_id' => $request->branch_id,
                'category' => $request->category,
                'description' => $request->description,
                'amount' => $request->amount,
                'transaction_date' => $request->expense_date
            ]);
        } else {
            // Create finance transaction if it doesn't exist
            FinanceTransaction::create([
                'branch_id' => $request->branch_id,
                'type' => 'out',
                'category' => $request->category,
                'description' => $request->description,
                'amount' => $request->amount,
                'reference_type' => 'App\Models\Expense',
                'reference_id' => $expense->id,
                'transaction_date' => $request->expense_date
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Expense updated successfully!'
        ]);
    }

    public function destroy(Expense $expense)
    {
        if ($expense->receipt_path) {
            Storage::disk('public')->delete($expense->receipt_path);
        }

        // Delete corresponding finance transaction
        FinanceTransaction::where('reference_type', 'App\Models\Expense')
            ->where('reference_id', $expense->id)
            ->delete();

        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expense deleted successfully!'
        ]);
    }

    public function getExpense(Expense $expense)
    {
        return response()->json([
            'success' => true,
            'expense' => $expense->load('branch')
        ]);
    }
}