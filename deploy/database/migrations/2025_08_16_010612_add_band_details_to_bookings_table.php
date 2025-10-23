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
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'band_name')) {
                $table->string('band_name')->nullable()->after('service_type');
            }
            if (!Schema::hasColumn('bookings', 'contact_number')) {
                $table->string('contact_number')->nullable()->after('band_name');
            }
            if (!Schema::hasColumn('bookings', 'reference_code')) {
                $table->string('reference_code', 4)->unique()->nullable()->after('contact_number');
            }
            if (!Schema::hasColumn('bookings', 'image_path')) {
                $table->string('image_path')->nullable()->after('reference_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['band_name', 'contact_number', 'reference_code', 'image_path']);
        });
    }
};
