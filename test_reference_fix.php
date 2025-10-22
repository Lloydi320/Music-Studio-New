<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\InstrumentRental;
use App\Http\Controllers\BookingController;
use Illuminate\Http\Request;

echo "Testing Reference Number Detection Fix\n";
echo "=====================================\n\n";

// Test 1: Check if we can find an instrument rental by payment_reference
echo "Test 1: Finding instrument rental by payment_reference\n";
$testPaymentRef = '1234567890123'; // 13-digit test reference

// Create a test instrument rental with payment_reference
$testRental = InstrumentRental::create([
    'user_id' => 1,
    'instrument_type' => 'Guitar',
    'instrument_name' => 'Test Guitar',
    'rental_start_date' => now()->addDays(1),
    'rental_end_date' => now()->addDays(3),
    'rental_duration_days' => 2,
    'daily_rate' => 100.00,
    'total_amount' => 200.00,
    'payment_reference' => $testPaymentRef,
    'four_digit_code' => '1234',
    'reference' => 'TEST-' . now()->format('YmdHis'),
    'status' => 'confirmed'
]);

echo "Created test rental with payment_reference: {$testPaymentRef}\n";

// Test the validateReference method
$controller = new BookingController();

try {
    $response = $controller->validateReference($testPaymentRef);
    $responseData = $response->getData(true);
    
    if ($response->getStatusCode() === 200 && isset($responseData['booking'])) {
        echo "✅ SUCCESS: validateReference found the rental by payment_reference\n";
        echo "   Found rental ID: {$responseData['booking']['id']}\n";
        echo "   Service type: {$responseData['booking']['service_type']}\n";
    } else {
        echo "❌ FAILED: validateReference did not find the rental\n";
        echo "   Response: " . json_encode($responseData) . "\n";
    }
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 2: Check rescheduleRequest method
echo "Test 2: Testing rescheduleRequest with payment_reference\n";

$rescheduleRequest = new Request([
    'booking_type' => 'instrument_rental',
    'reference_number' => $testPaymentRef,
    'start_date' => now()->addDays(5)->format('Y-m-d'),
    'end_date' => now()->addDays(7)->format('Y-m-d'),
    'reason' => 'Testing reference fix'
]);

try {
    $response = $controller->rescheduleRequest($rescheduleRequest);
    $responseData = $response->getData(true);
    
    if ($response->getStatusCode() === 200) {
        echo "✅ SUCCESS: rescheduleRequest found the rental by payment_reference\n";
        echo "   Response: " . json_encode($responseData) . "\n";
    } else {
        echo "❌ FAILED: rescheduleRequest did not work properly\n";
        echo "   Status: {$response->getStatusCode()}\n";
        echo "   Response: " . json_encode($responseData) . "\n";
    }
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n";

// Clean up test data
echo "Cleaning up test data...\n";
$testRental->delete();
echo "✅ Test rental deleted\n";

echo "\nTest completed!\n";