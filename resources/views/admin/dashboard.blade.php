@extends('layouts.admin')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <h2 class="page-title">📊 Analytics Dashboard</h2>
        <div class="header-actions">
            <span class="welcome-text">Sales and booking analytics for {{ Auth::user()->name ?? 'Admin' }}</span>
        </div>
    </div>

    <!-- Navigation Section -->
    <div class="dashboard-navigation">
        <h3>Quick Navigation</h3>
        <div class="nav-buttons">
            <a href="{{ route('admin.bookings') }}" class="nav-btn">
                <i class="fas fa-calendar-check"></i>
                <span>Bookings</span>
            </a>
            <a href="{{ route('admin.calendar') }}" class="nav-btn">
                <i class="fas fa-calendar-alt"></i>
                <span>Calendar</span>
            </a>
            <a href="{{ route('admin.users') }}" class="nav-btn">
                <i class="fas fa-users"></i>
                <span>Admin Users</span>
            </a>
            <a href="{{ route('admin.analytics') }}" class="nav-btn">
                <i class="fas fa-chart-bar"></i>
                <span>Reports</span>
            </a>
            <a href="{{ route('admin.database') }}" class="nav-btn">
                <i class="fas fa-database"></i>
                <span>Database Management</span>
            </a>
        </div>
    </div>

    <!-- Key Metrics -->    
    <div class="dashboard-stats">
        <div class="dashboard-stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-number">₱{{ number_format($totalRevenue ?? 0, 2) }}</div>
            <div class="stat-label">Total Revenue</div>
        </div>
        <div class="dashboard-stat-card">
            <div class="stat-icon">📈</div>
            <div class="stat-number">₱{{ number_format($thisMonthRevenue ?? 0, 2) }}</div>
            <div class="stat-label">This Month</div>
            @if(isset($lastMonthRevenue) && $lastMonthRevenue > 0)
                @php
                    $growth = (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
                @endphp
                <div class="stat-growth {{ $growth >= 0 ? 'positive' : 'negative' }}">
                    {{ $growth >= 0 ? '+' : '' }}{{ number_format($growth, 1) }}% vs last month
                </div>
            @endif
        </div>
        <div class="dashboard-stat-card">
            <div class="stat-icon">🎯</div>
            <div class="stat-number">₱{{ number_format($averageBookingValue ?? 0, 2) }}</div>
            <div class="stat-label">Average Booking Value</div>
        </div>
        <div class="dashboard-stat-card">
            <div class="stat-icon">📅</div>
            <div class="stat-number">{{ isset($bookingCounts) ? array_sum($bookingCounts) : 0 }}</div>
            <div class="stat-label">Total Bookings (12 months)</div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-section">
        <div class="chart-container">
            <div class="chart-header">
                <h2>📊 Monthly Sales Revenue</h2>
                <p>Revenue trends over the last 12 months</p>
            </div>
            <div class="chart-wrapper">
                <canvas id="salesChart" width="400" height="200"></canvas>
            </div>
        </div>

        <div class="chart-container">
            <div class="chart-header">
                <h2>📈 Monthly Booking Count</h2>
                <p>Number of confirmed bookings per month</p>
            </div>
            <div class="chart-wrapper">
                <canvas id="bookingsChart" width="400" height="200"></canvas>
            </div>
        </div>
        
        <div class="chart-container">
            <div class="chart-header">
                <h2>👥 Users per Service</h2>
                <p>Number of users utilizing each service type</p>
            </div>
            <div class="chart-wrapper">
                <canvas id="usersPerServiceChart" width="400" height="200"></canvas>
            </div>
        </div>
        
        <div class="chart-container">
            <div class="chart-header">
                <h2>🍋 Lemon Hub Studio Services Distribution</h2>
                <p>Breakdown of services offered by percentage</p>
            </div>
            <div class="chart-wrapper">
                <canvas id="servicesDistributionChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Customers -->
    <div class="customers-section">
        <h2>🏆 Top Customers</h2>
        <div class="customers-table">
            <table>
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Total Spent</th>
                        <th>Bookings</th>
                        <th>Avg. per Booking</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topCustomers ?? [] as $index => $customer)
                    <tr>
                        <td>
                            <span class="rank-badge rank-{{ $index + 1 }}">
                                @if($index === 0) 🥇
                                @elseif($index === 1) 🥈
                                @elseif($index === 2) 🥉
                                @else {{ $index + 1 }}
                                @endif
                            </span>
                        </td>
                        <td><strong>{{ $customer->name }}</strong></td>
                        <td>{{ $customer->email }}</td>
                        <td><strong>₱{{ number_format($customer->total_spent, 2) }}</strong></td>
                        <td>{{ $customer->booking_count }}</td>
                        <td>₱{{ number_format($customer->total_spent / $customer->booking_count, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="no-data">No customer data available yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.admin-content {
    max-width: 1400px;
    margin: 0 auto;
    padding: 10px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #ffffff;
    min-height: 100vh;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding: 15px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    border: none;
}

.page-title {
    margin: 0;
    font-size: 2.5em;
    font-weight: 700;
    letter-spacing: -0.5px;
    color: white;
}

.welcome-text {
    opacity: 0.9;
    font-size: 1.1em;
    font-weight: 400;
    color: white;
}

.dashboard-navigation {
    background: #ffffff;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 15px;
    border: none;
}

.dashboard-navigation h3 {
    margin: 0 0 10px 0;
    color: #333;
    font-size: 1.3em;
    font-weight: 600;
    text-align: center;
    border-bottom: 1px solid #eee;
    padding-bottom: 8px;
}

.nav-buttons {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 10px;
}

.nav-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    border: none;
}

.nav-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    color: white;
    text-decoration: none;
}

.nav-btn i {
    font-size: 2em;
    margin-bottom: 10px;
    opacity: 0.9;
}

.nav-btn span {
    font-size: 1em;
    font-weight: 500;
    text-align: center;
}

.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 10px;
    margin-bottom: 20px;
}

.dashboard-stat-card {
    background: #ffffff;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border: none;
    display: flex;
    flex-direction: column;
    gap: 4px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
    overflow: hidden;
}

.dashboard-stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.stat-icon {
    font-size: 3em;
    opacity: 0.8;
    margin-bottom: 8px;
    align-self: flex-start;
}

.stat-number {
    margin: 0;
    font-size: 2em;
    font-weight: 700;
    color: #333;
    line-height: 1.1;
}

.stat-label {
    margin: 0;
    color: #666;
    font-size: 1.1em;
    font-weight: 500;
    text-transform: none;
    letter-spacing: 0;
}

.stat-growth {
    font-size: 0.85em;
    font-weight: 600;
    margin-top: 4px;
}

.stat-growth.positive {
    color: #10b981;
}

.stat-growth.negative {
    color: #ef4444;
}

.charts-section {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    grid-template-rows: repeat(2, 1fr);
    gap: 15px;
    margin-bottom: 20px;
    align-items: stretch;
    justify-items: stretch;
}

@media (max-width: 1200px) {
    .charts-section {
        grid-template-columns: 1fr;
        grid-template-rows: auto;
    }
}

@media (max-width: 768px) {
    .admin-content {
        padding: 8px;
    }
    
    .page-header {
        margin-bottom: 10px;
        padding: 10px;
    }
    
    .page-title {
        font-size: 2em;
    }
    
    .dashboard-stats {
        grid-template-columns: 1fr;
        gap: 8px;
        margin-bottom: 15px;
    }
    
    .dashboard-stat-card {
        padding: 12px;
    }
    
    .charts-section {
        gap: 8px;
        margin-bottom: 15px;
    }
    
    .chart-container {
        padding: 12px;
    }
    
    .chart-wrapper {
        height: 200px;
    }
    
    .customers-section {
        padding: 12px;
        margin-bottom: 10px;
    }
    
    .customers-table th,
    .customers-table td {
        padding: 6px;
        font-size: 0.9em;
    }
}

.chart-container {
    background: #ffffff;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border: none;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.chart-container:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.chart-header {
    margin-bottom: 10px;
    text-align: center;
    border-bottom: 1px solid #eee;
    padding-bottom: 8px;
}

.chart-header h2 {
    margin: 0 0 8px 0;
    color: #333;
    font-size: 1.5em;
    font-weight: 600;
}

.chart-header p {
    margin: 0;
    color: #666;
    font-size: 1em;
    font-weight: 400;
}

.chart-wrapper {
    position: relative;
    height: 220px;
    padding: 5px 0;
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
}

.customers-section {
    background: #ffffff;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border: none;
    margin-bottom: 15px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.customers-section:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.customers-section h2 {
    margin: 0 0 10px 0;
    color: #333;
    text-align: center;
    font-size: 1.5em;
    font-weight: 600;
    border-bottom: 1px solid #eee;
    padding-bottom: 8px;
}

.customers-table {
    overflow-x: auto;
    border-radius: 10px;
    overflow: hidden;
    border: none;
}

.customers-table table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 0;
}

.customers-table th,
.customers-table td {
    padding: 8px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.customers-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #333;
    font-size: 1em;
    text-transform: none;
    letter-spacing: 0;
}

.customers-table tr:hover {
    background: #f8f9fa;
    transition: background-color 0.3s ease;
}

.customers-table tbody tr:last-child td {
    border-bottom: none;
}

.rank-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 6px 10px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9em;
    min-width: 36px;
    box-shadow: none;
    border: 1px solid #e5e7eb;
}

.rank-1 { 
    background: #fef3c7; 
    color: #92400e; 
    border-color: #fbbf24;
}
.rank-2 { 
    background: #f3f4f6; 
    color: #374151; 
    border-color: #d1d5db;
}
.rank-3 { 
    background: #fed7aa; 
    color: #9a3412; 
    border-color: #fb923c;
}

.no-data {
    text-align: center;
    color: #9ca3af;
    font-style: italic;
    padding: 24px 16px;
    font-size: 0.9em;
}
</style>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartDefaults = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.1)' } },
            x: { grid: { color: 'rgba(0,0,0,0.1)' } }
        }
    };

    // Sales Chart
    new Chart(document.getElementById('salesChart'), {
        type: 'line',
        data: {
            labels: @json($months ?? []),
            datasets: [{
                label: 'Revenue (\u20B1)',
                data: @json($salesData ?? []),
                borderColor: '#667eea',
                backgroundColor: 'rgba(102,126,234,0.1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: Object.assign({}, chartDefaults, {
            scales: Object.assign({}, chartDefaults.scales, {
                y: Object.assign({}, chartDefaults.scales.y, {
                    ticks: { callback: function(value) { return '\u20B1' + value.toLocaleString(); } }
                })
            })
        })
    });

    // Bookings Chart
    new Chart(document.getElementById('bookingsChart'), {
        type: 'bar',
        data: {
            labels: @json($months ?? []),
            datasets: [{
                label: 'Bookings',
                data: @json($bookingCounts ?? []),
                backgroundColor: 'rgba(118,75,162,0.8)',
                borderColor: '#764ba2',
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: chartDefaults
    });

    // Users per Service Chart
    new Chart(document.getElementById('usersPerServiceChart'), {
        type: 'bar',
        data: {
            labels: @json(array_keys($usersPerService ?? [])),
            datasets: [{
                label: 'Users',
                data: @json(array_values($usersPerService ?? [])),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 205, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 205, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: Object.assign({}, chartDefaults, {
            onClick: function(event, elements) {
                if (elements.length > 0) {
                    const elementIndex = elements[0].index;
                    const label = this.data.labels[elementIndex];
                    const value = this.data.datasets[0].data[elementIndex];
                    
                    // Show detailed information
                    alert(`Service: ${label}\nUsers: ${value}\n\nClick OK to view user details for this service.`);
                    
                    // Optional: Navigate to detailed view
                    // window.location.href = `/admin/users?service=${encodeURIComponent(label)}`;
                }
            },
            onHover: function(event, elements) {
                event.native.target.style.cursor = elements.length > 0 ? 'pointer' : 'default';
            }
        })
     });

     // Services Distribution Pie Chart
     new Chart(document.getElementById('servicesDistributionChart'), {
         type: 'pie',
         data: {
             labels: @json(array_keys($servicesDistribution ?? [])),
             datasets: [{
                 label: 'Services',
                 data: @json(array_values($servicesDistribution ?? [])),
                 backgroundColor: [
                     'rgba(255, 99, 132, 0.8)',
                     'rgba(54, 162, 235, 0.8)',
                     'rgba(255, 205, 86, 0.8)',
                     'rgba(75, 192, 192, 0.8)',
                     'rgba(153, 102, 255, 0.8)',
                     'rgba(255, 159, 64, 0.8)'
                 ],
                 borderColor: [
                     'rgba(255, 99, 132, 1)',
                     'rgba(54, 162, 235, 1)',
                     'rgba(255, 205, 86, 1)',
                     'rgba(75, 192, 192, 1)',
                     'rgba(153, 102, 255, 1)',
                     'rgba(255, 159, 64, 1)'
                 ],
                 borderWidth: 2
             }]
         },
         options: {
             responsive: true,
             maintainAspectRatio: false,
             plugins: {
                 legend: {
                     display: true,
                     position: 'bottom'
                 },
                 tooltip: {
                     callbacks: {
                         label: function(context) {
                             const label = context.label || '';
                             const value = context.parsed;
                             const total = context.dataset.data.reduce((a, b) => a + b, 0);
                             const percentage = ((value / total) * 100).toFixed(1);
                             return label + ': ' + value + ' (' + percentage + '%)';
                         }
                     }
                 }
             },
             onClick: function(event, elements) {
                 if (elements.length > 0) {
                     const elementIndex = elements[0].index;
                     const label = this.data.labels[elementIndex];
                     const value = this.data.datasets[0].data[elementIndex];
                     
                     // Show detailed information in a modal or alert
                     alert(`Service: ${label}\nBookings: ${value}\n\nClick OK to view detailed analytics for this service.`);
                     
                     // Optional: Navigate to detailed view
                     // window.location.href = `/admin/analytics?service=${encodeURIComponent(label)}`;
                 }
             },
             onHover: function(event, elements) {
                 event.native.target.style.cursor = elements.length > 0 ? 'pointer' : 'default';
             }
         }
     });
 });
 </script>
 @endsection