@extends('layouts.admin')

@section('title', 'Studio Rental Bookings')

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
        padding: 0.75rem 1rem;
        border: 2px solid #3a3a3a;
        border-radius: 10px;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        background: #1a1a1a;
        color: #e0e0e0;
    }

    .form-control:focus {
        outline: none;
        border-color: #FFD700;
        box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.1);
        background: #2a2a2a;
    }

    .filter-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }

    .bookings-table-card {
        background: #2a2a2a;
        border-radius: 20px;
        box-shadow: var(--shadow-soft);
        overflow: hidden;
        margin-bottom: 2rem;
        border: 1px solid #3a3a3a;
    }

    .table-header {
        background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
        color: #1a1a1a;
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
        font-weight: 700;
        padding: 1.2rem;
        border: none;
        text-transform: uppercase;
        font-size: 0.9rem;
        letter-spacing: 1px;
        position: sticky;
        top: 0;
        z-index: 10;
        text-shadow: 0 0 10px rgba(255, 215, 0, 0.4);
        border-bottom: 2px solid #FFD700;
    }

    .table tbody td {
        padding: 1.2rem;
        border-bottom: 1px solid #3a3a3a;
        vertical-align: middle;
        background: #2a2a2a;
        color: #f0f0f0;
        font-size: 0.95rem;
        font-weight: 500;
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

    .booking-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .service-type {
        font-weight: 700;
        color: #ffffff;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6);
        font-size: 1.05rem;
        letter-spacing: 0.3px;
    }

    .duration {
        color: #d0d0d0;
        font-size: 0.85rem;
        text-transform: capitalize;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
    }

    .booking-dates {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        font-size: 0.85rem;
    }

    .date-time {
        color: #ffffff;
        font-weight: 600;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6);
        font-size: 0.95rem;
    }

    .time-slot {
        color: #d0d0d0;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
    }

    .pagination-wrapper {
        display: flex;
        justify-content: center;
        padding: 2rem;
        background: #2a2a2a;
        border-radius: 0 0 20px 20px;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #b0b0b0;
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

    .booking-details {
        margin-top: 1rem;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid #4a4a4a;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 700;
        color: #FFD700;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        text-shadow: 0 0 8px rgba(255, 215, 0, 0.3);
    }

    .detail-value {
        color: #ffffff;
        font-weight: 600;
        text-align: right;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        font-size: 1rem;
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
        color: #888;
    }

    .created-date {
        color: #b0b0b0;
        font-size: 0.85rem;
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

        .page-header {
            padding: 1.5rem;
            background: var(--gradient-primary);
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

        .bookings-grid {
            grid-template-columns: 1fr;
            padding: 1rem;
            gap: 1rem;
        }

        .booking-card-header {
            padding: 0.75rem 1rem;
            flex-direction: column;
            gap: 0.5rem;
            align-items: flex-start;
        }

        .booking-card-body {
            padding: 1rem;
        }

        .booking-card-footer {
            padding: 0.75rem 1rem;
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

        .detail-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.25rem;
        }

        .detail-value {
            text-align: left;
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
        
        <div class="bookings-grid">
            @if($bookings->count() > 0)
                @foreach($bookings as $booking)
                    <div class="booking-card">
                        <div class="booking-card-header">
                            <div class="reference-id">
                                <strong>#{{ str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</strong>
                            </div>
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
                        </div>
                        
                        <div class="booking-card-body">
                            <div class="user-info">
                                <div class="user-avatar">
                                    {{ strtoupper(substr($booking->user->name, 0, 1)) }}
                                </div>
                                <div class="user-details">
                                    <div class="user-name">{{ $booking->user->name }}</div>
                                    <div class="user-email">{{ $booking->user->email }}</div>
                                </div>
                            </div>
                            
                            <div class="booking-details">
                                <div class="detail-row">
                                    <span class="detail-label">Service:</span>
                                    <span class="detail-value">Studio Rental</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Duration:</span>
                                    <span class="detail-value">{{ $booking->duration }} hours</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Date:</span>
                                    <span class="detail-value">{{ \Carbon\Carbon::parse($booking->date)->format('M d, Y') }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Time:</span>
                                    <span class="detail-value">{{ $booking->time_slot }}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Created:</span>
                                    <span class="detail-value">{{ $booking->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="booking-card-footer">
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
                        </div>
                    </div>
                @endforeach
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