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
    
    // Employee routes
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
