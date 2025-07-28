@extends('layouts.app')

@section('title', 'Database Management')

@section('content')
<div class="database-management">
    <div class="database-header">
        <h1>Database Management</h1>
        <p>Manage your Music Studio database with phpMyAdmin integration and direct database tools.</p>
    </div>

    <!-- Database Connection Status -->
    <div class="connection-status">
        <div class="status-card connected">
            <div class="status-icon">✓</div>
            <div class="status-content">
                <h3>Database Connected</h3>
                <p>Successfully connected to MySQL database.</p>
                <div class="db-info">
                    <div class="info-item">
                        <strong>Database:</strong> <code>{{ config('database.connections.mysql.database') }}</code>
                    </div>
                    <div class="info-item">
                        <strong>Host:</strong> <code>{{ config('database.connections.mysql.host') }}:{{ config('database.connections.mysql.port') }}</code>
                    </div>
                    <div class="info-item">
                        <strong>Username:</strong> <code>{{ config('database.connections.mysql.username') }}</code>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- phpMyAdmin Access -->
    <div class="phpmyadmin-section">
        <h2>phpMyAdmin Access</h2>
        <div class="phpmyadmin-card">
            <div class="phpmyadmin-info">
                <h3>Direct Database Access</h3>
                <p>Access your database through phpMyAdmin web interface for advanced database operations.</p>
                <div class="access-details">
                    <div class="detail-item">
                        <strong>URL:</strong> 
                        <a href="http://localhost/phpmyadmin" target="_blank" class="phpmyadmin-link">
                            http://localhost/phpmyadmin
                        </a>
                    </div>
                    <div class="detail-item">
                        <strong>Database:</strong> <code>{{ config('database.connections.mysql.database') }}</code>
                    </div>
                    <div class="detail-item">
                        <strong>Login:</strong> Use your MySQL credentials
                    </div>
                </div>
            </div>
            <div class="phpmyadmin-actions">
                <a href="http://localhost/phpmyadmin" target="_blank" class="btn btn-primary">
                    <i class="icon-external">🔗</i> Open phpMyAdmin
                </a>
                <button onclick="copyDatabaseInfo()" class="btn btn-secondary">
                    <i class="icon-copy">📋</i> Copy DB Info
                </button>
            </div>
        </div>
    </div>

    <!-- Database Statistics -->
    <div class="database-stats">
        <h2>Database Overview</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Bookings</h3>
                <div class="stat-number">{{ $totalBookings }}</div>
                <div class="stat-label">Records in bookings table</div>
            </div>
            <div class="stat-card">
                <h3>Total Users</h3>
                <div class="stat-number">{{ $totalUsers }}</div>
                <div class="stat-label">Registered users</div>
            </div>
            <div class="stat-card">
                <h3>Admin Users</h3>
                <div class="stat-number">{{ $adminUsers }}</div>
                <div class="stat-label">Users with admin access</div>
            </div>
            <div class="stat-card">
                <h3>Database Size</h3>
                <div class="stat-number">{{ $databaseSize }}</div>
                <div class="stat-label">Approximate size</div>
            </div>
        </div>
    </div>

    <!-- Google Calendar Integration Stats -->
    <div class="calendar-integration-stats">
        <h2>Google Calendar Integration</h2>
        <div class="calendar-stats-grid">
            <div class="stat-card calendar-stat">
                <h3>Calendar Connected</h3>
                <div class="stat-number">{{ $calendarConnectedAdmins }}</div>
                <div class="stat-label">Admins with Google Calendar</div>
            </div>
            <div class="stat-card calendar-stat">
                <h3>Synced Bookings</h3>
                <div class="stat-number">{{ $syncedBookings }}</div>
                <div class="stat-label">Bookings in Google Calendar</div>
            </div>
            <div class="stat-card calendar-stat">
                <h3>Pending Sync</h3>
                <div class="stat-number">{{ $unsyncedBookings }}</div>
                <div class="stat-label">Confirmed bookings not synced</div>
            </div>
            <div class="stat-card calendar-stat">
                <h3>Calendar Events</h3>
                <div class="stat-number">{{ $totalCalendarEvents }}</div>
                <div class="stat-label">Total events created</div>
            </div>
        </div>
    </div>

    <!-- Quick Database Actions -->
    <div class="database-actions">
        <h2>Quick Database Actions</h2>
        <div class="action-grid">
            <div class="action-card">
                <h3>View Recent Bookings</h3>
                <p>Check the latest booking entries in the database</p>
                <button onclick="showRecentBookings()" class="btn btn-info">
                    <i class="icon-list">📋</i> View Bookings
                </button>
            </div>
            <div class="action-card">
                <h3>Database Backup</h3>
                <p>Create a backup of your current database</p>
                <form method="POST" action="{{ route('admin.database.backup') }}">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="icon-download">💾</i> Create Backup
                    </button>
                </form>
            </div>
            <div class="action-card">
                <h3>Run Migrations</h3>
                <p>Execute pending database migrations</p>
                <form method="POST" action="{{ route('admin.database.migrate') }}">
                    @csrf
                    <button type="submit" class="btn btn-warning" onclick="return confirm('Run database migrations?')">
                        <i class="icon-refresh">🔄</i> Run Migrations
                    </button>
                </form>
            </div>
            <div class="action-card">
                <h3>Clear Cache</h3>
                <p>Clear application and database cache</p>
                <form method="POST" action="{{ route('admin.database.clear-cache') }}">
                    @csrf
                    <button type="submit" class="btn btn-secondary">
                        <i class="icon-clean">🧹</i> Clear Cache
                    </button>
                </form>
            </div>
            <div class="action-card calendar-action">
                <h3>Calendar Database Query</h3>
                <p>View Google Calendar related data in phpMyAdmin</p>
                <button onclick="openCalendarQueries()" class="btn btn-info">
                    <i class="icon-calendar">📅</i> Calendar Queries
                </button>
            </div>
            <div class="action-card calendar-action">
                <h3>Sync Status Check</h3>
                <p>Check booking sync status in database</p>
                <button onclick="showSyncStatus()" class="btn btn-warning">
                    <i class="icon-sync">🔄</i> Check Sync
                </button>
            </div>
        </div>
    </div>

    <!-- Recent Database Activity -->
    <div class="recent-activity" id="recent-bookings" style="display: none;">
        <h2>Recent Database Entries</h2>
        <div class="activity-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Reference</th>
                        <th>User</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Calendar Sync</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentBookings as $booking)
                    <tr>
                        <td>{{ $booking->id }}</td>
                        <td><code>{{ $booking->reference }}</code></td>
                        <td>{{ $booking->user->name ?? 'N/A' }}</td>
                        <td>{{ \Carbon\Carbon::parse($booking->date)->format('M d, Y') }}</td>
                        <td>{{ $booking->time_slot }}</td>
                        <td>
                            <span class="status-badge status-{{ $booking->status }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                        <td>
                            @if($booking->google_event_id)
                                <span class="sync-badge synced">✓ Synced</span>
                            @else
                                <span class="sync-badge not-synced">✗ Not Synced</span>
                            @endif
                        </td>
                        <td>{{ $booking->created_at->format('M d, H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Calendar Database Queries -->
    <div class="calendar-queries" id="calendar-queries" style="display: none;">
        <h2>Google Calendar Database Queries</h2>
        <p>Use these SQL queries in phpMyAdmin to analyze Google Calendar integration data:</p>
        
        <div class="query-section">
            <h3>📊 Calendar Statistics Queries</h3>
            <div class="query-item">
                <h4>Count of synced vs unsynced bookings:</h4>
                <div class="query-code">
                    <code>SELECT 
    CASE WHEN google_event_id IS NOT NULL THEN 'Synced' ELSE 'Not Synced' END as sync_status,
    COUNT(*) as count
FROM bookings 
WHERE status = 'confirmed'
GROUP BY sync_status;</code>
                    <button onclick="copyQuery(this)" class="copy-btn">📋 Copy</button>
                </div>
            </div>
            
            <div class="query-item">
                <h4>Admins with Google Calendar access:</h4>
                <div class="query-code">
                    <code>SELECT name, email, 
    CASE WHEN google_calendar_token IS NOT NULL THEN 'Connected' ELSE 'Not Connected' END as calendar_status
FROM users 
WHERE is_admin = 1;</code>
                    <button onclick="copyQuery(this)" class="copy-btn">📋 Copy</button>
                </div>
            </div>
            
            <div class="query-item">
                <h4>Bookings with calendar sync details:</h4>
                <div class="query-code">
                    <code>SELECT b.reference, b.date, b.time_slot, b.status,
    CASE WHEN b.google_event_id IS NOT NULL THEN 'Synced' ELSE 'Pending' END as sync_status,
    u.name as client_name
FROM bookings b
LEFT JOIN users u ON b.user_id = u.id
WHERE b.status = 'confirmed'
ORDER BY b.created_at DESC
LIMIT 20;</code>
                    <button onclick="copyQuery(this)" class="copy-btn">📋 Copy</button>
                </div>
            </div>
        </div>
        
        <div class="query-section">
            <h3>🔧 Calendar Management Queries</h3>
            <div class="query-item">
                <h4>Find bookings that failed to sync:</h4>
                <div class="query-code">
                    <code>SELECT * FROM bookings 
WHERE status = 'confirmed' 
AND google_event_id IS NULL 
AND created_at < DATE_SUB(NOW(), INTERVAL 1 HOUR);</code>
                    <button onclick="copyQuery(this)" class="copy-btn">📋 Copy</button>
                </div>
            </div>
            
            <div class="query-item">
                <h4>Calendar token status for all admins:</h4>
                <div class="query-code">
                    <code>SELECT name, email, 
    CASE 
        WHEN google_calendar_token IS NOT NULL THEN 'Has Token'
        ELSE 'No Token'
    END as token_status,
    google_calendar_id
FROM users 
WHERE is_admin = 1;</code>
                    <button onclick="copyQuery(this)" class="copy-btn">📋 Copy</button>
                </div>
            </div>
        </div>
        
        <div class="phpmyadmin-direct-link">
            <h3>🔗 Direct phpMyAdmin Access</h3>
            <p>Click below to open phpMyAdmin with the music studio database selected:</p>
            <a href="http://localhost/phpmyadmin/index.php?route=/database/structure&db={{ config('database.connections.mysql.database') }}" 
               target="_blank" class="btn btn-primary">
                <i class="icon-external">🔗</i> Open Database in phpMyAdmin
            </a>
        </div>
    </div>

    <!-- Sync Status Details -->
    <div class="sync-status" id="sync-status" style="display: none;">
        <h2>Calendar Sync Status</h2>
        <div class="sync-overview">
            <div class="sync-stat">
                <h3>Sync Summary</h3>
                <ul>
                    <li><strong>Total Confirmed Bookings:</strong> {{ $totalBookings }}</li>
                    <li><strong>Successfully Synced:</strong> {{ $syncedBookings }}</li>
                    <li><strong>Pending Sync:</strong> {{ $unsyncedBookings }}</li>
                    <li><strong>Sync Success Rate:</strong> {{ $totalBookings > 0 ? round(($syncedBookings / $totalBookings) * 100, 1) : 0 }}%</li>
                </ul>
            </div>
        </div>
        
        @if($unsyncedBookings > 0)
        <div class="sync-actions-section">
            <h3>⚠️ Unsynced Bookings Found</h3>
            <p>There are {{ $unsyncedBookings }} confirmed bookings that haven't been synced to Google Calendar.</p>
            <div class="sync-action-buttons">
                <a href="{{ route('admin.calendar') }}" class="btn btn-primary">
                    <i class="icon-calendar">📅</i> Go to Calendar Integration
                </a>
                <form method="POST" action="{{ route('admin.calendar.sync') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="icon-sync">🔄</i> Sync Now
                    </button>
                </form>
            </div>
        </div>
        @else
        <div class="sync-success">
            <h3>✅ All Bookings Synced</h3>
            <p>All confirmed bookings are successfully synced with Google Calendar.</p>
        </div>
        @endif
    </div>

    <!-- Alternative phpMyAdmin URLs -->
    <div class="alternative-urls">
        <h2>Alternative phpMyAdmin URLs</h2>
        <p>If the default URL doesn't work, try these common alternatives:</p>
        <div class="url-list">
            <div class="url-item">
                <strong>XAMPP:</strong> 
                <a href="http://localhost/phpmyadmin" target="_blank">http://localhost/phpmyadmin</a>
            </div>
            <div class="url-item">
                <strong>WAMP:</strong> 
                <a href="http://localhost/phpmyadmin" target="_blank">http://localhost/phpmyadmin</a>
            </div>
            <div class="url-item">
                <strong>MAMP:</strong> 
                <a href="http://localhost:8888/phpMyAdmin" target="_blank">http://localhost:8888/phpMyAdmin</a>
            </div>
            <div class="url-item">
                <strong>Custom Port:</strong> 
                <a href="http://localhost:8080/phpmyadmin" target="_blank">http://localhost:8080/phpmyadmin</a>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="navigation">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            ← Back to Dashboard
        </a>
    </div>
</div>

<script>
function copyDatabaseInfo() {
    const dbInfo = `Database: {{ config('database.connections.mysql.database') }}\nHost: {{ config('database.connections.mysql.host') }}:{{ config('database.connections.mysql.port') }}\nUsername: {{ config('database.connections.mysql.username') }}`;
    navigator.clipboard.writeText(dbInfo).then(() => {
        alert('Database information copied to clipboard!');
    });
}

function showRecentBookings() {
    const element = document.getElementById('recent-bookings');
    if (element.style.display === 'none') {
        element.style.display = 'block';
        element.scrollIntoView({ behavior: 'smooth' });
        // Hide other sections
        document.getElementById('calendar-queries').style.display = 'none';
        document.getElementById('sync-status').style.display = 'none';
    } else {
        element.style.display = 'none';
    }
}

function openCalendarQueries() {
    const element = document.getElementById('calendar-queries');
    if (element.style.display === 'none') {
        element.style.display = 'block';
        element.scrollIntoView({ behavior: 'smooth' });
        // Hide other sections
        document.getElementById('recent-bookings').style.display = 'none';
        document.getElementById('sync-status').style.display = 'none';
    } else {
        element.style.display = 'none';
    }
}

function showSyncStatus() {
    const element = document.getElementById('sync-status');
    if (element.style.display === 'none') {
        element.style.display = 'block';
        element.scrollIntoView({ behavior: 'smooth' });
        // Hide other sections
        document.getElementById('recent-bookings').style.display = 'none';
        document.getElementById('calendar-queries').style.display = 'none';
    } else {
        element.style.display = 'none';
    }
}

function copyQuery(button) {
    const codeElement = button.previousElementSibling;
    const queryText = codeElement.textContent;
    navigator.clipboard.writeText(queryText).then(() => {
        const originalText = button.textContent;
        button.textContent = '✅ Copied!';
        button.style.background = '#27ae60';
        setTimeout(() => {
            button.textContent = originalText;
            button.style.background = '';
        }, 2000);
    }).catch(() => {
        alert('Failed to copy query. Please select and copy manually.');
    });
}
</script>

<style>
.database-management {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.database-header {
    text-align: center;
    margin-bottom: 30px;
}

.database-header h1 {
    color: #333;
    margin-bottom: 10px;
}

.database-header p {
    color: #666;
    font-size: 16px;
}

.status-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    padding: 25px;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 20px;
    border-left: 5px solid #27ae60;
}

.status-icon {
    font-size: 48px;
    color: #27ae60;
    font-weight: bold;
}

.status-content {
    flex: 1;
}

.status-content h3 {
    margin: 0 0 10px 0;
    color: #333;
}

.status-content p {
    color: #666;
    margin-bottom: 15px;
}

.db-info {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 10px;
}

.info-item code {
    background: #f8f9fa;
    padding: 4px 8px;
    border-radius: 4px;
    font-family: monospace;
    font-size: 12px;
}

.phpmyadmin-section, .database-stats, .database-actions, .recent-activity, .alternative-urls {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 25px;
    margin-bottom: 25px;
}

.phpmyadmin-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    gap: 20px;
}

.phpmyadmin-info {
    flex: 1;
}

.phpmyadmin-info h3 {
    margin: 0 0 10px 0;
    color: #333;
}

.phpmyadmin-info p {
    color: #666;
    margin-bottom: 15px;
}

.access-details {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 10px;
}

.phpmyadmin-link {
    color: #3498db;
    text-decoration: none;
    font-family: monospace;
}

.phpmyadmin-link:hover {
    text-decoration: underline;
}

.phpmyadmin-actions {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.stat-card {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    border-left: 4px solid #3498db;
}

.stat-card h3 {
    margin: 0 0 10px 0;
    color: #666;
    font-size: 14px;
    text-transform: uppercase;
}

.stat-number {
    font-size: 32px;
    font-weight: bold;
    color: #333;
    margin-bottom: 5px;
}

.stat-label {
    color: #666;
    font-size: 12px;
}

.action-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.action-card {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
}

.action-card h3 {
    margin: 0 0 10px 0;
    color: #333;
}

.action-card p {
    color: #666;
    margin-bottom: 15px;
    font-size: 14px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: background 0.3s;
}

.btn-primary { background: #3498db; color: white; }
.btn-primary:hover { background: #2980b9; }
.btn-secondary { background: #95a5a6; color: white; }
.btn-secondary:hover { background: #7f8c8d; }
.btn-success { background: #27ae60; color: white; }
.btn-success:hover { background: #229954; }
.btn-warning { background: #f39c12; color: white; }
.btn-warning:hover { background: #e67e22; }
.btn-info { background: #17a2b8; color: white; }
.btn-info:hover { background: #138496; }

.activity-table {
    overflow-x: auto;
}

.activity-table table {
    width: 100%;
    border-collapse: collapse;
}

.activity-table th,
.activity-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.activity-table th {
    background: #f8f9fa;
    font-weight: bold;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
}

.status-pending { background: #fff3cd; color: #856404; }
.status-confirmed { background: #d4edda; color: #155724; }
.status-cancelled { background: #f8d7da; color: #721c24; }

.url-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.url-item {
    padding: 10px;
    background: #f8f9fa;
    border-radius: 5px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.url-item a {
    color: #3498db;
    text-decoration: none;
    font-family: monospace;
}

.url-item a:hover {
    text-decoration: underline;
}

.navigation {
    margin-top: 30px;
}

/* Calendar Integration Styles */
.calendar-integration-stats {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 25px;
    margin-bottom: 25px;
}

.calendar-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.calendar-stat {
    border-left: 4px solid #e74c3c;
}

.calendar-action {
    border-left: 4px solid #e74c3c;
}

.calendar-queries, .sync-status {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 25px;
    margin-bottom: 25px;
}

.query-section {
    margin-bottom: 30px;
}

.query-section h3 {
    color: #333;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #f8f9fa;
}

.query-item {
    margin-bottom: 25px;
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
}

.query-item h4 {
    color: #555;
    margin-bottom: 10px;
}

.query-code {
    position: relative;
    background: #2c3e50;
    border-radius: 5px;
    padding: 15px;
    margin-top: 10px;
}

.query-code code {
    color: #ecf0f1;
    font-family: 'Courier New', monospace;
    font-size: 13px;
    line-height: 1.4;
    white-space: pre-wrap;
    background: none;
    padding: 0;
}

.copy-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #3498db;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 3px;
    cursor: pointer;
    font-size: 12px;
    transition: background 0.3s;
}

.copy-btn:hover {
    background: #2980b9;
}

.phpmyadmin-direct-link {
    background: #e8f4fd;
    padding: 20px;
    border-radius: 8px;
    margin-top: 20px;
    text-align: center;
}

.sync-overview {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.sync-stat ul {
    list-style: none;
    padding: 0;
}

.sync-stat li {
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}

.sync-stat li:last-child {
    border-bottom: none;
}

.sync-actions-section {
    background: #fff3cd;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #ffc107;
    margin-bottom: 20px;
}

.sync-action-buttons {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.sync-success {
    background: #d4edda;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #28a745;
    text-align: center;
}

.sync-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
}

.sync-badge.synced {
    background: #d4edda;
    color: #155724;
}

.sync-badge.not-synced {
    background: #f8d7da;
    color: #721c24;
}

@media (max-width: 768px) {
    .phpmyadmin-card {
        flex-direction: column;
        text-align: center;
    }
    
    .stats-grid, .calendar-stats-grid {
        grid-template-columns: 1fr;
    }
    
    .action-grid {
        grid-template-columns: 1fr;
    }
    
    .sync-action-buttons {
        flex-direction: column;
    }
    
    .query-code {
        font-size: 12px;
    }
}
</style>
@endsection