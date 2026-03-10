<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('ghl_contact_id')->nullable()->after('email');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->string('ghl_appointment_id')->nullable()->after('appointment_datetime');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('ghl_contact_id');
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('ghl_appointment_id');
        });
    }
};
