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
        Schema::table('turn_trackers', function (Blueprint $table) {
            $table->decimal('services', 8, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('turn_trackers', function (Blueprint $table) {
            $table->unsignedInteger('services')->default(0)->change();
        });
    }
};
