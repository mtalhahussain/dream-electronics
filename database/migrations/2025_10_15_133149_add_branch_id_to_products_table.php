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
        // Step 1: Add branch_id column if it doesn't exist
        if (!Schema::hasColumn('products', 'branch_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->foreignId('branch_id')->nullable()->after('id')->constrained()->nullOnDelete()->index();
            });
        }

        // Step 2: Backfill existing products with default branch_id
        $productsWithoutBranch = DB::table('products')->whereNull('branch_id')->count();
        
        if ($productsWithoutBranch > 0) {
            // Try to get default branch from settings, otherwise use first available branch
            $defaultBranchId = DB::table('settings')->where('key', 'default_branch_id')->value('value');
            
            if (!$defaultBranchId) {
                $defaultBranchId = DB::table('branches')->orderBy('id')->value('id');
            }
            
            if ($defaultBranchId) {
                DB::table('products')
                    ->whereNull('branch_id')
                    ->update(['branch_id' => $defaultBranchId]);
            }
        }

        // Step 3: Make branch_id NOT NULL if all rows are filled
        $nullBranchCount = DB::table('products')->whereNull('branch_id')->count();
        if ($nullBranchCount === 0) {
            DB::statement('ALTER TABLE products MODIFY branch_id BIGINT UNSIGNED NOT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('products', 'branch_id')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropForeign(['branch_id']);
                $table->dropColumn('branch_id');
            });
        }
    }
};
