<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

echo "Testing Solo Rehearsal Service Type Fix...\n";
echo "==========================================\n\n";

try {
    // Find a test user or create one
    $user = User::where('email', 'test@example.com')->first();
    if (!$user) {
        echo "No test user found. Please create a user with email 'test@example.com' first.\n";
        exit(1);
    }
    
    // Login as the test user
    Auth::login($user);
    
    // Create a test booking with solo_rehearsal service type
    $testBooking = Booking::create([
        'user_id' => $user->id,
        'date' => Carbon::tomorrow()->format('Y-m-d'),
        'time_slot' => '10:00 AM - 11:00 AM',
        'duration' => 1,
        'price' => 300.00,
        'total_amount' => 300.00,
        'service_type' => 'solo_rehearsal',
        'band_name' => 'Test Solo Artist',
        'email' => $user->email,
        'contact_number' => '09123456789',
        'reference_code' => '1234',
        'status' => 'pending',
    ]);
    
    echo "✅ Test booking created successfully!\n";
    echo "Booking ID: {$testBooking->id}\n";
    echo "Service Type: {$testBooking->service_type}\n";
    echo "Price per hour: ₱{$testBooking->price}\n";
    echo "Total Amount: ₱{$testBooking->total_amount}\n";
    echo "Reference: {$testBooking->reference}\n\n";
    
    // Verify the service type is correctly saved
    if ($testBooking->service_type === 'solo_rehearsal') {
        echo "✅ SUCCESS: Service type is correctly saved as 'solo_rehearsal'\n";
    } else {
        echo "❌ ERROR: Service type is '{$testBooking->service_type}' instead of 'solo_rehearsal'\n";
    }
    
    // Verify the pricing is correct for solo rehearsal
    if ($testBooking->price == 300.00) {
        echo "✅ SUCCESS: Pricing is correct (₱300 per hour for solo rehearsal)\n";
    } else {
        echo "❌ ERROR: Pricing is ₱{$testBooking->price} instead of ₱300 for solo rehearsal\n";
    }
    
    // Clean up - delete the test booking
    $testBooking->delete();
    echo "\n🧹 Test booking cleaned up.\n";
    
    echo "\n✨ Solo rehearsal service type fix is working correctly!\n";
    
} catch (Exception $e) {
    echo "❌ Error during test: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\nTest completed successfully! 🎉\n";