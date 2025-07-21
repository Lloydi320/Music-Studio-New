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
        ]);

        // Prevent double booking
        $exists = Booking::where('date', $request->date)
            ->where('time_slot', $request->time_slot)
            ->exists();
        if ($exists) {
            return back()->with('error', 'This time slot is already booked.');
        }

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'date' => $request->date,
            'time_slot' => $request->time_slot,
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