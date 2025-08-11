<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use App\Models\User;
use App\Models\Booking;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class GoogleCalendarService
{
    private $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.calendar_redirect'));
        $this->client->addScope(Calendar::CALENDAR);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('consent');
    }

    /**
     * Get the Google OAuth URL for calendar access
     */
    public function getAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    /**
     * Handle the OAuth callback and store tokens
     */
    public function handleCallback($code, User $user)
    {
        try {
            $token = $this->client->fetchAccessTokenWithAuthCode($code);
            
            if (isset($token['error'])) {
                throw new Exception('OAuth Error: ' . $token['error_description']);
            }

            $user->update([
                'google_calendar_token' => json_encode($token)
            ]);

            // Get or create a calendar for the studio
            $this->setupStudioCalendar($user);

            return true;
        } catch (Exception $e) {
            Log::error('Google Calendar OAuth Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Setup or get the studio calendar
     */
    private function setupStudioCalendar(User $user)
    {
        $this->setTokenForUser($user);
        $service = new Calendar($this->client);

        // Try to find existing studio calendar
        $calendarList = $service->calendarList->listCalendarList();
        $studioCalendar = null;

        foreach ($calendarList->getItems() as $calendar) {
            if (strpos($calendar->getSummary(), 'Music Studio Bookings') !== false) {
                $studioCalendar = $calendar;
                break;
            }
        }

        // Create new calendar if not found
        if (!$studioCalendar) {
            $calendar = new \Google\Service\Calendar\Calendar();
            $calendar->setSummary('Music Studio Bookings');
            $calendar->setDescription('Studio booking sessions and appointments');
            $calendar->setTimeZone(config('app.timezone', 'UTC'));

            $createdCalendar = $service->calendars->insert($calendar);
            $calendarId = $createdCalendar->getId();
        } else {
            $calendarId = $studioCalendar->getId();
        }

        $user->update(['google_calendar_id' => $calendarId]);
    }

    /**
     * Set the access token for a specific user
     */
    public function setTokenForUser(User $user)
    {
        if (!$user->google_calendar_token) {
            throw new Exception('User does not have Google Calendar access');
        }

        $token = json_decode($user->google_calendar_token, true);
        $this->client->setAccessToken($token);

        // Refresh token if expired
        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                $newToken = $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
                $user->update(['google_calendar_token' => json_encode($newToken)]);
            } else {
                throw new Exception('Token expired and no refresh token available');
            }
        }
    }

    /**
     * Create a calendar event for a booking
     */
    public function createBookingEvent(Booking $booking)
    {
        try {
            // Find admin users with calendar access
            $admins = User::where('is_admin', true)
                         ->whereNotNull('google_calendar_token')
                         ->whereNotNull('google_calendar_id')
                         ->get();

            if ($admins->isEmpty()) {
                Log::warning('No admin users with Google Calendar access found');
                return false;
            }

            foreach ($admins as $admin) {
                $this->createEventForAdmin($booking, $admin);
            }

            return true;
        } catch (Exception $e) {
            Log::error('Failed to create calendar event: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create event for specific admin
     */
    private function createEventForAdmin(Booking $booking, User $admin)
    {
        $this->setTokenForUser($admin);
        $service = new Calendar($this->client);

        try {
            // Parse booking time - time_slot format: "08:00 AM - 11:00 AM"
            $startTime = trim(explode('-', $booking->time_slot)[0]);
            
            // Create Carbon instance from booking date and start time with proper timezone
            $bookingDate = Carbon::parse($booking->date, config('app.timezone', 'Asia/Manila'));
            $dateTimeString = $bookingDate->format('Y-m-d') . ' ' . $startTime;
            $startDateTime = Carbon::createFromFormat('Y-m-d g:i A', $dateTimeString, config('app.timezone', 'Asia/Manila'));
            
            if (!$startDateTime) {
                throw new \Exception("Failed to parse datetime: {$dateTimeString}");
            }
            
            $endDateTime = $startDateTime->copy()->addHours((int) $booking->duration);
            
            Log::info('Parsed booking datetime successfully', [
                'booking_id' => $booking->id,
                'original_date' => $booking->date,
                'original_time_slot' => $booking->time_slot,
                'parsed_start' => $startDateTime->toIso8601String(),
                'parsed_end' => $endDateTime->toIso8601String()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to parse booking datetime', [
                'booking_id' => $booking->id,
                'date' => $booking->date,
                'time_slot' => $booking->time_slot,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }

        // Create event
        $serviceTypeLabel = ucwords(str_replace('_', ' ', $booking->service_type));
        $event = new Event([
            'summary' => "{$serviceTypeLabel} - {$booking->user->name}",
            'description' => "Booking Reference: {$booking->reference}\n" .
                           "Service Type: {$serviceTypeLabel}\n" .
                           "Client: {$booking->user->name}\n" .
                           "Email: {$booking->user->email}\n" .
                           "Duration: {$booking->duration} hour(s)\n" .
                           "Status: {$booking->status}",
            'start' => new EventDateTime([
                'dateTime' => $startDateTime->toIso8601String(),
                'timeZone' => config('app.timezone', 'Asia/Manila'),
            ]),
            'end' => new EventDateTime([
                'dateTime' => $endDateTime->toIso8601String(),
                'timeZone' => config('app.timezone', 'Asia/Manila'),
            ]),
            'attendees' => [
                ['email' => $booking->user->email, 'displayName' => $booking->user->name]
            ],
            'reminders' => [
                'useDefault' => false,
                'overrides' => [
                    ['method' => 'email', 'minutes' => 24 * 60], // 24 hours before
                    ['method' => 'popup', 'minutes' => 60], // 1 hour before
                ],
            ],
        ]);

        $calendarId = $admin->google_calendar_id;
        $createdEvent = $service->events->insert($calendarId, $event);

        // Store the event ID in the booking for future updates/deletions
        // Note: This will be overwritten if multiple admins exist, but we now use search for deletion
        $booking->update(['google_event_id' => $createdEvent->getId()]);

        Log::info('Calendar event created', [
            'booking_id' => $booking->id,
            'event_id' => $createdEvent->getId(),
            'admin_id' => $admin->id,
            'calendar_id' => $calendarId
        ]);
    }

    /**
     * Update a calendar event when booking is modified
     */
    public function updateBookingEvent(Booking $booking)
    {
        if (!$booking->google_event_id) {
            return $this->createBookingEvent($booking);
        }

        try {
            $admins = User::where('is_admin', true)
                         ->whereNotNull('google_calendar_token')
                         ->whereNotNull('google_calendar_id')
                         ->get();

            foreach ($admins as $admin) {
                $this->updateEventForAdmin($booking, $admin);
            }

            return true;
        } catch (Exception $e) {
            Log::error('Failed to update calendar event: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Update event for specific admin
     */
    private function updateEventForAdmin(Booking $booking, User $admin)
    {
        $this->setTokenForUser($admin);
        $service = new Calendar($this->client);

        try {
            // Parse booking time - time_slot format: "08:00 AM - 11:00 AM"
            $startTime = trim(explode('-', $booking->time_slot)[0]);
            
            // Create Carbon instance from booking date and start time with proper timezone
            $bookingDate = Carbon::parse($booking->date, config('app.timezone', 'Asia/Manila'));
            $dateTimeString = $bookingDate->format('Y-m-d') . ' ' . $startTime;
            $startDateTime = Carbon::createFromFormat('Y-m-d g:i A', $dateTimeString, config('app.timezone', 'Asia/Manila'));
            
            if (!$startDateTime) {
                throw new \Exception("Failed to parse datetime: {$dateTimeString}");
            }
            
            $endDateTime = $startDateTime->copy()->addHours((int) $booking->duration);
            
            Log::info('Parsed booking datetime for update successfully', [
                'booking_id' => $booking->id,
                'original_date' => $booking->date,
                'original_time_slot' => $booking->time_slot,
                'parsed_start' => $startDateTime->toIso8601String(),
                'parsed_end' => $endDateTime->toIso8601String()
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to parse booking datetime for update', [
                'booking_id' => $booking->id,
                'date' => $booking->date,
                'time_slot' => $booking->time_slot,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }

        // Get existing event
        $event = $service->events->get($admin->google_calendar_id, $booking->google_event_id);

        // Update event details
        $serviceTypeLabel = ucwords(str_replace('_', ' ', $booking->service_type));
        $event->setSummary("{$serviceTypeLabel} - {$booking->user->name}");
        $event->setDescription("Booking Reference: {$booking->reference}\n" .
                              "Service Type: {$serviceTypeLabel}\n" .
                              "Client: {$booking->user->name}\n" .
                              "Email: {$booking->user->email}\n" .
                              "Duration: {$booking->duration} hour(s)\n" .
                              "Status: {$booking->status}");

        $event->setStart(new EventDateTime([
            'dateTime' => $startDateTime->toIso8601String(),
            'timeZone' => config('app.timezone', 'Asia/Manila'),
        ]));

        $event->setEnd(new EventDateTime([
            'dateTime' => $endDateTime->toIso8601String(),
            'timeZone' => config('app.timezone', 'Asia/Manila'),
        ]));

        $service->events->update($admin->google_calendar_id, $booking->google_event_id, $event);
    }

    /**
     * Delete a calendar event when booking is cancelled
     */
    public function deleteBookingEvent(Booking $booking)
    {
        if (!$booking->google_event_id) {
            return true;
        }

        try {
            $admins = User::where('is_admin', true)
                         ->whereNotNull('google_calendar_token')
                         ->whereNotNull('google_calendar_id')
                         ->get();

            foreach ($admins as $admin) {
                $this->deleteEventForAdmin($booking, $admin);
            }

            $booking->update(['google_event_id' => null]);
            return true;
        } catch (Exception $e) {
            Log::error('Failed to delete calendar event: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete event for specific admin
     */
    private function deleteEventForAdmin(Booking $booking, User $admin)
    {
        $this->setTokenForUser($admin);
        $service = new Calendar($this->client);

        try {
            // First, try to find the event by searching for it
            $optParams = [
                'q' => 'Studio Session - ' . $booking->user->name,
                'timeMin' => Carbon::parse($booking->date)->startOfDay()->toIso8601String(),
                'timeMax' => Carbon::parse($booking->date)->endOfDay()->toIso8601String(),
                'singleEvents' => true
            ];
            
            $events = $service->events->listEvents($admin->google_calendar_id, $optParams);
            
            foreach ($events->getItems() as $event) {
                $description = $event->getDescription() ?: '';
                // Check if this event matches our booking by reference
                if (strpos($description, $booking->reference) !== false) {
                    $service->events->delete($admin->google_calendar_id, $event->getId());
                    Log::info('Google Calendar event deleted for admin', [
                        'booking_id' => $booking->id,
                        'event_id' => $event->getId(),
                        'admin_id' => $admin->id
                    ]);
                    return;
                }
            }
            
            // If not found by search, try the stored event ID as fallback
            if ($booking->google_event_id) {
                $service->events->delete($admin->google_calendar_id, $booking->google_event_id);
                Log::info('Google Calendar event deleted using stored ID', [
                    'booking_id' => $booking->id,
                    'event_id' => $booking->google_event_id,
                    'admin_id' => $admin->id
                ]);
            }
        } catch (Exception $e) {
            Log::warning('Could not delete event for admin ' . $admin->id . ': ' . $e->getMessage());
        }
    }

    /**
     * Fetch events from Google Calendar for a specific date range
     */
    public function getCalendarEvents(User $user, $startDate = null, $endDate = null)
    {
        try {
            if (!$user->hasGoogleCalendarAccess()) {
                return [];
            }

            $this->setTokenForUser($user);
            $service = new Calendar($this->client);

            // Default to current month if no dates provided
            if (!$startDate) {
                $startDate = Carbon::now()->startOfMonth();
            }
            if (!$endDate) {
                $endDate = Carbon::now()->endOfMonth()->addMonths(2); // Show next 3 months
            }

            $optParams = [
                'orderBy' => 'startTime',
                'singleEvents' => true,
                'timeMin' => $startDate->toIso8601String(),
                'timeMax' => $endDate->toIso8601String(),
                'maxResults' => 100
            ];

            $events = $service->events->listEvents($user->google_calendar_id, $optParams);
            $calendarEvents = [];

            foreach ($events->getItems() as $event) {
                $start = $event->getStart();
                $end = $event->getEnd();
                
                // Skip all-day events or events without proper time
                if (!$start->getDateTime() || !$end->getDateTime()) {
                    continue;
                }

                $startDateTime = Carbon::parse($start->getDateTime());
                $endDateTime = Carbon::parse($end->getDateTime());

                $calendarEvents[] = [
                    'id' => $event->getId(),
                    'title' => $event->getSummary() ?: 'Untitled Event',
                    'description' => $event->getDescription() ?: '',
                    'start' => $startDateTime,
                    'end' => $endDateTime,
                    'duration' => $endDateTime->diffInHours($startDateTime),
                    'attendees' => $this->formatAttendees($event->getAttendees()),
                    'is_studio_booking' => $this->isStudioBookingEvent($event->getSummary() ?: ''),
                    'location' => $event->getLocation() ?: '',
                    'status' => $event->getStatus() ?: 'confirmed'
                ];
            }

            return $calendarEvents;
        } catch (Exception $e) {
            Log::error('Failed to fetch calendar events: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Check if an event is a studio booking based on its title
     */
    private function isStudioBookingEvent($summary)
    {
        $studioKeywords = [
            'Recording Session',
            'Mixing Session', 
            'Mastering Session',
            'Instrument Rental',
            'Studio Session',
            'Music Studio'
        ];
        
        foreach ($studioKeywords as $keyword) {
            if (strpos($summary, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Format attendees for display
     */
    private function formatAttendees($attendees)
    {
        if (!$attendees) {
            return [];
        }

        $formatted = [];
        foreach ($attendees as $attendee) {
            $formatted[] = [
                'email' => $attendee->getEmail(),
                'name' => $attendee->getDisplayName() ?: $attendee->getEmail(),
                'status' => $attendee->getResponseStatus() ?: 'needsAction'
            ];
        }

        return $formatted;
    }

    /**
     * Get upcoming events for dashboard display
     */
    public function getUpcomingEvents(User $user, $limit = 10)
    {
        try {
            $events = $this->getCalendarEvents(
                $user, 
                Carbon::now(), 
                Carbon::now()->addWeeks(4)
            );

            // Sort by start time and limit
            usort($events, function($a, $b) {
                return $a['start']->timestamp <=> $b['start']->timestamp;
            });

            return array_slice($events, 0, $limit);
        } catch (Exception $e) {
            Log::error('Failed to fetch upcoming events: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Set up webhook for calendar changes
     */
    public function setupWebhook(User $user)
    {
        if (!$user->hasGoogleCalendarAccess()) {
            return false;
        }

        try {
            $this->setTokenForUser($user);
            $service = new Calendar($this->client);

            // Create a unique channel ID
            $channelId = 'studio-booking-' . $user->id . '-' . time();
            $webhookUrl = config('app.url') . '/webhooks/google-calendar';

            $channel = new \Google\Service\Calendar\Channel([
                'id' => $channelId,
                'type' => 'web_hook',
                'address' => $webhookUrl,
                'expiration' => (time() + (7 * 24 * 60 * 60)) * 1000, // 7 days
            ]);

            $watchRequest = $service->events->watch($user->google_calendar_id, $channel);
            
            // Store webhook info in user record
            $user->update([
                'google_webhook_channel_id' => $channelId,
                'google_webhook_resource_id' => $watchRequest->getResourceId(),
                'google_webhook_expiration' => Carbon::createFromTimestamp($watchRequest->getExpiration() / 1000)
            ]);

            Log::info('Google Calendar webhook setup successful', [
                'user_id' => $user->id,
                'channel_id' => $channelId,
                'resource_id' => $watchRequest->getResourceId()
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Failed to setup Google Calendar webhook: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Stop webhook for calendar changes
     */
    public function stopWebhook(User $user)
    {
        if (!$user->google_webhook_channel_id || !$user->google_webhook_resource_id) {
            return true;
        }

        try {
            $this->setTokenForUser($user);
            $service = new Calendar($this->client);

            $channel = new \Google\Service\Calendar\Channel([
                'id' => $user->google_webhook_channel_id,
                'resourceId' => $user->google_webhook_resource_id
            ]);

            $service->channels->stop($channel);
            
            // Clear webhook info from user record
            $user->update([
                'google_webhook_channel_id' => null,
                'google_webhook_resource_id' => null,
                'google_webhook_expiration' => null
            ]);

            Log::info('Google Calendar webhook stopped', ['user_id' => $user->id]);
            return true;
        } catch (Exception $e) {
            Log::error('Failed to stop Google Calendar webhook: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the Google Client instance
     */
    public function getClient()
    {
        return $this->client;
    }
}