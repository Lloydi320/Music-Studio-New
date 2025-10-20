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
            if (!Schema::hasColumn('instrument_rentals', 'delivery_time')) {
                $table->string('delivery_time')->nullable()->after('transportation');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instrument_rentals', function (Blueprint $table) {
            if (Schema::hasColumn('instrument_rentals', 'delivery_time')) {
                $table->dropColumn('delivery_time');
            }
        });
    }
};