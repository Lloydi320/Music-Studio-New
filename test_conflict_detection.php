<?php

require_once 'vendor/autoload.php';

// Load Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;
use App\Models\InstrumentRental;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Controllers\BookingController;

echo "Testing Conflict Detection in Reschedule\n";
echo "========================================\n\n";

// Test 1: Create two bookings with same time slot to test conflict detection
echo "1. Creating two bookings with potential conflict...\n";
$timestamp = time();
$date = date('Y-m-d', strtotime('+1 day')); // Tomorrow's date
$timeSlot = '10:00AM - 11:00AM';

// Create first booking
$booking1 = Booking::create([
    'user_id' => 1,
    'date' => $date,
    'time_slot' => $timeSlot,
    'duration' => 1,
    'price' => 250.00,
    'total_amount' => 250.00,
    'service_type' => 'studio_rental',
    'band_name' => 'Test Band 1',
    'email' => 'test1@example.com',
    'contact_number' => '1234567890',
    'reference_code' => substr($timestamp, -4),
    'status' => 'confirmed',
]);

echo "Created first booking with ID: {$booking1->id} and time slot: {$timeSlot}\n";

// Create second booking with different time slot initially
$booking2 = Booking::create([
    'user_id' => 1,
    'date' => $date,
    'time_slot' => '11:00AM - 12:00PM',
    'duration' => 1,
    'price' => 250.00,
    'total_amount' => 250.00,
    'service_type' => 'studio_rental',
    'band_name' => 'Test Band 2',
    'email' => 'test2@example.com',
    'contact_number' => '1234567891',
    'reference_code' => substr($timestamp + 1, -4),
    'status' => 'confirmed',
]);

echo "Created second booking with ID: {$booking2->id} and time slot: 11:00AM - 12:00PM\n\n";

// Test 2: Try to reschedule second booking to conflict with first booking
echo "2. Testing conflict detection when rescheduling...\n";

$controller = new BookingController();

// Create a mock request to reschedule booking2 to the same time as booking1
$request = new Request([
    'booking_type' => 'studio_rental',
    'reference_number' => $booking2->reference_code,
    'new_date' => $date,
    'new_time_slot' => $timeSlot, // Same as booking1
    'duration' => 1
]);

try {
    $response = $controller->rescheduleRequest($request);
    $responseData = json_decode($response->getContent(), true);
    
    if (isset($responseData['error']) && strpos($responseData['error'], 'already booked') !== false) {
        echo "✅ Conflict detection working: {$responseData['error']}\n";
    } else {
        echo "❌ Conflict detection failed - reschedule was allowed\n";
        echo "Response: " . $response->getContent() . "\n";
    }
} catch (Exception $e) {
    echo "Error testing conflict detection: " . $e->getMessage() . "\n";
}

echo "\n";

// Test 3: Try to reschedule to a free time slot (should work)
echo "3. Testing reschedule to free time slot...\n";

$freeTimeSlot = '02:00PM - 03:00PM';
$request2 = new Request([
    'booking_type' => 'studio_rental',
    'reference_number' => $booking2->reference_code,
    'new_date' => $date,
    'new_time_slot' => $freeTimeSlot,
    'duration' => 1
]);

try {
    $response = $controller->rescheduleRequest($request2);
    $responseData = json_decode($response->getContent(), true);
    
    if (isset($responseData['success']) && $responseData['success']) {
        echo "✅ Reschedule to free slot successful: {$responseData['message']}\n";
    } else {
        echo "❌ Reschedule to free slot failed\n";
        echo "Response: " . $response->getContent() . "\n";
    }
} catch (Exception $e) {
    echo "Error testing free slot reschedule: " . $e->getMessage() . "\n";
}

echo "\n";
echo "Conflict detection test completed!\n";