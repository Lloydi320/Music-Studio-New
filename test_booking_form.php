<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Booking;
use App\Models\User;

echo "Testing booking form submission...\n";

// Check if any users exist
$users = User::all();
echo "Found " . $users->count() . " users\n";

if ($users->count() > 0) {
    $user = $users->first();
    echo "Using user: " . $user->name . " (ID: " . $user->id . ")\n";
    
    // Simulate booking form data
    $bookingData = [
        'date' => '2025-07-28',
        'time_slot' => '09:00 AM - 12:00 PM',
        'duration' => 3
    ];
    
    echo "Attempting to create booking with data:\n";
    echo "- Date: " . $bookingData['date'] . "\n";
    echo "- Time: " . $bookingData['time_slot'] . "\n";
    echo "- Duration: " . $bookingData['duration'] . " hours\n";
    
    // Try to create a booking
    try {
        $booking = Booking::create([
            'user_id' => $user->id,
            'date' => $bookingData['date'],
            'time_slot' => $bookingData['time_slot'],
            'duration' => $bookingData['duration'],
            'status' => 'confirmed'
        ]);
        
        echo "✅ Test booking created successfully!\n";
        echo "ID: " . $booking->id . "\n";
        echo "Reference: " . $booking->reference . "\n";
        
    } catch (Exception $e) {
        echo "❌ Error creating test booking: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ No users found in database!\n";
}

// Show all current bookings
echo "\nCurrent bookings in database:\n";
$bookings = Booking::all();
foreach ($bookings as $booking) {
    echo "- " . $booking->date . " " . $booking->time_slot . " (" . $booking->duration . "hrs) - " . $booking->status . "\n";
}
?> 