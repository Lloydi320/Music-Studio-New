<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'time_slot' => 'required|string',
            'duration' => 'required|integer|min:1|max:8',
        ]);

        // Prevent double booking - check for exact time slot match
        $exists = Booking::where('date', $request->date)
            ->where('time_slot', $request->time_slot)
            ->exists();
        if ($exists) {
            return back()->with('error', 'This time slot is already booked.');
        }

        // Additional check for overlapping bookings (optional enhancement)
        // This would require parsing time slots to check for overlaps
        // For now, we'll use the simple exact match approach

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'date' => $request->date,
            'time_slot' => $request->time_slot,
            'duration' => $request->duration,
        ]);

        return redirect('/booking')->with('success', 'Your booking has been confirmed!');
    }

    public function getByDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);
        $bookings = Booking::where('date', $request->date)->get(['time_slot', 'user_id']);
        return response()->json($bookings);
    }
} 