@extends('layouts.admin')

@section('title', 'Studio Rental Bookings')

@section('content')
<style>
    :root {
        --gradient-primary: linear-gradient(135deg, #3a3a3a 0%, #2c2c2c 100%);
        --gradient-secondary: linear-gradient(135deg, #4a4a4a 0%, #3a3a3a 100%);
        --gradient-success: linear-gradient(135deg, #ffc107 0%, #ffca2c 100%);
        --gradient-warning: linear-gradient(135deg, #ffc107 0%, #ffca2c 100%);
        --gradient-danger: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        --shadow-soft: 0 10px 40px rgba(0, 0, 0, 0.3);
        --shadow-hover: 0 20px 60px rgba(0, 0, 0, 0.4);
    }

    .admin-content {
        padding: 2rem;
        background: linear-gradient(135deg, #2c2c2c 0%, #1a1a1a 100%);
        min-height: 100vh;
        color: #e0e0e0;
    }

    .page-header {
        background: var(--gradient-primary);
        color: #ffc107;
        padding: 2rem;
        border-radius: 20px;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-soft);
        position: relative;
        overflow: hidden;
        border: 2px solid #ffc107;
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
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        position: relative;
        z-index: 1;
        color: #ffc107;
    }

    .stats-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: #4a4a4a;
        padding: 1.5rem;
        border-radius: 15px;
        box-shadow: var(--shadow-soft);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        border: 1px solid #555;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--gradient-warning);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        background: var(--gradient-warning);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        color: #ffc107;
    }

    .stat-label {
        color: #e0e0e0;
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
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
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
        background: var(--gradient-primary);
        color: white;
    }

    .btn-secondary {
        background: #f8f9fa;
        color: #666;
        border: 2px solid #e1e5e9;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-soft);
    }

    .bookings-table-card {
        background: white;
        border-radius: 20px;
        box-shadow: var(--shadow-soft);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .table-header {
        background: var(--gradient-primary);
        color: white;
        padding: 1.5rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table-title {
        font-size: 1.3rem;
        font-weight: 600;
        margin: 0;
    }

    .records-count {
        background: rgba(255, 255, 255, 0.2);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        margin: 0;
    }

    .table thead th {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        color: #333;
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
        border-bottom: 1px solid #f1f3f4;
        vertical-align: middle;
    }

    .table tbody tr {
        transition: all 0.3s ease;
    }

    .table tbody tr:hover {
        background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%);
        transform: scale(1.01);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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

    .status-accepted {
        background: var(--gradient-success);
        color: white;
    }

    .status-rejected {
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
        background: var(--gradient-primary);
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
        color: #333;
        font-size: 0.9rem;
    }

    .user-email {
        color: #666;
        font-size: 0.8rem;
    }

    .booking-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .service-type {
        font-weight: 600;
        color: #333;
    }

    .duration {
        color: #666;
        font-size: 0.85rem;
        text-transform: capitalize;
    }

    .booking-dates {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        font-size: 0.85rem;
    }

    .date-time {
        color: #333;
        font-weight: 500;
    }

    .time-slot {
        color: #666;
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

    .btn-view {
        background-color: #17a2b8;
        border-color: #17a2b8;
        color: white;
    }

    .btn-view:hover {
        background-color: #138496;
        border-color: #117a8b;
    }

    .btn-reschedule {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #212529;
    }

    .btn-reschedule:hover {
        background-color: #e0a800;
        border-color: #d39e00;
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
        <h1 class="page-title">🎵 Studio Rental Bookings</h1>
    </div>

    <!-- Stats Overview -->
    <div class="stats-overview">
        <div class="stat-card">
            <div class="stat-number">{{ $statusCounts['all'] }}</div>
            <div class="stat-label">Total Studio Bookings</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $statusCounts['confirmed'] }}</div>
            <div class="stat-label">Confirmed Bookings</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $statusCounts['pending'] }}</div>
            <div class="stat-label">Pending Approvals</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $statusCounts['rejected'] }}</div>
            <div class="stat-label">Rejected Bookings</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-card">
        <h3 class="filters-title">🔍 Filter Options</h3>
        <form method="GET" action="{{ route('admin.bookings') }}">
            <div class="filters-grid">
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search by name, email..." value="{{ $search }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Date Filter</label>
                    <select name="date_filter" class="form-control">
                        <option value="all" {{ $dateFilter === 'all' ? 'selected' : '' }}>All Dates</option>
                        <option value="today" {{ $dateFilter === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="week" {{ $dateFilter === 'week' ? 'selected' : '' }}>This Week</option>
                        <option value="month" {{ $dateFilter === 'month' ? 'selected' : '' }}>This Month</option>
                    </select>
                </div>
            </div>
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="{{ route('admin.bookings') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Studio Rental Records -->
    <div class="bookings-table-card">
        <div class="table-header">
            <h3 class="table-title">📋 Studio Rental Records</h3>
            <div class="records-count">{{ $bookings->total() }} Records</div>
        </div>
        
        <div class="table-responsive">
            @if($bookings->count() > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Customer</th>
                            <th>Studio Details</th>
                            <th>Booking Period</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bookings as $booking)
                            <tr>
                                <td>
                                    <div class="reference-id">
                                        <strong>#{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</strong>
                                    </div>
                                </td>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            {{ strtoupper(substr($booking->user->name, 0, 1)) }}
                                        </div>
                                        <div class="user-details">
                                            <div class="user-name">{{ $booking->user->name }}</div>
                                            <div class="user-email">{{ $booking->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="booking-info">
                                        <div class="service-type">Studio Rental</div>
                                        <div class="duration">{{ $booking->duration }} hours</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="booking-dates">
                                        <div class="date-time">{{ \Carbon\Carbon::parse($booking->date)->format('M d, Y') }}</div>
                                        <div class="time-slot">{{ $booking->time_slot }}</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge status-{{ $booking->status }}">
                                        @if($booking->status === 'pending')
                                            ⏳ Pending
                                        @elseif($booking->status === 'confirmed')
                                            ✅ Confirmed
                                        @elseif($booking->status === 'rejected')
                                            ❌ Rejected
                                        @else
                                            {{ ucfirst($booking->status) }}
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <div class="created-date">
                                        {{ $booking->created_at->format('M d, Y') }}
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn btn-view" title="View Details">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        @if($booking->status === 'pending')
                                            <form method="POST" action="{{ route('admin.booking.approve', $booking->id) }}" style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to accept this booking?')">
                                                    ✓ Accept
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.booking.reject', $booking->id) }}" style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to reject this booking?')">
                                                    ✗ Reject
                                                </button>
                                            </form>
                                        @elseif($booking->status === 'confirmed')
                                            <button class="btn btn-reschedule" onclick="rescheduleBooking({{ $booking->id }})" title="Reschedule Booking">
                                                <i class="fas fa-calendar-alt"></i> Reschedule
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">🎵</div>
                    <div class="empty-state-text">No studio rental bookings found</div>
                    <div class="empty-state-subtext">Try adjusting your filters or check back later</div>
                </div>
            @endif
        </div>
        
        @if($bookings->hasPages())
            <div class="pagination-wrapper">
                {{ $bookings->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

</div>

<script>
    function rescheduleBooking(bookingId) {
        // Add reschedule functionality here
        if (confirm('Are you sure you want to reschedule this booking?')) {
            // Implement reschedule logic
            alert('Reschedule functionality will be implemented');
        }
    }

    // Add smooth animations for status badges
    document.addEventListener('DOMContentLoaded', function() {
        const statusBadges = document.querySelectorAll('.status-badge');
        statusBadges.forEach(badge => {
            badge.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.05)';
            });
            badge.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });

        // Add confirmation for action buttons
        const acceptButtons = document.querySelectorAll('.btn-success');
        const rejectButtons = document.querySelectorAll('.btn-danger');

        acceptButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to accept this booking?')) {
                    e.preventDefault();
                }
            });
        });

        rejectButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to reject this booking?')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endsection