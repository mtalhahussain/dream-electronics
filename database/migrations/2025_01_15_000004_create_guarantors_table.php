<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guarantors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->onDelete('cascade');
            $table->string('name');
            $table->string('cnic', 13);
            $table->string('phone', 20);
            $table->text('address');
            $table->string('relationship');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guarantors');
    }
};