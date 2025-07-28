<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "=== Checking Users and Google Calendar Status ===\n";

try {
    $users = User::all(['id', 'email', 'name', 'is_admin', 'google_calendar_token', 'google_calendar_id']);
    
    echo "Total users found: " . $users->count() . "\n\n";
    
    foreach ($users as $user) {
        echo "User ID: {$user->id}\n";
        echo "Name: {$user->name}\n";
        echo "Email: {$user->email}\n";
        echo "Is Admin: " . ($user->is_admin ? 'Yes' : 'No') . "\n";
        echo "Has Google Token: " . (!empty($user->google_calendar_token) ? 'Yes' : 'No') . "\n";
        echo "Google Calendar ID: " . ($user->google_calendar_id ?: 'Not set') . "\n";
        echo "---\n";
    }
    
    // Check for admin users specifically
    $admins = User::where('is_admin', true)->get();
    echo "\nAdmin users: " . $admins->count() . "\n";
    
    foreach ($admins as $admin) {
        echo "Admin: {$admin->name} ({$admin->email})\n";
        
        if (!empty($admin->google_calendar_token)) {
            echo "  ✅ Has Google Calendar token\n";
            
            if (!empty($admin->google_calendar_id)) {
                echo "  ✅ Has Google Calendar ID: {$admin->google_calendar_id}\n";
            } else {
                echo "  ⚠️  Missing Google Calendar ID\n";
            }
        } else {
            echo "  ❌ No Google Calendar token\n";
        }
    }
    
    // Check bookings
    $totalBookings = \App\Models\Booking::count();
    $confirmedBookings = \App\Models\Booking::where('status', 'confirmed')->count();
    $syncedBookings = \App\Models\Booking::where('status', 'confirmed')->whereNotNull('google_event_id')->count();
    
    echo "\n=== Booking Status ===\n";
    echo "Total bookings: {$totalBookings}\n";
    echo "Confirmed bookings: {$confirmedBookings}\n";
    echo "Synced to Google Calendar: {$syncedBookings}\n";
    echo "Unsynced confirmed bookings: " . ($confirmedBookings - $syncedBookings) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== Check Complete ===\n";