<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function store(Request $request)
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
        $newStartTime = Carbon::createFromFormat('h:i A', $startTime);
        $newEndTime = $newStartTime->copy()->addHours($duration);
        
        // Check for overlapping bookings on the same date
        $overlappingBookings = Booking::where('date', $request->date)
            ->where('status', '!=', 'cancelled')
            ->get();
        
        foreach ($overlappingBookings as $existingBooking) {
            // Parse existing booking's time slot
            $existingStartTime = trim(explode('-', $existingBooking->time_slot)[0]);
            $existingStart = Carbon::createFromFormat('h:i A', $existingStartTime);
            $existingEnd = $existingStart->copy()->addHours($existingBooking->duration);
            
            // Check for overlap
            if (
                ($newStartTime < $existingEnd && $newEndTime > $existingStart) ||
                ($existingStart < $newEndTime && $existingEnd > $newStartTime)
            ) {
                return back()->with('error', 'This time slot overlaps with an existing booking. Please choose a different time.');
            }
        }

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'date' => $request->date,
            'time_slot' => $request->time_slot,
            'duration' => $duration, // Use the already cast integer
            'status' => 'confirmed',
        ]);

        // Log the booking creation for debugging
        Log::info('Booking created successfully', [
            'id' => $booking->id,
            'reference' => $booking->reference,
            'date' => $booking->date,
            'time_slot' => $booking->time_slot,
            'duration' => $booking->duration,
            'user_id' => $booking->user_id
        ]);

        return redirect('/booking')->with('success', 'Your booking has been confirmed! Reference: ' . $booking->reference);
    }

    public function getByDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);
        
        $bookings = Booking::where('date', $request->date)
            ->where('status', '!=', 'cancelled')
            ->get(['time_slot', 'user_id', 'duration']);
        
        // Calculate the actual occupied time ranges for each booking
        $occupiedRanges = [];
        foreach ($bookings as $booking) {
            // Extract start time from the stored time slot
            $startTime = trim(explode('-', $booking->time_slot)[0]);
            
            // Calculate the actual end time based on duration
            $startDateTime = Carbon::createFromFormat('h:i A', $startTime);
            $endDateTime = $startDateTime->copy()->addHours($booking->duration);
            
            // Format the actual occupied time range
            $actualTimeSlot = $startDateTime->format('h:i A') . ' - ' . $endDateTime->format('h:i A');
            
            $occupiedRanges[] = [
                'time_slot' => $actualTimeSlot,
                'user_id' => $booking->user_id,
                'duration' => $booking->duration
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
        $newStartTime = Carbon::createFromFormat('h:i A', $startTime);
        $newEndTime = $newStartTime->copy()->addHours($duration);
        
        // Check for overlapping bookings on the same date
        $overlappingBookings = Booking::where('date', $request->date)
            ->where('status', '!=', 'cancelled')
            ->get();
        
        foreach ($overlappingBookings as $existingBooking) {
            // Parse existing booking's time slot
            $existingStartTime = trim(explode('-', $existingBooking->time_slot)[0]);
            $existingStart = Carbon::createFromFormat('h:i A', $existingStartTime);
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
        
        $booking->update(['status' => 'cancelled']);
        
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
        
        $booking->update(['status' => $request->status]);
        
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
} 