<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instrument Rental {{ $recipientType === 'user' ? 'Confirmation' : 'Request' }} - {{ $rentalReference }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .email-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
            color: #333;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .header .subtitle {
            margin: 5px 0 0 0;
            font-size: 16px;
            opacity: 0.8;
        }
        .content {
            padding: 30px;
        }
        .alert {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
            color: #155724;
        }
        .alert-icon {
            font-size: 20px;
            margin-right: 10px;
        }
        .rental-details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
            flex: 1;
        }
        .detail-value {
            flex: 2;
            text-align: right;
            color: #212529;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            background: #fff3cd;
            color: #856404;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px 30px;
            text-align: center;
            font-size: 14px;
            color: #6c757d;
        }
        .footer a {
            color: #ffd700;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        .action-needed {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🎵 {{ $studioName }}</h1>
            <p class="subtitle">Instrument Rental {{ $recipientType === 'user' ? 'Confirmation' : 'Request' }}</p>
        </div>
        
        <div class="content">
            <div class="alert" style="{{ $recipientType === 'user' ? 'background:#e8f0fe;border-color:#cce1ff;color:#1a73e8' : '' }}">
                <span class="alert-icon">🎸</span>
                @if($recipientType === 'user')
                    <strong>Your Instrument Rental Request Was Received:</strong> We’ve logged your request and will contact you soon.
                @else
                    <strong>New Instrument Rental Request:</strong> A customer has submitted a new instrument rental request.
                @endif
            </div>
            
            <div class="rental-details">
                <h3 style="margin-top: 0; color: #333;">📋 Rental Details</h3>
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
                    <span class="detail-label">Total Amount:</span>
                    <span class="detail-value"><strong>₱{{ $totalAmount }}</strong></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Pickup Location:</span>
                    <span class="detail-value">{{ $pickupLocation }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Special Notes:</span>
                    <span class="detail-value">{{ $notes }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">
                        <span class="status-badge">{{ $status }}</span>
                    </span>
                </div>
            </div>
            
            @if($recipientType !== 'user')
            <div class="action-needed">
                <span class="alert-icon">⚠️</span>
                <strong>Action Required:</strong> Please review this instrument rental request and take appropriate action through the admin dashboard.
            </div>
            @else
            <p style="margin-top: 20px; color: #6c757d; font-size: 14px;">
                We’ll confirm availability and follow up via email or SMS. If you need to change details, reply to this email or contact us directly.
            </p>
            @endif
            
            <p style="margin-top: 30px; color: #6c757d; font-size: 14px;">
                This is an automated notification from {{ $studioName }}.
            </p>
        </div>
        
        <div class="footer">
            <p>
                <strong>{{ $studioName }}</strong><br>
                Email: <a href="mailto:{{ $studioEmail }}">{{ $studioEmail }}</a>
            </p>
            <p style="margin-top: 15px; font-size: 12px;">
                © {{ date('Y') }} {{ $studioName }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>