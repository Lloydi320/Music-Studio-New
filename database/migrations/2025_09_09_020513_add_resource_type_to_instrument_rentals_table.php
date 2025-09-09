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
            $table->string('resource_type')->default('instrument_rental')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instrument_rentals', function (Blueprint $table) {
            $table->dropColumn('resource_type');
        });
    }
};
