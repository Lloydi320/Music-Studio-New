<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Booking Rejected - {{ $studioName }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 720px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; border-bottom: 1px solid #eee; padding-bottom: 12px; margin-bottom: 20px; }
        .logo { font-size: 20px; font-weight: bold; color: #2c3e50; }
        h2 { margin: 10px 0 0; color: #e74c3c; }
        .booking-details { background: #fafafa; padding: 16px; border: 1px solid #eee; border-radius: 6px; }
        .detail-row { display: flex; justify-content: space-between; padding: 6px 0; }
        .label { color: #555; font-weight: 600; }
        .value { color: #222; }
        .footer { text-align: center; margin-top: 28px; padding-top: 16px; border-top: 1px solid #eee; color: #666; font-size: 14px; }
        .contact-info a { color: #2c7be5; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🎵 {{ $studioName }}</div>
            <h2>Booking Rejected</h2>
        </div>

        <p>Dear {{ $userName }},</p>
        <p>We’re sorry to inform you that your booking request has been <strong>rejected</strong>.</p>
        <p>This may be due to scheduling conflicts or other availability issues. You can submit a new booking request at a different date or time.</p>

        <div class="booking-details">
            <h3>📅 Booking Details</h3>
            <div class="detail-row">
                <span class="label">Reference Number:</span>
                <span class="value">{{ $bookingReference }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Date:</span>
                <span class="value">{{ $bookingDate }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Time:</span>
                <span class="value">{{ $bookingTime }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Duration:</span>
                <span class="value">{{ $bookingDuration }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Customer:</span>
                <span class="value">{{ $userName }}</span>
            </div>
            <div class="detail-row">
                <span class="label">Email:</span>
                <span class="value">{{ $userEmail }}</span>
            </div>
        </div>

        <p><strong>Next Steps</strong></p>
        <ul>
            <li>You may submit a new booking with a different schedule</li>
            <li>If you have questions, contact us and include your reference number</li>
        </ul>

        <div class="contact-info">
            <p>Contact us at: <a href="mailto:{{ $studioEmail }}">{{ $studioEmail }}</a></p>
        </div>

        <div class="footer">
            <p>Thank you for considering {{ $studioName }}.</p>
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} {{ $studioName }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>