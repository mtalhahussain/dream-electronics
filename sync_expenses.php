<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Expense;
use App\Models\FinanceTransaction;

echo "Starting expense sync...\n";

$expenses = Expense::all();
$created = 0;

foreach ($expenses as $expense) {
    $existingTransaction = FinanceTransaction::where('reference_type', 'App\Models\Expense')
        ->where('reference_id', $expense->id)
        ->first();
    
    if (!$existingTransaction) {
        FinanceTransaction::create([
            'branch_id' => $expense->branch_id,
            'type' => 'out',
            'category' => $expense->category,
            'description' => $expense->description,
            'amount' => $expense->amount,
            'reference_type' => 'App\Models\Expense',
            'reference_id' => $expense->id,
            'transaction_date' => $expense->expense_date
        ]);
        $created++;
        echo "Created finance transaction for expense ID: {$expense->id}\n";
    }
}

echo "Sync completed! Created {$created} finance transactions for existing expenses.\n";