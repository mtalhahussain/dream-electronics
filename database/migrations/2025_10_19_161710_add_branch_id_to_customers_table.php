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
        Schema::table('customers', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->after('id')->constrained()->onDelete('set null');
            // Update CNIC length to accommodate formatted CNIC with dashes (15 chars)
            $table->string('cnic', 15)->change();
            // Add is_active field
            $table->boolean('is_active')->default(true)->after('face_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'branch_id')) {
                try {
                    $table->dropForeign(['branch_id']);
                } catch (\Exception $e) {
                    // ignore if the foreign key does not exist
                }
                $table->dropColumn('branch_id');
            }
            if (Schema::hasColumn('customers', 'is_active')) {
                $table->dropColumn('is_active');
            }
        });
    }
};
