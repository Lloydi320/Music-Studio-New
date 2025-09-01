<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Validator;

echo "=== Testing Reference Code Validation ===\n\n";

// Check existing reference codes in database
echo "Existing reference codes in database:\n";
$existingCodes = Booking::whereNotNull('reference_code')->pluck('reference_code', 'id');
foreach ($existingCodes as $id => $code) {
    echo "ID: $id, Reference Code: $code\n";
}
echo "\n";

// Test validation rules
echo "Testing validation rules:\n";

// Test case 1: Duplicate reference code
if ($existingCodes->isNotEmpty()) {
    $duplicateCode = $existingCodes->first();
    echo "Testing duplicate reference code: $duplicateCode\n";
    
    $validator = Validator::make([
        'reference_code' => $duplicateCode,
        'date' => '2025-12-25',
        'time_slot' => '10:00 AM - 11:00 AM',
        'duration' => 1
    ], [
        'reference_code' => 'nullable|string|size:4|unique:bookings,reference_code',
        'date' => 'required|date',
        'time_slot' => 'required|string',
        'duration' => 'required|integer|min:1|max:8'
    ]);
    
    if ($validator->fails()) {
        echo "✓ Validation correctly failed for duplicate reference code\n";
        echo "Errors: " . json_encode($validator->errors()->all()) . "\n";
    } else {
        echo "✗ Validation should have failed but didn't!\n";
    }
}

// Test case 2: New reference code
echo "\nTesting new reference code: 9999\n";
$validator = Validator::make([
    'reference_code' => '9999',
    'date' => '2025-12-25',
    'time_slot' => '10:00 AM - 11:00 AM',
    'duration' => 1
], [
    'reference_code' => 'nullable|string|size:4|unique:bookings,reference_code',
    'date' => 'required|date',
    'time_slot' => 'required|string',
    'duration' => 'required|integer|min:1|max:8'
]);

if ($validator->passes()) {
    echo "✓ Validation correctly passed for new reference code\n";
} else {
    echo "✗ Validation should have passed but didn't!\n";
    echo "Errors: " . json_encode($validator->errors()->all()) . "\n";
}

// Test manual check logic
echo "\nTesting manual reference code check:\n";
if ($existingCodes->isNotEmpty()) {
    $duplicateCode = $existingCodes->first();
    $existingBooking = Booking::where('reference_code', $duplicateCode)->first();
    if ($existingBooking) {
        echo "✓ Manual check correctly found existing booking with reference code: $duplicateCode\n";
        echo "Booking ID: {$existingBooking->id}, Status: {$existingBooking->status}\n";
    } else {
        echo "✗ Manual check failed to find existing booking\n";
    }
}

echo "\n=== Test Complete ===\n";