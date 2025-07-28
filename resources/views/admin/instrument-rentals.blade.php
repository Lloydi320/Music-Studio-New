@extends('layouts.app')

@section('content')
<div class="admin-container">
    <div class="admin-header">
        <h1>🎸 Instrument Rentals Management</h1>
        <p>Manage all instrument rental requests and their status</p>
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
                                <span class="status-badge status-{{ $rental->status }}">
                                    {{ ucfirst($rental->status) }}
                                </span>
                            </td>
                            <td>
                                <form action="{{ route('admin.rental-status', $rental->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <select name="status" onchange="this.form.submit()" class="status-select">
                                        <option value="pending" {{ $rental->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="confirmed" {{ $rental->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                        <option value="active" {{ $rental->status === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="returned" {{ $rental->status === 'returned' ? 'selected' : '' }}>Returned</option>
                                        <option value="cancelled" {{ $rental->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </form>
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

<style>
.admin-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.admin-header {
    text-align: center;
    margin-bottom: 30px;
    padding: 20px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px;
}

.admin-header h1 {
    margin: 0;
    font-size: 2.5em;
    font-weight: bold;
}

.admin-header p {
    margin: 10px 0 0 0;
    font-size: 1.1em;
    opacity: 0.9;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    text-align: center;
}

.stat-card h3 {
    margin: 0 0 10px 0;
    color: #666;
    font-size: 1em;
}

.stat-number {
    margin: 0;
    font-size: 2em;
    font-weight: bold;
    color: #667eea;
}

.rentals-table {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    overflow: hidden;
}

.rentals-table table {
    width: 100%;
    border-collapse: collapse;
}

.rentals-table th,
.rentals-table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.rentals-table th {
    background: #f8f9fa;
    font-weight: bold;
    color: #333;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8em;
    font-weight: bold;
    text-transform: uppercase;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
}

.status-confirmed {
    background: #d1ecf1;
    color: #0c5460;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-returned {
    background: #e2e3e5;
    color: #383d41;
}

.status-cancelled {
    background: #f8d7da;
    color: #721c24;
}

.transportation-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8em;
    font-weight: bold;
    text-transform: uppercase;
}

.transportation-badge.delivery {
    background: #d1ecf1;
    color: #0c5460;
}

.transportation-badge.self {
    background: #e2e3e5;
    color: #383d41;
}

.event-details {
    font-size: 0.8em;
}

.event-details div {
    margin-bottom: 2px;
}

.consent-badge {
    background: #d4edda;
    color: #155724;
    padding: 2px 4px;
    border-radius: 3px;
    font-size: 0.7em;
    font-weight: bold;
}

.no-consent-badge {
    background: #f8d7da;
    color: #721c24;
    padding: 2px 4px;
    border-radius: 3px;
    font-size: 0.7em;
    font-weight: bold;
}

.status-select {
    padding: 6px 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 0.9em;
    background: white;
}

.rental-notes {
    background: #f8f9fa;
    font-style: italic;
    color: #666;
}

.no-data {
    text-align: center;
    color: #666;
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
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
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