@extends('layouts.admin')

@section('title', 'Booking Details')

@section('content')
<style>
    :root {
        --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --gradient-success: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --gradient-warning: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        --gradient-danger: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        --shadow-soft: 0 10px 40px rgba(0, 0, 0, 0.1);
        --shadow-hover: 0 20px 60px rgba(0, 0, 0, 0.15);
    }

    .admin-content {
        padding: 2rem;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
    }

    .page-header {
        background: var(--gradient-primary);
        color: white;
        padding: 2rem;
        border-radius: 20px;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-soft);
        position: relative;
        overflow: hidden;
        display: flex;
        justify-content: space-between;
        align-items: center;
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

    .back-button {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        padding: 0.75rem 1.5rem;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
        z-index: 1;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .back-button:hover {
        background: rgba(255, 255, 255, 0.3);
        border-color: rgba(255, 255, 255, 0.5);
        transform: translateY(-2px);
        color: white;
        text-decoration: none;
    }

    .booking-details-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .details-card {
        background: white;
        padding: 2rem;
        border-radius: 20px;
        box-shadow: var(--shadow-soft);
        transition: all 0.3s ease;
    }

    .details-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
    }

    .card-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        background: var(--gradient-primary);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid #f1f3f4;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 600;
        color: #333;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }

    .detail-value {
        color: #666;
        font-weight: 500;
        text-align: right;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }

    .status-pending {
        background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%);
        color: #2d3436;
    }

    .status-confirmed {
        background: var(--gradient-success);
        color: white;
    }

    .status-rejected {
        background: var(--gradient-danger);
        color: white;
    }

    .user-info-card {
        text-align: center;
    }

    .user-avatar-large {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: var(--gradient-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 2rem;
        margin: 0 auto 1rem;
        box-shadow: var(--shadow-soft);
    }

    .user-name {
        font-size: 1.5rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .user-email {
        color: #666;
        font-size: 1rem;
        margin-bottom: 1.5rem;
    }

    .user-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .user-stat {
        text-align: center;
        padding: 1rem;
        background: linear-gradient(135deg, #f8f9ff 0%, #f0f4ff 100%);
        border-radius: 10px;
    }

    .user-stat-number {
        font-size: 1.5rem;
        font-weight: 700;
        background: var(--gradient-primary);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .user-stat-label {
        color: #666;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 0.25rem;
    }

    .actions-card {
        grid-column: 1 / -1;
        text-align: center;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        justify-content: center;
        flex-wrap: wrap;
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
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.9rem;
    }

    .btn-success {
        background: var(--gradient-success);
        color: white;
    }

    .btn-danger {
        background: var(--gradient-danger);
        color: white;
    }

    .btn-warning {
        background: var(--gradient-warning);
        color: #333;
    }

    .btn-secondary {
        background: #f8f9fa;
        color: #666;
        border: 2px solid #e1e5e9;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-soft);
        text-decoration: none;
        color: inherit;
    }

    .reference-id {
        font-size: 1.2rem;
        font-weight: 700;
        background: var(--gradient-secondary);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .booking-timeline {
        margin-top: 2rem;
    }

    .timeline-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem 0;
        border-bottom: 1px solid #f1f3f4;
    }

    .timeline-item:last-child {
        border-bottom: none;
    }

    .timeline-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--gradient-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.9rem;
    }

    .timeline-content {
        flex: 1;
    }

    .timeline-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.25rem;
    }

    .timeline-date {
        color: #666;
        font-size: 0.85rem;
    }

    /* Image Preview Styles */
    .image-preview {
        margin-top: 0.5rem;
    }

    .booking-image {
        max-width: 200px;
        max-height: 150px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: all 0.3s ease;
        object-fit: cover;
    }

    .booking-image:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    /* Image Modal Styles */
    .image-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        animation: fadeIn 0.3s ease;
    }

    .image-modal-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        max-width: 90%;
        max-height: 90%;
        border-radius: 10px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }

    .image-modal img {
        width: 100%;
        height: auto;
        border-radius: 10px;
    }

    .image-modal-close {
        position: absolute;
        top: 15px;
        right: 25px;
        color: white;
        font-size: 35px;
        font-weight: bold;
        cursor: pointer;
        z-index: 1001;
        background: rgba(0, 0, 0, 0.5);
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .image-modal-close:hover {
        background: rgba(0, 0, 0, 0.8);
        transform: scale(1.1);
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @media (max-width: 768px) {
        .admin-content {
            padding: 1rem;
        }

        .page-header {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
        }

        .page-title {
            font-size: 2rem;
        }

        .booking-details-grid {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            flex-direction: column;
        }

        .user-stats {
            grid-template-columns: 1fr;
        }

        .booking-image {
            max-width: 150px;
            max-height: 100px;
        }
    }
</style>

<div class="admin-content">
    <div class="page-header">
        <h1 class="page-title">📋 Booking Details</h1>
        <a href="{{ route('admin.bookings') }}" class="back-button">
            <i class="fas fa-arrow-left"></i> Back to Bookings
        </a>
    </div>

    <div class="booking-details-grid">
        <!-- Main Booking Details -->
        <div class="details-card">
            <h3 class="card-title">
                <i class="fas fa-info-circle"></i> Booking Information
            </h3>
            
            <div class="detail-row">
                <span class="detail-label">Reference Code</span>
                <span class="detail-value reference-id">#{{ $booking->reference_code ?? str_pad($booking->id, 4, '0', STR_PAD_LEFT) }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Service Type</span>
                <span class="detail-value">🎵 Studio Rental</span>
            </div>
            
            @if($booking->band_name)
            <div class="detail-row">
                <span class="detail-label">Band Name</span>
                <span class="detail-value">🎸 {{ $booking->band_name }}</span>
            </div>
            @endif
            
            @if($booking->email)
            <div class="detail-row">
                <span class="detail-label">Contact Email</span>
                <span class="detail-value">📧 {{ $booking->email }}</span>
            </div>
            @endif
            
            @if($booking->contact_number)
            <div class="detail-row">
                <span class="detail-label">Contact Number</span>
                <span class="detail-value">📞 {{ $booking->contact_number }}</span>
            </div>
            @endif
            
            <div class="detail-row">
                <span class="detail-label">Duration</span>
                <span class="detail-value">{{ $booking->duration }} hours</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Booking Date</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($booking->date)->format('l, F j, Y') }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Time Slot</span>
                <span class="detail-value">{{ $booking->start_time }} - {{ $booking->end_time }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Status</span>
                <span class="detail-value">
                    <span class="status-badge status-{{ $booking->status }}">
                        @if($booking->status === 'pending')
                            ⏳ Pending
                        @elseif($booking->status === 'confirmed')
                            ✅ Confirmed
                        @elseif($booking->status === 'rejected')
                            ❌ Rejected
                        @else
                            {{ ucfirst($booking->status) }}
                        @endif
                    </span>
                </span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Created</span>
                <span class="detail-value">{{ $booking->created_at->format('M j, Y \\a\\t g:i A') }}</span>
            </div>
            
            @if($booking->updated_at != $booking->created_at)
            <div class="detail-row">
                <span class="detail-label">Last Updated</span>
                <span class="detail-value">{{ $booking->updated_at->format('M j, Y \\a\\t g:i A') }}</span>
            </div>
            @endif
            
            @if($booking->image_path)
            <div class="detail-row">
                <span class="detail-label">Uploaded Image</span>
                <div class="detail-value">
                    <div class="image-preview">
                        <img src="{{ asset('storage/' . $booking->image_path) }}" alt="Booking Image" class="booking-image" onclick="openImageModal(this.src)">
                    </div>
                </div>
            </div>
            @endif

            <!-- Booking Timeline -->
            <div class="booking-timeline">
                <h4 class="card-title">
                    <i class="fas fa-history"></i> Booking Timeline
                </h4>
                
                <div class="timeline-item">
                    <div class="timeline-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-title">Booking Created</div>
                        <div class="timeline-date">{{ $booking->created_at->format('M j, Y \\a\\t g:i A') }}</div>
                    </div>
                </div>
                
                @if($booking->status === 'confirmed')
                <div class="timeline-item">
                    <div class="timeline-icon">
                        <i class="fas fa-check"></i>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-title">Booking Confirmed</div>
                        <div class="timeline-date">{{ $booking->updated_at->format('M j, Y \\a\\t g:i A') }}</div>
                    </div>
                </div>
                @elseif($booking->status === 'rejected')
                <div class="timeline-item">
                    <div class="timeline-icon">
                        <i class="fas fa-times"></i>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-title">Booking Rejected</div>
                        <div class="timeline-date">{{ $booking->updated_at->format('M j, Y \\a\\t g:i A') }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Customer Information -->
        <div class="details-card user-info-card">
            <h3 class="card-title">
                <i class="fas fa-user"></i> Customer Information
            </h3>
            
            <div class="user-avatar-large">
                {{ strtoupper(substr($booking->user->name, 0, 1)) }}
            </div>
            
            <div class="user-name">{{ $booking->user->name }}</div>
            <div class="user-email">{{ $booking->user->email }}</div>
            
            @if($booking->user->phone)
            <div class="detail-row">
                <span class="detail-label">Phone</span>
                <span class="detail-value">{{ $booking->user->phone }}</span>
            </div>
            @endif
            
            <div class="detail-row">
                <span class="detail-label">Member Since</span>
                <span class="detail-value">{{ $booking->user->created_at->format('M Y') }}</span>
            </div>
            
            <!-- User Stats -->
            <div class="user-stats">
                <div class="user-stat">
                    <div class="user-stat-number">{{ $booking->user->bookings()->count() }}</div>
                    <div class="user-stat-label">Total Bookings</div>
                </div>
                <div class="user-stat">
                    <div class="user-stat-number">{{ $booking->user->bookings()->where('status', 'confirmed')->count() }}</div>
                    <div class="user-stat-label">Confirmed</div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="details-card actions-card">
            <h3 class="card-title">
                <i class="fas fa-cogs"></i> Actions
            </h3>
            
            <div class="action-buttons">
                @if($booking->status === 'pending')
                    <form method="POST" action="{{ route('admin.booking.approve', $booking->id) }}" style="display: inline;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to accept this booking?')">
                            <i class="fas fa-check"></i> Accept Booking
                        </button>
                    </form>
                    
                    <form method="POST" action="{{ route('admin.booking.reject', $booking->id) }}" style="display: inline;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to reject this booking?')">
                            <i class="fas fa-times"></i> Reject Booking
                        </button>
                    </form>
                @elseif($booking->status === 'confirmed')
                    <button class="btn btn-warning" onclick="rescheduleBooking({{ $booking->id }})">
                        <i class="fas fa-calendar-alt"></i> Reschedule
                    </button>
                @endif
                
                <a href="mailto:{{ $booking->user->email }}" class="btn btn-secondary">
                    <i class="fas fa-envelope"></i> Contact Customer
                </a>
                
                <button class="btn btn-secondary" onclick="window.print()">
                    <i class="fas fa-print"></i> Print Details
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="image-modal">
    <span class="image-modal-close" onclick="closeImageModal()">&times;</span>
    <div class="image-modal-content">
        <img id="modalImage" src="" alt="Booking Image">
    </div>
</div>

<script>
    function rescheduleBooking(bookingId) {
        if (confirm('Are you sure you want to reschedule this booking?')) {
            // Implement reschedule logic
            alert('Reschedule functionality will be implemented');
        }
    }

    // Image Modal Functions
    function openImageModal(imageSrc) {
        const modal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        modal.style.display = 'block';
        modalImage.src = imageSrc;
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }

    function closeImageModal() {
        const modal = document.getElementById('imageModal');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto'; // Restore scrolling
    }

    // Close modal when clicking outside the image
    document.getElementById('imageModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeImageModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeImageModal();
        }
    });

    // Add smooth animations
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.details-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.5s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });
</script>
@endsection