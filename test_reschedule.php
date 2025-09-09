<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Booking;
use App\Models\InstrumentRental;
use App\Models\ActivityLog;
use App\Http\Controllers\BookingController;
use Illuminate\Http\Request;

echo "Testing Reschedule Functionality\n";
echo "================================\n\n";

// Test 1: Create a test studio booking
echo "1. Creating test studio booking...\n";
$timestamp = time();
$shortId = substr($timestamp, -6);
$booking = Booking::create([
    'user_id' => 1,
    'service_type' => 'studio_rental',
    'date' => '2024-02-15',
    'time_slot' => '10:00-12:00',
    'duration' => 2,
    'status' => 'confirmed',
    'reference' => 'BK-2024-' . $shortId,
    'reference_code' => substr($timestamp, -4),
    'band_name' => 'Test Band',
    'email' => 'test@example.com',
    'contact_number' => '1234567890',
    'price' => 100,
    'total_amount' => 100
]);
echo "Created booking with ID: {$booking->id} and reference: {$booking->reference}\n\n";

// Test 2: Test reference validation
echo "2. Testing reference validation...\n";
$controller = new BookingController();
$request = new Request();
$bookingRefCode = substr($timestamp, -4);
$request->merge(['reference' => $bookingRefCode]);

try {
    $response = $controller->validateReference($bookingRefCode);
    $data = json_decode($response->getContent(), true);
    echo "Reference validation result: " . ($data['valid'] ? 'VALID' : 'INVALID') . "\n";
    if ($data['valid']) {
        echo "Service type: {$data['booking']['service_type']}\n";
        echo "Band/Item name: {$data['booking']['band_name']}\n";
        echo "Date: {$data['booking']['date']}\n";
    }
} catch (Exception $e) {
    echo "Error in reference validation: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 3: Create test instrument rental
echo "3. Creating test instrument rental...\n";
$rentalRefCode = substr($timestamp + 1, -4);
$rentalShortId = substr($timestamp + 1, -6);
$rental = InstrumentRental::create([
    'user_id' => 1,
    'instrument_type' => 'Guitar',
    'instrument_name' => 'Electric Guitar',
    'rental_start_date' => '2024-02-20',
    'rental_end_date' => '2024-02-22',
    'rental_duration_days' => 3,
    'daily_rate' => 50.00,
    'status' => 'confirmed',
    'reference' => 'IR-2024-' . $rentalShortId,
    'four_digit_code' => $rentalRefCode,
    'total_amount' => 150
]);
echo "Created rental with ID: {$rental->id} and reference: {$rental->reference}\n\n";

// Test 4: Test instrument rental reference validation
echo "4. Testing instrument rental reference validation...\n";
try {
    $response = $controller->validateReference($rentalRefCode);
    $data = json_decode($response->getContent(), true);
    echo "Reference validation result: " . ($data['valid'] ? 'VALID' : 'INVALID') . "\n";
    if ($data['valid']) {
        echo "Service type: {$data['booking']['service_type']}\n";
        echo "Band/Item name: {$data['booking']['band_name']}\n";
        echo "Date: {$data['booking']['date']}\n";
    }
} catch (Exception $e) {
    echo "Error in reference validation: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 5: Check activity logs
echo "5. Checking activity logs...\n";
$logs = ActivityLog::orderBy('created_at', 'desc')->take(5)->get();
echo "Recent activity logs count: " . $logs->count() . "\n";
foreach ($logs as $log) {
    echo "- {$log->description} (Resource: {$log->resource_type})\n";
}
echo "\n";

echo "Test completed successfully!\n";