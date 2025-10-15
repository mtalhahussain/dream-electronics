<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches');
            $table->foreignId('customer_id')->constrained('customers');
            $table->decimal('total_price', 12, 2);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->decimal('net_total', 12, 2);
            $table->decimal('advance_received', 12, 2)->default(0);
            $table->decimal('remaining_balance', 12, 2);
            $table->integer('duration_months');
            $table->decimal('monthly_installment', 12, 2);
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->date('sale_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};