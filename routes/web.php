<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\StockCreditController;
use App\Http\Controllers\SalaryPaymentController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Product routes
    Route::resource('products', ProductController::class)->only(['index','store','update','destroy']);
    Route::patch('/products/{product}/toggle-active', [ProductController::class, 'toggle'])->name('products.toggle');
    
    // Category routes
    Route::resource('categories', CategoryController::class)->only(['index','store','update','destroy']);
    Route::get('/categories/active', [CategoryController::class, 'getActive'])->name('categories.active');
    
    // Customer routes
    Route::resource('customers', CustomerController::class);
    Route::get('/customers/{customer}/get', [CustomerController::class, 'getCustomer'])->name('customers.get');
    
    // Sales routes
    Route::get('installments', [SaleController::class, 'installments'])->name('sales.installments');
    Route::post('sales/pay-installment', [SaleController::class, 'payInstallment'])->name('sales.pay-installment');
    Route::get('sales/{sale}/print', [SaleController::class, 'print'])->name('sales.print');
    Route::resource('sales', SaleController::class);
    
    // Finance routes
    Route::get('finance', [FinanceController::class, 'index'])->name('finance.index');
    Route::get('finance/summary', [FinanceController::class, 'summary'])->name('finance.summary');
    
    // Expense Management routes
    Route::get('expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('expenses/{expense}/get', [ExpenseController::class, 'getExpense'])->name('expenses.get');
    Route::put('expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
    
    // Stock Credit Management routes
    Route::get('stock-credits', [StockCreditController::class, 'index'])->name('stock-credits.index');
    Route::post('stock-credits', [StockCreditController::class, 'store'])->name('stock-credits.store');
    Route::get('stock-credits/{stockCredit}/get', [StockCreditController::class, 'getStockCredit'])->name('stock-credits.get');
    Route::put('stock-credits/{stockCredit}', [StockCreditController::class, 'update'])->name('stock-credits.update');
    Route::delete('stock-credits/{stockCredit}', [StockCreditController::class, 'destroy'])->name('stock-credits.destroy');
    
    // Salary Payment Management routes
    Route::get('salary-payments', [SalaryPaymentController::class, 'index'])->name('salary-payments.index');
    Route::post('salary-payments', [SalaryPaymentController::class, 'store'])->name('salary-payments.store');
    Route::get('salary-payments/{salaryPayment}/get', [SalaryPaymentController::class, 'getSalaryPayment'])->name('salary-payments.get');
    Route::put('salary-payments/{salaryPayment}', [SalaryPaymentController::class, 'update'])->name('salary-payments.update');
    Route::delete('salary-payments/{salaryPayment}', [SalaryPaymentController::class, 'destroy'])->name('salary-payments.destroy');
    Route::post('salary-payments/generate', [SalaryPaymentController::class, 'generate'])->name('salary-payments.generate');
    
    // Employee routes
    Route::get('employees/roles', [EmployeeController::class, 'roles'])->name('employees.roles');
    Route::resource('employees', EmployeeController::class);
    
    // Branch routes
    Route::get('branches', [BranchController::class, 'index'])->name('branches.index');
    Route::post('branches', [BranchController::class, 'store'])->name('branches.store');
    Route::put('branches/{branch}', [BranchController::class, 'update'])->name('branches.update');
    Route::delete('branches/{branch}', [BranchController::class, 'destroy'])->name('branches.destroy');
    
    // SMS routes
    Route::get('sms', [SmsController::class, 'index'])->name('sms.index');
    Route::get('sms/logs', [SmsController::class, 'logs'])->name('sms.logs');
    Route::post('sms/send', [SmsController::class, 'send'])->name('sms.send');
    Route::post('sms/send-bulk', [SmsController::class, 'sendBulk'])->name('sms.send-bulk');
    Route::post('sms/send-reminder', [SmsController::class, 'sendReminder'])->name('sms.send-reminder');
    
    // Settings routes
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::post('settings/test-twilio', [SettingController::class, 'testTwilio'])->name('settings.test-twilio');
});

require __DIR__.'/auth.php';
