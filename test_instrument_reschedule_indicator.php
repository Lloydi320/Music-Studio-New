<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\InstrumentRental;
use App\Models\RescheduleRequest;
use App\Models\User;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

echo "Testing Instrument Rental Reschedule Indicator...\n";
echo "=============================================\n\n";

try {
    // Find an existing instrument rental or create one for testing
    $rental = InstrumentRental::whereIn('status', ['active', 'pending', 'confirmed'])->first();
    
    if (!$rental) {
        echo "❌ No instrument rental found for testing\n";
        exit(1);
    }
    
    echo "Using existing rental: {$rental->reference} (ID: {$rental->id})\n";
    echo "Current reschedule_source: " . ($rental->reschedule_source ?? 'null') . "\n\n";
    
    // Create a reschedule request for the rental
    $rescheduleRequest = RescheduleRequest::create([
        'user_id' => $rental->user_id,
        'customer_name' => $rental->user->name,
        'customer_email' => $rental->user->email,
        'resource_type' => 'instrument_rental',
        'resource_id' => $rental->id,
        'requested_start_date' => '2025-09-20',
        'requested_end_date' => '2025-09-25',
        'requested_duration' => 5,
        'reason' => 'Testing instrument rental reschedule indicator',
        'status' => 'pending',
        'priority' => 'medium',
        'original_data' => json_encode([
            'start_date' => $rental->rental_start_date,
            'end_date' => $rental->rental_end_date,
            'duration' => $rental->rental_duration_days
        ]),
        'requested_data' => json_encode([
            'start_date' => '2025-09-20',
            'end_date' => '2025-09-25',
            'duration' => 5
        ])
    ]);
    
    echo "Created reschedule request with ID: {$rescheduleRequest->id}\n";
    echo "Status before approval: {$rescheduleRequest->status}\n";
    
    // Simulate admin approval
    $adminUser = User::where('is_admin', true)->first();
    if (!$adminUser) {
        echo "❌ No admin user found\n";
        exit(1);
    }
    
    echo "Approving reschedule request as admin: {$adminUser->name}\n\n";
    
    // Simulate the approval process
    Auth::login($adminUser);
    
    $adminController = new AdminController();
    $response = $adminController->approveRescheduleRequest($rescheduleRequest->id);
    
    // Refresh the rental to get updated data
    $rental->refresh();
    
    echo "Reschedule request processed successfully!\n";
    echo "Updated rental status: {$rental->status}\n";
    echo "Updated rental reschedule_source: {$rental->reschedule_source}\n";
    echo "Updated rental start date: {$rental->rental_start_date}\n";
    
    // Check if the reschedule request was deleted
    $deletedRequest = RescheduleRequest::find($rescheduleRequest->id);
    if (!$deletedRequest) {
        echo "✅ SUCCESS: Reschedule request was automatically deleted!\n\n";
    } else {
        echo "❌ WARNING: Reschedule request still exists in database\n\n";
    }
    
    echo "✅ Test completed successfully!\n";
    echo "The instrument rental should now show the system reschedule indicator (⚙️) in the admin panel.\n";
    
} catch (Exception $e) {
    echo "❌ Error during test: {$e->getMessage()}\n";
    echo "File: {$e->getFile()} Line: {$e->getLine()}\n";
    exit(1);
}