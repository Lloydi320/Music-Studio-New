<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class UpdateBookingServiceTypesSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $this->command->info('Updating existing bookings with service types...');
        
        // Update bookings based on duration and time patterns
        
        // Recording Sessions (4+ hours, typically longer sessions)
        DB::table('bookings')
            ->where('duration', '>=', 240)
            ->update(['service_type' => 'recording_session']);
        
        // Music Lessons (1-2 hours, common lesson times)
        DB::table('bookings')
            ->where('duration', '<=', 120)
            ->whereIn('time_slot', ['09:00', '10:00', '11:00', '14:00', '15:00', '16:00'])
            ->update(['service_type' => 'music_lesson']);
        
        // Band Practice (3 hours, evening/weekend slots)
        DB::table('bookings')
            ->where('duration', 180)
            ->whereIn('time_slot', ['18:00', '19:00', '20:00'])
            ->update(['service_type' => 'band_practice']);
        
        // Audio Production (2-3 hours, professional times)
        DB::table('bookings')
            ->whereBetween('duration', [120, 180])
            ->whereIn('time_slot', ['10:00', '13:00', '14:00'])
            ->where('service_type', 'studio_rental') // Only update if still default
            ->update(['service_type' => 'audio_production']);
        
        // Remaining bookings stay as studio_rental (default)
        
        $recordingSessions = Booking::where('service_type', 'recording_session')->count();
        $musicLessons = Booking::where('service_type', 'music_lesson')->count();
        $bandPractice = Booking::where('service_type', 'band_practice')->count();
        $audioProduction = Booking::where('service_type', 'audio_production')->count();
        $studioRentals = Booking::where('service_type', 'studio_rental')->count();
        
        $this->command->info("Service types updated:");
        $this->command->info("- Recording Sessions: {$recordingSessions}");
        $this->command->info("- Music Lessons: {$musicLessons}");
        $this->command->info("- Band Practice: {$bandPractice}");
        $this->command->info("- Audio Production: {$audioProduction}");
        $this->command->info("- Studio Rentals: {$studioRentals}");
    }
}
