<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 0: Ensure the products table exists, then add branch_id if missing
        if (Schema::hasTable('products')) {
            if (!Schema::hasColumn('products', 'branch_id')) {
            Schema::table('products', function (Blueprint $table) {
                // Ensure branch_id matches branches.id type (unsignedBigInteger)
                $table->unsignedBigInteger('branch_id')->nullable()->after('id');
                $table->index('branch_id');
            });
            }
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
            Schema::table('products', function (Blueprint $table) {
              
                $table->dropColumn('branch_id');
            });
            
        
    }
};
