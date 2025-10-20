@extends('layouts.admin')

@section('title', 'Instrument Rental Details')

@section('content')
<style>
    :root {
        --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --gradient-success: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --gradient-warning: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        --gradient-danger: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        --shadow-soft: 0 10px 40px rgba(0, 0, 0, 0.1);
        --shadow-hover: 0 20px 60px rgba(0, 0, 0, 0.2);
    }

    body {
        background: #1a1a1a;
        color: #ffffff;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        min-height: 100vh;
    }

    .admin-content {
        padding: 2rem;
        width: 100%;
        margin: 0;
        background: #1a1a1a;
        min-height: 100vh;
    }

    .page-header {
        background: linear-gradient(135deg, #ff8c00 0%, #ff6b35 100%);
        padding: 2rem;
        border-radius: 20px;
        margin-bottom: 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow-soft);
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: shimmer 3s ease-in-out infinite;
    }

    @keyframes shimmer {
        0%, 100% { transform: rotate(0deg); }
        50% { transform: rotate(180deg); }
    }

    .page-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        position: relative;
        z-index: 1;
    }

    .back-button {
        position: absolute;
        right: 2rem;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255,255,255,0.2);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        z-index: 2;
    }

    .back-button:hover {
        background: rgba(255,255,255,0.3);
        transform: translateY(-50%) translateX(5px);
        color: white;
        text-decoration: none;
    }

    .booking-layout {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
        width: 100%;
    }

    .booking-info-section {
        background: #2a2a2a;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: var(--shadow-soft);
        border: 1px solid rgba(255, 140, 0, 0.3);
    }

    .customer-info-section {
        background: #2a2a2a;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: var(--shadow-soft);
        border: 1px solid rgba(255, 140, 0, 0.3);
        height: fit-content;
    }

    .section-title {
        color: #ff8c00;
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-list {
        background: rgba(255, 140, 0, 0.05);
        border-radius: 12px;
        border: 1px solid rgba(255, 140, 0, 0.2);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid rgba(255, 140, 0, 0.1);
        transition: background-color 0.3s ease;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-item:hover {
        background: rgba(255, 140, 0, 0.08);
    }

    .info-label {
        color: #ff8c00;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        flex-shrink: 0;
        min-width: 150px;
    }

    .info-value {
        color: #ffffff;
        font-size: 1rem;
        font-weight: 600;
        text-align: right;
        flex: 1;
    }

    .receipt-toggle-btn {
        background: linear-gradient(135deg, #ff8c00 0%, #ff6b00 100%);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .receipt-toggle-btn:hover {
        background: linear-gradient(135deg, #ff6b00 0%, #e65100 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(255, 140, 0, 0.3);
    }

    .receipt-arrow {
        transition: transform 0.3s ease;
    }

    .receipt-arrow.rotated {
        transform: rotate(180deg);
    }

    .no-receipt-text {
        color: #666;
        font-style: italic;
    }

    .receipt-expanded-section {
        background: rgba(255, 140, 0, 0.05);
        border-radius: 12px;
        border: 1px solid rgba(255, 140, 0, 0.2);
        padding: 1.5rem;
        margin-top: 1rem;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .receipt-container {
        text-align: center;
    }

    .receipt-image {
        max-width: 100%;
        max-height: 400px;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .receipt-image:hover {
        transform: scale(1.02);
    }

    .receipt-info {
        color: #b0b0b0;
        font-size: 0.9rem;
        margin-top: 1rem;
        font-style: italic;
    }

    .detail-item {
        background: rgba(255, 140, 0, 0.1);
        padding: 1rem;
        border-radius: 10px;
        border-left: 4px solid #ff8c00;
    }

    .detail-label {
        color: #ff8c00;
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .detail-value {
        color: #ffffff;
        font-size: 1.1rem;
        font-weight: 500;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-pending {
        background: linear-gradient(135deg, #ffa726 0%, #ff9800 100%);
        color: #1a1a1a;
    }

    .status-approved {
        background: linear-gradient(135deg, #66bb6a 0%, #4caf50 100%);
        color: white;
    }

    .status-active {
        background: linear-gradient(135deg, #42a5f5 0%, #2196f3 100%);
        color: white;
    }

    .status-returned {
        background: linear-gradient(135deg, #ab47bc 0%, #9c27b0 100%);
        color: white;
    }

    .status-cancelled {
        background: linear-gradient(135deg, #ef5350 0%, #f44336 100%);
        color: white;
    }

    .customer-card {
        background: rgba(255, 140, 0, 0.05);
        border-radius: 15px;
        padding: 1.5rem;
        border: 1px solid rgba(255, 140, 0, 0.2);
    }

    .customer-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid rgba(255, 140, 0, 0.2);
    }

    .customer-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--gradient-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1.5rem;
        box-shadow: var(--shadow-soft);
    }

    .customer-details {
        flex: 1;
    }

    .customer-name {
        color: #ffffff;
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .customer-email {
        color: #b0b0b0;
        font-size: 1rem;
    }

    .customer-info {
        margin-bottom: 1.5rem;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(255, 140, 0, 0.1);
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        color: #ff8c00;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        color: #ffffff;
        font-weight: 600;
    }

    .status-section {
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .status-indicator {
        display: inline-block;
        padding: 0.75rem 1.5rem;
        border-radius: 25px;
        font-weight: 700;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
        flex-wrap: wrap;
    }

    .btn-approve {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        border: none;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        cursor: pointer;
        font-size: 0.9rem;
    }

    .btn-approve:hover {
        background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
        transform: translateY(-2px);
        box-shadow: var(--shadow-hover);
        color: white;
    }

    .btn-reject {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        border: none;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        cursor: pointer;
        font-size: 0.9rem;
    }

    .btn-reject:hover {
        background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
        transform: translateY(-2px);
        box-shadow: var(--shadow-hover);
        color: white;
    }

    .booking-timeline {
        background: #2a2a2a;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: var(--shadow-soft);
        border: 1px solid rgba(255, 140, 0, 0.3);
        margin-top: 2rem;
    }

    .timeline {
        position: relative;
        padding-left: 2rem;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 1rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(to bottom, #ff8c00, rgba(255, 140, 0, 0.3));
    }

    .timeline-item {
        position: relative;
        margin-bottom: 2rem;
        padding-left: 2rem;
    }

    .timeline-item:last-child {
        margin-bottom: 0;
    }

    .timeline-marker {
        position: absolute;
        left: -2.5rem;
        top: 0;
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        background: #666;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        border: 3px solid #2a2a2a;
    }

    .timeline-item.completed .timeline-marker {
        background: linear-gradient(135deg, #ff8c00 0%, #ff6b35 100%);
        color: #1a1a1a;
    }

    .timeline-content {
        background: rgba(255, 140, 0, 0.1);
        padding: 1rem;
        border-radius: 10px;
        border-left: 3px solid #ff8c00;
    }

    .timeline-title {
        color: #ff8c00;
        font-weight: 700;
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
    }

    .timeline-date {
        color: #b0b0b0;
        font-size: 0.9rem;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        border: none;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        cursor: pointer;
        font-size: 0.9rem;
    }

    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
        transform: translateY(-2px);
        box-shadow: var(--shadow-hover);
        color: white;
    }

    .btn-danger {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
    }

    .btn-danger:hover {
        background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
        transform: translateY(-2px);
        box-shadow: var(--shadow-hover);
        color: white;
    }

    .btn-warning {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        color: #1a1a1a;
    }

    .btn-warning:hover {
        background: linear-gradient(135deg, #e0a800 0%, #d39e00 100%);
        transform: translateY(-2px);
        box-shadow: var(--shadow-hover);
        color: #1a1a1a;
    }

    .receipt-section {
        background: rgba(255, 140, 0, 0.1);
        padding: 1.5rem;
        border-radius: 15px;
        border: 2px dashed #ff8c00;
        text-align: center;
        margin-top: 1.5rem;
    }

    .receipt-container {
        text-align: center;
    }

    .receipt-image {
        max-width: 100%;
        max-height: 400px;
        height: auto;
        border-radius: 10px;
        box-shadow: var(--shadow-soft);
        margin-top: 1rem;
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .receipt-image:hover {
        transform: scale(1.02);
        box-shadow: var(--shadow-hover);
    }

    .receipt-info {
        color: #FFD700;
        font-size: 0.9rem;
        margin-top: 0.5rem;
        font-style: italic;
    }

    .no-receipt-container {
        text-align: center;
        padding: 2rem;
    }

    .no-receipt {
        color: #b0b0b0;
        font-style: italic;
        font-size: 1rem;
        margin: 0;
    }

    /* Modal styles for receipt image */
    .receipt-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.9);
    }

    .receipt-modal-content {
        margin: auto;
        display: block;
        width: 90%;
        max-width: 800px;
        max-height: 90%;
        object-fit: contain;
        border-radius: 10px;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .receipt-modal-close {
        position: absolute;
        top: 20px;
        right: 35px;
        color: #fff;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
        z-index: 1001;
    }

    .receipt-modal-close:hover {
        color: #FFD700;
    }

    .notes-section {
        background: rgba(255, 215, 0, 0.05);
        padding: 1.5rem;
        border-radius: 15px;
        border-left: 4px solid #FFD700;
        margin-top: 1.5rem;
    }

    .notes-text {
        color: #e0e0e0;
        font-size: 1rem;
        line-height: 1.6;
        margin: 0;
    }

    @media (min-width: 1600px) {
        .booking-layout {
            grid-template-columns: 3fr 1fr;
        }
        
        .info-cards {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        }
    }

    @media (min-width: 2000px) {
        .booking-layout {
            grid-template-columns: 4fr 1fr;
        }
        
        .info-cards {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }
    }

    @media (max-width: 768px) {
        .booking-layout {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .info-cards {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .action-buttons {
            flex-direction: column;
        }

        .btn, .btn-approve, .btn-reject {
            justify-content: center;
        }

        .back-button {
            position: static;
            transform: none;
            margin-top: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .page-header {
            text-align: center;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .page-title {
            font-size: 2rem;
        }

        .customer-header {
            flex-direction: column;
            text-align: center;
        }

        .timeline {
            padding-left: 1rem;
        }

        .timeline-marker {
            left: -1.5rem;
        }
    }
</style>

<div class="admin-content">
    <div class="page-header">
        <h1 class="page-title">📋 Booking Details</h1>
        <a href="{{ route('admin.instrument-bookings') }}" class="back-button">
            <i class="fas fa-arrow-left"></i> Back to Bookings
        </a>
    </div>

    <div class="booking-layout">
        <!-- Left Side: Booking Information -->
        <div class="booking-info-section">
            <h2 class="section-title">
                <i class="fas fa-info-circle"></i>
                Booking Information
            </h2>

            <div class="info-list">
                <!-- Reference Number -->
                <div class="info-item">
                    <span class="info-label">REFERENCE NUMBER</span>
                    <span class="info-value">#{{ $rental->reference }}</span>
                </div>

                <!-- GCash Payment Reference Number -->
                <div class="info-item">
                    <span class="info-label">GCASH PAYMENT REFERENCE NUMBER</span>
                    <span class="info-value">{{ $rental->payment_reference ?? 'N/A' }}</span>
                </div>

            <!-- Payment Receipt as List Item -->
            <div class="info-item receipt-item">
                <span class="info-label">PAYMENT RECEIPT</span>
                <span class="info-value">
                    @if($rental->receipt_image)
                        <button class="receipt-toggle-btn" onclick="toggleReceipt()">
                            <i class="fas fa-receipt"></i> View Receipt
                            <i class="fas fa-chevron-down receipt-arrow" id="receiptArrow"></i>
                        </button>
                    @else
                        <span class="no-receipt-text">No receipt uploaded</span>
                    @endif
                </span>
            </div>
        </div>

        @if($rental->receipt_image)
        <!-- Expandable Receipt Section -->
        <div class="receipt-expanded-section" id="receiptExpanded" style="display: none;">
            <div class="receipt-container">
                <img src="{{ asset('storage/' . $rental->receipt_image) }}" alt="Payment Receipt" class="receipt-image" onclick="openReceiptModal(this.src)">
                <p class="receipt-info">Click image to view full size</p>
            </div>
        </div>
        @endif

                <!-- Service Type -->
                <div class="info-item">
                    <span class="info-label">SERVICE TYPE</span>
                    <span class="info-value">{{ $rental->instrument_type }}</span>
                </div>

                <!-- Band Name -->
                <div class="info-item">
                    <span class="info-label">BAND NAME</span>
                    <span class="info-value">{{ $rental->instrument_name }}</span>
                </div>

                <!-- Contact Email -->
                <div class="info-item">
                    <span class="info-label">CONTACT EMAIL</span>
                    <span class="info-value">{{ $rental->user->email ?? 'No email provided' }}</span>
                </div>

                <!-- Contact Number -->
                <div class="info-item">
                    <span class="info-label">CONTACT NUMBER</span>
                    <span class="info-value">{{ $rental->user->phone ?? 'Not provided' }}</span>
                </div>

                <!-- Duration -->
                <div class="info-item">
                    <span class="info-label">DURATION</span>
                    <span class="info-value">{{ $rental->rental_duration_days ?? $rental->duration }} day(s)</span>
                </div>

                <!-- Time Slot -->
                <div class="info-item">
                    <span class="info-label">TIME SLOT</span>
                    <span class="info-value">
                        @php
                            $startTime = null;
                            $endTime = null;
                            if (!empty($rental->delivery_time)) {
                                try {
                                    $startTime = \Carbon\Carbon::createFromFormat('H:i', $rental->delivery_time);
                                    if (!empty($rental->event_duration_hours)) {
                                        $endTime = (clone $startTime)->addHours($rental->event_duration_hours);
                                    }
                                } catch (\Exception $e) {
                                    // Fallback: leave as null if parsing fails
                                }
                            }
                        @endphp
                        @if($startTime && $endTime)
                            {{ $startTime->format('g:i A') }} - {{ $endTime->format('g:i A') }}
                        @elseif($startTime)
                            {{ $startTime->format('g:i A') }}
                        @else
                            -
                        @endif
                    </span>
                </div>

                <!-- Status -->
                <div class="info-item">
                    <span class="info-label">STATUS</span>
                    <span class="info-value">
                        <span class="status-badge status-{{ $rental->status }}">
                            @if($rental->status == 'pending')
                                ⏳ Pending
                            @elseif($rental->status == 'approved')
                                ✅ Approved
                            @elseif($rental->status == 'active')
                                🔄 Active
                            @elseif($rental->status == 'returned')
                                📦 Returned
                            @elseif($rental->status == 'cancelled')
                                ❌ Cancelled
                            @else
                                {{ ucfirst($rental->status) }}
                            @endif
                        </span>
                    </span>
                </div>

                <!-- Created -->
                <div class="info-item">
                    <span class="info-label">CREATED</span>
                    <span class="info-value">{{ $rental->created_at->format('M d, Y \\a\\t g:i A') }}</span>
                </div>

                <!-- Last Updated -->
                <div class="info-item">
                    <span class="info-label">LAST UPDATED</span>
                    <span class="info-value">{{ $rental->updated_at->format('M d, Y \\a\\t g:i A') }}</span>
                </div>

                @if($rental->event_duration)
                <div class="info-item">
                    <span class="info-label">EVENT DURATION</span>
                    <span class="info-value">{{ $rental->event_duration }}</span>
                </div>
                @endif

                @if($rental->pickup_location)
                <div class="info-item">
                    <span class="info-label">PICKUP LOCATION</span>
                    <span class="info-value">{{ $rental->pickup_location }}</span>
                </div>
                @endif

                @if($rental->return_location)
                <div class="info-item">
                    <span class="info-label">RETURN LOCATION</span>
                    <span class="info-value">{{ $rental->return_location }}</span>
                </div>
                @endif

                @if($rental->transportation_needed)
                <div class="info-item">
                    <span class="info-label">TRANSPORTATION</span>
                    <span class="info-value">{{ $rental->transportation_needed ? 'Required' : 'Not Required' }}</span>
                </div>
                @endif
            </div>

            @if($rental->notes)
            <div class="notes-section">
                <h3 class="section-title">
                    <i class="fas fa-sticky-note"></i>
                    Additional Notes
                </h3>
                <p class="notes-text">{{ $rental->notes }}</p>
            </div>
            @endif


        </div>

        <!-- Right Side: Customer Information -->
        <div class="customer-info-section">
            <h2 class="section-title">
                <i class="fas fa-user"></i>
                Customer Information
            </h2>

            <div class="customer-card">
                <div class="customer-header">
                    <div class="customer-avatar">
                        {{ strtoupper(substr($rental->user->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="customer-details">
                        <div class="customer-name">{{ $rental->user->name ?? 'Unknown User' }}</div>
                        <div class="customer-email">{{ $rental->user->email ?? 'No email provided' }}</div>
                    </div>
                </div>

                <div class="customer-info">
                    <div class="info-row">
                        <span class="info-label">CUSTOMER ID</span>
                        <span class="info-value">#{{ $rental->user->id ?? 'N/A' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">BOOKING DATE</span>
                        <span class="info-value">{{ $rental->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">TOTAL AMOUNT</span>
                        <span class="info-value">₱{{ number_format($rental->total_amount, 2) }}</span>
                    </div>
                </div>

                <div class="status-section">
                    <div class="status-indicator status-{{ $rental->status }}">
                        @if($rental->status == 'pending')
                            ⏳ PENDING
                        @elseif($rental->status == 'approved')
                            ✅ CONFIRMED
                        @elseif($rental->status == 'active')
                            🔄 ACTIVE
                        @elseif($rental->status == 'returned')
                            📦 RETURNED
                        @elseif($rental->status == 'cancelled')
                            ❌ CANCELLED
                        @else
                            {{ strtoupper($rental->status) }}
                        @endif
                    </div>
                </div>

                @if($rental->status == 'pending')
                <div class="action-buttons">
                    <form method="POST" action="{{ route('admin.rental-status', $rental->id) }}" style="display: inline;">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="btn btn-approve" onclick="return confirm('Are you sure you want to approve this rental?')">
                            <i class="fas fa-check"></i> Approve Rental
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.rental-status', $rental->id) }}" style="display: inline;">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" class="btn btn-reject" onclick="return confirm('Are you sure you want to reject this rental?')">
                            <i class="fas fa-times"></i> Reject Rental
                        </button>
                    </form>
                </div>
                @elseif($rental->status == 'approved')
                <div class="action-buttons">
                    <form method="POST" action="{{ route('admin.rental-status', $rental->id) }}" style="display: inline;">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="active">
                        <button type="submit" class="btn btn-warning" onclick="return confirm('Mark this rental as active?')">
                            <i class="fas fa-play"></i> Mark as Active
                        </button>
                    </form>
                </div>
                @elseif($rental->status == 'active')
                <div class="action-buttons">
                    <form method="POST" action="{{ route('admin.rental-status', $rental->id) }}" style="display: inline;">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="returned">
                        <button type="submit" class="btn btn-success" onclick="return confirm('Mark this rental as returned?')">
                            <i class="fas fa-check-circle"></i> Mark as Returned
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Booking Timeline -->
    <div class="booking-timeline">
        <h2 class="section-title">
            <i class="fas fa-clock"></i>
            Booking Timeline
        </h2>
        
        <div class="timeline">
            <div class="timeline-item {{ $rental->created_at ? 'completed' : '' }}">
                <div class="timeline-marker">1</div>
                <div class="timeline-content">
                    <div class="timeline-title">Booking Created</div>
                    <div class="timeline-date">{{ $rental->created_at->format('M d, Y \\a\\t g:i A') }}</div>
                </div>
            </div>
            
            <div class="timeline-item {{ in_array($rental->status, ['approved', 'active', 'returned']) ? 'completed' : '' }}">
                <div class="timeline-marker">2</div>
                <div class="timeline-content">
                    <div class="timeline-title">Booking Confirmed</div>
                    <div class="timeline-date">
                        @if(in_array($rental->status, ['approved', 'active', 'returned']))
                            {{ $rental->updated_at->format('M d, Y \\a\\t g:i A') }}
                        @else
                            Pending confirmation
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Receipt Image Modal -->
<div id="receiptModal" class="receipt-modal">
    <span class="receipt-modal-close" onclick="closeReceiptModal()">&times;</span>
    <img class="receipt-modal-content" id="modalReceiptImage">
</div>

<script>
function openReceiptModal(imageSrc) {
    const modal = document.getElementById('receiptModal');
    const modalImg = document.getElementById('modalReceiptImage');
    modal.style.display = 'block';
    modalImg.src = imageSrc;
}

function closeReceiptModal() {
    const modal = document.getElementById('receiptModal');
    modal.style.display = 'none';
}

function toggleReceipt() {
    const receiptSection = document.getElementById('receiptExpanded');
    const arrow = document.getElementById('receiptArrow');
    
    if (receiptSection.style.display === 'none' || receiptSection.style.display === '') {
        receiptSection.style.display = 'block';
        arrow.classList.add('rotated');
    } else {
        receiptSection.style.display = 'none';
        arrow.classList.remove('rotated');
    }
}

// Close modal when clicking outside the image
document.getElementById('receiptModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeReceiptModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeReceiptModal();
    }
});
</script>

@endsection