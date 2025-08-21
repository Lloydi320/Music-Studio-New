<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reschedule Request - {{ $booking->reference }}</title>
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
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
            color: #856404;
        }
        .alert-icon {
            font-size: 20px;
            margin-right: 10px;
        }
        .booking-details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .detail-label {
            font-weight: 600;
            color: #495057;
        }
        .detail-value {
            color: #333;
        }
        .changes-section {
            margin: 25px 0;
        }
        .changes-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        .change-item {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 0 8px 8px 0;
        }
        .change-from {
            color: #d32f2f;
            text-decoration: line-through;
            font-weight: 500;
        }
        .change-to {
            color: #388e3c;
            font-weight: 600;
        }
        .action-buttons {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 0 10px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .btn-primary {
            background: #007bff;
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #6c757d;
            border-top: 1px solid #e9ecef;
        }
        .reference {
            background: #ffd700;
            color: #333;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🔄 Reschedule Request - Action Required</h1>
            <p class="subtitle">{{ $studioName }}</p>
        </div>
        
        <div class="content">
            <div class="alert">
                <span class="alert-icon">⚠️</span>
                <strong>New reschedule request requires your attention!</strong>
            </div>
            
            <p>Hello Admin,</p>
            
            <p>A customer has submitted a reschedule request for booking <span class="reference">{{ $booking->reference }}</span>. Please review the details below and take appropriate action.</p>
            
            <div class="booking-details">
                <h3 style="margin-top: 0; color: #333;">📋 Current Booking Details</h3>
                <div class="detail-row">
                    <span class="detail-label">Reference:</span>
                    <span class="detail-value">{{ $booking->reference }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Band Name:</span>
                    <span class="detail-value">{{ $booking->band_name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Customer:</span>
                    <span class="detail-value">{{ $booking->user->name ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value">{{ $booking->user->email ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">{{ ucfirst($booking->status) }}</span>
                </div>
            </div>
            
            <div class="changes-section">
                <h3 class="changes-title">🔄 Requested Changes</h3>
                
                <div class="change-item">
                    <strong>📅 Date:</strong><br>
                    <span class="change-from">{{ \Carbon\Carbon::parse($rescheduleData['old_date'])->format('l, F j, Y') }}</span><br>
                    <span class="change-to">{{ \Carbon\Carbon::parse($rescheduleData['new_date'])->format('l, F j, Y') }}</span>
                </div>
                
                <div class="change-item">
                    <strong>⏰ Time Slot:</strong><br>
                    <span class="change-from">{{ $rescheduleData['old_time_slot'] }}</span><br>
                    <span class="change-to">{{ $rescheduleData['new_time_slot'] }}</span>
                </div>
                
                <div class="change-item">
                    <strong>⏱️ Duration:</strong><br>
                    <span class="change-from">{{ $rescheduleData['old_duration'] }} hour(s)</span><br>
                    <span class="change-to">{{ $rescheduleData['new_duration'] }} hour(s)</span>
                </div>
            </div>
            
            <div class="action-buttons">
                <a href="{{ url('/admin/dashboard') }}" class="btn btn-primary">📊 Review & Process Request</a>
                <a href="{{ url('/admin/bookings') }}" class="btn btn-secondary">👁️ View Original Booking</a>
            </div>
            
            <p><strong>⚠️ Manual Action Required:</strong></p>
            <p><strong>Important:</strong> This is a reschedule REQUEST only. The original booking has NOT been changed automatically.</p>
            <p>You need to manually review and approve/reject this request in the admin panel.</p>
            
            <p><strong>Next Steps:</strong></p>
            <ul>
                <li>Review the reschedule request details</li>
                <li>Check for any scheduling conflicts</li>
                <li>Approve or reject the request in the admin panel</li>
                <li>The customer will be notified of your decision</li>
            </ul>
            
            <p>Thank you for your prompt attention to this matter.</p>
        </div>
        
        <div class="footer">
            <p>This is an automated notification from {{ $studioName }}.</p>
            <p>Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} {{ $studioName }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>