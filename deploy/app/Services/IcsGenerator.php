<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\InstrumentRental;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class IcsGenerator
{
    /**
     * Regenerate the combined ICS file for bookings and rentals.
     * Writes to storage/app/public/calendar/music-studio-bookings.ics
     */
    public function regenerate(): void
    {
        $tz = config('app.timezone', 'Asia/Manila');
        $nowTz = Carbon::now($tz);

        // Window for static export: last 1 month to next 12 months
        $windowStart = Carbon::now()->subMonths(1)->startOfDay();
        $windowEnd = Carbon::now()->addMonths(12)->endOfDay();

        $bookings = Booking::whereIn('status', ['pending', 'confirmed'])
            ->whereDate('date', '>=', $windowStart)
            ->whereDate('date', '<=', $windowEnd)
            ->orderBy('date', 'asc')
            ->get();

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Music Studio//Bookings & Rentals//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'X-WR-CALNAME:Music Studio Services',
            'X-WR-CALDESC:Studio bookings and instrument rentals',
            'X-WR-TIMEZONE:' . $tz,
        ];

        // Add booking events
        foreach ($bookings as $booking) {
            try {
                [$startUtc, $endUtc] = $this->parseBookingDateTimes($booking, $tz);
            } catch (\Throwable $e) {
                continue;
            }

            $serviceLabel = method_exists($booking, 'getServiceTypeLabel') ? $booking->getServiceTypeLabel() : (\App\Models\Booking::SERVICE_TYPES[$booking->service_type] ?? ucfirst((string)$booking->service_type));
            $summary = $this->escapeText($this->styledSummary($serviceLabel, $booking));

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
            $lines[] = 'CATEGORIES:' . $this->eventCategory($booking->service_type);
            $lines[] = 'PRIORITY:' . ($booking->status === 'confirmed' ? '5' : '3');
            $lines[] = 'END:VEVENT';
        }

        // Add rental events (all-day, inclusive of end date)
        $rentals = InstrumentRental::whereIn('status', ['pending', 'confirmed', 'active'])
            ->whereDate('rental_end_date', '>=', $windowStart)
            ->whereDate('rental_start_date', '<=', $windowEnd)
            ->orderBy('rental_start_date', 'asc')
            ->get();

        foreach ($rentals as $rental) {
            $start = Carbon::parse($rental->rental_start_date, $tz)->copy()->setTimezone('UTC');
            $endExclusive = Carbon::parse($rental->rental_end_date, $tz)->copy()->addDay()->setTimezone('UTC');

            $summary = $this->escapeText($this->styledRentalSummary($rental));
            $descriptionParts = [
                'Ref: ' . ($rental->reference ?? 'N/A'),
                'Instrument: ' . ($rental->instrument_name ?? $rental->instrument_type ?? 'N/A'),
                'Client: ' . ($rental->user->name ?? ($rental->name ?? $rental->email ?? 'N/A')),
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
            $lines[] = 'PRIORITY:' . ($rental->status === 'confirmed' ? '5' : '3');
            $lines[] = 'TRANSP:OPAQUE';
            $lines[] = 'END:VEVENT';
        }

        $lines[] = 'END:VCALENDAR';
        $ics = implode("\r\n", $lines) . "\r\n";

        // Persist to public disk for easy serving
        Storage::disk('public')->put('calendar/music-studio-bookings.ics', $ics);
    }

    private function parseBookingDateTimes(\App\Models\Booking $booking, string $tz): array
    {
        $bookingDate = Carbon::parse($booking->date, $tz);
        $slotRaw = trim((string)($booking->time_slot ?? ''));
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
        if (!$parts) { $parts = [trim($slotRaw)]; }
        $startStr = $parts[0] ?? '';
        $endStr = $parts[1] ?? '';
        if ($startStr === '') { throw new \Exception('Missing start time'); }
        $dateString = $bookingDate->format('Y-m-d');
        $startLocal = $this->tryParseTime($dateString, $startStr, $tz);
        if (!$startLocal) { throw new \Exception('Unparseable start time: ' . $startStr); }
        $durationHours = (int)($booking->duration ?? 1);
        $endLocal = null;
        if ($endStr !== '') {
            $endLocal = $this->tryParseTime($dateString, $endStr, $tz);
            if ($endLocal && $endLocal->lessThanOrEqualTo($startLocal) && $durationHours > 0) {
                $endLocal = $startLocal->copy()->addHours($durationHours);
            }
        }
        if (!$endLocal) {
            if ($durationHours > 0) { $endLocal = $startLocal->copy()->addHours($durationHours); }
            else { throw new \Exception('Missing end time and duration'); }
        }
        return [$startLocal->copy()->setTimezone('UTC'), $endLocal->copy()->setTimezone('UTC')];
    }

    private function tryParseTime(string $dateString, string $timeString, string $tz): ?Carbon
    {
        $timeString = trim($timeString);
        $candidates = [
            'Y-m-d g:i A','Y-m-d h:i A','Y-m-d G:i','Y-m-d H:i','Y-m-d g A','Y-m-d h A','Y-m-d G','Y-m-d H',
        ];
        foreach ($candidates as $fmt) {
            try {
                $dt = Carbon::createFromFormat($fmt, $dateString . ' ' . $timeString, $tz);
                if ($dt !== false && $dt->format($fmt) === $dateString . ' ' . $timeString) { return $dt; }
            } catch (\Throwable $e) {}
        }
        if (preg_match('/^(\d{1,2}):(\d{2})$/', $timeString, $m)) {
            $hour = (int)$m[1]; $minute = (int)$m[2];
            try {
                return Carbon::createFromTime($hour, $minute, 0, $tz)->setDate(
                    Carbon::parse($dateString)->year,
                    Carbon::parse($dateString)->month,
                    Carbon::parse($dateString)->day
                );
            } catch (\Throwable $e) {}
        }
        try { return Carbon::parse($dateString . ' ' . $timeString, $tz); } catch (\Throwable $e) { return null; }
    }

    private function escapeText(string $text): string
    {
        $text = str_replace('\\', '\\\\', $text);
        $text = str_replace(';', '\\;', $text);
        $text = str_replace(',', '\\,', $text);
        $text = str_replace("\n", '\\n', $text);
        $text = str_replace("\r", '', $text);
        return $text;
    }

    private function styledSummary(string $serviceLabel, \App\Models\Booking $booking): string
    {
        $base = $booking->service_type === 'music_lesson'
            ? ($booking->lesson_type ? ucwords($booking->lesson_type) : 'Music Lesson')
            : $serviceLabel;
        $base = preg_replace('/\bLesson\b/i', 'Les', $base);
        $base = preg_replace('/\bRehearsal\b/i', 'Reh', $base);
        $base = preg_replace('/\bInstrument\s+Rental\b/i', 'Rental', $base);
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

    private function styledRentalSummary(\App\Models\InstrumentRental $rental): string
    {
        $blocksStudio = in_array(strtolower($rental->instrument_type), ['drums','full package']);
        $prefix = $blocksStudio ? 'UNAVAIL' : 'Rental';
        $instrument = $rental->instrument_name ?: (\App\Models\InstrumentRental::getInstrumentTypes()[$rental->instrument_type] ?? ucfirst((string)$rental->instrument_type));
        $clientName = $rental->user->name ?? ($rental->name ?? $rental->email ?? 'Client');
        return trim($prefix . ' - ' . $instrument . ' - ' . $clientName);
    }

    private function eventCategory(string $serviceType): string
    {
        switch ($serviceType) {
            case 'music_lesson': return 'Lesson';
            case 'solo_rehearsal': return 'Rehearsal';
            case 'band_rehearsal': return 'Rehearsal';
            case 'instrument_rental': return 'Rental';
            default: return 'Booking';
        }
    }

    private function icsStatusForBooking(\App\Models\Booking $booking): string
    {
        $status = strtolower((string) $booking->status);
        return match ($status) {
            'pending' => 'TENTATIVE',
            'confirmed' => 'CONFIRMED',
            'cancelled', 'rejected' => 'CANCELLED',
            default => 'CONFIRMED',
        };
    }

    private function icsStatusForRental(\App\Models\InstrumentRental $rental): string
    {
        $status = strtolower((string) $rental->status);
        return match ($status) {
            'pending' => 'TENTATIVE',
            'confirmed', 'active' => 'CONFIRMED',
            'cancelled', 'returned' => 'CANCELLED',
            default => 'CONFIRMED',
        };
    }
}