<?php

// Simple test script to create booking and feedback entries
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Booking;
use App\Models\Feedback;

echo "Testing database operations...\n";

try {
    // Get first user or create one
    $user = User::first();
    if (!$user) {
        echo "No users found, creating test user...\n";
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@musicstudio.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);
    }
    echo "Using user: {$user->name} (ID: {$user->id})\n";

    // Create test booking
    echo "Creating test booking...\n";
    $booking = Booking::create([
        'user_id' => $user->id,
        'date' => '2025-01-30',
        'time_slot' => '10:00 AM - 12:00 PM',
        'duration' => 2,
        'status' => 'confirmed',
    ]);
    echo "✓ Booking created: ID {$booking->id}, Reference: {$booking->reference}\n";

    // Create test feedback
    echo "Creating test feedback...\n";
    $feedback = Feedback::create([
        'user_id' => $user->id,
        'name' => 'Test User',
        'rating' => 5,
        'comment' => 'Great studio! Testing the feedback system.',
        'content' => 'Great studio! Testing the feedback system.',
    ]);
    echo "✓ Feedback created: ID {$feedback->id}\n";

    // Check totals
    $bookingCount = Booking::count();
    $feedbackCount = Feedback::count();
    echo "\nDatabase totals:\n";
    echo "- Bookings: {$bookingCount}\n";
    echo "- Feedback: {$feedbackCount}\n";
    
    echo "\n✓ Test completed successfully! Check phpMyAdmin now.\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}