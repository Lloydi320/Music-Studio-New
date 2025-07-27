<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Booking;
use App\Models\User;

// Check if any users exist
$users = User::all();
echo "Existing users: " . $users->count() . "\n";

if ($users->count() > 0) {
    $userId = $users->first()->id;
    echo "Using user ID: " . $userId . "\n";
} else {
    $userId = null;
    echo "No users found, creating booking without user_id\n";
}

// Create a test booking for July 27th, 2025
$booking = Booking::create([
    'date' => '2025-07-27',
    'time_slot' => '08:30 AM - 11:30 AM',
    'duration' => 3,
    'user_id' => $userId,
    'status' => 'confirmed'
]);

echo "Test booking created: " . $booking->date . ' - ' . $booking->time_slot . ' (' . $booking->duration . 'hrs)' . "\n";
?> 