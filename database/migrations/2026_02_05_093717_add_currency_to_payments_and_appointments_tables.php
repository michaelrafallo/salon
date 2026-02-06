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
        Schema::table('payments', function (Blueprint $table) {
            if (! Schema::hasColumn('payments', 'currency')) {
                $table->string('currency', 3)->nullable()->after('amount');
            }
        });

        Schema::table('appointment_services', function (Blueprint $table) {
            if (! Schema::hasColumn('appointment_services', 'currency')) {
                $table->string('currency', 3)->nullable()->after('unit_price');
            }
        });

        Schema::table('appointment_technician', function (Blueprint $table) {
            if (! Schema::hasColumn('appointment_technician', 'currency')) {
                $table->string('currency', 3)->nullable()->after('total');
            }
        });

        $currency = 'USD';

        try {
            if (Schema::hasTable('settings')) {
                $saved = DB::table('settings')->where('option_key', 'currency_code')->value('option_value');
                if (is_string($saved) && trim($saved) !== '') {
                    $currency = strtoupper(trim($saved));
                }
            }
        } catch (\Throwable) {
            // keep default
        }

        DB::table('payments')->whereNull('currency')->update(['currency' => $currency]);
        DB::table('appointment_services')->whereNull('currency')->update(['currency' => $currency]);
        DB::table('appointment_technician')->whereNull('currency')->update(['currency' => $currency]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'currency')) {
                $table->dropColumn('currency');
            }
        });

        Schema::table('appointment_services', function (Blueprint $table) {
            if (Schema::hasColumn('appointment_services', 'currency')) {
                $table->dropColumn('currency');
            }
        });

        Schema::table('appointment_technician', function (Blueprint $table) {
            if (Schema::hasColumn('appointment_technician', 'currency')) {
                $table->dropColumn('currency');
            }
        });
    }
};
