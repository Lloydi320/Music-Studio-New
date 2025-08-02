<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Booking;
use App\Models\User;
use App\Services\GoogleCalendarService;
use Google\Service\Calendar;
use Carbon\Carbon;

class GoogleCalendarWebhookController extends Controller
{
    protected $calendarService;

    public function __construct()
    {
        $this->calendarService = app(GoogleCalendarService::class);
    }

    /**
     * Handle Google Calendar webhook notifications
     */
    public function handleWebhook(Request $request)
    {
        // Verify the webhook is from Google
        $channelId = $request->header('X-Goog-Channel-ID');
        $resourceId = $request->header('X-Goog-Resource-ID');
        $resourceState = $request->header('X-Goog-Resource-State');
        $resourceUri = $request->header('X-Goog-Resource-URI');
        
        Log::info('Google Calendar webhook received', [
            'channel_id' => $channelId,
            'resource_id' => $resourceId,
            'resource_state' => $resourceState,
            'resource_uri' => $resourceUri,
            'headers' => $request->headers->all()
        ]);

        // Only process sync events (when calendar changes)
        if ($resourceState !== 'sync') {
            return response('OK', 200);
        }

        try {
            // Find the admin user associated with this calendar
            $admin = $this->findAdminByCalendarId($resourceUri);
            
            if (!$admin) {
                Log::warning('No admin found for calendar webhook', ['resource_uri' => $resourceUri]);
                return response('OK', 200);
            }

            // Check for deleted events
            $this->checkForDeletedEvents($admin);
            
        } catch (\Exception $e) {
            Log::error('Error processing Google Calendar webhook: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all()
            ]);
        }

        return response('OK', 200);
    }

    /**
     * Find admin user by calendar resource URI
     */
    private function findAdminByCalendarId($resourceUri)
    {
        // Extract calendar ID from resource URI
        // Format: https://www.googleapis.com/calendar/v3/calendars/{calendarId}/events
        preg_match('/calendars\/([^\/]+)\/events/', $resourceUri, $matches);
        
        if (!isset($matches[1])) {
            return null;
        }
        
        $calendarId = urldecode($matches[1]);
        
        return User::where('is_admin', true)
                  ->where('google_calendar_id', $calendarId)
                  ->first();
    }

    /**
     * Check for deleted studio booking events
     */
    private function checkForDeletedEvents(User $admin)
    {
        // Get all bookings that have Google Calendar events
        $bookingsWithEvents = Booking::whereNotNull('google_event_id')
                                   ->where('status', 'confirmed')
                                   ->get();

        foreach ($bookingsWithEvents as $booking) {
            try {
                // Try to fetch the event from Google Calendar
                $this->calendarService->setTokenForUser($admin);
                $service = new Calendar($this->calendarService->getClient());
                
                $event = $service->events->get($admin->google_calendar_id, $booking->google_event_id);
                
                // If event status is 'cancelled', delete the booking
                if ($event->getStatus() === 'cancelled') {
                    $this->deleteBookingFromSystem($booking, 'Event cancelled in Google Calendar');
                }
                
            } catch (\Google\Service\Exception $e) {
                // If event not found (404), it was deleted
                if ($e->getCode() === 404) {
                    $this->deleteBookingFromSystem($booking, 'Event deleted from Google Calendar');
                }
            } catch (\Exception $e) {
                Log::warning('Error checking event status', [
                    'booking_id' => $booking->id,
                    'google_event_id' => $booking->google_event_id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Delete booking from system when Google Calendar event is deleted
     */
    private function deleteBookingFromSystem(Booking $booking, $reason)
    {
        Log::info('Deleting booking due to Google Calendar deletion', [
            'booking_id' => $booking->id,
            'booking_reference' => $booking->reference,
            'user_email' => $booking->user->email,
            'reason' => $reason
        ]);

        // Update booking status to cancelled instead of hard delete
        $booking->update([
            'status' => 'cancelled',
            'google_event_id' => null,
            'cancellation_reason' => $reason,
            'cancelled_at' => now()
        ]);

        // Optionally send notification to user about cancellation
        // You can implement email notification here if needed
    }
}