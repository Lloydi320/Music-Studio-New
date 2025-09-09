<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Session;

echo "🔍 Debugging Notification Issue\n";
echo "===============================\n\n";

// Check current session data
echo "📋 Current Session Data:\n";
$sessionData = Session::all();
if (empty($sessionData)) {
    echo "  - No session data found\n";
} else {
    foreach ($sessionData as $key => $value) {
        if (is_string($value) && strlen($value) < 200) {
            echo "  - {$key}: {$value}\n";
        } else {
            echo "  - {$key}: [" . gettype($value) . "]\n";
        }
    }
}

// Check for flash messages specifically
echo "\n🔔 Flash Messages:\n";
if (Session::has('success')) {
    echo "  - Success: " . Session::get('success') . "\n";
}
if (Session::has('error')) {
    echo "  - Error: " . Session::get('error') . "\n";
}
if (Session::has('warning')) {
    echo "  - Warning: " . Session::get('warning') . "\n";
}
if (Session::has('info')) {
    echo "  - Info: " . Session::get('info') . "\n";
}

// Check if there are any persistent messages
echo "\n🔄 Checking for persistent messages...\n";
$allKeys = array_keys($sessionData);
$messageKeys = array_filter($allKeys, function($key) {
    return strpos($key, 'message') !== false || 
           strpos($key, 'error') !== false || 
           strpos($key, 'success') !== false ||
           strpos($key, 'notification') !== false;
});

if (empty($messageKeys)) {
    echo "  - No message-related session keys found\n";
} else {
    foreach ($messageKeys as $key) {
        echo "  - Found key: {$key} = " . $sessionData[$key] . "\n";
    }
}

echo "\n✅ Debug completed!\n";