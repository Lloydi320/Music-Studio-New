<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;

class InstrumentRentalBookingsSeeder extends Seeder
{
    public function run()
    {
        // Get the first user or create a test user
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        // Create some instrument rental bookings
        $instrumentBookings = [
            [
                'date' => Carbon::now()->addDays(1)->format('Y-m-d'),
                'time_slot' => '10:00 AM - 02:00 PM',
                'duration' => 4,
                'price' => 150,
                'total_amount' => 600,
                'status' => 'confirmed',
            ],
            [
                'date' => Carbon::now()->addDays(2)->format('Y-m-d'),
                'time_slot' => '02:00 PM - 04:00 PM',
                'duration' => 2,
                'price' => 150,
                'total_amount' => 300,
                'status' => 'confirmed',
            ],
            [
                'date' => Carbon::now()->addDays(3)->format('Y-m-d'),
                'time_slot' => '09:00 AM - 12:00 PM',
                'duration' => 3,
                'price' => 150,
                'total_amount' => 450,
                'status' => 'pending',
            ],
        ];

        foreach ($instrumentBookings as $bookingData) {
            Booking::create([
                'user_id' => $user->id,
                'date' => $bookingData['date'],
                'time_slot' => $bookingData['time_slot'],
                'duration' => $bookingData['duration'],
                'price' => $bookingData['price'],
                'total_amount' => $bookingData['total_amount'],
                'reference' => 'INST' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
                'status' => $bookingData['status'],
                'service_type' => 'instrument_rental',
            ]);
        }

        echo "Instrument rental bookings created successfully!\n";
    }
}