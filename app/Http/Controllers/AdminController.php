<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\GoogleCalendarService;
use App\Models\User;
use App\Models\Booking;
use App\Models\InstrumentRental;

class AdminController extends Controller
{
    protected ?GoogleCalendarService $calendarService;

    public function __construct()
    {
        // Initialize calendar service only if Google Client is available
        try {
            if (class_exists('Google\Client')) {
                $this->calendarService = app(GoogleCalendarService::class);
            } else {
                $this->calendarService = null;
            }
        } catch (\Exception $e) {
            $this->calendarService = null;
        }
    }

    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }
        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();
        $recentBookings = Booking::with('user')->latest()->take(10)->get();
        
        // Instrument rental statistics
        $totalRentals = InstrumentRental::count();
        $pendingRentals = InstrumentRental::where('status', 'pending')->count();
        $activeRentals = InstrumentRental::where('status', 'active')->count();
        $recentRentals = InstrumentRental::with('user')->latest()->take(10)->get();
        
        return view('admin.dashboard', compact(
            'user', 
            'totalBookings', 
            'pendingBookings', 
            'confirmedBookings', 
            'recentBookings',
            'totalRentals',
            'pendingRentals',
            'activeRentals',
            'recentRentals'
        ));
    }

    /**
     * Show Google Calendar integration page
     */
    public function calendar()
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }
        return view('admin.calendar', compact('user'));
    }

    /**
     * Connect to Google Calendar
     */
    public function connectGoogleCalendar()
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }
        
        if (!$this->calendarService) {
            return redirect()->back()->with('error', 'Google Calendar service is not available. Please run: composer install');
        }
        
        try {
            $authUrl = $this->calendarService->getAuthUrl();
            return redirect($authUrl);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to connect to Google Calendar: ' . $e->getMessage());
        }
    }

    /**
     * Handle Google Calendar OAuth callback
     */
    public function handleGoogleCalendarCallback(Request $request)
    {
        if (!$this->calendarService) {
            return redirect('/admin/calendar')->with('error', 'Google Calendar service is not available. Please run: composer install');
        }
        
        if ($request->has('error')) {
            return redirect('/admin/calendar')->with('error', 'Google Calendar connection was cancelled.');
        }

        if (!$request->has('code')) {
            return redirect('/admin/calendar')->with('error', 'Invalid callback from Google.');
        }

        try {
            $user = Auth::user();
            $success = $this->calendarService->handleCallback($request->code, $user);
            
            if ($success) {
                return redirect('/admin/calendar')->with('success', 'Google Calendar connected successfully!');
            } else {
                return redirect('/admin/calendar')->with('error', 'Failed to connect Google Calendar.');
            }
        } catch (\Exception $e) {
            return redirect('/admin/calendar')->with('error', 'Error connecting Google Calendar: ' . $e->getMessage());
        }
    }

    /**
     * Disconnect Google Calendar
     */
    public function disconnectGoogleCalendar()
    {
        try {
            /** @var User $user */
            $user = Auth::user();
            $user->update([
                'google_calendar_token' => null,
                'google_calendar_id' => null
            ]);

            return redirect('/admin/calendar')->with('success', 'Google Calendar disconnected successfully.');
        } catch (\Exception $e) {
            return redirect('/admin/calendar')->with('error', 'Failed to disconnect Google Calendar: ' . $e->getMessage());
        }
    }

    /**
     * Sync all existing bookings to Google Calendar
     */
    public function syncBookingsToCalendar()
    {
        if (!$this->calendarService) {
            return redirect('/admin/calendar')->with('error', 'Google Calendar service is not available. Please run: composer install');
        }
        
        try {
            /** @var User $user */
            $user = Auth::user();
            
            if (!$user->hasGoogleCalendarAccess()) {
                return redirect('/admin/calendar')->with('error', 'Please connect Google Calendar first.');
            }

            $bookings = Booking::where('status', 'confirmed')
                             ->whereNull('google_event_id')
                             ->with('user')
                             ->get();

            $synced = 0;
            foreach ($bookings as $booking) {
                try {
                    $this->calendarService->createBookingEvent($booking);
                    $synced++;
                } catch (\Exception $e) {
                    Log::warning('Failed to sync booking to calendar', [
                        'booking_id' => $booking->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            return redirect('/admin/calendar')->with('success', "Successfully synced {$synced} bookings to Google Calendar.");
        } catch (\Exception $e) {
            return redirect('/admin/calendar')->with('error', 'Failed to sync bookings: ' . $e->getMessage());
        }
    }

    /**
     * Make a user an admin
     */
    public function makeAdmin(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        try {
            $user = User::where('email', $request->email)->first();
            if ($user) {
                $user->update(['is_admin' => true]);
                return redirect()->back()->with('success', "User {$user->name} is now an admin.");
            } else {
                return redirect()->back()->with('error', 'User not found.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to make user admin: ' . $e->getMessage());
        }
    }

    /**
     * Remove admin privileges
     */
    public function removeAdmin(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        try {
            $user = User::findOrFail($request->user_id);
            
            // Prevent removing admin from current user
            if ($user->id === Auth::id()) {
                return redirect()->back()->with('error', 'You cannot remove admin privileges from yourself.');
            }

            $user->update(['is_admin' => false]);

            return redirect()->back()->with('success', "Admin privileges removed from {$user->name}.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to remove admin privileges: ' . $e->getMessage());
        }
    }

    /**
     * Show instrument rentals management page
     */
    public function instrumentRentals()
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        $rentals = InstrumentRental::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.instrument-rentals', compact('user', 'rentals'));
    }

    /**
     * Update instrument rental status
     */
    public function updateRentalStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,active,returned,cancelled'
        ]);

        try {
            $rental = InstrumentRental::findOrFail($id);
            $rental->update(['status' => $request->status]);

            return redirect()->back()->with('success', "Rental status updated to {$request->status}.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update rental status: ' . $e->getMessage());
        }
    }
} 