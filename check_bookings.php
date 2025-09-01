<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\BookingController;
use App\Models\Booking;
use Carbon\Carbon;

// Show ALL bookings first
echo "ALL bookings in database:\n";
$allBookings = Booking::all();
foreach ($allBookings as $booking) {
    echo "ID: {$booking->id}, Date: {$booking->date}, Time: {$booking->time_slot}, Status: {$booking->status}\n";
}
echo "\n";

// Test the getBookedDates method
$controller = new BookingController();
$response = $controller->getBookedDates();
$content = $response->getContent();

echo "API Response:\n";
echo $content . "\n\n";

// Show current confirmed and pending bookings for reference
echo "Confirmed and Pending bookings:\n";
$relevantBookings = Booking::whereIn('status', ['confirmed', 'pending'])->get();
foreach ($relevantBookings as $booking) {
    echo "ID: {$booking->id}, Date: {$booking->date}, Time: {$booking->time_slot}, Status: {$booking->status}\n";
}