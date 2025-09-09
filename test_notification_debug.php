<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Notification Debug Test ===\n";

// Check admin users
$adminUsers = App\Models\User::where('is_admin', true)->get();
echo "Admin users found: " . $adminUsers->count() . "\n";

foreach ($adminUsers as $admin) {
    echo "Admin: {$admin->name} ({$admin->email})\n";
}

// Check recent reschedule requests
$recentRequests = App\Models\RescheduleRequest::orderBy('created_at', 'desc')->take(5)->get();
echo "\nRecent reschedule requests: " . $recentRequests->count() . "\n";

foreach ($recentRequests as $request) {
    echo "Request: {$request->reference} - {$request->resource_type} - {$request->status}\n";
}

// Check mail configuration
echo "\nMail driver: " . config('mail.default') . "\n";
echo "Mail from: " . config('mail.from.address') . "\n";

echo "\n=== End Debug Test ===\n";