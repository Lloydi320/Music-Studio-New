<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Booking;

// Remove the test booking for July 27th, 2025
$deleted = Booking::where('date', '2025-07-27')
                  ->where('time_slot', '08:30 AM - 11:30 AM')
                  ->delete();

echo "Removed $deleted test booking(s) from database.\n";
echo "Your database is now clean!\n";
?> 