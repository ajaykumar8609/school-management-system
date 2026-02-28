<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->string('fee_type');
            $table->decimal('amount', 12, 2);
            $table->enum('payment_mode', ['Cash', 'Online', 'Card', 'UPI', 'Cheque'])->default('Cash');
            $table->string('transaction_id')->nullable();
            $table->date('payment_date');
            $table->string('receipt_no')->nullable();
            $table->enum('status', ['Paid', 'Pending', 'Cancelled'])->default('Paid');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_payments');
    }
};
