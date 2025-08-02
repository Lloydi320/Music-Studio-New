<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #ffd700;
            margin-bottom: 10px;
        }
        .booking-details {
            background-color: #fff;
            border: 2px solid #ffd700;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
        .value {
            color: #333;
        }
        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status.pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
        .contact-info {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">🎵 {{ $studioName }}</div>
        <h2>Booking Confirmation</h2>
    </div>

    <p>Dear {{ $userName }},</p>
    
    <p>Thank you for booking with {{ $studioName }}! Your booking has been successfully created and is currently pending approval.</p>

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
            <span class="label">Status:</span>
            <span class="value">
                <span class="status {{ strtolower($bookingStatus) }}">{{ $bookingStatus }}</span>
            </span>
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

    <p><strong>What's Next?</strong></p>
    <ul>
        <li>Your booking is currently <strong>pending approval</strong></li>
        <li>You will receive another email once your booking is approved</li>
        <li>If approved, a calendar event will be automatically created</li>
        <li>Please keep this reference number for your records: <strong>{{ $bookingReference }}</strong></li>
    </ul>

    <div class="contact-info">
        <p><strong>Need to make changes or have questions?</strong></p>
        <p>Contact us at: <a href="mailto:{{ $studioEmail }}">{{ $studioEmail }}</a></p>
    </div>

    <div class="footer">
        <p>Thank you for choosing {{ $studioName }}!</p>
        <p>This is an automated message. Please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} {{ $studioName }}. All rights reserved.</p>
    </div>
</body>
</html>