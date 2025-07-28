@extends('layouts.app')

@section('title', 'Google Calendar Integration')

@section('content')
<div class="calendar-integration">
    <div class="calendar-header">
        <h1>Google Calendar Integration</h1>
        <p>Sync your studio bookings with Google Calendar for better schedule management.</p>
    </div>

    <!-- Connection Status -->
    <div class="connection-status">
        @if($user->hasGoogleCalendarAccess())
            <div class="status-card connected">
                <div class="status-icon">✓</div>
                <div class="status-content">
                    <h3>Google Calendar Connected</h3>
                    <p>Your bookings are automatically synced to Google Calendar.</p>
                    <div class="calendar-info">
                        <strong>Calendar ID:</strong> 
                        <code>{{ Str::limit($user->google_calendar_id, 40, '...') }}</code>
                    </div>
                </div>
                <div class="status-actions">
                    <form method="POST" action="{{ route('admin.calendar.disconnect') }}" 
                          onsubmit="return confirm('Are you sure you want to disconnect Google Calendar?')">
                        @csrf
                        <button type="submit" class="btn btn-danger">Disconnect</button>
                    </form>
                </div>
            </div>
        @else
            <div class="status-card disconnected">
                <div class="status-icon">✗</div>
                <div class="status-content">
                    <h3>Google Calendar Not Connected</h3>
                    <p>Connect your Google Calendar to automatically sync studio bookings.</p>
                    <ul class="benefits">
                        <li>Automatic event creation for new bookings</li>
                        <li>Email and push notifications</li>
                        <li>Easy schedule management</li>
                        <li>Client information in calendar events</li>
                    </ul>
                </div>
                <div class="status-actions">
                    <a href="{{ route('admin.calendar.connect') }}" class="btn btn-primary">
                        <i class="icon-google"></i> Connect Google Calendar
                    </a>
                </div>
            </div>
        @endif
    </div>

    @if($user->hasGoogleCalendarAccess())
    <!-- Sync Actions -->
    <div class="sync-actions">
        <h2>Sync Management</h2>
        <div class="sync-card">
            <div class="sync-info">
                <h3>Sync Existing Bookings</h3>
                <p>Sync all confirmed bookings that haven't been added to Google Calendar yet.</p>
                @php
                    $unsyncedCount = \App\Models\Booking::where('status', 'confirmed')
                                                      ->whereNull('google_event_id')
                                                      ->count();
                @endphp
                <div class="sync-stats">
                    <strong>{{ $unsyncedCount }}</strong> bookings need syncing
                </div>
            </div>
            <div class="sync-actions-buttons">
                @if($unsyncedCount > 0)
                    <form method="POST" action="{{ route('admin.calendar.sync') }}" 
                          onsubmit="return confirm('Sync {{ $unsyncedCount }} bookings to Google Calendar?')">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="icon-sync"></i> Sync {{ $unsyncedCount }} Bookings
                        </button>
                    </form>
                @else
                    <div class="all-synced">
                        <span class="sync-complete">✓ All bookings are synced</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Unified Calendar View -->
    <div class="calendar-view">
        <h2>📅 Calendar Overview</h2>
        
        @if($user->hasGoogleCalendarAccess() && count($upcomingEvents) > 0)
            <div class="calendar-section">
                <h3>🔮 Upcoming Events (Next 4 Weeks)</h3>
                <div class="events-grid">
                    @foreach($upcomingEvents as $event)
                    <div class="event-card {{ $event['is_studio_booking'] ? 'studio-event' : 'other-event' }}">
                        <div class="event-time">
                            <div class="event-date">{{ $event['start']->format('M d') }}</div>
                            <div class="event-hour">{{ $event['start']->format('g:i A') }}</div>
                        </div>
                        <div class="event-details">
                            <div class="event-title">{{ $event['title'] }}</div>
                            <div class="event-duration">{{ $event['duration'] }}h duration</div>
                            @if($event['attendees'])
                                <div class="event-attendees">
                                    👥 {{ count($event['attendees']) }} attendee(s)
                                </div>
                            @endif
                            @if($event['location'])
                                <div class="event-location">📍 {{ $event['location'] }}</div>
                            @endif
                        </div>
                        <div class="event-type">
                            @if($event['is_studio_booking'])
                                <span class="type-badge studio">🎵 Studio</span>
                            @else
                                <span class="type-badge other">📅 Other</span>
                            @endif
                        </div>
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
.calendar-integration {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.calendar-header {
    text-align: center;
    margin-bottom: 30px;
}

.calendar-header h1 {
    color: #333;
    margin-bottom: 10px;
}

.calendar-header p {
    color: #666;
    font-size: 16px;
}

.status-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    padding: 30px;
    margin-bottom: 30px;
    display: flex;
    align-items: center;
    gap: 20px;
}

.status-card.connected {
    border-left: 5px solid #27ae60;
}

.status-card.disconnected {
    border-left: 5px solid #e74c3c;
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

.sync-actions, .calendar-view, .instructions {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 25px;
    margin-bottom: 25px;
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

.events-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.event-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    border-left: 4px solid #ddd;
    display: flex;
    gap: 15px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.event-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.event-card.studio-event {
    border-left-color: #e74c3c;
    background: linear-gradient(135deg, #fff5f5 0%, #f8f9fa 100%);
}

.event-card.other-event {
    border-left-color: #3498db;
    background: linear-gradient(135deg, #f0f8ff 0%, #f8f9fa 100%);
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

@media (max-width: 768px) {
    .status-card {
        flex-direction: column;
        text-align: center;
    }
    
    .sync-card {
        flex-direction: column;
        text-align: center;
    }
    
    .activity-item {
        flex-direction: column;
        text-align: center;
    }
    
    .events-grid {
        grid-template-columns: 1fr;
    }
    
    .comparison-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .event-card {
        flex-direction: column;
        text-align: center;
        gap: 10px;
    }
    
    .event-time {
        min-width: auto;
    }
    
    .calendar-stats {
        flex-direction: column;
        gap: 10px;
    }
    
    .booking-item {
        flex-direction: column;
        text-align: center;
        gap: 8px;
    }
}
</style>
@endsection