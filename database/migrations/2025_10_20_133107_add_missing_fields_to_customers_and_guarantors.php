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
        // Add missing fields to customers table
        Schema::table('customers', function (Blueprint $table) {
            $table->string('account_number')->unique()->nullable()->after('cnic')->comment('Auto-generated account number');
            $table->string('profession')->nullable()->after('address')->comment('Customer profession');
            $table->string('father_husband_name')->nullable()->after('profession')->comment('S/o W/o D/o field');
        });

        // Add missing fields to guarantors table
        Schema::table('guarantors', function (Blueprint $table) {
            $table->string('account_number')->unique()->nullable()->after('cnic')->comment('Auto-generated account number');
            $table->string('profession')->nullable()->after('address')->comment('Guarantor profession');
            $table->string('father_husband_name')->nullable()->after('profession')->comment('S/o W/o D/o field');
            $table->string('biometric_path')->nullable()->after('father_husband_name')->comment('Guarantor biometric picture');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['account_number', 'profession', 'father_husband_name']);
        });

        Schema::table('guarantors', function (Blueprint $table) {
            $table->dropColumn(['account_number', 'profession', 'father_husband_name', 'biometric_path']);
        });
    }
};
