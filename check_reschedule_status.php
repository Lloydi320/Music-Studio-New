<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\InstrumentRental;
use App\Models\RescheduleRequest;

echo "🔍 Checking Reschedule Status\n";
echo "============================\n\n";

// Check instrument rentals with reschedule indicators
$rentalsWithReschedule = InstrumentRental::whereNotNull('reschedule_source')->get();
echo "📊 Rentals with reschedule indicators: {$rentalsWithReschedule->count()}\n";

foreach ($rentalsWithReschedule as $rental) {
    echo "  - Rental: {$rental->reference} - Source: {$rental->reschedule_source} - Status: {$rental->status}\n";
}

// Check pending reschedule requests
$pendingRequests = RescheduleRequest::where('status', 'pending')->get();
echo "\n📋 Pending reschedule requests: {$pendingRequests->count()}\n";

foreach ($pendingRequests as $request) {
    echo "  - Request ID: {$request->id} - Type: {$request->resource_type} - Resource ID: {$request->resource_id}\n";
}

// Check all instrument rentals
$allRentals = InstrumentRental::all();
echo "\n📦 Total instrument rentals: {$allRentals->count()}\n";

foreach ($allRentals->take(5) as $rental) {
    $rescheduleSource = $rental->reschedule_source ? $rental->reschedule_source : 'none';
    echo "  - {$rental->reference}: Status={$rental->status}, Reschedule={$rescheduleSource}\n";
}

echo "\n✅ Status check completed!\n";