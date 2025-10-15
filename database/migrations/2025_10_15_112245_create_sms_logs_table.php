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
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('phone');
            $table->text('message');
            $table->enum('type', ['manual', 'reminder', 'bulk'])->default('manual');
            $table->unsignedBigInteger('reference_id')->nullable(); // installment_id for reminders
            $table->enum('status', ['sent', 'failed', 'pending'])->default('pending');
            $table->string('twilio_sid')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at');
            $table->timestamps();

            $table->index(['customer_id', 'type']);
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
    }
};
