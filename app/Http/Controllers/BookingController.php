<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
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
        $duration = $request->duration;
        
        // Extract start time from time slot (e.g., "10:00 AM - 02:00 PM" -> "10:00 AM")
        $startTime = trim(explode('-', $timeSlot)[0]);
        
        // Calculate end time based on duration
        $startDateTime = Carbon::createFromFormat('h:i A', $startTime);
        $endDateTime = $startDateTime->copy()->addHours($duration);
        
        // Format times for comparison
        $newStartTime = $startDateTime->format('H:i');
        $newEndTime = $endDateTime->format('H:i');

        // Check for overlapping bookings on the same date
        $overlappingBookings = Booking::where('date', $request->date)
            ->where('status', '!=', 'cancelled')
            ->get();

        foreach ($overlappingBookings as $existingBooking) {
            $existingTimeSlot = $existingBooking->time_slot;
            $existingStartTime = trim(explode('-', $existingTimeSlot)[0]);
            $existingStartDateTime = Carbon::createFromFormat('h:i A', $existingStartTime);
            $existingEndDateTime = $existingStartDateTime->copy()->addHours($existingBooking->duration);
            
            $existingStart = $existingStartDateTime->format('H:i');
            $existingEnd = $existingEndDateTime->format('H:i');

            // Check if the new booking overlaps with existing booking
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
            'duration' => $request->duration,
            'status' => 'pending',
        ]);

        return redirect('/booking')->with('success', 'Your booking has been confirmed! Reference: ' . $booking->reference);
    }

    public function getByDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);
        $bookings = Booking::where('date', $request->date)->get(['time_slot', 'user_id']);
        return response()->json($bookings);
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
        $duration = $request->duration;
        
        // Extract start time from time slot
        $startTime = trim(explode('-', $timeSlot)[0]);
        
        // Calculate end time based on duration
        $startDateTime = Carbon::createFromFormat('h:i A', $startTime);
        $endDateTime = $startDateTime->copy()->addHours($duration);
        
        // Format times for comparison
        $newStartTime = $startDateTime->format('H:i');
        $newEndTime = $endDateTime->format('H:i');

        // Check for overlapping bookings on the same date
        $overlappingBookings = Booking::where('date', $request->date)
            ->where('status', '!=', 'cancelled')
            ->get();

        foreach ($overlappingBookings as $existingBooking) {
            $existingTimeSlot = $existingBooking->time_slot;
            $existingStartTime = trim(explode('-', $existingTimeSlot)[0]);
            $existingStartDateTime = Carbon::createFromFormat('h:i A', $existingStartTime);
            $existingEndDateTime = $existingStartDateTime->copy()->addHours($existingBooking->duration);
            
            $existingStart = $existingStartDateTime->format('H:i');
            $existingEnd = $existingEndDateTime->format('H:i');

            // Check if the new booking overlaps with existing booking
            if (
                ($newStartTime < $existingEnd && $newEndTime > $existingStart) ||
                ($existingStart < $newEndTime && $existingEnd > $newStartTime)
            ) {
                return response()->json(['available' => false, 'reason' => 'Time slot overlaps with existing booking']);
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
} 