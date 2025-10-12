<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\BookingController;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;

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

// Test getBookingsByDate for a date range (e.g., Oct 29–31, 2025)
echo "\nTesting getBookingsByDate for 2025-10-29 to 2025-10-31:\n";
foreach (['2025-10-29','2025-10-30','2025-10-31'] as $testDate) {
    $request = Request::create('/api/bookings-by-date', 'GET', ['date' => $testDate]);
    $response = $controller->getBookingsByDate($request);
    echo "Date {$testDate} -> ".$response->getContent()."\n";
}