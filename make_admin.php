<?php

/**
 * Make Admin Script
 * 
 * Quick script to grant admin privileges to a user.
 * Usage: php make_admin.php user@example.com
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get email from command line argument
$email = $argv[1] ?? null;

if (!$email) {
    echo "❌ Usage: php make_admin.php user@example.com\n";
    exit(1);
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "❌ Invalid email format: {$email}\n";
    exit(1);
}

try {
    // Find user by email
    $user = \App\Models\User::where('email', $email)->first();
    
    if (!$user) {
        echo "❌ User not found with email: {$email}\n";
        echo "💡 Make sure the user has logged in at least once via Google OAuth.\n";
        exit(1);
    }
    
    // Check if already admin
    if ($user->is_admin) {
        echo "ℹ️  User {$user->name} ({$email}) is already an admin.\n";
        exit(0);
    }
    
    // Make admin
    $user->update(['is_admin' => true]);
    
    echo "✅ Success! User {$user->name} ({$email}) is now an admin.\n";
    echo "\n";
    echo "🎯 Next steps:\n";
    echo "   1. User can now access: /admin/dashboard\n";
    echo "   2. Connect Google Calendar: /admin/calendar\n";
    echo "   3. Grant admin access to other users via the admin panel\n";
    echo "\n";
    echo "🔗 Admin Dashboard: " . config('app.url') . "/admin/dashboard\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
} 