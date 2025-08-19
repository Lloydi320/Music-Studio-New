<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InstrumentRental;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class InstrumentRentalController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'Please log in to rent instruments.');
        }

        $instrumentTypes = InstrumentRental::getInstrumentTypes();
        $availableInstruments = InstrumentRental::getAvailableInstruments();
        $dailyRates = InstrumentRental::getDailyRates();

        return view('instrument-rental', compact('instrumentTypes', 'availableInstruments', 'dailyRates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'instrument_type' => 'required|string',
            'instrument_name' => 'required|string',
            'rental_start_date' => 'required|date|after_or_equal:today',
            'rental_end_date' => 'required|date|after:rental_start_date',
            'notes' => 'nullable|string|max:500',
            'pickup_location' => 'required|string',
            'return_location' => 'required|string',
            'transportation' => 'required|string',
            'full_package' => 'nullable|boolean',
            'venue_type' => 'required|in:indoor,outdoor',
            'event_duration_hours' => 'required|integer|min:1|max:12',
            'documentation_consent' => 'nullable|boolean',
            'picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            'reference_code' => 'required|string|size:4|regex:/^[0-9]{4}$/',
        ]);

        // Calculate rental duration
        $startDate = Carbon::parse($request->rental_start_date);
        $endDate = Carbon::parse($request->rental_end_date);
        $durationDays = $startDate->diffInDays($endDate) + 1; // Include both start and end dates

        // Get daily rate for the instrument type
        $dailyRates = InstrumentRental::getDailyRates();
        $isFullPackage = $request->has('full_package');
        
        if ($isFullPackage) {
            $dailyRate = 4500.00; // Full package rate
        } else {
            $dailyRate = $dailyRates[$request->instrument_type] ?? 10.00;
        }
        
        // Calculate transportation fee
        $transportationFee = ($request->transportation === 'delivery') ? 550.00 : 0.00;
        $reservationFee = 300.00; // Reservation fee & security deposit
        $totalAmount = ($dailyRate * $durationDays) + $transportationFee + $reservationFee;

        // Check if instrument is available for the selected dates
        $conflictingRentals = InstrumentRental::where('instrument_name', $request->instrument_name)
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'returned')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('rental_start_date', [$startDate, $endDate])
                    ->orWhereBetween('rental_end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('rental_start_date', '<=', $startDate)
                            ->where('rental_end_date', '>=', $endDate);
                    });
            })
            ->first();

        if ($conflictingRentals) {
            return back()->with('error', 'This instrument is not available for the selected dates. Please choose different dates or a different instrument.');
        }

        // Handle image upload
        $receiptImagePath = null;
        if ($request->hasFile('picture')) {
            $receiptImagePath = $request->file('picture')->store('instrument-receipts', 'public');
        }

        // Use the 4-digit code from the form and ensure it's unique
        $fourDigitCode = $request->reference_code;
        
        // Check if this 4-digit code is already in use
        if (InstrumentRental::where('four_digit_code', $fourDigitCode)->exists()) {
            return back()->with('error', 'This 4-digit reference code is already in use. Please try a different code.');
        }

        $rental = InstrumentRental::create([
            'user_id' => Auth::id(),
            'instrument_type' => $request->instrument_type,
            'instrument_name' => $request->instrument_name,
            'rental_start_date' => $startDate,
            'rental_end_date' => $endDate,
            'rental_duration_days' => $durationDays,
            'daily_rate' => $dailyRate,
            'total_amount' => $totalAmount,
            'status' => 'pending',
            'four_digit_code' => $fourDigitCode,
            'notes' => $request->notes,
            'receipt_image' => $receiptImagePath,
            'pickup_location' => $request->pickup_location,
            'return_location' => $request->return_location,
            'transportation' => $request->transportation,
            'venue_type' => $request->venue_type,
            'event_duration_hours' => $request->event_duration_hours,
            'documentation_consent' => $request->has('documentation_consent'),
            'reservation_fee' => $reservationFee,
            'security_deposit' => $reservationFee,
        ]);

        // Add transportation and package info to notes
        $additionalNotes = [];
        if ($isFullPackage) {
            $additionalNotes[] = "Full Package Rental";
        }
        if ($request->transportation === 'delivery') {
            $additionalNotes[] = "Transportation Service: Delivery & Pickup (₱550)";
        }
        
        if (!empty($additionalNotes)) {
            $rental->update([
                'notes' => $request->notes . "\n" . implode("\n", $additionalNotes)
            ]);
        }

        Log::info('Instrument rental created successfully', [
            'id' => $rental->id,
            'reference' => $rental->reference,
            'instrument' => $rental->instrument_name,
            'user_id' => $rental->user_id,
            'total_amount' => $rental->total_amount
        ]);

        // Prepare detailed booking information for confirmation modal
        $bookingDetails = [
            'reference' => $rental->reference,
            'four_digit_code' => $rental->four_digit_code,
            'instrument_name' => $rental->instrument_name,
            'rental_start_date' => $rental->rental_start_date->format('Y-m-d'),
            'rental_end_date' => $rental->rental_end_date->format('Y-m-d'),
            'rental_duration_days' => $rental->rental_duration_days,
            'total_amount' => $rental->total_amount,
            'created_at' => $rental->created_at->format('Y-m-d H:i')
        ];

        return redirect('/instrument-rental')->with([
            'booking_confirmed' => true,
            'booking_details' => $bookingDetails
        ]);
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'instrument_name' => 'required|string',
            'rental_start_date' => 'required|date',
            'rental_end_date' => 'required|date|after:rental_start_date',
        ]);

        $startDate = Carbon::parse($request->rental_start_date);
        $endDate = Carbon::parse($request->rental_end_date);

        $conflictingRentals = InstrumentRental::where('instrument_name', $request->instrument_name)
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'returned')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('rental_start_date', [$startDate, $endDate])
                    ->orWhereBetween('rental_end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('rental_start_date', '<=', $startDate)
                            ->where('rental_end_date', '>=', $endDate);
                    });
            })
            ->first();

        if ($conflictingRentals) {
            return response()->json([
                'available' => false,
                'reason' => 'This instrument is not available for the selected dates'
            ]);
        }

        return response()->json(['available' => true]);
    }

    public function getUserRentals()
    {
        $rentals = InstrumentRental::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($rentals);
    }

    public function getByReference($reference)
    {
        $rental = InstrumentRental::with('user')->where('reference', $reference)->first();

        if (!$rental) {
            return response()->json(['error' => 'Rental not found'], 404);
        }

        return response()->json($rental);
    }

    public function cancelByReference($reference)
    {
        $rental = InstrumentRental::where('reference', $reference)
            ->where('user_id', Auth::id())
            ->first();

        if (!$rental) {
            return response()->json(['error' => 'Rental not found or unauthorized'], 404);
        }

        if ($rental->status !== 'pending') {
            return response()->json(['error' => 'Cannot cancel rental that is not pending'], 400);
        }

        $rental->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Rental cancelled successfully'
        ]);
    }

    public function updateStatus(Request $request, $reference)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,active,returned,cancelled',
        ]);

        $rental = InstrumentRental::where('reference', $reference)->first();

        if (!$rental) {
            return response()->json(['error' => 'Rental not found'], 404);
        }

        $rental->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Rental status updated successfully'
        ]);
    }

    public function getInstrumentsByType(Request $request)
    {
        $instrumentType = $request->query('type');
        $availableInstruments = InstrumentRental::getAvailableInstruments();

        if (!isset($availableInstruments[$instrumentType])) {
            return response()->json(['instruments' => []]);
        }

        return response()->json(['instruments' => $availableInstruments[$instrumentType]]);
    }

    public function getDailyRate(Request $request)
    {
        $instrumentType = $request->query('type');
        $dailyRates = InstrumentRental::getDailyRates();

        $dailyRate = $dailyRates[$instrumentType] ?? 10.00;

        return response()->json(['daily_rate' => $dailyRate]);
    }
}
