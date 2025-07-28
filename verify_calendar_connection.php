<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/bootstrap/app.php';

use App\Models\User;
use App\Services\GoogleCalendarService;
use Illuminate\Support\Facades\Log;

echo "=== Google Calendar Connection Verification ===\n";

// Find admin user
$admin = User::where('is_admin', true)->first();

if (!$admin) {
    echo "❌ No admin user found\n";
    exit(1);
}

echo "✅ Admin user found: {$admin->name} (ID: {$admin->id})\n";
echo "📧 Email: {$admin->email}\n";

// Check Google Calendar token
if (empty($admin->google_calendar_token)) {
    echo "❌ No Google Calendar token found\n";
    echo "💡 Please reconnect Google Calendar from /admin/calendar\n";
    exit(1);
}

echo "✅ Google Calendar token exists\n";

// Check Google Calendar ID
if (empty($admin->google_calendar_id)) {
    echo "⚠️  No Google Calendar ID found\n";
    echo "🔧 Attempting to setup calendar...\n";
    
    try {
        $calendarService = new GoogleCalendarService();
        $calendarId = $calendarService->setupStudioCalendar($admin);
        
        if ($calendarId) {
            echo "✅ Calendar setup successful! Calendar ID: {$calendarId}\n";
        } else {
            echo "❌ Calendar setup failed\n";
            exit(1);
        }
    } catch (Exception $e) {
        echo "❌ Calendar setup error: " . $e->getMessage() . "\n";
        
        if (strpos($e->getMessage(), 'PERMISSION_DENIED') !== false) {
            echo "💡 The Google Calendar API might not be properly enabled or configured\n";
            echo "💡 Please check your Google Cloud Console settings\n";
        }
        
        exit(1);
    }
} else {
    echo "✅ Google Calendar ID exists: {$admin->google_calendar_id}\n";
}

// Test API connection
echo "🔍 Testing Google Calendar API connection...\n";

try {
    $calendarService = new GoogleCalendarService();
    
    // Try to get calendar info
    $client = $calendarService->getClient($admin);
    $service = new Google_Service_Calendar($client);
    
    $calendar = $service->calendars->get($admin->google_calendar_id);
    echo "✅ API connection successful!\n";
    echo "📅 Calendar name: {$calendar->getSummary()}\n";
    echo "🌍 Calendar timezone: {$calendar->getTimeZone()}\n";
    
    // Check for existing events
    $events = $service->events->listEvents($admin->google_calendar_id, [
        'maxResults' => 10,
        'orderBy' => 'startTime',
        'singleEvents' => true,
        'timeMin' => date('c'),
    ]);
    
    $eventCount = count($events->getItems());
    echo "📊 Upcoming events in calendar: {$eventCount}\n";
    
    if ($eventCount > 0) {
        echo "📋 Recent events:\n";
        foreach (array_slice($events->getItems(), 0, 3) as $event) {
            $start = $event->start->dateTime ?: $event->start->date;
            echo "   - {$event->getSummary()} ({$start})\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ API connection failed: " . $e->getMessage() . "\n";
    
    if (strpos($e->getMessage(), 'invalid_grant') !== false) {
        echo "💡 Token may be expired. Please reconnect Google Calendar from /admin/calendar\n";
    } elseif (strpos($e->getMessage(), 'PERMISSION_DENIED') !== false) {
        echo "💡 Permission denied. Check API enablement and credentials\n";
    }
    
    exit(1);
}

// Check unsynced bookings
echo "\n🔍 Checking for unsynced bookings...\n";

$unsyncedBookings = \App\Models\Booking::where('status', 'confirmed')
    ->whereNull('google_event_id')
    ->get();

echo "📊 Unsynced confirmed bookings: {$unsyncedBookings->count()}\n";

if ($unsyncedBookings->count() > 0) {
    echo "📋 Unsynced bookings:\n";
    foreach ($unsyncedBookings as $booking) {
        echo "   - Booking #{$booking->id}: {$booking->service_type} on {$booking->booking_date} at {$booking->booking_time}\n";
    }
    
    echo "\n🔧 Would you like to sync these bookings? (This script only verifies, use sync script to actually sync)\n";
}

echo "\n✅ Verification complete!\n";