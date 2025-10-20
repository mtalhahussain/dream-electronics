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
        Schema::table('products', function (Blueprint $table) {
            // Add missing fields
            $table->decimal('purchase_cost', 12, 2)->nullable()->after('price')->comment('Cost at which item was purchased');
            $table->string('purchased_from')->nullable()->after('purchase_cost')->comment('Supplier/vendor name');
            $table->string('sku')->unique()->nullable()->after('purchased_from')->comment('Stock Keeping Unit');
            $table->string('serial_number')->unique()->nullable()->after('sku')->comment('Unique serial number for each item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['purchase_cost', 'purchased_from', 'sku', 'serial_number']);
        });
    }
};
