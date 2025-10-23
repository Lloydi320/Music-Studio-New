<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InstrumentRentalController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\CalendarFeedController;


Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/services', function () {
    return view('services');
})->name('services');

// New policy pages
Route::get('/terms', function () {
    return view('terms');
})->name('terms');
Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');
Route::get('/music-lessons', function () {
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Please log in to access Music Lessons.');
    }
    return app(App\Http\Controllers\MusicLessonsController::class)->index();
})->name('music-lessons');





Route::get('/map', function () {
    return view('map');
})->name('map');



Route::get('/booking', function () {
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Please log in to book a session.');
    }
    return view('booking');
})->name('booking');

Route::post('/booking', [BookingController::class, 'store'])->middleware('auth')->name('booking.store');

Route::get('/solo-rehearsal', function () {
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Please log in to book a session.');
    }
    return view('solo-rehearsal');
})->name('solo-rehearsal');

Route::post('/solo-rehearsal', [BookingController::class, 'store'])->middleware('auth')->name('solo-rehearsal.store');

Route::get('/api/booked-dates', [App\Http\Controllers\BookingController::class, 'getBookedDates']);
Route::get('/api/bookings-by-date', [App\Http\Controllers\BookingController::class, 'getBookingsByDate']);
Route::get('/api/bookings', [App\Http\Controllers\BookingController::class, 'getByDate']);
Route::get('/api/validate-reference/{reference}', [App\Http\Controllers\BookingController::class, 'validateReference']);

// Instrument Rental Routes
Route::get('/instrument-rental', function () {
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Please log in to access Instrument Rental.');
    }
    return app(App\Http\Controllers\InstrumentRentalController::class)->index();
})->name('instrument-rental.index');
Route::post('/instrument-rental', [InstrumentRentalController::class, 'store'])->middleware('auth')->name('instrument-rental.store');

// Instrument Rental API Routes
Route::get('/api/instruments-by-type', [InstrumentRentalController::class, 'getInstrumentsByType']);
Route::get('/api/daily-rate', [InstrumentRentalController::class, 'getDailyRate']);
Route::post('/api/check-rental-availability', [InstrumentRentalController::class, 'checkAvailability']);
Route::get('/api/user-rentals', [InstrumentRentalController::class, 'getUserRentals'])->middleware('auth');
Route::get('/api/rental/{reference}', [InstrumentRentalController::class, 'getByReference']);
Route::post('/api/rental/{reference}/cancel', [InstrumentRentalController::class, 'cancelByReference'])->middleware('auth');
Route::post('/api/rental/{reference}/status', [InstrumentRentalController::class, 'updateStatus']);
Route::get('/api/instrument-booked-dates', [InstrumentRentalController::class, 'getBookedDates']);
Route::get('/api/instrument-bookings-by-date', [InstrumentRentalController::class, 'getBookingsByDate']);

// Removed this route as it conflicts with API routes

// Login route (show login page with tabs)
Route::get('/login', function () {
    return view('login');
})->name('login');

// Google OAuth routes with type parameter
// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password reset routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Email verification routes
Route::get('/email/verify', [AuthController::class, 'showVerifyEmailForm'])->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Pending user verification routes
Route::get('/verify-registration/{token}/{email}', [AuthController::class, 'verifyPendingUser'])->name('verification.verify.pending');
Route::post('/resend-pending-verification', [AuthController::class, 'resendPendingVerification'])->name('verification.resend.pending');

// Feedback routes (submission requires authentication)
Route::post('/feedback', [FeedbackController::class, 'store'])->middleware('auth')->name('feedback.store');

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
    Route::get('/analytics', [App\Http\Controllers\AdminController::class, 'analytics'])->name('admin.analytics');
    Route::get('/analytics/export', [App\Http\Controllers\AdminController::class, 'analytics'])->name('admin.analytics.export');
    Route::get('/bookings', [App\Http\Controllers\AdminController::class, 'bookings'])->name('admin.bookings');
    Route::get('/bookings/create', [App\Http\Controllers\AdminController::class, 'createBooking'])->name('admin.bookings.create');
    Route::get('/bookings/{id}', [App\Http\Controllers\AdminController::class, 'showBooking'])->name('admin.bookings.show');
    Route::get('/google-calendar/connect', [App\Http\Controllers\AdminController::class, 'connectGoogleCalendar'])->name('admin.calendar.connect');
    Route::get('/google-calendar/callback', [App\Http\Controllers\AdminController::class, 'handleGoogleCalendarCallback'])->name('admin.calendar.callback');
    Route::post('/google-calendar/disconnect', [App\Http\Controllers\AdminController::class, 'disconnectGoogleCalendar'])->name('admin.calendar.disconnect');
    Route::post('/google-calendar/sync', [App\Http\Controllers\AdminController::class, 'syncBookingsToCalendar'])->name('admin.calendar.sync');
    Route::post('/google-calendar/sync-all', [App\Http\Controllers\AdminController::class, 'syncBookingsToCalendar'])->name('admin.calendar.sync-all');
    Route::get('/users', [App\Http\Controllers\AdminController::class, 'users'])->name('admin.users');
    Route::delete('/users/{id}', [App\Http\Controllers\AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::post('/make-admin', [App\Http\Controllers\AdminController::class, 'makeAdmin'])->name('admin.makeAdmin');
    Route::post('/remove-admin', [App\Http\Controllers\AdminController::class, 'removeAdmin'])->name('admin.removeAdmin');
    Route::post('/database/backup', [App\Http\Controllers\AdminController::class, 'createBackup'])->name('admin.database.backup');
    Route::get('/database/backups', [App\Http\Controllers\AdminController::class, 'getBackups'])->name('admin.database.backups');
    Route::get('/database/backup/download/{filename}', [App\Http\Controllers\AdminController::class, 'downloadBackup'])->name('admin.database.backup.download');
    Route::delete('/database/backup/delete/{filename}', [App\Http\Controllers\AdminController::class, 'deleteBackup'])->name('admin.database.backup.delete');
    Route::post('/database/restore', [App\Http\Controllers\AdminController::class, 'restoreDatabase'])->name('admin.database.restore');
    Route::post('/database/migrate', [App\Http\Controllers\AdminController::class, 'runMigrations'])->name('admin.database.migrate');
    Route::post('/database/clear-cache', [App\Http\Controllers\AdminController::class, 'clearCache'])->name('admin.database.clear-cache');
    Route::get('/instrument-rentals', [App\Http\Controllers\AdminController::class, 'instrumentRentals'])->name('admin.instrument-rentals');
    Route::get('/instrument-rentals/{id}', [App\Http\Controllers\AdminController::class, 'showInstrumentRental'])->name('admin.instrument-rentals.show');
    Route::post('/instrument-rentals/{id}/status', [App\Http\Controllers\AdminController::class, 'updateRentalStatus'])->name('admin.rental-status');
    Route::delete('/bookings/{id}', [App\Http\Controllers\AdminController::class, 'deleteBooking'])->name('admin.booking.delete');
    Route::patch('/bookings/{id}/approve', [App\Http\Controllers\AdminController::class, 'approveBooking'])->name('admin.booking.approve');
    Route::get('/activity-logs', [App\Http\Controllers\AdminController::class, 'activityLogs'])->name('admin.activity-logs');
    Route::delete('/activity-logs/clear', [App\Http\Controllers\AdminController::class, 'clearActivityLogs'])->name('admin.activity-logs.clear');
    Route::get('/instrument-bookings', [App\Http\Controllers\AdminController::class, 'instrumentBookings'])->name('admin.instrument-bookings');

    // QR management routes
    Route::get('/qr-configs', [App\Http\Controllers\QrConfigAdminController::class, 'index'])->name('admin.qr.index');
    Route::post('/qr-configs/rehearsal', [App\Http\Controllers\QrConfigAdminController::class, 'storeRehearsal'])->name('admin.qr.rehearsal.store');
    Route::post('/qr-configs/rental', [App\Http\Controllers\QrConfigAdminController::class, 'storeRental'])->name('admin.qr.rental.store');

    // Admin Walk-In creation routes
    Route::get('/walk-in', [App\Http\Controllers\AdminController::class, 'walkInCreate'])->name('admin.walk-in.create');
    Route::post('/walk-in', [App\Http\Controllers\AdminController::class, 'walkInStore'])->name('admin.walk-in.store');
    Route::get('/walk-in/availability', [App\Http\Controllers\AdminController::class, 'walkInAvailability'])->name('admin.walk-in.availability');

    // Carousel management routes
    Route::get('/carousel', [App\Http\Controllers\AdminController::class, 'carouselManagement'])->name('admin.carousel');
    Route::post('/carousel', [App\Http\Controllers\AdminController::class, 'storeCarouselItem'])->name('admin.carousel.store');
    Route::put('/carousel/{id}', [App\Http\Controllers\AdminController::class, 'updateCarouselItem'])->name('admin.carousel.update');
    Route::delete('/carousel/{id}', [App\Http\Controllers\AdminController::class, 'deleteCarouselItem'])->name('admin.carousel.delete');

    Route::patch('/bookings/{id}/reject', [App\Http\Controllers\AdminController::class, 'rejectBooking'])->name('admin.booking.reject');
    Route::patch('/bookings/{id}/reschedule', [App\Http\Controllers\AdminController::class, 'rescheduleBooking'])->name('admin.booking.reschedule');
    
    // Notification routes
    Route::get('/notifications/new-bookings', [App\Http\Controllers\AdminController::class, 'getNewBookingNotifications'])->name('admin.notifications.new-bookings');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\AdminController::class, 'markAllNotificationsAsRead'])->name('admin.notifications.mark-all-read');
    
    // Reschedule request routes
    Route::get('/reschedule-requests', [App\Http\Controllers\AdminController::class, 'rescheduleRequests'])->name('admin.reschedule-requests');
    Route::get('/reschedule-requests/{id}', [App\Http\Controllers\AdminController::class, 'showRescheduleRequest'])->name('admin.reschedule-request.show');
    Route::post('/reschedule-requests/{id}/approve', [App\Http\Controllers\AdminController::class, 'approveRescheduleRequest'])->name('admin.reschedule-request.approve');
    Route::post('/reschedule-requests/{id}/reject', [App\Http\Controllers\AdminController::class, 'rejectRescheduleRequest'])->name('admin.reschedule-request.reject');
    
    // Modal API routes
    Route::get('/reschedule-requests/{id}/data', [App\Http\Controllers\AdminController::class, 'getRescheduleRequestData'])->name('admin.reschedule-request.data');
    Route::get('/bookings/{id}/data', [App\Http\Controllers\AdminController::class, 'getBookingData'])->name('admin.booking.data');
    
    // Backup & Restore routes (consolidated into database management)
    // All backup functionality is now handled through /admin/database routes above

// Add these new routes for instrument rental approval
Route::patch('/instrument-rentals/{id}/approve', [App\Http\Controllers\AdminController::class, 'approveRental'])->name('admin.rental.approve');
Route::patch('/instrument-rentals/{id}/reject', [App\Http\Controllers\AdminController::class, 'rejectRental'])->name('admin.rental.reject');
Route::get('/api/bookings/dates-with-status', [BookingController::class, 'getBookedDatesWithStatus']);
});

// Duplicate debug and webhook routes removed; single definitions kept above.


// QR management routes moved into admin group

Route::get('/debug-google', function () {
    return [
        'client_id' => config('services.google.client_id'),
        'secret' => config('services.google.client_secret'),
        'redirect' => config('services.google.redirect'),
    ];
});

// Google Calendar webhook (should be outside auth middleware)
Route::post('/webhooks/google-calendar', [App\Http\Controllers\GoogleCalendarWebhookController::class, 'handleWebhook'])
    ->name('google.calendar.webhook');


Route::get('/calendar/feed.ics', [CalendarFeedController::class, 'ics'])->name('calendar.feed');
Route::get('/calendar/export.ics', [CalendarFeedController::class, 'export'])->name('calendar.export');

// Individual booking ICS download routes
Route::get('/booking/{id}/calendar.ics', [CalendarFeedController::class, 'exportBooking'])->name('booking.calendar.export');
Route::get('/rental/{id}/calendar.ics', [CalendarFeedController::class, 'exportRental'])->name('rental.calendar.export');

