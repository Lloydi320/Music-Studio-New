@extends('layouts.admin')

@section('title', 'Instrument Bookings')

@section('content')
<style>
    :root {
        --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --gradient-success: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --gradient-warning: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        --gradient-danger: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        --shadow-soft: 0 10px 40px rgba(0, 0, 0, 0.1);
        --shadow-hover: 0 20px 60px rgba(0, 0, 0, 0.15);
    }

    .admin-content {
        padding: 2rem;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
    }

    .page-header {
        background: var(--gradient-primary);
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
        background: var(--gradient-primary);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        background: var(--gradient-primary);
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

    .rentals-table-card {
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

    .status-active {
        background: var(--gradient-success);
        color: white;
    }

    .status-returned {
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

    .instrument-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .instrument-name {
        font-weight: 600;
        color: #333;
    }

    .instrument-type {
        color: #666;
        font-size: 0.85rem;
        text-transform: capitalize;
    }

    .rental-dates {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        font-size: 0.85rem;
    }

    .date-range {
        color: #333;
        font-weight: 500;
    }

    .duration {
        color: #666;
    }

    .amount {
        font-weight: 700;
        font-size: 1.1rem;
        background: var(--gradient-success);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
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
        <h1 class="page-title">🎸 Instrument Bookings</h1>
    </div>

    <!-- Stats Overview -->
    <div class="stats-overview">
        <div class="stat-card">
            <div class="stat-number">{{ $totalRentals }}</div>
            <div class="stat-label">Total Instrument Rentals</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $instrumentRentals->where('status', 'active')->count() }}</div>
            <div class="stat-label">Active Rentals</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $instrumentRentals->where('status', 'pending')->count() }}</div>
            <div class="stat-label">Pending Approvals</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">₱{{ number_format($instrumentRentals->sum('total_amount'), 2) }}</div>
            <div class="stat-label">Total Revenue</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-card">
        <h3 class="filters-title">🔍 Filter Options</h3>
        <form method="GET" action="{{ route('admin.instrument-bookings') }}">
            <div class="filters-grid">
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="returned" {{ request('status') == 'returned' ? 'selected' : '' }}>Returned</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Instrument Type</label>
                    <select name="instrument_type" class="form-control">
                        <option value="">All Types</option>
                        @foreach($instrumentTypes as $type)
                            <option value="{{ $type }}" {{ request('instrument_type') == $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>
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
                <a href="{{ route('admin.instrument-bookings') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Rentals Table -->
    <div class="rentals-table-card">
        <div class="table-header">
            <h3 class="table-title">Instrument Rental Records</h3>
            <div class="records-count">{{ $instrumentRentals->total() }} records found</div>
        </div>

        @if($instrumentRentals->count() > 0)
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Customer</th>
                            <th>Instrument</th>
                            <th>Rental Period</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($instrumentRentals as $rental)
                            <tr>
                                <td>
                                    <strong>{{ $rental->reference }}</strong>
                                </td>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            {{ strtoupper(substr($rental->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="user-details">
                                            <div class="user-name">{{ $rental->user->name ?? 'Unknown' }}</div>
                                            <div class="user-email">{{ $rental->user->email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="instrument-info">
                                        <div class="instrument-name">{{ $rental->instrument_name }}</div>
                                        <div class="instrument-type">{{ $rental->instrument_type }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="rental-dates">
                                        <div class="date-range">
                                            {{ \Carbon\Carbon::parse($rental->rental_start_date)->format('M d, Y') }} - 
                                            {{ \Carbon\Carbon::parse($rental->rental_end_date)->format('M d, Y') }}
                                        </div>
                                        <div class="duration">{{ $rental->rental_duration_days }} day(s)</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="amount">₱{{ number_format($rental->total_amount, 2) }}</div>
                                </td>
                                <td>
                                    <span class="status-badge status-{{ $rental->status }}">
                                        @if($rental->status == 'pending')
                                            ⏳ Pending
                                        @elseif($rental->status == 'active')
                                            ✅ Active
                                        @elseif($rental->status == 'returned')
                                            📦 Returned
                                        @elseif($rental->status == 'cancelled')
                                            ❌ Cancelled
                                        @else
                                            {{ ucfirst($rental->status) }}
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <div style="font-size: 0.85rem; color: #666;">
                                        {{ $rental->created_at->format('M d, Y') }}<br>
                                        <small>{{ $rental->created_at->format('h:i A') }}</small>
                                    </div>
                                </td>
                                <td>
                                    @if($rental->status == 'pending')
                                        <div class="action-buttons">
                                            <form method="POST" action="{{ route('admin.rental.approve', $rental->id) }}" style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Are you sure you want to approve this rental?')">
                                                    <i class="fas fa-check"></i> Accept
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.rental.reject', $rental->id) }}" style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to reject this rental?')">
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
                {{ $instrumentRentals->appends(request()->query())->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">🎸</div>
                <div class="empty-state-text">No instrument rentals found</div>
                <div class="empty-state-subtext">Try adjusting your filters or check back later</div>
            </div>
        @endif
    </div>
</div>
@endsection