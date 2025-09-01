<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;

echo "=== Checking for Duplicate Reference Codes ===\n\n";

// Find duplicate reference codes
$duplicates = Booking::select('reference_code')
    ->whereNotNull('reference_code')
    ->groupBy('reference_code')
    ->havingRaw('COUNT(*) > 1')
    ->get();

if ($duplicates->count() > 0) {
    echo "Found duplicate reference codes:\n";
    foreach ($duplicates as $dup) {
        $bookings = Booking::where('reference_code', $dup->reference_code)->get();
        echo "Reference Code: {$dup->reference_code}\n";
        foreach ($bookings as $booking) {
            echo "  ID: {$booking->id}, Date: {$booking->date}, Status: {$booking->status}, Created: {$booking->created_at}\n";
        }
        echo "\n";
    }
} else {
    echo "No duplicate reference codes found in database.\n";
}

// Also check recent bookings
echo "\n=== Recent Bookings (Last 10) ===\n";
$recentBookings = Booking::orderBy('created_at', 'desc')->limit(10)->get();
foreach ($recentBookings as $booking) {
    echo "ID: {$booking->id}, Reference: {$booking->reference_code}, Date: {$booking->date}, Status: {$booking->status}, Created: {$booking->created_at}\n";
}

echo "\n=== Check Complete ===\n";