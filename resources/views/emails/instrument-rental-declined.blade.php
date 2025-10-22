<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Instrument Rental Declined - Lemon Hub Studio</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background: #f7f9fc; color: #333; margin: 0; padding: 0; }
        .email-container { max-width: 680px; margin: 30px auto; background: #fff; border: 1px solid #e6e9ef; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .header { background: #1a73e8; color: #fff; padding: 24px; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; }
        .subtitle { margin-top: 6px; font-size: 14px; color: #e9f3ff; }
        .content { padding: 24px; }
        .alert { background: #fde8e8; border: 1px solid #f5c2c2; border-radius: 8px; padding: 15px; margin: 20px 0; color: #b4231a; }
        .alert-icon { margin-right: 8px; }
        .rental-details h3 { margin: 0 0 12px 0; color: #333; }
        .detail-row { display: flex; justify-content: space-between; padding: 8px 12px; border: 1px dashed #e6e9ef; border-radius: 8px; margin-bottom: 8px; background: #fafbff; }
        .detail-label { font-weight: 600; color: #555; }
        .detail-value { color: #222; }
        .footer { background: #f2f6fb; padding: 18px 24px; text-align: center; color: #6c757d; font-size: 13px; }
        .status-badge { display: inline-block; padding: 4px 8px; border-radius: 6px; background: #fee2e2; color: #b91c1c; font-weight: 600; font-size: 12px; }
    </style>
</head>
<body>
<div class="email-container">
    <div class="header">
        <h1>🎵 {{ $studioName }}</h1>
        <p class="subtitle">Instrument Rental Declined</p>
    </div>
    <div class="content">
        <div class="alert">
            <span class="alert-icon">❌</span>
            <strong>We’re sorry.</strong> Your instrument rental request has been declined.
            <p style="margin-top: 10px;">This may be due to limited availability or policy constraints for the selected dates. If you’d like to adjust details or choose alternative dates or instruments, please reply to this email and we’ll be happy to assist.</p>
        </div>

        <div class="rental-details">
            <h3>📋 Rental Details</h3>
            <div class="detail-row">
                <span class="detail-label">Reference:</span>
                <span class="detail-value"><strong>{{ $rentalReference }}</strong></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">GCash Payment Reference:</span>
                <span class="detail-value">{{ $paymentReference ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Customer:</span>
                <span class="detail-value">{{ $customerName }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span class="detail-value">{{ $customerEmail }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Instrument Type:</span>
                <span class="detail-value">{{ $instrumentType }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Instrument:</span>
                <span class="detail-value">{{ $instrumentName }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Start Date:</span>
                <span class="detail-value">{{ $startDate }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">End Date:</span>
                <span class="detail-value">{{ $endDate }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Duration:</span>
                <span class="detail-value">{{ $duration }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value"><span class="status-badge">Cancelled</span></span>
            </div>
        </div>

        <p style="margin-top: 20px; color: #555;">If this was a mistake or you want to try a different option, reply here or contact us — we’ll help you find a suitable alternative.</p>
        <p style="margin-top: 10px; color: #6c757d; font-size: 14px;">This is an automated notification from {{ $studioName }}.</p>
    </div>
    <div class="footer">
        <p>
            <strong>{{ $studioName }}</strong><br />
            Email: <a href="mailto:{{ $studioEmail }}">{{ $studioEmail }}</a>
        </p>
        <p style="margin-top: 12px; font-size: 12px;">© {{ date('Y') }} {{ $studioName }}. All rights reserved.</p>
    </div>
</div>
</body>
</html>