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
        Schema::table('instrument_rentals', function (Blueprint $table) {
            // Add reschedule_source field to track if rental was rescheduled by system or user
            if (!Schema::hasColumn('instrument_rentals', 'reschedule_source')) {
                $table->string('reschedule_source')->nullable()->after('status');
                // Add index for better query performance
                $table->index('reschedule_source');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instrument_rentals', function (Blueprint $table) {
            if (Schema::hasColumn('instrument_rentals', 'reschedule_source')) {
                $table->dropIndex(['reschedule_source']);
                $table->dropColumn('reschedule_source');
            }
        });
    }
};
