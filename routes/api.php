<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\InstrumentRentalController;
use App\Http\Controllers\PaymentQrController;

// Test route to verify API is working
Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});

// Feedback routes
Route::get('/my-feedback', [FeedbackController::class, 'myFeedback']);
Route::post('/feedback', [FeedbackController::class, 'store']);
Route::get('/feedbacks', [FeedbackController::class, 'index']);

// Reference code validation
Route::post('/check-reference-code', [BookingController::class, 'checkReferenceCode']);

// Get booked dates for calendar
Route::get('/booked-dates', [BookingController::class, 'getBookedDates']);
// Get dates that have any studio bookings (band or solo rehearsal)
Route::get('/has-booking-dates', [BookingController::class, 'getHasBookingDates']);

// Get booked dates for instrument rental conflict checking
Route::get('/instrument-rental/booked-dates', [InstrumentRentalController::class, 'getBookedDates']);

// Booking API routes to match your PHP scripts
Route::prefix('bookings')->group(function () {
    // Get bookings by date
    Route::get('/', [BookingController::class, 'getByDate']);
    
    // Public routes
    Route::post('/check-availability', [BookingController::class, 'checkAvailability']);
    Route::get('/get/{reference}', [BookingController::class, 'getByReference']);
    Route::get('/check-status/{reference}', [BookingController::class, 'checkStatus']);
    
    // Protected routes (require authentication) - temporarily removed auth middleware for testing
    Route::post('/create', [BookingController::class, 'store']);
    Route::get('/user-bookings', [BookingController::class, 'getUserBookings']);
    Route::post('/cancel/{reference}', [BookingController::class, 'cancelByReference']);
    Route::post('/reschedule/{reference}', [BookingController::class, 'rescheduleByReference']);
    Route::post('/reschedule', [BookingController::class, 'rescheduleRequest']);
    Route::post('/update-status/{reference}', [BookingController::class, 'updateStatus']);
});
// Payment QR routes
Route::get('/payment-qr/rehearsal', [PaymentQrController::class, 'rehearsal']);
Route::get('/payment-qr/rental', [PaymentQrController::class, 'rental']);