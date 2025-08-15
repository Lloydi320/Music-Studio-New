<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Services\GoogleCalendarService;

class BookingController extends Controller
{
    protected $calendarService;

    public function __construct()
    {
        // Initialize calendar service only if Google Client is available
        try {
            if (class_exists('Google\Client')) {
                $this->calendarService = app(GoogleCalendarService::class);
            }
        } catch (\Exception $e) {
            $this->calendarService = null;
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'time_slot' => 'required|string',
            'duration' => 'required|integer|min:1|max:8',
            'service_type' => 'required|string|in:studio_rental,recording_session,music_lesson,band_practice,audio_production,instrument_rental,other',
            'band_name' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        // Parse the time slot to get start and end times
        $timeSlot = $request->time_slot;
        $duration = (int) $request->duration; // Ensure duration is an integer
        
        // Extract start time from the time slot (e.g., "09:00 AM - 01:00 PM" -> "09:00 AM")
        $startTime = trim(explode('-', $timeSlot)[0]);
        
        // Calculate the new booking's start and end times
        $bookingDate = Carbon::parse($request->date);
        $newStartTime = Carbon::createFromFormat('Y-m-d g:i A', $bookingDate->format('Y-m-d') . ' ' . $startTime, config('app.timezone', 'Asia/Manila'));
        $newEndTime = $newStartTime->copy()->addHours($duration);
        
        // Check for overlapping bookings
        $existingBookings = Booking::where('date', $request->date)
            ->where('status', '!=', 'cancelled')
            ->get();
        
        foreach ($existingBookings as $existingBooking) {
            $existingStartTime = trim(explode('-', $existingBooking->time_slot)[0]);
            $existingBookingDate = Carbon::parse($existingBooking->date);
            $existingStart = Carbon::createFromFormat('Y-m-d g:i A', $existingBookingDate->format('Y-m-d') . ' ' . $existingStartTime, config('app.timezone', 'Asia/Manila'));
            $existingEnd = $existingStart->copy()->addHours($existingBooking->duration);
            
            // Check if there's any overlap
            if (
                ($newStartTime < $existingEnd && $newEndTime > $existingStart) ||
                ($existingStart < $newEndTime && $existingEnd > $newStartTime)
            ) {
                return back()->with('error', 'This time slot overlaps with an existing booking. Please choose a different time.');
            }
        }
    
        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('booking_images', $imageName, 'public');
        }

        // Calculate pricing (₱250 per hour)
        $hourlyRate = 250.00;
        $totalAmount = $hourlyRate * $duration;
        
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'date' => $request->date,
            'time_slot' => $request->time_slot,
            'duration' => $duration,
            'price' => $hourlyRate,
            'total_amount' => $totalAmount,
            'service_type' => $request->service_type,
            'band_name' => $request->band_name,
            'contact_number' => $request->contact_number,
            'image_path' => $imagePath,
            'status' => 'pending',
        ]);

        // Log booking creation
        ActivityLog::logBooking(
            ActivityLog::ACTION_BOOKING_CREATED,
            $booking,
            null,
            $booking->toArray()
        );
    
        // Remove this section - don't create calendar event immediately
        // Google Calendar event will be created only when booking is approved
        
        // Get the user for email notification
        $user = Auth::user();
        
        // Send email notification
        try {
            Mail::to($user->email)->send(new \App\Mail\BookingNotification($booking, $user));
            
            Log::info('Booking notification email sent', [
                'booking_id' => $booking->id,
                'user_email' => $user->email,
                'reference' => $booking->reference
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send booking notification email', [
                'booking_id' => $booking->id,
                'user_email' => $user->email,
                'error' => $e->getMessage()
            ]);
        }
    
        // Log the booking creation for debugging
        Log::info('Booking created successfully', [
            'id' => $booking->id,
            'reference' => $booking->reference,
            'user_id' => $booking->user_id,
            'date' => $booking->date,
            'time_slot' => $booking->time_slot,
            'duration' => $booking->duration,
            'status' => $booking->status,
        ]);
    
        return back()->with('success', 'Booking created successfully! You will receive an email confirmation shortly.');
    }

    public function getByDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);
        
        $bookings = Booking::where('date', $request->date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get(['time_slot', 'user_id', 'duration', 'status', 'reference']); // Added status and reference
        
        // Calculate the actual occupied time ranges for each booking
        $occupiedRanges = [];
        foreach ($bookings as $booking) {
            // Extract start time from the stored time slot
            $startTime = trim(explode('-', $booking->time_slot)[0]);
            
            // Calculate the actual end time based on duration
            $startDateTime = Carbon::createFromFormat('h:i A', $startTime, config('app.timezone', 'Asia/Manila'));
            $endDateTime = $startDateTime->copy()->addHours($booking->duration);
            
            // Format the actual occupied time range
            $actualTimeSlot = $startDateTime->format('h:i A') . ' - ' . $endDateTime->format('h:i A');
            
            $occupiedRanges[] = [
                'time_slot' => $actualTimeSlot,
                'user_id' => $booking->user_id,
                'duration' => $booking->duration,
                'status' => $booking->status, // Include status
                'reference' => $booking->reference // Include reference for identification
            ];
        }
        
        return response()->json($occupiedRanges);
    }

    // New API methods to match your PHP scripts

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'time_slot' => 'required|string',
            'duration' => 'required|integer|min:1|max:8',
        ]);
        
        // Parse the time slot to get start and end times
        $timeSlot = $request->time_slot;
        $duration = (int) $request->duration; // Ensure duration is an integer
        
        // Extract start time from the time slot (e.g., "09:00 AM - 01:00 PM" -> "09:00 AM")
        $startTime = trim(explode('-', $timeSlot)[0]);
        
        // Calculate the new booking's start and end times
        $newStartTime = Carbon::createFromFormat('h:i A', $startTime, config('app.timezone', 'Asia/Manila'));
        $newEndTime = $newStartTime->copy()->addHours($duration);
        
        // Check for overlapping bookings on the same date (pending and confirmed bookings)
        $overlappingBookings = Booking::where('date', $request->date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get();
        
        foreach ($overlappingBookings as $existingBooking) {
            // Parse existing booking's time slot
            $existingStartTime = trim(explode('-', $existingBooking->time_slot)[0]);
            $existingStart = Carbon::createFromFormat('h:i A', $existingStartTime, config('app.timezone', 'Asia/Manila'));
            $existingEnd = $existingStart->copy()->addHours($existingBooking->duration);
            
            // Check for overlap
            if (
                ($newStartTime < $existingEnd && $newEndTime > $existingStart) ||
                ($existingStart < $newEndTime && $existingEnd > $newStartTime)
            ) {
                return response()->json(['available' => false, 'reason' => 'This time slot overlaps with an existing booking']);
            }
        }
        
        return response()->json(['available' => true]);
    }

    public function getByReference($reference)
    {
        $booking = Booking::with('user')->where('reference', $reference)->first();
        
        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }
        
        return response()->json($booking);
    }

    public function getUserBookings()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->orderBy('date', 'desc')
            ->get();
        
        return response()->json($bookings);
    }

    public function cancelByReference($reference)
    {
        $booking = Booking::where('reference', $reference)
            ->where('user_id', Auth::id())
            ->first();
        
        if (!$booking) {
            return response()->json(['error' => 'Booking not found or unauthorized'], 404);
        }
        
        $oldValues = $booking->toArray();
        $booking->update(['status' => 'cancelled']);
        
        // Log booking cancellation
        ActivityLog::logBooking(
            ActivityLog::ACTION_BOOKING_CANCELLED,
            $booking,
            $oldValues,
            $booking->fresh()->toArray()
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully'
        ]);
    }

    public function updateStatus(Request $request, $reference)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled',
        ]);
        
        $booking = Booking::where('reference', $reference)->first();
        
        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }
        
        $oldValues = $booking->toArray();
        $booking->update(['status' => $request->status]);
        
        // Log booking status update
        $action = match($request->status) {
            'confirmed' => ActivityLog::ACTION_BOOKING_APPROVED,
            'cancelled' => ActivityLog::ACTION_BOOKING_CANCELLED,
            default => ActivityLog::ACTION_BOOKING_UPDATED
        };
        
        ActivityLog::logBooking(
            $action,
            $booking,
            $oldValues,
            $booking->fresh()->toArray()
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Booking status updated successfully'
        ]);
    }

    public function checkStatus($reference)
    {
        $booking = Booking::where('reference', $reference)->first();
        
        if (!$booking) {
            return response()->json(['status' => 'not_found'], 404);
        }
        
        return response()->json(['status' => $booking->status]);
    }

    /**
     * Return all booked dates as an array of date strings (YYYY-MM-DD).
     */
    public function getBookedDates()
    {
        $dates = \App\Models\Booking::pluck('date')->unique()->values();
        return response()->json(['booked_dates' => $dates]);
    }

    /**
     * Return all bookings for a given date as JSON.
     */
    public function getBookingsByDate(Request $request)
    {
        $date = $request->query('date');
        $bookings = \App\Models\Booking::where('date', $date)->get(['id', 'reference', 'user_id', 'date', 'time_slot', 'duration', 'status']);
        return response()->json(['bookings' => $bookings]);
    }

    /**
     * Return all booked dates with their status information
     */
    public function getBookedDatesWithStatus()
    {
        $bookings = Booking::whereIn('status', ['pending', 'confirmed'])
            ->select('date', 'status')
            ->get()
            ->groupBy('date');
        
        $result = [];
        foreach ($bookings as $date => $dateBookings) {
            $statuses = $dateBookings->pluck('status')->unique()->values()->toArray();
            $result[] = [
                'date' => $date,
                'statuses' => $statuses
            ];
        }
        
        return response()->json($result);
    }
}