<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\BookingController;

// Test route to verify API is working
Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});

// Feedback routes
Route::get('/my-feedback', [FeedbackController::class, 'myFeedback']);
Route::post('/feedback', [FeedbackController::class, 'store']);
Route::get('/feedbacks', [FeedbackController::class, 'index']);

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