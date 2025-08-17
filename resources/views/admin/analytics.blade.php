@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
<div class="dashboard-container">
    <div class="page-header">
        <h1 class="page-title">📊 Reports</h1>
        <div class="header-actions">
            <button class="btn btn-primary" onclick="window.location.href='{{ route('admin.analytics') }}?export=csv'">📤 Export CSV</button>
            <button class="btn btn-secondary" onclick="window.print()">🖨️ Print Report</button>
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
            <table class="table table-striped">
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
                    @forelse($confirmedBookings->take(10) as $booking)
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
            <table class="table table-striped">
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
            <table class="table table-striped">
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
    padding: 20px;
    background: #1a1a1a;
    min-height: 100vh;
    color: #ffffff;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding: 20px;
    background: #2d2d2d;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.3);
    border: 1px solid #444;
}

.page-title {
    font-size: 2rem;
    font-weight: 600;
    color: #ffd700;
    margin: 0;
}

.header-actions {
    display: flex;
    gap: 10px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: #2d2d2d;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.3);
    text-align: center;
    border: 1px solid #444;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(255,215,0,0.2);
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #ffd700;
    margin-bottom: 5px;
}

.stat-label {
    color: #cccccc;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.report-section {
    background: #2d2d2d;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.3);
    margin-bottom: 30px;
    border: 1px solid #444;
}

.report-section h2 {
    font-size: 1.3rem;
    font-weight: 600;
    color: #ffd700;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #ffd700;
}

.table {
    margin-bottom: 0;
    background: #1a1a1a;
    border-radius: 8px;
    overflow: hidden;
}

.table th {
    background: #333333;
    font-weight: 600;
    color: #ffd700;
    border-bottom: 2px solid #444;
    padding: 12px;
    text-align: left;
}

.table td {
    vertical-align: middle;
    color: #ffffff;
    border-bottom: 1px solid #444;
    padding: 12px;
    text-align: left;
    background: transparent;
}

.table tbody tr {
    background: #2d2d2d;
}

.table-striped tbody tr:nth-child(odd) {
    background: #2a2a2a;
}

.table-striped tbody tr:hover {
    background: #3a3a3a;
}

.table tbody tr td {
    background: inherit;
}

.table-responsive {
    overflow-x: auto;
    border-radius: 8px;
}

.btn {
    padding: 10px 20px;
    border-radius: 20px;
    border: none;
    font-weight: 500;
    text-decoration: none;
    display: inline-block;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-primary {
    background: #ffd700;
    color: #1a1a1a;
}

.btn-primary:hover {
    background: #ffed4e;
    box-shadow: 0 4px 16px rgba(255,215,0,0.4);
}

.btn-secondary {
    background: #555;
    color: #ffffff;
}

.btn-secondary:hover {
    background: #666;
    box-shadow: 0 4px 16px rgba(85,85,85,0.4);
}

.badge {
    padding: 6px 12px;
    border-radius: 16px;
    font-size: 12px;
    font-weight: bold;
}

.bg-success {
    background: #34a853;
    color: white;
}
</style>
@endsection