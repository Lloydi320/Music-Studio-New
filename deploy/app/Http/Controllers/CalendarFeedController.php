<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\InstrumentRental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class CalendarFeedController extends Controller
{
    /**
     * Generate an ICS feed of confirmed bookings for calendar subscription.
     * URL: /calendar/feed.ics?token=<ICS_FEED_TOKEN>
     */
    public function export(Request $request)
    {
        // Token-based access control
        $token = $request->query('token');
        $expectedToken = env('ICS_FEED_TOKEN');
        if ($expectedToken) {
            if (!$token || !hash_equals($expectedToken, $token)) {
                return Response::make('Unauthorized', 401);
            }
        }

        $tz = config('app.timezone', 'Asia/Manila');
        $nowTz = Carbon::now($tz);
    
        // Wider window for manual import: last 1 month to next 12 months
        $bookings = Booking::whereIn('status', ['pending', 'confirmed'])
            ->whereDate('date', '>=', Carbon::now()->subMonths(1)->startOfDay())
            ->whereDate('date', '<=', Carbon::now()->addMonths(12)->endOfDay())
            ->orderBy('date', 'asc')
            ->get();
    
        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Music Studio//Bookings//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'X-WR-CALNAME:Music Studio Bookings',
            'X-WR-CALDESC:Studio bookings (auto-updating)',
            'X-WR-TIMEZONE:' . $tz,
        ];
    
        $this->addBookingEvents($lines, $bookings, $nowTz);
    
        // Include instrument rentals as all-day events in the same window
        $this->addRentalEvents($lines, Carbon::now()->subMonths(1)->startOfDay(), Carbon::now()->addMonths(12)->endOfDay(), $nowTz);
    
        $lines[] = 'END:VCALENDAR';
        $ics = implode("\r\n", $lines) . "\r\n";
    
        return Response::make(
            $ics,
            200,
            [
                'Content-Type' => 'text/calendar; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="music-studio-bookings.ics"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
            ]
        );
    }

    private function addBookingEvents(array &$lines, $bookings, Carbon $nowTz): void
    {
        foreach ($bookings as $booking) {
            try {
                [$startUtc, $endUtc] = $this->parseBookingDateTimes($booking);
            } catch (\Throwable $e) {
                continue;
            }

            $serviceLabel = method_exists($booking, 'getServiceTypeLabel') ? $booking->getServiceTypeLabel() : (Booking::SERVICE_TYPES[$booking->service_type] ?? ucfirst((string)$booking->service_type));
            $summary = $this->escapeText($this->styledSummary($booking));
            
            // Build comprehensive description
            $descriptionParts = [
                'Ref: ' . ($booking->reference ?? 'N/A'),
                'Service: ' . $serviceLabel,
                'Client: ' . ($booking->user->name ?? ($booking->email ?? 'N/A')),
                'Status: ' . ucfirst((string)$booking->status),
                'Time Slot: ' . ($booking->time_slot ?? 'N/A'),
                'Duration: ' . (int)($booking->duration ?? 0) . ' hour(s)',
            ];
            
            if (!empty($booking->price)) {
                $descriptionParts[] = 'Price: ₱' . number_format($booking->price, 2);
            }
            
            if (!empty($booking->lesson_type)) {
                $descriptionParts[] = 'Lesson Type: ' . ucfirst($booking->lesson_type);
            }
            
            if (!empty($booking->band_name)) {
                $descriptionParts[] = 'Band: ' . $booking->band_name;
            }
            
            if (!empty($booking->band_details)) {
                $descriptionParts[] = 'Band Details: ' . $booking->band_details;
            }
            
            $description = $this->escapeText(implode('\n', $descriptionParts));
            $location = $this->escapeText('Music Studio');
    
            $lines[] = 'BEGIN:VEVENT';
            $lines[] = 'UID:booking-' . $booking->id . '@musicstudio';
            $lines[] = 'DTSTAMP:' . $nowTz->copy()->setTimezone('UTC')->format('Ymd\\THis\\Z');
            $lines[] = 'DTSTART:' . $startUtc->format('Ymd\\THis\\Z');
            $lines[] = 'DTEND:' . $endUtc->format('Ymd\\THis\\Z');
            $lines[] = 'SUMMARY:' . $summary;
            $lines[] = 'DESCRIPTION:' . $description;
            $lines[] = 'LOCATION:' . $location;
            $lines[] = 'STATUS:' . $this->icsStatusForBooking($booking);
            $lines[] = 'CATEGORIES:' . $this->eventCategory($booking);
            
            // Add priority based on booking status
            if ($booking->status === 'confirmed') {
                $lines[] = 'PRIORITY:5'; // Normal priority for confirmed bookings
            } else {
                $lines[] = 'PRIORITY:3'; // Lower priority for pending bookings
            }
            
            $lines[] = 'END:VEVENT';
        }
    }

    /**
     * Parse booking date/time from date and time_slot to UTC Carbon instances.
     * @throws \Exception on parsing failure
     */
    private function parseBookingDateTimes(Booking $booking): array
    {
        $tz = config('app.timezone', 'Asia/Manila');
        $bookingDate = Carbon::parse($booking->date, $tz);

        $slotRaw = trim((string)($booking->time_slot ?? ''));

        // Detect "start - end" like patterns with various separators
        $parts = null;
        $separators = ['-', '–', '—', 'to'];
        foreach ($separators as $sep) {
            if (stripos($slotRaw, $sep) !== false) {
                $parts = preg_split('/\s*' . preg_quote($sep, '/') . '\s*/i', $slotRaw);
                if ($parts && count($parts) >= 2) {
                    $parts = [trim($parts[0]), trim($parts[1])];
                }
                break;
            }
        }
        if (!$parts) {
            $parts = [trim($slotRaw)];
        }

        $startStr = $parts[0] ?? '';
        $endStr = $parts[1] ?? '';

        if ($startStr === '') {
            throw new \Exception('Missing start time');
        }

        // Try parsing with multiple formats, ensuring proper timezone handling
        $dateString = $bookingDate->format('Y-m-d');
        $startLocal = $this->tryParseTime($dateString, $startStr, $tz);
        if (!$startLocal) {
            throw new \Exception('Unparseable start time: ' . $startStr);
        }

        $durationHours = (int)($booking->duration ?? 1); // Default to 1 hour if not specified

        $endLocal = null;
        if ($endStr !== '') {
            $endLocal = $this->tryParseTime($dateString, $endStr, $tz);
            // If parsed end time is before or equal to start time, use duration instead
            if ($endLocal && $endLocal->lessThanOrEqualTo($startLocal) && $durationHours > 0) {
                $endLocal = $startLocal->copy()->addHours($durationHours);
            }
        }

        if (!$endLocal) {
            if ($durationHours > 0) {
                $endLocal = $startLocal->copy()->addHours($durationHours);
            } else {
                throw new \Exception('Missing end time and duration');
            }
        }

        return [$startLocal->copy()->setTimezone('UTC'), $endLocal->copy()->setTimezone('UTC')];
    }

    /**
     * Escape text values for ICS.
     */
    private function escapeText(string $text): string
    {
        $text = str_replace('\\', '\\\\', $text);
        $text = str_replace(';', '\\;', $text);
        $text = str_replace(',', '\\,', $text);
        $text = str_replace("\n", '\\n', $text);
        $text = str_replace("\r", '', $text);
        return $text;
    }

    private function tryParseTime(string $dateString, string $timeString, string $tz): ?Carbon
    {
        // Clean up the time string
        $timeString = trim($timeString);
        
        // Handle common time formats used in the booking system
        $candidates = [
            'Y-m-d g:i A',      // e.g., "3:00 PM"
            'Y-m-d h:i A',      // e.g., "03:00 PM"
            'Y-m-d G:i',        // e.g., "15:00"
            'Y-m-d H:i',        // e.g., "15:00"
            'Y-m-d g A',        // e.g., "3 PM"
            'Y-m-d h A',        // e.g., "03 PM"
            'Y-m-d G',          // e.g., "15"
            'Y-m-d H',          // e.g., "15"
        ];

        // Try each format
        foreach ($candidates as $fmt) {
            try {
                $dt = Carbon::createFromFormat($fmt, $dateString . ' ' . $timeString, $tz);
                if ($dt !== false && $dt->format($fmt) === $dateString . ' ' . $timeString) {
                    return $dt;
                }
            } catch (\Throwable $e) {
                // Continue to next format
            }
        }

        // Handle special cases for common booking time formats
        // Convert "09:00-10:00" style to "09:00 AM" format
        if (preg_match('/^(\d{1,2}):(\d{2})$/', $timeString, $matches)) {
            $hour = (int)$matches[1];
            $minute = (int)$matches[2];
            try {
                return Carbon::createFromTime($hour, $minute, 0, $tz)->setDate(
                    Carbon::parse($dateString)->year,
                    Carbon::parse($dateString)->month,
                    Carbon::parse($dateString)->day
                );
            } catch (\Throwable $e) {
                // Continue to fallback
            }
        }

        // Fallback to Carbon's natural language parser
        try {
            return Carbon::parse($dateString . ' ' . $timeString, $tz);
        } catch (\Throwable $e) {
            return null;
        }
    }

    // Add subscription ICS endpoint for Google Calendar
    public function ics(Request $request)
    {
        $token = $request->query('token');
        $expectedToken = env('ICS_FEED_TOKEN');
        if ($expectedToken) {
            if (!$token || !hash_equals($expectedToken, $token)) {
                return Response::make('Unauthorized', 401);
            }
        }

        $tz = config('app.timezone', 'Asia/Manila');
        $nowTz = Carbon::now($tz);

        // Window for subscription: next 6 months
        $bookings = Booking::whereIn('status', ['pending', 'confirmed'])
            ->whereDate('date', '>=', Carbon::now()->startOfDay())
            ->whereDate('date', '<=', Carbon::now()->addMonths(6)->endOfDay())
            ->orderBy('date', 'asc')
            ->get();

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Music Studio//Bookings//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'X-WR-CALNAME:Music Studio Bookings',
            'X-WR-CALDESC:Studio bookings (auto-updating)',
            'X-WR-TIMEZONE:' . $tz,
        ];

        foreach ($bookings as $booking) {
            try {
                [$startUtc, $endUtc] = $this->parseBookingDateTimes($booking);
            } catch (\Throwable $e) {
                continue;
            }

            $serviceLabel = method_exists($booking, 'getServiceTypeLabel') ? $booking->getServiceTypeLabel() : (Booking::SERVICE_TYPES[$booking->service_type] ?? ucfirst((string)$booking->service_type));
            $summary = $this->escapeText($this->styledSummary($booking));
            $description = $this->escapeText("Ref: {$booking->reference}\nService: {$serviceLabel}\nDuration: " . (int)($booking->duration ?? 0) . " hour(s)");
            $location = $this->escapeText('Music Studio');

            $lines[] = 'BEGIN:VEVENT';
            $lines[] = 'UID:booking-' . $booking->id . '@musicstudio';
            $lines[] = 'DTSTAMP:' . $nowTz->copy()->setTimezone('UTC')->format('Ymd\\THis\\Z');
            $lines[] = 'DTSTART:' . $startUtc->format('Ymd\\THis\\Z');
            $lines[] = 'DTEND:' . $endUtc->format('Ymd\\THis\\Z');
            $lines[] = 'SUMMARY:' . $summary;
            $lines[] = 'DESCRIPTION:' . $description;
            $lines[] = 'LOCATION:' . $location;
            $lines[] = 'STATUS:CONFIRMED';
            $lines[] = 'CATEGORIES:' . $this->eventCategory($booking);
            $lines[] = 'END:VEVENT';
        }

        // Include instrument rentals as all-day events in the same window
        $this->addRentalEvents($lines, Carbon::now()->startOfDay(), Carbon::now()->addMonths(6)->endOfDay(), $nowTz);

        $lines[] = 'END:VCALENDAR';
        $ics = implode("\r\n", $lines) . "\r\n";

        return Response::make(
            $ics,
            200,
            [
                'Content-Type' => 'text/calendar; charset=utf-8',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
            ]
        );
    }

    // Helper: styled summary with abbreviations and emojis
    private function styledSummary(Booking $booking): string
    {
        $serviceLabel = method_exists($booking, 'getServiceTypeLabel') ? $booking->getServiceTypeLabel() : (Booking::SERVICE_TYPES[$booking->service_type] ?? ucfirst((string)$booking->service_type));
        $base = $booking->service_type === 'music_lesson'
            ? ($booking->lesson_type ? ucwords($booking->lesson_type) : 'Music Lesson')
            : $serviceLabel;
        // Abbreviations to match screenshot style
        $base = preg_replace('/\bLesson\b/i', 'Les', $base);
        $base = preg_replace('/\bRehearsal\b/i', 'Reh', $base);
        $base = preg_replace('/\bInstrument\s+Rental\b/i', 'Rental', $base);

        // Emoji markers (optional, subtle)
        $emoji = '';
        $lower = strtolower($base);
        if (str_contains($lower, 'voice')) { $emoji = '🎤'; }
        elseif (str_contains($lower, 'guitar')) { $emoji = '🎸'; }
        elseif (str_contains($lower, 'drum')) { $emoji = '🥁'; }
        elseif (str_contains($lower, 'piano')) { $emoji = '🎹'; }
        elseif (str_contains($lower, 'band')) { $emoji = '👥'; }
        elseif (str_contains($lower, 'solo')) { $emoji = '🧍'; }

        $clientName = $booking->band_name ?: ($booking->user->name ?? null) ?: ($booking->email ?? null) ?: 'Client';
        $statusPrefix = '';
        if (strtolower((string)$booking->status) === 'pending') { $statusPrefix = '⏳ '; }
        elseif (in_array(strtolower((string)$booking->status), ['cancelled','rejected'])) { $statusPrefix = '❌ '; }
        return trim($statusPrefix . $base . ' - ' . $clientName . ($emoji ? ' ' . $emoji : ''));
    }

    private function eventCategory(Booking $booking): string
    {
        switch ($booking->service_type) {
            case 'music_lesson': return 'Lesson';
            case 'solo_rehearsal': return 'Rehearsal';
            case 'band_rehearsal': return 'Rehearsal';
            case 'instrument_rental': return 'Rental';
            default: return 'Booking';
        }
    }

    /**
     * Export ICS file for a specific booking.
     */
    public function exportBooking($id)
    {
        $booking = Booking::findOrFail($id);
        
        $lines = [];
        $lines[] = 'BEGIN:VCALENDAR';
        $lines[] = 'VERSION:2.0';
        $lines[] = 'PRODID:-//Music Studio//Booking Calendar//EN';
        $lines[] = 'CALSCALE:GREGORIAN';
        $lines[] = 'METHOD:PUBLISH';
        $lines[] = 'X-WR-CALNAME:Music Studio Booking';
        $lines[] = 'X-WR-TIMEZONE:Asia/Manila';
        
        // Add the single booking event
        $this->addSingleBookingEvent($lines, $booking);
        
        $lines[] = 'END:VCALENDAR';
        
        $content = implode("\r\n", $lines);
        
        return response($content, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="booking-' . $booking->id . '.ics"',
        ]);
    }

    /**
     * Export ICS file for a specific instrument rental.
     */
    public function exportRental($id)
    {
        $rental = InstrumentRental::findOrFail($id);
        
        $lines = [];
        $lines[] = 'BEGIN:VCALENDAR';
        $lines[] = 'VERSION:2.0';
        $lines[] = 'PRODID:-//Music Studio//Rental Calendar//EN';
        $lines[] = 'CALSCALE:GREGORIAN';
        $lines[] = 'METHOD:PUBLISH';
        $lines[] = 'X-WR-CALNAME:Music Studio Rental';
        $lines[] = 'X-WR-TIMEZONE:Asia/Manila';
        
        // Add the single rental event
        $this->addSingleRentalEvent($lines, $rental);
        
        $lines[] = 'END:VCALENDAR';
        
        $content = implode("\r\n", $lines);
        
        return response($content, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="rental-' . $rental->id . '.ics"',
        ]);
    }

    /**
     * Add a single booking event to ICS lines.
     */
    private function addSingleBookingEvent(array &$lines, Booking $booking): void
    {
        $dateTime = $this->parseBookingDateTimes($booking);
        
        $lines[] = 'BEGIN:VEVENT';
        $lines[] = 'UID:booking-' . $booking->id . '@musicstudio';
        $lines[] = 'DTSTAMP:' . now('Asia/Manila')->format('Ymd\THis\Z');
        $lines[] = 'DTSTART:' . $dateTime['start']->format('Ymd\THis\Z');
        $lines[] = 'DTEND:' . $dateTime['end']->format('Ymd\THis\Z');
        $lines[] = 'SUMMARY:' . $this->escapeIcsText($this->getBookingSummary($booking));
        $lines[] = 'DESCRIPTION:' . $this->escapeIcsText($this->getBookingDescription($booking));
        $lines[] = 'LOCATION:' . $this->escapeIcsText($this->getBookingLocation($booking));
        $lines[] = 'STATUS:' . $this->icsStatusForBooking($booking);
        $lines[] = 'PRIORITY:' . $this->getBookingPriority($booking);
        $lines[] = 'CATEGORIES:' . ($booking->service_type === 'Band Rehearsal' ? 'BAND' : 'SOLO');
        $lines[] = 'END:VEVENT';
    }

    /**
     * Add a single rental event to ICS lines.
     */
    private function addSingleRentalEvent(array &$lines, InstrumentRental $rental): void
    {
        $dateTime = $this->parseRentalDateTimes($rental);
        
        $lines[] = 'BEGIN:VEVENT';
        $lines[] = 'UID:rental-' . $rental->id . '@musicstudio';
        $lines[] = 'DTSTAMP:' . now('Asia/Manila')->format('Ymd\THis\Z');
        $lines[] = 'DTSTART:' . $dateTime['start']->format('Ymd\THis\Z');
        $lines[] = 'DTEND:' . $dateTime['end']->format('Ymd\THis\Z');
        $lines[] = 'SUMMARY:' . $this->escapeIcsText($this->getRentalSummary($rental));
        $lines[] = 'DESCRIPTION:' . $this->escapeIcsText($this->getRentalDescription($rental));
        $lines[] = 'LOCATION:' . $this->escapeIcsText($this->getRentalLocation($rental));
        $lines[] = 'STATUS:' . $this->icsStatusForRental($rental);
        $lines[] = 'PRIORITY:' . $this->getRentalPriority($rental);
        $lines[] = 'CATEGORIES:RENTAL';
        $lines[] = 'END:VEVENT';
    }

    private function parseRentalDateTimes(InstrumentRental $rental): array
    {
        $tz = config('app.timezone', 'Asia/Manila');
        $start = Carbon::parse($rental->rental_start_date, $tz)->copy()->setTimezone('UTC');
        $end = Carbon::parse($rental->rental_end_date, $tz)->copy()->addDay()->setTimezone('UTC');
        
        return [
            'start' => $start,
            'end' => $end,
        ];
    }

    private function getRentalSummary(InstrumentRental $rental): string
    {
        return $this->styledRentalSummary($rental);
    }

    private function getRentalDescription(InstrumentRental $rental): string
    {
        $descriptionParts = [
            'Ref: ' . ($rental->reference ?? 'N/A'),
            'Instrument: ' . ($rental->instrument_name ?? $rental->instrument_type ?? 'N/A'),
            'Client: ' . ($rental->user->name ?? 'N/A'),
            'Status: ' . ucfirst((string)$rental->status),
            'Duration: ' . ($rental->rental_duration_days ?? 'N/A') . ' days',
            'Total Amount: ₱' . number_format($rental->total_amount ?? 0, 2),
            'Pickup Location: ' . ($rental->pickup_location ?? 'Studio'),
            'Return Location: ' . ($rental->return_location ?? 'Studio'),
        ];
        
        if (!empty($rental->notes)) {
            $descriptionParts[] = 'Notes: ' . $rental->notes;
        }
        
        if (!empty($rental->transportation) && $rental->transportation !== 'none') {
            $descriptionParts[] = 'Transportation: ' . ucfirst($rental->transportation);
        }
        
        return implode('\n', $descriptionParts);
    }

    private function getRentalLocation(InstrumentRental $rental): string
    {
        return $rental->pickup_location ?? 'Studio';
    }

    private function getRentalPriority(InstrumentRental $rental): string
    {
        return $rental->status === 'confirmed' ? '5' : '3';
    }

    private function getBookingSummary(Booking $booking): string
    {
        return $this->styledSummary($booking);
    }

    private function getBookingDescription(Booking $booking): string
    {
        $descriptionParts = [
            'Ref: ' . ($booking->reference ?? 'N/A'),
            'Service: ' . ($booking->service_type ?? 'N/A'),
            'Client: ' . ($booking->user->name ?? 'N/A'),
            'Status: ' . ucfirst((string)$booking->status),
            'Date: ' . ($booking->booking_date ?? 'N/A'),
            'Time: ' . ($booking->booking_time ?? 'N/A') . ' - ' . ($booking->booking_end_time ?? 'N/A'),
            'Duration: ' . ($booking->duration ?? 'N/A') . ' hours',
            'Total Amount: ₱' . number_format($booking->total_amount ?? 0, 2),
        ];
        
        if (!empty($booking->notes)) {
            $descriptionParts[] = 'Notes: ' . $booking->notes;
        }
        
        return implode('\n', $descriptionParts);
    }

    private function getBookingLocation(Booking $booking): string
    {
        return 'Music Studio';
    }

    private function getBookingPriority(Booking $booking): string
    {
        return $booking->status === 'confirmed' ? '5' : '3';
    }

    private function escapeIcsText(string $text): string
    {
        return $this->escapeText($text);
    }

    private function icsStatusForBooking(Booking $booking): string
    {
        $status = strtolower((string) $booking->status);
        return match ($status) {
            'pending' => 'TENTATIVE',
            'confirmed' => 'CONFIRMED',
            'cancelled', 'rejected' => 'CANCELLED',
            default => 'CONFIRMED',
        };
    }

    private function icsStatusForRental(InstrumentRental $rental): string
    {
        $status = strtolower((string) $rental->status);
        return match ($status) {
            'pending' => 'TENTATIVE',
            'confirmed', 'active' => 'CONFIRMED',
            'cancelled', 'returned' => 'CANCELLED',
            default => 'CONFIRMED',
        };
    }

    private function styledRentalSummary(InstrumentRental $rental): string
    {
        $blocksStudio = in_array(strtolower($rental->instrument_type), ['drums','full package']);
        $prefix = $blocksStudio ? 'UNAVAIL' : 'Rental';
        $instrument = $rental->instrument_name ?: (InstrumentRental::getInstrumentTypes()[$rental->instrument_type] ?? ucfirst((string)$rental->instrument_type));
        $clientName = $rental->user->name ?? ($rental->name ?? $rental->email ?? 'Client');
        return trim($prefix . ' - ' . $instrument . ' - ' . $clientName);
    }

    private function addRentalEvents(array &$lines, Carbon $windowStart, Carbon $windowEnd, Carbon $nowTz): void
    {
        $tz = config('app.timezone', 'Asia/Manila');
        $rentals = InstrumentRental::whereIn('status', ['pending', 'confirmed', 'active'])
            ->whereDate('rental_end_date', '>=', $windowStart->copy()->startOfDay())
            ->whereDate('rental_start_date', '<=', $windowEnd->copy()->endOfDay())
            ->orderBy('rental_start_date', 'asc')
            ->get();

        foreach ($rentals as $rental) {
            $start = Carbon::parse($rental->rental_start_date, $tz)->copy()->setTimezone('UTC');
            $endExclusive = Carbon::parse($rental->rental_end_date, $tz)->copy()->addDay()->setTimezone('UTC');
            $summary = $this->escapeText($this->styledRentalSummary($rental));
            
            // Build comprehensive description
            $descriptionParts = [
                'Ref: ' . ($rental->reference ?? 'N/A'),
                'Instrument: ' . ($rental->instrument_name ?? $rental->instrument_type ?? 'N/A'),
                'Client: ' . ($rental->user->name ?? 'N/A'),
                'Status: ' . ucfirst((string)$rental->status),
                'Duration: ' . ($rental->rental_duration_days ?? 'N/A') . ' days',
                'Total Amount: ₱' . number_format($rental->total_amount ?? 0, 2),
                'Pickup Location: ' . ($rental->pickup_location ?? 'Studio'),
                'Return Location: ' . ($rental->return_location ?? 'Studio'),
            ];
            
            if (!empty($rental->notes)) {
                $descriptionParts[] = 'Notes: ' . $rental->notes;
            }
            
            if (!empty($rental->transportation) && $rental->transportation !== 'none') {
                $descriptionParts[] = 'Transportation: ' . ucfirst($rental->transportation);
            }
            
            $description = $this->escapeText(implode('\n', $descriptionParts));
            $category = in_array(strtolower($rental->instrument_type), ['drums','full package']) ? 'Unavail' : 'Rental';
            $status = $this->icsStatusForRental($rental);

            $lines[] = 'BEGIN:VEVENT';
            $lines[] = 'UID:rental-' . $rental->id . '@musicstudio';
            $lines[] = 'DTSTAMP:' . $nowTz->copy()->setTimezone('UTC')->format('Ymd\\THis\\Z');
            $lines[] = 'DTSTART;VALUE=DATE:' . $start->format('Ymd');
            $lines[] = 'DTEND;VALUE=DATE:' . $endExclusive->format('Ymd');
            $lines[] = 'SUMMARY:' . $summary;
            $lines[] = 'DESCRIPTION:' . $description;
            $lines[] = 'LOCATION:' . $this->escapeText($rental->pickup_location ?? 'Studio');
            $lines[] = 'STATUS:' . $status;
            $lines[] = 'CATEGORIES:' . $category;
            
            // Add priority based on rental status
            if ($rental->status === 'confirmed') {
                $lines[] = 'PRIORITY:5'; // Normal priority for confirmed rentals
            } else {
                $lines[] = 'PRIORITY:3'; // Lower priority for pending rentals
            }
            
            $lines[] = 'TRANSP:OPAQUE';
            $lines[] = 'END:VEVENT';
        }
    }
}