@extends('layouts.admin')

@section('title', 'Database Management')

@section('content')
<div class="admin-content">
    <!-- Modern Page Header -->
    <div class="modern-header">
        <div class="header-content">
            <div class="header-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none">
                    <path d="M4 6C4 4.89543 4.89543 4 6 4H18C19.1046 4 20 4.89543 20 6V7H4V6Z" fill="#4285f4"/>
                    <path d="M4 9H20V18C20 19.1046 19.1046 20 18 20H6C4.89543 20 4 19.1046 4 18V9Z" fill="#4285f4" fill-opacity="0.7"/>
                    <circle cx="6" cy="12" r="1" fill="white"/>
                    <circle cx="6" cy="15" r="1" fill="white"/>
                    <circle cx="6" cy="18" r="1" fill="white"/>
                </svg>
            </div>
            <div class="header-text">
                <h1>Database Management</h1>
                <p>Manage your database, backups, and system maintenance</p>
            </div>
        </div>
        
    </div>

    <!-- Database Connection Status -->
    <div class="modern-card">
        <div class="card-header">
            <div class="card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M9 12L11 14L15 10M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="#34a853" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="card-title">
                <h2>Database Connection</h2>
                <p>Successfully connected to MySQL database</p>
            </div>
        </div>
        
        <div class="connection-details">
            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">Database Name</span>
                    <span class="detail-value">{{ config('database.connections.mysql.database') }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Host Address</span>
                    <span class="detail-value">{{ config('database.connections.mysql.host') }}:{{ config('database.connections.mysql.port') }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Username</span>
                    <span class="detail-value">{{ config('database.connections.mysql.username') }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Connection Status</span>
                    <span class="detail-value status-connected">Connected</span>
                </div>
            </div>
        </div>
    </div>

    <!-- phpMyAdmin Access -->
    <div class="modern-card">
        <div class="card-header">
            <div class="card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M13 2L3 14H12L11 22L21 10H12L13 2Z" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="card-title">
                <h2>phpMyAdmin Access</h2>
                <p>Direct database access through web interface</p>
            </div>
        </div>
        
        <div class="phpmyadmin-content">
            <div class="access-info">
                <div class="info-section">
                    <h4>Quick Access</h4>
                    <p>Access your database through phpMyAdmin web interface for advanced database operations.</p>
                    
                    <div class="access-details">
                        <div class="detail-item">
                            <span class="detail-label">Primary URL</span>
                            <a href="http://localhost/phpmyadmin" target="_blank" class="detail-link">
                                http://localhost/phpmyadmin
                            </a>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Database</span>
                            <span class="detail-value">{{ config('database.connections.mysql.database') }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Authentication</span>
                            <span class="detail-value">Use your MySQL credentials</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="phpmyadmin-actions">
                <a href="http://localhost/phpmyadmin" target="_blank" class="btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M18 13V6C18 5.46957 17.7893 4.96086 17.4142 4.58579C17.0391 4.21071 16.5304 4 16 4H5C4.46957 4 3.96086 4.21071 3.58579 4.58579C3.21071 4.96086 3 5.46957 3 6V18C3 18.5304 3.21071 19.0391 3.58579 19.4142C3.96086 19.7893 4.46957 20 5 20H16C16.5304 20 17.0391 19.7893 17.4142 19.4142C17.7893 19.0391 18 18.5304 18 18V13Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M15 3V7L18 4L21 7V3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Open phpMyAdmin
                </a>
                <button onclick="copyDatabaseInfo()" class="btn-secondary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <rect x="9" y="9" width="13" height="13" rx="2" ry="2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M5 15H4C3.46957 15 2.96086 14.7893 2.58579 14.4142C2.21071 14.0391 2 13.5304 2 13V4C2 3.46957 2.21071 2.96086 2.58579 2.58579C2.96086 2.21071 3.46957 2 4 2H13C13.5304 2 14.0391 2.21071 14.4142 2.58579C14.7893 2.96086 15 3.46957 15 4V5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Copy DB Info
                </button>
            </div>
        </div>
    </div>

    <!-- Database Statistics -->
    <div class="modern-card">
        <div class="card-header">
            <div class="card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M3 3V21H21V3H3Z" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9 9H15" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9 13H15" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M9 17H13" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="card-title">
                <h2>Database Statistics</h2>
                <p>Overview of your database metrics</p>
            </div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="#34a853" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="16" y1="2" x2="16" y2="6" stroke="#34a853" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="8" y1="2" x2="8" y2="6" stroke="#34a853" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="3" y1="10" x2="21" y2="10" stroke="#34a853" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="stat-info">
                    <h3>{{ $totalBookings }}</h3>
                    <p>Total Bookings</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M20 21V19C20 17.9391 19.5786 16.9217 18.8284 16.1716C18.0783 15.4214 17.0609 15 16 15H8C6.93913 15 5.92172 15.4214 5.17157 16.1716C4.42143 16.9217 4 17.9391 4 19V21" stroke="#fbbc04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="12" cy="7" r="4" stroke="#fbbc04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="stat-info">
                    <h3>{{ $totalUsers }}</h3>
                    <p>Registered Users</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2" stroke="#ea4335" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <circle cx="12" cy="16" r="1" stroke="#ea4335" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M7 11V7C7 5.67392 7.52678 4.40215 8.46447 3.46447C9.40215 2.52678 10.6739 2 12 2C13.3261 2 14.5979 2.52678 15.5355 3.46447C16.4732 4.40215 17 5.67392 17 7V11" stroke="#ea4335" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="stat-info">
                    <h3>{{ $adminUsers }}</h3>
                    <p>Admin Users</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <ellipse cx="12" cy="5" rx="9" ry="3" stroke="#9aa0a6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M21 12C21 13.66 16.97 15 12 15S3 13.66 3 12" stroke="#9aa0a6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M3 5V19C3 20.66 7.03 22 12 22S21 20.66 21 19V5" stroke="#9aa0a6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="stat-info">
                    <h3>{{ $databaseSize }}</h3>
                    <p>Database Size</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Google Calendar Integration Stats -->
    <div class="modern-card">
        <div class="card-header">
            <div class="card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <line x1="16" y1="2" x2="16" y2="6" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <line x1="8" y1="2" x2="8" y2="6" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <line x1="3" y1="10" x2="21" y2="10" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M8 14L10 16L16 10" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="card-title">
                <h2>Google Calendar Integration</h2>
                <p>Synchronization status and metrics</p>
            </div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M16 4H18C19.1046 4 20 4.89543 20 6V20C20 21.1046 19.1046 22 18 22H6C4.89543 22 4 21.1046 4 20V6C4 4.89543 4.89543 4 6 4H8M16 4V2M16 4V6M8 4V2M8 4V6M4 10H20" stroke="#34a853" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M9 14L11 16L15 12" stroke="#34a853" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="stat-info">
                    <h3>{{ $calendarConnectedAdmins }}</h3>
                    <p>Calendar Connected</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M23 12C23 18.075 18.075 23 12 23S1 18.075 1 12 5.925 1 12 1" stroke="#34a853" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M22 4L12 14.01L9 11.01" stroke="#34a853" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="stat-info">
                    <h3>{{ $syncedBookings }}</h3>
                    <p>Synced Bookings</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="10" stroke="#fbbc04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <polyline points="12,6 12,12 16,14" stroke="#fbbc04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="stat-info">
                    <h3>{{ $unsyncedBookings }}</h3>
                    <p>Pending Sync</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="#9aa0a6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="16" y1="2" x2="16" y2="6" stroke="#9aa0a6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="8" y1="2" x2="8" y2="6" stroke="#9aa0a6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="3" y1="10" x2="21" y2="10" stroke="#9aa0a6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="stat-info">
                    <h3>{{ $totalCalendarEvents }}</h3>
                    <p>Calendar Events</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Database Actions -->
    <div class="modern-card">
        <div class="card-header">
            <div class="card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M12.89 1.45C12.75 1.15 12.38 1.15 12.24 1.45L8.24 9.45C8.1 9.75 8.32 10.09 8.66 10.09H15.34C15.68 10.09 15.9 9.75 15.76 9.45L11.76 1.45H12.89Z" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 15L8 19H16L12 15Z" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="12" r="3" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="card-title">
                <h2>Quick Database Actions</h2>
                <p>Essential database management tools</p>
            </div>
        </div>
        
        <div class="action-grid">
            <div class="action-card">
                <div class="action-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="16" y1="2" x2="16" y2="6" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="8" y1="2" x2="8" y2="6" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="3" y1="10" x2="21" y2="10" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="action-content">
                    <h3>View Recent Bookings</h3>
                    <p>Check the latest booking entries in the database</p>
                    <button onclick="showRecentBookings()" class="btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <line x1="16" y1="2" x2="16" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <line x1="8" y1="2" x2="8" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <line x1="3" y1="10" x2="21" y2="10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        View Bookings
                    </button>
                </div>
            </div>
            
            <div class="action-card backup-restore-card">
                <div class="action-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M21 15V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V15" stroke="#34a853" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <polyline points="7,10 12,15 17,10" stroke="#34a853" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="12" y1="15" x2="12" y2="3" stroke="#34a853" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="action-content">
                    <h3>Backup & Restore</h3>
                    <p>Comprehensive backup and restore management</p>
                    <button onclick="toggleBackupSection()" class="btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <path d="M21 15V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <polyline points="7,10 12,15 17,10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <line x1="12" y1="15" x2="12" y2="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Manage Backups
                    </button>
                </div>
            </div>
            
            <div class="action-card">
                <div class="action-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <polyline points="23,4 23,10 17,10" stroke="#fbbc04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <polyline points="1,20 1,14 7,14" stroke="#fbbc04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M20.49 9C19.9828 7.56678 19.1209 6.28392 17.9845 5.27304C16.8482 4.26216 15.4745 3.55682 13.9917 3.21834C12.5089 2.87986 10.9652 2.91902 9.50481 3.33329C8.04437 3.74757 6.70481 4.52437 5.60999 5.59L1 10M23 14L18.39 18.41C17.2952 19.4756 15.9556 20.2524 14.4952 20.6667C13.0348 21.081 11.4911 21.1201 10.0083 20.7817C8.52547 20.4432 7.1518 19.7378 6.01547 18.727C4.87913 17.7161 4.01717 16.4332 3.51 15" stroke="#fbbc04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="action-content">
                    <h3>Run Migrations</h3>
                    <p>Execute pending database migrations</p>
                    <form method="POST" action="{{ route('admin.database.migrate') }}" class="action-form">
                        @csrf
                        <button type="submit" class="btn-secondary" onclick="return confirm('Run database migrations?')">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <polyline points="23,4 23,10 17,10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <polyline points="1,20 1,14 7,14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M20.49 9C19.9828 7.56678 19.1209 6.28392 17.9845 5.27304C16.8482 4.26216 15.4745 3.55682 13.9917 3.21834C12.5089 2.87986 10.9652 2.91902 9.50481 3.33329C8.04437 3.74757 6.70481 4.52437 5.60999 5.59L1 10M23 14L18.39 18.41C17.2952 19.4756 15.9556 20.2524 14.4952 20.6667C13.0348 21.081 11.4911 21.1201 10.0083 20.7817C8.52547 20.4432 7.1518 19.7378 6.01547 18.727C4.87913 17.7161 4.01717 16.4332 3.51 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Run Migrations
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="action-card">
                <div class="action-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M3 6H5H21" stroke="#ea4335" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="#ea4335" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="action-content">
                    <h3>Clear Cache</h3>
                    <p>Clear application and database cache</p>
                    <form method="POST" action="{{ route('admin.database.clear-cache') }}" class="action-form">
                        @csrf
                        <button type="submit" class="btn-warning">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                <path d="M3 6H5H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Clear Cache
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="action-card calendar-action">
                <div class="action-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="16" y1="2" x2="16" y2="6" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="8" y1="2" x2="8" y2="6" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <line x1="3" y1="10" x2="21" y2="10" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8 14L10 16L16 10" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="action-content">
                    <h3>Calendar Database Query</h3>
                    <p>View Google Calendar related data in phpMyAdmin</p>
                    <button onclick="openCalendarQueries()" class="btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <line x1="16" y1="2" x2="16" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <line x1="8" y1="2" x2="8" y2="6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <line x1="3" y1="10" x2="21" y2="10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M8 14L10 16L16 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Calendar Queries
                    </button>
                </div>
            </div>
            
            <div class="action-card calendar-action">
                <div class="action-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <polyline points="23,4 23,10 17,10" stroke="#fbbc04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <polyline points="1,20 1,14 7,14" stroke="#fbbc04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M20.49 9C19.9828 7.56678 19.1209 6.28392 17.9845 5.27304C16.8482 4.26216 15.4745 3.55682 13.9917 3.21834C12.5089 2.87986 10.9652 2.91902 9.50481 3.33329C8.04437 3.74757 6.70481 4.52437 5.60999 5.59L1 10M23 14L18.39 18.41C17.2952 19.4756 15.9556 20.2524 14.4952 20.6667C13.0348 21.081 11.4911 21.1201 10.0083 20.7817C8.52547 20.4432 7.1518 19.7378 6.01547 18.727C4.87913 17.7161 4.01717 16.4332 3.51 15" stroke="#fbbc04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="action-content">
                    <h3>Sync Status Check</h3>
                    <p>Check booking sync status in database</p>
                    <button onclick="showSyncStatus()" class="btn-secondary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <polyline points="23,4 23,10 17,10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <polyline points="1,20 1,14 7,14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M20.49 9C19.9828 7.56678 19.1209 6.28392 17.9845 5.27304C16.8482 4.26216 15.4745 3.55682 13.9917 3.21834C12.5089 2.87986 10.9652 2.91902 9.50481 3.33329C8.04437 3.74757 6.70481 4.52437 5.60999 5.59L1 10M23 14L18.39 18.41C17.2952 19.4756 15.9556 20.2524 14.4952 20.6667C13.0348 21.081 11.4911 21.1201 10.0083 20.7817C8.52547 20.4432 7.1518 19.7378 6.01547 18.727C4.87913 17.7161 4.01717 16.4332 3.51 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Check Sync
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Comprehensive Backup & Restore Management -->
    <div class="modern-card backup-restore-section" id="backup-restore-section" style="display: none;">
        <div class="card-header">
            <div class="card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <polyline points="14,2 14,8 20,8" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <line x1="16" y1="13" x2="8" y2="13" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <line x1="16" y1="17" x2="8" y2="17" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <polyline points="10,9 9,9 8,9" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <div class="card-title">
                <h2>Backup & Restore Management</h2>
                <p>Create, download, and manage complete system backups</p>
            </div>
        </div>
        
        <div class="backup-restore-content">
            <!-- Create New Backup -->
            <div class="backup-section">
                <h3>Create New Backup</h3>
                <p>Generate a complete backup including database and files</p>
                <form id="backup-form" class="backup-form">
                    @csrf
                    <div class="backup-options">
                        <label class="checkbox-option">
                            <input type="checkbox" name="include_database" value="1" checked>
                            <span class="checkmark"></span>
                            Include Database
                        </label>
                        <label class="checkbox-option">
                            <input type="checkbox" name="include_files" value="1" checked>
                            <span class="checkmark"></span>
                            Include Files (uploads, images, .env)
                        </label>
                    </div>
                    <button type="button" onclick="createBackup()" class="btn-primary backup-btn" id="backup-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <path d="M21 15V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <polyline points="7,10 12,15 17,10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <line x1="12" y1="15" x2="12" y2="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Create Backup
                    </button>
                </form>
            </div>

            <!-- Database Restore Section -->
            <div class="restore-section">
                <h3>Database Restore</h3>
                <p>Restore your database from a backup file</p>
                <form id="restore-form" class="restore-form">
                    @csrf
                    <div class="file-upload-area">
                        <div class="upload-zone" id="restore-upload-zone">
                            <div class="upload-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none">
                                    <path d="M14 2H6C5.46957 2 4.96086 2.21071 4.58579 2.58579C4.21071 2.96086 4 3.46957 4 4V20C4 20.5304 4.21071 21.0391 4.58579 21.4142C4.96086 21.7893 5.46957 22 6 22H18C18.5304 22 19.0391 21.7893 19.4142 21.4142C19.7893 21.0391 20 20.5304 20 20V8L14 2Z" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <polyline points="14,2 14,8 20,8" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 18V12M12 12L9 15M12 12L15 15" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <h4>Drop database backup file here or click to browse</h4>
                            <p>Supported formats: .sql, .zip, .tar, .gz</p>
                        </div>
                        <input type="file" id="restore-file" name="restore_file" accept=".sql,.zip,.tar,.gz" style="display: none;">
                    </div>
                    <div class="restore-options" style="display: none;" id="restore-options">
                        <div class="file-info" id="restore-file-info"></div>
                        <div class="restore-settings">
                            <label class="checkbox-option">
                                <input type="checkbox" id="confirm-restore" name="confirm_restore" value="1">
                                <span class="checkmark"></span>
                                I understand this will overwrite the current database
                            </label>
                        </div>
                        <div class="restore-actions">
                            <button type="button" onclick="restoreDatabase()" class="btn-primary restore-btn" id="restore-btn" disabled>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                    <path d="M3 12C3 12 5.5 6 12 6S21 12 21 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M12 6V2L8 6L12 10V6Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Restore Database
                            </button>
                            <button type="button" onclick="clearRestoreFile()" class="btn-secondary cancel-btn" id="restore-clear-btn">
                                Cancel
                            </button>
                        </div>
                    </div>
                </form>
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
    /* Modern Database Management Styles */
    .database-management {
        max-width: 1200px;
        margin: 0 auto;
        padding: 24px;
        background: #1a1a1a;
        min-height: 100vh;
        font-family: 'Google Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    /* Modern Header */
    .modern-header {
        background: #2d2d2d;
        padding: 32px;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border: 1px solid #444;
    }

    .header-content {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .header-icon {
        width: 48px;
        height: 48px;
        background: #ffd700;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .header-text h1 {
        color: #ffffff;
        margin: 0 0 4px 0;
        font-size: 28px;
        font-weight: 500;
        line-height: 1.2;
    }

    .header-text p {
        color: #cccccc;
        margin: 0;
        font-size: 14px;
    }

    .theme-toggle {
        background: #444;
        border: none;
        border-radius: 20px;
        padding: 8px 16px;
        color: #ffd700;
        cursor: pointer;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .theme-toggle:hover {
        background: #555;
    }

    /* Modern Card Design */
    .modern-card {
        background: #2d2d2d;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        margin-bottom: 24px;
        border: 1px solid #444;
        overflow: hidden;
    }

    .card-header {
        padding: 24px 24px 16px 24px;
        display: flex;
        align-items: flex-start;
        gap: 16px;
        border-bottom: 1px solid #444;
    }

    .card-icon {
        width: 40px;
        height: 40px;
        background: #ffd700;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .card-title h2 {
        color: #ffffff;
        margin: 0 0 4px 0;
        font-size: 20px;
        font-weight: 500;
        line-height: 1.2;
    }

    .card-title p {
        color: #cccccc;
        margin: 0;
        font-size: 14px;
        line-height: 1.4;
    }

    /* Connection Details */
    .connection-details {
        padding: 24px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 16px;
    }

    .detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px;
        background: #3a3a3a;
        border-radius: 12px;
        border: 1px solid #555;
    }

    .detail-label {
        color: #cccccc;
        font-size: 14px;
        font-weight: 500;
    }

    .detail-value {
        color: #ffd700;
        font-size: 14px;
        font-family: 'Google Sans Mono', monospace;
    }

    .detail-link {
        color: #ffd700;
        text-decoration: none;
        font-size: 14px;
        font-family: 'Google Sans Mono', monospace;
    }

    .detail-link:hover {
        text-decoration: underline;
    }

    .status-connected .detail-item {
        background: #2d4a2d;
        border-color: #34a853;
    }

    .status-disconnected .detail-item {
        background: #4a2d2d;
        border-color: #ea4335;
    }

    /* phpMyAdmin Content */
    .phpmyadmin-content {
        padding: 24px;
    }

    .access-info {
        margin-bottom: 24px;
    }

    .info-section h4 {
        color: #ffffff;
        font-size: 16px;
        font-weight: 500;
        margin: 0 0 8px 0;
    }

    .info-section p {
        color: #cccccc;
        font-size: 14px;
        margin: 0 0 16px 0;
        line-height: 1.4;
    }

    .phpmyadmin-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    /* Statistics Grid */
    .stats-grid {
        padding: 24px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 16px;
    }

    .stat-card {
        background: #3a3a3a;
        border-radius: 12px;
        padding: 20px;
        border: 1px solid #555;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: all 0.2s ease;
    }

    .stat-card:hover {
        box-shadow: 0 4px 16px rgba(255,215,0,0.2);
        transform: translateY(-1px);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        background: #ffd700;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .stat-info h3 {
        color: #ffffff;
        font-size: 24px;
        font-weight: 500;
        margin: 0 0 4px 0;
        line-height: 1.2;
    }

    .stat-info p {
        color: #cccccc;
        font-size: 14px;
        margin: 0;
        line-height: 1.3;
    }

    /* Actions Grid */
    .actions-grid {
        padding: 24px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 16px;
    }

    .action-grid {
        padding: 24px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 16px;
    }

    .action-card {
        background: #3a3a3a;
        border-radius: 12px;
        padding: 24px;
        border: 1px solid #555;
        display: flex;
        gap: 16px;
        transition: all 0.2s ease;
    }

    .action-card:hover {
        box-shadow: 0 4px 16px rgba(255,215,0,0.2);
        transform: translateY(-1px);
    }

    .action-icon {
        width: 48px;
        height: 48px;
        background: #ffd700;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .action-content {
        flex: 1;
    }

    .action-content h3 {
        color: #ffffff;
        font-size: 16px;
        font-weight: 500;
        margin: 0 0 8px 0;
        line-height: 1.2;
    }

    .action-content p {
        color: #cccccc;
        font-size: 14px;
        margin: 0 0 16px 0;
        line-height: 1.4;
    }

    .action-form {
        margin: 0;
    }

    /* Modern Buttons */
    .btn-primary, .btn-secondary, .btn-warning {
        background: #ffd700;
        color: #1a1a1a;
        border: none;
        border-radius: 20px;
        padding: 10px 20px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
        font-family: 'Google Sans', sans-serif;
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

    .btn-warning {
        background: #ea4335;
        color: #ffffff;
    }

    .btn-warning:hover {
        background: #d33b2c;
        box-shadow: 0 4px 16px rgba(234,67,53,0.4);
    }

    /* Activity Tables - Modern Design */
    .activity-table {
        overflow-x: auto;
        background: #2d2d2d;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        border: 1px solid #444;
    }

    .activity-table table {
        width: 100%;
        border-collapse: collapse;
    }

    .activity-table th,
    .activity-table td {
        padding: 16px;
        text-align: left;
        border-bottom: 1px solid #444;
    }

    .activity-table th {
        background: #3a3a3a;
        font-weight: 500;
        color: #ffd700;
        font-size: 14px;
    }

    .activity-table tr:hover {
        background: #3a3a3a;
    }

    .activity-table tr:last-child td {
        border-bottom: none;
    }

    /* Status Badges - Modern Design */
    .status-badge {
        padding: 6px 12px;
        border-radius: 16px;
        font-size: 12px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-pending {
        background: #fef7e0;
        color: #b06000;
    }

    .status-confirmed {
        background: #e8f5e8;
        color: #137333;
    }

    .status-cancelled {
        background: #fce8e6;
        color: #d93025;
    }

    /* URL Lists - Modern Design */
    .url-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .url-item {
        padding: 16px;
        background: #3a3a3a;
        border-radius: 12px;
        border: 1px solid #555;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.2s ease;
    }

    .url-item:hover {
        box-shadow: 0 4px 16px rgba(255,215,0,0.2);
        transform: translateY(-1px);
    }

    .url-item a {
        color: #ffd700;
        text-decoration: none;
        font-family: 'Google Sans Mono', monospace;
        font-size: 14px;
    }

    .url-item a:hover {
        text-decoration: underline;
    }

    .navigation {
        margin-top: 32px;
        text-align: center;
    }

    /* Calendar Integration - Modern Design */
    .calendar-integration-stats {
        background: white;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 24px;
        margin-bottom: 24px;
        border: 1px solid #e8eaed;
    }

    .calendar-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 16px;
    }

    .calendar-stat {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        border: 1px solid #e8eaed;
        transition: all 0.2s ease;
    }

    .calendar-stat:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transform: translateY(-1px);
    }

    .calendar-action {
        background: #3a3a3a;
        border-radius: 12px;
        padding: 20px;
        border: 1px solid #555;
        transition: all 0.2s ease;
    }

    .calendar-action:hover {
        box-shadow: 0 4px 16px rgba(255,215,0,0.2);
        transform: translateY(-1px);
    }

    .calendar-queries, .sync-status {
        background: #2d2d2d;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        padding: 24px;
        margin-bottom: 24px;
        border: 1px solid #444;
    }

    /* Query Sections - Modern Design */
    .query-section {
        margin-bottom: 32px;
    }

    .query-section h3 {
        color: #ffd700;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #555;
        font-size: 18px;
        font-weight: 500;
    }

    .query-item {
        margin-bottom: 24px;
        background: #3a3a3a;
        padding: 24px;
        border-radius: 12px;
        border: 1px solid #555;
    }

    .query-item h4 {
        color: #ffffff;
        margin-bottom: 12px;
        font-size: 16px;
        font-weight: 500;
    }

    .query-code {
        position: relative;
        background: #1a1a1a;
        border-radius: 8px;
        padding: 20px;
        margin-top: 12px;
        border: 1px solid #555;
    }

    .query-code code {
        color: #ffd700;
        font-family: 'Google Sans Mono', 'Courier New', monospace;
        font-size: 13px;
        line-height: 1.5;
        white-space: pre-wrap;
        background: none;
        padding: 0;
    }

    .copy-btn {
        position: absolute;
        top: 12px;
        right: 12px;
        background: #ffd700;
        color: #1a1a1a;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
        transition: all 0.2s ease;
    }

    .copy-btn:hover {
        background: #ffed4e;
    }

    /* phpMyAdmin Direct Link - Modern Design */
    .phpmyadmin-direct-link {
        background: #2d2d2d;
        padding: 24px;
        border-radius: 12px;
        margin-top: 24px;
        text-align: center;
        border: 1px solid #ffd700;
    }

    /* Sync Overview - Modern Design */
    .sync-overview {
        background: #3a3a3a;
        padding: 24px;
        border-radius: 12px;
        margin-bottom: 24px;
        border: 1px solid #555;
    }

    .sync-stat ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sync-stat li {
        padding: 12px 0;
        border-bottom: 1px solid #555;
        color: #cccccc;
        font-size: 14px;
    }

    .sync-stat li:last-child {
        border-bottom: none;
    }

    /* Sync Actions Section - Modern Design */
    .sync-actions-section {
        background: #3a3a2d;
        padding: 24px;
        border-radius: 12px;
        border: 1px solid #ffd700;
        margin-bottom: 24px;
    }

    .sync-action-buttons {
        display: flex;
        gap: 12px;
        margin-top: 16px;
        flex-wrap: wrap;
    }

    /* Sync Success - Modern Design */
    .sync-success {
        background: #2d4a2d;
        padding: 24px;
        border-radius: 12px;
        border: 1px solid #34a853;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    /* Sync Badges - Modern Design */
    .sync-badge {
        padding: 6px 12px;
        border-radius: 16px;
        font-size: 11px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .sync-badge.synced {
        background: #e8f5e8;
        color: #137333;
    }

    .sync-badge.not-synced {
        background: #fce8e6;
        color: #d93025;
    }

    /* Responsive Design - Enhanced */
    @media (max-width: 768px) {
        .database-management {
            padding: 16px;
        }
        
        .modern-header {
            padding: 24px;
            flex-direction: column;
            gap: 16px;
            text-align: center;
        }
        
        .phpmyadmin-card {
            flex-direction: column;
            text-align: center;
        }
        
        .stats-grid, .calendar-stats-grid {
            grid-template-columns: 1fr;
        }
        
        .action-grid, .actions-grid {
            grid-template-columns: 1fr;
        }
        
        .connection-details {
            grid-template-columns: 1fr;
        }
        
        .sync-action-buttons {
            flex-direction: column;
        }
        
        .query-code {
            font-size: 12px;
        }
        
        .activity-table th,
        .activity-table td {
            padding: 12px 8px;
        }
    }

    /* Backup & Restore Section Styles */
    .backup-restore-section {
        margin-top: 24px;
    }

    .backup-restore-content {
        display: grid;
        gap: 32px;
        margin-top: 24px;
    }

    .backup-section, .backups-list-section, .restore-section {
        background: #2d2d2d;
        padding: 24px;
        border-radius: 12px;
        border: 1px solid #555;
    }

    .backup-section h3, .backups-list-section h3, .restore-section h3 {
        color: #ffffff;
        margin-bottom: 12px;
        font-size: 18px;
        font-weight: 600;
    }

    .backup-options {
        display: flex;
        gap: 24px;
        margin: 16px 0;
        flex-wrap: wrap;
    }

    .checkbox-option {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #cccccc;
        cursor: pointer;
        font-size: 14px;
    }

    .checkbox-option input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: #4285f4;
    }

    .backup-btn {
        margin-top: 16px;
        padding: 12px 24px;
        font-size: 14px;
        font-weight: 500;
    }

    /* Restore Upload Styles */
    .file-upload-area {
        margin: 16px 0;
    }

    .upload-zone {
        border: 2px dashed #4285f4;
        border-radius: 12px;
        padding: 40px 20px;
        text-align: center;
        background: #2a2a2a;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .upload-zone:hover {
        border-color: #5a9cff;
        background: #333;
    }

    .upload-zone.dragover {
        border-color: #5a9cff;
        background: #333;
        transform: scale(1.02);
    }

    .upload-icon {
        margin-bottom: 16px;
        opacity: 0.7;
    }

    .upload-zone h4 {
        color: #ffffff;
        margin-bottom: 8px;
        font-size: 16px;
        font-weight: 500;
    }

    .upload-zone p {
        color: #cccccc;
        font-size: 14px;
        margin: 0;
    }

    .restore-options {
        background: #3a3a3a;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #555;
        margin-top: 16px;
    }

    .restore-settings {
        margin: 16px 0;
    }

    .restore-actions {
        display: flex;
        gap: 12px;
        margin-top: 16px;
    }

    .restore-btn {
        padding: 12px 24px;
        font-size: 14px;
        font-weight: 500;
    }

    .restore-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .backups-grid {
        display: grid;
        gap: 16px;
        margin-top: 16px;
    }

    .backup-item {
        background: #3a3a3a;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #555;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .backup-info h4 {
        color: #ffffff;
        margin: 0 0 8px 0;
        font-size: 16px;
        font-weight: 500;
    }

    .backup-meta {
        color: #9aa0a6;
        font-size: 13px;
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
    }

    .backup-actions {
        display: flex;
        gap: 8px;
    }

    .btn-small {
        padding: 6px 12px;
        font-size: 12px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .btn-download {
        background: #34a853;
        color: white;
    }

    .btn-download:hover {
        background: #2d8f47;
    }

    .btn-delete {
        background: #ea4335;
        color: white;
    }

    .btn-delete:hover {
        background: #d33b2c;
    }

    .loading-state {
        text-align: center;
        padding: 40px;
        color: #9aa0a6;
    }

    .loading-state svg {
        margin-bottom: 16px;
    }

    .info-card {
        background: #3a3a3a;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #555;
        display: flex;
        gap: 16px;
    }

    .info-icon {
        flex-shrink: 0;
    }

    .info-content h4 {
        color: #ffffff;
        margin: 0 0 12px 0;
        font-size: 16px;
        font-weight: 500;
    }

    .info-content p {
        color: #cccccc;
        margin-bottom: 12px;
        line-height: 1.5;
    }

    .info-content ol {
        color: #cccccc;
        padding-left: 20px;
        line-height: 1.6;
    }

    .info-content li {
        margin-bottom: 8px;
    }
</style>

<script>
// Toggle backup section visibility
function toggleBackupSection() {
    const section = document.getElementById('backup-restore-section');
    if (section.style.display === 'none' || section.style.display === '') {
        section.style.display = 'block';
        loadBackups();
    } else {
        section.style.display = 'none';
    }
}

// Load available backups via AJAX
function loadBackups() {
    const grid = document.getElementById('backups-grid');
    
    fetch('{{ route("admin.database.backups") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayBackups(data.backups);
            } else {
                grid.innerHTML = '<div class="loading-state"><p>Error loading backups: ' + data.message + '</p></div>';
            }
        })
        .catch(error => {
            grid.innerHTML = '<div class="loading-state"><p>Error loading backups</p></div>';
        });
}

// Display backups in the grid
function displayBackups(backups) {
    const grid = document.getElementById('backups-grid');
    
    if (backups.length === 0) {
        grid.innerHTML = '<div class="loading-state"><p>No backups available</p></div>';
        return;
    }
    
    let html = '';
    backups.forEach(backup => {
        html += `
            <div class="backup-item">
                <div class="backup-info">
                    <h4>${backup.name}</h4>
                    <div class="backup-meta">
                        <span>📅 ${backup.date}</span>
                        <span>📦 ${backup.size}</span>
                    </div>
                </div>
                <div class="backup-actions">
                    <a href="/admin/database/backup/download/${backup.name}" class="btn-small btn-download">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                            <path d="M21 15V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <polyline points="7,10 12,15 17,10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <line x1="12" y1="15" x2="12" y2="3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Download
                    </a>
                    <button onclick="deleteBackup('${backup.name}')" class="btn-small btn-delete">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none">
                            <path d="M3 6H5H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Delete
                    </button>
                </div>
            </div>
        `;
    });
    
    grid.innerHTML = html;
}

// Create backup
function createBackup() {
    const btn = document.getElementById('backup-btn');
    const originalText = btn.innerHTML;
    
    // Show loading state
    btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>Creating Backup...';
    btn.disabled = true;
    
    // Get form data
    const form = document.getElementById('backup-form');
    const formData = new FormData(form);
    
    fetch('{{ route("admin.database.backup") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            alert('Backup created successfully! Size: ' + data.backup_size);
            
            // Trigger download
            window.location.href = `/admin/database/backup/download/${data.backup_name}`;
            
            // Reload backups list
            loadBackups();
        } else {
            alert('Backup failed: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error creating backup. Please try again.');
    })
    .finally(() => {
        // Restore button state
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

// Delete backup
function deleteBackup(backupName) {
    if (!confirm('Are you sure you want to delete this backup? This action cannot be undone.')) {
        return;
    }
    
    fetch(`/admin/database/backup/delete/${backupName}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadBackups(); // Reload the backups list
        } else {
            alert('Error deleting backup: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error deleting backup');
    });
}

// Restore functionality
let selectedRestoreFile = null;

function initializeRestore() {
    const uploadZone = document.getElementById('restore-upload-zone');
    const fileInput = document.getElementById('restore-file');
    const confirmCheckbox = document.getElementById('confirm-restore');
    const restoreBtn = document.getElementById('restore-btn');
    
    // Click to upload
    uploadZone.addEventListener('click', () => {
        fileInput.click();
    });
    
    // File input change
    fileInput.addEventListener('change', (e) => {
        handleRestoreFileSelect(e.target.files[0]);
    });
    
    // Drag and drop
    uploadZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadZone.classList.add('dragover');
    });
    
    uploadZone.addEventListener('dragleave', () => {
        uploadZone.classList.remove('dragover');
    });
    
    uploadZone.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadZone.classList.remove('dragover');
        handleRestoreFileSelect(e.dataTransfer.files[0]);
    });
    
    // Confirm checkbox change
    confirmCheckbox.addEventListener('change', () => {
        restoreBtn.disabled = !confirmCheckbox.checked || !selectedRestoreFile;
    });
}

function handleRestoreFileSelect(file) {
    if (!file) return;
    
    // Validate file type
    const allowedTypes = ['.sql', '.zip', '.tar', '.gz'];
    const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
    
    if (!allowedTypes.includes(fileExtension)) {
        alert('Please select a valid backup file (.sql, .zip, .tar, .gz)');
        return;
    }
    
    selectedRestoreFile = file;
    const fileInfo = document.getElementById('restore-file-info');
    const restoreOptions = document.getElementById('restore-options');
    const confirmCheckbox = document.getElementById('confirm-restore');
    const restoreBtn = document.getElementById('restore-btn');
    
    // Show file info
    fileInfo.innerHTML = `
        <div class="file-details">
            <strong>Selected file:</strong> ${file.name}<br>
            <strong>Size:</strong> ${(file.size / 1024 / 1024).toFixed(2)} MB<br>
            <strong>Type:</strong> ${fileExtension}
        </div>
    `;
    
    // Show restore options
    restoreOptions.style.display = 'block';
    
    // Reset confirmation
    confirmCheckbox.checked = false;
    restoreBtn.disabled = true;
}

function restoreDatabase() {
    if (!selectedRestoreFile) {
        alert('Please select a backup file first.');
        return;
    }
    
    const confirmCheckbox = document.getElementById('confirm-restore');
    if (!confirmCheckbox.checked) {
        alert('Please confirm that you understand this will overwrite the current database.');
        return;
    }
    
    const btn = document.getElementById('restore-btn');
    const originalText = btn.innerHTML;
    
    // Show loading state
    btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 6v6l4 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>Restoring...';
    btn.disabled = true;
    
    // Prepare form data
    const formData = new FormData();
    formData.append('backup_file', selectedRestoreFile);
    formData.append('confirm_overwrite', '1');
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
    
    fetch('{{ route("admin.database.restore") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Database restored successfully!');
            clearRestoreFile();
            // Optionally reload the page to reflect changes
            window.location.reload();
        } else {
            alert('Restore failed: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error restoring database. Please try again.');
    })
    .finally(() => {
        // Restore button state
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

function clearRestoreFile() {
    selectedRestoreFile = null;
    document.getElementById('restore-file').value = '';
    document.getElementById('restore-options').style.display = 'none';
    document.getElementById('confirm-restore').checked = false;
    document.getElementById('restore-btn').disabled = true;
}

// Existing functions...
function showRecentBookings() {
    const section = document.getElementById('recent-bookings');
    section.style.display = section.style.display === 'none' ? 'block' : 'none';
}

function openCalendarQueries() {
    const section = document.getElementById('calendar-queries');
    section.style.display = section.style.display === 'none' ? 'block' : 'none';
}

function showSyncStatus() {
    const section = document.getElementById('sync-status');
    if (section) {
        section.style.display = section.style.display === 'none' ? 'block' : 'none';
    }
}

function copyQuery(button) {
    const code = button.previousElementSibling;
    const text = code.textContent;
    
    navigator.clipboard.writeText(text).then(() => {
        const originalText = button.textContent;
        button.textContent = '✓ Copied!';
        button.style.background = '#34a853';
        
        setTimeout(() => {
            button.textContent = originalText;
            button.style.background = '#ffd700';
        }, 2000);
    });
}

function toggleTheme() {
    // Theme toggle functionality
    document.body.classList.toggle('light-theme');
}

// Initialize restore functionality when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeRestore();
});
</script>

@endsection