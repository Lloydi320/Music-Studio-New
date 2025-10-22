<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\ActivityLog;
use App\Models\RescheduleRequest;
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
        // First, validate all fields including reference code uniqueness
        $request->validate([
            'date' => 'required|date',
            'time_slot' => 'required|string',
            'duration' => 'required|integer|min:1|max:8',
            // Accept band_rehearsal alias and normalize later
            'service_type' => 'nullable|string|in:studio_rental,band_rehearsal,solo_rehearsal,instrument_rental',
            'band_name' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|size:11|regex:/^[0-9]{11}$/',
            'reference_code' => 'nullable|string|regex:/^[0-9]{13}$/|unique:bookings,reference_code',
            'upload_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'reference_code.unique' => 'Reference number "' . $request->reference_code . '" already exists. Please use a different reference number from GCash to proceed booking.'
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
        
        // Check if drums or full package are rented on this date (studio unavailable)
        $drumOrFullPackageRentals = \App\Models\InstrumentRental::whereIn('status', ['pending', 'confirmed'])
            ->where(function($query) {
                $query->where('instrument_type', 'drums')
                      ->orWhere('instrument_type', 'Full Package');
            })
            ->where(function($query) use ($request) {
                $query->where('rental_start_date', '<=', $request->date)
                      ->where('rental_end_date', '>=', $request->date);
            })
            ->exists();

        if ($drumOrFullPackageRentals) {
            $errorMessage = 'Studio is unavailable on this date due to a Full Package or drum rental. Please choose a different date.';
            
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 422);
            }
            
            return back()->with('error', $errorMessage);
        }

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
                $errorMessage = 'This time slot overlaps with an existing booking. Please choose a different time.';
                
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 422);
                }
                
                return back()->with('error', $errorMessage);
            }
        }
    
        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('upload_picture')) {
            $image = $request->file('upload_picture');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('booking_images', $imageName, 'public');
        }

        // Calculate pricing based on service type (normalize alias "band_rehearsal" to studio_rental)
        $serviceType = $request->service_type ?? 'studio_rental';
        if ($serviceType === 'band_rehearsal') {
            $serviceType = 'studio_rental';
        }
        $hourlyRate = ($serviceType === 'solo_rehearsal') ? 300.00 : 250.00; // ₱300 for solo rehearsal, ₱250 for studio rental
        $totalAmount = $hourlyRate * $duration;
        
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'date' => $request->date,
            'time_slot' => $request->time_slot,
            'duration' => $duration,
            'price' => $hourlyRate,
            'total_amount' => $totalAmount,
            'service_type' => $serviceType, // normalized
            'band_name' => $request->band_name,
            'email' => Auth::user()->email, // Use authenticated user's email
            'contact_number' => $request->contact_number,
            'reference_code' => $request->reference_code,
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
    
        $successMessage = 'Booking confirmed! Your session on ' . $booking->date . ' at ' . $booking->time_slot . ' for ' . $booking->duration . ' hours has been booked. Reference: ' . $booking->reference . '. You will receive an email confirmation shortly.';
        
        // Check if this is an AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'booking' => [
                    'id' => $booking->id,
                    'reference' => $booking->reference,
                    'date' => $booking->date,
                    'time_slot' => $booking->time_slot,
                    'duration' => $booking->duration,
                    'total_amount' => $booking->total_amount
                ]
            ]);
        }
        
        return back()->with('success', $successMessage);
    
    }

    public function getByDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);
        
        // Parse the date with proper timezone handling to ensure consistent querying
        $queryDate = \Carbon\Carbon::parse($request->date)->setTimezone(config('app.timezone', 'Asia/Manila'))->format('Y-m-d');
        
        $bookings = Booking::whereDate('date', $queryDate)
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
        
        // Check if drums or full package are rented on this date (studio unavailable)
        $drumOrFullPackageRentals = \App\Models\InstrumentRental::whereIn('status', ['pending', 'confirmed'])
            ->where(function($query) {
                $query->where('instrument_type', 'drums')
                      ->orWhere('instrument_type', 'Full Package');
            })
            ->where(function($query) use ($request) {
                $query->where('rental_start_date', '<=', $request->date)
                      ->where('rental_end_date', '>=', $request->date);
            })
            ->exists();

        if ($drumOrFullPackageRentals) {
            return response()->json(['available' => false, 'reason' => 'Studio unavailable due to drum/full package rental']);
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

    public function rescheduleRequest(Request $request)
    {
        // Determine booking type and validate accordingly
        $bookingType = $request->input('booking_type', 'studio_rental');
        
        if ($bookingType === 'studio_rental') {
            $request->validate([
                'reference_number' => 'required|string|regex:/^[0-9]{13}$/',
                'new_date' => 'required|date|after_or_equal:today',
                'new_time_slot' => 'required|string',
                'duration' => 'required|integer|min:1|max:8'
            ]);
        } else {
            $request->validate([
                'reference_number' => 'required|string|regex:/^[0-9]{13}$/',
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after_or_equal:start_date'
            ]);
        }

        try {
            $booking = null;
            $rental = null;
            
            if ($bookingType === 'studio_rental') {
                // Find studio booking by reference code
                $booking = Booking::where('reference_code', $request->reference_number)
                                 ->whereIn('status', ['pending', 'confirmed'])
                                 ->first();
                
                if (!$booking) {
                    return response()->json(['error' => 'Studio booking not found'], 404);
                }

                // Enforce studio closing time: end must be <= 8:00 PM
                $startPart = trim(explode('-', $request->new_time_slot)[0]);
                try {
                    $newStartTime = \Carbon\Carbon::createFromFormat('h:i A', $startPart, config('app.timezone', 'Asia/Manila'));
                } catch (\Exception $e) {
                    $newStartTime = \Carbon\Carbon::createFromFormat('H:i', $startPart, config('app.timezone', 'Asia/Manila'));
                }
                $newEndTime = $newStartTime->copy()->addHours((int)$request->duration);
                if ($newEndTime->hour > 20 || ($newEndTime->hour === 20 && $newEndTime->minute > 0)) {
                    return response()->json(['error' => 'Selected time exceeds studio closing time (8:00 PM)'], 422);
                }

                // Check for time slot conflicts
                $conflictingBooking = Booking::where('date', $request->new_date)
                    ->where('time_slot', $request->new_time_slot)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->where('id', '!=', $booking->id)
                    ->first();
                    
                if ($conflictingBooking) {
                    return response()->json(['error' => 'The selected time slot is already booked'], 409);
                }

                // Create reschedule request for studio booking
                $rescheduleRequest = RescheduleRequest::create([
                    'resource_type' => RescheduleRequest::RESOURCE_BOOKING,
                    'resource_id' => $booking->id,
                    'user_id' => $booking->user_id,
                    'customer_name' => $booking->band_name,
                    'customer_email' => $booking->email,
                    'original_data' => $booking->toArray(),
                    'requested_data' => [
                        'new_date' => $request->new_date,
                        'new_time_slot' => $request->new_time_slot,
                        'new_duration' => $request->duration,
                        'booking_type' => 'studio_rental'
                    ],
                    'original_date' => $booking->date,
                    'original_time_slot' => $booking->time_slot,
                    'original_duration' => $booking->duration,
                    'requested_date' => $request->new_date,
                    'requested_time_slot' => $request->new_time_slot,
                    'requested_duration' => $request->duration,
                    'reason' => $request->reason,
                    'has_conflict' => $conflictingBooking !== null,
                    'conflict_details' => $conflictingBooking ? [
                        'conflicting_booking_id' => $conflictingBooking->id,
                        'conflicting_booking_reference' => $conflictingBooking->reference
                    ] : null,
                    'priority' => RescheduleRequest::PRIORITY_MEDIUM
                ]);

                // Log the reschedule request creation
                ActivityLog::logBooking(
                    'Reschedule Request Submitted: Studio Booking - ' . $rescheduleRequest->reference,
                    $booking,
                    $booking->toArray(),
                    $rescheduleRequest->requested_data
                );

                // Send notification to admin
                $this->notifyAdminOfReschedule($booking, $rescheduleRequest->requested_data);
                // Send confirmation to customer
                $this->notifyUserOfReschedule($booking, $rescheduleRequest->requested_data);
                
            } else {
                // Find instrument rental by reference code
                $rental = \App\Models\InstrumentRental::where('four_digit_code', $request->reference_number)
                                                     ->orWhere('payment_reference', $request->reference_number)
                                                     ->whereIn('status', ['pending', 'confirmed'])
                                                     ->first();
                
                if (!$rental) {
                    return response()->json(['error' => 'Instrument rental not found'], 404);
                }

                // Create reschedule request for instrument rental
                $rescheduleRequest = RescheduleRequest::create([
                    'resource_type' => RescheduleRequest::RESOURCE_INSTRUMENT_RENTAL,
                    'resource_id' => $rental->id,
                    'user_id' => $rental->user_id,
                    'customer_name' => $rental->user->name ?? 'Unknown Customer',
                    'customer_email' => $rental->user->email ?? 'unknown@example.com',
                    'original_data' => $rental->toArray(),
                    'requested_data' => [
                        'new_start_date' => $request->start_date,
                        'new_end_date' => $request->end_date,
                        'new_duration' => \Carbon\Carbon::parse($request->start_date)->diffInDays(\Carbon\Carbon::parse($request->end_date)) + 1,
                        'booking_type' => 'instrument_rental'
                    ],
                    'original_start_date' => $rental->rental_start_date,
                    'original_end_date' => $rental->rental_end_date,
                    'requested_start_date' => $request->start_date,
                    'requested_end_date' => $request->end_date,
                    'reason' => $request->reason,
                    'priority' => RescheduleRequest::PRIORITY_MEDIUM
                ]);

                // Log reschedule request for instrument rental
                ActivityLog::create([
                    'description' => 'Reschedule Request Submitted: Instrument Rental - ' . $rescheduleRequest->reference,
                    'resource_type' => 'App\\Models\\InstrumentRental',
                    'resource_id' => $rental->id,
                    'old_values' => $rental->toArray(),
                    'new_values' => $rescheduleRequest->requested_data,
                    'user_id' => $rental->user_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Send notification to admin for instrument rental
                $this->notifyAdminOfInstrumentReschedule($rental, $rescheduleRequest->requested_data);
                // Send confirmation to customer
                $this->notifyUserOfInstrumentReschedule($rental, $rescheduleRequest->requested_data);
            }

            return response()->json([
                'success' => true,
                'message' => 'Reschedule request submitted successfully. Admin will review and approve your request.'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to submit reschedule request', [
                'booking_type' => $bookingType,
                'reference' => $request->input('reference_number'),
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Failed to submit reschedule request: ' . $e->getMessage()], 500);
        }
    }

    public function rescheduleByReference(Request $request, $reference)
    {
        // Determine booking type and validate accordingly
        $bookingType = $request->input('booking_type', 'studio_rental');
        
        if ($bookingType === 'studio_rental') {
            $request->validate([
                'reference_number' => 'required|string|regex:/^[0-9]{13}$/',
                'new_date' => 'required|date|after_or_equal:today',
                'new_time_slot' => 'required|string',
                'duration' => 'required|integer|min:1|max:8'
            ]);
        } else {
            $request->validate([
                'reference_number' => 'required|string|regex:/^[0-9]{13}$/',
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after:start_date'
            ]);
        }

        try {
            $booking = null;
            $rental = null;
            
            if ($bookingType === 'studio_rental') {
                // Find studio booking by reference code
                $booking = Booking::where('reference_code', $request->reference_number)
                                 ->whereIn('status', ['pending', 'confirmed'])
                                 ->first();
                
                if (!$booking) {
                    return response()->json(['error' => 'Studio booking not found'], 404);
                }

                // Enforce studio closing time: end must be <= 8:00 PM
                $startPart = trim(explode('-', $request->new_time_slot)[0]);
                try {
                    $newStartTime = \Carbon\Carbon::createFromFormat('h:i A', $startPart, config('app.timezone', 'Asia/Manila'));
                } catch (\Exception $e) {
                    $newStartTime = \Carbon\Carbon::createFromFormat('H:i', $startPart, config('app.timezone', 'Asia/Manila'));
                }
                $newEndTime = $newStartTime->copy()->addHours((int)$request->duration);
                if ($newEndTime->hour > 20 || ($newEndTime->hour === 20 && $newEndTime->minute > 0)) {
                    return response()->json(['error' => 'Selected time exceeds studio closing time (8:00 PM)'], 422);
                }

                // Check for time slot conflicts
                $conflictingBooking = Booking::where('date', $request->new_date)
                    ->where('time_slot', $request->new_time_slot)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->where('id', '!=', $booking->id)
                    ->first();
                    
                if ($conflictingBooking) {
                    return response()->json(['error' => 'The selected time slot is already booked'], 409);
                }

                // Create reschedule request for studio booking
                $rescheduleRequest = RescheduleRequest::create([
                    'resource_type' => RescheduleRequest::RESOURCE_BOOKING,
                    'resource_id' => $booking->id,
                    'user_id' => $booking->user_id,
                    'customer_name' => $booking->band_name,
                    'customer_email' => $booking->email,
                    'original_data' => $booking->toArray(),
                    'requested_data' => [
                        'new_date' => $request->new_date,
                        'new_time_slot' => $request->new_time_slot,
                        'new_duration' => $request->duration,
                        'booking_type' => 'studio_rental'
                    ],
                    'original_date' => $booking->date,
                    'original_time_slot' => $booking->time_slot,
                    'original_duration' => $booking->duration,
                    'requested_date' => $request->new_date,
                    'requested_time_slot' => $request->new_time_slot,
                    'requested_duration' => $request->duration,
                    'reason' => $request->reason,
                    'has_conflict' => $conflictingBooking !== null,
                    'conflict_details' => $conflictingBooking ? [
                        'conflicting_booking_id' => $conflictingBooking->id,
                        'conflicting_booking_reference' => $conflictingBooking->reference
                    ] : null,
                    'priority' => RescheduleRequest::PRIORITY_MEDIUM
                ]);

                // Log the reschedule request creation
                ActivityLog::logBooking(
                    'Reschedule Request Submitted: Studio Booking - ' . $rescheduleRequest->reference,
                    $booking,
                    $booking->toArray(),
                    $rescheduleRequest->requested_data
                );

                // Send notification to admin
                $this->notifyAdminOfReschedule($booking, $rescheduleRequest->requested_data);
                
            } else {
                // Find instrument rental by reference code
                $rental = \App\Models\InstrumentRental::where('four_digit_code', $request->reference_number)
                                                     ->orWhere('payment_reference', $request->reference_number)
                                                     ->whereIn('status', ['pending', 'confirmed'])
                                                     ->first();
                
                if (!$rental) {
                    return response()->json(['error' => 'Instrument rental not found'], 404);
                }

                // Create reschedule request for instrument rental
                $rescheduleRequest = RescheduleRequest::create([
                    'resource_type' => RescheduleRequest::RESOURCE_INSTRUMENT_RENTAL,
                    'resource_id' => $rental->id,
                    'user_id' => $rental->user_id,
                    'customer_name' => $rental->user->name ?? 'Unknown Customer',
                    'customer_email' => $rental->user->email ?? 'unknown@example.com',
                    'original_data' => $rental->toArray(),
                    'requested_data' => [
                        'new_start_date' => $request->start_date,
                        'new_end_date' => $request->end_date,
                        'new_duration' => \Carbon\Carbon::parse($request->start_date)->diffInDays(\Carbon\Carbon::parse($request->end_date)) + 1,
                        'booking_type' => 'instrument_rental'
                    ],
                    'original_start_date' => $rental->rental_start_date,
                    'original_end_date' => $rental->rental_end_date,
                    'requested_start_date' => $request->start_date,
                    'requested_end_date' => $request->end_date,
                    'reason' => $request->reason,
                    'priority' => RescheduleRequest::PRIORITY_MEDIUM
                ]);

                // Log reschedule request for instrument rental
                ActivityLog::create([
                    'description' => 'Reschedule Request Submitted: Instrument Rental - ' . $rescheduleRequest->reference,
                    'resource_type' => 'App\\Models\\InstrumentRental',
                    'resource_id' => $rental->id,
                    'old_values' => $rental->toArray(),
                    'new_values' => $rescheduleRequest->requested_data,
                    'user_id' => $rental->user_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Send notification to admin for instrument rental
                $this->notifyAdminOfInstrumentReschedule($rental, $rescheduleRequest->requested_data);
            }

            return response()->json([
                'success' => true,
                'message' => 'Reschedule request submitted successfully. Admin will review and approve your request.'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to submit reschedule request', [
                'booking_type' => $bookingType,
                'reference' => $reference,
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Failed to submit reschedule request: ' . $e->getMessage()], 500);
        }
    }

    private function notifyUserOfReschedule($booking, $rescheduleData)
    {
        try {
            $recipient = $booking->email ?? ($booking->user->email ?? null);
            if (!$recipient) {
                \Illuminate\Support\Facades\Log::warning('No customer email found for reschedule confirmation', [
                    'booking_reference' => $booking->reference
                ]);
                return;
            }
            \Illuminate\Support\Facades\Mail::to($recipient)->send(
                new \App\Mail\UserRescheduleConfirmation($booking, $rescheduleData)
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send user reschedule confirmation', [
                'booking_reference' => $booking->reference,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function notifyUserOfInstrumentReschedule($rental, $rescheduleData)
    {
        try {
            $recipient = $rental->user->email ?? null;
            if (!$recipient) {
                \Illuminate\Support\Facades\Log::warning('No customer email found for instrument reschedule confirmation', [
                    'rental_reference' => $rental->reference
                ]);
                return;
            }
            \Illuminate\Support\Facades\Mail::to($recipient)->send(
                new \App\Mail\UserInstrumentRescheduleConfirmation($rental, $rescheduleData)
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send user instrument reschedule confirmation', [
                'rental_reference' => $rental->reference,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function notifyAdminOfReschedule($booking, $rescheduleData)
    {
        try {
            $adminEmails = \App\Models\User::where('is_admin', true)
                ->pluck('email')
                ->filter()
                ->toArray();
            if (empty($adminEmails)) {
                $fallback = config('mail.from.address') ?? 'magamponr@gmail.com';
                $adminEmails = [$fallback];
            }

            $subject = 'Reschedule Request Submitted - ' . ($booking->reference ?? 'Unknown');

            $tz = config('app.timezone', 'UTC');
            $originalDateFmt = $booking->date ? \Carbon\Carbon::parse($booking->date)->timezone($tz)->format('M j, Y') : 'N/A';
            $requestedDate = $rescheduleData['new_date'] ?? $rescheduleData['requested_date'] ?? null;
            $requestedSlot = $rescheduleData['new_time_slot'] ?? $rescheduleData['requested_time_slot'] ?? null;
            $requestedDuration = $rescheduleData['new_duration'] ?? $rescheduleData['requested_duration'] ?? null;
            $requestedDateFmt = $requestedDate ? \Carbon\Carbon::parse($requestedDate)->timezone($tz)->format('M j, Y') : 'N/A';

            $body = "A customer submitted a studio reschedule request.\n\n" .
                "Reference: " . ($booking->reference ?? 'N/A') . "\n" .
                "Band/Customer: " . ($booking->band_name ?? 'Unknown') . "\n" .
                "Original Date: " . $originalDateFmt . " (" . $tz . ")\n" .
                "Original Time Slot: " . ($booking->time_slot ?? 'N/A') . "\n" .
                "Original Duration: " . ((string) $booking->duration) . " hour(s)\n\n" .
                "Requested Date: " . $requestedDateFmt . " (" . $tz . ")\n" .
                "Requested Time Slot: " . ($requestedSlot ?? 'N/A') . "\n" .
                "Requested Duration: " . ($requestedDuration !== null ? $requestedDuration . ' hour(s)' : 'N/A') . "\n";

            foreach ($adminEmails as $email) {
                \Illuminate\Support\Facades\Mail::raw($body, function ($message) use ($email, $subject) {
                    $message->to($email)->subject($subject);
                });
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to notify admin of reschedule', [
                'booking_reference' => $booking->reference ?? 'N/A',
                'error' => $e->getMessage()
            ]);
        }
    }

    private function notifyAdminOfInstrumentReschedule($rental, $rescheduleData)
    {
        try {
            $adminEmails = \App\Models\User::where('is_admin', true)
                ->pluck('email')
                ->filter()
                ->toArray();
            if (empty($adminEmails)) {
                $fallback = config('mail.from.address') ?? 'magamponr@gmail.com';
                $adminEmails = [$fallback];
            }

            $subject = 'Instrument Reschedule Submitted - ' . ($rental->reference ?? 'Unknown');

            $tz = config('app.timezone', 'UTC');
            $originalStartFmt = $rental->rental_start_date ? \Carbon\Carbon::parse($rental->rental_start_date)->timezone($tz)->format('M j, Y') : 'N/A';
            $originalEndFmt = $rental->rental_end_date ? \Carbon\Carbon::parse($rental->rental_end_date)->timezone($tz)->format('M j, Y') : 'N/A';
            $requestedStart = $rescheduleData['new_start_date'] ?? $rescheduleData['requested_start_date'] ?? null;
            $requestedEnd = $rescheduleData['new_end_date'] ?? $rescheduleData['requested_end_date'] ?? null;
            $requestedStartFmt = $requestedStart ? \Carbon\Carbon::parse($requestedStart)->timezone($tz)->format('M j, Y') : 'N/A';
            $requestedEndFmt = $requestedEnd ? \Carbon\Carbon::parse($requestedEnd)->timezone($tz)->format('M j, Y') : 'N/A';
            $requestedDurationDays = ($requestedStart && $requestedEnd)
                ? (\Carbon\Carbon::parse($requestedStart)->diffInDays(\Carbon\Carbon::parse($requestedEnd)) + 1)
                : ($rental->rental_duration_days ?? null);

            $body = "A customer submitted an instrument rental reschedule request.\n\n" .
                "Reference: " . ($rental->reference ?? 'N/A') . "\n" .
                "Instrument: " . ($rental->instrument_name ?? 'Unknown Instrument') . "\n" .
                "Original Start: " . $originalStartFmt . " (" . $tz . ")\n" .
                "Original End: " . $originalEndFmt . " (" . $tz . ")\n\n" .
                "Requested Start: " . $requestedStartFmt . " (" . $tz . ")\n" .
                "Requested End: " . $requestedEndFmt . " (" . $tz . ")\n" .
                "Requested Duration: " . ($requestedDurationDays !== null ? $requestedDurationDays . ' day(s)' : 'N/A') . "\n";

            foreach ($adminEmails as $email) {
                \Illuminate\Support\Facades\Mail::raw($body, function ($message) use ($email, $subject) {
                    $message->to($email)->subject($subject);
                });
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to notify admin of instrument reschedule', [
                'rental_reference' => $rental->reference ?? 'N/A',
                'error' => $e->getMessage()
            ]);
        }
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
     * Only includes dates when ALL time slots are booked or when drums/full package are rented (studio unavailable).
     */
    public function getBookedDates()
    {
        // Get instrument rental dates that make studio unavailable
        // (drums and full package rentals)
        $instrumentRentalDates = \App\Models\InstrumentRental::whereIn('status', ['pending', 'confirmed'])
            ->where(function($query) {
                $query->where('instrument_type', 'drums')
                      ->orWhere('instrument_type', 'Full Package');
            })
            ->get()
            ->flatMap(function($rental) {
                $dates = [];
                $startDate = \Carbon\Carbon::parse($rental->rental_start_date)->setTimezone(config('app.timezone', 'Asia/Manila'));
                $endDate = \Carbon\Carbon::parse($rental->rental_end_date)->setTimezone(config('app.timezone', 'Asia/Manila'));
                
                // Add all dates in the rental period
                $currentDate = $startDate->copy();
                while ($currentDate->lte($endDate)) {
                    $dates[] = $currentDate->format('Y-m-d');
                    $currentDate->addDay();
                }
                
                return $dates;
            })
            ->unique()
            ->values()
            ->toArray();

        // Get dates where ALL time slots are booked
        // Studio operates 8 AM to 8 PM with 30-minute slots = 24 total slots per day
        $totalSlotsPerDay = 24;
        
        $fullyBookedDates = \App\Models\Booking::whereIn('status', ['pending', 'confirmed'])
            ->selectRaw('DATE(CONVERT_TZ(date, "+00:00", "' . config('app.timezone', 'Asia/Manila') . '")) as booking_date, COUNT(*) as booking_count')
            ->groupBy('booking_date')
            ->having('booking_count', '>=', $totalSlotsPerDay)
            ->pluck('booking_date')
            ->toArray();

        // Combine and return all unavailable dates
        $allBookedDates = array_unique(array_merge($fullyBookedDates, $instrumentRentalDates));
        
        return response()->json(['booked_dates' => $allBookedDates]);
    }

    /**
     * Return all dates that have at least one studio booking (band or solo rehearsal).
     * This does NOT include instrument rentals and does not disable the date —
     * it is used for the home page calendar red dot indicator.
     */
    public function getHasBookingDates()
    {
        try {
            // Collect dates that have any pending or confirmed bookings
            // Avoid CONVERT_TZ to prevent nulls when MySQL timezone tables are missing
            $datesWithBookings = \App\Models\Booking::whereIn('status', ['pending', 'confirmed'])
                ->get(['date'])
                ->map(function ($booking) {
                    // Normalize to YYYY-MM-DD regardless of stored timezone
                    return \Carbon\Carbon::parse($booking->date)->format('Y-m-d');
                })
                ->unique()
                ->values()
                ->toArray();

            return response()->json([
                'booked_dates' => $datesWithBookings
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to fetch dates with bookings', [
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'booked_dates' => []
            ], 500);
        }
    }

    /**
     * Return all bookings for a given date as JSON.
     */
    public function getBookingsByDate(Request $request)
    {
        $date = $request->query('date');
        
        // Parse the date with proper timezone handling to ensure consistent querying
        $queryDate = \Carbon\Carbon::parse($date)->setTimezone(config('app.timezone', 'Asia/Manila'))->format('Y-m-d');
        
        // Get regular bookings (only active: pending or confirmed)
        $bookings = \App\Models\Booking::whereDate('date', $queryDate)
            ->whereIn('status', ['pending', 'confirmed'])
            ->get(['id', 'reference', 'user_id', 'date', 'time_slot', 'duration', 'status'])
            ->map(function($booking) {
                // Convert the date to a simple Y-m-d format to avoid timezone issues
                $booking->date = \Carbon\Carbon::parse($booking->date)->format('Y-m-d');
                $booking->type = 'booking';
                return $booking;
            });
        
        // Get instrument rentals that include this date within their rental range
        $instrumentRentals = \App\Models\InstrumentRental::whereIn('status', ['pending', 'confirmed'])
            ->where('rental_start_date', '<=', $queryDate)
            ->where('rental_end_date', '>=', $queryDate)
            ->get(['id', 'four_digit_code as reference', 'user_id', 'rental_start_date', 'rental_end_date', 'status', 'instrument_type', 'instrument_name', 'rental_duration_days'])
            ->map(function($rental) use ($queryDate) {
                // Set the date to the queried date for consistency
                $rental->date = $queryDate;
                $rental->type = 'instrument_rental';
                // Add time_slot and duration for consistency with bookings
                $rental->time_slot = 'Full Day';
                $rental->duration = $rental->rental_duration_days . ' day(s)';
                return $rental;
            });
        
        // Combine both collections
        $allBookings = $bookings->concat($instrumentRentals);
            
        return response()->json(['bookings' => $allBookings]);
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

    /**
     * Check if a reference code already exists
     */
    public function checkReferenceCode(Request $request)
    {
        $request->validate([
            'reference_code' => 'required|string|regex:/^[0-9]{13}$/'
        ]);

        // Check both bookings and instrument rentals tables
        $existsInBookings = Booking::where('reference_code', $request->reference_code)->exists();
        // For rentals, the user-entered 13-digit code is the GCash payment reference
        $existsInRentals = \App\Models\InstrumentRental::where('payment_reference', $request->reference_code)->exists();
        
        $exists = $existsInBookings || $existsInRentals;
        
        return response()->json([
            'exists' => $exists,
            'available' => !$exists
        ]);
    }

    /**
     * Validate if a reference exists for reschedule
     */
    public function validateReference($reference)
    {
        // First check studio bookings
        $booking = Booking::where(function($query) use ($reference) {
                $query->where('reference', $reference)
                      ->orWhere('reference_code', $reference);
            })
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();
        
        if ($booking) {
            return response()->json([
                'valid' => true,
                'booking' => [
                    'id' => $booking->id,
                    'band_name' => $booking->band_name,
                    'date' => $booking->date,
                    'time_slot' => $booking->time_slot,
                    'duration' => $booking->duration,
                    'service_type' => $booking->service_type
                ]
            ]);
        }
        
        // If not found in bookings, check instrument rentals
        $rental = \App\Models\InstrumentRental::where(function($query) use ($reference) {
                $query->where('reference', $reference)
                      ->orWhere('four_digit_code', $reference)
                      ->orWhere('payment_reference', $reference);
            })
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();
            
        if ($rental) {
            return response()->json([
                'valid' => true,
                'booking' => [
                    'id' => $rental->id,
                    'band_name' => $rental->instrument_name, // Use instrument name as identifier
                    'date' => $rental->rental_start_date->format('Y-m-d'),
                    'time_slot' => 'Rental Period',
                    'duration' => $rental->rental_duration_days,
                    'service_type' => 'instrument_rental'
                ]
            ]);
        }
        
        return response()->json([
            'valid' => false,
            'message' => 'Reference number not found or booking is not active.'
        ]);
    }
}