<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InstrumentRental;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\InstrumentRentalNotification;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class InstrumentRentalController extends Controller
{
    public function index()
    {
        // Define instrument types and their available instruments
        $instrumentTypes = [
            'guitar' => 'Guitar',
            'bass' => 'Bass Guitar',
            'drums' => 'Drums',
            'keyboard' => 'Keyboard/Piano',
            'microphone' => 'Microphone',
            'amplifier' => 'Amplifier',
            'mixer' => 'Audio Mixer',
            'speakers' => 'Speakers',
            'recording' => 'Recording Equipment'
        ];

        // Define available instruments for each type
        $availableInstruments = [
            'guitar' => [
                'acoustic_guitar' => 'Acoustic Guitar',
                'electric_guitar' => 'Electric Guitar',
                'classical_guitar' => 'Classical Guitar'
            ],
            'bass' => [
                'electric_bass' => 'Electric Bass',
                'acoustic_bass' => 'Acoustic Bass'
            ],
            'drums' => [
                'full_drum_kit' => 'Full Drum Kit',
                'electronic_drums' => 'Electronic Drums',
                'cajon' => 'Cajon'
            ],
            'keyboard' => [
                'digital_piano' => 'Digital Piano',
                'synthesizer' => 'Synthesizer',
                'midi_keyboard' => 'MIDI Keyboard'
            ],
            'microphone' => [
                'vocal_mic' => 'Vocal Microphone',
                'instrument_mic' => 'Instrument Microphone',
                'condenser_mic' => 'Condenser Microphone'
            ],
            'amplifier' => [
                'guitar_amp' => 'Guitar Amplifier',
                'bass_amp' => 'Bass Amplifier',
                'keyboard_amp' => 'Keyboard Amplifier'
            ],
            'mixer' => [
                'analog_mixer' => 'Analog Mixer',
                'digital_mixer' => 'Digital Mixer'
            ],
            'speakers' => [
                'monitor_speakers' => 'Monitor Speakers',
                'pa_speakers' => 'PA Speakers'
            ],
            'recording' => [
                'audio_interface' => 'Audio Interface',
                'headphones' => 'Studio Headphones',
                'pop_filter' => 'Pop Filter'
            ]
        ];

        // Define daily rates for each instrument type
        $dailyRates = [
            'guitar' => 15,
            'bass' => 15,
            'drums' => 25,
            'keyboard' => 20,
            'microphone' => 10,
            'amplifier' => 18,
            'mixer' => 22,
            'speakers' => 20,
            'recording' => 12
        ];

        return view('instrument-rental', compact('instrumentTypes', 'availableInstruments', 'dailyRates'));
    }

    public function store(Request $request)
    {
        try {
            // Validate the request
            $validatedData = $request->validate([
                'instrument_type' => 'required|string|max:255',
                'instrument_name' => 'required|string|max:255',
                'rental_start_date' => 'required|date|after_or_equal:today',
                'rental_end_date' => 'required|date|after:rental_start_date',
                'full_package' => 'boolean',
                'pickup_location' => 'required|string|max:255',
                'notes' => 'nullable|string|max:1000', // Changed from 'special_requests' to 'notes' to match form field
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'reference_number' => 'nullable|string|max:50'
            ]);

            // Calculate rental duration
            $startDate = new \DateTime($validatedData['rental_start_date']);
            $endDate = new \DateTime($validatedData['rental_end_date']);
            $duration = $startDate->diff($endDate)->days;

            // Set daily rate (you can adjust this based on instrument type)
            $dailyRate = 500.00; // Default rate
            $totalAmount = $duration * $dailyRate;

            // Generate reference and four digit code if not provided
            $reference = !empty($validatedData['reference_number']) ? $validatedData['reference_number'] : 'IR' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $fourDigitCode = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

            // Prepare data for model (map field names to match model fillable fields)
            $rentalData = [
                'user_id' => Auth::id() ?? 1, // Use authenticated user or default
                'instrument_type' => $validatedData['instrument_type'],
                'instrument_name' => $validatedData['instrument_name'],
                'rental_start_date' => $validatedData['rental_start_date'],
                'rental_end_date' => $validatedData['rental_end_date'],
                'rental_duration_days' => $duration,
                'daily_rate' => $dailyRate,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'reference' => $reference, // Model expects 'reference', not 'reference_number'
                'four_digit_code' => $fourDigitCode,
                'notes' => $validatedData['notes'], // Form sends 'notes' and model expects 'notes'
                'pickup_location' => $validatedData['pickup_location'],
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
            ];

            // Create the rental record
            $rental = InstrumentRental::create($rentalData);

            // Send notification email to admin
            try {
                $adminEmail = env('ADMIN_EMAIL', 'admin@lemonhubstudio.com');
                
                Mail::to($adminEmail)->send(new InstrumentRentalNotification($rental));
                
                Log::info('Instrument rental notification sent successfully', [
                    'reference' => $rental->reference,
                    'admin_email' => $adminEmail
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send instrument rental notification email', [
                    'error' => $e->getMessage(),
                    'reference' => $rental->reference
                ]);
            }

            // Check if this is an AJAX request
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Instrument rental request submitted successfully!',
                    'reference' => $rental->reference,
                    'rental_id' => $rental->id
                ]);
            }

            // For regular form submission, redirect with success message
             return redirect()->route('instrument-rental.index')->with([
                 'booking_confirmed' => true,
                 'reference' => $rental->reference,
                 'rental_id' => $rental->id,
                 'message' => 'Instrument rental request submitted successfully!',
                 'booking_details' => [
                     'rental_start_date' => $rental->rental_start_date,
                     'rental_duration_days' => $rental->rental_duration_days,
                     'reference' => $rental->reference,
                     'created_at' => $rental->created_at->format('M d, Y g:i A')
                 ]
             ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Instrument rental submission failed', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request. Please try again.'
            ], 500);
        }
    }

    public function checkAvailability(Request $request)
    {
        try {
            $request->validate([
                'instrument_type' => 'required|string',
                'instrument_name' => 'required|string',
                'rental_start_date' => 'required|date',
                'rental_end_date' => 'required|date|after:rental_start_date'
            ]);

            // Check for conflicting rentals
            $conflicts = InstrumentRental::where('instrument_type', $request->instrument_type)
                ->where('instrument_name', $request->instrument_name)
                ->where('status', '!=', 'cancelled')
                ->where(function ($query) use ($request) {
                    $query->whereBetween('rental_start_date', [$request->rental_start_date, $request->rental_end_date])
                          ->orWhereBetween('rental_end_date', [$request->rental_start_date, $request->rental_end_date])
                          ->orWhere(function ($q) use ($request) {
                              $q->where('rental_start_date', '<=', $request->rental_start_date)
                                ->where('rental_end_date', '>=', $request->rental_end_date);
                          });
                })
                ->exists();

            return response()->json([
                'available' => !$conflicts,
                'message' => $conflicts ? 'This instrument is not available for the selected dates.' : 'Instrument is available for the selected dates.'
            ]);

        } catch (\Exception $e) {
            Log::error('Availability check failed', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'available' => false,
                'message' => 'Unable to check availability. Please try again.'
            ], 500);
        }
    }

    /**
     * Return all booked dates for instrument rentals as an array of date strings (YYYY-MM-DD).
     * Used by the frontend to disable unavailable dates in the date picker.
     */
    public function getBookedDates()
    {
        try {
            // Get all confirmed and pending instrument rental dates
            $instrumentRentals = InstrumentRental::whereIn('status', ['pending', 'confirmed'])
                ->get(['rental_start_date', 'rental_end_date']);

            $bookedDates = [];
            
            foreach ($instrumentRentals as $rental) {
                $startDate = Carbon::parse($rental->rental_start_date);
                $endDate = Carbon::parse($rental->rental_end_date);
                
                // Add all dates in the rental period
                $currentDate = $startDate->copy();
                while ($currentDate->lte($endDate)) {
                    $bookedDates[] = $currentDate->format('Y-m-d');
                    $currentDate->addDay();
                }
            }

            // Remove duplicates and sort
            $bookedDates = array_unique($bookedDates);
            sort($bookedDates);

            return response()->json([
                'booked_dates' => array_values($bookedDates)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to fetch booked dates', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'booked_dates' => [],
                'message' => 'Unable to fetch booked dates.'
            ], 500);
        }
    }

    /**
     * Get instrument rental bookings for a specific date.
     * Used by the calendar to display booking information.
     */
    public function getBookingsByDate(Request $request)
    {
        try {
            $request->validate([
                'date' => 'required|date_format:Y-m-d'
            ]);

            $date = $request->input('date');
            
            // Get all instrument rentals that include this date
            $rentals = InstrumentRental::whereIn('status', ['pending', 'confirmed'])
                ->where('rental_start_date', '<=', $date)
                ->where('rental_end_date', '>=', $date)
                ->with('user:id,name,email')
                ->get();

            $bookings = [];
            
            foreach ($rentals as $rental) {
                $bookings[] = [
                    'id' => $rental->id,
                    'instrument_type' => $rental->instrument_type,
                    'instrument_name' => $rental->instrument_name,
                    'customer_name' => $rental->user ? $rental->user->name : $rental->customer_name,
                    'customer_email' => $rental->user ? $rental->user->email : $rental->customer_email,
                    'rental_start_date' => $rental->rental_start_date,
                    'rental_end_date' => $rental->rental_end_date,
                    'total_cost' => $rental->total_cost,
                    'status' => $rental->status,
                    'pickup_location' => $rental->pickup_location,
                    'return_location' => $rental->return_location,
                    'transportation' => $rental->transportation,
                    'special_requests' => $rental->special_requests,
                    'created_at' => $rental->created_at->format('Y-m-d H:i:s')
                ];
            }

            return response()->json([
                'success' => true,
                'bookings' => $bookings,
                'date' => $date,
                'total_bookings' => count($bookings)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to fetch bookings by date', [
                'error' => $e->getMessage(),
                'date' => $request->input('date')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch bookings for this date.',
                'bookings' => []
            ], 500);
        }
    }
}