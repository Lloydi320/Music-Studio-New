@extends('layouts.admin')

@section('content')
<style>
    :root {
        --gradient-primary: linear-gradient(135deg, #3a3a3a 0%, #2c2c2c 100%);
        --gradient-secondary: linear-gradient(135deg, #4a4a4a 0%, #3a3a3a 100%);
        --gradient-success: linear-gradient(135deg, #ffc107 0%, #ffca2c 100%);
        --gradient-warning: linear-gradient(135deg, #ffc107 0%, #ffca2c 100%);
        --gradient-danger: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        --shadow-soft: 0 10px 40px rgba(0, 0, 0, 0.3);
        --shadow-hover: 0 20px 60px rgba(0, 0, 0, 0.4);
    }

    .admin-content {
        padding: 2rem;
        background: linear-gradient(135deg, #2c2c2c 0%, #1a1a1a 100%);
        min-height: 100vh;
        color: #e0e0e0;
    }

    .page-header {
        background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
        color: #1a1a1a;
        padding: 2rem;
        border-radius: 20px;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-soft);
        position: relative;
        overflow: hidden;
        border: 2px solid #FFD700;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }

    .page-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        position: relative;
        z-index: 1;
    }

    .welcome-text {
        font-size: 1.1rem;
        opacity: 0.9;
        position: relative;
        z-index: 1;
    }
</style>

<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <h2 class="page-title">🎸 Instrument Rentals Management</h2>
        <div class="header-actions">
            <span class="welcome-text">Manage all instrument rental requests and their status</span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    <div class="admin-content">
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Rentals</h3>
                <p class="stat-number">{{ $rentals->total() }}</p>
            </div>
            <div class="stat-card">
                <h3>Pending</h3>
                <p class="stat-number">{{ $rentals->where('status', 'pending')->count() }}</p>
            </div>
            <div class="stat-card">
                <h3>Active</h3>
                <p class="stat-number">{{ $rentals->where('status', 'active')->count() }}</p>
            </div>
            <div class="stat-card">
                <h3>Returned</h3>
                <p class="stat-number">{{ $rentals->where('status', 'returned')->count() }}</p>
            </div>
        </div>

        <div class="rentals-table">
            <table>
                <thead>
                                            <tr>
                            <th>Reference</th>
                            <th>Customer</th>
                            <th>Instrument</th>
                            <th>Rental Period</th>
                            <th>Duration</th>
                            <th>Total Amount</th>
                            <th>Transportation</th>
                            <th>Event Details</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                </thead>
                <tbody>
                    @forelse($rentals as $rental)
                        <tr>
                            <td>
                                <strong>{{ $rental->reference }}</strong>
                                <br>
                                <small>{{ $rental->created_at->format('M d, Y H:i') }}</small>
                            </td>
                            <td>
                                <strong>{{ $rental->user->name }}</strong>
                                <br>
                                <small>{{ $rental->user->email }}</small>
                            </td>
                            <td>
                                <strong>{{ $rental->instrument_name }}</strong>
                                <br>
                                <small>{{ ucfirst($rental->instrument_type) }}</small>
                            </td>
                            <td>
                                <strong>{{ $rental->rental_start_date->format('M d, Y') }}</strong>
                                <br>
                                <small>to {{ $rental->rental_end_date->format('M d, Y') }}</small>
                            </td>
                            <td>
                                <strong>{{ $rental->rental_duration_days }} days</strong>
                                <br>
                                <small>@ ₱{{ number_format($rental->daily_rate, 2) }}/day</small>
                            </td>
                            <td>
                                <strong>₱{{ number_format($rental->total_amount, 2) }}</strong>
                            </td>
                            <td>
                                @if($rental->transportation === 'delivery')
                                    <span class="transportation-badge delivery">Delivery & Pickup</span>
                                @else
                                    <span class="transportation-badge self">Self Pickup</span>
                                @endif
                            </td>
                            <td>
                                <div class="event-details">
                                    <div><strong>{{ ucfirst($rental->venue_type) }}</strong></div>
                                    <div>{{ $rental->event_duration_hours }}h event</div>
                                    @if($rental->documentation_consent)
                                        <div class="consent-badge">📸 Photo Consent</div>
                                    @else
                                        <div class="no-consent-badge">🚫 No Photos</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @if($rental->status === 'pending')
                                    <!-- Approve Button -->
                                    <form method="POST" action="{{ route('admin.rental.approve', $rental->id) }}" 
                                          style="display: inline; margin-right: 5px;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-sm" title="Approve Rental">
                                            ✓ Approve
                                        </button>
                                    </form>
                                    
                                    <!-- Reject Button -->
                                    <form method="POST" action="{{ route('admin.rental.reject', $rental->id) }}" 
                                          onsubmit="return confirm('Are you sure you want to reject rental {{ $rental->reference }} for {{ $rental->user->name }}?')" 
                                          style="display: inline; margin-right: 5px;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-warning btn-sm" title="Reject Rental">
                                            ✗ Reject
                                        </button>
                                    </form>
                                @endif
                                
                                <!-- Status Change Dropdown (for non-pending statuses) -->
                                @if($rental->status !== 'pending')
                                <form action="{{ route('admin.rental-status', $rental->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" class="status-select">
                                        <option value="confirmed" {{ $rental->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                        <option value="active" {{ $rental->status === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="returned" {{ $rental->status === 'returned' ? 'selected' : '' }}>Returned</option>
                                        <option value="cancelled" {{ $rental->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        <option value="rejected" {{ $rental->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </form>
                                @endif
                            </td>
                        </tr>
                                                 @if($rental->notes)
                             <tr>
                                 <td colspan="10" class="rental-notes">
                                     <strong>Notes:</strong> {{ $rental->notes }}
                                 </td>
                             </tr>
                         @endif
                                         @empty
                         <tr>
                             <td colspan="10" class="no-data">
                                 No instrument rentals found.
                             </td>
                         </tr>
                     @endforelse
                </tbody>
            </table>
        </div>

        @if($rentals->hasPages())
            <div class="pagination">
                {{ $rentals->links() }}
            </div>
        @endif
    </div>
</div>

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-bottom: 3rem;
    }

    .stat-card {
        background: linear-gradient(135deg, #2a2a2a 0%, #1a1a1a 100%);
        padding: 2rem;
        border-radius: 15px;
        box-shadow: var(--shadow-soft);
        text-align: center;
        border: 2px solid #FFD700;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
        border-color: #FFA500;
    }

    .stat-card h3 {
        margin: 0 0 1rem 0;
        color: #e0e0e0;
        font-size: 1.1rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .stat-number {
        margin: 0;
        font-size: 3rem;
        font-weight: 700;
        background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-shadow: 0 0 20px rgba(255, 215, 0, 0.3);
    }

    .rentals-table {
        background: linear-gradient(135deg, #2a2a2a 0%, #1a1a1a 100%);
        border-radius: 15px;
        box-shadow: var(--shadow-soft);
        overflow: hidden;
        border: 2px solid #FFD700;
    }

    .rentals-table table {
        width: 100%;
        border-collapse: collapse;
    }

    .rentals-table th,
    .rentals-table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid rgba(255, 215, 0, 0.1);
        color: #e0e0e0;
    }

    .rentals-table th {
        background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
        font-weight: 700;
        color: #1a1a1a;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 0.9rem;
    }

    .rentals-table tbody tr {
        transition: all 0.3s ease;
    }

    .rentals-table tbody tr:hover {
        background: rgba(255, 215, 0, 0.05);
        transform: scale(1.01);
    }

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8em;
    font-weight: bold;
    text-transform: uppercase;
}

.status-pending {
        background: #3a3a00;
        color: #ffff99;
    }

.status-confirmed {
        background: #1a3a3a;
        color: #66cccc;
    }

.status-active {
        background: #1a3a1a;
        color: #66cc66;
    }

.status-returned {
        background: #3a3a3a;
        color: #cccccc;
    }

.status-cancelled {
        background: #3a1a1a;
        color: #ff6666;
    }

.transportation-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8em;
    font-weight: bold;
    text-transform: uppercase;
}

.transportation-badge.delivery {
        background: #1a3a3a;
        color: #66cccc;
    }

.transportation-badge.self {
        background: #3a3a3a;
        color: #cccccc;
    }

.event-details {
    font-size: 0.8em;
}

.event-details div {
    margin-bottom: 2px;
}

.consent-badge {
        background: #1a3a1a;
        color: #66cc66;
        padding: 2px 4px;
        border-radius: 3px;
        font-size: 0.7em;
        font-weight: bold;
    }

.no-consent-badge {
        background: #3a1a1a;
        color: #ff6666;
        padding: 2px 4px;
        border-radius: 3px;
        font-size: 0.7em;
        font-weight: bold;
    }

/* Add to existing <style> section */
.btn {
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    font-weight: bold;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease;
}

.btn-success {
    background-color: #28a745;
    color: white;
}

.btn-success:hover {
    background-color: #218838;
}

.btn-warning {
    background-color: #ffc107;
    color: #212529;
}

.btn-warning:hover {
    background-color: #e0a800;
}

.btn-sm {
    padding: 4px 8px;
    font-size: 11px;
}

.status-select {
        padding: 4px 8px;
        border: 1px solid #3a3a3a;
        border-radius: 4px;
        font-size: 12px;
        background: #2a2a2a;
        color: #e0e0e0;
    }

.rental-notes {
        background: #2a2a2a;
        font-style: italic;
        color: #b0b0b0;
    }

.no-data {
        text-align: center;
        color: #b0b0b0;
        font-style: italic;
        padding: 40px;
    }

.alert {
    padding: 15px;
    margin: 20px 0;
    border-radius: 6px;
    font-weight: 500;
}

.alert-success {
        background-color: #1a3a1a;
        color: #66cc66;
        border: 1px solid #2a4a2a;
    }

.alert-error {
        background-color: #3a1a1a;
        color: #ff6666;
        border: 1px solid #4a2a2a;
    }

.pagination {
    margin-top: 20px;
    text-align: center;
}

@media (max-width: 768px) {
    .rentals-table {
        overflow-x: auto;
    }
    
    .rentals-table table {
        min-width: 800px;
    }
}
</style>
@endsection