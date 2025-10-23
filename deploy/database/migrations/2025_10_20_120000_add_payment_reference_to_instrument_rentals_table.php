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
            if (!Schema::hasColumn('instrument_rentals', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->after('four_digit_code');
                $table->index('payment_reference');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instrument_rentals', function (Blueprint $table) {
            if (Schema::hasColumn('instrument_rentals', 'payment_reference')) {
                $table->dropIndex(['payment_reference']);
                $table->dropColumn('payment_reference');
            }
        });
    }
};