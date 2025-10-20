<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('name');
            $table->string('cnic', 15)->unique();
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->string('position');
            $table->decimal('salary', 10, 2);
            $table->date('hire_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('employees');
        Schema::enableForeignKeyConstraints();
    }
};