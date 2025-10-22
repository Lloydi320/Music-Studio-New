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
        // Define instrument types and their available instruments based on provided list
        $instrumentTypes = [
            'full_package' => 'Full Package',
            'drums' => 'Drum Set',
            'amplifier' => 'Amplifiers',
            'keyboard' => 'Keyboard',
            'guitar' => 'Electric Guitar',
            'bass' => 'Bass Guitar',
            'accessories' => 'Accessories'
        ];

        // Define available instruments for each type (exact items from the list)
        $availableInstruments = [
            'full_package' => [
                // No specific instrument selection needed
            ],
            'drums' => [
                'yamaha_manu_katche_jungle_kit' => 'Drum Set - Yamaha Manu Katche (Jungle Kit)'
            ],
            'amplifier' => [
                'fender_champion_100' => 'Guitar Amp - Fender Champion 100',
                'fender_rumble_100' => 'Bass Amp - Fender Rumble 100',
                'peavey_bandit_80_100' => 'Guitar Amp - Peavey Bandit 80/100',
                'avatar_dm50' => 'Keyboard/Acoustic Guitar Amp - Avatar DM50'
            ],
            'keyboard' => [
                'roland_go_keys_go61k' => 'Keyboard - Roland GO:KEYS (GO-61K)'
            ],
            'guitar' => [
                'electric_guitar' => 'Electric Guitar'
            ],
            'bass' => [
                'bass_guitar' => 'Bass Guitar'
            ],
            'accessories' => [
                'guitar_cable' => 'Guitar Cable'
            ]
        ];

        // Define instrument-specific daily rates (used when a specific instrument is chosen)
        $instrumentRates = [
            'yamaha_manu_katche_jungle_kit' => 1500,
            'fender_champion_100' => 900,
            'fender_rumble_100' => 900,
            'peavey_bandit_80_100' => 900,
            'avatar_dm50' => 750,
            'roland_go_keys_go61k' => 750,
            'electric_guitar' => 500,
            'bass_guitar' => 550,
            'guitar_cable' => 50,
        ];

        // Define fallback daily rates per type (used when type chosen but specific instrument not yet selected)
        $dailyRates = [
            'full_package' => 4500,
            'drums' => 1500,
            'amplifier' => 0, // require specific amplifier selection for accurate rate
            'keyboard' => 750,
            'guitar' => 500,
            'bass' => 550,
            'accessories' => 50,
        ];

        return view('instrument-rental', compact('instrumentTypes', 'availableInstruments', 'dailyRates', 'instrumentRates'));
    }

    public function store(Request $request)
    {
        try {
            // Validate the request
            $validatedData = $request->validate([
                'instrument_type' => 'required|string|max:255',
                'instrument_name' => 'required|string|max:255',
                // Enforce 24-hour lead time: start date must be after today
                'rental_start_date' => 'required|date|after:today',
                'rental_end_date' => 'required|date|after_or_equal:rental_start_date',
                'full_package' => 'boolean',
                'pickup_location' => 'required|string|max:255',
                'return_location' => 'required|string|max:255',
                'transportation' => 'nullable|string|max:50',
                'delivery_time' => 'nullable|date_format:H:i',
                'pickup_time' => 'nullable|date_format:H:i',
                'event_duration_hours' => 'nullable|integer|min:1|max:24',
                'notes' => 'nullable|string|max:1000', // Changed from 'special_requests' to 'notes' to match form field
                'name' => 'required|string|max:255',
                // Email is taken from authenticated user; allow nullable in request
                'email' => 'nullable|email|max:255',
                'phone' => ['required','string','size:11','regex:/^09[0-9]{9}$/'],
                'reference_number' => 'nullable|string|max:50',
                'picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120' // Required picture upload, max 5MB
            ]);

            // Calculate rental duration (minimum of 1 day)
            $startDate = new \DateTime($validatedData['rental_start_date']);
            $endDate = new \DateTime($validatedData['rental_end_date']);
            $duration = max(1, $startDate->diff($endDate)->days);

            // Enforce 24-hour lead time (server-side safety)
            $nowManila = Carbon::now(config('app.timezone', 'Asia/Manila'));
            $startDateCarbon = Carbon::parse($validatedData['rental_start_date'], config('app.timezone', 'Asia/Manila'));
            if ($startDateCarbon->lte($nowManila->endOfDay())) {
                return back()->withErrors(['rental_start_date' => 'Rentals must be booked at least 24 hours in advance (no same-day rentals).'])->withInput();
            }

            // Single-day rentals: allow beyond closing if no time conflicts with studio bookings
            $endDateCarbon = Carbon::parse($validatedData['rental_end_date'], config('app.timezone', 'Asia/Manila'));
            $isSingleDay = $startDateCarbon->isSameDay($endDateCarbon);
            if ($isSingleDay) {
                $eventHours = isset($validatedData['event_duration_hours']) ? (int)$validatedData['event_duration_hours'] : null;
                $startTime = (($validatedData['transportation'] ?? 'none') === 'delivery') ? ($validatedData['delivery_time'] ?? null) : ($validatedData['pickup_time'] ?? null);

                if ($startTime && $eventHours) {
                    $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $validatedData['rental_start_date'].' '.$startTime, config('app.timezone', 'Asia/Manila'));
                    $endDateTime = (clone $startDateTime)->addHours($eventHours);

                    $existingBookings = \App\Models\Booking::whereDate('date', $startDateCarbon->format('Y-m-d'))
                        ->whereIn('status', ['pending', 'confirmed'])
                        ->get();

                    foreach ($existingBookings as $existingBooking) {
                        $existingStartStr = trim(explode('-', $existingBooking->time_slot)[0]);
                        $existingStart = Carbon::createFromFormat('h:i A', $existingStartStr, config('app.timezone', 'Asia/Manila'));
                        $existingStart->setDate($startDateTime->year, $startDateTime->month, $startDateTime->day);
                        $existingEnd = (clone $existingStart)->addHours($existingBooking->duration);

                        if (($startDateTime < $existingEnd && $endDateTime > $existingStart) || ($existingStart < $endDateTime && $existingEnd > $startDateTime)) {
                            return back()->withErrors(['event_duration_hours' => 'Selected time overlaps with an existing studio booking. Please choose a different time or date.'])->withInput();
                        }
                    }
                }
            }

            // Set daily rate (you can adjust this based on instrument type)
            $dailyRate = 500.00; // Default rate
            $totalAmount = $duration * $dailyRate;

            // Always generate IR rental reference; store user-entered payment reference separately
            $fourDigitCode = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

            // Prepare data for model (map field names to match model fillable fields)
            // Prefer authenticated user's email over any provided value (handle guest users safely)
            $authenticatedEmail = Auth::check() ? Auth::user()->email : null;

            // Handle picture upload
            $receiptImagePath = null;
            if ($request->hasFile('picture')) {
                $receiptImagePath = $request->file('picture')->store('rental_receipts', 'public');
            }

            // Determine if full package was selected and compute down payment
            $isFullPackage = (bool) ($validatedData['full_package'] ?? false);
            if (!$isFullPackage) {
                $typeLower = strtolower($validatedData['instrument_type']);
                $isFullPackage = ($typeLower === 'full package' || $typeLower === 'full_package');
            }
            $downPayment = $isFullPackage ? 500.00 : 300.00;

            $rentalData = [
                'user_id' => Auth::id(), // Auth middleware ensures user_id is present
                'instrument_type' => $validatedData['instrument_type'],
                'instrument_name' => $validatedData['instrument_name'],
                'rental_start_date' => $validatedData['rental_start_date'],
                'rental_end_date' => $validatedData['rental_end_date'],
                'rental_duration_days' => $duration,
                'daily_rate' => $dailyRate,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'reference' => InstrumentRental::generateUniqueReference(),
                'four_digit_code' => $fourDigitCode,
                'payment_reference' => $validatedData['reference_number'] ?? null,
                'notes' => $validatedData['notes'] ?? null, // Form sends 'notes', allow null if omitted
                'pickup_location' => $validatedData['pickup_location'],
                'return_location' => $validatedData['return_location'],
                'transportation' => $validatedData['transportation'] ?? null,
                'delivery_time' => (($validatedData['transportation'] ?? null) === 'delivery') ? ($validatedData['delivery_time'] ?? null) : ($validatedData['pickup_time'] ?? null),
                'event_duration_hours' => $validatedData['event_duration_hours'] ?? null,
                'name' => $validatedData['name'],
                'email' => $authenticatedEmail ?? ($validatedData['email'] ?? null),
                'phone' => $validatedData['phone'],
                'receipt_image' => $receiptImagePath, // Store the uploaded receipt image path
                // Persist down payment fields
                'reservation_fee' => $downPayment,
                'security_deposit' => $downPayment,
            ];

            // Enforce closing-time rules for single-day rentals
            // If transportation is delivery, ensure delivery_time + event_duration <= 20:00 of start date
            if (($validatedData['transportation'] ?? null) === 'delivery' && $isSingleDay) {
                $deliveryTime = $validatedData['delivery_time'] ?? null;

                if ($deliveryTime && $deliveryTime < '08:00') {
                    return back()->withErrors(['delivery_time' => 'Delivery time must be at or after 8:00 AM.'])->withInput();
                }
                // No strict 8:00 PM closing-time enforcement here; time-overlap conflicts are handled above.
            }

            // Create the rental record
            $rental = InstrumentRental::create($rentalData);

            // Send notification email to admin(s)
            try {
                // Prefer sending to all admin users; fallback to ADMIN_EMAIL env
                $adminUsers = User::where('is_admin', true)->get();

                if ($adminUsers->isEmpty()) {
                    $fallbackEmail = env('ADMIN_EMAIL', 'admin@lemonhubstudio.com');
                    Mail::to($fallbackEmail)->send(new InstrumentRentalNotification($rental, 'admin'));

                    Log::info('Instrument rental notification sent to fallback admin email', [
                        'reference' => $rental->reference,
                        'admin_email' => $fallbackEmail
                    ]);
                } else {
                    foreach ($adminUsers as $admin) {
                        Mail::to($admin->email)->send(new InstrumentRentalNotification($rental, 'admin'));

                        Log::info('Instrument rental notification sent to admin', [
                            'reference' => $rental->reference,
                            'admin_email' => $admin->email
                        ]);
                    }
                }

                // Send confirmation email to the customer (user)
                $customerEmail = optional($rental->user)->email ?? $rental->email;
                if (!empty($customerEmail)) {
                    Mail::to($customerEmail)->send(new InstrumentRentalNotification($rental, 'user'));

                    Log::info('Instrument rental confirmation sent to customer', [
                        'reference' => $rental->reference,
                        'customer_email' => $customerEmail
                    ]);
                } else {
                    Log::warning('Customer email missing; skipping instrument rental confirmation', [
                        'reference' => $rental->reference
                    ]);
                }
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
                    'payment_reference' => $rental->payment_reference,
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
                    'payment_reference' => $rental->payment_reference,
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

            $response = [
                'success' => false,
                'message' => 'An error occurred while processing your request. Please try again.'
            ];
            if (config('app.debug')) {
                $response['error'] = $e->getMessage();
                $response['exception'] = get_class($e);
                $response['file'] = $e->getFile();
                $response['line'] = $e->getLine();
            }

            return response()->json($response, 500);
        }
    }

    public function checkAvailability(Request $request)
    {
        try {
            $request->validate([
                'instrument_type' => 'required|string',
                'instrument_name' => 'required|string',
                'rental_start_date' => 'required|date',
                'rental_end_date' => 'required|date|after_or_equal:rental_start_date'
            ]);

            // Lead-time rule: start date must be after today
            $nowManila = Carbon::now(config('app.timezone', 'Asia/Manila'));
            $start = Carbon::parse($request->rental_start_date, config('app.timezone', 'Asia/Manila'));
            $end = Carbon::parse($request->rental_end_date, config('app.timezone', 'Asia/Manila'));

            if ($start->lte($nowManila->endOfDay())) {
                return response()->json([
                    'available' => false,
                    'message' => 'Rentals must be booked at least 24 hours in advance.'
                ], 422);
            }

            // If single-day, block days that have any studio booking
            // Single-day rentals are allowed; rely on time-overlap checks during creation for conflicts.

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