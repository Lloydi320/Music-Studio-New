@extends('layouts.admin')

@section('title', 'Calendar Integration')

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
                <h1>Calendar Integration</h1>
                <p>Subscribe to bookings in Google Calendar via ICS (no login).</p>
            </div>
        </div>
        <div class="header-actions">
            <a href="https://calendar.google.com" target="_blank" class="open-calendar-btn">Open Calendar</a>
        </div>
    </div>

    <!-- Connection Status -->
    <div class="calendar-main">
        <div class="connection-card connected">
            <div class="connection-status">
                <div class="status-indicator connected"></div>
                <h2>Calendar Subscription Enabled</h2>
                <p>Use this ICS link; booked sessions will reflect automatically.</p>
            </div>
            <div class="calendar-details">
                <div class="detail-item">
                    <span class="detail-label">ICS Feed URL:</span>
                    <code class="calendar-id">{{ route('calendar.feed') }}@if(env('ICS_FEED_TOKEN'))?token={{ env('ICS_FEED_TOKEN') }}@endif</code>
                </div>
                <div class="detail-item">
                    <span class="detail-label">How to add:</span>
                    <span>Google Calendar → Other calendars → Add by URL → paste the link.</span>
                </div>
            </div>
        </div>

        {{-- ICS Subscription Card (actions added) --}}
        <div class="card" style="margin-top: 16px;">
            <div class="card-header">ICS Feed Actions</div>
            <div class="card-body">
                <p>Use these quick actions for the ICS feed:</p>
                <div style="display:flex; gap:8px; flex-wrap:wrap;">
                    <button type="button" class="btn btn-primary" onclick="copyIcsUrl()">Copy ICS Link</button>
                    <a class="btn btn-secondary" href="{{ route('calendar.export') }}@if(env('ICS_FEED_TOKEN'))?token={{ env('ICS_FEED_TOKEN') }}@endif">Download ICS File</a>
                </div>
                <small class="text-muted d-block" style="margin-top:8px;">Copy the link to subscribe in Google Calendar, or download to import a static snapshot.</small>
            </div>
        </div>

        {{-- Google OAuth connection UI removed in favor of ICS subscription --}}

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
                    <h3>Copy Your ICS Feed Link</h3>
                    <p>The link is provided above; it contains all confirmed bookings.</p>
                </div>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <div class="step-content">
                    <h3>Subscribe in Google Calendar</h3>
                    <p>In Google Calendar, go to "Other calendars," click "Add by URL," and paste the link.</p>
                </div>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <div class="step-content">
                    <h3>Automatic Sync</h3>
                    <p>Your calendar will now automatically sync, showing all confirmed bookings.</p>
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
/* Global Font and Text Styling */
* {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* Improved Text Visibility */
body, .admin-content {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #e0e0e0;
    line-height: 1.6;
}

h1, h2, h3, h4, h5, h6 {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-weight: 600;
    color: #ffd700;
    text-shadow: 0 1px 2px rgba(0,0,0,0.5);
}

p, span, div {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #e0e0e0;
}

/* Google Calendar UI Styles */
.admin-content {
    width: 100%;
    margin: 0;
    padding: 24px;
    background: #1a1a1a;
    min-height: 100vh;
}

/* Header Styles */
.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
    padding: 24px;
    background: #2d2d2d;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(255,215,0,0.1);
    border: 1px solid #444;
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
    font-size: 28px;
    font-weight: 700;
    color: #ffd700;
    margin: 0 0 4px 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    text-shadow: 0 2px 4px rgba(0,0,0,0.6);
}

.header-text p {
    font-size: 16px;
    color: #d0d0d0;
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-weight: 400;
}

.header-actions {
    display: flex;
    gap: 12px;
}

.toggle-theme-btn, .open-calendar-btn {
    padding: 10px 18px;
    border: 1px solid #555;
    border-radius: 6px;
    background: #3a3a3a;
    color: #ffd700;
    text-decoration: none;
    font-size: 15px;
    font-weight: 600;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    cursor: pointer;
    transition: all 0.2s;
    text-shadow: 0 1px 2px rgba(0,0,0,0.5);
}

.open-calendar-btn {
    background: #ffd700;
    color: #1a1a1a;
    border-color: #ffd700;
}

.toggle-theme-btn:hover {
    background: #4a4a4a;
    border-color: #ffd700;
}

.open-calendar-btn:hover {
    background: #ffed4e;
    transform: translateY(-1px);
}

/* Connection Card Styles */
.calendar-main {
    margin-bottom: 24px;
}

.connection-card {
    background: #2d2d2d;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(255,215,0,0.1);
    padding: 24px;
    margin-bottom: 24px;
    border: 1px solid #444;
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
    color: #ffd700;
    margin: 0;
}

.connection-status p {
    color: #b0b0b0;
    font-size: 14px;
    margin: 4px 0 0 0;
}

.calendar-details {
    background: #3a3a3a;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 20px;
    border: 1px solid #555;
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
    color: #b0b0b0;
    min-width: 80px;
}

.calendar-id {
    background: #4a4a4a;
    color: #ffd700;
    padding: 4px 8px;
    border-radius: 4px;
    font-family: monospace;
    font-size: 12px;
    border: 1px solid #666;
}

.sync-time {
    color: #4ade80;
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
    color: #b0b0b0;
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
    color: #ffffff;
    padding: 12px 24px;
    border-radius: 6px;
    text-decoration: none;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-weight: 600;
    transition: background 0.2s;
}

.connect-btn:hover {
    background: #1557b0;
}

.disconnect-btn {
    background: #ea4335;
    color: #ffffff;
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-weight: 600;
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

.btn-primary { background: #4285f4; color: #ffffff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 600; }
.btn-primary:hover { background: #3367d6; }
.btn-success { background: #27ae60; color: #ffffff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 600; }
.btn-success:hover { background: #229954; }
.btn-danger { background: #e74c3c; color: #ffffff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 600; }
.btn-danger:hover { background: #c0392b; }
.btn-secondary { background: #95a5a6; color: #ffffff; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: 600; }
.btn-secondary:hover { background: #7f8c8d; }

/* Actions Grid */
.actions-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin-bottom: 24px;
}

.action-card {
    background: #2d3748;
    border: 1px solid #4a5568;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.3);
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
    font-size: 20px;
    font-weight: 600;
    color: #ffd700;
    margin: 0 0 8px 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    text-shadow: 0 1px 2px rgba(0,0,0,0.5);
}

.card-content p {
    color: #d0d0d0;
    font-size: 15px;
    margin: 0 0 12px 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-weight: 400;
    line-height: 1.5;
}

.sync-status {
    margin-bottom: 16px;
}

.status-badge {
    padding: 6px 14px;
    border-radius: 16px;
    font-size: 13px;
    font-weight: 600;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.status-badge.synced {
    background: #27ae60;
    color: #ffffff;
    border: 1px solid #2ecc71;
}

.sync-btn {
    background: #1a73e8;
    color: #ffffff;
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}

.sync-btn:hover:not(:disabled) {
    background: #1557b0;
}

.sync-btn:disabled {
    background: #555;
    color: #888;
    cursor: not-allowed;
}

.quick-actions-card .card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.card-header h3 {
    font-size: 20px;
    font-weight: 600;
    color: #ffd700;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    text-shadow: 0 1px 2px rgba(0,0,0,0.5);
    margin: 0;
}

.shortcuts-label {
    font-size: 12px;
    color: #b0b0b0;
    background: #3a3a3a;
    padding: 4px 8px;
    border-radius: 12px;
    border: 1px solid #555;
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
    color: #b0b0b0;
    transition: all 0.2s;
    border: 1px solid transparent;
}

.quick-action:hover {
    background: #4a4a4a;
    color: #ffd700;
    border-color: #ffd700;
    transform: translateY(-1px);
}

.action-icon {
    font-size: 16px;
}

/* Calendar View */
.calendar-view, .instructions {
    background: #2d2d2d;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(255,215,0,0.1);
    padding: 24px;
    margin-bottom: 24px;
    border: 1px solid #444;
}

.overview-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 1px solid #555;
}

.overview-header h2 {
    font-size: 20px;
    font-weight: 500;
    color: #ffd700;
    margin: 0;
}

.overview-meta {
    text-align: right;
}

.overview-subtitle {
    display: block;
    font-size: 14px;
    color: #b0b0b0;
    margin-bottom: 8px;
}

.view-controls {
    display: flex;
    align-items: center;
    gap: 8px;
}

.view-label {
    font-size: 14px;
    color: #b0b0b0;
}

.view-toggle {
    display: flex;
    border: 1px solid #555;
    border-radius: 6px;
    overflow: hidden;
}

.view-btn {
    padding: 6px 12px;
    border: none;
    background: #3a3a3a;
    color: #b0b0b0;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s;
}

.view-btn.active {
    background: #ffd700;
    color: #1a1a1a;
}

.view-btn:hover:not(.active) {
    background: #4a4a4a;
    color: #ffd700;
}

.sync-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px;
    background: #3a3a3a;
    border-radius: 8px;
    gap: 20px;
    border: 1px solid #555;
}

.sync-stats {
    margin-top: 10px;
    color: #b0b0b0;
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
    background: #3a3a3a;
    border-radius: 8px;
    border: 1px solid #555;
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
    color: #d0d0d0;
    font-size: 15px;
    margin-bottom: 3px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-weight: 400;
}

.activity-time {
    color: #b0b0b0;
    font-size: 13px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
}

.status-pending { background: #4a4a00; color: #ffff99; }
.status-confirmed { background: #004a00; color: #99ff99; }
.status-cancelled { background: #4a0000; color: #ff9999; }

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
    background: #ffd700;
    color: #1a1a1a;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    flex-shrink: 0;
    box-shadow: 0 2px 4px rgba(255,215,0,0.3);
}

.step-content h3 {
    margin: 0 0 8px 0;
    color: #ffd700;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-weight: 600;
    font-size: 18px;
    text-shadow: 0 1px 2px rgba(0,0,0,0.5);
}

.step-content p {
    color: #d0d0d0;
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-size: 15px;
    line-height: 1.5;
}

.no-activity {
    text-align: center;
    padding: 40px;
    color: #d0d0d0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-size: 16px;
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
    color: #ffd700;
    margin: 0 0 16px 0;
}

.events-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 16px;
}

.event-card {
    background: #2d2d2d;
    border: 1px solid #555;
    border-radius: 8px;
    padding: 16px;
    transition: all 0.2s;
    position: relative;
}

.event-card:hover {
    box-shadow: 0 4px 16px rgba(255,215,0,0.2);
    border-color: #ffd700;
    transform: translateY(-1px);
}

.event-card.studio-event {
    border-left: 4px solid #ff6b6b;
}

.event-card.other-event {
    border-left: 4px solid #4dabf7;
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
    color: #ffd700;
    margin-bottom: 2px;
}

.event-duration {
    font-size: 12px;
    color: #b0b0b0;
}

.event-type-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
    text-transform: uppercase;
}

.event-type-badge.studio {
    background: #4a1a1a;
    color: #ff6b6b;
    border: 1px solid #ff6b6b;
}

.event-type-badge.other {
    background: #1a2a4a;
    color: #4dabf7;
    border: 1px solid #4dabf7;
}

.event-content {
    margin-bottom: 12px;
}

.event-title {
    font-size: 16px;
    font-weight: 500;
    color: #ffd700;
    margin: 0 0 8px 0;
}

.event-client, .event-location {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
    color: #b0b0b0;
    margin-bottom: 4px;
}

.client-icon, .location-icon {
    font-size: 12px;
}

.event-meta {
    font-size: 12px;
    color: #b0b0b0;
}

.event-actions {
    display: flex;
    justify-content: flex-end;
}

.event-view-btn {
    color: #ffd700;
    text-decoration: none;
    font-size: 12px;
    font-weight: 500;
    padding: 4px 8px;
    border-radius: 4px;
    transition: all 0.2s;
    border: 1px solid transparent;
}

.event-view-btn:hover {
    background: #4a4a4a;
    border-color: #ffd700;
    transform: translateY(-1px);
}

.event-time {
    text-align: center;
    min-width: 60px;
}

.event-date {
    font-weight: bold;
    color: #ffd700;
    font-size: 14px;
}

.event-hour {
    color: #b0b0b0;
    font-size: 12px;
}

.event-details {
    flex: 1;
}

.event-title {
    font-weight: bold;
    color: #ffd700;
    margin-bottom: 5px;
}

.event-duration, .event-attendees, .event-location {
    font-size: 12px;
    color: #b0b0b0;
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
    background: #4a1a1a;
    color: #ff6b6b;
    border: 1px solid #ff6b6b;
}

.type-badge.other {
    background: #1a2a4a;
    color: #4dabf7;
    border: 1px solid #4dabf7;
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
    background: #2d2d2d;
    border-radius: 8px;
    padding: 20px;
    border: 1px solid #555;
}

.comparison-section h4 {
    margin: 0 0 15px 0;
    color: #ffd700;
    border-bottom: 2px solid #555;
    padding-bottom: 8px;
}

.bookings-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.booking-item {
    background: #3a3a3a;
    border-radius: 6px;
    padding: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-left: 3px solid #666;
    border: 1px solid #555;
}

.booking-item.synced {
    border-left-color: #4ade80;
}

.booking-item.unsynced {
    border-left-color: #fbbf24;
}

.booking-title {
    font-weight: bold;
    margin-bottom: 3px;
    color: #ffd700;
}

.booking-ref {
    font-size: 12px;
    color: #b0b0b0;
    margin-left: 8px;
}

.booking-time {
    font-size: 12px;
    color: #b0b0b0;
}

.sync-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: bold;
}

.sync-badge.synced {
    background: #1a4a1a;
    color: #4ade80;
    border: 1px solid #4ade80;
}

.sync-badge.unsynced {
    background: #4a3a00;
    color: #fbbf24;
    border: 1px solid #fbbf24;
}

.calendar-stats {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
}

.stat-item {
    text-align: center;
    background: #3a3a3a;
    padding: 12px;
    border-radius: 6px;
    flex: 1;
    border: 1px solid #555;
}

.stat-number {
    display: block;
    font-size: 24px;
    font-weight: bold;
    color: #ffd700;
}

.stat-label {
    font-size: 12px;
    color: #b0b0b0;
}

.calendar-actions {
    text-align: center;
}

.btn-info {
    background: #ffd700;
    color: #1a1a1a;
    border: 1px solid #ffd700;
}

.btn-info:hover {
    background: #ffed4e;
    transform: translateY(-1px);
}

.no-bookings, .no-events {
    text-align: center;
    padding: 20px;
    color: #b0b0b0;
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

function copyIcsUrl() {
    const el = document.querySelector('.calendar-id');
    if (!el) { alert('ICS url not found on page'); return; }
    const url = (el.textContent || el.innerText || '').trim();
    if (!url) { alert('ICS url is empty'); return; }
    navigator.clipboard.writeText(url).then(() => {
        alert('ICS link copied to clipboard');
    }).catch(() => {
        alert('Failed to copy ICS link');
    });
}

// Auto-refresh calendar data every 5 minutes
setInterval(function() {
    // This would typically make an AJAX call to refresh calendar data
    console.log('Auto-refreshing calendar data...');
}, 300000);
</script>

@endsection