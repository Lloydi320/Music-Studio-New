<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\InstrumentRental;

echo "All Instrument Rentals:\n";
$rentals = InstrumentRental::all();
foreach ($rentals as $rental) {
    echo "ID: {$rental->id}, Type: {$rental->instrument_type}, Start: {$rental->rental_start_date}, End: {$rental->rental_end_date}, Status: {$rental->status}\n";
}

echo "\nFull Package and Drums rentals (pending/confirmed):\n";
$relevantRentals = InstrumentRental::whereIn('status', ['pending', 'confirmed'])
    ->where(function($query) {
        $query->where('instrument_type', 'drums')
              ->orWhere('instrument_type', 'Full Package');
    })
    ->get();

foreach ($relevantRentals as $rental) {
    echo "ID: {$rental->id}, Type: {$rental->instrument_type}, Start: {$rental->rental_start_date}, End: {$rental->rental_end_date}, Status: {$rental->status}\n";
}

echo "\nChecking date range generation:\n";
foreach ($relevantRentals as $rental) {
    $start = new DateTime($rental->rental_start_date);
    $end = new DateTime($rental->rental_end_date);
    $dates = [];
    
    while ($start <= $end) {
        $dates[] = $start->format('Y-m-d');
        $start->add(new DateInterval('P1D'));
    }
    
    echo "Rental ID {$rental->id} ({$rental->instrument_type}): " . implode(', ', $dates) . "\n";
}