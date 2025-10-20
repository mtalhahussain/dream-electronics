<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customer_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
            $table->string('discount_type')->comment('percentage, fixed_amount, etc.');
            $table->decimal('discount_value', 10, 2)->comment('Discount amount or percentage');
            $table->string('category')->nullable()->comment('Product category for discount');
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_discounts');
    }
};
