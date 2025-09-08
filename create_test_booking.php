<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Booking;
use App\Models\User;

$user = User::first();
if ($user) {
    $booking = Booking::create([
        'user_id' => $user->id,
        'service_type' => 'solo_rehearsal',
        'date' => '2025-01-20',
        'time_slot' => '02:00 PM - 03:00 PM',
        'duration' => 1,
        'price' => 300,
        'total_amount' => 300,
        'status' => 'confirmed',
        'band_name' => 'Test Solo Artist',
        'email' => 'test@example.com',
        'contact_number' => '09123456789'
    ]);
    echo 'Solo rehearsal booking created with ID: ' . $booking->id . PHP_EOL;
    echo 'Service type: ' . $booking->service_type . PHP_EOL;
} else {
    echo 'No users found' . PHP_EOL;
}

// Check total solo rehearsal bookings
echo 'Total solo rehearsal bookings: ' . Booking::where('service_type', 'solo_rehearsal')->count() . PHP_EOL;