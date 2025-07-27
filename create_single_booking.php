<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Booking;
use App\Models\User;

echo "Creating a single test booking...\n";

// Check if any users exist
$users = User::all();
echo "Found " . $users->count() . " users\n";

if ($users->count() > 0) {
    $user = $users->first();
    echo "Using user: " . $user->name . " (ID: " . $user->id . ")\n";
    
    // Create a single booking
    try {
        $booking = Booking::create([
            'user_id' => $user->id,
            'date' => '2025-07-27',
            'time_slot' => '10:00 AM - 01:00 PM',
            'duration' => 3,
            'status' => 'confirmed'
        ]);
        
        echo "✅ Single booking created successfully!\n";
        echo "ID: " . $booking->id . "\n";
        echo "Reference: " . $booking->reference . "\n";
        echo "Date: " . $booking->date . "\n";
        echo "Time: " . $booking->time_slot . "\n";
        echo "Duration: " . $booking->duration . " hours\n";
        
    } catch (Exception $e) {
        echo "❌ Error creating booking: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ No users found in database!\n";
}

// Show current bookings
echo "\nCurrent bookings in database:\n";
$bookings = Booking::all();
foreach ($bookings as $booking) {
    echo "- " . $booking->date . " " . $booking->time_slot . " (" . $booking->duration . "hrs) - " . $booking->status . "\n";
}
?> 