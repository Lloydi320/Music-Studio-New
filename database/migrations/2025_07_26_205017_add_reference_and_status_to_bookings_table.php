<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Add reference column if it doesn't exist
            if (!Schema::hasColumn('bookings', 'reference')) {
                $table->string('reference')->nullable()->after('id');
            }
            
            // Add status column if it doesn't exist
            if (!Schema::hasColumn('bookings', 'status')) {
                $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending')->after('duration');
            }
        });

        // Generate references for existing bookings that don't have one
        $bookings = \App\Models\Booking::whereNull('reference')->get();
        foreach ($bookings as $booking) {
            $booking->update(['reference' => 'BK' . strtoupper(Str::random(8))]);
        }

        // Now make reference unique if it's not already
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'reference')) {
                $table->string('reference')->unique()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['reference', 'status']);
        });
    }
};
