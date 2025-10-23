@extends('layouts.admin')

@section('title', 'Reports')

@section('head')
<!-- Modern Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
@endsection

@section('content')
<div class="dashboard-container">
    <div class="page-header">
        <h1 class="page-title">📊 Reports</h1>
        <div class="header-actions">
            <div class="export-dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" id="exportDropdown">
                    📤 Export Report
                </button>
                <div class="dropdown-menu" id="exportDropdownMenu">
                    <a class="dropdown-item" href="{{ route('admin.analytics.export') }}?export=csv&start_date={{ $startDate->format('Y-m-d') }}&end_date={{ $endDate->format('Y-m-d') }}">
                        <i class="fas fa-file-csv"></i> Export as CSV
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.analytics.export') }}?export=excel&start_date={{ $startDate->format('Y-m-d') }}&end_date={{ $endDate->format('Y-m-d') }}">
                        <i class="fas fa-file-excel"></i> Export as Excel
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.analytics.export') }}?export=pdf&start_date={{ $startDate->format('Y-m-d') }}&end_date={{ $endDate->format('Y-m-d') }}">
                        <i class="fas fa-file-pdf"></i> Export as PDF
                    </a>
                </div>
            </div>
            <button class="btn btn-secondary" onclick="window.print()">🖨️ Print Report</button>
        </div>
    </div>

    <!-- Date Filter Section -->
    <div class="filter-section">
        <form method="GET" action="{{ route('admin.analytics') }}" class="date-filter-form">
            <div class="filter-group">
                <label for="start_date">From Date:</label>
                <input type="date" id="start_date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="form-control">
            </div>
            <div class="filter-group">
                <label for="end_date">To Date:</label>
                <input type="date" id="end_date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="form-control">
            </div>
            <div class="filter-group">
                <button type="submit" class="btn btn-primary">📅 Filter Reports</button>
                <a href="{{ route('admin.analytics') }}" class="btn btn-secondary">🔄 Reset</a>
            </div>
        </form>
        <div class="date-range-info">
            <span>Showing data from {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</span>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number">{{ $confirmedBookings->count() }}</div>
            <div class="stat-label">Confirmed Bookings</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $pendingBookings->count() }}</div>
            <div class="stat-label">Pending Bookings</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $activeRentals->count() }}</div>
            <div class="stat-label">Active Rentals</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">₱{{ number_format($totalRevenue, 2) }}</div>
            <div class="stat-label">Total Revenue</div>
        </div>
    </div>

    <!-- Recent Bookings Report -->
    <div class="report-section">
        <h2>📅 Recent Bookings</h2>
        <div class="table-responsive">
            <table id="bookingsTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Client</th>
                        <th>Time Slot</th>
                        <th>Status</th>
                        <th>Duration</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($confirmedBookings as $booking)
                    <tr>
                        <td>{{ $booking->date }}</td>
                        <td>{{ $booking->user->name ?? 'N/A' }}</td>
                        <td>{{ $booking->time_slot }}</td>
                        <td><span class="badge bg-success">{{ ucfirst($booking->status) }}</span></td>
                        <td>{{ $booking->duration ?? 'N/A' }} hours</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">No bookings found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Service Summary -->
    <div class="report-section">
        <h2>🎵 Service Summary</h2>
        <div class="table-responsive">
            <table id="servicesTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>Service Type</th>
                        <th>Count</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($serviceCategories as $service => $count)
                    <tr>
                        <td>{{ $service }}</td>
                        <td>{{ $count }}</td>
                        <td>₱{{ number_format($revenueByService[$service] ?? 0, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Top Customers -->
    <div class="report-section">
        <h2>👥 Top Customers</h2>
        <div class="table-responsive">
            <table id="customersTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Email</th>
                        <th>Bookings</th>
                        <th>Total Spent</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topCustomers as $customer)
                    <tr>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->booking_count }}</td>
                        <td>₱{{ number_format($customer->total_spent, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">No customer data available</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.dashboard-container {
    padding: 24px;
    background: linear-gradient(135deg, #0f0f0f 0%, #1a1a1a 50%, #0f0f0f 100%);
    min-height: 100vh;
    color: #ffffff;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
    padding: 32px;
    background: linear-gradient(135deg, rgba(45, 45, 45, 0.95) 0%, rgba(35, 35, 35, 0.95) 100%);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.4), 0 0 0 1px rgba(255,215,0,0.1);
    border: 1px solid rgba(255,215,0,0.2);
    position: relative;
    overflow: hidden;
}

.page-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, #ffd700, transparent);
}

.page-title {
    font-size: 2.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, #ffd700 0%, #ffed4e 50%, #ffd700 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin: 0;
    text-shadow: 0 0 30px rgba(255,215,0,0.3);
}

.header-actions {
    display: flex;
    gap: 16px;
    align-items: center;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 24px;
    margin-bottom: 40px;
}

.stat-card {
    background: linear-gradient(135deg, rgba(45, 45, 45, 0.9) 0%, rgba(35, 35, 35, 0.9) 100%);
    backdrop-filter: blur(15px);
    padding: 28px;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.3), 0 0 0 1px rgba(255,215,0,0.1);
    text-align: center;
    border: 1px solid rgba(255,215,0,0.15);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,215,0,0.1), transparent);
    transition: left 0.6s;
}

.stat-card:hover::before {
    left: 100%;
}

.stat-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 25px 50px rgba(0,0,0,0.4), 0 0 0 1px rgba(255,215,0,0.3);
    border-color: rgba(255,215,0,0.4);
}

.stat-number {
    font-size: 2.8rem;
    font-weight: 800;
    background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 8px;
    text-shadow: 0 0 20px rgba(255,215,0,0.3);
}

.stat-label {
    color: #e0e0e0;
    font-size: 0.95rem;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    opacity: 0.9;
}

.report-section {
    background: linear-gradient(135deg, rgba(45, 45, 45, 0.95) 0%, rgba(35, 35, 35, 0.95) 100%);
    backdrop-filter: blur(20px);
    padding: 32px;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.3), 0 0 0 1px rgba(255,215,0,0.1);
    margin-bottom: 32px;
    border: 1px solid rgba(255,215,0,0.15);
    position: relative;
    overflow: hidden;
}

.report-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, #ffd700, transparent);
}

.report-section h2 {
    font-size: 1.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid transparent;
    border-image: linear-gradient(90deg, transparent, #ffd700, transparent) 1;
    position: relative;
    text-shadow: 0 0 20px rgba(255,215,0,0.2);
}

.report-section h2::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, transparent, #ffd700, transparent);
}

.report-section .table {
    margin-bottom: 0;
    background: linear-gradient(135deg, rgba(26, 26, 26, 0.95) 0%, rgba(20, 20, 20, 0.95) 100%) !important;
    border-radius: 16px;
    overflow: hidden;
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    backdrop-filter: blur(10px);
    box-shadow: 0 8px 32px rgba(0,0,0,0.3);
}

.report-section .table th {
    background: linear-gradient(135deg, rgba(51, 51, 51, 0.95) 0%, rgba(45, 45, 45, 0.95) 100%) !important;
    font-weight: 700;
    font-size: 0.95rem;
    background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    border-bottom: 2px solid rgba(255,215,0,0.3) !important;
    border-top: none !important;
    border-left: none !important;
    border-right: none !important;
    padding: 16px !important;
    text-align: left;
    vertical-align: middle;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    position: relative;
}

.report-section .table th::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 2px;
    background: linear-gradient(90deg, transparent, #ffd700, transparent);
}

.report-section .table td {
    vertical-align: middle;
    color: #ffffff !important;
    border-bottom: 1px solid rgba(255,215,0,0.1) !important;
    border-top: none !important;
    border-left: none !important;
    border-right: none !important;
    padding: 16px !important;
    text-align: left;
    background: linear-gradient(135deg, rgba(45, 45, 45, 0.8) 0%, rgba(35, 35, 35, 0.8) 100%) !important;
    transition: all 0.3s ease;
    font-weight: 500;
}

.report-section .table tbody tr {
    background: linear-gradient(135deg, rgba(45, 45, 45, 0.8) 0%, rgba(35, 35, 35, 0.8) 100%) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.report-section .table-striped tbody tr:nth-child(odd) {
    background: linear-gradient(135deg, rgba(42, 42, 42, 0.9) 0%, rgba(32, 32, 32, 0.9) 100%) !important;
}

.report-section .table-striped tbody tr:nth-child(even) {
    background: linear-gradient(135deg, rgba(45, 45, 45, 0.8) 0%, rgba(35, 35, 35, 0.8) 100%) !important;
}

.report-section .table-striped tbody tr:hover {
    background: linear-gradient(135deg, rgba(58, 58, 58, 0.9) 0%, rgba(48, 48, 48, 0.9) 100%) !important;
    transform: translateX(4px);
    box-shadow: 0 4px 20px rgba(255,215,0,0.1);
}

.report-section .table tbody tr td {
    background: inherit !important;
}

.report-section .table-responsive {
    overflow-x: auto;
    border-radius: 16px;
    background: linear-gradient(135deg, rgba(26, 26, 26, 0.95) 0%, rgba(20, 20, 20, 0.95) 100%);
    backdrop-filter: blur(15px);
    border: 1px solid rgba(255,215,0,0.2);
    box-shadow: 0 12px 40px rgba(0,0,0,0.4), 0 0 0 1px rgba(255,215,0,0.1);
}

.report-section .table-responsive::-webkit-scrollbar {
    height: 8px;
}

.report-section .table-responsive::-webkit-scrollbar-track {
    background: #2a2a2a;
    border-radius: 4px;
}

.report-section .table-responsive::-webkit-scrollbar-thumb {
    background: #ffd700;
    border-radius: 4px;
}

.report-section .table-responsive::-webkit-scrollbar-thumb:hover {
    background: #ffed4e;
}

.btn {
    padding: 12px 24px;
    border-radius: 25px;
    border: none;
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.btn:hover::before {
    left: 100%;
}

.btn-primary {
    background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
    color: #1a1a1a;
    box-shadow: 0 4px 15px rgba(255,215,0,0.3);
}

.btn-primary:hover {
    background: linear-gradient(135deg, #ffed4e 0%, #fff176 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255,215,0,0.4);
}

.btn-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #8a9ba8 100%);
    color: #ffffff;
    box-shadow: 0 4px 15px rgba(108,117,125,0.3);
}

.btn-secondary:hover {
    background: linear-gradient(135deg, #8a9ba8 0%, #9db4c4 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(108,117,125,0.4);
}

.export-dropdown {
    position: relative;
    display: inline-block;
    margin-right: 16px;
}

.dropdown-toggle::after {
    content: "▼";
    margin-left: 10px;
    font-size: 11px;
    transition: transform 0.3s ease;
}

.dropdown-toggle:hover::after {
    transform: translateY(1px);
}

.dropdown-menu {
    position: absolute;
    top: calc(100% + 8px);
    left: 0;
    z-index: 1000;
    display: none;
    min-width: 200px;
    padding: 12px 0;
    margin: 0;
    background: linear-gradient(135deg, rgba(42, 42, 42, 0.95) 0%, rgba(35, 35, 35, 0.95) 100%);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255,215,0,0.2);
    border-radius: 16px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.4), 0 0 0 1px rgba(255,215,0,0.1);
    animation: dropdownFadeIn 0.3s ease;
}

@keyframes dropdownFadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.dropdown-menu.show {
    display: block;
}

.dropdown-item {
    display: flex;
    align-items: center;
    width: 100%;
    padding: 12px 20px;
    font-weight: 500;
    color: #ffffff;
    text-align: left;
    white-space: nowrap;
    background-color: transparent;
    border: 0;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.dropdown-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,215,0,0.1), transparent);
    transition: left 0.4s;
}

.dropdown-item:hover::before {
    left: 100%;
}

.dropdown-item:hover {
    background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
    color: #1a1a1a;
    transform: translateX(4px);
}

.dropdown-item i {
    margin-right: 12px;
    width: 18px;
    font-size: 14px;
}

.report-section .badge {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    white-space: nowrap;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.report-section .badge::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.report-section .badge:hover::before {
    left: 100%;
}

.report-section .bg-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
    color: white !important;
    border: 1px solid rgba(40, 167, 69, 0.3);
}

.report-section .bg-success:hover {
    background: linear-gradient(135deg, #218838 0%, #1abc9c 100%) !important;
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
}

.report-section .text-center {
    text-align: center !important;
    color: #e0e0e0 !important;
    font-style: italic;
    font-weight: 500;
    opacity: 0.8;
}

/* DataTables Dark Theme Styling */
.dataTables_wrapper {
    color: #ffffff;
}

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
.dataTables_wrapper .dataTables_paginate {
    color: #ffffff;
}

.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_filter input {
    background-color: #2d2d2d;
    border: 1px solid #444;
    color: #ffffff;
    border-radius: 4px;
    padding: 5px 8px;
}

.dataTables_wrapper .dataTables_length select:focus,
.dataTables_wrapper .dataTables_filter input:focus {
    border-color: #ffd700;
    outline: none;
    box-shadow: 0 0 0 2px rgba(255, 215, 0, 0.2);
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    background: #2d2d2d;
    border: 1px solid #444;
    color: #ffffff !important;
    margin: 0 2px;
    border-radius: 4px;
    padding: 6px 12px;
    text-decoration: none;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #ffd700 !important;
    color: #1a1a1a !important;
    border-color: #ffd700;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: #ffd700 !important;
    color: #1a1a1a !important;
    border-color: #ffd700;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
    background: #1a1a1a;
    color: #666 !important;
    border-color: #333;
    cursor: not-allowed;
}

.dt-buttons {
    margin-bottom: 20px;
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items: center;
}

.dt-buttons .btn {
    border-radius: 12px;
    font-size: 13px;
    font-weight: 600;
    padding: 10px 18px;
    border: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    position: relative;
    overflow: hidden;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.dt-buttons .btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.dt-buttons .btn:hover::before {
    left: 100%;
}

.dt-buttons .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
}

.dt-buttons .btn-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
}

.dt-buttons .btn-success:hover {
    background: linear-gradient(135deg, #218838 0%, #1abc9c 100%);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
}

.dt-buttons .btn-danger {
    background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%);
    color: white;
}

.dt-buttons .btn-danger:hover {
    background: linear-gradient(135deg, #c82333 0%, #c0392b 100%);
    box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
}

.dt-buttons .btn-info {
    background: linear-gradient(135deg, #17a2b8 0%, #3498db 100%);
    color: white;
}

.dt-buttons .btn-info:hover {
    background: linear-gradient(135deg, #138496 0%, #2980b9 100%);
    box-shadow: 0 8px 25px rgba(23, 162, 184, 0.4);
}

.dt-buttons .btn-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #95a5a6 100%);
    color: white;
}

.dt-buttons .btn-secondary:hover {
    background: linear-gradient(135deg, #5a6268 0%, #7f8c8d 100%);
    box-shadow: 0 8px 25px rgba(108, 117, 125, 0.4);
}

/* Modern Export Button Container */
.export-button-container {
    background: rgba(45, 45, 45, 0.8);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 215, 0, 0.2);
    border-radius: 16px;
    padding: 16px;
    margin-bottom: 20px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.export-button-container::before {
    content: '📊 Export Options';
    display: block;
    color: #ffd700;
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Enhanced Responsive Design */
@media (max-width: 1200px) {
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
    }
    
    .page-header {
        padding: 24px;
    }
    
    .page-title {
        font-size: 2.2rem;
    }
}

@media (max-width: 768px) {
    .dashboard-container {
        padding: 16px;
    }
    
    .page-header {
        flex-direction: column;
        gap: 20px;
        padding: 20px;
        text-align: center;
    }
    
    .page-title {
        font-size: 1.8rem;
    }
    
    .header-actions {
        flex-wrap: wrap;
        justify-content: center;
        gap: 12px;
    }
    
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    
    .stat-card {
        padding: 20px;
    }
    
    .stat-number {
        font-size: 2.2rem;
    }
    
    .report-section {
        padding: 20px;
        margin-bottom: 24px;
    }
    
    .report-section h2 {
        font-size: 1.3rem;
        margin-bottom: 20px;
    }
    
    .dt-buttons {
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .dt-buttons .btn {
        font-size: 11px;
        padding: 8px 14px;
        margin: 2px;
    }
    
    .export-button-container {
        padding: 12px;
        text-align: center;
    }
    
    .dropdown-menu {
        position: fixed;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        min-width: 250px;
        max-width: 90vw;
    }
}

@media (max-width: 480px) {
    .page-title {
        font-size: 1.5rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .stat-number {
        font-size: 2rem;
    }
    
    .report-section .table th,
    .report-section .table td {
        padding: 12px 8px !important;
        font-size: 0.85rem;
    }
    
    .btn {
        padding: 10px 16px;
        font-size: 0.8rem;
    }
}

/* DataTables responsive styling */
.dtr-details {
    background-color: #2d2d2d !important;
    border: 1px solid #444 !important;
}

.dtr-details li {
    border-bottom: 1px solid #444;
    padding: 8px 0;
}

.dtr-details li:last-child {
    border-bottom: none;
}

.dtr-title {
    color: #ffd700 !important;
    font-weight: bold;
}

/* Custom scrollbar for DataTables */
.dataTables_scrollBody::-webkit-scrollbar {
    height: 8px;
    width: 8px;
}

.dataTables_scrollBody::-webkit-scrollbar-track {
    background: #2a2a2a;
    border-radius: 4px;
}

.dataTables_scrollBody::-webkit-scrollbar-thumb {
    background: #ffd700;
    border-radius: 4px;
}

.dataTables_scrollBody::-webkit-scrollbar-thumb:hover {
    background: #ffed4e;
}

.filter-section {
    background: linear-gradient(135deg, rgba(45, 45, 45, 0.95) 0%, rgba(35, 35, 35, 0.95) 100%);
    backdrop-filter: blur(20px);
    padding: 32px;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.3), 0 0 0 1px rgba(255,215,0,0.1);
    margin-bottom: 32px;
    border: 1px solid rgba(255,215,0,0.15);
    position: relative;
    overflow: hidden;
}

.filter-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, #ffd700, transparent);
}

.date-filter-form {
    display: flex;
    align-items: end;
    gap: 24px;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.filter-group label {
    font-weight: 600;
    color: #ffd700;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.filter-group input[type="date"] {
    padding: 12px 16px;
    border: 1px solid rgba(255,215,0,0.3);
    border-radius: 12px;
    font-size: 14px;
    min-width: 180px;
    background: linear-gradient(135deg, rgba(26, 26, 26, 0.95) 0%, rgba(20, 20, 20, 0.95) 100%);
    color: #ffffff;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    backdrop-filter: blur(10px);
}

.filter-group input[type="date"]:focus {
    border-color: #ffd700;
    outline: none;
    box-shadow: 0 0 0 3px rgba(255,215,0,0.2);
    transform: translateY(-2px);
}

.filter-group input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(1) sepia(1) saturate(5) hue-rotate(30deg);
    cursor: pointer;
}

.date-range-info {
    margin-top: 16px;
    padding: 16px 20px;
    background: linear-gradient(135deg, rgba(255,215,0,0.1) 0%, rgba(255,237,78,0.1) 100%);
    border-radius: 12px;
    font-size: 14px;
    color: #e0e0e0;
    border: 1px solid rgba(255,215,0,0.2);
    font-weight: 500;
    text-align: center;
}
</style>

<!-- DataTables JavaScript -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Common DataTable configuration
    const commonConfig = {
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"<"export-button-container"B>>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm',
                exportOptions: {
                    columns: ':visible'
                },
                customize: function(doc) {
                    doc.defaultStyle.fontSize = 10;
                    doc.styles.tableHeader.fontSize = 12;
                    doc.styles.tableHeader.fillColor = '#ffd700';
                    doc.styles.tableHeader.color = '#1a1a1a';
                }
            },
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv"></i> CSV',
                className: 'btn btn-info btn-sm',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print"></i> Print',
                className: 'btn btn-secondary btn-sm',
                exportOptions: {
                    columns: ':visible'
                }
            }
        ],
        language: {
            search: "Search:",
            lengthMenu: "Show _MENU_ entries",
            info: "Showing _START_ to _END_ of _TOTAL_ entries",
            infoEmpty: "Showing 0 to 0 of 0 entries",
            infoFiltered: "(filtered from _MAX_ total entries)",
            paginate: {
            first: "First",
            last: "Last",
            next: "→",
            previous: "←"
        }
        }
    };

    // Initialize DataTables for each table
    $('#bookingsTable').DataTable({
        ...commonConfig,
        order: [[0, 'desc']], // Sort by date descending
        columnDefs: [
            { targets: [3], orderable: false } // Status column not sortable
        ]
    });

    $('#servicesTable').DataTable({
        ...commonConfig,
        order: [[1, 'desc']], // Sort by count descending
        columnDefs: [
            { targets: [1, 2], className: 'text-center' } // Center align count and revenue
        ]
    });

    $('#customersTable').DataTable({
        ...commonConfig,
        order: [[3, 'desc']], // Sort by total spent descending
        columnDefs: [
            { targets: [2, 3], className: 'text-center' } // Center align bookings and total spent
        ]
    });

    // Handle original dropdown toggle
    const dropdownToggle = document.getElementById('exportDropdown');
    const dropdownMenu = document.getElementById('exportDropdownMenu');
    
    if (dropdownToggle && dropdownMenu) {
        dropdownToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropdownMenu.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });
        
        // Close dropdown when clicking on dropdown items
        const dropdownItems = dropdownMenu.querySelectorAll('.dropdown-item');
        dropdownItems.forEach(item => {
            item.addEventListener('click', function() {
                dropdownMenu.classList.remove('show');
            });
        });
    }
});
</script>

@endsection