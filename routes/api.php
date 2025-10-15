<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\FinanceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Sales API routes
Route::prefix('sales')->group(function () {
    Route::post('/', [SaleController::class, 'store']);
    Route::post('pay-installment', [SaleController::class, 'payInstallment']);
    Route::get('{sale}/installments', function (\App\Models\Sale $sale) {
        $installments = $sale->installments()->orderBy('installment_number')->get();
        return response()->json([
            'success' => true,
            'installments' => $installments
        ]);
    });
});

// Finance API routes
Route::prefix('finance')->group(function () {
    Route::get('summary', [FinanceController::class, 'summary']);
});