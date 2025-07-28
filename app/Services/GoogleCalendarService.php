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
    private function setTokenForUser(User $user)
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

        // Parse booking time
        $startTime = trim(explode('-', $booking->time_slot)[0]);
        $startDateTime = Carbon::createFromFormat('Y-m-d H:i A', $booking->date . ' ' . $startTime);
        $endDateTime = $startDateTime->copy()->addHours((int) $booking->duration);

        // Create event
        $event = new Event([
            'summary' => 'Studio Session - ' . $booking->user->name,
            'description' => "Booking Reference: {$booking->reference}\n" .
                           "Client: {$booking->user->name}\n" .
                           "Email: {$booking->user->email}\n" .
                           "Duration: {$booking->duration} hour(s)\n" .
                           "Status: {$booking->status}",
            'start' => new EventDateTime([
                'dateTime' => $startDateTime->toIso8601String(),
                'timeZone' => config('app.timezone', 'UTC'),
            ]),
            'end' => new EventDateTime([
                'dateTime' => $endDateTime->toIso8601String(),
                'timeZone' => config('app.timezone', 'UTC'),
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
        $booking->update(['google_event_id' => $createdEvent->getId()]);

                    Log::info('Calendar event created', [
            'booking_id' => $booking->id,
            'event_id' => $createdEvent->getId(),
            'admin_id' => $admin->id
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

        // Parse booking time
        $startTime = trim(explode('-', $booking->time_slot)[0]);
        $startDateTime = Carbon::createFromFormat('Y-m-d H:i A', $booking->date . ' ' . $startTime);
        $endDateTime = $startDateTime->copy()->addHours((int) $booking->duration);

        // Get existing event
        $event = $service->events->get($admin->google_calendar_id, $booking->google_event_id);

        // Update event details
        $event->setSummary('Studio Session - ' . $booking->user->name);
        $event->setDescription("Booking Reference: {$booking->reference}\n" .
                              "Client: {$booking->user->name}\n" .
                              "Email: {$booking->user->email}\n" .
                              "Duration: {$booking->duration} hour(s)\n" .
                              "Status: {$booking->status}");

        $event->setStart(new EventDateTime([
            'dateTime' => $startDateTime->toIso8601String(),
            'timeZone' => config('app.timezone', 'UTC'),
        ]));

        $event->setEnd(new EventDateTime([
            'dateTime' => $endDateTime->toIso8601String(),
            'timeZone' => config('app.timezone', 'UTC'),
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
            $service->events->delete($admin->google_calendar_id, $booking->google_event_id);
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
                    'is_studio_booking' => strpos($event->getSummary() ?: '', 'Studio Session') !== false,
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
}