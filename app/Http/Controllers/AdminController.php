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
            if ($key === 'instrument_rental') {
                // For instrument rentals, get data from instrument_rentals table
                $usersPerService[$label] = DB::table('instrument_rentals')
                    ->join('users', 'instrument_rentals.user_id', '=', 'users.id')
                    ->whereIn('instrument_rentals.status', ['confirmed', 'active'])
                    ->distinct('users.id')
                    ->count();
            } else {
                // For other services, get data from bookings table
                $usersPerService[$label] = DB::table('bookings')
                    ->join('users', 'bookings.user_id', '=', 'users.id')
                    ->where('bookings.status', 'confirmed')
                    ->where('bookings.service_type', $key)
                    ->distinct('users.id')
                    ->count();
            }
        }


        // Services distribution for pie chart (using real service_type data)
        $servicesDistribution = [];
        foreach ($serviceTypeAnalytics as $key => $data) {
            if ($key === 'instrument_rental') {
                // For instrument rentals, get data from instrument_rentals table
                $servicesDistribution[$data['label']] = InstrumentRental::whereIn('status', ['confirmed', 'active'])->count();
            } else {
                // For other services, get data from bookings table
                $servicesDistribution[$data['label']] = $data['confirmed'];
            }
        }


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
    public function analytics(Request $request)
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

        // Handle export requests
        if ($request->has('export')) {
            $exportType = $request->get('export');
            
            if ($exportType === 'csv') {
                return $this->exportCSV($serviceCategories, $revenueByService, $totalRevenue, $topCustomers, $confirmedBookings, $pendingBookings, $activeRentals);
            } elseif ($exportType === 'excel') {
                return $this->exportExcel($serviceCategories, $revenueByService, $totalRevenue, $topCustomers, $confirmedBookings, $pendingBookings, $activeRentals);
            } elseif ($exportType === 'pdf') {
                return $this->exportPDF($serviceCategories, $revenueByService, $totalRevenue, $topCustomers, $confirmedBookings, $pendingBookings, $activeRentals);
            }
        }

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
     * Export analytics data as CSV
     */
    private function exportCSV($serviceCategories, $revenueByService, $totalRevenue, $topCustomers, $confirmedBookings, $pendingBookings, $activeRentals)
    {
        $filename = 'analytics_report_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($serviceCategories, $revenueByService, $totalRevenue, $topCustomers, $confirmedBookings, $pendingBookings, $activeRentals) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for proper UTF-8 encoding in Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Header Section
            fputcsv($file, ['ANALYTICS SUMMARY REPORT', '', '', '', '', '']);
            fputcsv($file, ['Generated on: ' . date('Y-m-d H:i:s'), '', '', '', '', '']);
            fputcsv($file, ['', '', '', '', '', '']); // Empty row
            
            // Service Summary Section
            fputcsv($file, ['SERVICE SUMMARY', '', '', '', '', '']);
            fputcsv($file, ['', '', '', '', '', '']); // Empty row
            fputcsv($file, ['Service Type', 'Count', 'Revenue', '', '', '']);
            foreach ($serviceCategories as $service => $count) {
                fputcsv($file, [
                    $service, 
                    $count, 
                    number_format($revenueByService[$service] ?? 0, 2), 
                    '', 
                    '', 
                    ''
                ]);
            }
            fputcsv($file, ['TOTAL REVENUE', '', number_format($totalRevenue, 2), '', '', '']);
            fputcsv($file, ['', '', '', '', '', '']); // Empty row
            fputcsv($file, ['', '', '', '', '', '']); // Empty row
            
            // Confirmed Bookings Section
            fputcsv($file, ['CONFIRMED BOOKINGS', '', '', '', '', '']);
            fputcsv($file, ['', '', '', '', '', '']); // Empty row
            fputcsv($file, ['Date', 'Client', 'Time Slot', 'Status', 'Duration', '']);
            foreach ($confirmedBookings->take(20) as $booking) {
                fputcsv($file, [
                    $booking->date ?? 'N/A',
                    $booking->user->name ?? 'N/A',
                    $booking->time_slot ?? 'N/A',
                    ucfirst($booking->status ?? 'N/A'),
                    ($booking->duration ?? 'N/A') . ' hours',
                    ''
                ]);
            }
            fputcsv($file, ['', '', '', '', '', '']); // Empty row
            fputcsv($file, ['', '', '', '', '', '']); // Empty row
            
            // Pending Bookings Section
            fputcsv($file, ['PENDING BOOKINGS', '', '', '', '', '']);
            fputcsv($file, ['', '', '', '', '', '']); // Empty row
            fputcsv($file, ['Date', 'Client', 'Time Slot', 'Status', 'Duration', '']);
            foreach ($pendingBookings->take(20) as $booking) {
                fputcsv($file, [
                    $booking->date ?? 'N/A',
                    $booking->user->name ?? 'N/A',
                    $booking->time_slot ?? 'N/A',
                    ucfirst($booking->status ?? 'N/A'),
                    ($booking->duration ?? 'N/A') . ' hours',
                    ''
                ]);
            }
            fputcsv($file, ['', '', '', '', '', '']); // Empty row
            fputcsv($file, ['', '', '', '', '', '']); // Empty row
            
            // Active Rentals Section
            fputcsv($file, ['ACTIVE RENTALS', '', '', '', '', '']);
            fputcsv($file, ['', '', '', '', '', '']); // Empty row
            fputcsv($file, ['Start Date', 'End Date', 'Client', 'Instrument', 'Status', '']);
            foreach ($activeRentals->take(20) as $rental) {
                fputcsv($file, [
                    $rental->start_date ?? 'N/A',
                    $rental->end_date ?? 'N/A',
                    $rental->user->name ?? 'N/A',
                    ($rental->instrument_type ?? 'N/A') . ' - ' . ($rental->instrument_name ?? 'N/A'),
                    ucfirst($rental->status ?? 'N/A'),
                    ''
                ]);
            }
            fputcsv($file, ['', '', '', '', '', '']); // Empty row
            fputcsv($file, ['', '', '', '', '', '']); // Empty row
            
            // Top Customers Section
            fputcsv($file, ['TOP CUSTOMERS', '', '', '', '', '']);
            fputcsv($file, ['', '', '', '', '', '']); // Empty row
            fputcsv($file, ['Customer Name', 'Email', 'Total Services', 'Total Spent', '', '']);
            foreach ($topCustomers as $customer) {
                fputcsv($file, [
                    $customer->name ?? 'N/A',
                    $customer->email ?? 'N/A',
                    ($customer->booking_count ?? 0) + ($customer->rental_count ?? 0),
                    number_format($customer->total_spent ?? 0, 2),
                    '',
                    ''
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export analytics data as Excel (HTML format)
     */
    private function exportExcel($serviceCategories, $revenueByService, $totalRevenue, $topCustomers, $confirmedBookings, $pendingBookings, $activeRentals)
    {
        $filename = 'analytics_report_' . date('Y-m-d_H-i-s') . '.html';
        
        $headers = [
            'Content-Type' => 'text/html',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $html = $this->generateExcelHTML($serviceCategories, $revenueByService, $totalRevenue, $topCustomers, $confirmedBookings, $pendingBookings, $activeRentals);
        
        return response($html, 200, $headers);
    }

    /**
     * Generate well-formatted HTML for Excel export
     */
    private function generateExcelHTML($serviceCategories, $revenueByService, $totalRevenue, $topCustomers, $confirmedBookings, $pendingBookings, $activeRentals)
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Analytics Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .section { margin-bottom: 30px; }
        .section-title { font-size: 18px; font-weight: bold; color: #2c3e50; margin-bottom: 15px; border-bottom: 2px solid #3498db; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #3498db; color: white; padding: 12px; text-align: left; font-weight: bold; }
        td { padding: 10px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        tr:hover { background-color: #e8f4fd; }
        .summary-table th { background-color: #27ae60; }
        .total-row { font-weight: bold; background-color: #ecf0f1 !important; }
        .currency { text-align: right; }
        .center { text-align: center; }
        .status-confirmed { background-color: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px; }
        .status-pending { background-color: #fff3cd; color: #856404; padding: 4px 8px; border-radius: 4px; }
        .status-active { background-color: #cce5ff; color: #004085; padding: 4px 8px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Music Studio Analytics Report</h1>
        <p>Generated on: ' . date('F j, Y \a\t g:i A') . '</p>
    </div>

    <div class="section">
        <h2 class="section-title">📊 Summary Overview</h2>
        <table class="summary-table">
            <tr>
                <th>Service Type</th>
                <th class="center">Count</th>
                <th class="currency">Revenue</th>
            </tr>';

        foreach ($serviceCategories as $service => $count) {
            $html .= '<tr>
                <td>' . htmlspecialchars($service) . '</td>
                <td class="center">' . $count . '</td>
                <td class="currency">₱' . number_format($revenueByService[$service] ?? 0, 2) . '</td>
            </tr>';
        }
        
        $html .= '<tr class="total-row">
                <td><strong>TOTAL REVENUE</strong></td>
                <td class="center">-</td>
                <td class="currency"><strong>₱' . number_format($totalRevenue, 2) . '</strong></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2 class="section-title">✅ Confirmed Bookings</h2>
        <table>
            <tr>
                <th>Date</th>
                <th>Client</th>
                <th>Time Slot</th>
                <th class="center">Status</th>
                <th class="center">Duration</th>
            </tr>';

        foreach ($confirmedBookings->take(25) as $booking) {
            $html .= '<tr>
                <td>' . htmlspecialchars($booking->date) . '</td>
                <td>' . htmlspecialchars($booking->user->name ?? 'N/A') . '</td>
                <td>' . htmlspecialchars($booking->time_slot) . '</td>
                <td class="center"><span class="status-confirmed">' . ucfirst($booking->status) . '</span></td>
                <td class="center">' . htmlspecialchars($booking->duration ?? 'N/A') . ' hours</td>
            </tr>';
        }
        
        $html .= '</table>
    </div>

    <div class="section">
        <h2 class="section-title">⏳ Pending Bookings</h2>
        <table>
            <tr>
                <th>Date</th>
                <th>Client</th>
                <th>Time Slot</th>
                <th class="center">Status</th>
                <th class="center">Duration</th>
            </tr>';

        foreach ($pendingBookings->take(25) as $booking) {
            $html .= '<tr>
                <td>' . htmlspecialchars($booking->date) . '</td>
                <td>' . htmlspecialchars($booking->user->name ?? 'N/A') . '</td>
                <td>' . htmlspecialchars($booking->time_slot) . '</td>
                <td class="center"><span class="status-pending">' . ucfirst($booking->status) . '</span></td>
                <td class="center">' . htmlspecialchars($booking->duration ?? 'N/A') . ' hours</td>
            </tr>';
        }
        
        $html .= '</table>
    </div>

    <div class="section">
        <h2 class="section-title">🎸 Active Rentals</h2>
        <table>
            <tr>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Client</th>
                <th>Instrument</th>
                <th class="center">Status</th>
            </tr>';

        foreach ($activeRentals->take(25) as $rental) {
            $html .= '<tr>
                <td>' . htmlspecialchars($rental->start_date) . '</td>
                <td>' . htmlspecialchars($rental->end_date) . '</td>
                <td>' . htmlspecialchars($rental->user->name ?? 'N/A') . '</td>
                <td>' . htmlspecialchars($rental->instrument_type . ' - ' . $rental->instrument_name) . '</td>
                <td class="center"><span class="status-active">' . ucfirst($rental->status) . '</span></td>
            </tr>';
        }
        
        $html .= '</table>
    </div>

    <div class="section">
        <h2 class="section-title">👥 Top Customers</h2>
        <table>
            <tr>
                <th>Customer Name</th>
                <th>Email</th>
                <th class="center">Total Services</th>
                <th class="currency">Total Spent</th>
            </tr>';

        foreach ($topCustomers as $customer) {
            $html .= '<tr>
                <td>' . htmlspecialchars($customer->name) . '</td>
                <td>' . htmlspecialchars($customer->email) . '</td>
                <td class="center">' . ($customer->booking_count + $customer->rental_count) . '</td>
                <td class="currency">₱' . number_format($customer->total_spent, 2) . '</td>
            </tr>';
        }
        
        $html .= '</table>
    </div>

    <div class="section">
        <p style="text-align: center; color: #7f8c8d; font-style: italic; margin-top: 40px;">
            Report generated by Music Studio Management System<br>
            © ' . date('Y') . ' Music Studio. All rights reserved.
        </p>
    </div>

</body>
</html>';

        return $html;
    }

    /**
     * Export analytics data as PDF (placeholder)
     */
    private function exportPDF($serviceCategories, $revenueByService, $totalRevenue, $topCustomers, $confirmedBookings, $pendingBookings, $activeRentals)
    {
        // For now, return the same HTML as Excel but with PDF headers
        $filename = 'analytics_report_' . date('Y-m-d_H-i-s') . '.pdf';
        
        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $html = $this->generateExcelHTML($serviceCategories, $revenueByService, $totalRevenue, $topCustomers, $confirmedBookings, $pendingBookings, $activeRentals);
        
        return response($html, 200, $headers);
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
     * Reschedule a confirmed booking
     */
    public function rescheduleBooking(Request $request, $id)
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time_slot' => 'required|string',
            'duration' => 'required|integer|min:1|max:8'
        ]);

        try {
            $booking = Booking::findOrFail($id);
            
            if ($booking->status !== 'confirmed') {
                return redirect()->back()->with('error', 'Only confirmed bookings can be rescheduled.');
            }
            
            // Check for time slot conflicts
            $conflictingBooking = Booking::where('date', $request->date)
                ->where('time_slot', $request->time_slot)
                ->where('status', 'confirmed')
                ->where('id', '!=', $id)
                ->first();
                
            if ($conflictingBooking) {
                return redirect()->back()->with('error', 'The selected time slot is already booked.');
            }
            
            // Store old values for logging
            $oldValues = $booking->toArray();
            
            // Update booking details
            $booking->update([
                'date' => $request->date,
                'time_slot' => $request->time_slot,
                'duration' => $request->duration
            ]);
            
            // Log booking reschedule
            ActivityLog::logBooking(
                'Booking Rescheduled',
                $booking,
                $oldValues,
                $booking->fresh()->toArray()
            );
            
            // Update Google Calendar event if it exists
            $calendarSyncMessage = '';
            if ($booking->google_event_id && $this->calendarService) {
                try {
                    $result = $this->calendarService->updateBookingEvent($booking);
                    if ($result) {
                        $calendarSyncMessage = ' Google Calendar event has been updated.';
                        Log::info('Google Calendar event updated for rescheduled booking', [
                            'booking_id' => $booking->id,
                            'reference' => $booking->reference
                        ]);
                    } else {
                        $calendarSyncMessage = ' Warning: Google Calendar event update failed.';
                    }
                } catch (\Exception $e) {
                    $calendarSyncMessage = ' Warning: Google Calendar sync failed - ' . $e->getMessage();
                    Log::warning('Failed to update Google Calendar event for rescheduled booking', [
                        'booking_id' => $booking->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            return redirect()->back()->with('success', "Booking {$booking->reference} for {$booking->user->name} has been successfully rescheduled.{$calendarSyncMessage}");
        } catch (\Exception $e) {
            Log::error('Failed to reschedule booking', [
                'booking_id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Failed to reschedule booking: ' . $e->getMessage());
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
                ->limit(5)
                ->get()
                ->map(function ($booking) {
                    return [
                        'id' => $booking->id,
                        'type' => 'booking',
                        'customer_name' => $booking->customer_name,
                        'studio_name' => 'New Booking Request',
                        'date' => $booking->date,
                        'time_slot' => $booking->time_slot,
                        'created_at' => $booking->created_at->toISOString(),
                    ];
                });

            // Get reschedule requests from the last 24 hours
            $rescheduleRequests = \App\Models\ActivityLog::where('created_at', '>=', now()->subDay())
                ->where('description', 'LIKE', 'Reschedule Request Submitted:%')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($log) {
                    $booking = \App\Models\Booking::find($log->resource_id);
                    if (!$booking) return null;
                    
                    $newValues = $log->new_values ?? [];
                    return [
                        'id' => $log->id,
                        'type' => 'reschedule',
                        'customer_name' => $booking->customer_name,
                        'studio_name' => 'Reschedule Request',
                        'date' => $newValues['new_date'] ?? $booking->date,
                        'time_slot' => $newValues['new_time_slot'] ?? $booking->time_slot,
                        'original_date' => $booking->date,
                        'original_time_slot' => $booking->time_slot,
                        'booking_reference' => $booking->reference,
                        'created_at' => $log->created_at->toISOString(),
                    ];
                })
                ->filter(); // Remove null values

            // Combine and sort all notifications
            $allNotifications = $newBookings->concat($rescheduleRequests)
                ->sortByDesc('created_at')
                ->take(10)
                ->values();
            
            return response()->json([
                'count' => $allNotifications->count(),
                'bookings' => $allNotifications
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

    /**
     * Show reschedule request details
     */
    public function showRescheduleRequest($id)
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        try {
            // Find the activity log entry for the reschedule request
            $activityLog = \App\Models\ActivityLog::where('id', $id)
                ->where('description', 'LIKE', 'Reschedule Request Submitted:%')
                ->firstOrFail();

            // Get the associated booking
            $booking = \App\Models\Booking::findOrFail($activityLog->resource_id);

            // Get reschedule data from the activity log
            $rescheduleData = $activityLog->new_values ?? [];

            // Check for conflicts with the requested time slot
            $hasConflict = false;
            $conflictingBooking = null;
            
            if (isset($rescheduleData['new_date']) && isset($rescheduleData['new_time_slot'])) {
                $conflictingBooking = \App\Models\Booking::where('date', $rescheduleData['new_date'])
                    ->where('time_slot', $rescheduleData['new_time_slot'])
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->where('id', '!=', $booking->id)
                    ->first();
                    
                $hasConflict = $conflictingBooking !== null;
            }

            return view('admin.reschedule-details', compact(
                'activityLog',
                'booking',
                'rescheduleData',
                'hasConflict',
                'conflictingBooking'
            ));

        } catch (\Exception $e) {
            Log::error('Error showing reschedule request details: ' . $e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'Reschedule request not found.');
        }
    }

    /**
     * Approve a reschedule request
     */
    public function approveRescheduleRequest($id)
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        try {
            // Find the activity log entry for the reschedule request
            $activityLog = \App\Models\ActivityLog::where('id', $id)
                ->where('description', 'LIKE', 'Reschedule Request Submitted:%')
                ->firstOrFail();

            // Get the associated booking
            $booking = \App\Models\Booking::findOrFail($activityLog->resource_id);

            // Get reschedule data from the activity log
            $rescheduleData = $activityLog->new_values ?? [];

            // Validate reschedule data
            if (!isset($rescheduleData['new_date']) || !isset($rescheduleData['new_time_slot']) || !isset($rescheduleData['new_duration'])) {
                return redirect()->back()->with('error', 'Invalid reschedule data.');
            }

            // Check for conflicts again
            $conflictingBooking = \App\Models\Booking::where('date', $rescheduleData['new_date'])
                ->where('time_slot', $rescheduleData['new_time_slot'])
                ->whereIn('status', ['pending', 'confirmed'])
                ->where('id', '!=', $booking->id)
                ->first();
                
            if ($conflictingBooking) {
                return redirect()->back()->with('error', 'Cannot approve: The requested time slot is now occupied by another booking.');
            }

            // Store old values for logging
            $oldValues = $booking->toArray();

            // Update the booking with new details
            $booking->update([
                'date' => $rescheduleData['new_date'],
                'time_slot' => $rescheduleData['new_time_slot'],
                'duration' => $rescheduleData['new_duration']
            ]);

            // Log the approval
            \App\Models\ActivityLog::logBooking(
                'Reschedule Request Approved by Admin',
                $booking,
                $oldValues,
                $booking->toArray()
            );

            // Log admin action
            \App\Models\ActivityLog::logAdmin(
                'Admin approved reschedule request for booking ' . $booking->reference,
                \App\Models\ActivityLog::ACTION_ADMIN_BOOKING
            );

            // Try to sync with Google Calendar if connected
            $calendarSyncMessage = '';
            try {
                if ($user->google_calendar_id) {
                    $googleCalendarService = app(\App\Services\GoogleCalendarService::class);
                    $googleCalendarService->updateBookingEvent($booking);
                    $calendarSyncMessage = ' The booking has been updated in Google Calendar.';
                }
            } catch (\Exception $e) {
                Log::warning('Failed to update Google Calendar event after reschedule approval', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage()
                ]);
                $calendarSyncMessage = ' Note: Google Calendar sync failed.';
            }

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => "Reschedule request approved successfully! Booking {$booking->reference} has been updated.{$calendarSyncMessage}"
                ]);
            }
            
            return redirect()->route('admin.dashboard')
                ->with('success', "Reschedule request approved successfully! Booking {$booking->reference} has been updated.{$calendarSyncMessage}");

        } catch (\Exception $e) {
            Log::error('Error approving reschedule request: ' . $e->getMessage());
            
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to approve reschedule request: ' . $e->getMessage()]);
            }
            
            return redirect()->back()->with('error', 'Failed to approve reschedule request: ' . $e->getMessage());
        }
    }

    /**
     * Reject a reschedule request
     */
    public function rejectRescheduleRequest($id)
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        try {
            // Find the activity log entry for the reschedule request
            $activityLog = \App\Models\ActivityLog::where('id', $id)
                ->where('description', 'LIKE', 'Reschedule Request Submitted:%')
                ->firstOrFail();

            // Get the associated booking
            $booking = \App\Models\Booking::findOrFail($activityLog->resource_id);

            // Log the rejection
            \App\Models\ActivityLog::logBooking(
                'Reschedule Request Rejected by Admin',
                $booking,
                $activityLog->new_values ?? [],
                ['rejection_reason' => 'Admin rejected the reschedule request']
            );

            // Log admin action
            \App\Models\ActivityLog::logAdmin(
                'Admin rejected reschedule request for booking ' . $booking->reference,
                \App\Models\ActivityLog::ACTION_ADMIN_BOOKING
            );

            return redirect()->route('admin.dashboard')
                ->with('success', "Reschedule request for booking {$booking->reference} has been rejected.");

        } catch (\Exception $e) {
            Log::error('Error rejecting reschedule request: ' . $e->getMessage());
            
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to reject reschedule request: ' . $e->getMessage()]);
            }
            
            return redirect()->back()->with('error', 'Failed to reject reschedule request: ' . $e->getMessage());
        }
    }
    
    /**
     * Get reschedule request data for modal display
     */
    public function getRescheduleRequestData($id)
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Access denied']);
        }

        try {
            // Find the activity log entry for the reschedule request
            $activityLog = \App\Models\ActivityLog::where('id', $id)
                ->where('description', 'LIKE', 'Reschedule Request Submitted:%')
                ->firstOrFail();

            // Get the associated booking
            $booking = \App\Models\Booking::findOrFail($activityLog->resource_id);
            
            // Parse the original and requested data from the activity log
            $originalData = $activityLog->old_values ?? [];
            $requestedData = $activityLog->new_values ?? [];
            
            // Normalize the data structure for consistent access
            // Current booking data (what's currently in the database)
            $currentData = [
                'date' => $booking->date,
                'time_slot' => $booking->time_slot,
                'duration' => $booking->duration
            ];
            
            // Requested changes (from the reschedule request)
            $normalizedRequestedData = [
                'date' => $requestedData['new_date'] ?? $requestedData['date'] ?? null,
                'time_slot' => $requestedData['new_time_slot'] ?? $requestedData['time_slot'] ?? null,
                'duration' => $requestedData['duration'] ?? $requestedData['new_duration'] ?? null
            ];
            
            // Check for conflicts with the requested time slot
            $conflicts = [];
            if ($normalizedRequestedData['date'] && $normalizedRequestedData['time_slot']) {
                $conflictingBookings = \App\Models\Booking::where('date', $normalizedRequestedData['date'])
                    ->where('time_slot', $normalizedRequestedData['time_slot'])
                    ->where('id', '!=', $booking->id)
                    ->where('status', '!=', 'cancelled')
                    ->get();
                
                if ($conflictingBookings->count() > 0) {
                    $conflicts = $conflictingBookings->toArray();
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'booking' => $booking,
                    'activityLog' => $activityLog,
                    'originalData' => $currentData,
                    'requestedData' => $normalizedRequestedData,
                    'conflicts' => $conflicts
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading reschedule request data: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error loading reschedule request: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Get booking data for modal display
     */
    public function getBookingData($id)
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Access denied']);
        }

        try {
            $booking = \App\Models\Booking::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $booking
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading booking data: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error loading booking: ' . $e->getMessage()]);
        }
    }
}
