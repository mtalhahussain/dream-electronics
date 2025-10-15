<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $currentMonth = Carbon::now();
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        // KPI calculations
        $revenueThisMonth = DB::table('finance_transactions')
            ->where('type', 'in')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->sum('amount') ?? 0;

        $expensesThisMonth = DB::table('finance_transactions')
            ->where('type', 'out')
            ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->sum('amount') ?? 0;

        $outstandingBalance = DB::table('sales')
            ->sum('remaining_balance') ?? 0;

        $dueThisWeekCount = DB::table('installments')
            ->whereBetween('due_date', [$startOfWeek, $endOfWeek])
            ->whereIn('status', ['unpaid', 'partial'])
            ->count();

        // Last 6 months data
        $monthsData = [];
        $collectionsData = [];
        $expensesData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $monthsData[] = $month->format('M');
            
            $collections = DB::table('finance_transactions')
                ->where('type', 'in')
                ->whereIn('category', ['Advance Received', 'Installment Payment'])
                ->whereBetween('transaction_date', [$monthStart, $monthEnd])
                ->sum('amount') ?? 0;
            
            $expenses = DB::table('finance_transactions')
                ->where('type', 'out')
                ->whereBetween('transaction_date', [$monthStart, $monthEnd])
                ->sum('amount') ?? 0;
            
            $collectionsData[] = $collections;
            $expensesData[] = $expenses;
        }

        $monthsLabels = $monthsData;
        $collectionsSeries = $collectionsData;
        $expensesSeries = $expensesData;

        // Sales status split
        $salesStatusSplit = [
            'active' => DB::table('sales')->where('status', 'pending')->count(),
            'completed' => DB::table('sales')->where('status', 'completed')->count(),
            'defaulted' => 0, // Assuming no defaulted status in current schema
            'cancelled' => 0  // Assuming no cancelled status in current schema
        ];

        // Upcoming installments
        $upcomingInstallments = DB::table('installments')
            ->join('sales', 'installments.sale_id', '=', 'sales.id')
            ->join('customers', 'sales.customer_id', '=', 'customers.id')
            ->join('branches', 'sales.branch_id', '=', 'branches.id')
            ->select(
                'installments.*',
                'sales.id as sale_id',
                'customers.name as customer_name',
                'branches.name as branch_name'
            )
            ->whereIn('installments.status', ['unpaid', 'partial'])
            ->orderBy('installments.due_date')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'revenueThisMonth',
            'expensesThisMonth',
            'outstandingBalance',
            'dueThisWeekCount',
            'monthsLabels',
            'collectionsSeries',
            'expensesSeries',
            'salesStatusSplit',
            'upcomingInstallments'
        ));
    }
}