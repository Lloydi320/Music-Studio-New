@extends('layouts.admin')

@section('title', 'Google Calendar Integration')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="calendar-header">
        <div class="header-content">
            <div class="header-icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" stroke="#4285f4" stroke-width="2" fill="#e8f0fe"/>
                    <line x1="16" y1="2" x2="16" y2="6" stroke="#4285f4" stroke-width="2"/>
                    <line x1="8" y1="2" x2="8" y2="6" stroke="#4285f4" stroke-width="2"/>
                    <line x1="3" y1="10" x2="21" y2="10" stroke="#4285f4" stroke-width="2"/>
                </svg>
            </div>
            <div class="header-text">
                <h1>Google Calendar Integration</h1>
                <p>Sync your studio bookings automatically for easier schedule management.</p>
            </div>
        </div>
        <div class="header-actions">
            <button class="toggle-theme-btn" onclick="toggleTheme()">Toggle theme</button>
            <a href="https://calendar.google.com" target="_blank" class="open-calendar-btn">Open Calendar</a>
        </div>
    </div>

    <!-- Connection Status -->
    <div class="calendar-main">
        @if($user->hasGoogleCalendarAccess())
            <div class="connection-card connected">
                <div class="connection-status">
                    <div class="status-indicator connected"></div>
                    <h2>Google Calendar Connected</h2>
                    <p>Your bookings are automatically synced to Google Calendar.</p>
                </div>
                <div class="calendar-details">
                    <div class="detail-item">
                        <span class="detail-label">Calendar ID:</span>
                        <code class="calendar-id">{{ Str::limit($user->google_calendar_id, 40, '...') }}</code>
                    </div>
                    @php
                        $lastSync = \Carbon\Carbon::now()->subMinutes(2);
                    @endphp
                    <div class="detail-item">
                        <span class="detail-label">Last sync:</span>
                        <span class="sync-time">{{ $lastSync->diffForHumans() }}</span>
                    </div>
                </div>
                <div class="connection-actions">
                    <form method="POST" action="{{ route('admin.calendar.disconnect') }}" 
                          onsubmit="return confirm('Are you sure you want to disconnect Google Calendar?')">
                        @csrf
                        <button type="submit" class="disconnect-btn">Disconnect</button>
                    </form>
                </div>
            </div>
        @else
            <div class="connection-card disconnected">
                <div class="connection-status">
                    <div class="status-indicator disconnected"></div>
                    <h2>Google Calendar Not Connected</h2>
                    <p>Connect your Google Calendar to automatically sync studio bookings.</p>
                </div>
                <div class="benefits-list">
                    <div class="benefit-item">
                        <span class="benefit-icon">📅</span>
                        <span>Automatic event creation for new bookings</span>
                    </div>
                    <div class="benefit-item">
                        <span class="benefit-icon">🔔</span>
                        <span>Email and push notifications</span>
                    </div>
                    <div class="benefit-item">
                        <span class="benefit-icon">⚡</span>
                        <span>Easy schedule management</span>
                    </div>
                    <div class="benefit-item">
                        <span class="benefit-icon">👥</span>
                        <span>Client information in calendar events</span>
                    </div>
                </div>
                <div class="connection-actions">
                    <a href="{{ route('admin.calendar.connect') }}" class="connect-btn">
                        Connect Google Calendar
                    </a>
                </div>
            </div>
        @endif

    @if($user->hasGoogleCalendarAccess())
    <!-- Sync Actions and Quick Actions -->
    <div class="actions-grid">
        <!-- Sync Existing Bookings -->
        <div class="action-card sync-card">
            <div class="card-icon">
                <div class="sync-icon">✓</div>
            </div>
            <div class="card-content">
                <h3>All bookings are synced</h3>
                <p>All confirmed bookings have been synced to Google Calendar.</p>
                @php
                    $pendingBookings = \App\Models\Booking::where('status', 'confirmed')
                        ->whereNull('google_event_id')
                        ->count();
                @endphp
                <div class="sync-status">
                    <span class="status-badge synced">{{ $pendingBookings }} bookings need syncing</span>
                </div>
            </div>
            <div class="card-actions">
                <form method="POST" action="{{ route('admin.calendar.sync-all') }}">
                    @csrf
                    <button type="submit" class="sync-btn" 
                            {{ $pendingBookings === 0 ? 'disabled' : '' }}>
                        Sync Now
                    </button>
                </form>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="action-card quick-actions-card">
            <div class="card-header">
                <h3>Quick Actions</h3>
                <span class="shortcuts-label">Shortcuts</span>
            </div>
            <div class="quick-actions-list">
                <a href="{{ route('admin.bookings.create') }}" class="quick-action">
                    <span class="action-icon">📝</span>
                    <span>New Booking</span>
                </a>
                <a href="{{ route('admin.bookings') }}" class="quick-action">
                    <span class="action-icon">📋</span>
                    <span>Open Bookings</span>
                </a>
                <a href="#" class="quick-action" onclick="exportCalendar()">
                    <span class="action-icon">📤</span>
                    <span>Export</span>
                </a>
                <a href="#" class="quick-action" onclick="openSettings()">
                    <span class="action-icon">⚙️</span>
                    <span>Settings</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Unified Calendar View -->
    <div class="calendar-view">
        <div class="overview-header">
            <h2>Calendar Overview</h2>
            <div class="overview-meta">
                <span class="overview-subtitle">Upcoming Events (Next 4 Weeks)</span>
                <div class="view-controls">
                    <span class="view-label">View:</span>
                    <div class="view-toggle">
                        <button class="view-btn active" data-view="grid">Grid</button>
                        <button class="view-btn" data-view="list">List</button>
                    </div>
                </div>
            </div>
        </div>
        
        @if($user->hasGoogleCalendarAccess() && count($upcomingEvents) > 0)
            <div class="calendar-section">
                <h3>🔮 Upcoming Events (Next 4 Weeks)</h3>
                <div class="events-grid" id="events-grid">
                    @foreach($upcomingEvents as $event)
                    <div class="event-card {{ $event['is_studio_booking'] ? 'studio-event' : 'other-event' }}">
                        <div class="event-header">
                            <div class="event-date-time">
                                <div class="event-date">
                                    {{ $event['start']->format('M j') }} • 
                                    {{ $event['start']->format('H:i') }}
                                </div>
                                <div class="event-duration">
                                    {{ $event['start']->format('g:i A') }}
                                </div>
                            </div>
                            <div class="event-type-badge {{ $event['is_studio_booking'] ? 'studio' : 'other' }}">
                                {{ $event['is_studio_booking'] ? 'Studio' : 'Other' }}
                            </div>
                        </div>
                        <div class="event-content">
                            <h4 class="event-title">{{ $event['title'] }}</h4>
                            @if($event['attendees'])
                                <div class="event-client">
                                    <span class="client-icon">👤</span>
                                    <span>{{ count($event['attendees']) }} attendee(s)</span>
                                </div>
                            @endif
                            @if($event['location'])
                                <div class="event-location">
                                    <span class="location-icon">📍</span>
                                    <span>{{ $event['location'] }}</span>
                                </div>
                            @endif
                            <div class="event-meta">
                                <span class="event-duration-text">
                                    {{ $event['duration'] }}h duration
                                </span>
                            </div>
                        </div>
                        @if($event['is_studio_booking'])
                            <div class="event-actions">
                                <a href="#" class="event-view-btn">View Details</a>
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- System Bookings vs Calendar Sync Status -->
        <div class="sync-comparison">
            <h3>🔄 Booking Sync Status</h3>
            <div class="comparison-grid">
                <div class="comparison-section">
                    <h4>📋 System Bookings</h4>
                    @if($systemBookings->count() > 0)
                        <div class="bookings-list">
                            @foreach($systemBookings as $booking)
                            <div class="booking-item {{ $booking->google_event_id ? 'synced' : 'unsynced' }}">
                                <div class="booking-info">
                                    <div class="booking-title">
                                        <strong>{{ $booking->user->name }}</strong>
                                        <span class="booking-ref">{{ $booking->reference }}</span>
                                    </div>
                                    <div class="booking-time">
                                        {{ \Carbon\Carbon::parse($booking->date)->format('M d, Y') }} 
                                        at {{ $booking->time_slot }} ({{ $booking->duration }}h)
                                    </div>
                                </div>
                                <div class="sync-status">
                                    @if($booking->google_event_id)
                                        <span class="sync-badge synced">✅ Synced</span>
                                    @else
                                        <span class="sync-badge unsynced">⏳ Pending</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="no-bookings">
                            <p>No upcoming system bookings found.</p>
                        </div>
                    @endif
                </div>

                @if($user->hasGoogleCalendarAccess())
                <div class="comparison-section">
                    <h4>📅 Google Calendar Events</h4>
                    @if(count($calendarEvents) > 0)
                        <div class="calendar-stats">
                            <div class="stat-item">
                                <span class="stat-number">{{ count($calendarEvents) }}</span>
                                <span class="stat-label">Total Events</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number">{{ count(array_filter($calendarEvents, function($e) { return $e['is_studio_booking']; })) }}</span>
                                <span class="stat-label">Studio Sessions</span>
                            </div>
                            <div class="stat-item">
                                <span class="stat-number">{{ count(array_filter($calendarEvents, function($e) { return !$e['is_studio_booking']; })) }}</span>
                                <span class="stat-label">Other Events</span>
                            </div>
                        </div>
                        <div class="calendar-actions">
                            <a href="https://calendar.google.com" target="_blank" class="btn btn-info">
                                🔗 Open Google Calendar
                            </a>
                        </div>
                    @else
                        <div class="no-events">
                            <p>No events found in your Google Calendar for the next 3 months.</p>
                        </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Instructions -->
    <div class="instructions">
        <h2>How It Works</h2>
        <div class="instruction-steps">
            <div class="step">
                <div class="step-number">1</div>
                <div class="step-content">
                    <h3>Connect Your Google Account</h3>
                    <p>Click "Connect Google Calendar" to authorize access to your Google Calendar.</p>
                </div>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <div class="step-content">
                    <h3>Automatic Sync</h3>
                    <p>New bookings will automatically create events in your "Music Studio Bookings" calendar.</p>
                </div>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <div class="step-content">
                    <h3>Stay Updated</h3>
                    <p>Get notifications on your phone and computer for upcoming studio sessions.</p>
                </div>
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

<style>
/* Google Calendar UI Styles */
.admin-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 24px;
    background: #f8f9fa;
    min-height: 100vh;
}

/* Header Styles */
.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
    padding: 24px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.header-content {
    display: flex;
    align-items: center;
    gap: 16px;
}

.header-icon svg {
    width: 40px;
    height: 40px;
}

.header-text h1 {
    font-size: 24px;
    font-weight: 500;
    color: #202124;
    margin: 0 0 4px 0;
}

.header-text p {
    font-size: 14px;
    color: #5f6368;
    margin: 0;
}

.header-actions {
    display: flex;
    gap: 12px;
}

.toggle-theme-btn, .open-calendar-btn {
    padding: 8px 16px;
    border: 1px solid #dadce0;
    border-radius: 6px;
    background: white;
    color: #1a73e8;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.open-calendar-btn {
    background: #1a73e8;
    color: white;
    border-color: #1a73e8;
}

.toggle-theme-btn:hover {
    background: #f8f9fa;
}

.open-calendar-btn:hover {
    background: #1557b0;
}

/* Connection Card Styles */
.calendar-main {
    margin-bottom: 24px;
}

.connection-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 24px;
    margin-bottom: 24px;
}

.connection-status {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 20px;
}

.status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    flex-shrink: 0;
}

.status-indicator.connected {
    background: #34a853;
}

.status-indicator.disconnected {
    background: #ea4335;
}

.connection-status h2 {
    font-size: 20px;
    font-weight: 500;
    color: #202124;
    margin: 0;
}

.connection-status p {
    color: #5f6368;
    font-size: 14px;
    margin: 4px 0 0 0;
}

.calendar-details {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 20px;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 8px;
}

.detail-item:last-child {
    margin-bottom: 0;
}

.detail-label {
    font-weight: 500;
    color: #5f6368;
    min-width: 80px;
}

.calendar-id {
    background: #e8f0fe;
    color: #1a73e8;
    padding: 4px 8px;
    border-radius: 4px;
    font-family: monospace;
    font-size: 12px;
}

.sync-time {
    color: #34a853;
    font-weight: 500;
}

.benefits-list {
    margin: 20px 0;
}

.benefit-item {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
    color: #5f6368;
}

.benefit-icon {
    font-size: 16px;
}

.connection-actions {
    display: flex;
    gap: 12px;
}

.connect-btn {
    background: #1a73e8;
    color: white;
    padding: 12px 24px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    transition: background 0.2s;
}

.connect-btn:hover {
    background: #1557b0;
}

.disconnect-btn {
    background: #ea4335;
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s;
}

.disconnect-btn:hover {
    background: #d33b2c;
}

.status-icon {
    font-size: 48px;
    font-weight: bold;
}

.status-card.connected .status-icon {
    color: #27ae60;
}

.status-card.disconnected .status-icon {
    color: #e74c3c;
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

.calendar-info {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
    font-family: monospace;
    font-size: 12px;
}

.benefits {
    list-style: none;
    padding: 0;
    margin: 15px 0 0 0;
}

.benefits li {
    padding: 5px 0;
    color: #666;
}

.benefits li:before {
    content: "✓ ";
    color: #27ae60;
    font-weight: bold;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: background 0.3s;
}

.btn-primary { background: #4285f4; color: white; }
.btn-primary:hover { background: #3367d6; }
.btn-success { background: #27ae60; color: white; }
.btn-success:hover { background: #229954; }
.btn-danger { background: #e74c3c; color: white; }
.btn-danger:hover { background: #c0392b; }
.btn-secondary { background: #95a5a6; color: white; }
.btn-secondary:hover { background: #7f8c8d; }

/* Actions Grid */
.actions-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin-bottom: 24px;
}

.action-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 24px;
}

.sync-card {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.card-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    background: #e8f5e8;
    border-radius: 50%;
    margin-bottom: 16px;
}

.sync-icon {
    font-size: 24px;
    color: #34a853;
}

.card-content h3 {
    font-size: 18px;
    font-weight: 500;
    color: #202124;
    margin: 0 0 8px 0;
}

.card-content p {
    color: #5f6368;
    font-size: 14px;
    margin: 0 0 12px 0;
}

.sync-status {
    margin-bottom: 16px;
}

.status-badge {
    padding: 4px 12px;
    border-radius: 16px;
    font-size: 12px;
    font-weight: 500;
}

.status-badge.synced {
    background: #e8f5e8;
    color: #137333;
}

.sync-btn {
    background: #1a73e8;
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s;
}

.sync-btn:hover:not(:disabled) {
    background: #1557b0;
}

.sync-btn:disabled {
    background: #dadce0;
    color: #5f6368;
    cursor: not-allowed;
}

.quick-actions-card .card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.card-header h3 {
    font-size: 18px;
    font-weight: 500;
    color: #202124;
    margin: 0;
}

.shortcuts-label {
    font-size: 12px;
    color: #5f6368;
    background: #f8f9fa;
    padding: 4px 8px;
    border-radius: 12px;
}

.quick-actions-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.quick-action {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    border-radius: 8px;
    text-decoration: none;
    color: #5f6368;
    transition: background 0.2s;
}

.quick-action:hover {
    background: #f8f9fa;
    color: #202124;
}

.action-icon {
    font-size: 16px;
}

/* Calendar View */
.calendar-view, .instructions {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 24px;
    margin-bottom: 24px;
}

.overview-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 1px solid #e0e0e0;
}

.overview-header h2 {
    font-size: 20px;
    font-weight: 500;
    color: #202124;
    margin: 0;
}

.overview-meta {
    text-align: right;
}

.overview-subtitle {
    display: block;
    font-size: 14px;
    color: #5f6368;
    margin-bottom: 8px;
}

.view-controls {
    display: flex;
    align-items: center;
    gap: 8px;
}

.view-label {
    font-size: 14px;
    color: #5f6368;
}

.view-toggle {
    display: flex;
    border: 1px solid #dadce0;
    border-radius: 6px;
    overflow: hidden;
}

.view-btn {
    padding: 6px 12px;
    border: none;
    background: white;
    color: #5f6368;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s;
}

.view-btn.active {
    background: #1a73e8;
    color: white;
}

.view-btn:hover:not(.active) {
    background: #f8f9fa;
}

.sync-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    gap: 20px;
}

.sync-stats {
    margin-top: 10px;
    color: #666;
}

.sync-stats strong {
    color: #e74c3c;
    font-size: 18px;
}

.all-synced .sync-complete {
    color: #27ae60;
    font-weight: bold;
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.activity-icon {
    font-size: 24px;
}

.activity-content {
    flex: 1;
}

.activity-title {
    margin-bottom: 5px;
}

.activity-details {
    color: #666;
    font-size: 14px;
    margin-bottom: 3px;
}

.activity-time {
    color: #999;
    font-size: 12px;
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

.instruction-steps {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.step {
    display: flex;
    align-items: flex-start;
    gap: 15px;
}

.step-number {
    background: #3498db;
    color: white;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    flex-shrink: 0;
}

.step-content h3 {
    margin: 0 0 8px 0;
    color: #333;
}

.step-content p {
    color: #666;
    margin: 0;
}

.no-activity {
    text-align: center;
    padding: 40px;
    color: #666;
}

/* New Calendar View Styles */
.calendar-section {
    margin-bottom: 30px;
}

/* Calendar Section Styles */
.calendar-section {
    margin-bottom: 32px;
}

.calendar-section h3 {
    font-size: 16px;
    font-weight: 500;
    color: #202124;
    margin: 0 0 16px 0;
}

.events-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 16px;
}

.event-card {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 16px;
    transition: all 0.2s;
    position: relative;
}

.event-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border-color: #dadce0;
}

.event-card.studio-event {
    border-left: 4px solid #ea4335;
}

.event-card.other-event {
    border-left: 4px solid #1a73e8;
}

.event-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
}

.event-date-time {
    flex: 1;
}

.event-date {
    font-size: 14px;
    font-weight: 500;
    color: #202124;
    margin-bottom: 2px;
}

.event-duration {
    font-size: 12px;
    color: #5f6368;
}

.event-type-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
    text-transform: uppercase;
}

.event-type-badge.studio {
    background: #fce8e6;
    color: #d93025;
}

.event-type-badge.other {
    background: #e8f0fe;
    color: #1a73e8;
}

.event-content {
    margin-bottom: 12px;
}

.event-title {
    font-size: 16px;
    font-weight: 500;
    color: #202124;
    margin: 0 0 8px 0;
}

.event-client, .event-location {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #5f6368;
    margin-bottom: 4px;
}

.client-icon, .location-icon {
    font-size: 12px;
}

.event-meta {
    font-size: 12px;
    color: #5f6368;
}

.event-actions {
    display: flex;
    justify-content: flex-end;
}

.event-view-btn {
    color: #1a73e8;
    text-decoration: none;
    font-size: 12px;
    font-weight: 500;
    padding: 4px 8px;
    border-radius: 4px;
    transition: background 0.2s;
}

.event-view-btn:hover {
    background: #f8f9fa;
}

.event-time {
    text-align: center;
    min-width: 60px;
}

.event-date {
    font-weight: bold;
    color: #333;
    font-size: 14px;
}

.event-hour {
    color: #666;
    font-size: 12px;
}

.event-details {
    flex: 1;
}

.event-title {
    font-weight: bold;
    color: #333;
    margin-bottom: 5px;
}

.event-duration, .event-attendees, .event-location {
    font-size: 12px;
    color: #666;
    margin-bottom: 3px;
}

.event-type {
    align-self: flex-start;
}

.type-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: bold;
}

.type-badge.studio {
    background: #fee;
    color: #e74c3c;
}

.type-badge.other {
    background: #eff8ff;
    color: #3498db;
}

.sync-comparison {
    margin-top: 30px;
}

.comparison-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-top: 20px;
}

.comparison-section {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
}

.comparison-section h4 {
    margin: 0 0 15px 0;
    color: #333;
    border-bottom: 2px solid #ddd;
    padding-bottom: 8px;
}

.bookings-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.booking-item {
    background: white;
    border-radius: 6px;
    padding: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-left: 3px solid #ddd;
}

.booking-item.synced {
    border-left-color: #27ae60;
}

.booking-item.unsynced {
    border-left-color: #f39c12;
}

.booking-title {
    font-weight: bold;
    margin-bottom: 3px;
}

.booking-ref {
    font-size: 12px;
    color: #666;
    margin-left: 8px;
}

.booking-time {
    font-size: 12px;
    color: #666;
}

.sync-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: bold;
}

.sync-badge.synced {
    background: #d4edda;
    color: #155724;
}

.sync-badge.unsynced {
    background: #fff3cd;
    color: #856404;
}

.calendar-stats {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
}

.stat-item {
    text-align: center;
    background: white;
    padding: 12px;
    border-radius: 6px;
    flex: 1;
}

.stat-number {
    display: block;
    font-size: 24px;
    font-weight: bold;
    color: #3498db;
}

.stat-label {
    font-size: 12px;
    color: #666;
}

.calendar-actions {
    text-align: center;
}

.btn-info {
    background: #17a2b8;
    color: white;
}

.btn-info:hover {
    background: #138496;
}

.no-bookings, .no-events {
    text-align: center;
    padding: 20px;
    color: #666;
    font-style: italic;
}

.navigation {
    margin-top: 30px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .admin-content {
        padding: 16px;
    }
    
    .calendar-header {
        flex-direction: column;
        gap: 16px;
        text-align: center;
    }
    
    .header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .actions-grid {
        grid-template-columns: 1fr;
    }
    
    .overview-header {
        flex-direction: column;
        gap: 16px;
        text-align: left;
    }
    
    .overview-meta {
        text-align: left;
    }
    
    .events-grid {
        grid-template-columns: 1fr;
    }
    
    .comparison-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .calendar-stats {
        flex-direction: column;
        gap: 8px;
    }
    
    .booking-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .connection-actions {
        flex-direction: column;
    }
    
    .header-actions {
        flex-direction: column;
        width: 100%;
    }
}
</style>

<script>
// Theme toggle functionality
function toggleTheme() {
    const body = document.body;
    const isDark = body.classList.contains('dark-theme');
    
    if (isDark) {
        body.classList.remove('dark-theme');
        localStorage.setItem('theme', 'light');
    } else {
        body.classList.add('dark-theme');
        localStorage.setItem('theme', 'dark');
    }
}

// Load saved theme
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
    }
});

// View toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const viewButtons = document.querySelectorAll('.view-btn');
    const eventsGrid = document.getElementById('events-grid');
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            viewButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            const view = this.dataset.view;
            if (eventsGrid) {
                if (view === 'list') {
                    eventsGrid.style.gridTemplateColumns = '1fr';
                    eventsGrid.style.gap = '8px';
                } else {
                    eventsGrid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(320px, 1fr))';
                    eventsGrid.style.gap = '16px';
                }
            }
        });
    });
});

// Quick action functions
function exportCalendar() {
    alert('Export functionality would be implemented here');
}

function openSettings() {
    alert('Settings panel would open here');
}

// Auto-refresh calendar data every 5 minutes
setInterval(function() {
    // This would typically make an AJAX call to refresh calendar data
    console.log('Auto-refreshing calendar data...');
}, 300000);
</script>

@endsection