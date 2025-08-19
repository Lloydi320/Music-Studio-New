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
use App\Models\ActivityLog;

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
        
        // Log admin dashboard access
        ActivityLog::logAdmin('Admin accessed dashboard', ActivityLog::ACTION_ADMIN_ACCESS);
        $totalBookings = Booking::whereIn('status', ['pending', 'confirmed'])->count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();
        $recentBookings = Booking::with('user')->whereIn('status', ['pending', 'confirmed'])->latest()->take(10)->get();
        
        // Add cancelled bookings query
        $cancelledBookings = Booking::where('status', 'cancelled')->latest()->take(10)->get();
        $cancelledBookingsCount = Booking::where('status', 'cancelled')->count();
        
        // Instrument rental statistics
        $totalRentals = InstrumentRental::count();
        $pendingRentals = InstrumentRental::where('status', 'pending')->count();
        $activeRentals = InstrumentRental::where('status', 'active')->count();
        $recentRentals = InstrumentRental::with('user')->latest()->take(10)->get();
        
        // Get admin users data for dashboard
        $admins = User::where('is_admin', true)->get();
        $adminUsers = DB::table('admin_users')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        // Studio Analytics Data - Connected to Real Services
        $confirmedAndActiveRentals = InstrumentRental::whereIn('status', ['confirmed', 'active'])->count();
        $totalServices = $confirmedBookings + $confirmedAndActiveRentals; // Total active services
        $lostServices = $cancelledBookingsCount + ($totalRentals - $confirmedAndActiveRentals); // Lost services
        $serviceEfficiency = $totalServices > 0 ? round(($totalServices / ($totalServices + $lostServices)) * 100, 2) : 0;
        $efficiencyChange = -0.73; // Can be calculated from historical data
        
        // Get service type analytics from the unified booking system
        $serviceTypeAnalytics = Booking::getServiceTypeAnalytics();
        
        // Calculate total revenue from all confirmed bookings
        $totalBookingRevenue = Booking::where('status', 'confirmed')->sum('total_amount') ?? 0;
        $totalRentalRevenue = InstrumentRental::whereIn('status', ['confirmed', 'active'])->sum('total_amount') ?? 0;
        $estimatedRevenue = $totalBookingRevenue + $totalRentalRevenue;
        $operatingCosts = $totalBookings * 200; // Estimated operating costs per booking
        
        // Services by type (real data from service_type field)
        $servicesByType = [];
        foreach ($serviceTypeAnalytics as $key => $data) {
            $servicesByType[$data['label']] = $data['confirmed'];
        }
        // Add instrument rentals as separate service (include both confirmed and active)
        $servicesByType['Instrument Rentals'] = $confirmedAndActiveRentals;
        
        // Staff allocation by function
        $staffByFunction = [
            'Sound Engineers' => 35.0,
            'Music Instructors' => 28.5,
            'Studio Assistants' => 20.0,
            'Equipment Technicians' => 16.5
        ];
        
        // Studio utilization rates
        $studioAUtilization = 85.20; // Main recording studio
        $studioBUtilization = 72.40; // Practice/lesson rooms
        
        // Monthly cost vs revenue data (last 12 months)
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyBookings = Booking::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $monthlyRentals = InstrumentRental::whereIn('status', ['confirmed', 'active'])
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $monthlyData[$date->format('M')] = [
                'cost' => ($monthlyBookings + $monthlyRentals) * 500,
                'revenue' => ($monthlyBookings * 1200) + ($monthlyRentals * 300)
            ];
        }
        
        // Analytics data for the dashboard
        $totalRevenue = $estimatedRevenue;
        $thisMonthRevenue = $monthlyData[now()->format('M')]['revenue'] ?? 0;
        $lastMonthRevenue = $monthlyData[now()->subMonth()->format('M')]['revenue'] ?? 0;
        $averageBookingValue = $totalBookings > 0 ? round($totalRevenue / $totalBookings, 2) : 0;
        
        // Monthly data for charts
        $months = array_keys($monthlyData);
        $salesData = array_values(array_column($monthlyData, 'revenue'));
        $bookingCounts = [];
        
        // Get booking counts for each month (including instrument rentals)
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyBookings = Booking::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $monthlyRentals = InstrumentRental::whereIn('status', ['confirmed', 'active'])
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $bookingCounts[] = $monthlyBookings + $monthlyRentals;
        }
        
        // Top customers data (including both bookings and instrument rentals)
        $topCustomers = DB::table('users')
            ->leftJoin('bookings', function($join) {
                $join->on('users.id', '=', 'bookings.user_id')
                     ->whereIn('bookings.status', ['confirmed', 'completed']);
            })
            ->leftJoin('instrument_rentals', function($join) {
                $join->on('users.id', '=', 'instrument_rentals.user_id')
                     ->whereIn('instrument_rentals.status', ['confirmed', 'active']);
            })
            ->select('users.name', 'users.email',
                DB::raw('COUNT(DISTINCT bookings.id) as booking_count'),
                DB::raw('COUNT(DISTINCT instrument_rentals.id) as rental_count'),
                DB::raw('(COALESCE(SUM(DISTINCT bookings.price), 0) + COALESCE(SUM(DISTINCT instrument_rentals.total_amount), 0)) as total_spent'))
            ->groupBy('users.id', 'users.name', 'users.email')
            ->havingRaw('(booking_count > 0 OR rental_count > 0)')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();

        // Users per service data for bar chart (using real service_type data)
        $usersPerService = [];
        foreach (Booking::getServiceTypes() as $key => $label) {
            $usersPerService[$label] = DB::table('bookings')
                ->join('users', 'bookings.user_id', '=', 'users.id')
                ->where('bookings.status', 'confirmed')
                ->where('bookings.service_type', $key)
                ->distinct('users.id')
                ->count();
        }
        // Add instrument rentals (include both confirmed and active)
        $usersPerService['Instrument Rentals'] = DB::table('instrument_rentals')
             ->join('users', 'instrument_rentals.user_id', '=', 'users.id')
             ->whereIn('instrument_rentals.status', ['confirmed', 'active'])
             ->distinct('users.id')
             ->count();

        // Services distribution for pie chart (using real service_type data)
        $servicesDistribution = [];
        foreach ($serviceTypeAnalytics as $key => $data) {
            $servicesDistribution[$data['label']] = $data['confirmed'];
        }
        // Add instrument rentals as separate service (include both confirmed and active)
        $servicesDistribution['Instrument Rentals'] = InstrumentRental::whereIn('status', ['confirmed', 'active'])->count();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'thisMonthRevenue', 
            'lastMonthRevenue',
            'averageBookingValue',
            'months',
            'salesData',
            'bookingCounts',
            'topCustomers',
            'usersPerService',
            'servicesDistribution',
            'serviceTypeAnalytics',
            'totalBookings',
            'pendingBookings',
            'confirmedBookings',
            'totalRentals',
            'activeRentals',
            'pendingRentals'
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
                // Setup webhook for automatic sync
                $webhookSetup = $this->calendarService->setupWebhook($user);
                
                $message = 'Google Calendar connected successfully!';
                if ($webhookSetup) {
                    $message .= ' Automatic sync enabled.';
                } else {
                    $message .= ' Note: Automatic sync setup failed, but manual sync is available.';
                }
                
                return redirect('/admin/calendar')->with('success', $message);
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
            
            // Stop webhook before disconnecting
            if ($this->calendarService) {
                $this->calendarService->stopWebhook($user);
            }
            
            $user->update([
                'google_calendar_token' => null,
                'google_calendar_id' => null,
                'google_webhook_channel_id' => null,
                'google_webhook_resource_id' => null,
                'google_webhook_expiration' => null
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
        // Check if current user is a super admin
        $currentUser = Auth::user();
        $currentAdminUser = DB::table('admin_users')->where('email', $currentUser->email)->first();
        
        if (!$currentAdminUser || $currentAdminUser->role !== 'super_admin') {
            return redirect()->back()->with('error', 'Only super admins can add new administrators.');
        }

        $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        try {
            $user = User::where('email', $request->email)->first();
            if ($user) {
                // Check if user is already an admin
                if ($user->is_admin) {
                    return redirect()->back()->with('error', 'User is already an admin.');
                }
                
                $user->update(['is_admin' => true]);
                
                // Also add to admin_users table with default admin role
                DB::table('admin_users')->updateOrInsert(
                    ['email' => $user->email],
                    [
                        'name' => $user->name,
                        'email' => $user->email,
                        'role' => 'admin',
                        'permissions' => json_encode(['manage_bookings', 'view_dashboard', 'manage_calendar']),
                        'is_active' => true,
                        'created_by' => $currentUser->email,
                        'notes' => 'Added by super admin via admin panel',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
                
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
        // Check if current user is a super admin
        $currentUser = Auth::user();
        $currentAdminUser = DB::table('admin_users')->where('email', $currentUser->email)->first();
        
        if (!$currentAdminUser || $currentAdminUser->role !== 'super_admin') {
            return redirect()->back()->with('error', 'Only super admins can remove administrators.');
        }

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
     * Show analytics dashboard with service statistics
     */
    public function analytics()
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        // Get recent bookings and rentals
        $confirmedBookings = Booking::with('user')
            ->where('status', 'confirmed')
            ->latest()
            ->get();
            
        $pendingBookings = Booking::with('user')
            ->where('status', 'pending')
            ->latest()
            ->get();
            
        $cancelledBookings = Booking::with('user')
            ->where('status', 'cancelled')
            ->latest()
            ->get();

        $activeRentals = InstrumentRental::with('user')
            ->where('status', 'active')
            ->latest()
            ->get();
            
        $pendingRentals = InstrumentRental::with('user')
            ->where('status', 'pending')
            ->latest()
            ->get();

        // Simple service categorization
        $confirmedAndActiveRentals = InstrumentRental::whereIn('status', ['confirmed', 'active'])->count();
        $serviceCategories = [
            'Studio Bookings' => $confirmedBookings->count(),
            'Instrument Rentals' => $confirmedAndActiveRentals,
            'Pending Services' => $pendingBookings->count() + $pendingRentals->count()
        ];

        // Simple revenue calculation
        $revenueByService = [
            'Studio Bookings' => $confirmedBookings->count() * 800, // Average booking price
            'Instrument Rentals' => $confirmedAndActiveRentals * 300, // Average rental price
            'Pending Services' => 0
        ];
        
        $totalRevenue = array_sum($revenueByService);

        // Top customers (including both bookings and instrument rentals)
        $topCustomers = DB::table('users')
            ->leftJoin('bookings', function($join) {
                $join->on('users.id', '=', 'bookings.user_id')
                     ->where('bookings.status', 'confirmed');
            })
            ->leftJoin('instrument_rentals', function($join) {
                $join->on('users.id', '=', 'instrument_rentals.user_id')
                     ->whereIn('instrument_rentals.status', ['confirmed', 'active']);
            })
            ->select('users.name', 'users.email',
                DB::raw('COUNT(DISTINCT bookings.id) as booking_count'),
                DB::raw('COUNT(DISTINCT instrument_rentals.id) as rental_count'),
                DB::raw('(COUNT(DISTINCT bookings.id) * 800 + COUNT(DISTINCT instrument_rentals.id) * 300) as total_spent'))
            ->groupBy('users.id', 'users.name', 'users.email')
            ->havingRaw('(booking_count > 0 OR rental_count > 0)')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();

        return view('admin.analytics', compact(
            'user',
            'serviceCategories',
            'revenueByService',
            'totalRevenue',
            'topCustomers',
            'confirmedBookings',
            'pendingBookings',
            'cancelledBookings',
            'activeRentals',
            'pendingRentals'
        ));
    }

    /**
     * Show bookings management page
     */
    public function bookings(Request $request)
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        // Get filter parameters
        $status = $request->get('status', 'all');
        $search = $request->get('search', '');
        $dateFilter = $request->get('date_filter', 'all');

        // Build query
        $query = Booking::with(['user'])
            ->orderBy('date', 'desc')
            ->orderBy('time_slot', 'desc');

        // Apply status filter
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Apply search filter
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', '%' . $search . '%')
                             ->orWhere('email', 'like', '%' . $search . '%');
                })
                ->orWhere('service_type', 'like', '%' . $search . '%')
                ->orWhere('notes', 'like', '%' . $search . '%');
            });
        }

        // Apply date filter
        if ($dateFilter !== 'all') {
            switch ($dateFilter) {
                case 'today':
                    $query->whereDate('date', today());
                    break;
                case 'week':
                    $query->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('date', now()->month)
                          ->whereYear('date', now()->year);
                    break;
            }
        }

        $bookings = $query->paginate(15);

        // Get counts for status badges
        $statusCounts = [
            'all' => Booking::count(),
            'pending' => Booking::where('status', 'pending')->count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'rejected' => Booking::where('status', 'rejected')->count(),
        ];

        return view('admin.bookings', compact(
            'user',
            'bookings',
            'statusCounts',
            'status',
            'search',
            'dateFilter'
        ));
    }

    /**
     * Show create booking form
     */
    public function createBooking()
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        return redirect()->route('booking')->with('info', 'Use the main booking form to create new bookings.');
    }

    /**
     * Show individual booking details
     */
    public function showBooking($id)
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        $booking = Booking::with('user')->findOrFail($id);
        
        // Log booking view
        ActivityLog::logAdmin("Admin viewed booking details: {$booking->reference}", ActivityLog::ACTION_ADMIN_ACCESS);
        
        return view('admin.booking-details', compact('user', 'booking'));
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
            
            // Store booking details for success message and logging
            $bookingReference = $booking->reference;
            $clientName = $booking->user->name ?? 'Unknown';
            $bookingData = $booking->toArray();
            
            // Log booking deletion before deleting
            ActivityLog::logBooking(
                ActivityLog::ACTION_BOOKING_DELETED,
                $booking,
                $bookingData,
                null
            );
            
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

    /**
     * Approve a pending booking
     */
    public function approveBooking($id)
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        try {
            $booking = Booking::findOrFail($id);
            
            if ($booking->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending bookings can be approved.');
            }
            
            // Store old values for logging
            $oldValues = $booking->toArray();
            
            // Update booking status to confirmed
            $booking->update(['status' => 'confirmed']);
            
            // Log booking approval
            ActivityLog::logBooking(
                ActivityLog::ACTION_BOOKING_APPROVED,
                $booking,
                $oldValues,
                $booking->fresh()->toArray()
            );
            
            // Create Google Calendar event for approved booking
            $calendarSyncMessage = '';
            if ($this->calendarService) {
                try {
                    $result = $this->calendarService->createBookingEvent($booking);
                    if ($result) {
                        $calendarSyncMessage = ' Google Calendar event has been automatically created.';
                        Log::info('Google Calendar event created for approved booking', [
                            'booking_id' => $booking->id,
                            'reference' => $booking->reference
                        ]);
                    } else {
                        $calendarSyncMessage = ' Warning: Google Calendar event creation failed.';
                    }
                } catch (\Exception $e) {
                    $calendarSyncMessage = ' Warning: Google Calendar sync failed - ' . $e->getMessage();
                    Log::warning('Failed to create Google Calendar event for approved booking', [
                        'booking_id' => $booking->id,
                        'error' => $e->getMessage()
                    ]);
                    // Continue with approval even if calendar creation fails
                }
            } else {
                $calendarSyncMessage = ' Note: Google Calendar service is not available.';
            }
            
            return redirect()->back()->with('success', "Booking {$booking->reference} for {$booking->user->name} has been approved and confirmed.{$calendarSyncMessage}");
        } catch (\Exception $e) {
            Log::error('Failed to approve booking', [
                'booking_id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to approve booking: ' . $e->getMessage());
        }
    }

    /**
     * Reject a pending booking
     */
    public function rejectBooking($id)
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        try {
            $booking = Booking::findOrFail($id);
            
            if ($booking->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending bookings can be rejected.');
            }
            
            // Store old values for logging
            $oldValues = $booking->toArray();
            $bookingReference = $booking->reference;
            $customerName = $booking->user->name;
            
            // Log booking rejection before deletion
            ActivityLog::logBooking(
                ActivityLog::ACTION_BOOKING_REJECTED,
                $booking,
                $oldValues,
                ['status' => 'deleted']
            );
            
            // Delete from Google Calendar if event exists
            if ($booking->google_event_id && $this->calendarService) {
                try {
                    $this->calendarService->deleteBookingEvent($booking);
                    Log::info('Google Calendar event deleted for rejected booking', [
                        'booking_id' => $booking->id,
                        'google_event_id' => $booking->google_event_id
                    ]);
                } catch (\Exception $e) {
                    Log::warning('Failed to delete Google Calendar event for rejected booking', [
                        'booking_id' => $booking->id,
                        'google_event_id' => $booking->google_event_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            // Delete the booking from database
            $booking->delete();
            
            Log::info('Booking rejected and deleted by admin', [
                'booking_id' => $oldValues['id'],
                'reference' => $bookingReference,
                'admin_id' => $user->id
            ]);
            
            return redirect()->back()->with('success', "Booking {$bookingReference} for {$customerName} has been rejected and automatically deleted from the system.");
        } catch (\Exception $e) {
            Log::error('Failed to reject booking', [
                'booking_id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to reject booking: ' . $e->getMessage());
        }
    }

    /**
     * Approve a pending instrument rental
     */
    public function approveRental($id)
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        try {
            $rental = InstrumentRental::findOrFail($id);
            
            if ($rental->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending rentals can be approved.');
            }
            
            // Update rental status to confirmed
            $rental->update(['status' => 'confirmed']);
            
            Log::info('Instrument rental approved by admin', [
                'rental_id' => $rental->id,
                'reference' => $rental->reference,
                'admin_id' => $user->id
            ]);
            
            return redirect()->back()->with('success', "Rental {$rental->reference} for {$rental->user->name} has been approved and confirmed.");
        } catch (\Exception $e) {
            Log::error('Failed to approve rental', [
                'rental_id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to approve rental: ' . $e->getMessage());
        }
    }

    /**
     * Reject a pending instrument rental
     */
    public function rejectRental($id)
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        try {
            $rental = InstrumentRental::findOrFail($id);
            
            if ($rental->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending rentals can be rejected.');
            }
            
            // Update rental status to cancelled
            $rental->update(['status' => 'cancelled']);
            
            Log::info('Instrument rental rejected by admin', [
                'rental_id' => $rental->id,
                'reference' => $rental->reference,
                'admin_id' => $user->id
            ]);
            
            return redirect()->back()->with('success', "Rental {$rental->reference} for {$rental->user->name} has been rejected.");
        } catch (\Exception $e) {
            Log::error('Failed to reject rental', [
                'rental_id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to reject rental: ' . $e->getMessage());
        }
    }

    /**
     * Show admin users management page
     */
    public function users()
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        return view('admin.users');
    }

    /**
     * Show activity logs / audit trail
     */
    public function activityLogs(Request $request)
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        $query = \App\Models\ActivityLog::query();

        // Apply filters
        if ($request->filled('user')) {
            $query->where('user_name', 'like', '%' . $request->user . '%');
        }

        if ($request->filled('action_type')) {
            $query->where('action_type', $request->action_type);
        }

        if ($request->filled('severity_level')) {
            $query->where('severity_level', $request->severity_level);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Get paginated results
        $activityLogs = $query->orderBy('created_at', 'desc')->paginate(50);
        $totalRecords = \App\Models\ActivityLog::count();

        return view('admin.activity-logs', compact('activityLogs', 'totalRecords'));
    }

    public function instrumentBookings(Request $request)
    {
        $query = InstrumentRental::with('user')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('instrument_type')) {
            $query->where('instrument_type', $request->instrument_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('rental_start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('rental_end_date', '<=', $request->date_to);
        }

        $instrumentRentals = $query->paginate(20);
        $totalRentals = InstrumentRental::count();
        
        // Get unique instrument types for filter
        $instrumentTypes = InstrumentRental::distinct('instrument_type')
            ->pluck('instrument_type')
            ->filter()
            ->sort()
            ->values();

        return view('admin.instrument-bookings', compact('instrumentRentals', 'totalRentals', 'instrumentTypes'));
    }

    /**
     * Show individual instrument rental details
     */
    public function showInstrumentRental($id)
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        $rental = InstrumentRental::with('user')->findOrFail($id);
        
        return view('admin.instrument-rental-details', compact('rental'));
    }

    public function musicLessonBookings(Request $request)
    {
        // Filter bookings that are likely music lessons (duration <= 120 minutes)
        $query = Booking::with('user')
            ->where('duration', '<=', 120)
            ->whereIn('time_slot', ['10:00', '14:00', '16:00', '18:00']) // Common lesson times
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date', '<=', $request->date_to);
        }

        if ($request->filled('user')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user . '%')
                  ->orWhere('email', 'like', '%' . $request->user . '%');
            });
        }

        $musicLessonBookings = $query->paginate(20);
        $totalLessons = Booking::where('duration', '<=', 120)
            ->whereIn('time_slot', ['10:00', '14:00', '16:00', '18:00'])
            ->count();

        return view('admin.music-lesson-bookings', compact('musicLessonBookings', 'totalLessons'));
    }

    /**
     * Clear all activity logs
     */
    public function clearActivityLogs()
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        try {
            // Get count before deletion for logging
            $deletedCount = \App\Models\ActivityLog::count();
            
            // Clear all activity logs
            \App\Models\ActivityLog::truncate();
            
            // Log this action (will create a new entry after clearing)
            \App\Models\ActivityLog::logAdmin(
                "Admin cleared all activity logs ({$deletedCount} entries deleted)",
                \App\Models\ActivityLog::ACTION_SYSTEM_CHANGE,
                \App\Models\ActivityLog::SEVERITY_HIGH
            );
            
            return redirect()->route('admin.activity-logs')->with('success', "Successfully cleared {$deletedCount} activity log entries.");
        } catch (\Exception $e) {
            return redirect()->route('admin.activity-logs')->with('error', 'Failed to clear activity logs: ' . $e->getMessage());
        }
    }
    
    /**
     * Get new booking notifications for admin
     */
    public function getNewBookingNotifications()
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            return response()->json(['error' => 'Access denied'], 403);
        }
        
        try {
            // Get bookings from the last 24 hours that haven't been viewed
            $newBookings = Booking::where('created_at', '>=', now()->subDay())
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($booking) {
                    return [
                        'id' => $booking->id,
                        'customer_name' => $booking->customer_name,
                        'studio_name' => 'Studio Rental', // You can customize this based on your studio types
                        'date' => $booking->date,
                        'time_slot' => $booking->time_slot,
                        'created_at' => $booking->created_at->toISOString(),
                    ];
                });
            
            return response()->json([
                'count' => $newBookings->count(),
                'bookings' => $newBookings
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching new booking notifications: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch notifications'], 500);
        }
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead()
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            return response()->json(['error' => 'Access denied'], 403);
        }
        
        try {
            // For now, we'll just return success
            // In a more complex implementation, you might want to track which notifications have been read
            
            ActivityLog::logAdmin('Admin marked all notifications as read', ActivityLog::ACTION_ADMIN_ACCESS);
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            Log::error('Error marking notifications as read: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to mark notifications as read'], 500);
        }
    }
}
