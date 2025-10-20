<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->enum('role', ['employee', 'product_manager', 'sales_manager', 'admin'])->default('employee')->after('position');
            $table->json('permissions')->nullable()->after('role');
        });
    }

    public function down(): void
    {
        // Check for column existence before attempting to drop to avoid SQL errors
        if (Schema::hasColumn('employees', 'role') || Schema::hasColumn('employees', 'permissions')) {
            Schema::table('employees', function (Blueprint $table) {
                if (Schema::hasColumn('employees', 'role')) {
                    $table->dropColumn('role');
                }
                if (Schema::hasColumn('employees', 'permissions')) {
                    $table->dropColumn('permissions');
                }
            });
        }
    }
};