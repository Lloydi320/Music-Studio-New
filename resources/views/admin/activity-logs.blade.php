@extends('layouts.admin')

@section('title', 'Activity Logs - Audit Trail')

@section('content')
<div class="admin-content">
    <!-- Modern Header -->
    <div class="admin-header">
        <div class="header-content">
            <div class="header-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 8V12L15 15" stroke="#ffd700" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="12" r="9" stroke="#ffd700" stroke-width="2"/>
                </svg>
            </div>
            <div class="header-text">
                <h1 style="color: #ffd700;">Activity Logs</h1>
                <p style="color: #cccccc;">Monitor system activities and user actions</p>
            </div>
        </div>
        <div class="header-actions">
            <button class="theme-toggle" onclick="toggleTheme()" style="background: #333; border: 1px solid #555; color: #ffd700;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                    <path d="M12 3V4M12 20V21M4 12H3M6.31412 6.31412L5.5 5.5M17.6859 6.31412L18.5 5.5M6.31412 17.6859L5.5 18.5M17.6859 17.6859L18.5 18.5M21 12H20M16 12C16 14.2091 14.2091 16 12 16C9.79086 16 8 14.2091 8 12C8 9.79086 9.79086 8 12 8C14.2091 8 16 9.79086 16 12Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Filter Options -->
    <div class="modern-card">
        <div class="card-header">
            <h3>Filter Options</h3>
        </div>
        <div class="card-content">
            <form method="GET" action="{{ route('admin.activity-logs') }}" class="filter-form">
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="user">User</label>
                        <select name="user" id="user" class="form-select">
                            <option value="">All Users</option>
                            @foreach(\App\Models\ActivityLog::distinct()->pluck('user_name') as $userName)
                                <option value="{{ $userName }}" {{ request('user') == $userName ? 'selected' : '' }}>
                                    {{ $userName }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="action_type">Action Type</label>
                        <select name="action_type" id="action_type" class="form-select">
                            <option value="">All Actions</option>
                            @foreach(\App\Models\ActivityLog::distinct()->whereNotNull('action_type')->pluck('action_type') as $actionType)
                                <option value="{{ $actionType }}" {{ request('action_type') == $actionType ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $actionType)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="severity_level">Severity</label>
                        <select name="severity_level" id="severity_level" class="form-select">
                            <option value="">All Levels</option>
                            <option value="low" {{ request('severity_level') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="medium" {{ request('severity_level') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="high" {{ request('severity_level') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="critical" {{ request('severity_level') == 'critical' ? 'selected' : '' }}>Critical</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="from_date">From Date</label>
                        <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}" class="form-input">
                    </div>
                    <div class="filter-group">
                        <label for="to_date">To Date</label>
                        <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}" class="form-input">
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.activity-logs') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Activity Logs Table -->
    <div class="modern-card">
        <div class="card-header">
            <h3>Activity Logs</h3>
            <div class="card-actions">
                <span class="record-count">{{ number_format($totalRecords) }} records found</span>
                <button class="btn btn-outline" onclick="exportLogs()">📊 Export</button>
                <button class="btn btn-danger" onclick="clearActivityLogs()" title="Clear all activity logs">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M3 6H5H21" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M10 11V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14 11V17" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Clear Logs
                </button>
            </div>
        </div>
        <div class="card-content">
            @if($activityLogs->count() > 0)
                <div class="table-container">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Date & Time</th>
                                <th>User</th>
                                <th>Action Type</th>
                                <th>Severity</th>
                                <th>Description</th>
                                <th>Resource</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activityLogs as $log)
                                <tr class="log-row {{ $log->severity_level ?? 'low' }}">
                                    <td>
                                        <div class="timestamp">
                                            {{ $log->created_at->format('M d, Y H:i A') }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="user-info">
                                            <span class="user-name">{{ $log->user_name }}</span>
                                            <span class="role-badge {{ strtolower($log->user_role) }}">
                                                {{ $log->user_role }}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="action-badge">
                                            {{ $log->action_type_display ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="severity-badge {{ $log->severity_level ?? 'low' }}">
                                            {{ ucfirst($log->severity_level ?? 'low') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="description">
                                            {{ $log->description }}
                                            @if($log->old_values || $log->new_values)
                                                <button class="btn-details" onclick="toggleDetails({{ $log->id }})">
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                                        <path d="M19 9L12 16L5 9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                        @if($log->old_values || $log->new_values)
                                            <div class="details-panel" id="details-{{ $log->id }}" style="display: none;">
                                                @if($log->old_values)
                                                    <div class="change-info">
                                                        <strong>Before:</strong>
                                                        <pre>{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                                    </div>
                                                @endif
                                                @if($log->new_values)
                                                    <div class="change-info">
                                                        <strong>After:</strong>
                                                        <pre>{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->resource_type && $log->resource_id)
                                            <div class="resource-info">
                                                <span class="resource-type">{{ $log->resource_type }}</span>
                                                <span class="resource-id">#{{ $log->resource_id }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="ip-address">{{ $log->ip_address }}</span>
                                        @if($log->session_id)
                                            <div class="session-id">Session: {{ substr($log->session_id, 0, 8) }}...</div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination-wrapper">
                    {{ $activityLogs->appends(request()->query())->links() }}
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none">
                            <path d="M12 8V12L15 15" stroke="#9CA3AF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="12" cy="12" r="9" stroke="#9CA3AF" stroke-width="2"/>
                        </svg>
                    </div>
                    <h3>No Activity Logs Found</h3>
                    <p>No activities match your current filter criteria.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
:root {
    --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --gradient-success: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.admin-content {
    background: #1a1a1a;
    min-height: 100vh;
    padding: 2rem;
    color: #ffffff;
}

.modern-card {
    background: #2d2d2d;
    border: 1px solid #444;
    border-radius: 1rem;
    box-shadow: 0 4px 16px rgba(0,0,0,0.3);
    margin-bottom: 2rem;
    overflow: hidden;
    transition: all 0.3s ease;
}

.modern-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(255,215,0,0.2);
}

.card-header {
    background: #333333;
    color: #ffd700;
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #444;
}

.card-header h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.card-content {
    padding: 2rem;
}

.filter-form {
    margin: 0;
}

.filter-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    align-items: end;
}

.filter-group {
    display: flex;
    flex-direction: column;
}

.filter-group label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #ffd700;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.form-select,
.form-input {
    padding: 0.75rem 1rem;
    border: 2px solid #555;
    border-radius: 0.75rem;
    background: #1a1a1a;
    color: #ffffff;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    box-shadow: var(--shadow-sm);
}

.form-select:focus,
.form-input:focus {
    outline: none;
    border-color: #ffd700;
    box-shadow: 0 0 0 4px rgba(255, 215, 0, 0.2), var(--shadow-md);
    transform: translateY(-1px);
}

.filter-actions {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    justify-content: center;
    grid-column: 1 / -1;
    margin-top: 1rem;
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 0.75rem;
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    position: relative;
    overflow: hidden;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.btn:hover::before {
    left: 100%;
}

.btn-primary {
    background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
    color: #1a1a1a;
    box-shadow: var(--shadow-md);
    font-weight: 700;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #ffed4e 0%, #ffd700 100%);
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.btn-secondary {
    background: #333333;
    color: #ffffff;
    border: 2px solid #555;
    box-shadow: var(--shadow-sm);
}

.btn-secondary:hover {
    background: #444444;
    border-color: #ffd700;
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.btn-outline {
    background: transparent;
    color: #ffd700;
    border: 2px solid #ffd700;
    backdrop-filter: blur(10px);
}

.btn-outline:hover {
    background: #ffd700;
    color: #1a1a1a;
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.btn-danger {
    background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
    color: white;
    border: 2px solid #dc2626;
    box-shadow: var(--shadow-md);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-danger:hover {
    background: linear-gradient(135deg, #b91c1c 0%, #dc2626 100%);
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.record-count {
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.9);
    background: rgba(255, 255, 255, 0.1);
    padding: 0.5rem 1rem;
    border-radius: 2rem;
    backdrop-filter: blur(10px);
}

.card-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.table-container {
    overflow-x: auto;
    border-radius: 1rem;
    box-shadow: var(--shadow-lg);
    background: white;
}

.modern-table {
    width: 100%;
    border-collapse: collapse;
    background: #1a1a1a;
}

.modern-table th {
    background: #333333;
    padding: 1rem;
    text-align: left;
    font-weight: 700;
    color: #ffd700;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    position: sticky;
    top: 0;
    z-index: 10;
    border-bottom: 2px solid #444;
}

.modern-table td {
    padding: 1rem;
    border-bottom: 1px solid #444;
    color: #ffffff;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.modern-table tbody tr {
    transition: all 0.3s ease;
}

.modern-table tbody tr:hover {
    background: #3a3a3a;
    transform: scale(1.01);
    box-shadow: 0 4px 16px rgba(255,215,0,0.1);
}

.timestamp {
    font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Roboto Mono', monospace;
    font-size: 0.8rem;
    color: #ffd700;
    font-weight: 600;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.user-name {
    font-weight: 600;
    color: #ffffff;
}

.user-name::before {
    content: '👤';
    margin-right: 0.5rem;
}

.role-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 2rem;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    box-shadow: var(--shadow-sm);
    border: 2px solid transparent;
    transition: all 0.3s ease;
    margin-left: 8px;
}

.role-badge:hover {
    transform: scale(1.05);
    box-shadow: var(--shadow-md);
}

.role-badge.admin {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #dc2626;
    border-color: #fca5a5;
}

.role-badge.customer {
    background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
    color: #16a34a;
    border-color: #86efac;
}

.action-badge {
    padding: 4px 8px;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 500;
    background-color: #444;
    color: #ffd700;
}

.severity-badge {
    padding: 4px 8px;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 500;
    text-transform: uppercase;
}

.severity-badge.low {
    background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

.severity-badge.medium {
    background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
}

.severity-badge.high {
    background: linear-gradient(135deg, #f97316 0%, #fb923c 100%);
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(249, 115, 22, 0.3);
}

.severity-badge.critical {
    background: linear-gradient(135deg, #dc2626 0%, #ef4444 100%);
    color: #ffffff;
    box-shadow: 0 2px 8px rgba(220, 38, 38, 0.3);
    animation: glow 2s ease-in-out infinite;
}

.log-row.critical {
    border-left: 4px solid #dc2626;
}

.log-row.high {
    border-left: 4px solid #f59e0b;
}

.log-row.medium {
    border-left: 4px solid #eab308;
}

.log-row.low {
    border-left: 4px solid #10b981;
}

.resource-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.resource-type {
    font-size: 0.75rem;
    color: #cccccc;
    text-transform: uppercase;
}

.resource-id {
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
}

.session-id {
    font-size: 0.75rem;
    color: #6b7280;
    margin-top: 2px;
}

.btn-details {
    background: none;
    border: none;
    cursor: pointer;
    padding: 2px;
    margin-left: 8px;
    color: #6b7280;
    transition: color 0.2s;
}

.btn-details:hover {
    color: #ffd700;
}

.details-panel {
    margin-top: 8px;
    padding: 12px;
    background-color: #333333;
    border-radius: 6px;
    border: 1px solid #555;
}

.change-info {
    margin-bottom: 8px;
}

.change-info:last-child {
    margin-bottom: 0;
}

.change-info strong {
    color: #ffd700;
    font-size: 0.875rem;
}

.change-info pre {
    background-color: #1a1a1a;
    border: 1px solid #555;
    border-radius: 4px;
    padding: 8px;
    margin-top: 4px;
    font-size: 0.75rem;
    overflow-x: auto;
    white-space: pre-wrap;
    color: #ffffff;
}

.text-muted {
    color: #cccccc;
}

.description {
    max-width: 350px;
    word-wrap: break-word;
    line-height: 1.5;
    position: relative;
}

.description::before {
    content: '📝';
    margin-right: 0.5rem;
    opacity: 0.7;
}

.ip-address {
    font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Roboto Mono', monospace;
    font-size: 0.8rem;
    color: #cccccc;
    background: #333333;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    border: 1px solid #555;
}

.ip-address::before {
    content: '🌐';
    margin-right: 0.25rem;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: #2d2d2d;
    border-radius: 1rem;
    margin: 2rem 0;
    border: 1px solid #444;
}

.empty-icon {
    margin-bottom: 1.5rem;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.empty-state h3 {
    color: #ffd700;
    margin-bottom: 0.75rem;
    font-size: 1.5rem;
    font-weight: 700;
}

.empty-state p {
    color: #cccccc;
    font-size: 1rem;
    line-height: 1.6;
}

.pagination-wrapper {
    margin-top: 2rem;
    display: flex;
    justify-content: center;
}

.pagination-wrapper .pagination {
    background: #2d2d2d;
    border-radius: 1rem;
    padding: 0.5rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid #555;
}

.pagination-wrapper .pagination a,
.pagination-wrapper .pagination span {
    padding: 0.75rem 1rem;
    margin: 0 0.25rem;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
    font-weight: 600;
    color: #ffffff;
    text-decoration: none;
}

.pagination-wrapper .pagination .active span {
    background: #ffd700;
    color: #1a1a1a;
}

.pagination-wrapper .pagination a:hover {
    background: #ffd700;
    color: #1a1a1a;
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes glow {
    0%, 100% {
        box-shadow: 0 0 5px rgba(255, 215, 0, 0.3);
    }
    50% {
        box-shadow: 0 0 20px rgba(255, 215, 0, 0.6);
    }
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

.modern-card {
    animation: fadeInUp 0.6s ease-out;
}

.modern-card:nth-child(2) {
    animation-delay: 0.1s;
}

.modern-card:nth-child(3) {
    animation-delay: 0.2s;
}

.modern-table tbody tr {
    animation: slideIn 0.3s ease-out;
}

.btn:focus {
    animation: glow 0.5s ease-in-out;
}

/* Responsive Design */
@media (max-width: 768px) {
    .admin-content {
        padding: 1rem;
    }
    
    .filter-row {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .filter-actions {
        grid-column: 1;
        margin-top: 1.5rem;
    }
    
    .card-header {
        padding: 1rem;
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .card-content {
        padding: 1rem;
    }
    
    .modern-table th,
    .modern-table td {
        padding: 0.75rem 0.5rem;
        font-size: 0.8rem;
    }
    
    .description {
        max-width: 200px;
    }
}

@media (max-width: 480px) {
    .modern-table {
        font-size: 0.75rem;
    }
    
    .modern-table th,
    .modern-table td {
        padding: 0.5rem 0.25rem;
    }
    
    .btn {
        padding: 0.5rem 1rem;
        font-size: 0.8rem;
    }
}
</style>

<script>
function exportLogs() {
    // Simple CSV export functionality
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'csv');
    window.location.href = '{{ route("admin.activity-logs") }}?' + params.toString();
}

function clearActivityLogs() {
    // Show modern confirmation dialog
    if (confirm('⚠️ Are you sure you want to clear all activity logs?\n\nThis action cannot be undone and will permanently delete all log entries.')) {
        // Create a form to submit the clear request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.activity-logs.clear") ?? "#" }}';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Add method spoofing for DELETE request
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function toggleDetails(logId) {
    const detailsPanel = document.getElementById('details-' + logId);
    const button = event.target.closest('.btn-details');
    const icon = button.querySelector('svg');
    
    if (detailsPanel.style.display === 'none' || detailsPanel.style.display === '') {
        detailsPanel.style.display = 'block';
        icon.style.transform = 'rotate(180deg)';
        // Add smooth animation
        detailsPanel.style.animation = 'fadeInUp 0.3s ease-out';
    } else {
        detailsPanel.style.display = 'none';
        icon.style.transform = 'rotate(0deg)';
    }
}

// Add modern loading states
function showLoading(button) {
    const originalText = button.innerHTML;
    button.innerHTML = '<svg class="animate-spin" width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" opacity="0.25"></circle><path fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" opacity="0.75"></path></svg> Loading...';
    button.disabled = true;
    
    return function() {
        button.innerHTML = originalText;
        button.disabled = false;
    };
}
</script>
@endsection