<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FeedbackController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/services', function () {
    return view('services');
})->name('services');



Route::get('/booking', function () {
    if (!Auth::check()) {
        return redirect('/')->with('error', 'Please log in to book a session.');
    }
    return view('booking');
})->name('booking');

Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');



Route::get('/api/bookings', [BookingController::class, 'getByDate']);

// Google OAuth routes
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google.callback');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Feedback routes (must be logged in)
Route::middleware('auth')->group(function () {
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
    Route::get('/my-feedback', [FeedbackController::class, 'myFeedback'])->name('feedback.my');
});

// Public API to get all feedback
Route::get('/api/feedback', [FeedbackController::class, 'index']);


Route::get('/debug-google', function () {
    return config('services.google.client_id');
});

Route::get('/debug-google', function () {
    return [
        'client_id' => config('services.google.client_id'),
        'secret' => config('services.google.client_secret'),
        'redirect' => config('services.google.redirect'),
    ];
});

