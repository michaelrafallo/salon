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
        Schema::table('appointment_technician', function (Blueprint $table) {
            $table->decimal('total_service', 12, 2)->default(0)->after('tip');
            $table->decimal('commission', 12, 2)->default(0)->after('total_service');
            $table->decimal('total', 12, 2)->default(0)->after('commission');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointment_technician', function (Blueprint $table) {
            $table->dropColumn(['total_service', 'commission', 'total']);
        });
    }
};
