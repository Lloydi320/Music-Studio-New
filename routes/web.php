<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InstrumentRentalController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FeedbackController;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/services', function () {
    return view('services');
})->name('services');

Route::get('/music-lessons', function () {
    return view('music-lessons');
})->name('music-lessons');

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

// Instrument Rental Routes
Route::get('/instrument-rental', [InstrumentRentalController::class, 'index'])->name('instrument-rental');
Route::post('/instrument-rental', [InstrumentRentalController::class, 'store'])->middleware('auth')->name('instrument-rental.store');

// Instrument Rental API Routes
Route::get('/api/instruments-by-type', [InstrumentRentalController::class, 'getInstrumentsByType']);
Route::get('/api/daily-rate', [InstrumentRentalController::class, 'getDailyRate']);
Route::post('/api/check-rental-availability', [InstrumentRentalController::class, 'checkAvailability']);
Route::get('/api/user-rentals', [InstrumentRentalController::class, 'getUserRentals'])->middleware('auth');
Route::get('/api/rental/{reference}', [InstrumentRentalController::class, 'getByReference']);
Route::post('/api/rental/{reference}/cancel', [InstrumentRentalController::class, 'cancelByReference'])->middleware('auth');
Route::post('/api/rental/{reference}/status', [InstrumentRentalController::class, 'updateStatus']);

// Removed this route as it conflicts with API routes

// Login route (redirects to Google OAuth)
Route::get('/login', function () {
    return redirect()->route('google.login');
})->name('login');

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
    Route::get('/instrument-rentals', [App\Http\Controllers\AdminController::class, 'instrumentRentals'])->name('admin.instrument-rentals');
    Route::post('/instrument-rentals/{id}/status', [App\Http\Controllers\AdminController::class, 'updateRentalStatus'])->name('admin.rental-status');
    Route::delete('/bookings/{id}', [App\Http\Controllers\AdminController::class, 'deleteBooking'])->name('admin.booking.delete');
Route::patch('/bookings/{id}/approve', [App\Http\Controllers\AdminController::class, 'approveBooking'])->name('admin.booking.approve');
Route::patch('/bookings/{id}/reject', [App\Http\Controllers\AdminController::class, 'rejectBooking'])->name('admin.booking.reject');

// Add these new routes for instrument rental approval
Route::patch('/instrument-rentals/{id}/approve', [App\Http\Controllers\AdminController::class, 'approveRental'])->name('admin.rental.approve');
Route::patch('/instrument-rentals/{id}/reject', [App\Http\Controllers\AdminController::class, 'rejectRental'])->name('admin.rental.reject');
});

Route::get('/debug-google', function () {
    return [
        'client_id' => config('services.google.client_id'),
        'secret' => config('services.google.client_secret'),
        'redirect' => config('services.google.redirect'),
    ];
});

