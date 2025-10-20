<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->date('payment_date');
            $table->string('payment_month'); // e.g., "2025-01"
            $table->text('notes')->nullable();
            $table->enum('status', ['paid', 'pending', 'partial'])->default('pending');
            $table->timestamps();
            
            $table->unique(['employee_id', 'payment_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_payments');
    }
};