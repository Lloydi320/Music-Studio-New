@extends('layouts.admin')

@section('title', 'Bookings Management')

@section('content')
<div class="admin-content">
    <!-- Page Header -->
    <div class="page-header">
        <h2 class="page-title">Bookings</h2>
        <div class="header-actions">
            <input type="text" class="search-input" id="searchInput" placeholder="" value="{{ $search }}">
            <button class="search-btn" onclick="searchBookings()">Search Bookings</button>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-row">
        <select class="filter-select" id="statusFilter" onchange="filterBookings()">
            <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All dates</option>
            <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
            <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
        
        <select class="filter-select" id="timeFilter" onchange="filterBookings()">
            <option value="all" {{ $dateFilter === 'all' ? 'selected' : '' }}>Time</option>
            <option value="today" {{ $dateFilter === 'today' ? 'selected' : '' }}>Today</option>
            <option value="week" {{ $dateFilter === 'week' ? 'selected' : '' }}>This Week</option>
            <option value="month" {{ $dateFilter === 'month' ? 'selected' : '' }}>This Month</option>
        </select>
    </div>

    <!-- Bookings Table -->
    <div class="bookings-table-container">
        <table class="bookings-table">
        <thead>
            <tr>
                <th class="checkbox-col">
                    <input type="checkbox" class="table-checkbox" id="selectAll">
                </th>
                <th class="name-col">All</th>
                <th class="status-col">Status</th>
                <th class="email-col">Email</th>
                <th class="datetime-col">Date & Time</th>
                <th class="actions-col">Booking Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($bookings as $booking)
            <tr class="table-row">
                <td class="checkbox-cell">
                    <input type="checkbox" class="table-checkbox booking-checkbox" value="{{ $booking->id }}">
                </td>
                <td class="name-cell">
                    <div class="booking-name">{{ $booking->user->name ?? 'N/A' }}</div>
                    <div class="booking-details">
                        ({{ $booking->service_type ?? 'Studio Rental' }}) - {{ $booking->duration ?? '3' }} hrs
                    </div>
                    <div class="booking-adjustments">See Attachments</div>
                </td>
                <td class="status-cell">
                    @if($booking->status === 'pending')
                        <span class="status-badge status-pending">Pending</span>
                    @elseif($booking->status === 'confirmed')
                        <span class="status-badge status-accepted">Accepted</span>
                    @elseif($booking->status === 'rejected')
                        <span class="status-badge status-rejected">Rejected</span>
                    @endif
                </td>
                <td class="email-cell">{{ $booking->user->email ?? 'N/A' }}</td>
                <td class="datetime-cell">
                    <div class="date-time">
                         {{ \Carbon\Carbon::parse($booking->date)->format('d/m/Y') }}
                         <br>
                         <span class="time-slot">
                             {{ $booking->time_slot }}
                         </span>
                     </div>
                </td>
                <td class="actions-cell">
                    @if($booking->status === 'pending')
                        <div class="action-buttons">
                            <form method="POST" action="{{ route('admin.booking.reject', $booking->id) }}" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn-reject" onclick="return confirm('Are you sure you want to reject this booking?')">
                                    Reject
                                </button>
                            </form>
                            
                            <form method="POST" action="{{ route('admin.booking.approve', $booking->id) }}" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn-accept" onclick="return confirm('Are you sure you want to approve this booking?')">
                                    Accept
                                </button>
                            </form>
                        </div>
                    @elseif($booking->status === 'confirmed')
                        <button class="btn-reschedule">Reschedule</button>
                    @elseif($booking->status === 'rejected')
                        <span class="status-rejected-text">Rejected</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center py-4">
                    <div style="color: #6c757d;">
                        <i class="fas fa-calendar-times fa-3x mb-3"></i>
                        <p>No bookings found matching your criteria.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
   </table>
    </div>

    <!-- Pagination -->
    @if($bookings->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $bookings->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<script>
function searchBookings() {
    const search = document.getElementById('searchInput').value;
    const status = document.getElementById('statusFilter').value;
    const dateFilter = document.getElementById('timeFilter').value;
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (status !== 'all') params.append('status', status);
    if (dateFilter !== 'all') params.append('date_filter', dateFilter);
    
    window.location.href = '{{ route("admin.bookings") }}' + (params.toString() ? '?' + params.toString() : '');
}

function filterBookings() {
    const status = document.getElementById('statusFilter').value;
    const dateFilter = document.getElementById('timeFilter').value;
    const search = document.getElementById('searchInput').value || '';
    
    const params = new URLSearchParams();
    if (status !== 'all') params.append('status', status);
    if (dateFilter !== 'all') params.append('date_filter', dateFilter);
    if (search) params.append('search', search);
    
    window.location.href = '{{ route("admin.bookings") }}' + (params.toString() ? '?' + params.toString() : '');
}

// Allow search on Enter key
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        searchBookings();
    }
});

// Select all functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.booking-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Update select all when individual checkboxes change
document.querySelectorAll('.booking-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const allCheckboxes = document.querySelectorAll('.booking-checkbox');
        const checkedCheckboxes = document.querySelectorAll('.booking-checkbox:checked');
        const selectAllCheckbox = document.getElementById('selectAll');
        
        if (checkedCheckboxes.length === allCheckboxes.length) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else if (checkedCheckboxes.length > 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        }
    });
});
</script>
@endsection