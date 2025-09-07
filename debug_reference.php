<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;
use App\Models\InstrumentRental;

echo "=== Testing Reference Code Validation ===\n\n";

// Test with a few different codes
$testCodes = ['1234', '5678', '9999', '0000'];

foreach ($testCodes as $testCode) {
    echo "Testing code: $testCode\n";
    
    $existsInBookings = Booking::where('reference_code', $testCode)->exists();
    $existsInRentals = InstrumentRental::where('four_digit_code', $testCode)->exists();
    
    echo "  - Exists in bookings: " . ($existsInBookings ? 'YES' : 'NO') . "\n";
    echo "  - Exists in rentals: " . ($existsInRentals ? 'YES' : 'NO') . "\n";
    echo "  - Combined result: " . (($existsInBookings || $existsInRentals) ? 'EXISTS' : 'AVAILABLE') . "\n\n";
}

// Check what's actually in the database
echo "=== Current Reference Codes in Database ===\n\n";

echo "Bookings table:\n";
$bookingCodes = Booking::whereNotNull('reference_code')->pluck('reference_code', 'id');
foreach ($bookingCodes as $id => $code) {
    echo "  ID: $id, Code: $code\n";
}
if ($bookingCodes->isEmpty()) {
    echo "  No reference codes found in bookings table\n";
}

echo "\nInstrument Rentals table:\n";
$rentalCodes = InstrumentRental::whereNotNull('four_digit_code')->pluck('four_digit_code', 'id');
foreach ($rentalCodes as $id => $code) {
    echo "  ID: $id, Code: $code\n";
}
if ($rentalCodes->isEmpty()) {
    echo "  No reference codes found in instrument rentals table\n";
}

echo "\n=== Test Complete ===\n";