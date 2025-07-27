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

Route::get('/feedback', function () {
    return view('feedback');
})->name('feedback');



Route::get('/booking', function () {
    if (!Auth::check()) {
        return redirect('/')->with('error', 'Please log in to book a session.');
    }
    return view('booking');
})->name('booking');

Route::post('/booking', [BookingController::class, 'store'])->middleware('auth')->name('booking.store');



// Removed this route as it conflicts with API routes

// Google OAuth routes
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('google.callback');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Feedback routes
Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store'); // Allow guests

// Authenticated feedback routes
Route::middleware('auth')->group(function () {
    Route::get('/my-feedback', [FeedbackController::class, 'myFeedback'])->name('feedback.my');
});

// Removed this route as it conflicts with API routes


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

