<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a test booking
use App\Models\Booking;
use App\Models\Feedback;
use App\Models\User;

echo "Testing form submissions...\n\n";

// Get or create a test user
$user = User::first();
if (!$user) {
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
        'email_verified_at' => now(),
    ]);
    echo "Created test user: {$user->name}\n";
} else {
    echo "Using existing user: {$user->name}\n";
}

// Create a test booking
try {
    $booking = Booking::create([
        'user_id' => $user->id,
        'date' => '2025-01-30',
        'time_slot' => '10:00 AM - 02:00 PM',
        'duration' => 2,
        'status' => 'confirmed',
    ]);
    echo "✓ Test booking created successfully!\n";
    echo "  - ID: {$booking->id}\n";
    echo "  - Reference: {$booking->reference}\n";
    echo "  - Date: {$booking->date}\n";
    echo "  - Time: {$booking->time_slot}\n";
} catch (Exception $e) {
    echo "✗ Failed to create booking: " . $e->getMessage() . "\n";
}

// Create a test feedback
try {
    $feedback = Feedback::create([
        'user_id' => $user->id,
        'name' => 'Test User',
        'rating' => 5,
        'comment' => 'This is a test feedback to verify the system is working.',
        'content' => 'This is a test feedback to verify the system is working.',
    ]);
    echo "✓ Test feedback created successfully!\n";
    echo "  - ID: {$feedback->id}\n";
    echo "  - Name: {$feedback->name}\n";
    echo "  - Rating: {$feedback->rating}\n";
    echo "  - Comment: {$feedback->comment}\n";
} catch (Exception $e) {
    echo "✗ Failed to create feedback: " . $e->getMessage() . "\n";
}

echo "\n=== Database Check ===\n";
echo "Total bookings in database: " . Booking::count() . "\n";
echo "Total feedback in database: " . Feedback::count() . "\n";

echo "\nTest completed! Check phpMyAdmin to see if the data appears.\n";