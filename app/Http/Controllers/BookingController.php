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
            'service_type' => 'nullable|string|in:studio_rental,solo_rehearsal,instrument_rental',
            'band_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'contact_number' => 'nullable|string|size:11|regex:/^[0-9]{11}$/',
            'reference_code' => 'nullable|string|size:4|unique:bookings,reference_code',
            'upload_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'reference_code.unique' => 'Reference number "' . $request->reference_code . '" already exists. Please use a different reference number from GCash 4-digits last number to proceed booking.'
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

        // Calculate pricing based on service type
        $serviceType = $request->service_type ?? 'studio_rental';
        $hourlyRate = ($serviceType === 'solo_rehearsal') ? 300.00 : 250.00; // ₱300 for solo rehearsal, ₱250 for studio rental
        $totalAmount = $hourlyRate * $duration;
        
        $booking = Booking::create([
            'user_id' => Auth::id(),
            'date' => $request->date,
            'time_slot' => $request->time_slot,
            'duration' => $duration,
            'price' => $hourlyRate,
            'total_amount' => $totalAmount,
            'service_type' => $request->service_type ?? 'studio_rental', // Use request service_type or default to studio_rental
            'band_name' => $request->band_name,
            'email' => $request->email,
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

    public function rescheduleRequest(Request $request)
    {
        // Determine booking type and validate accordingly
        $bookingType = $request->input('booking_type', 'studio_rental');
        
        if ($bookingType === 'studio_rental') {
            $request->validate([
                'reference_number' => 'required|string|size:4',
                'new_date' => 'required|date|after_or_equal:today',
                'new_time_slot' => 'required|string',
                'duration' => 'required|integer|min:1|max:8'
            ]);
        } else {
            $request->validate([
                'reference_number' => 'required|string|size:4',
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
                ActivityLog::logActivity(
                    'Reschedule Request Submitted: Instrument Rental - ' . $rescheduleRequest->reference,
                    'rental_reschedule_requested',
                    $rental->user_id,
                    'App\\Models\\InstrumentRental',
                    $rental->id,
                    $rental->toArray(),
                    $rescheduleRequest->requested_data,
                    ActivityLog::SEVERITY_MEDIUM
                );

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
                'reference_number' => $request->reference_number,
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
                'reference_number' => 'required|string|size:4',
                'new_date' => 'required|date|after_or_equal:today',
                'new_time_slot' => 'required|string',
                'duration' => 'required|integer|min:1|max:8'
            ]);
        } else {
            $request->validate([
                'reference_number' => 'required|string|size:4',
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

    private function notifyAdminOfReschedule($booking, $rescheduleData)
    {
        try {
            \Illuminate\Support\Facades\Log::info('Starting reschedule notification process', [
                'booking_reference' => $booking->reference
            ]);
            
            // Get all admin users
            $adminUsers = \App\Models\User::where('is_admin', true)->get();
            
            \Illuminate\Support\Facades\Log::info('Found admin users', [
                'admin_count' => $adminUsers->count(),
                'admin_emails' => $adminUsers->pluck('email')->toArray()
            ]);
            
            if ($adminUsers->count() === 0) {
                \Illuminate\Support\Facades\Log::warning('No admin users found for reschedule notification');
                return;
            }
            
            foreach ($adminUsers as $admin) {
                \Illuminate\Support\Facades\Log::info('Sending notification to admin', [
                    'admin_email' => $admin->email,
                    'admin_name' => $admin->name
                ]);
                
                // Send email notification
                \Illuminate\Support\Facades\Mail::to($admin->email)->send(
                    new \App\Mail\RescheduleNotification($booking, $rescheduleData)
                );
                
                \Illuminate\Support\Facades\Log::info('Notification sent successfully to admin', [
                    'admin_email' => $admin->email
                ]);
            }
            
            \Illuminate\Support\Facades\Log::info('Reschedule notification sent to all admins', [
                'booking_reference' => $booking->reference,
                'admin_count' => $adminUsers->count()
            ]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send reschedule notification', [
                'booking_reference' => $booking->reference,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    private function notifyAdminOfInstrumentReschedule($rental, $rescheduleData)
    {
        try {
            \Illuminate\Support\Facades\Log::info('Starting instrument reschedule notification process', [
                'rental_reference' => $rental->reference
            ]);
            
            // Get all admin users
            $adminUsers = \App\Models\User::where('is_admin', true)->get();
            
            \Illuminate\Support\Facades\Log::info('Found admin users for instrument reschedule', [
                'admin_count' => $adminUsers->count(),
                'admin_emails' => $adminUsers->pluck('email')->toArray()
            ]);
            
            if ($adminUsers->count() === 0) {
                \Illuminate\Support\Facades\Log::warning('No admin users found for instrument reschedule notification');
                return;
            }
            
            foreach ($adminUsers as $admin) {
                \Illuminate\Support\Facades\Log::info('Sending instrument reschedule notification to admin', [
                    'admin_email' => $admin->email,
                    'admin_name' => $admin->name
                ]);
                
                // Send email notification for instrument rental reschedule
                \Illuminate\Support\Facades\Mail::to($admin->email)->send(
                    new \App\Mail\InstrumentRescheduleNotification($rental, $rescheduleData)
                );
                
                \Illuminate\Support\Facades\Log::info('Instrument reschedule notification sent successfully to admin', [
                    'admin_email' => $admin->email
                ]);
            }
            
            \Illuminate\Support\Facades\Log::info('Instrument reschedule notification sent to all admins', [
                'rental_reference' => $rental->reference,
                'admin_count' => $adminUsers->count()
            ]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send instrument reschedule notification', [
                'rental_reference' => $rental->reference,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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
     */
    public function getBookedDates()
    {
        $dates = \App\Models\Booking::whereIn('status', ['pending', 'confirmed'])
            ->get()
            ->map(function($booking) {
                return \Carbon\Carbon::parse($booking->date)->format('Y-m-d');
            })
            ->unique()
            ->values();
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

    /**
     * Check if a reference code already exists
     */
    public function checkReferenceCode(Request $request)
    {
        $request->validate([
            'reference_code' => 'required|string|size:4'
        ]);

        // Check both bookings and instrument rentals tables
        $existsInBookings = Booking::where('reference_code', $request->reference_code)->exists();
        $existsInRentals = \App\Models\InstrumentRental::where('four_digit_code', $request->reference_code)->exists();
        
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
                      ->orWhere('four_digit_code', $reference);
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