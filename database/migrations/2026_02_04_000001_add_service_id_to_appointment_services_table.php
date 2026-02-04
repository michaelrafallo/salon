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
        Schema::table('appointment_services', function (Blueprint $table) {
            if (! Schema::hasColumn('appointment_services', 'service_id')) {
                $table->foreignId('service_id')
                    ->nullable()
                    ->after('appointment_id')
                    ->constrained('services')
                    ->nullOnDelete();

                $table->index(['appointment_id', 'service_id']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointment_services', function (Blueprint $table) {
            if (Schema::hasColumn('appointment_services', 'service_id')) {
                $table->dropConstrainedForeignId('service_id');
            }
        });
    }
};

