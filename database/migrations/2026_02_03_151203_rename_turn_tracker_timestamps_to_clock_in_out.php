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
        Schema::table('turn_trackers', function (Blueprint $table) {
            $table->dateTime('clock_in')->nullable()->after('services');
            $table->dateTime('clock_out')->nullable()->after('clock_in');
        });

        DB::table('turn_trackers')->update([
            'clock_in' => DB::raw('created_at'),
            'clock_out' => DB::raw('updated_at'),
        ]);

        Schema::table('turn_trackers', function (Blueprint $table) {
            $table->dropColumn(['created_at', 'updated_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('turn_trackers', function (Blueprint $table) {
            $table->timestamps();
        });

        DB::table('turn_trackers')->update([
            'created_at' => DB::raw('clock_in'),
            'updated_at' => DB::raw('clock_out'),
        ]);

        Schema::table('turn_trackers', function (Blueprint $table) {
            $table->dropColumn(['clock_in', 'clock_out']);
        });
    }
};
