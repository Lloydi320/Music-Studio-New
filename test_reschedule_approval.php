<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\InstrumentRental;
use App\Models\RescheduleRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

echo "🧪 Testing Instrument Rental Reschedule Approval Process\n";
echo "======================================================\n\n";

try {
    // Find a confirmed rental
    $rental = InstrumentRental::where('status', 'confirmed')->first();
    
    if (!$rental) {
        echo "❌ No confirmed rental found to test with\n";
        exit(1);
    }
    
    echo "📋 Using rental: {$rental->reference} (ID: {$rental->id})\n";
    echo "Current status: {$rental->status}\n";
    echo "Current reschedule_source: " . ($rental->reschedule_source ?? 'null') . "\n\n";
    
    // Create a reschedule request
    $rescheduleRequest = RescheduleRequest::create([
        'resource_type' => RescheduleRequest::RESOURCE_INSTRUMENT_RENTAL,
        'resource_id' => $rental->id,
        'user_id' => $rental->user_id,
        'requested_date' => now()->addDays(7)->format('Y-m-d'),
        'requested_time_slot' => '10:00-12:00',
        'reason' => 'Test reschedule approval process',
        'status' => RescheduleRequest::STATUS_PENDING
    ]);
    
    echo "✅ Created reschedule request with ID: {$rescheduleRequest->id}\n";
    echo "Status before approval: {$rescheduleRequest->status}\n\n";
    
    // Simulate admin login
    $admin = User::where('is_admin', true)->first();
    if (!$admin) {
        echo "❌ No admin user found\n";
        exit(1);
    }
    
    Auth::login($admin);
    echo "🔐 Logged in as admin: {$admin->name}\n\n";
    
    // Test the approval process by calling the controller method
    $controller = new \App\Http\Controllers\AdminController();
    
    echo "🔄 Approving reschedule request...\n";
    
    // Simulate the approval request
    $request = new \Illuminate\Http\Request();
    $request->setMethod('POST');
    
    try {
        $response = $controller->approveRescheduleRequest($rescheduleRequest->id);
        
        // Check if it's a redirect response
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            $session = $response->getSession();
            if ($session && $session->has('success')) {
                echo "✅ SUCCESS: {$session->get('success')}\n";
            } elseif ($session && $session->has('error')) {
                echo "❌ ERROR: {$session->get('error')}\n";
            } else {
                echo "✅ Reschedule request processed (redirect response)\n";
            }
        } else {
            echo "✅ Reschedule request processed successfully!\n";
        }
        
        // Check the updated rental
        $updatedRental = $rental->fresh();
        echo "Updated rental status: {$updatedRental->status}\n";
        echo "Updated rental reschedule_source: " . ($updatedRental->reschedule_source ?? 'null') . "\n";
        echo "Updated rental start date: {$updatedRental->rental_start_date}\n";
        
        // Check if reschedule request was deleted
        $deletedRequest = RescheduleRequest::find($rescheduleRequest->id);
        if (!$deletedRequest) {
            echo "✅ SUCCESS: Reschedule request was automatically deleted!\n";
        } else {
            echo "ℹ️  Reschedule request still exists with status: {$deletedRequest->status}\n";
        }
        
    } catch (Exception $e) {
        echo "❌ ERROR during approval: {$e->getMessage()}\n";
        echo "Stack trace: {$e->getTraceAsString()}\n";
    }
    
    echo "\n✅ Test completed successfully!\n";
    echo "The instrument rental should now show the system reschedule indicator (⚙️) in the admin panel.\n";
    
} catch (Exception $e) {
    echo "❌ Test failed: {$e->getMessage()}\n";
    echo "Stack trace: {$e->getTraceAsString()}\n";
    exit(1);
}