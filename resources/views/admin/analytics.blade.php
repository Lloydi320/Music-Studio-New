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
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 2px solid #e9ecef;
}

.page-title {
    font-size: 2rem;
    font-weight: 600;
    color: #333;
    margin: 0;
}

.header-actions {
    display: flex;
    gap: 10px;
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

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    color: #ffc107;
    margin-bottom: 5px;
}

.stat-label {
    color: #666;
    font-size: 0.9rem;
}

.report-section {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.report-section h2 {
    font-size: 1.3rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e9ecef;
}

.table {
    margin-bottom: 0;
}

.table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
}

.table td {
    vertical-align: middle;
}

.btn {
    padding: 8px 16px;
    border-radius: 4px;
    border: none;
    font-weight: 500;
    text-decoration: none;
    display: inline-block;
    cursor: pointer;
}

.btn-primary {
    background: #ffc107;
    color: #000;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn:hover {
    opacity: 0.9;
}

.badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}

.bg-success {
    background: #28a745;
    color: white;
}
</style>
@endsection