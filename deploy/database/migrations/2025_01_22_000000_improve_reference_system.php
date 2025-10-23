<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Booking;
use App\Models\InstrumentRental;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ensure bookings reference column is properly indexed and has correct constraints
        Schema::table('bookings', function (Blueprint $table) {
            // Make sure reference column exists and is unique
            if (!Schema::hasColumn('bookings', 'reference')) {
                $table->string('reference', 20)->unique()->after('id');
            }
        });

        // Ensure instrument_rentals reference column is properly indexed
        Schema::table('instrument_rentals', function (Blueprint $table) {
            // Modify existing reference column to ensure proper length
            $table->string('reference', 20)->change();
        });

        // Update existing bookings that don't have the new reference format
        $bookings = Booking::whereNull('reference')
            ->orWhere('reference', 'LIKE', 'BK%')
            ->where('reference', 'NOT LIKE', 'BK-____-%')
            ->get();
            
        foreach ($bookings as $booking) {
            $booking->update(['reference' => Booking::generateUniqueReference()]);
        }

        // Update existing instrument rentals that don't have the new reference format
        $rentals = InstrumentRental::whereNull('reference')
            ->orWhere('reference', 'LIKE', 'IR%')
            ->where('reference', 'NOT LIKE', 'IR-____-%')
            ->get();
            
        foreach ($rentals as $rental) {
            $rental->update(['reference' => InstrumentRental::generateUniqueReference()]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No specific rollback needed for this migration
    }
};