@extends('layouts.admin')

@section('title', 'Reschedule Requests')

@section('content')
<style>
    :root {
        --gradient-primary: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
        --gradient-secondary: linear-gradient(135deg, #FFD700 0%, #FFED4E 100%);
        --gradient-success: linear-gradient(135deg, #34a853 0%, #4ade80 100%);
        --gradient-warning: linear-gradient(135deg, #f39c12 0%, #f1c40f 100%);
        --gradient-danger: linear-gradient(135deg, #ea4335 0%, #f87171 100%);
        --shadow-soft: 0 10px 40px rgba(0, 0, 0, 0.1);
        --shadow-hover: 0 20px 60px rgba(0, 0, 0, 0.15);
    }

    .admin-content {
        padding: 2rem;
        background: #1a1a1a;
        min-height: 100vh;
        color: #e0e0e0;
    }

    .page-header {
        background: var(--gradient-warning);
        color: white;
        padding: 2rem;
        border-radius: 20px;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-soft);
        position: relative;
        overflow: hidden;
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
        position: relative;
        z-index: 1;
    }

    .page-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        margin: 0.5rem 0 0 0;
        position: relative;
        z-index: 1;
    }

    .requests-container {
        background: #2a2a2a;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: var(--shadow-soft);
        border: 1px solid #444;
    }

    .request-card {
        background: #333;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #444;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .request-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
        border-color: #FFD700;
    }

    .request-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .request-info {
        flex: 1;
    }

    .booking-reference {
        font-size: 1.2rem;
        font-weight: 600;
        color: #FFD700;
        margin-bottom: 0.5rem;
    }

    .customer-name {
        font-size: 1rem;
        color: #e0e0e0;
        margin-bottom: 0.5rem;
    }

    .request-date {
        font-size: 0.9rem;
        color: #b0b0b0;
    }

    .conflict-badge {
        background: var(--gradient-danger);
        color: white;
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .request-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .detail-section {
        background: #2a2a2a;
        padding: 1rem;
        border-radius: 10px;
        border: 1px solid #444;
    }

    .detail-title {
        font-size: 0.9rem;
        font-weight: 600;
        color: #FFD700;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .detail-content {
        color: #e0e0e0;
    }

    .detail-content .date {
        font-weight: 600;
        color: #81c784;
    }

    .detail-content .time {
        color: #64b5f6;
    }

    .detail-content .duration {
        color: #ffb74d;
    }

    .request-actions {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        cursor: pointer;
        font-size: 0.9rem;
    }

    .btn-approve {
        background: var(--gradient-success);
        color: white;
    }

    .btn-approve:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(52, 168, 83, 0.3);
    }

    .btn-reject {
        background: var(--gradient-danger);
        color: white;
    }

    .btn-reject:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(234, 67, 53, 0.3);
    }

    .btn-view {
        background: var(--gradient-secondary);
        color: #1a1a1a;
    }

    .btn-view:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 215, 0, 0.3);
    }

    .no-requests {
        text-align: center;
        padding: 3rem;
        color: #6c757d;
    }

    .no-requests i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .pagination-wrapper {
        margin-top: 2rem;
        display: flex;
        justify-content: center;
    }

    .pagination {
        background: #333;
        border-radius: 10px;
        padding: 0.5rem;
    }

    .pagination .page-link {
        background: transparent;
        border: none;
        color: #e0e0e0;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        margin: 0 0.2rem;
    }

    .pagination .page-link:hover {
        background: #FFD700;
        color: #1a1a1a;
    }

    .pagination .page-item.active .page-link {
        background: #FFD700;
        color: #1a1a1a;
    }

    /* Popup Notification Styles */
    .notification-popup {
        position: fixed;
        top: 20px;
        right: 20px;
        background: var(--gradient-success);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        box-shadow: var(--shadow-hover);
        z-index: 9999;
        transform: translateX(400px);
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        min-width: 300px;
        max-width: 400px;
    }

    .notification-popup.show {
        transform: translateX(0);
        opacity: 1;
    }

    .notification-popup.error {
        background: var(--gradient-danger);
    }

    .notification-popup.warning {
        background: var(--gradient-warning);
    }

    .notification-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }

    .notification-title {
        font-weight: 600;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .notification-close {
        background: none;
        border: none;
        color: white;
        font-size: 1.2rem;
        cursor: pointer;
        opacity: 0.8;
        transition: opacity 0.2s;
    }

    .notification-close:hover {
        opacity: 1;
    }

    .notification-body {
        font-size: 0.9rem;
        line-height: 1.4;
    }

    .notification-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 0 0 10px 10px;
        animation: progress 5s linear forwards;
    }

    @keyframes progress {
        from { width: 100%; }
        to { width: 0%; }
    }
</style>

<div class="admin-content">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-calendar-alt"></i>
            Reschedule Requests
        </h1>
        <p class="page-subtitle">Manage customer reschedule requests</p>
    </div>

    <div class="requests-container">
        @if($rescheduleRequests->count() > 0)
            @foreach($rescheduleRequests as $request)
                @if($request->booking_data)
                    <div class="request-card">
                        <div class="request-header">
                            <div class="request-info">
                                <div class="booking-reference">
                                    <i class="fas fa-hashtag"></i>
                                    {{ $request->booking_data->reference }}
                                </div>
                                <div class="customer-name">
                                    <i class="fas fa-user"></i>
                                    {{ $request->booking_data->customer_name }}
                                </div>
                                <div class="request-date">
                                    <i class="fas fa-clock"></i>
                                    Requested {{ $request->created_at->diffForHumans() }}
                                </div>
                            </div>
                            @if($request->has_conflict)
                                <div class="conflict-badge">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Conflict
                                </div>
                            @endif
                        </div>

                        <div class="request-details">
                            <div class="detail-section">
                                <div class="detail-title">
                                    <i class="fas fa-calendar-minus"></i>
                                    Current Booking
                                </div>
                                <div class="detail-content">
                                    <div class="date">{{ \Carbon\Carbon::parse($request->booking_data->date)->format('M d, Y') }}</div>
                                    <div class="time">{{ $request->booking_data->time_slot }}</div>
                                    <div class="duration">
                                        @if($request->resource_type === 'instrument_rental')
                                            {{ $request->booking_data->duration }}
                                        @else
                                            {{ $request->booking_data->duration }} hour(s)
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="detail-section">
                                <div class="detail-title">
                                    <i class="fas fa-calendar-plus"></i>
                                    Requested Changes
                                </div>
                                <div class="detail-content">
                                    @if($request->resource_type === 'instrument_rental')
                                        {{-- Instrument rental reschedule data --}}
                                        @if($request->requested_start_date)
                                            <div class="date">Start: {{ \Carbon\Carbon::parse($request->requested_start_date)->format('M d, Y') }}</div>
                                        @endif
                                        @if($request->requested_end_date)
                                            <div class="date">End: {{ \Carbon\Carbon::parse($request->requested_end_date)->format('M d, Y') }}</div>
                                        @endif
                                        <div class="time">Multi-day instrument rental</div>
                                    @else
                                        {{-- Studio booking reschedule data --}}
                                        @if($request->requested_date)
                                            <div class="date">{{ \Carbon\Carbon::parse($request->requested_date)->format('M d, Y') }}</div>
                                        @endif
                                        @if($request->requested_time_slot)
                                            <div class="time">{{ $request->requested_time_slot }}</div>
                                        @endif
                                        @if($request->requested_duration)
                                            <div class="duration">{{ $request->requested_duration }} hour(s)</div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="request-actions">
                            <a href="{{ route('admin.reschedule-request.show', $request->id) }}" class="btn btn-view">
                                <i class="fas fa-eye"></i>
                                View Details
                            </a>
                            
                            @if(!$request->has_conflict)
                                <form method="POST" action="{{ route('admin.reschedule-request.approve', $request->id) }}" style="display: inline;" class="approve-form" data-booking-ref="{{ $request->booking_data->reference }}">
                                    @csrf
                                    <button type="button" class="btn btn-approve approve-btn">
                                        <i class="fas fa-check"></i>
                                        Approve
                                    </button>
                                </form>
                            @endif
                            
                            <form method="POST" action="{{ route('admin.reschedule-request.reject', $request->id) }}" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-reject" onclick="return confirm('Are you sure you want to reject this reschedule request?')">
                                    <i class="fas fa-times"></i>
                                    Reject
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            @endforeach

            <div class="pagination-wrapper">
                {{ $rescheduleRequests->links() }}
            </div>
        @else
            <div class="no-requests">
                <i class="fas fa-calendar-check"></i>
                <h3>No Reschedule Requests</h3>
                <p>There are currently no pending reschedule requests.</p>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle approve button clicks
    document.querySelectorAll('.approve-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('.approve-form');
            const bookingRef = form.dataset.bookingRef;
            
            // Show confirmation popup
            showConfirmationDialog(
                'Approve Reschedule Request',
                `Are you sure you want to approve the reschedule request for booking ${bookingRef}?`,
                () => {
                    // Show loading state
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                    this.disabled = true;
                    
                    // Submit form via AJAX
                    submitApprovalForm(form, bookingRef, this);
                }
            );
        });
    });
    
    // Check for Laravel session messages and show notifications
    @if(session('success'))
        showNotification('success', 'Success', '{{ session('success') }}');
    @endif
    
    @if(session('error'))
        showNotification('error', 'Error', '{{ session('error') }}');
    @endif
});

function showConfirmationDialog(title, message, onConfirm) {
    const dialog = document.createElement('div');
    dialog.className = 'notification-popup show';
    dialog.style.background = 'var(--gradient-warning)';
    dialog.innerHTML = `
        <div class="notification-header">
            <div class="notification-title">
                <i class="fas fa-question-circle"></i>
                ${title}
            </div>
        </div>
        <div class="notification-body">
            ${message}
        </div>
        <div style="margin-top: 1rem; display: flex; gap: 0.5rem; justify-content: flex-end;">
            <button class="btn btn-sm" style="background: rgba(255,255,255,0.2); color: white; border: none; padding: 0.3rem 0.8rem; border-radius: 5px; cursor: pointer;" onclick="this.closest('.notification-popup').remove()">Cancel</button>
            <button class="btn btn-sm" style="background: rgba(255,255,255,0.9); color: #1a1a1a; border: none; padding: 0.3rem 0.8rem; border-radius: 5px; cursor: pointer; font-weight: 600;" onclick="confirmAction(this)">Confirm</button>
        </div>
    `;
    
    dialog.querySelector('button[onclick="confirmAction(this)"]').addEventListener('click', function() {
        dialog.remove();
        onConfirm();
    });
    
    document.body.appendChild(dialog);
}

function submitApprovalForm(form, bookingRef, button) {
    fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (response.ok) {
            return response.json().then(data => {
                if (data.success) {
                    showNotification('success', 'Approved Successfully', `Reschedule request for booking ${bookingRef} has been approved successfully!`);
                    // Remove the request card after a short delay
                    setTimeout(() => {
                        form.closest('.request-card').style.transition = 'all 0.5s ease';
                        form.closest('.request-card').style.transform = 'translateX(-100%)';
                        form.closest('.request-card').style.opacity = '0';
                        setTimeout(() => {
                            form.closest('.request-card').remove();
                            // Check if no more requests
                            if (document.querySelectorAll('.request-card').length === 0) {
                                location.reload();
                            }
                        }, 500);
                    }, 2000);
                } else {
                    showNotification('error', 'Approval Failed', data.message || 'Failed to approve the reschedule request.');
                    // Reset button
                    button.innerHTML = '<i class="fas fa-check"></i> Approve';
                    button.disabled = false;
                }
            });
        } else {
            return response.json().then(data => {
                throw new Error(data.message || 'Failed to approve request');
            }).catch(() => {
                throw new Error('Failed to approve request');
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Approval Failed', error.message || 'Failed to approve the reschedule request. Please try again.');
        // Reset button
        button.innerHTML = '<i class="fas fa-check"></i> Approve';
        button.disabled = false;
    });
}

function showNotification(type, title, message) {
    const notification = document.createElement('div');
    notification.className = `notification-popup ${type}`;
    
    const iconMap = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle'
    };
    
    notification.innerHTML = `
        <div class="notification-header">
            <div class="notification-title">
                <i class="${iconMap[type] || iconMap.info}"></i>
                ${title}
            </div>
            <button class="notification-close" onclick="this.closest('.notification-popup').remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="notification-body">
            ${message}
        </div>
        <div class="notification-progress"></div>
    `;
    
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 400);
    }, 5000);
}
</script>

@endsection