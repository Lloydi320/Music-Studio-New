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
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <h2>Quick Actions</h2>
        <div class="action-buttons">
            <a href="{{ route('admin.calendar') }}" class="btn btn-primary">
                <i class="icon-calendar"></i> Google Calendar Setup
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
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="no-bookings">No bookings found.</p>
        @endif
    </div>

    <!-- User Management -->
    <div class="user-management">
        <h2>User Management</h2>
        
        <!-- Make Admin Form -->
        <div class="admin-form">
            <h3>Grant Admin Access</h3>
            <form method="POST" action="{{ route('admin.make') }}">
                @csrf
                <div class="form-group">
                    <label for="email">User Email:</label>
                    <input type="email" id="email" name="email" required 
                           placeholder="Enter user email to grant admin access">
                    <button type="submit" class="btn btn-success">Make Admin</button>
                </div>
            </form>
        </div>

        <!-- Current Admins -->
        <div class="current-admins">
            <h3>Current Admins</h3>
            @php
                $admins = \App\Models\User::where('is_admin', true)->get();
            @endphp
            @if($admins->count() > 0)
                <div class="admin-list">
                    @foreach($admins as $admin)
                    <div class="admin-item">
                        <div class="admin-info">
                            <strong>{{ $admin->name }}</strong>
                            <span class="admin-email">{{ $admin->email }}</span>
                            @if($admin->hasGoogleCalendarAccess())
                                <span class="calendar-badge">Calendar Connected</span>
                            @endif
                        </div>
                        @if($admin->id !== Auth::id())
                        <form method="POST" action="{{ route('admin.remove') }}" 
                              onsubmit="return confirm('Remove admin privileges from {{ $admin->name }}?')"
                              style="display: inline;">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $admin->id }}">
                            <button type="submit" class="btn btn-danger btn-sm">Remove Admin</button>
                        </form>
                        @else
                        <span class="current-user">(You)</span>
                        @endif
                    </div>
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
    background: #d4edda;
    color: #155724;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 10px;
    text-transform: uppercase;
}

.current-user {
    color: #666;
    font-style: italic;
}

.no-bookings {
    text-align: center;
    color: #666;
    padding: 40px;
}
</style>
@endsection 