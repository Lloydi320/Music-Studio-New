@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="admin-dashboard">
    <div class="dashboard-header">
        <h1>Admin Dashboard</h1>
        <p>Welcome back, {{ $user->name }}!</p>
    </div>

    <!-- Quick Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Bookings</h3>
            <div class="stat-number">{{ $totalBookings }}</div>
        </div>
        <div class="stat-card">
            <h3>Pending</h3>
            <div class="stat-number pending">{{ $pendingBookings }}</div>
        </div>
        <div class="stat-card">
            <h3>Confirmed</h3>
            <div class="stat-number confirmed">{{ $confirmedBookings }}</div>
        </div>
        <div class="stat-card">
            <h3>Calendar Status</h3>
            <div class="stat-status">
                @if($user->hasGoogleCalendarAccess())
                    <span class="status-connected">✓ Connected</span>
                @else
                    <span class="status-disconnected">✗ Not Connected</span>
                @endif
            </div>
        </div>
        <div class="stat-card">
            <h3>Total Rentals</h3>
            <div class="stat-number">{{ $totalRentals }}</div>
        </div>
        <div class="stat-card">
            <h3>Pending Rentals</h3>
            <div class="stat-number pending">{{ $pendingRentals }}</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <h2>Quick Actions</h2>
        <div class="action-buttons">
            <a href="{{ route('admin.calendar') }}" class="btn btn-primary">
                <i class="icon-calendar"></i> Google Calendar Setup
            </a>
            <a href="{{ route('admin.database') }}" class="btn btn-success">
                <i class="icon-database">🗄️</i> Database Management
            </a>
            <a href="{{ route('admin.instrument-rentals') }}" class="btn btn-info">
                <i class="icon-rental"></i> Manage Instrument Rentals
            </a>
            @if($user->hasGoogleCalendarAccess())
                <form method="POST" action="{{ route('admin.calendar.sync') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-secondary">
                        <i class="icon-sync"></i> Sync All Bookings
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="recent-bookings">
        <h2>Recent Bookings</h2>
        @if($recentBookings->count() > 0)
            <div class="bookings-table">
                <table>
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Calendar</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentBookings as $booking)
                        <tr>
                            <td><strong>{{ $booking->reference }}</strong></td>
                            <td>{{ $booking->user->name }}</td>
                            <td>{{ \Carbon\Carbon::parse($booking->date)->format('M d, Y') }}</td>
                            <td>{{ $booking->time_slot }}</td>
                            <td>{{ $booking->duration }}h</td>
                            <td>
                                <span class="status-badge status-{{ $booking->status }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td>
                                @if($booking->google_event_id)
                                    <span class="calendar-status synced">✓ Synced</span>
                                @else
                                    <span class="calendar-status not-synced">○ Not Synced</span>
                                @endif
                            </td>
                            <td>
                                @if($booking->status === 'pending')
                                    <!-- Approve Button -->
                                    <form method="POST" action="{{ route('admin.booking.approve', $booking->id) }}" 
                                          style="display: inline; margin-right: 5px;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-sm" title="Approve Booking">
                                            ✓ Approve
                                        </button>
                                    </form>
                                    
                                    <!-- Reject Button -->
                                    <form method="POST" action="{{ route('admin.booking.reject', $booking->id) }}" 
                                          onsubmit="return confirm('Are you sure you want to reject booking {{ $booking->reference }} for {{ $booking->user->name }}?')" 
                                          style="display: inline; margin-right: 5px;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-warning btn-sm" title="Reject Booking">
                                            ✗ Reject
                                        </button>
                                    </form>
                                @endif
                                
                                <!-- Delete Button (always available) -->
                                <form method="POST" action="{{ route('admin.booking.delete', $booking->id) }}" 
                                      onsubmit="return confirm('Are you sure you want to delete booking {{ $booking->reference }} for {{ $booking->user->name }}? This will also remove it from Google Calendar.')" 
                                      style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete Booking">
                                        🗑️ Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="no-bookings">No bookings found.</p>
        @endif
    </div>

    <!-- Recent Instrument Rentals -->
    <div class="recent-rentals">
        <h2>Recent Instrument Rentals</h2>
        @if($recentRentals->count() > 0)
            <div class="rentals-table">
                <table>
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Client</th>
                            <th>Instrument</th>
                            <th>Rental Period</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentRentals as $rental)
                        <tr>
                            <td><strong>{{ $rental->reference }}</strong></td>
                            <td>{{ $rental->user->name }}</td>
                            <td>{{ $rental->instrument_name }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($rental->rental_start_date)->format('M d') }} - 
                                {{ \Carbon\Carbon::parse($rental->rental_end_date)->format('M d, Y') }}
                                <br><small>{{ $rental->rental_duration_days }} days</small>
                            </td>
                            <td>₱{{ number_format($rental->total_amount, 2) }}</td>
                            <td>
                                <span class="status-badge status-{{ $rental->status }}">
                                    {{ ucfirst($rental->status) }}
                                </span>
                            </td>
                            <td>
                                @if($rental->status === 'pending')
                                    <!-- Approve Button -->
                                    <form method="POST" action="{{ route('admin.rental.approve', $rental->id) }}" 
                                          style="display: inline; margin-right: 5px;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-sm" title="Approve Rental">
                                            ✓ Approve
                                        </button>
                                    </form>
                                    
                                    <!-- Reject Button -->
                                    <form method="POST" action="{{ route('admin.rental.reject', $rental->id) }}" 
                                          onsubmit="return confirm('Are you sure you want to reject rental {{ $rental->reference }} for {{ $rental->user->name }}?')" 
                                          style="display: inline; margin-right: 5px;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-warning btn-sm" title="Reject Rental">
                                            ✗ Reject
                                        </button>
                                    </form>
                                @else
                                    <!-- Status Change Dropdown -->
                                    <form action="{{ route('admin.rental-status', $rental->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <select name="status" onchange="this.form.submit()" class="status-select">
                                            <option value="confirmed" {{ $rental->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                            <option value="active" {{ $rental->status === 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="returned" {{ $rental->status === 'returned' ? 'selected' : '' }}>Returned</option>
                                            <option value="cancelled" {{ $rental->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="no-rentals">No instrument rentals found.</p>
        @endif
    </div>

    <!-- User Management -->
    <div class="user-management">
        <h2>User Management</h2>
        
        <!-- Make Admin Form -->
        <div class="admin-form">
            <h3>Grant Admin Access</h3>
            
            @if ($errors->any())
                <div class="alert alert-error" style="background-color: #f8d7da; color: #721c24; padding: 15px; margin: 10px 0; border-radius: 5px; border: 1px solid #f5c6cb;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if(session('success'))
                <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 15px; margin: 10px 0; border-radius: 5px; border: 1px solid #c3e6cb;">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-error" style="background-color: #f8d7da; color: #721c24; padding: 15px; margin: 10px 0; border-radius: 5px; border: 1px solid #f5c6cb;">
                    {{ session('error') }}
                </div>
            @endif
            
            <form method="POST" action="{{ route('admin.make') }}">
                @csrf
                <div class="form-group">
                    <label for="email">User Email:</label>
                    <input type="email" id="email" name="email" required 
                           value="{{ old('email') }}"
                           placeholder="Enter user email to grant admin access"
                           class="@error('email') error @enderror">
                    @error('email')
                        <span class="error-message" style="color: #e74c3c; font-size: 14px; display: block; margin-top: 5px;">{{ $message }}</span>
                    @enderror
                    <button type="submit" class="btn btn-success">Make Admin</button>
                </div>
            </form>
        </div>

        <!-- Current Admins -->
        <div class="current-admins">
            <h3>Current Admins</h3>
            @php
                $admins = \App\Models\User::where('is_admin', true)->get();
                $adminUsers = \Illuminate\Support\Facades\DB::table('admin_users')
                    ->where('is_active', true)
                    ->orderBy('role', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->get();
            @endphp
            @if($admins->count() > 0 || $adminUsers->count() > 0)
                <div class="admin-list">
                    @foreach($admins as $admin)
                    @php
                        $adminUserRecord = $adminUsers->firstWhere('email', $admin->email);
                        $role = $adminUserRecord ? $adminUserRecord->role : 'admin';
                    @endphp
                    <div class="admin-item">
                        <div class="admin-info">
                            <strong>{{ $admin->name }}</strong>
                            <span class="admin-email">{{ $admin->email }}</span>
                            @if($role === 'super_admin')
                                <span class="role-badge super-admin">👑 Super Admin</span>
                            @else
                                <span class="role-badge admin">👤 Admin</span>
                            @endif
                            @if($admin->hasGoogleCalendarAccess())
                                <span class="calendar-badge">Calendar Connected</span>
                            @endif
                        </div>
                        @if($admin->id !== Auth::id())
                            @if($role !== 'super_admin')
                            <form method="POST" action="{{ route('admin.remove') }}" 
                                  onsubmit="return confirm('Remove admin privileges from {{ $admin->name }}? This will remove admin access.')"
                                  style="display: inline;">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $admin->id }}">
                                <button type="submit" class="btn btn-danger btn-sm">Remove Admin</button>
                            </form>
                            @else
                            <span class="protected-admin">🔒 Protected</span>
                            @endif
                        @else
                        <span class="current-user">(You)</span>
                        @endif
                    </div>
                    @endforeach
                    
                    {{-- Show admin_users records that don't have corresponding users --}}
                    @foreach($adminUsers as $adminUser)
                        @if(!$admins->contains('email', $adminUser->email))
                        <div class="admin-item">
                            <div class="admin-info">
                                <strong>{{ $adminUser->name }}</strong>
                                <span class="admin-email">{{ $adminUser->email }}</span>
                                @if($adminUser->role === 'super_admin')
                                    <span class="role-badge super-admin">👑 Super Admin</span>
                                @else
                                    <span class="role-badge admin">👤 Admin</span>
                                @endif
                                <span class="status-badge">Admin Users Only</span>
                            </div>
                            @if($adminUser->role !== 'super_admin')
                            <form method="POST" action="{{ route('admin.remove') }}" 
                                  onsubmit="return confirm('Remove admin privileges from {{ $adminUser->name }}? This will remove admin access.')"
                                  style="display: inline;">
                                @csrf
                                <input type="hidden" name="user_id" value="0">
                                <input type="hidden" name="admin_email" value="{{ $adminUser->email }}">
                                <button type="submit" class="btn btn-danger btn-sm">Remove Admin</button>
                            </form>
                            @else
                            <span class="protected-admin">🔒 Protected</span>
                            @endif
                        </div>
                        @endif
                    @endforeach
                </div>
            @else
                <p>No admin users found.</p>
            @endif
        </div>
    </div>
</div>

<style>
.admin-dashboard {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.dashboard-header {
    margin-bottom: 30px;
    text-align: center;
}

.dashboard-header h1 {
    color: #333;
    margin-bottom: 10px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
}

.stat-card h3 {
    margin: 0 0 10px 0;
    color: #666;
    font-size: 14px;
    text-transform: uppercase;
}

.stat-number {
    font-size: 32px;
    font-weight: bold;
    color: #333;
}

.stat-number.pending { color: #f39c12; }
.stat-number.confirmed { color: #27ae60; }

.status-connected { color: #27ae60; font-weight: bold; }
.status-disconnected { color: #e74c3c; font-weight: bold; }

.quick-actions, .recent-bookings, .user-management {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.action-buttons {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary { background: #3498db; color: white; }
.btn-secondary { background: #95a5a6; color: white; }
.btn-success { background: #27ae60; color: white; }
.btn-danger { background: #e74c3c; color: white; }
.btn-info { background: #17a2b8; color: white; }
.btn-sm { padding: 5px 10px; font-size: 12px; }

.bookings-table {
    overflow-x: auto;
}

.bookings-table table {
    width: 100%;
    border-collapse: collapse;
}

.bookings-table th,
.bookings-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.bookings-table th {
    background: #f8f9fa;
    font-weight: bold;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
}

.status-pending { background: #fff3cd; color: #856404; }
.status-confirmed { background: #d4edda; color: #155724; }
.status-cancelled { background: #f8d7da; color: #721c24; }

.calendar-status.synced { color: #27ae60; }
.calendar-status.not-synced { color: #e74c3c; }

.form-group {
    display: flex;
    gap: 10px;
    align-items: center;
    flex-wrap: wrap;
}

.form-group input[type="email"] {
    flex: 1;
    min-width: 250px;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.form-group input[type="email"].error {
    border-color: #e74c3c;
    background-color: #fdf2f2;
}

.error-message {
    color: #e74c3c;
    font-size: 14px;
    display: block;
    margin-top: 5px;
}

.admin-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.admin-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 5px;
}

.admin-info {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.admin-email {
    color: #666;
    font-size: 14px;
}

.calendar-badge {
    background: #27ae60;
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    margin-left: 10px;
}

.role-badge {
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    margin-left: 10px;
    font-weight: bold;
}

.role-badge.super-admin {
    background: #f39c12;
    color: white;
}

.role-badge.admin {
    background: #3498db;
    color: white;
}

.status-badge {
    background: #95a5a6;
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    margin-left: 10px;
}

.current-user {
    color: #666;
    font-style: italic;
}

.protected-admin {
    color: #e67e22;
    font-weight: bold;
    font-size: 14px;
    padding: 4px 8px;
    background: #fdf2e9;
    border-radius: 4px;
    border: 1px solid #f39c12;
}

.btn-sm {
    padding: 4px 8px;
    font-size: 12px;
    border-radius: 3px;
    border: none;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
}

.btn-danger {
    background-color: #e74c3c;
    color: white;
    transition: background-color 0.2s;
}

.btn-danger:hover {
    background-color: #c0392b;
}

.btn-success {
    background-color: #27ae60;
    color: white;
    transition: background-color 0.2s;
}

.btn-success:hover {
    background-color: #229954;
}

.btn-warning {
    background-color: #f39c12;
    color: white;
    transition: background-color 0.2s;
}

.btn-warning:hover {
    background-color: #e67e22;
}

.bookings-table td {
    vertical-align: middle;
}

.no-bookings {
    text-align: center;
    color: #666;
    padding: 40px;
}
.recent-rentals {
    margin-bottom: 40px;
}

.rentals-table {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

.rentals-table table {
    width: 100%;
    border-collapse: collapse;
}

.rentals-table th,
.rentals-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.rentals-table th {
    background: #f8f9fa;
    font-weight: bold;
    color: #333;
}

.status-select {
    padding: 4px 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 12px;
}

.no-rentals {
    text-align: center;
    color: #666;
    font-style: italic;
    padding: 40px;
}
</style>
@endsection