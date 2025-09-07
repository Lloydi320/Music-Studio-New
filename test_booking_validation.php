<?php

// Test booking form validation with existing reference code
require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "=== Testing Booking Form Validation ===\n\n";

// Test 1: Check if reference code 2222 exists (should exist)
echo "Test 1: Checking existing reference code 2222\n";
$existsInBookings = \App\Models\Booking::where('reference_code', '2222')->exists();
$existsInRentals = \App\Models\InstrumentRental::where('four_digit_code', '2222')->exists();
$exists = $existsInBookings || $existsInRentals;

echo "  - Exists in bookings: " . ($existsInBookings ? 'YES' : 'NO') . "\n";
echo "  - Exists in rentals: " . ($existsInRentals ? 'YES' : 'NO') . "\n";
echo "  - Should block submission: " . ($exists ? 'YES' : 'NO') . "\n\n";

// Test 2: Check if reference code 1234 exists (should not exist)
echo "Test 2: Checking new reference code 1234\n";
$existsInBookings = \App\Models\Booking::where('reference_code', '1234')->exists();
$existsInRentals = \App\Models\InstrumentRental::where('four_digit_code', '1234')->exists();
$exists = $existsInBookings || $existsInRentals;

echo "  - Exists in bookings: " . ($existsInBookings ? 'YES' : 'NO') . "\n";
echo "  - Exists in rentals: " . ($existsInRentals ? 'YES' : 'NO') . "\n";
echo "  - Should allow submission: " . ($exists ? 'NO' : 'YES') . "\n\n";

// Test 3: Simulate API call for reference code validation
echo "Test 3: Simulating API validation calls\n";

$testCodes = ['2222', '1234', '9999'];

foreach ($testCodes as $code) {
    $existsInBookings = \App\Models\Booking::where('reference_code', $code)->exists();
    $existsInRentals = \App\Models\InstrumentRental::where('four_digit_code', $code)->exists();
    $exists = $existsInBookings || $existsInRentals;
    
    $response = [
        'exists' => $exists,
        'available' => !$exists
    ];
    
    echo "  Code $code: " . json_encode($response) . "\n";
}

echo "\n=== Test Complete ===\n";
echo "\nExpected behavior:\n";
echo "- Code 2222: Should show validation error and block form submission\n";
echo "- Code 1234: Should show validation success and allow form submission\n";
echo "- Code 9999: Should show validation success and allow form submission\n";