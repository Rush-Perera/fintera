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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_method_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['income', 'expense']);
            $table->date('transacted_on');
            $table->decimal('amount', 12, 2);
            $table->char('currency', 3)->default('LKR');
            $table->string('category')->nullable();
            $table->text('remarks')->nullable();
            $table->string('payslip_path')->nullable();
            $table->string('payslip_original_name')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'transacted_on']);
            $table->index(['user_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
