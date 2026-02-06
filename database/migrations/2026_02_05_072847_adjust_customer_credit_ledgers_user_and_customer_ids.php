<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'customer_credit_ledgers'
              AND COLUMN_NAME IN ('user_id', 'customer_id')
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        foreach ($foreignKeys as $foreignKey) {
            DB::statement('ALTER TABLE customer_credit_ledgers DROP FOREIGN KEY '.$foreignKey->CONSTRAINT_NAME);
        }

        $indexes = DB::select("
            SELECT INDEX_NAME
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'customer_credit_ledgers'
              AND COLUMN_NAME = 'user_id'
              AND INDEX_NAME <> 'PRIMARY'
        ");

        foreach ($indexes as $index) {
            DB::statement('ALTER TABLE customer_credit_ledgers DROP INDEX '.$index->INDEX_NAME);
        }

        if (! Schema::hasColumn('customer_credit_ledgers', 'customer_id')) {
            Schema::table('customer_credit_ledgers', function (Blueprint $table) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('user_id');
                $table->index('customer_id');
            });
        }

        DB::statement('ALTER TABLE customer_credit_ledgers MODIFY customer_id BIGINT UNSIGNED NULL');
        DB::statement('UPDATE customer_credit_ledgers SET customer_id = NULL WHERE customer_id = 0');

        DB::statement('
            UPDATE customer_credit_ledgers l
            JOIN customers c ON c.id = l.user_id
            SET l.customer_id = l.user_id
            WHERE l.customer_id IS NULL
        ');

        DB::statement('
            DELETE l
            FROM customer_credit_ledgers l
            LEFT JOIN customers c ON c.id = l.customer_id
            WHERE l.customer_id IS NULL OR c.id IS NULL
        ');

        DB::statement('ALTER TABLE customer_credit_ledgers MODIFY user_id BIGINT UNSIGNED NULL');
        DB::statement('UPDATE customer_credit_ledgers SET user_id = NULL');

        DB::statement('ALTER TABLE customer_credit_ledgers MODIFY customer_id BIGINT UNSIGNED NOT NULL');

        $userIdIndex = DB::selectOne("
            SELECT 1 AS has_index
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'customer_credit_ledgers'
              AND COLUMN_NAME = 'user_id'
              AND INDEX_NAME <> 'PRIMARY'
            LIMIT 1
        ");

        if (! $userIdIndex) {
            Schema::table('customer_credit_ledgers', function (Blueprint $table) {
                $table->index('user_id');
            });
        }

        Schema::table('customer_credit_ledgers', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('customer_id')->references('id')->on('customers')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'customer_credit_ledgers'
              AND COLUMN_NAME IN ('user_id', 'customer_id')
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        foreach ($foreignKeys as $foreignKey) {
            DB::statement('ALTER TABLE customer_credit_ledgers DROP FOREIGN KEY '.$foreignKey->CONSTRAINT_NAME);
        }

        DB::statement('ALTER TABLE customer_credit_ledgers MODIFY user_id BIGINT UNSIGNED NOT NULL');

        Schema::table('customer_credit_ledgers', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('customers')->cascadeOnDelete();

            $table->dropIndex(['customer_id']);
            $table->dropColumn('customer_id');
        });
    }
};
