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

Route::get('/api/booked-dates', [App\Http\Controllers\BookingController::class, 'getBookedDates']);
Route::get('/api/bookings-by-date', [App\Http\Controllers\BookingController::class, 'getBookingsByDate']);

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


// Admin routes
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/calendar', [App\Http\Controllers\AdminController::class, 'calendar'])->name('admin.calendar');
    Route::get('/database', [App\Http\Controllers\AdminController::class, 'database'])->name('admin.database');
    Route::get('/google-calendar/connect', [App\Http\Controllers\AdminController::class, 'connectGoogleCalendar'])->name('admin.calendar.connect');
    Route::get('/google-calendar/callback', [App\Http\Controllers\AdminController::class, 'handleGoogleCalendarCallback'])->name('admin.calendar.callback');
    Route::post('/google-calendar/disconnect', [App\Http\Controllers\AdminController::class, 'disconnectGoogleCalendar'])->name('admin.calendar.disconnect');
    Route::post('/google-calendar/sync', [App\Http\Controllers\AdminController::class, 'syncBookingsToCalendar'])->name('admin.calendar.sync');
    Route::post('/make-admin', [App\Http\Controllers\AdminController::class, 'makeAdmin'])->name('admin.make');
    Route::post('/remove-admin', [App\Http\Controllers\AdminController::class, 'removeAdmin'])->name('admin.remove');
    Route::post('/database/backup', [App\Http\Controllers\AdminController::class, 'createBackup'])->name('admin.database.backup');
    Route::post('/database/migrate', [App\Http\Controllers\AdminController::class, 'runMigrations'])->name('admin.database.migrate');
    Route::post('/database/clear-cache', [App\Http\Controllers\AdminController::class, 'clearCache'])->name('admin.database.clear-cache');
});

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

