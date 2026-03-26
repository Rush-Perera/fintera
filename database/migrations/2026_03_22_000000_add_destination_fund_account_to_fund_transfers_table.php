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
        Schema::table('fund_transfers', function (Blueprint $table) {
            $table->foreignId('payment_method_id')->nullable()->change();
            $table->foreignId('destination_fund_account_id')->nullable()->constrained('fund_accounts')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fund_transfers', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['destination_fund_account_id']);
            $table->dropColumn('destination_fund_account_id');
            $table->foreignId('payment_method_id')->nullable(false)->change();
        });
    }
};
