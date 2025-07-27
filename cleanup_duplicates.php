<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Booking;

echo "Cleaning up duplicate bookings...\n";

// Remove all test bookings for July 27th
$deleted = Booking::where('date', '2025-07-27')->delete();

echo "Removed $deleted duplicate booking(s) from database.\n";
echo "Your database is now clean!\n";

// Show remaining bookings
echo "\nRemaining bookings in database:\n";
$bookings = Booking::all();
if ($bookings->count() == 0) {
    echo "No bookings found.\n";
} else {
    foreach ($bookings as $booking) {
        echo "- " . $booking->date . " " . $booking->time_slot . " (" . $booking->duration . "hrs) - " . $booking->status . "\n";
    }
}
?> 