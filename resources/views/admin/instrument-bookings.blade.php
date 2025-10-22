@extends('layouts.admin')

@section('title', 'Instrument Rental Bookings')

@section('content')
<style>
    :root {
        --gradient-primary: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
        --gradient-secondary: linear-gradient(135deg, #FFD700 0%, #FFED4E 100%);
        --gradient-success: linear-gradient(135deg, #34a853 0%, #4ade80 100%);
        --gradient-warning: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
        --gradient-danger: linear-gradient(135deg, #ea4335 0%, #f87171 100%);
        --shadow-soft: 0 10px 40px rgba(0, 0, 0, 0.1);
        --shadow-hover: 0 20px 60px rgba(0, 0, 0, 0.15);
    }

    .admin-content {
        padding: 2rem;
        background: #1a1a1a;
        min-height: 100vh;
        color: #e0e0e0;
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
        background: #2a2a2a;
        padding: 1.5rem;
        border-radius: 15px;
        box-shadow: var(--shadow-soft);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        border: 1px solid #3a3a3a;
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
        color: #b0b0b0;
        font-weight: 500;
        margin-top: 0.5rem;
    }

    .filters-card {
        background: #2a2a2a;
        padding: 2rem;
        border-radius: 20px;
        box-shadow: var(--shadow-soft);
        margin-bottom: 2rem;
        backdrop-filter: blur(10px);
        border: 1px solid #3a3a3a;
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
        color: #FFD700;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }

    .form-control {
        padding: 0.9rem 1.2rem;
        border: 2px solid #4a4a4a;
        border-radius: 10px;
        font-size: 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        background: #2a2a2a;
        color: #ffffff;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);
        text-shadow: 0 1px 2px rgba(0,0,0,0.5);
    }

    .form-control:focus {
        outline: none;
        border-color: #FFD700;
        box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.3), inset 0 2px 4px rgba(0,0,0,0.2);
        background: #3a3a3a;
        color: #FFD700;
        text-shadow: 0 1px 3px rgba(0,0,0,0.7);
    }

    .form-control::placeholder {
        color: #FFD700;
        font-weight: 500;
        opacity: 0.8;
    }

    .form-select {
        padding: 0.9rem 1.2rem;
        border: 2px solid #4a4a4a;
        border-radius: 10px;
        font-size: 1.1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        background: #2a2a2a;
        color: #ffffff;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.2);
        cursor: pointer;
        text-shadow: 0 1px 2px rgba(0,0,0,0.5);
    }

    .form-select:focus {
        outline: none;
        border-color: #FFD700;
        box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.3), inset 0 2px 4px rgba(0,0,0,0.2);
        background: #3a3a3a;
        color: #FFD700;
        text-shadow: 0 1px 3px rgba(0,0,0,0.7);
    }

    .form-select option {
        background: #2a2a2a;
        color: #ffffff;
        padding: 0.8rem;
        font-weight: 500;
        border-bottom: 1px solid #4a4a4a;
    }

    .form-select option:hover {
        background: #FFD700;
        color: #1a1a1a;
    }

    .form-select option:selected {
        background: #FFD700;
        color: #1a1a1a;
        font-weight: 600;
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
        background: #3a3a3a;
        color: #e0e0e0;
        border: 2px solid #4a4a4a;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-soft);
        text-decoration: none;
        color: inherit;
    }

    .rentals-table-card {
        background: #2a2a2a;
        border-radius: 20px;
        box-shadow: var(--shadow-soft);
        overflow: hidden;
        margin-bottom: 2rem;
        border: 1px solid #3a3a3a;
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
        font-size: 1.6rem;
        font-weight: 800;
        margin: 0;
        color: #1a1a1a;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        letter-spacing: 0.5px;
    }

    .records-count {
        background: rgba(0, 0, 0, 0.08);
        backdrop-filter: blur(10px);
        padding: 0.6rem 1.2rem;
        border-radius: 25px;
        font-size: 0.85rem;
        font-weight: 600;
        letter-spacing: 0.3px;
        border: 1px solid rgba(0, 0, 0, 0.1);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        color: black;
    }

    .bookings-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
        gap: 1.5rem;
        padding: 1.5rem;
    }

    .booking-card {
        background: #3a3a3a;
        border-radius: 15px;
        box-shadow: var(--shadow-soft);
        transition: all 0.3s ease;
        border: 2px solid #4a4a4a;
        overflow: hidden;
    }

    .booking-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 25px 70px rgba(0, 0, 0, 0.2), 0 0 30px rgba(255, 215, 0, 0.3);
        border-color: #FFD700;
    }

    .booking-card-header {
        background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid #FFD700;
    }

    .booking-card-body {
        padding: 1.5rem;
    }

    .booking-card-footer {
        padding: 1rem 1.5rem;
        background: #2a2a2a;
        border-top: 1px solid #4a4a4a;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
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

    /* System Reschedule Indicator Styles */
    .system-reschedule-indicator {
        margin-left: 5px;
        font-size: 0.8rem;
        opacity: 0.9;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { opacity: 0.9; }
        50% { opacity: 0.6; }
        100% { opacity: 0.9; }
    }

    .booking-reference {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0;
    }

    .booking-info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .info-label {
        font-size: 0.8rem;
        color: #b0b0b0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .info-value {
        color: #FFD700;
        font-weight: 600;
    }

    .booking-actions {
        display: flex;
        gap: 0.5rem;
        justify-content: flex-end;
    }

    .booking-actions .btn {
        padding: 0.5rem 1rem;
        font-size: 0.8rem;
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
        font-weight: 700;
        color: #ffffff;
        font-size: 1.1rem;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6);
        letter-spacing: 0.3px;
    }

    .user-email {
        color: #e0e0e0;
        font-size: 0.9rem;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        font-weight: 500;
    }

    .instrument-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .instrument-name {
        font-weight: 600;
        color: #FFD700;
    }

    .instrument-type {
        color: #b0b0b0;
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
        color: #e0e0e0;
        font-weight: 500;
    }

    .duration {
        color: #b0b0b0;
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
        background: #2a2a2a;
        border-radius: 0 0 20px 20px;
    }

    .booking-info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .info-label {
        font-size: 0.8rem;
        color: #b0b0b0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    .info-value {
        color: #FFD700;
        font-weight: 600;
    }

    .instrument-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .instrument-name {
        font-weight: 600;
        color: #FFD700;
    }

    .instrument-type {
        color: #b0b0b0;
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
        color: #e0e0e0;
        font-weight: 500;
    }

    .duration {
        color: #b0b0b0;
    }

    .amount {
        font-weight: 700;
        font-size: 1.1rem;
        background: var(--gradient-success);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #b0b0b0;
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

    .action-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .action-buttons .btn {
        flex: 1;
        min-width: 80px;
        font-size: 0.8rem;
        padding: 0.5rem 1rem;
    }

    .btn-view {
        background: var(--gradient-secondary);
        color: #ffffff;
        border: 2px solid #FFD700;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
    }

    .btn-view:hover {
        background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
        color: #1a1a1a;
        border-color: #FFD700;
        transform: translateY(-2px);
    }

    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border: none;
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
        transform: translateY(-2px);
    }

    .btn-danger {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
        border: none;
    }

    .btn-danger:hover {
        background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
        transform: translateY(-2px);
    }

    .btn-reschedule {
        background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
        color: #1a1a1a;
        border: 2px solid #FFD700;
        font-weight: 600;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
    }

    .btn-reschedule:hover {
        background: linear-gradient(135deg, #FFA500 0%, #FF8C00 100%);
        color: #1a1a1a;
        border-color: #FFA500;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 215, 0, 0.4);
    }

    .reference-id {
        color: #1a1a1a;
        font-weight: 700;
        font-size: 1.2rem;
        text-shadow: 1px 1px 3px rgba(255, 255, 255, 0.8);
        letter-spacing: 0.5px;
    }

    .created-date {
        color: #1a1a1a;
        font-size: 0.9rem;
        font-weight: 500;
        text-shadow: 1px 1px 2px rgba(255, 255, 255, 0.6);
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

        .action-buttons {
            flex-direction: column;
        }

        .action-buttons .btn {
            width: 100%;
            margin-bottom: 0.25rem;
        }
    }
</style>

<div class="admin-content">
    <div class="page-header">
        <h1 class="page-title">🎸 Instrument Rental Bookings</h1>
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
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
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

    <!-- Rentals Cards -->
    <div class="rentals-table-card">
        <div class="table-header">
            <h3 class="table-title">🎸 Instrument Rental Records</h3>
            <div class="records-count">{{ $instrumentRentals->total() }} records found</div>
        </div>

        @if($instrumentRentals->count() > 0)
            <div class="bookings-grid">
                @foreach($instrumentRentals as $rental)
                    <div class="booking-card">
                        <div class="booking-card-header">
                            <div class="booking-reference">
                                <div class="reference-id">{{ $rental->reference }}</div>
                                <div class="created-date">{{ $rental->created_at->format('M d, Y') }}</div>
                            </div>
                            <span class="status-badge status-{{ $rental->status }}">
                                @if($rental->status == 'pending')
                                    ⏳ Pending
                                @elseif($rental->status == 'active')
                                    ✅ Active
                                    @if($rental->reschedule_source === 'system')
                                        <span class="system-reschedule-indicator" title="Rescheduled by Admin">⚙️</span>
                                    @endif
                                @elseif($rental->status == 'returned')
                                    📦 Returned
                                @elseif($rental->status == 'cancelled')
                                    ❌ Cancelled
                                @elseif($rental->status == 'rejected')
                                    ❌ Rejected
                                @else
                                    {{ ucfirst($rental->status) }}
                                @endif
                            </span>
                        </div>
                        
                        <div class="booking-card-body">
                            <div class="booking-info">
                                <div class="info-item">
                                    <div class="info-label">Customer</div>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            {{ strtoupper(substr($rental->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="user-details">
                                            <div class="user-name">{{ $rental->user->name ?? 'Unknown' }}</div>
                                            <div class="user-email">{{ $rental->user->email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">Instrument</div>
                                    <div class="instrument-info">
                                        <div class="instrument-name">{{ $rental->instrument_name }}</div>
                                        <div class="instrument-type">{{ $rental->instrument_type }}</div>
                                    </div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">Rental Period</div>
                                    <div class="rental-dates">
                                        <div class="date-range">
                                            {{ \Carbon\Carbon::parse($rental->rental_start_date)->format('M d, Y') }} - 
                                            {{ \Carbon\Carbon::parse($rental->rental_end_date)->format('M d, Y') }}
                                        </div>
                                        <div class="duration">{{ $rental->rental_duration_days }} day(s)</div>
                                    </div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">Down Payment</div>
                                    <div class="amount">₱{{ number_format($rental->reservation_fee ?? 0, 2) }}</div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label">Total Amount</div>
                                    <div class="amount">₱{{ number_format($rental->total_amount, 2) }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="booking-card-footer">
                            <div class="booking-actions">
                                <a href="{{ route('admin.instrument-rentals.show', $rental->id) }}" class="btn btn-view">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                @if($rental->status == 'pending')
                                    <form method="POST" action="{{ route('admin.rental.approve', $rental->id) }}" style="display: inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this rental?')">
                                            <i class="fas fa-check"></i> Accept
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.rental.reject', $rental->id) }}" style="display: inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to reject this rental?')">
                                            <i class="fas fa-times"></i> Decline
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
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