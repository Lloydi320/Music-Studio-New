@extends('layouts.admin')

@section('title', 'Music Lesson Bookings')

@section('content')
<style>
    :root {
        --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --gradient-success: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --gradient-warning: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        --gradient-danger: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        --gradient-music: linear-gradient(135deg, #ff9a9e 0%, #fecfef 50%, #fecfef 100%);
        --shadow-soft: 0 10px 40px rgba(0, 0, 0, 0.1);
        --shadow-hover: 0 20px 60px rgba(0, 0, 0, 0.15);
    }

    .admin-content {
        padding: 2rem;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
    }

    .page-header {
        background: var(--gradient-music);
        color: white;
        padding: 2rem;
        border-radius: 20px;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-soft);
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }

    .page-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        position: relative;
        z-index: 1;
    }

    .stats-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 15px;
        box-shadow: var(--shadow-soft);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--gradient-music);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        background: var(--gradient-music);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .stat-label {
        color: #666;
        font-weight: 500;
        margin-top: 0.5rem;
    }

    .filters-card {
        background: white;
        padding: 2rem;
        border-radius: 20px;
        box-shadow: var(--shadow-soft);
        margin-bottom: 2rem;
        backdrop-filter: blur(10px);
    }

    .filters-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        background: var(--gradient-secondary);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }

    .form-control {
        padding: 0.75rem 1rem;
        border: 2px solid #e1e5e9;
        border-radius: 10px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.8);
    }

    .form-control:focus {
        outline: none;
        border-color: #ff9a9e;
        box-shadow: 0 0 0 3px rgba(255, 154, 158, 0.1);
        background: white;
    }

    .filter-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        cursor: pointer;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.9rem;
    }

    .btn-primary {
        background: var(--gradient-music);
        color: white;
    }

    .btn-secondary {
        background: #2a2a2a;
        color: #b0b0b0;
        border: 2px solid #3a3a3a;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-soft);
    }

    .lessons-table-card {
        background: #2a2a2a;
        border-radius: 20px;
        box-shadow: var(--shadow-soft);
        overflow: hidden;
        margin-bottom: 2rem;
        border: 1px solid #3a3a3a;
    }

    .table-header {
        background: var(--gradient-music);
        color: #1a1a1a;
        padding: 1.5rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table-title {
        font-size: 1.3rem;
        font-weight: 600;
        margin: 0;
        color: #1a1a1a;
    }

    .records-count {
        background: rgba(26, 26, 26, 0.2);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        color: #1a1a1a;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        margin: 0;
    }

    .table thead th {
        background: linear-gradient(135deg, #3a3a3a 0%, #2a2a2a 100%);
        color: #FFD700;
        font-weight: 600;
        padding: 1rem;
        border: none;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .table tbody td {
        padding: 1rem;
        border-bottom: 1px solid #3a3a3a;
        vertical-align: middle;
        background: #2a2a2a;
        color: #e0e0e0;
    }

    .table tbody tr {
        transition: all 0.3s ease;
    }

    .table tbody tr:hover {
        background: linear-gradient(135deg, #3a3a3a 0%, #2a2a2a 100%);
        transform: scale(1.01);
        box-shadow: 0 5px 15px rgba(255,215,0,0.1);
    }

    .status-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }

    .status-pending {
        background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%);
        color: #2d3436;
    }

    .status-confirmed {
        background: var(--gradient-success);
        color: white;
    }

    .status-completed {
        background: linear-gradient(135deg, #a8e6cf 0%, #7fcdcd 100%);
        color: #2d3436;
    }

    .status-cancelled {
        background: var(--gradient-danger);
        color: white;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--gradient-music);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .user-details {
        display: flex;
        flex-direction: column;
    }

    .user-name {
        font-weight: 600;
        color: #e0e0e0;
        font-size: 0.9rem;
    }

    .user-email {
        color: #b0b0b0;
        font-size: 0.8rem;
    }

    .lesson-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .lesson-date {
        font-weight: 600;
        color: #e0e0e0;
    }

    .lesson-time {
        color: #b0b0b0;
        font-size: 0.85rem;
    }

    .lesson-duration {
        color: #999;
        font-size: 0.8rem;
    }

    .lesson-details {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        font-size: 0.85rem;
    }

    .service-type {
        color: #333;
        font-weight: 500;
    }

    .lesson-notes {
        color: #666;
        font-style: italic;
    }

    .pagination-wrapper {
        display: flex;
        justify-content: center;
        padding: 2rem;
        background: white;
        border-radius: 0 0 20px 20px;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #666;
    }

    .empty-state-icon {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 0.5; }
        50% { opacity: 0.8; }
    }

    .empty-state-text {
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
    }

    .empty-state-subtext {
        color: #999;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .action-buttons .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 4px;
        font-weight: 500;
    }

    .action-buttons .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }

    .action-buttons .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }

    .action-buttons .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .action-buttons .btn-danger:hover {
        background-color: #c82333;
        border-color: #bd2130;
    }

    @media (max-width: 768px) {
        .admin-content {
            padding: 1rem;
        }

        .page-title {
            font-size: 2rem;
        }

        .filters-grid {
            grid-template-columns: 1fr;
        }

        .filter-actions {
            justify-content: stretch;
        }

        .filter-actions .btn {
            flex: 1;
        }

        .stats-overview {
            grid-template-columns: 1fr;
        }

        .table-responsive {
            font-size: 0.8rem;
        }

        .user-info {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
    }
</style>

<div class="admin-content">
    <div class="page-header">
        <h1 class="page-title">🎵 Music Lesson Bookings</h1>
    </div>

    <!-- Stats Overview -->
    <div class="stats-overview">
        <div class="stat-card">
            <div class="stat-number">{{ $totalLessons }}</div>
            <div class="stat-label">Total Music Lessons</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $musicLessonBookings->where('status', 'confirmed')->count() }}</div>
            <div class="stat-label">Confirmed Lessons</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $musicLessonBookings->where('status', 'pending')->count() }}</div>
            <div class="stat-label">Pending Approvals</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $musicLessonBookings->unique('user_id')->count() }}</div>
            <div class="stat-label">Active Students</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-card">
        <h3 class="filters-title">🔍 Filter Options</h3>
        <form method="GET" action="{{ route('admin.music-lesson-bookings') }}">
            <div class="filters-grid">
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Student</label>
                    <input type="text" name="user" class="form-control" placeholder="Search by name or email" value="{{ request('user') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">From Date</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">To Date</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="{{ route('admin.music-lesson-bookings') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Lessons Table -->
    <div class="lessons-table-card">
        <div class="table-header">
            <h3 class="table-title">Music Lesson Records</h3>
            <div class="records-count">{{ $musicLessonBookings->total() }} records found</div>
        </div>

        @if($musicLessonBookings->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Student</th>
                            <th>Lesson Schedule</th>
                            <th>Service Details</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($musicLessonBookings as $booking)
                            <tr>
                                <td>
                                    <strong>#{{ $booking->id }}</strong>
                                </td>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            {{ strtoupper(substr($booking->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="user-details">
                                            <div class="user-name">{{ $booking->user->name ?? 'Unknown' }}</div>
                                            <div class="user-email">{{ $booking->user->email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="lesson-info">
                                        <div class="lesson-date">
                                            {{ \Carbon\Carbon::parse($booking->date)->format('M d, Y') }}
                                        </div>
                                        <div class="lesson-time">
                                            {{ $booking->time_slot }} ({{ $booking->duration }} min)
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="lesson-details">
                                        <div class="service-type">{{ $booking->service_type ?? 'Music Lesson' }}</div>
                                        @if($booking->notes)
                                            <div class="lesson-notes">{{ Str::limit($booking->notes, 50) }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge status-{{ $booking->status }}">
                                        @if($booking->status == 'pending')
                                            ⏳ Pending
                                        @elseif($booking->status == 'confirmed')
                                            ✅ Confirmed
                                        @elseif($booking->status == 'completed')
                                            🎓 Completed
                                        @elseif($booking->status == 'cancelled')
                                            ❌ Cancelled
                                        @else
                                            {{ ucfirst($booking->status) }}
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <div style="font-size: 0.85rem; color: #666;">
                                        {{ $booking->created_at->format('M d, Y') }}<br>
                                        <small>{{ $booking->created_at->format('h:i A') }}</small>
                                    </div>
                                </td>
                                <td>
                                    @if($booking->status == 'pending')
                                        <div class="action-buttons">
                                            <form method="POST" action="{{ route('admin.booking.approve', $booking->id) }}" style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to approve this booking?')">
                                                    <i class="fas fa-check"></i> Accept
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.booking.reject', $booking->id) }}" style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to reject this booking?')">
                                                    <i class="fas fa-times"></i> Decline
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-muted">No actions available</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                {{ $musicLessonBookings->appends(request()->query())->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">🎵</div>
                <div class="empty-state-text">No music lesson bookings found</div>
                <div class="empty-state-subtext">Try adjusting your filters or check back later</div>
            </div>
        @endif
    </div>
</div>
@endsection