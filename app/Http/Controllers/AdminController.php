<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Services\GoogleCalendarService;
use App\Models\User;
use App\Models\Booking;
use App\Models\InstrumentRental;
use App\Models\ActivityLog;
use App\Models\RescheduleRequest;
use App\Models\CarouselItem;

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
        
        // Determine walk-in flag column for bookings
        $walkInColumn = Schema::hasColumn('bookings', 'is_walk_in_booking')
            ? 'is_walk_in_booking'
            : (Schema::hasColumn('bookings', 'is_admin_walkin') ? 'is_admin_walkin' : null);
        
        // Calculate revenue using down payments collected for rehearsal studio bookings (exclude walk-ins)
        $downpaymentMap = [1 => 100, 2 => 100, 3 => 150, 4 => 200, 5 => 250, 6 => 300, 7 => 350, 8 => 400];
        $confirmedPaidStudioBookings = Booking::where('status', 'confirmed')
            ->when($walkInColumn, function ($q) use ($walkInColumn) {
                $q->where($walkInColumn, false);
            })
            ->whereIn('service_type', ['studio_rental', 'solo_rehearsal'])
            ->whereNotNull('reference_code')
            ->get(['duration']);
        $totalBookingRevenue = $confirmedPaidStudioBookings->sum(function ($b) use ($downpaymentMap) {
            return $downpaymentMap[$b->duration] ?? 0;
        }) ?? 0;
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

            // Confirmed bookings excluding walk-ins for this month
            $monthlyConfirmedNonWalkInCount = Booking::where('status', 'confirmed')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->when($walkInColumn, function ($q) use ($walkInColumn) {
                    $q->where($walkInColumn, false);
                })
                ->count();

            $monthlyBookingRevenue = (function () use ($date, $walkInColumn, $downpaymentMap) {
                $monthlyStudioBookings = Booking::where('status', 'confirmed')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->when($walkInColumn, function ($q) use ($walkInColumn) {
                        $q->where($walkInColumn, false);
                    })
                    ->whereIn('service_type', ['studio_rental', 'solo_rehearsal'])
                    ->whereNotNull('reference_code')
                    ->get(['duration']);
                return $monthlyStudioBookings->sum(function ($b) use ($downpaymentMap) {
                    return $downpaymentMap[$b->duration] ?? 0;
                });
            })();

            // Instrument rentals revenue (confirmed or active) for this month
            $monthlyRentalsCount = InstrumentRental::whereIn('status', ['confirmed', 'active'])
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();

            $monthlyRentalsRevenue = InstrumentRental::whereIn('status', ['confirmed', 'active'])
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('total_amount');

            // Costs remain a simple estimate
            $monthlyData[$date->format('M')] = [
                'cost' => ($monthlyConfirmedNonWalkInCount + $monthlyRentalsCount) * 500,
                'revenue' => ($monthlyBookingRevenue + $monthlyRentalsRevenue)
            ];
        }
        
        // Analytics data for the dashboard
        $totalRevenue = $estimatedRevenue;
        $thisMonthRevenue = $monthlyData[now()->format('M')]['revenue'] ?? 0;
        $lastMonthRevenue = $monthlyData[now()->subMonth()->format('M')]['revenue'] ?? 0;
        $confirmedNonWalkInCount = Booking::where('status', 'confirmed')
            ->when($walkInColumn, function ($q) use ($walkInColumn) {
                $q->where($walkInColumn, false);
            })
            ->whereIn('service_type', ['studio_rental', 'solo_rehearsal'])
            ->whereNotNull('reference_code')
            ->count();
        $averageBookingValue = $confirmedNonWalkInCount > 0 ? round($totalBookingRevenue / $confirmedNonWalkInCount, 2) : 0;
        
        // Monthly data for charts
        $months = array_keys($monthlyData);
        $salesData = array_values(array_column($monthlyData, 'revenue'));
        $bookingCounts = [];
        
        // Get booking counts for each month (confirmed bookings only; include instrument rentals)
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyConfirmedNonWalkIn = Booking::where('status', 'confirmed')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->when($walkInColumn, function ($q) use ($walkInColumn) {
                    $q->where($walkInColumn, false);
                })
                ->count();
            $monthlyRentalsCount = InstrumentRental::whereIn('status', ['confirmed', 'active'])
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            $bookingCounts[] = $monthlyConfirmedNonWalkIn + $monthlyRentalsCount;
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
                // For other services, include confirmed and walk-in bookings
                $usersPerService[$label] = DB::table('bookings')
                    ->join('users', 'bookings.user_id', '=', 'users.id')
                    ->where('bookings.service_type', $key)
                    ->where(function ($q) use ($walkInColumn) {
                        $q->where('bookings.status', 'confirmed');
                        if ($walkInColumn) {
                            $q->orWhere('bookings.' . $walkInColumn, true);
                        }
                    })
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
                // For other services, include confirmed and walk-in bookings
                $servicesDistribution[$data['label']] = Booking::where('service_type', $key)
                    ->where(function ($q) use ($walkInColumn) {
                        $q->where('status', 'confirmed');
                        if ($walkInColumn) {
                            $q->orWhere($walkInColumn, true);
                        }
                    })
                    ->count();
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

        // Get date range from request or default to current month
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        // Validate dates
        try {
            $startDate = \Carbon\Carbon::parse($startDate)->startOfDay();
            $endDate = \Carbon\Carbon::parse($endDate)->endOfDay();
        } catch (\Exception $e) {
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
        }

        // Check for walk-in booking column
        $walkInColumn = Schema::hasColumn('bookings', 'is_walk_in_booking')
            ? 'is_walk_in_booking'
            : (Schema::hasColumn('bookings', 'is_admin_walkin') ? 'is_admin_walkin' : null);

        // Get bookings within date range (excluding walk-in bookings from analytics)
        $confirmedBookings = Booking::with('user')
            ->where('status', 'confirmed')
            ->whereBetween('date', [$startDate, $endDate])
            ->when($walkInColumn, function ($query) use ($walkInColumn) {
                $query->where($walkInColumn, false);
            })
            ->latest()
            ->get();
            
        $pendingBookings = Booking::with('user')
            ->where('status', 'pending')
            ->whereBetween('date', [$startDate, $endDate])
            ->when($walkInColumn, function ($query) use ($walkInColumn) {
                $query->where($walkInColumn, false);
            })
            ->latest()
            ->get();
            
        $cancelledBookings = Booking::with('user')
            ->where('status', 'cancelled')
            ->whereBetween('date', [$startDate, $endDate])
            ->when($walkInColumn, function ($query) use ($walkInColumn) {
                $query->where($walkInColumn, false);
            })
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

        // Get detailed service analytics using the improved Booking model method with date filtering
        $serviceAnalytics = Booking::getServiceTypeAnalytics($startDate, $endDate);
        
        // Service categorization with accurate counts - only existing service types
        $confirmedAndActiveRentals = InstrumentRental::whereIn('status', ['confirmed', 'active'])->count();
        $serviceCategories = [
            'Band Rehearsal' => $serviceAnalytics['studio_rental']['confirmed'] ?? 0,
            'Solo Rehearsal' => $serviceAnalytics['solo_rehearsal']['confirmed'] ?? 0,
            'Instrument Rentals' => $confirmedAndActiveRentals,
            'Pending Services' => $pendingBookings->count() + $pendingRentals->count()
        ];

        // Accurate revenue calculation using the improved methods - only existing service types
        $rentalsRevenue = InstrumentRental::whereIn('status', ['confirmed', 'active'])->sum('total_amount') ?? 0;
        $revenueByService = [
            'Band Rehearsal' => $serviceAnalytics['studio_rental']['revenue'] ?? 0,
            'Solo Rehearsal' => $serviceAnalytics['solo_rehearsal']['revenue'] ?? 0,
            'Instrument Rentals' => $rentalsRevenue,
            'Pending Services' => 0
        ];
        
        $totalRevenue = array_sum($revenueByService);

        // Top customers with accurate revenue calculation
        $topCustomers = DB::table('users')
            ->leftJoin('bookings', function($join) use ($walkInColumn) {
                $join->on('users.id', '=', 'bookings.user_id')
                     ->where('bookings.status', 'confirmed');
                if ($walkInColumn) {
                    $join->where('bookings.' . $walkInColumn, false);
                }
            })
            ->leftJoin('instrument_rentals', function($join) {
                $join->on('users.id', '=', 'instrument_rentals.user_id')
                     ->whereIn('instrument_rentals.status', ['confirmed', 'active']);
            })
            ->select('users.id', 'users.name', 'users.email',
                DB::raw('COUNT(DISTINCT bookings.id) as booking_count'),
                DB::raw('COUNT(DISTINCT instrument_rentals.id) as rental_count'),
                DB::raw('COALESCE(SUM(DISTINCT bookings.total_amount), 0) as booking_revenue'),
                DB::raw('COALESCE(SUM(DISTINCT instrument_rentals.total_amount), 0) as rental_revenue'))
            ->groupBy('users.id', 'users.name', 'users.email')
            ->havingRaw('(booking_count > 0 OR rental_count > 0)')
            ->get()
            ->map(function($customer) {
                // Calculate accurate total spent for each customer
                $bookingRevenue = 0;
                
                // Get accurate booking revenue for this customer (excluding walk-in bookings)
                $userBookings = Booking::where('user_id', $customer->id)
                    ->where('status', 'confirmed')
                    ->when($walkInColumn, function ($query) use ($walkInColumn) {
                        $query->where($walkInColumn, false);
                    })
                    ->get(['service_type', 'duration', 'total_amount']);
                
                foreach($userBookings as $booking) {
                    if ($booking->total_amount && $booking->total_amount > 0) {
                        $bookingRevenue += $booking->total_amount;
                    } else {
                        // Calculate based on service type and duration
                        if (in_array($booking->service_type, ['studio_rental', 'solo_rehearsal'])) {
                            $studioRates = [1 => 100, 2 => 200, 3 => 300, 4 => 400, 5 => 500, 6 => 600, 7 => 700, 8 => 800];
                            $bookingRevenue += $studioRates[$booking->duration] ?? 0;
                        } elseif ($booking->service_type === 'music_lesson') {
                            $bookingRevenue += 500; // Default lesson rate
                        }
                    }
                }
                
                $customer->total_spent = $bookingRevenue + $customer->rental_revenue;
                return $customer;
            })
            ->sortByDesc('total_spent')
            ->take(10)
            ->values();

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
            'pendingRentals',
            'startDate',
            'endDate',
            'serviceAnalytics'
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
        $serviceType = $request->get('service_type', 'all');

        // Date range filters (mirroring instrument bookings)
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Default: hide past bookings, show today and future
        if (empty($dateFrom) && empty($dateTo)) {
            $dateFrom = now()->toDateString();
        }

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

        // Apply service type filter
        if ($serviceType !== 'all') {
            $query->where('service_type', $serviceType);
        }

        // Apply date range filters
        if (!empty($dateFrom)) {
            $query->whereDate('date', '>=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $query->whereDate('date', '<=', $dateTo);
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
            'serviceType',
            'dateFrom',
            'dateTo'
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
     * Walk-In Booking: show create form
     */
    public function walkInCreate(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        // Use service types but exclude instrument rentals for walk-in page
        $serviceTypes = Booking::getServiceTypes();
        unset($serviceTypes['instrument_rental']);
        $walkInColumn = \Schema::hasColumn('bookings', 'is_walk_in_booking')
            ? 'is_walk_in_booking'
            : (\Schema::hasColumn('bookings', 'is_admin_walkin') ? 'is_admin_walkin' : null);

        // Manual booking history filters (for recent walk-ins list)
        $historyFilters = [
            'from_date' => $request->query('from_date'),
            'to_date' => $request->query('to_date'),
            // Use a distinct name to avoid clashing with the create form POST field
            'service_type' => $request->query('filter_service_type', 'all'),
        ];

        // Build recent walk-ins query and apply filters without changing the list structure
        if ($walkInColumn) {
            $query = Booking::where($walkInColumn, true);

            // Service type filter: studio_rental (Band), solo_rehearsal (Solo), music_lesson
            if ($historyFilters['service_type'] !== 'all') {
                $query->where('service_type', $historyFilters['service_type']);
            }

            // Date range filters
            if (!empty($historyFilters['from_date'])) {
                $query->whereDate('date', '>=', $historyFilters['from_date']);
            }
            if (!empty($historyFilters['to_date'])) {
                $query->whereDate('date', '<=', $historyFilters['to_date']);
            }

            $recentWalkIns = $query
                ->orderBy('date', 'desc')
                ->orderBy('time_slot', 'desc')
                ->limit(10)
                ->get();
        } else {
            $recentWalkIns = collect();
        }

        ActivityLog::logAdmin('Admin opened Walk-In Booking page', ActivityLog::ACTION_ADMIN_ACCESS);

        return view('admin.walk-in-create', compact('user', 'serviceTypes', 'recentWalkIns', 'historyFilters'));
    }

    /**
     * Walk-In Booking: store booking
     */
    public function walkInStore(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        $request->validate([
            // Accept band_rehearsal alias and normalize to studio_rental
            'service_type' => 'required|in:studio_rental,band_rehearsal,solo_rehearsal,music_lesson',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|string',
            'duration' => 'required|integer|min:1|max:8',
            'band_name' => 'nullable|string|max:255',
            'lesson_type' => 'required_if:service_type,music_lesson|in:Voice Lesson,Drum Lesson,Guitar Lesson,Ukulele Lesson,Bass Guitar Lesson,Keyboard Lesson'
        ]);

        try {
            $duration = (int) $request->duration;
            $start = Carbon::createFromFormat('h:i A', $request->start_time, config('app.timezone', 'Asia/Manila'));
            $end = $start->copy()->addHours($duration);
            $timeSlot = $start->format('h:i A') . ' - ' . $end->format('h:i A');

            // Enforce closing time: end must not exceed 8:00 PM
            $closingTime = Carbon::createFromTime(20, 0, 0, config('app.timezone', 'Asia/Manila'));
            if ($end->gt($closingTime)) {
                return back()->withInput()->with('error', 'Selected time exceeds studio closing time (8:00 PM).');
            }

            // Check studio unavailability due to instrument rentals (drums/full package)
            $drumOrFullPackageRentals = InstrumentRental::whereIn('status', ['pending', 'confirmed'])
                ->where(function($query) {
                    $query->where('instrument_type', 'drums')
                          ->orWhere('instrument_type', 'Full Package');
                })
                ->where(function($query) use ($request) {
                    $query->where('rental_start_date', '<=', $request->date)
                          ->where('rental_end_date', '>=', $request->date);
                })
                ->exists();

            if ($drumOrFullPackageRentals) {
                return back()->withInput()->with('error', 'Studio is unavailable on this date due to a Full Package or drum rental.');
            }

            // Check for overlapping bookings (pending or confirmed)
            $existingBookings = Booking::where('date', $request->date)
                ->whereIn('status', ['pending', 'confirmed'])
                ->get();

            foreach ($existingBookings as $existingBooking) {
                $existingStartStr = trim(explode('-', $existingBooking->time_slot)[0]);
                $existingStart = Carbon::createFromFormat('h:i A', $existingStartStr, config('app.timezone', 'Asia/Manila'));
                $existingEnd = $existingStart->copy()->addHours($existingBooking->duration);

                if (($start < $existingEnd && $end > $existingStart) || ($existingStart < $end && $existingEnd > $start)) {
                    return back()->withInput()->with('error', 'Selected time overlaps with an existing booking.');
                }
            }

            // No attachments are processed for admin walk-in to avoid conflicts

            // Normalize alias for pricing and storage
            $serviceType = $request->service_type === 'band_rehearsal' ? 'studio_rental' : $request->service_type;

            // Pricing: apply band/solo matrix; no pricing for music lessons
            if (in_array($serviceType, ['studio_rental', 'solo_rehearsal'])) {
                if ($serviceType === 'studio_rental') {
                    // Band rehearsal: ₱230 (1hr), then ₱200/hr for 2hrs+
                    $hourlyRate = $duration === 1 ? 230.0 : 200.0;
                    $totalAmount = $duration === 1 ? 230.0 : 200.0 * $duration;
                } else { // solo_rehearsal
                    // Solo rehearsal: ₱200 (1hr), then ₱180/hr for 2hrs+
                    $hourlyRate = $duration === 1 ? 200.0 : 180.0;
                    $totalAmount = $duration === 1 ? 200.0 : 180.0 * $duration;
                }
            } else {
                // Music lesson: pricing not required here
                $hourlyRate = 0.0;
                $totalAmount = null;
            }

            $walkInColumn = \Schema::hasColumn('bookings', 'is_walk_in_booking')
                ? 'is_walk_in_booking'
                : (\Schema::hasColumn('bookings', 'is_admin_walkin') ? 'is_admin_walkin' : null);

            $bookingData = [
                'user_id' => $user->id,
                'date' => $request->date,
                'time_slot' => $timeSlot,
                'duration' => $duration,
                'price' => $hourlyRate,
                'total_amount' => $totalAmount,
                'status' => 'confirmed',
                'created_by_admin_id' => $user->id,
                'service_type' => $serviceType,
                'band_name' => $request->band_name,
            ];
            if ($walkInColumn) {
                $bookingData[$walkInColumn] = true;
            } else if (\Schema::hasColumn('bookings', 'is_walk_in_booking')) {
                $bookingData['is_walk_in_booking'] = true;
            }
            if (Schema::hasColumn('bookings', 'lesson_type')) {
                $bookingData['lesson_type'] = $request->lesson_type;
            }
            $booking = Booking::create($bookingData);

            ActivityLog::logBooking(ActivityLog::ACTION_BOOKING_CREATED, $booking, null, $booking->toArray());

            // Create Google Calendar event if available
            $calendarMessage = '';
            if ($this->calendarService) {
                try {
                    $result = $this->calendarService->createBookingEvent($booking);
                    if ($result) {
                        $calendarMessage = ' Google Calendar event created.';
                        ActivityLog::logAdmin('Created calendar event for walk-in booking ' . $booking->reference, ActivityLog::ACTION_CALENDAR_SYNC);
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to create calendar event for walk-in: ' . $e->getMessage());
                }
            }

            return redirect()->route('admin.walk-in.create')
                ->with('success', 'Walk-in booking created successfully.' . $calendarMessage);
        } catch (\Exception $e) {
            Log::error('Error creating walk-in booking: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create walk-in booking: ' . $e->getMessage());
        }
    }

    /**
     * Walk-In Booking: availability for time slots
     */
    public function walkInAvailability(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        $request->validate([
            // Accept band_rehearsal alias for availability check
            'service_type' => 'required|in:studio_rental,band_rehearsal,solo_rehearsal,music_lesson',
            'date' => 'required|date',
            'duration' => 'required|integer|min:1|max:8',
        ]);

        $tz = config('app.timezone', 'Asia/Manila');
        $cursor = Carbon::createFromTime(8, 0, 0, $tz);
        $endOfDay = Carbon::createFromTime(20, 0, 0, $tz);
        $times = [];
        while ($cursor <= $endOfDay) {
            $times[] = $cursor->copy()->format('h:i A');
            $cursor->addHour();
        }

        // Block studio if drum/full package rentals cover the date
        $drumOrFullPackageRentals = InstrumentRental::whereIn('status', ['pending', 'confirmed'])
            ->where(function ($query) {
                $query->where('instrument_type', 'drums')
                    ->orWhere('instrument_type', 'Full Package');
            })
            ->where(function ($query) use ($request) {
                $query->where('rental_start_date', '<=', $request->date)
                    ->where('rental_end_date', '>=', $request->date);
            })
            ->exists();

        $disabled = [];
        $message = '';

        if ($drumOrFullPackageRentals) {
            $disabled = $times; // All times disabled
            $message = 'Studio is unavailable on this date due to a Full Package or drum rental.';
        } else {
            $existingBookings = Booking::where('date', $request->date)
                ->whereIn('status', ['pending', 'confirmed'])
                ->get();

            $now = Carbon::now($tz);
            $selectedDate = Carbon::parse($request->date, $tz)->startOfDay();
            $today = $now->copy()->startOfDay();
            $closingTime = Carbon::createFromTime(20, 0, 0, $tz);

            foreach ($times as $t) {
                $startTime = Carbon::createFromFormat('h:i A', $t, $tz);
                $endTime = $startTime->copy()->addHours((int) $request->duration);

                $disable = false;

                // Disable past times if selected date is today
                if ($selectedDate->equalTo($today) && $startTime < $now) {
                    $disable = true;
                }

                // Enforce closing time: start + duration must not exceed 8:00 PM
                if (!$disable && $endTime->gt($closingTime)) {
                    $disable = true;
                }

                // Disable overlaps with existing bookings (pending/confirmed)
                if (!$disable) {
                    foreach ($existingBookings as $existingBooking) {
                        $existingStartStr = trim(explode('-', $existingBooking->time_slot)[0]);
                        $existingStart = Carbon::createFromFormat('h:i A', $existingStartStr, $tz);
                        $existingEnd = $existingStart->copy()->addHours($existingBooking->duration);
                        if (($startTime < $existingEnd && $endTime > $existingStart) || ($existingStart < $endTime && $existingEnd > $startTime)) {
                            $disable = true;
                            break;
                        }
                    }
                }

                if ($disable) {
                    $disabled[] = $t;
                }
            }
        }

        return response()->json([
            'disabled_times' => $disabled,
            'all_times' => $times,
            'message' => $message,
        ]);
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
     * Manual Booking History (walk-in bookings)
     */
    public function manualBookingHistory(Request $request)
    {
        // Ensure admin access
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        $status = $request->query('status', 'all');
        $dateFilter = $request->query('date', 'all'); // today|week|month|all
        $serviceType = $request->query('service_type', 'all');
        $search = trim($request->query('search', ''));

        $walkInColumnHistory = \Schema::hasColumn('bookings', 'is_walk_in_booking')
            ? 'is_walk_in_booking'
            : (\Schema::hasColumn('bookings', 'is_admin_walkin') ? 'is_admin_walkin' : null);

        $query = Booking::with('user');
        if ($walkInColumnHistory) {
            $query->where($walkInColumnHistory, true);
        }
        $query->orderBy('date', 'desc')
              ->orderBy('time_slot', 'asc');

        // Apply status filter
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Apply service type filter
        if ($serviceType !== 'all') {
            $query->where('service_type', $serviceType);
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

        // Apply search on reference or band name/customer name
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%$search%")
                  ->orWhere('reference_code', 'like', "%$search%")
                  ->orWhere('band_name', 'like', "%$search%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%$search%");
                  });
            });
        }

        $bookings = $query->paginate(15);

        // Quick counts for badges
        if ($walkInColumnHistory) {
            $statusCounts = [
                'all' => Booking::where($walkInColumnHistory, true)->count(),
                'pending' => Booking::where($walkInColumnHistory, true)->where('status', 'pending')->count(),
                'confirmed' => Booking::where($walkInColumnHistory, true)->where('status', 'confirmed')->count(),
                'rejected' => Booking::where($walkInColumnHistory, true)->where('status', 'rejected')->count(),
            ];
        } else {
            $statusCounts = [
                'all' => Booking::count(),
                'pending' => Booking::where('status', 'pending')->count(),
                'confirmed' => Booking::where('status', 'confirmed')->count(),
                'rejected' => Booking::where('status', 'rejected')->count(),
            ];
        }

        ActivityLog::logAdmin('Admin viewed manual booking history', ActivityLog::ACTION_ADMIN_ACCESS);

        return view('admin.manual-booking-history', compact(
            'user',
            'bookings',
            'statusCounts',
            'status',
            'search',
            'dateFilter',
            'serviceType'
        ));
    }



    /**
     * Create full system backup
     */
    public function createBackup(Request $request)
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        try {
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $backupName = 'backup_' . $timestamp;
            
            // Create backup directory
            $backupPath = storage_path('app\\backups\\' . $backupName);
            if (!File::exists($backupPath)) {
                File::makeDirectory($backupPath, 0755, true);
            }

            // 1. Database backup
            $this->createDatabaseBackup($backupPath, $backupName);

            // 2. Files backup
            $this->createFilesBackup($backupPath);

            // Calculate backup size
            $backupSize = $this->getDirectorySize($backupPath);
            $formattedSize = $this->formatBytes($backupSize);

            return response()->json([
                'success' => true,
                'message' => 'Backup created successfully',
                'backup_name' => $backupName,
                'backup_size' => $formattedSize
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Backup failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download a backup file
     */
    public function downloadBackup($filename)
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        $backupPath = storage_path('app\\backups\\' . $filename);
        
        if (!File::exists($backupPath)) {
            abort(404, 'Backup not found');
        }

        try {
            // Create temporary TAR file for download since ZIP extension is not available
            $tempTarPath = storage_path('app\\temp\\' . $filename . '.tar');
            
            // Ensure temp directory exists
            $tempDir = dirname($tempTarPath);
            if (!File::exists($tempDir)) {
                File::makeDirectory($tempDir, 0755, true);
            }

            // Create TAR archive using PHP's PharData
            $tar = new \PharData($tempTarPath);
            $tar->buildFromDirectory($backupPath);

            // Download the TAR file
            return response()->download($tempTarPath, $filename . '.tar')->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create download: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a backup file
     */
    public function deleteBackup($filename)
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        try {
            $backupPath = storage_path('app\\backups\\' . $filename);
            
            if (File::exists($backupPath)) {
                if (File::isDirectory($backupPath)) {
                    File::deleteDirectory($backupPath);
                } else {
                    File::delete($backupPath);
                }
                return response()->json([
                    'success' => true,
                    'message' => 'Backup deleted successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Backup not found'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete backup: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get list of available backups
     */
    public function getBackups()
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        $backups = $this->getAvailableBackups();
        return response()->json($backups);
    }

    /**
     * Restore database from uploaded backup file
     */
    public function restoreDatabase(Request $request)
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        $request->validate([
            'backup_file' => 'required|file|mimes:sql,zip,tar,gz|max:102400', // 100MB max
            'confirm_overwrite' => 'required|accepted'
        ]);

        try {
            $file = $request->file('backup_file');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            
            // Store the uploaded file temporarily
            $tempPath = $file->store('temp/restore', 'local');
            $fullTempPath = storage_path('app/' . $tempPath);
            
            // Process based on file type
            if ($extension === 'sql') {
                $this->restoreFromSqlFile($fullTempPath);
            } elseif (in_array($extension, ['zip', 'tar', 'gz'])) {
                $this->restoreFromArchive($fullTempPath, $extension);
            } else {
                throw new \Exception('Unsupported file format');
            }
            
            // Clean up temporary file
            File::delete($fullTempPath);
            
            return response()->json([
                'success' => true,
                'message' => 'Database restored successfully from ' . $originalName
            ]);
            
        } catch (\Exception $e) {
            // Clean up temporary file if it exists
            if (isset($fullTempPath) && File::exists($fullTempPath)) {
                File::delete($fullTempPath);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Database restore failed: ' . $e->getMessage()
            ], 500);
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
            'status' => 'required|in:pending,approved,confirmed,active,returned,cancelled,rejected'
        ]);

        try {
            $rental = InstrumentRental::findOrFail($id);
            $oldStatus = $rental->status;
            $rental->update(['status' => $request->status]);

            // Send confirmation email when moving from pending to approved/confirmed
            if (in_array(strtolower($request->status), ['approved', 'confirmed']) && strtolower((string)$oldStatus) === 'pending') {
                try {
                    $recipient = optional($rental->user)->email ?? ($rental->email ?? null);
                    if ($recipient) {
                        \Illuminate\Support\Facades\Mail::to($recipient)->send(new \App\Mail\UserInstrumentRentalConfirmed($rental));
                    }
                } catch (\Exception $e) {
                    \Log::error('Failed to send instrument rental confirmation email', [
                        'rental_id' => $rental->id,
                        'reference' => $rental->reference,
                        'error' => $e->getMessage()
                    ]);
                }
            }

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
            
            // Send approval email to user
            try {
                $customer = $booking->user;
                if ($customer && $customer->email) {
                    \Illuminate\Support\Facades\Mail::to($customer->email)->send(
                        new \App\Mail\BookingNotification($booking, $customer)
                    );
                    Log::info('Booking approval email sent', [
                        'booking_id' => $booking->id,
                        'user_email' => $customer->email,
                        'reference' => $booking->reference
                    ]);
                } else {
                    Log::warning('No user email found for approved booking', [
                        'booking_id' => $booking->id
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to send booking approval email', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage()
                ]);
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
            
            // Send rejection email to user
            try {
                \Illuminate\Support\Facades\Mail::to($booking->user->email)->send(
                    new \App\Mail\BookingRejectionNotification($booking)
                );
                Log::info('Booking rejection email sent', [
                    'booking_id' => $booking->id,
                    'user_email' => $booking->user->email,
                    'reference' => $booking->reference
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send booking rejection email', [
                    'booking_id' => $booking->id,
                    'user_email' => $booking->user->email,
                    'error' => $e->getMessage()
                ]);
            }
            
            // Update booking status to rejected (keep record)
            $booking->status = 'rejected';
            $booking->save();
            
            // Log booking rejection with updated values
            ActivityLog::logBooking(
                ActivityLog::ACTION_BOOKING_REJECTED,
                $booking,
                $oldValues,
                $booking->fresh()->toArray()
            );
            
            Log::info('Booking rejected by admin', [
                'booking_id' => $booking->id,
                'reference' => $bookingReference,
                'admin_id' => $user->id
            ]);
            
            return redirect()->back()->with('success', "Booking {$bookingReference} for {$customerName} has been rejected and remains listed for records and reporting.");
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
            
            // Use original booking duration instead of request duration
            $originalDuration = $booking->duration;
            
            // Enforce studio closing time: end must be <= 8:00 PM
            $startPart = trim(explode('-', $request->time_slot)[0]);
            try {
                $newStartTime = \Carbon\Carbon::createFromFormat('h:i A', $startPart, config('app.timezone', 'Asia/Manila'));
            } catch (\Exception $e) {
                // Admin form uses 24h format like "19:00"
                $newStartTime = \Carbon\Carbon::createFromFormat('H:i', $startPart, config('app.timezone', 'Asia/Manila'));
            }
            $newEndTime = $newStartTime->copy()->addHours($originalDuration);
            if ($newEndTime->hour > 20 || ($newEndTime->hour === 20 && $newEndTime->minute > 0)) {
                return redirect()->back()->with('error', 'Selected time exceeds studio closing time (8:00 PM)');
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
            
            // Update booking details (keep original duration)
            $booking->update([
                'date' => $request->date,
                'time_slot' => $request->time_slot,
                'duration' => $originalDuration, // Use original duration
                'reschedule_source' => 'system'
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

            // Email the user about confirmation
            try {
                $recipient = optional($rental->user)->email ?? ($rental->email ?? null);
                if ($recipient) {
                    \Illuminate\Support\Facades\Mail::to($recipient)->send(new \App\Mail\UserInstrumentRentalConfirmed($rental));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send instrument rental confirmation email (approveRental)', [
                    'rental_id' => $rental->id,
                    'reference' => $rental->reference,
                    'error' => $e->getMessage()
                ]);
            }
            
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
            
            // Update rental status to rejected (do not delete the record)
            $rental->update(['status' => 'rejected']);
            
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
     * Delete a user account from the system
     */
    public function deleteUser($id)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        if (!Auth::check() || !$currentUser->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        try {
            $user = User::findOrFail($id);

            // Prevent deleting self
            if ($user->id === $currentUser->id) {
                return redirect()->back()->with('error', 'You cannot delete your own account.');
            }

            // Check if the target is an administrator
            $adminRecord = DB::table('admin_users')->where('email', $user->email)->first();

            if ($user->is_admin || $adminRecord) {
                // Only super admins can delete administrator accounts
                $currentAdminRecord = DB::table('admin_users')->where('email', $currentUser->email)->first();
                if (!$currentAdminRecord || $currentAdminRecord->role !== 'super_admin') {
                    return redirect()->back()->with('error', 'Only super admins can delete administrator accounts.');
                }

                // Protect super admin accounts from deletion
                if ($adminRecord && $adminRecord->role === 'super_admin') {
                    return redirect()->back()->with('error', 'Super admin accounts cannot be deleted.');
                }
            }

            $oldValues = $user->toArray();

            // Clean up admin_users table entry if exists
            DB::table('admin_users')->where('email', $user->email)->delete();

            // Delete user
            $user->delete();

            // Log deletion
            ActivityLog::logActivity(
                "Admin deleted user {$user->name} ({$user->email})",
                ActivityLog::ACTION_USER_DELETED,
                $currentUser->id,
                'User',
                $user->id,
                $oldValues,
                null,
                ActivityLog::SEVERITY_HIGH
            );

            return redirect()->back()->with('success', "User {$user->name} has been deleted.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
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
        $activityLogs = $query->orderBy('created_at', 'desc')->paginate(6);
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
            $rescheduleRequests = RescheduleRequest::where('created_at', '>=', now()->subDay())
                ->where('status', RescheduleRequest::STATUS_PENDING)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($request) {
                    // Handle both studio bookings and instrument rentals
                    if ($request->resource_type === RescheduleRequest::RESOURCE_BOOKING) {
                        return [
                            'id' => $request->id,
                            'type' => 'reschedule',
                            'customer_name' => $request->customer_name,
                            'studio_name' => 'Studio Reschedule Request',
                            'date' => $request->requested_date,
                            'time_slot' => $request->requested_time_slot,
                            'original_date' => $request->original_date,
                            'original_time_slot' => $request->original_time_slot,
                            'booking_reference' => $request->booking ? $request->booking->reference : 'N/A',
                            'created_at' => $request->created_at->toISOString(),
                        ];
                    } else {
                        return [
                            'id' => $request->id,
                            'type' => 'reschedule',
                            'customer_name' => $request->customer_name,
                            'studio_name' => 'Instrument Reschedule Request',
                            'date' => $request->requested_start_date,
                            'time_slot' => 'Rental Period',
                            'original_date' => $request->original_start_date,
                            'original_time_slot' => 'to ' . $request->original_end_date,
                            'booking_reference' => $request->instrumentRental ? $request->instrumentRental->reference : 'N/A',
                            'created_at' => $request->created_at->toISOString(),
                        ];
                    }
                });

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
            // Find the reschedule request
            $rescheduleRequest = RescheduleRequest::findOrFail($id);
            
            // If the request is already approved, redirect with success message
            if ($rescheduleRequest->status === RescheduleRequest::STATUS_APPROVED) {
                return redirect()->route('admin.dashboard')
                    ->with('success', 'This reschedule request has already been approved and processed successfully.');
            }
            
            // If the request is rejected, redirect with info message
            if ($rescheduleRequest->status === RescheduleRequest::STATUS_REJECTED) {
                return redirect()->route('admin.dashboard')
                    ->with('info', 'This reschedule request has been rejected.');
            }

            // Get the associated booking or rental
            $booking = null;
            $rental = null;
            
            if ($rescheduleRequest->resource_type === RescheduleRequest::RESOURCE_BOOKING) {
                $booking = $rescheduleRequest->booking;
            } else {
                $rental = $rescheduleRequest->instrumentRental;
            }

            // Check for conflicts with the requested time slot
            $hasConflict = $rescheduleRequest->has_conflict;
            $conflictingBooking = null;
            
            if ($rescheduleRequest->resource_type === RescheduleRequest::RESOURCE_BOOKING && $rescheduleRequest->requested_date && $rescheduleRequest->requested_time_slot) {
                $conflictingBooking = \App\Models\Booking::where('date', $rescheduleRequest->requested_date)
                    ->where('time_slot', $rescheduleRequest->requested_time_slot)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->where('id', '!=', $rescheduleRequest->resource_id)
                    ->first();
                    
                $hasConflict = $conflictingBooking !== null;
            }

            return view('admin.reschedule-details', compact(
                'rescheduleRequest',
                'booking',
                'rental',
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
            // Find the reschedule request
            $rescheduleRequest = RescheduleRequest::findOrFail($id);
            
            // Check if already processed
            if ($rescheduleRequest->status !== RescheduleRequest::STATUS_PENDING) {
                return redirect()->back()->with('error', 'This reschedule request has already been processed.');
            }
            
            if ($rescheduleRequest->resource_type === RescheduleRequest::RESOURCE_BOOKING) {
                // Handle studio booking reschedule
                $originalBooking = $rescheduleRequest->booking;
                
                if (!$originalBooking) {
                    return redirect()->back()->with('error', 'Original booking not found.');
                }
                
                return $this->approveStudioReschedule($originalBooking, $rescheduleRequest, $user);
                
            } else {
                // Handle instrument rental reschedule
                $originalRental = $rescheduleRequest->instrumentRental;
                
                if (!$originalRental) {
                    return redirect()->back()->with('error', 'Original rental not found.');
                }
                
                return $this->approveInstrumentReschedule($originalRental, $rescheduleRequest, $user);
            }
            
        } catch (\Exception $e) {
            Log::error('Error approving reschedule request: ' . $e->getMessage());
            
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to approve reschedule request: ' . $e->getMessage()]);
            }
            
            return redirect()->back()->with('error', 'Failed to approve reschedule request: ' . $e->getMessage());
        }
    }
    
    private function approveStudioReschedule($originalBooking, $rescheduleRequest, $user)
    {
        try {

            // Check for conflicts again
            $conflictingBooking = \App\Models\Booking::where('date', $rescheduleRequest->requested_date)
                ->where('time_slot', $rescheduleRequest->requested_time_slot)
                ->whereIn('status', ['pending', 'confirmed'])
                ->where('id', '!=', $originalBooking->id)
                ->first();
                
            if ($conflictingBooking) {
                return redirect()->back()->with('error', 'Cannot approve: The requested time slot is now occupied by another booking.');
            }

            // Store old values for logging
            $oldValues = $originalBooking->toArray();

            // Update the original booking with the new details
            $originalBooking->update([
                'date' => $rescheduleRequest->requested_date,
                'time_slot' => $rescheduleRequest->requested_time_slot,
                'duration' => $rescheduleRequest->requested_duration,
                'status' => 'confirmed',
                'reschedule_source' => 'system',
                'updated_at' => now()
            ]);
            
            // Mark reschedule request as approved
            $rescheduleRequest->approve($user);

            // Log the reschedule approval
            \App\Models\ActivityLog::logBooking(
                'Reschedule Request Approved - Booking Updated',
                $originalBooking,
                $oldValues,
                $originalBooking->toArray()
            );

            // Log admin action
            \App\Models\ActivityLog::logAdmin(
                'Admin approved reschedule request - updated booking ' . $originalBooking->reference . ' with new schedule',
                \App\Models\ActivityLog::ACTION_ADMIN_ACCESS
            );

            // Handle Google Calendar sync
            $calendarSyncMessage = '';
            try {
                if ($user->google_calendar_id) {
                    $googleCalendarService = app(\App\Services\GoogleCalendarService::class);
                    
                    // Update the booking event in Google Calendar
                    $googleCalendarService->updateBookingEvent($originalBooking, $oldValues);
                    
                    $calendarSyncMessage = ' The booking has been updated in Google Calendar.';
                }
            } catch (\Exception $e) {
                Log::warning('Failed to sync Google Calendar events after reschedule approval', [
                    'booking_id' => $originalBooking->id,
                    'error' => $e->getMessage()
                ]);
                $calendarSyncMessage = ' Note: Google Calendar sync failed.';
            }

            // Notify user of approval via email
            try {
                $recipient = $originalBooking->email ?? ($originalBooking->user->email ?? null);
                if ($recipient) {
                    $previousData = [
                        'date' => \Carbon\Carbon::parse($rescheduleRequest->original_date ?? ($oldValues['date'] ?? $originalBooking->getOriginal('date')))->toDateString(),
                        'time_slot' => $rescheduleRequest->original_time_slot ?? ($oldValues['time_slot'] ?? $originalBooking->getOriginal('time_slot')),
                        'duration' => $rescheduleRequest->original_duration ?? ($oldValues['duration'] ?? $originalBooking->getOriginal('duration')),
                    ];
                    \Illuminate\Support\Facades\Mail::to($recipient)->send(
                        new \App\Mail\UserRescheduleApproved($originalBooking, $previousData)
                    );
                } else {
                    Log::warning('No recipient email for booking reschedule approval', [
                        'booking_id' => $originalBooking->id,
                        'reference' => $originalBooking->reference,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to send booking reschedule approval email', [
                    'booking_id' => $originalBooking->id,
                    'reference' => $originalBooking->reference,
                    'error' => $e->getMessage()
                ]);
            }

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => "Reschedule request approved successfully! Booking {$originalBooking->reference} has been updated with the new schedule.{$calendarSyncMessage}",
                    'calendar_refresh' => true,
                    'updated_booking' => [
                        'reference' => $originalBooking->reference,
                        'old_date' => $rescheduleRequest->original_date,
                        'new_date' => $rescheduleRequest->requested_date,
                        'old_time_slot' => $rescheduleRequest->original_time_slot,
                        'new_time_slot' => $rescheduleRequest->requested_time_slot,
                        'duration' => $rescheduleRequest->requested_duration
                    ]
                ]);
            }
            
            return redirect()->route('admin.dashboard')
                ->with('success', "Reschedule request approved successfully! Booking {$originalBooking->reference} has been updated with the new schedule.{$calendarSyncMessage}")
                ->with('calendar_refresh', true);

        } catch (\Exception $e) {
            Log::error('Error approving reschedule request: ' . $e->getMessage());
            
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to approve reschedule request: ' . $e->getMessage()]);
            }
            
            return redirect()->back()->with('error', 'Failed to approve reschedule request: ' . $e->getMessage());
        }
    }
    
    private function approveInstrumentReschedule($originalRental, $rescheduleRequest, $user)
    {
        try {
            // Store old values for logging
            $oldValues = $originalRental->toArray();

            // Calculate duration from start and end dates
            $startDate = \Carbon\Carbon::parse($rescheduleRequest->requested_start_date);
            $endDate = \Carbon\Carbon::parse($rescheduleRequest->requested_end_date);
            $durationDays = $startDate->diffInDays($endDate) + 1;

            // Update the original rental with the new details
            $originalRental->update([
                'rental_start_date' => $rescheduleRequest->requested_start_date,
                'rental_end_date' => $rescheduleRequest->requested_end_date,
                'rental_duration_days' => $durationDays,
                'status' => 'confirmed',
                'reschedule_source' => 'system',
                'updated_at' => now()
            ]);
            
            // Mark reschedule request as approved
            $rescheduleRequest->approve($user);

            // Delete the approved reschedule request to prevent looping
            $rescheduleRequest->delete();

            // Log the reschedule approval using proper ActivityLog method
            \App\Models\ActivityLog::logActivity(
                'Reschedule Request Confirmed - Instrument Rental Updated: ' . $originalRental->reference,
                \App\Models\ActivityLog::ACTION_RENTAL_UPDATED,
                $user->id,
                'App\\Models\\InstrumentRental',
                $originalRental->id,
                $oldValues,
                $originalRental->toArray(),
                \App\Models\ActivityLog::SEVERITY_MEDIUM
            );

            // Log admin action
            \App\Models\ActivityLog::logAdmin(
                'Admin approved instrument rental reschedule request - updated rental ' . $originalRental->reference . ' with new schedule',
                \App\Models\ActivityLog::ACTION_ADMIN_ACCESS
            );

            // Notify user of instrument reschedule approval via email
            try {
                $recipient = $originalRental->user->email ?? null;
                if ($recipient) {
                    $previousData = [
                        'rental_start_date' => \Carbon\Carbon::parse($oldValues['rental_start_date'] ?? $originalRental->getOriginal('rental_start_date'))->toDateString(),
                        'rental_end_date' => \Carbon\Carbon::parse($oldValues['rental_end_date'] ?? $originalRental->getOriginal('rental_end_date'))->toDateString(),
                        'rental_duration_days' => $oldValues['rental_duration_days'] ?? $originalRental->getOriginal('rental_duration_days'),
                    ];
                    \Illuminate\Support\Facades\Mail::to($recipient)->send(
                        new \App\Mail\UserInstrumentRescheduleApproved($originalRental, $previousData)
                    );
                } else {
                    Log::warning('No recipient email for instrument reschedule approval', [
                        'rental_id' => $originalRental->id,
                        'reference' => $originalRental->reference,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Failed to send instrument reschedule approval email', [
                    'rental_id' => $originalRental->id,
                    'reference' => $originalRental->reference,
                    'error' => $e->getMessage()
                ]);
            }

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => "Instrument rental reschedule request approved successfully! Rental {$originalRental->reference} has been updated with the new schedule.",
                    'calendar_refresh' => true,
                    'updated_rental' => [
                        'reference' => $originalRental->reference,
                        'old_start_date' => $rescheduleRequest->original_start_date,
                        'new_start_date' => $rescheduleRequest->requested_start_date,
                        'old_end_date' => $rescheduleRequest->original_end_date,
                        'new_end_date' => $rescheduleRequest->requested_end_date,
                        'duration_days' => $durationDays
                    ]
                ]);
            }
            
            return redirect()->route('admin.dashboard')
                ->with('success', "Instrument rental reschedule request approved successfully! Rental {$originalRental->reference} has been updated with the new schedule.")
                ->with('calendar_refresh', true);

        } catch (\Exception $e) {
            Log::error('Error approving instrument rental reschedule request: ' . $e->getMessage());
            
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to approve instrument rental reschedule request: ' . $e->getMessage()]);
            }
            
            return redirect()->back()->with('error', 'Failed to approve instrument rental reschedule request: ' . $e->getMessage());
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
            // Find the reschedule request
            $rescheduleRequest = RescheduleRequest::findOrFail($id);
            
            // Check if already processed
            if ($rescheduleRequest->status !== RescheduleRequest::STATUS_PENDING) {
                return redirect()->back()->with('error', 'This reschedule request has already been processed.');
            }
            
            if ($rescheduleRequest->resource_type === RescheduleRequest::RESOURCE_BOOKING) {
                // Handle studio booking rejection
                $booking = $rescheduleRequest->booking;
                
                if (!$booking) {
                    return redirect()->back()->with('error', 'Original booking not found.');
                }
                
                // Mark reschedule request as rejected
                $rescheduleRequest->reject($user, 'Admin rejected the reschedule request');
                
                // Delete the rejected reschedule request to keep system clean
                $rescheduleRequest->delete();
                
                // Log the rejection
                \App\Models\ActivityLog::logBooking(
                    'Reschedule Request Rejected by Admin',
                    $booking,
                    [],
                    ['rejection_reason' => 'Admin rejected the reschedule request']
                );

                // Log admin action
                \App\Models\ActivityLog::logAdmin(
                    'Admin rejected reschedule request for booking ' . $booking->reference,
                    \App\Models\ActivityLog::ACTION_ADMIN_BOOKING
                );

                $successMessage = "Reschedule request for booking {$booking->reference} has been rejected.";
                
            } else {
                // Handle instrument rental rejection
                $rental = $rescheduleRequest->instrumentRental;
                
                if (!$rental) {
                    return redirect()->back()->with('error', 'Original rental not found.');
                }
                
                // Mark reschedule request as rejected
                $rescheduleRequest->reject($user, 'Admin rejected the reschedule request');
                
                // Delete the rejected reschedule request to keep system clean
                $rescheduleRequest->delete();
                
                // Log the rejection using proper ActivityLog method
                \App\Models\ActivityLog::logActivity(
                    'Reschedule Request Rejected by Admin: ' . $rental->reference,
                    \App\Models\ActivityLog::ACTION_RENTAL_UPDATED,
                    $user->id,
                    'App\\Models\\InstrumentRental',
                    $rental->id,
                    [],
                    ['rejection_reason' => 'Admin rejected the reschedule request'],
                    \App\Models\ActivityLog::SEVERITY_MEDIUM
                );

                // Log admin action
                \App\Models\ActivityLog::logAdmin(
                    'Admin rejected reschedule request for instrument rental ' . $rental->reference,
                    \App\Models\ActivityLog::ACTION_ADMIN_ACCESS
                );

                $successMessage = "Reschedule request for instrument rental {$rental->reference} has been rejected.";
            }

            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => $successMessage]);
            }
            
            return redirect()->route('admin.dashboard')
                ->with('success', $successMessage);

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

    /**
     * Show all reschedule requests
     */
    public function rescheduleRequests()
    {
        // Check if user is admin
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        try {
            // Get only pending reschedule requests from the reschedule_requests table
            $rescheduleRequests = RescheduleRequest::with(['user', 'booking', 'instrumentRental', 'handledBy'])
                ->where('status', RescheduleRequest::STATUS_PENDING)
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            // Process each request to add additional data
            $rescheduleRequests->getCollection()->transform(function ($request) {
                if ($request->resource_type === RescheduleRequest::RESOURCE_BOOKING) {
                    // Handle studio booking reschedule requests
                    $booking = $request->booking;
                    if ($booking) {
                        $request->booking_data = $booking;
                        
                        // Check for conflicts
                        $hasConflict = false;
                        if ($request->requested_date && $request->requested_time_slot) {
                            $conflictingBooking = \App\Models\Booking::where('date', $request->requested_date)
                                ->where('time_slot', $request->requested_time_slot)
                                ->whereIn('status', ['pending', 'confirmed'])
                                ->where('id', '!=', $booking->id)
                                ->first();
                            $hasConflict = $conflictingBooking !== null;
                        }
                        $request->has_conflict = $hasConflict;
                    }
                } else {
                    // Handle instrument rental reschedule requests
                    $rental = $request->instrumentRental;
                    if ($rental) {
                        // Create a booking-like data structure for consistency
                        $request->booking_data = (object) [
                            'reference' => $rental->reference_code,
                            'customer_name' => $rental->user->name ?? 'Unknown Customer',
                            'date' => $rental->rental_start_date,
                            'time_slot' => 'N/A (Multi-day rental)',
                            'duration' => \Carbon\Carbon::parse($rental->rental_start_date)->diffInDays(\Carbon\Carbon::parse($rental->rental_end_date)) + 1 . ' days'
                        ];
                        
                        // Check for conflicts (instrument rentals have different conflict logic)
                        $hasConflict = false;
                        if ($request->requested_start_date && $request->requested_end_date) {
                            $conflictingRental = \App\Models\InstrumentRental::where('instrument_type', $rental->instrument_type)
                                ->where(function($query) use ($request) {
                                    $query->whereBetween('rental_start_date', [$request->requested_start_date, $request->requested_end_date])
                                          ->orWhereBetween('rental_end_date', [$request->requested_start_date, $request->requested_end_date])
                                          ->orWhere(function($q) use ($request) {
                                              $q->where('rental_start_date', '<=', $request->requested_start_date)
                                                ->where('rental_end_date', '>=', $request->requested_end_date);
                                          });
                                })
                                ->whereIn('status', ['pending', 'confirmed'])
                                ->where('id', '!=', $rental->id)
                                ->first();
                            $hasConflict = $conflictingRental !== null;
                        }
                        $request->has_conflict = $hasConflict;
                    }
                }
                return $request;
            });

            return view('admin.reschedule-requests', compact('rescheduleRequests'));

        } catch (\Exception $e) {
            Log::error('Error loading reschedule requests: ' . $e->getMessage());
            return redirect()->route('admin.dashboard')->with('error', 'Error loading reschedule requests.');
        }
    }

    /**
     * Create database backup
     */
    private function createDatabaseBackup($backupPath, $backupName)
    {
        $databaseName = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host');
        $port = config('database.connections.mysql.port', 3306);

        $sqlFile = $backupPath . '/database.sql';

        // Use mysqldump command
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s --port=%s --single-transaction --routines --triggers %s > %s',
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($databaseName),
            escapeshellarg($sqlFile)
        );

        $process = \Symfony\Component\Process\Process::fromShellCommandline($command);
        $process->run();

        if (!$process->isSuccessful()) {
            // Fallback to Laravel's database export
            $this->createLaravelDatabaseBackup($backupPath);
        }
    }

    /**
     * Laravel-based database backup (fallback)
     */
    private function createLaravelDatabaseBackup($backupPath)
    {
        $tables = DB::select('SHOW TABLES');
        $sqlContent = "-- Database Backup\n-- Generated: " . Carbon::now() . "\n\n";

        foreach ($tables as $table) {
            $tableName = array_values((array) $table)[0];
            
            // Get table structure
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`")[0];
            $sqlContent .= "-- Table: {$tableName}\n";
            $sqlContent .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $sqlContent .= $createTable->{'Create Table'} . ";\n\n";

            // Get table data
            $rows = DB::table($tableName)->get();
            if ($rows->count() > 0) {
                $sqlContent .= "-- Data for table: {$tableName}\n";
                foreach ($rows as $row) {
                    $values = array_map(function($value) {
                        return is_null($value) ? 'NULL' : "'" . addslashes($value) . "'";
                    }, (array) $row);
                    $sqlContent .= "INSERT INTO `{$tableName}` VALUES (" . implode(', ', $values) . ");\n";
                }
                $sqlContent .= "\n";
            }
        }

        File::put($backupPath . '/database.sql', $sqlContent);
    }

    /**
     * Create files backup
     */
    private function createFilesBackup($backupPath)
    {
        $filesToBackup = [
            'storage/app/public' => 'storage',
            'public/images' => 'public_images',
            '.env' => 'env_file'
        ];

        foreach ($filesToBackup as $source => $destination) {
            $sourcePath = base_path($source);
            $destPath = $backupPath . '/' . $destination;

            if (File::exists($sourcePath)) {
                if (File::isDirectory($sourcePath)) {
                    File::copyDirectory($sourcePath, $destPath);
                } else {
                    File::copy($sourcePath, $destPath);
                }
            }
        }
    }

    /**
     * Get list of available backups
     */
    private function getAvailableBackups()
    {
        $backupDir = storage_path('app\\backups');
        $backups = [];

        if (File::exists($backupDir)) {
            $directories = File::directories($backupDir);
            foreach ($directories as $directory) {
                $dirName = basename($directory);
                $backups[] = [
                    'name' => $dirName,
                    'size' => $this->formatBytes($this->getDirectorySize($directory)),
                    'date' => Carbon::createFromTimestamp(filemtime($directory))->format('Y-m-d H:i:s')
                ];
            }
        }

        return collect($backups)->sortByDesc('date')->values()->all();
    }

    /**
     * Get directory size recursively
     */
    private function getDirectorySize($directory)
    {
        $size = 0;
        if (File::exists($directory)) {
            foreach (File::allFiles($directory) as $file) {
                $size += $file->getSize();
            }
        }
        return $size;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Restore database from SQL file
     */
    private function restoreFromSqlFile($sqlFilePath)
    {
        if (!File::exists($sqlFilePath)) {
            throw new \Exception('SQL file not found');
        }

        $sqlContent = File::get($sqlFilePath);
        
        // Split SQL content into individual statements
        $statements = array_filter(
            array_map('trim', explode(';', $sqlContent)),
            function($statement) {
                return !empty($statement) && !preg_match('/^\s*--/', $statement);
            }
        );

        DB::beginTransaction();
        try {
            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            
            foreach ($statements as $statement) {
                if (!empty(trim($statement))) {
                    DB::statement($statement);
                }
            }
            
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            throw new \Exception('Failed to execute SQL: ' . $e->getMessage());
        }
    }

    /**
     * Restore database from archive file (zip, tar, gz)
     */
    private function restoreFromArchive($archivePath, $extension)
    {
        $extractPath = storage_path('app/temp/extract_' . time());
        
        try {
            // Create extraction directory
            File::makeDirectory($extractPath, 0755, true);
            
            // Extract archive based on type
            if ($extension === 'zip') {
                $zip = new \ZipArchive();
                if ($zip->open($archivePath) === TRUE) {
                    $zip->extractTo($extractPath);
                    $zip->close();
                } else {
                    throw new \Exception('Failed to open ZIP file');
                }
            } elseif (in_array($extension, ['tar', 'gz'])) {
                $tar = new \PharData($archivePath);
                $tar->extractTo($extractPath);
            }
            
            // Look for SQL file in extracted content
            $sqlFile = $this->findSqlFileInDirectory($extractPath);
            
            if (!$sqlFile) {
                throw new \Exception('No SQL file found in the archive');
            }
            
            // Restore from the found SQL file
            $this->restoreFromSqlFile($sqlFile);
            
        } finally {
            // Clean up extraction directory
            if (File::exists($extractPath)) {
                File::deleteDirectory($extractPath);
            }
        }
    }

    /**
     * Find SQL file in directory recursively
     */
    private function findSqlFileInDirectory($directory)
    {
        $files = File::allFiles($directory);
        
        foreach ($files as $file) {
            if ($file->getExtension() === 'sql') {
                return $file->getPathname();
            }
        }
        
        return null;
    }

    /**
     * Show carousel management page
     */
    public function carouselManagement()
    {
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        $carouselItems = CarouselItem::ordered()->get();
        
        return view('admin.carousel', compact('user', 'carouselItems'));
    }

    /**
     * Store a new carousel item
     */
    public function storeCarouselItem(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'expertise' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'order_position' => 'required|integer|min:0'
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/carousel'), $imageName);
            $imagePath = $imageName; // Store only filename, not full path
        }

        CarouselItem::create([
            'title' => $request->title,
            'description' => $request->description,
            'expertise' => $request->expertise,
            'image_path' => $imagePath,
            'order_position' => $request->order_position,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.carousel')->with('success', 'Carousel item created successfully!');
    }

    /**
     * Update carousel item
     */
    public function updateCarouselItem(Request $request, $id)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        $carouselItem = CarouselItem::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'expertise' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'order_position' => 'required|integer|min:0'
        ]);

        $updateData = [
            'title' => $request->title,
            'description' => $request->description,
            'expertise' => $request->expertise,
            'order_position' => $request->order_position,
            'is_active' => $request->has('is_active')
        ];

        // Handle image upload if new image is provided
        if ($request->hasFile('image')) {
            // Delete old image
            if ($carouselItem->image_path && file_exists(public_path('images/carousel/' . $carouselItem->image_path))) {
                unlink(public_path('images/carousel/' . $carouselItem->image_path));
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/carousel'), $imageName);
            $updateData['image_path'] = $imageName; // Store only filename, not full path
        }

        $carouselItem->update($updateData);

        return redirect()->route('admin.carousel')->with('success', 'Carousel item updated successfully!');
    }

    /**
     * Delete carousel item
     */
    public function deleteCarouselItem($id)
    {
        /** @var User $user */
        $user = Auth::user();
        if (!Auth::check() || !$user->isAdmin()) {
            abort(403, 'Access denied. Admin access required.');
        }

        $carouselItem = CarouselItem::findOrFail($id);

        // Delete associated image
        if ($carouselItem->image_path && file_exists(public_path('images/carousel/' . $carouselItem->image_path))) {
            unlink(public_path('images/carousel/' . $carouselItem->image_path));
        }

        $carouselItem->delete();

        return redirect()->route('admin.carousel')->with('success', 'Carousel item deleted successfully!');
    }
}
