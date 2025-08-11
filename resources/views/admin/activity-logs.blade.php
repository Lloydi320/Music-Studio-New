@extends('layouts.admin')

@section('title', 'Activity Logs - Audit Trail')

@section('content')
<div class="admin-content">
    <!-- Modern Header -->
    <div class="admin-header">
        <div class="header-content">
            <div class="header-icon">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 8V12L15 15" stroke="#4285f4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="12" r="9" stroke="#4285f4" stroke-width="2"/>
                </svg>
            </div>
            <div class="header-text">
                <h1>Activity Logs</h1>
                <p>Monitor system activities and user actions</p>
            </div>
        </div>
        <div class="header-actions">
            <button class="theme-toggle" onclick="toggleTheme()">
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
                                <th>Role</th>
                                <th>Description</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activityLogs as $log)
                                <tr>
                                    <td>
                                        <div class="timestamp">
                                            {{ $log->created_at->format('M d, Y H:i A') }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="user-info">
                                            <span class="user-name">{{ $log->user_name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="role-badge {{ strtolower($log->user_role) }}">
                                            {{ $log->user_role }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="description">
                                            {{ $log->description }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="ip-address">{{ $log->ip_address }}</span>
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
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
    padding: 2rem;
}

.modern-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 1rem;
    box-shadow: var(--shadow-xl);
    margin-bottom: 2rem;
    overflow: hidden;
    transition: all 0.3s ease;
}

.modern-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
}

.card-header {
    background: var(--gradient-primary);
    color: white;
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
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
    color: #374151;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.form-select,
.form-input {
    padding: 0.75rem 1rem;
    border: 2px solid #e5e7eb;
    border-radius: 0.75rem;
    background: white;
    color: #1f2937;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    box-shadow: var(--shadow-sm);
}

.form-select:focus,
.form-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1), var(--shadow-md);
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
    background: var(--gradient-primary);
    color: white;
    box-shadow: var(--shadow-md);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.btn-secondary {
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    color: #475569;
    border: 2px solid #e2e8f0;
    box-shadow: var(--shadow-sm);
}

.btn-secondary:hover {
    background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.btn-outline {
    background: transparent;
    color: #667eea;
    border: 2px solid #667eea;
    backdrop-filter: blur(10px);
}

.btn-outline:hover {
    background: var(--gradient-primary);
    color: white;
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
    background: white;
}

.modern-table th {
    background: var(--gradient-secondary);
    padding: 1rem;
    text-align: left;
    font-weight: 700;
    color: white;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 0;
    z-index: 10;
}

.modern-table td {
    padding: 1rem;
    border-bottom: 1px solid #f1f5f9;
    color: #334155;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.modern-table tbody tr {
    transition: all 0.3s ease;
}

.modern-table tbody tr:hover {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    transform: scale(1.01);
    box-shadow: var(--shadow-md);
}

.timestamp {
    font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Roboto Mono', monospace;
    font-size: 0.8rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 600;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.user-name {
    font-weight: 600;
    color: #1e293b;
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
    color: #64748b;
    background: #f8fafc;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    border: 1px solid #e2e8f0;
}

.ip-address::before {
    content: '🌐';
    margin-right: 0.25rem;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-radius: 1rem;
    margin: 2rem 0;
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
    color: #1e293b;
    margin-bottom: 0.75rem;
    font-size: 1.5rem;
    font-weight: 700;
}

.empty-state p {
    color: #64748b;
    font-size: 1rem;
    line-height: 1.6;
}

.pagination-wrapper {
    margin-top: 2rem;
    display: flex;
    justify-content: center;
}

.pagination-wrapper .pagination {
    background: white;
    border-radius: 1rem;
    padding: 0.5rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid #e2e8f0;
}

.pagination-wrapper .pagination a,
.pagination-wrapper .pagination span {
    padding: 0.75rem 1rem;
    margin: 0 0.25rem;
    border-radius: 0.5rem;
    transition: all 0.3s ease;
    font-weight: 600;
}

.pagination-wrapper .pagination a:hover {
    background: var(--gradient-primary);
    color: white;
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

.modern-card {
    animation: fadeInUp 0.6s ease-out;
}

.modern-card:nth-child(2) {
    animation-delay: 0.1s;
}

.modern-card:nth-child(3) {
    animation-delay: 0.2s;
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
</script>
@endsection