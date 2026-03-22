<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('ghl_staff_id')->nullable()->unique()->after('profile_photo');
            $table->string('ghl_calendar_id')->nullable()->after('ghl_staff_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ghl_staff_id', 'ghl_calendar_id']);
        });
    }
};
