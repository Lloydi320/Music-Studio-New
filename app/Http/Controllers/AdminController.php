<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
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

        // Get Google Calendar events if connected
        $calendarEvents = [];
        $upcomingEvents = [];
        if ($user->hasGoogleCalendarAccess() && $this->calendarService) {
            try {
                $calendarEvents = $this->calendarService->getCalendarEvents($user);
                $upcomingEvents = $this->calendarService->getUpcomingEvents($user, 10);
            } catch (\Exception $e) {
                Log::warning('Failed to fetch calendar events: ' . $e->getMessage());
            }
        }

        // Get system bookings for comparison
        $systemBookings = Booking::with('user')
            ->where('status', 'confirmed')
            ->where('date', '>=', now()->format('Y-m-d'))
            ->orderBy('date')
            ->orderBy('time_slot')
            ->take(20)
            ->get();

        return view('admin.calendar', compact('user', 'calendarEvents', 'upcomingEvents', 'systemBookings'));
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
        // Handle both regular users and admin_users only records
        if ($request->has('admin_email')) {
            $request->validate([
                'admin_email' => 'required|email'
            ]);
        } else {
            $request->validate([
                'user_id' => 'required|exists:users,id'
            ]);
        }

        try {
            if ($request->has('admin_email')) {
                // Remove admin that only exists in admin_users table
                $adminEmail = $request->admin_email;
                $adminUser = DB::table('admin_users')->where('email', $adminEmail)->first();
                
                if (!$adminUser) {
                    return redirect()->back()->with('error', 'Admin user not found.');
                }
                
                // Prevent removing current user's admin privileges
                if ($adminEmail === Auth::user()->email) {
                    return redirect()->back()->with('error', 'You cannot remove admin privileges from yourself.');
                }
                
                // Prevent removing super admin privileges
                if ($adminUser->role === 'super_admin') {
                    return redirect()->back()->with('error', 'Super admin privileges cannot be removed.');
                }
                
                DB::table('admin_users')->where('email', $adminEmail)->delete();
                return redirect()->back()->with('success', "Admin privileges removed from {$adminUser->name}.");
            } else {
                // Remove admin from regular users table
                $user = User::findOrFail($request->user_id);
                
                // Prevent removing admin from current user
                if ($user->id === Auth::id()) {
                    return redirect()->back()->with('error', 'You cannot remove admin privileges from yourself.');
                }

                // Check if user is a super admin and prevent removal
                $adminUser = DB::table('admin_users')->where('email', $user->email)->first();
                if ($adminUser && $adminUser->role === 'super_admin') {
                    return redirect()->back()->with('error', 'Super admin privileges cannot be removed.');
                }

                // Update main users table
                $user->update(['is_admin' => false]);
                
                // Remove from admin_users table if exists
                DB::table('admin_users')->where('email', $user->email)->delete();

                return redirect()->back()->with('success', "Admin privileges removed from {$user->name}.");
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to remove admin privileges: ' . $e->getMessage());
        }
    }

    /**
     * Show database management page
     */
    public function database()
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        // Get database statistics
        $totalBookings = Booking::count();
        $totalUsers = User::count();
        $adminUsers = User::where('is_admin', true)->count();
        $recentBookings = Booking::with('user')->latest()->take(10)->get();
        
        // Get Google Calendar integration statistics
        $calendarConnectedAdmins = User::where('is_admin', true)
                                      ->whereNotNull('google_calendar_token')
                                      ->count();
        
        $syncedBookings = Booking::where('status', 'confirmed')
                                ->whereNotNull('google_event_id')
                                ->count();
        
        $unsyncedBookings = Booking::where('status', 'confirmed')
                                  ->whereNull('google_event_id')
                                  ->count();
        
        $totalCalendarEvents = Booking::whereNotNull('google_event_id')->count();
        
        // Calculate approximate database size
        try {
            $databaseName = config('database.connections.mysql.database');
            $sizeQuery = DB::select("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) AS 'size_mb' FROM information_schema.tables WHERE table_schema = ?", [$databaseName]);
            $databaseSize = ($sizeQuery[0]->size_mb ?? 0) . ' MB';
        } catch (\Exception $e) {
            $databaseSize = 'Unknown';
        }
        
        return view('admin.database', compact(
            'user',
            'totalBookings',
            'totalUsers', 
            'adminUsers',
            'recentBookings',
            'databaseSize',
            'calendarConnectedAdmins',
            'syncedBookings',
            'unsyncedBookings',
            'totalCalendarEvents'
        ));
    }

    /**
     * Create database backup
     */
    public function createBackup()
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        try {
            $databaseName = config('database.connections.mysql.database');
            $backupFileName = 'backup_' . $databaseName . '_' . date('Y-m-d_H-i-s') . '.sql';
            $backupPath = storage_path('app/backups/' . $backupFileName);
            
            // Create backups directory if it doesn't exist
            if (!file_exists(dirname($backupPath))) {
                mkdir(dirname($backupPath), 0755, true);
            }
            
            // Use mysqldump command (requires mysqldump to be available)
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            
            $command = "mysqldump -h{$host} -P{$port} -u{$username}";
            if ($password) {
                $command .= " -p{$password}";
            }
            $command .= " {$databaseName} > {$backupPath}";
            
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0 && file_exists($backupPath)) {
                return response()->download($backupPath)->deleteFileAfterSend(true);
            } else {
                return redirect()->back()->with('error', 'Failed to create database backup. Please ensure mysqldump is available.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create backup: ' . $e->getMessage());
        }
    }

    /**
     * Run database migrations
     */
    public function runMigrations()
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        try {
            Artisan::call('migrate', ['--force' => true]);
            $output = Artisan::output();
            
            return redirect()->back()->with('success', 'Database migrations completed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to run migrations: ' . $e->getMessage());
        }
    }

    /**
     * Clear application cache
     */
    public function clearCache()
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        try {
            // Clear various caches
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            
            return redirect()->back()->with('success', 'All caches cleared successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to clear cache: ' . $e->getMessage());
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

    /**
     * Delete a booking and remove from Google Calendar
     */
    public function deleteBooking($id)
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        try {
            $booking = Booking::findOrFail($id);
            
            // Delete from Google Calendar if event exists
            if ($booking->google_event_id && $this->calendarService) {
                try {
                    $this->calendarService->deleteBookingEvent($booking);
                    Log::info('Google Calendar event deleted for booking', [
                        'booking_id' => $booking->id,
                        'event_id' => $booking->google_event_id
                    ]);
                } catch (\Exception $e) {
                    Log::warning('Failed to delete Google Calendar event', [
                        'booking_id' => $booking->id,
                        'error' => $e->getMessage()
                    ]);
                    // Continue with booking deletion even if calendar deletion fails
                }
            }
            
            // Store booking details for success message
            $bookingReference = $booking->reference;
            $clientName = $booking->user->name ?? 'Unknown';
            
            // Delete the booking from database
            $booking->delete();
            
            return redirect()->back()->with('success', "Booking {$bookingReference} for {$clientName} has been deleted successfully.");
        } catch (\Exception $e) {
            Log::error('Failed to delete booking', [
                'booking_id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to delete booking: ' . $e->getMessage());
        }
    }
}
