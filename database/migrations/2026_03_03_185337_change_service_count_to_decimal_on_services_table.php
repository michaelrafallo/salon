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
        if (Schema::hasColumn('services', 'service_count')) {
            Schema::table('services', function (Blueprint $table) {
                $table->decimal('service_count', 5, 1)->default(0)->change();
            });
        } else {
            Schema::table('services', function (Blueprint $table) {
                $table->decimal('service_count', 5, 1)->default(0)->after('price');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('services', 'service_count')) {
            Schema::table('services', function (Blueprint $table) {
                $table->unsignedInteger('service_count')->default(1)->change();
            });
        }
    }
};
