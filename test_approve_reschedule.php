<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\RescheduleRequest;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

echo "🧪 Testing Reschedule Request Approval\n";
echo "=====================================\n\n";

// Find the pending reschedule request
$rescheduleRequest = RescheduleRequest::where('status', 'pending')->first();

if (!$rescheduleRequest) {
    echo "❌ No pending reschedule request found!\n";
    exit(1);
}

echo "📋 Found reschedule request:\n";
echo "  - ID: {$rescheduleRequest->id}\n";
echo "  - Type: {$rescheduleRequest->resource_type}\n";
echo "  - Resource ID: {$rescheduleRequest->resource_id}\n";
echo "  - Status: {$rescheduleRequest->status}\n";
echo "  - New Date: {$rescheduleRequest->new_date}\n";
echo "  - New Time: {$rescheduleRequest->new_time_slot}\n\n";

// Skip admin authentication for this test
echo "⚠️  Skipping authentication for direct testing\n\n";

try {
    echo "🔄 Attempting to approve reschedule request...\n";
    
    // Create controller instance
    $controller = new AdminController();
    
    // Call the approve method directly
    $response = $controller->approveRescheduleRequest($rescheduleRequest->id);
    
    echo "✅ Approval method executed successfully!\n";
    echo "📄 Response type: " . get_class($response) . "\n";
    
    // Check if it's a redirect response
    if (method_exists($response, 'getTargetUrl')) {
        echo "🔗 Redirect URL: " . $response->getTargetUrl() . "\n";
    }
    
    // Check session for flash messages
    if (session()->has('success')) {
        echo "✅ Success message: " . session('success') . "\n";
    }
    
    if (session()->has('error')) {
        echo "❌ Error message: " . session('error') . "\n";
    }
    
    // Check the updated status
    $updatedRequest = RescheduleRequest::find($rescheduleRequest->id);
    if ($updatedRequest) {
        echo "📊 Updated request status: {$updatedRequest->status}\n";
    } else {
        echo "❌ Reschedule request not found after approval!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error during approval: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "📋 Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n🏁 Test completed!\n";