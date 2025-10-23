@extends('layouts.admin')

@section('title', 'Reschedule Request Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">Reschedule Request Details</h1>
                    <p class="text-muted mb-0">Review and manage reschedule request</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            @endif

            <div class="row">
                <!-- Request Details Card -->
                <div class="col-lg-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-calendar-alt"></i> Reschedule Request #{{ $rescheduleRequest->id }}
                            </h6>
                            <span class="badge badge-warning">Pending Review</span>
                        </div>
                        <div class="card-body">
                            <!-- Current Resource Details -->
                            <div class="mb-4">
                                <h5 class="text-dark mb-3">
                                    <i class="fas fa-info-circle text-primary"></i> 
                                    @if($rescheduleRequest->resource_type === 'booking')
                                        Current Booking Details
                                    @else
                                        Current Rental Details
                                    @endif
                                </h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <label class="detail-label">Reference:</label>
                                            <span class="detail-value reference-badge">
                                                @if($booking)
                                                    {{ $booking->reference }}
                                                @elseif($rental)
                                                    {{ $rental->reference }}
                                                @else
                                                    N/A
                                                @endif
                                            </span>
                                        </div>
                                        @if($booking)
                                            <div class="detail-item">
                                                <label class="detail-label">Band Name:</label>
                                                <span class="detail-value">{{ $booking->band_name }}</span>
                                            </div>
                                            <div class="detail-item">
                                                <label class="detail-label">Customer:</label>
                                                <span class="detail-value">{{ $booking->customer_name }}</span>
                                            </div>
                                        @elseif($rental)
                                            <div class="detail-item">
                                                <label class="detail-label">Instrument:</label>
                                                <span class="detail-value">{{ $rental->instrument_name }}</span>
                                            </div>
                                            <div class="detail-item">
                                                <label class="detail-label">Customer:</label>
                                                <span class="detail-value">{{ $rental->customer_name }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <label class="detail-label">Email:</label>
                                            <span class="detail-value">
                                                @if($booking)
                                                    {{ $booking->user->email ?? 'N/A' }}
                                                @elseif($rental)
                                                    {{ $rental->customer_email ?? 'N/A' }}
                                                @else
                                                    N/A
                                                @endif
                                            </span>
                                        </div>
                                        @if($booking)
                                            <div class="detail-item">
                                                <label class="detail-label">Phone:</label>
                                                <span class="detail-value">{{ $booking->phone ?? 'N/A' }}</span>
                                            </div>
                                        @elseif($rental)
                                            <div class="detail-item">
                                                <label class="detail-label">Phone:</label>
                                                <span class="detail-value">{{ $rental->customer_phone ?? 'N/A' }}</span>
                                            </div>
                                        @endif
                                        <div class="detail-item">
                                            <label class="detail-label">Status:</label>
                                            @if($booking)
                                                <span class="badge badge-{{ $booking->status === 'confirmed' ? 'success' : ($booking->status === 'pending' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($booking->status) }}
                                                </span>
                                            @elseif($rental)
                                                <span class="badge badge-{{ $rental->status === 'confirmed' ? 'success' : ($rental->status === 'pending' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst($rental->status) }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Changes Requested -->
                            <div class="mb-4">
                                <h5 class="text-dark mb-3">
                                    <i class="fas fa-exchange-alt text-warning"></i> Requested Changes
                                </h5>
                                
                                @if($rescheduleRequest->resource_type === 'booking')
                                    <!-- Studio Booking Changes -->
                                    @if($rescheduleRequest->original_date && $rescheduleRequest->requested_date)
                                        <div class="mb-3">
                                            <h6><i class="fas fa-calendar text-primary"></i> Date</h6>
                                            <div class="d-flex align-items-center">
                                                <span class="text-muted">From:</span>
                                                <span class="text-danger mx-2">{{ \Carbon\Carbon::parse($rescheduleRequest->original_date)->format('l, F j, Y') }}</span>
                                                <i class="fas fa-arrow-right mx-2"></i>
                                                <span class="text-muted">To:</span>
                                                <span class="text-success mx-2">{{ \Carbon\Carbon::parse($rescheduleRequest->requested_date)->format('l, F j, Y') }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    @if($rescheduleRequest->original_time_slot && $rescheduleRequest->requested_time_slot)
                                        <div class="mb-3">
                                            <h6><i class="fas fa-clock text-primary"></i> Time Slot</h6>
                                            <div class="d-flex align-items-center">
                                                <span class="text-muted">From:</span>
                                                <span class="text-danger mx-2">{{ $rescheduleRequest->original_time_slot }}</span>
                                                <i class="fas fa-arrow-right mx-2"></i>
                                                <span class="text-muted">To:</span>
                                                <span class="text-success mx-2">{{ $rescheduleRequest->requested_time_slot }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    @if($rescheduleRequest->original_duration && $rescheduleRequest->requested_duration)
                                        <div class="mb-3">
                                            <h6><i class="fas fa-hourglass-half text-primary"></i> Duration</h6>
                                            <div class="d-flex align-items-center">
                                                <span class="text-muted">From:</span>
                                                <span class="text-danger mx-2">{{ $rescheduleRequest->original_duration }} hour(s)</span>
                                                <i class="fas fa-arrow-right mx-2"></i>
                                                <span class="text-muted">To:</span>
                                                <span class="text-success mx-2">{{ $rescheduleRequest->requested_duration }} hour(s)</span>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <!-- Instrument Rental Changes -->
                                    @if($rescheduleRequest->original_start_date && $rescheduleRequest->requested_start_date)
                                        <div class="mb-3">
                                            <h6><i class="fas fa-calendar text-primary"></i> Start Date</h6>
                                            <div class="d-flex align-items-center">
                                                <span class="text-muted">From:</span>
                                                <span class="text-danger mx-2">{{ \Carbon\Carbon::parse($rescheduleRequest->original_start_date)->format('l, F j, Y') }}</span>
                                                <i class="fas fa-arrow-right mx-2"></i>
                                                <span class="text-muted">To:</span>
                                                <span class="text-success mx-2">{{ \Carbon\Carbon::parse($rescheduleRequest->requested_start_date)->format('l, F j, Y') }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    @if($rescheduleRequest->original_end_date && $rescheduleRequest->requested_end_date)
                                        <div class="mb-3">
                                            <h6><i class="fas fa-calendar-check text-primary"></i> End Date</h6>
                                            <div class="d-flex align-items-center">
                                                <span class="text-muted">From:</span>
                                                <span class="text-danger mx-2">{{ \Carbon\Carbon::parse($rescheduleRequest->original_end_date)->format('l, F j, Y') }}</span>
                                                <i class="fas fa-arrow-right mx-2"></i>
                                                <span class="text-muted">To:</span>
                                                <span class="text-success mx-2">{{ \Carbon\Carbon::parse($rescheduleRequest->requested_end_date)->format('l, F j, Y') }}</span>
                                            </div>
                                        </div>
                                    @endif

                                    @if($rescheduleRequest->original_duration && $rescheduleRequest->requested_duration)
                                        <div class="mb-3">
                                            <h6><i class="fas fa-hourglass-half text-primary"></i> Duration</h6>
                                            <div class="d-flex align-items-center">
                                                <span class="text-muted">From:</span>
                                                <span class="text-danger mx-2">{{ $rescheduleRequest->original_duration }} day(s)</span>
                                                <i class="fas fa-arrow-right mx-2"></i>
                                                <span class="text-muted">To:</span>
                                                <span class="text-success mx-2">{{ $rescheduleRequest->requested_duration }} day(s)</span>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <!-- Request Information -->
                            <div class="mb-4">
                                <h5 class="text-dark mb-3">
                                    <i class="fas fa-info text-info"></i> Request Information
                                </h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <label class="detail-label">Submitted:</label>
                                            <span class="detail-value">{{ $rescheduleRequest->created_at->format('M j, Y \a\t g:i A') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <label class="detail-label">Time Ago:</label>
                                            <span class="detail-value">{{ $rescheduleRequest->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions Card -->
                <div class="col-lg-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-cogs"></i> Actions
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Review Required</strong><br>
                                Please review the reschedule request and take appropriate action.
                            </div>

                            <!-- Approve Button -->
                            <form method="POST" action="{{ route('admin.reschedule-request.approve', $rescheduleRequest->id) }}" class="mb-3">
                                @csrf
                                <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Are you sure you want to approve this reschedule request? This will update the booking with the new details.')">
                                    <i class="fas fa-check"></i> Approve Request
                                </button>
                            </form>

                            <!-- Reject Button -->
                            <form method="POST" action="{{ route('admin.reschedule-request.reject', $rescheduleRequest->id) }}" class="mb-3">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-block" onclick="return confirm('Are you sure you want to reject this reschedule request? The customer will be notified.')">
                                    <i class="fas fa-times"></i> Reject Request
                                </button>
                            </form>

                            <hr>

                            <!-- Additional Actions -->
                            @if($booking)
                                <a href="{{ route('admin.bookings') }}?highlight={{ $booking->id }}" class="btn btn-outline-primary btn-block mb-2">
                                    <i class="fas fa-eye"></i> View Original Booking
                                </a>
                            @elseif($rental)
                                <a href="{{ route('admin.instrument-bookings') }}?highlight={{ $rental->id }}" class="btn btn-outline-primary btn-block mb-2">
                                    <i class="fas fa-eye"></i> View Original Rental
                                </a>
                            @endif

                            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-block">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
                    </div>

                    <!-- Conflict Check Card -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-warning">
                                <i class="fas fa-exclamation-triangle"></i> Conflict Check
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($hasConflict)
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Conflict Detected!</strong><br>
                                    The requested time slot is already booked.
                                    <hr>
                                    <small>
                                        <strong>Conflicting Booking:</strong><br>
                                        {{ $conflictingBooking->reference }} - {{ $conflictingBooking->band_name }}
                                    </small>
                                </div>
                            @else
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle"></i>
                                    <strong>No Conflicts</strong><br>
                                    The requested time slot is available.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.detail-item {
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e3e6f0;
}

.detail-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.detail-label {
    font-weight: 600;
    color: #5a5c69;
    display: block;
    margin-bottom: 5px;
    font-size: 0.9rem;
}

.detail-value {
    color: #3a3b45;
    font-size: 1rem;
}

.reference-badge {
    background: #ffd700;
    color: #333;
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: 600;
    font-family: monospace;
}

.change-comparison {
    background: #f8f9fc;
    border: 1px solid #e3e6f0;
    border-radius: 8px;
    padding: 15px;
}

.change-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
    color: #5a5c69;
}

.change-content {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

.change-from, .change-to {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.change-label {
    font-size: 0.8rem;
    color: #6c757d;
    font-weight: 500;
}

.change-value.old {
    color: #dc3545;
    text-decoration: line-through;
    font-weight: 500;
}

.change-value.new {
    color: #28a745;
    font-weight: 600;
}

.change-arrow {
    color: #6c757d;
    font-size: 1.2rem;
}

@media (max-width: 768px) {
    .change-content {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .change-arrow {
        transform: rotate(90deg);
    }
}
</style>
@endsection