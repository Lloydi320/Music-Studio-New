<?php

/**
 * Create Admin User Script
 * 
 * Creates a new user and grants admin privileges.
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Check if user already exists
    $existingUser = \App\Models\User::where('email', 'chatgptdot4o@gmail.com')->first();
    
    if ($existingUser) {
        // User exists, just make them admin
        $existingUser->update(['is_admin' => true]);
        echo "✅ User {$existingUser->name} ({$existingUser->email}) is now an admin.\n";
    } else {
        // Create new user with admin privileges
        $user = \App\Models\User::create([
            'name' => 'ChatGPT User',
            'email' => 'chatgptdot4o@gmail.com',
            'is_admin' => true,
            'email_verified_at' => now(),
            'password' => bcrypt('temporary_password_123') // Will be overridden by Google OAuth
        ]);
        
        echo "✅ New admin user created successfully!\n";
        echo "   Name: {$user->name}\n";
        echo "   Email: {$user->email}\n";
        echo "   Admin: " . ($user->is_admin ? 'Yes' : 'No') . "\n";
    }
    
    echo "\n🎯 Next steps:\n";
    echo "   1. User can now access: /admin/dashboard\n";
    echo "   2. Connect Google Calendar: /admin/calendar\n";
    echo "   3. Grant admin access to other users via the admin panel\n";
    echo "\n🔗 Admin Dashboard: " . config('app.url') . "/admin/dashboard\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}