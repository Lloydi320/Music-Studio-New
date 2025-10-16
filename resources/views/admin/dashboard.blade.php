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

    

    <!-- Key Metrics -->    
    <div class="dashboard-stats">
        <div class="dashboard-stat-card total-revenue-card">
            <div class="stat-icon">💰</div>
            <div class="stat-number">₱{{ number_format($totalRevenue ?? 0, 2) }}</div>
            <div class="stat-label">Total Revenue</div>
        </div>
        <div class="dashboard-stat-card gold-stat-card">
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
        <div class="dashboard-stat-card gold-stat-card">
            <div class="stat-icon">🎯</div>
            <div class="stat-number">₱{{ number_format($averageBookingValue ?? 0, 2) }}</div>
            <div class="stat-label">Average Booking Value</div>
        </div>
        <div class="dashboard-stat-card gold-stat-card">
            <div class="stat-icon">📅</div>
            <div class="stat-number">{{ isset($bookingCounts) ? array_sum($bookingCounts) : 0 }}</div>
            <div class="stat-label">Total Bookings (12 months)</div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-section">
        <div class="section-header">
            <h2>📊 Analytics Charts</h2>
            <button class="toggle-btn" onclick="toggleSection('charts')">
                <span id="charts-icon">−</span>
            </button>
        </div>
        <div class="charts-content" id="charts-content">
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
    </div>

    <!-- Top Customers -->
    <div class="customers-section">
        <div class="section-header">
            <h2>🏆 Top Customers</h2>
            <button class="toggle-btn" onclick="toggleSection('customers')">
                <span id="customers-icon">−</span>
            </button>
        </div>
        <div class="customers-table" id="customers-content">
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
                        <td>₱{{ $customer->booking_count > 0 ? number_format($customer->total_spent / $customer->booking_count, 2) : '0.00' }}</td>
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
    max-width: 100%;
    margin: 0;
    padding: 10px 15px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #1a1a1a;
    min-height: 100vh;
    color: #e0e0e0;
    overflow-x: hidden;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding: 12px 15px;
    background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%);
    color: #e2e8f0;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(74, 85, 104, 0.3);
    border: none;
    position: relative;
}

.page-title {
    margin: 0;
    font-size: 1.6em;
    font-weight: 700;
    letter-spacing: -0.5px;
    color: #e2e8f0;
}

.welcome-text {
    opacity: 0.9;
    font-size: 1.1em;
    font-weight: 400;
    color: #e2e8f0;
}

.dashboard-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 10px;
    margin-bottom: 15px;
    background-color: #2a2a2a;
}

.dashboard-stat-card {
    background: #2a2a2a;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    border: 1px solid #3a3a3a;
    display: flex;
    flex-direction: column;
    gap: 4px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
    overflow: hidden;
    text-align: center;
}

.dashboard-stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(255, 215, 0, 0.2);
    border-color: #81c784;
    
}

.total-revenue-card {
    background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%) !important;
    border: 2px solid #FFD700 !important;
    box-shadow: 0 4px 20px rgba(255, 215, 0, 0.3) !important;
}

.total-revenue-card:hover {
    transform: translateY(-8px) !important;
    box-shadow: 0 12px 40px rgba(255, 215, 0, 0.5) !important;
    border-color: #FFED4E !important;
}

.total-revenue-card .stat-icon {
    color: #1a1a1a !important;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
}

.total-revenue-card .stat-number {
    color: #1a1a1a !important;
    font-weight: 800 !important;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
}

.total-revenue-card .stat-label {
    color: #2a2a2a !important;
    font-weight: 600 !important;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
}

.gold-stat-card {
    background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%) !important;
    border: 2px solid #FFD700 !important;
    box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3) !important;
    transition: all 0.3s ease !important;
}

.gold-stat-card:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 6px 20px rgba(255, 215, 0, 0.4) !important;
    border-color: #FFC700 !important;
}

.gold-stat-card .stat-icon {
    color: #1a1a1a !important;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
}

.gold-stat-card .stat-number {
    color: #1a1a1a !important;
    font-weight: 800 !important;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
}

.gold-stat-card .stat-label {
    color: #2a2a2a !important;
    font-weight: 600 !important;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
}

.gold-stat-card .stat-growth {
    color: #1a1a1a !important;
    font-weight: 700 !important;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
}

.stat-icon {
    font-size: 3em;
    opacity: 0.8;
    margin-bottom: 8px;
    align-self: flex-start;
    color: #81c784;
}

.stat-number {
    margin: 0;
    font-size: 2em;
    font-weight: 700;
    color: #e0e0e0;
    line-height: 1.1;
}

.stat-label {
    margin: 0;
    color: #b0b0b0;
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
    background: #2a2a2a;
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 15px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    border: 1px solid #3a3a3a;
}

.charts-content {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    grid-template-rows: repeat(2, 1fr);
    gap: 15px;
    margin-top: 15px;
    align-items: stretch;
    justify-items: stretch;
}

@media (max-width: 1200px) {
    .charts-content {
        grid-template-columns: 1fr;
        grid-template-rows: auto;
    }
}

@media (max-width: 768px) {
    .admin-content {
        padding: 5px 10px;
    }
    
    .page-header {
        flex-direction: column;
        gap: 10px;
        margin-bottom: 10px;
        padding: 10px;
    }
    
    .page-title {
        font-size: 1.4em;
    }
    
    .dashboard-stats {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 8px;
        margin-bottom: 15px;
    }
    
    .dashboard-stat-card {
        padding: 10px;
    }
    
    .charts-section {
        gap: 8px;
        margin-bottom: 15px;
    }
    
    .charts-content {
        grid-template-columns: 1fr;
        gap: 10px;
    }
    
    .chart-container {
        padding: 8px;
    }
    
    .chart-header h2 {
        font-size: 1.2em;
    }
    
    .chart-wrapper {
        height: 200px;
    }
    
    .customers-section {
        padding: 12px;
        margin-bottom: 10px;
    }
    
    .customers-table {
        font-size: 0.9em;
    }
    
    .customers-table th,
    .customers-table td {
        padding: 6px;
    }
}

.chart-container {
    background: #333333;
    padding: 12px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    border: 1px solid #444444;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.chart-container:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(255, 215, 0, 0.2);
    border-color: #FFD700;
}

.chart-header {
    margin-bottom: 10px;
    text-align: center;
    border-bottom: 1px solid #3a3a3a;
    padding-bottom: 8px;
}

.chart-header h2 {
    margin: 0 0 8px 0;
    color: #81c784;
    font-size: 1.5em;
    font-weight: 600;
}

.chart-header p {
    margin: 0;
    color: #b0b0b0;
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
    background: #2a2a2a;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    border: 1px solid #3a3a3a;
    margin-bottom: 15px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.customers-section:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(255, 215, 0, 0.2);
    border-color: #FFD700;
}

.customers-section h2 {
    margin: 0 0 10px 0;
    color: #FFD700;
    text-align: center;
    font-size: 1.5em;
    font-weight: 600;
    border-bottom: 1px solid #3a3a3a;
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
    border-bottom: 1px solid #3a3a3a;
    color: #e0e0e0;
}

.customers-table th {
    background: #3a3a3a;
    font-weight: 600;
    color: #FFD700;
    font-size: 1em;
    text-transform: none;
    letter-spacing: 0;
}

.customers-table tr:hover {
    background: #3a3a3a;
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
    border: 1px solid #3a3a3a;
}

.rank-1 { 
    background: #2a2a2a; 
    color: #FFD700; 
    border-color: #FFD700;
}
.rank-2 { 
    background: #2a2a2a; 
    color: #c0c0c0; 
    border-color: #c0c0c0;
}
.rank-3 { 
    background: #2a2a2a; 
    color: #cd7f32; 
    border-color: #cd7f32;
}

.no-data {
    text-align: center;
    color: #6b7280;
    font-style: italic;
    padding: 24px 16px;
    font-size: 0.9em;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    padding-bottom: 8px;
    border-bottom: 1px solid #3a3a3a;
}

.section-header h2,
.section-header h3 {
    margin: 0;
    color: #FFD700;
    font-weight: 600;
}

.section-header h2 {
    font-size: 1.5em;
}

.section-header h3 {
    font-size: 1.3em;
}

.toggle-btn {
    background: #4a5568;
    color: #e2e8f0;
    border: none;
    border-radius: 4px;
    padding: 4px 8px;
    cursor: pointer;
    font-weight: bold;
    font-size: 1.2em;
    transition: all 0.3s ease;
    min-width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.toggle-btn:hover {
    background: #2d3748;
    transform: scale(1.1);
}

.section-content {
    transition: all 0.3s ease;
    overflow: hidden;
}

.section-content.collapsed {
    max-height: 0;
    opacity: 0;
    margin: 0;
    padding: 0;
}
</style>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartDefaults = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
            legend: { 
                display: false,
                labels: {
                    color: '#e0e0e0'
                }
            },
            tooltip: {
                backgroundColor: '#2a2a2a',
                titleColor: '#FFD700',
                bodyColor: '#e0e0e0',
                borderColor: '#3a3a3a',
                borderWidth: 1
            }
        },
        scales: {
            y: { 
                beginAtZero: true, 
                grid: { color: 'rgba(255,255,255,0.1)' },
                ticks: { color: '#e0e0e0' }
            },
            x: { 
                grid: { color: 'rgba(255,255,255,0.1)' },
                ticks: { color: '#e0e0e0' }
            }
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
                borderColor: '#FFD700',
                backgroundColor: 'rgba(255,215,0,0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true
            }]
        },
        options: Object.assign({}, chartDefaults, {
            plugins: Object.assign({}, chartDefaults.plugins, {
                legend: Object.assign({}, chartDefaults.plugins.legend, {
                    display: false
                })
            }),
            scales: Object.assign({}, chartDefaults.scales, {
                y: Object.assign({}, chartDefaults.scales.y, {
                    ticks: { 
                        callback: function(value) { return '\u20B1' + value.toLocaleString(); },
                        color: '#e0e0e0'
                    }
                })
            }),
            layout: {
                padding: 10
            },
            elements: {
                point: {
                    backgroundColor: '#FFD700',
                    borderColor: '#FFD700'
                }
            }
        })
    });
    
    // Set canvas background color
    document.getElementById('salesChart').style.backgroundColor = '#333333';

    // Bookings Chart
    new Chart(document.getElementById('bookingsChart'), {
        type: 'bar',
        data: {
            labels: @json($months ?? []),
            datasets: [{
                label: 'Bookings',
                data: @json($bookingCounts ?? []),
                backgroundColor: 'rgba(255,215,0,0.8)',
                borderColor: '#FFD700',
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: chartDefaults
    });
    
    // Set canvas background color
    document.getElementById('bookingsChart').style.backgroundColor = '#333333';

    // Users per Service Chart
    new Chart(document.getElementById('usersPerServiceChart'), {
        type: 'bar',
        data: {
            labels: @json(array_keys($usersPerService ?? [])),
            datasets: [{
                label: 'Users',
                data: @json(array_values($usersPerService ?? [])),
                backgroundColor: [
                    'rgba(255, 215, 0, 0.8)',
                    'rgba(255, 165, 0, 0.8)',
                    'rgba(218, 165, 32, 0.8)',
                    'rgba(184, 134, 11, 0.8)',
                    'rgba(146, 104, 8, 0.8)'
                ],
                borderColor: [
                    'rgba(255, 215, 0, 1)',
                    'rgba(255, 165, 0, 1)',
                    'rgba(218, 165, 32, 1)',
                    'rgba(184, 134, 11, 1)',
                    'rgba(146, 104, 8, 1)'
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
     
     // Set canvas background color
     document.getElementById('usersPerServiceChart').style.backgroundColor = '#333333';

     // Services Distribution Pie Chart
     new Chart(document.getElementById('servicesDistributionChart'), {
         type: 'pie',
         data: {
             labels: @json(array_keys($servicesDistribution ?? [])),
             datasets: [{
                 label: 'Services',
                 data: @json(array_values($servicesDistribution ?? [])),
                 backgroundColor: [
                     'rgba(255, 215, 0, 0.8)',
                     'rgba(255, 165, 0, 0.8)',
                     'rgba(218, 165, 32, 0.8)',
                     'rgba(184, 134, 11, 0.8)',
                     'rgba(146, 104, 8, 0.8)',
                     'rgba(255, 193, 7, 0.8)'
                 ],
                 borderColor: [
                     'rgba(129, 199, 132, 1)',
                     'rgba(102, 187, 106, 1)',
                     'rgba(76, 175, 80, 1)',
                     'rgba(67, 160, 71, 1)',
                     'rgba(56, 142, 60, 1)',
                     'rgba(46, 125, 50, 1)'
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
                     position: 'bottom',
                     labels: {
                         color: '#e0e0e0',
                         font: {
                             size: 12
                         }
                     }
                 },
                 tooltip: {
                     backgroundColor: '#2a2a2a',
                     titleColor: '#81c784',
                     bodyColor: '#e0e0e0',
                     borderColor: '#3a3a3a',
                     borderWidth: 1,
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
     
     // Set canvas background color
     document.getElementById('servicesDistributionChart').style.backgroundColor = '#333333';
});

// Toggle section functionality
function toggleSection(sectionName) {
    const content = document.getElementById(sectionName + '-content');
    const icon = document.getElementById(sectionName + '-icon');
    
    if (content.style.display === 'none') {
        content.style.display = '';
        icon.textContent = '−';
    } else {
        content.style.display = 'none';
        icon.textContent = '+';
    }
}
</script>
 @endsection