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
        Schema::table('customer_credit_ledgers', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('customers')->cascadeOnDelete();
            $table->string('operation', 32);
            $table->decimal('amount', 10, 2);
            $table->decimal('remaining_balance', 10, 2);
            $table->decimal('new_balance', 10, 2);

            $table->dropColumn('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_credit_ledgers', function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable();

            $table->dropColumn([
                'operation',
                'amount',
                'remaining_balance',
                'new_balance',
            ]);

            $table->dropConstrainedForeignId('user_id');
        });
    }
};
