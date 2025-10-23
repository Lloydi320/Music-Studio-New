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
            if (!Schema::hasColumn('instrument_rentals', 'name')) {
                $table->string('name')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('instrument_rentals', 'email')) {
                $table->string('email')->nullable()->after('name');
            }
            if (!Schema::hasColumn('instrument_rentals', 'phone')) {
                $table->string('phone', 11)->nullable()->after('email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instrument_rentals', function (Blueprint $table) {
            if (Schema::hasColumn('instrument_rentals', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('instrument_rentals', 'email')) {
                $table->dropColumn('email');
            }
            if (Schema::hasColumn('instrument_rentals', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};