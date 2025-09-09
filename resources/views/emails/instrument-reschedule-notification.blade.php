<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instrument Rental Reschedule Request - {{ $rental->reference }}</title>
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
        .rental-details {
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
            transition: all 0.3s ease;
        }
        .btn-approve {
            background: #28a745;
            color: white;
        }
        .btn-approve:hover {
            background: #218838;
        }
        .btn-reject {
            background: #dc3545;
            color: white;
        }
        .btn-reject:hover {
            background: #c82333;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
        }
        .footer a {
            color: #ffd700;
            text-decoration: none;
        }
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .content {
                padding: 20px;
            }
            .detail-row {
                flex-direction: column;
            }
            .detail-label {
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🎵 {{ $studioName }}</h1>
            <p class="subtitle">Instrument Rental Reschedule Request</p>
        </div>
        
        <div class="content">
            <div class="alert">
                <span class="alert-icon">⏰</span>
                <strong>New Reschedule Request:</strong> A customer has requested to reschedule their instrument rental.
            </div>
            
            <div class="rental-details">
                <h3 style="margin-top: 0; color: #333;">📋 Original Rental Details</h3>
                <div class="detail-row">
                    <span class="detail-label">Reference:</span>
                    <span class="detail-value">{{ $rental->reference }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Customer:</span>
                    <span class="detail-value">{{ $rental->user->name ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value">{{ $rental->user->email ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Instrument:</span>
                    <span class="detail-value">{{ $rental->instrument_type }} - {{ $rental->instrument_name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Current Dates:</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($rental->rental_start_date)->format('M j, Y') }} - {{ \Carbon\Carbon::parse($rental->rental_end_date)->format('M j, Y') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Duration:</span>
                    <span class="detail-value">{{ $rental->rental_duration_days }} days</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Total Amount:</span>
                    <span class="detail-value">${{ number_format($rental->total_amount, 2) }}</span>
                </div>
            </div>
            
            <div class="changes-section">
                <h3 class="changes-title">🔄 Requested Changes</h3>
                
                <div class="change-item">
                    <strong>📅 Rental Dates:</strong><br>
                    <span class="change-from">From: {{ \Carbon\Carbon::parse($rescheduleData['old_start_date'])->format('M j, Y') }} - {{ \Carbon\Carbon::parse($rescheduleData['old_end_date'])->format('M j, Y') }}</span><br>
                    <span class="change-to">To: {{ \Carbon\Carbon::parse($rescheduleData['new_start_date'])->format('M j, Y') }} - {{ \Carbon\Carbon::parse($rescheduleData['new_end_date'])->format('M j, Y') }}</span>
                </div>
                
                @if(isset($rescheduleData['old_duration']) && isset($rescheduleData['new_duration']))
                <div class="change-item">
                    <strong>⏱️ Duration:</strong><br>
                    <span class="change-from">From: {{ $rescheduleData['old_duration'] }} days</span><br>
                    <span class="change-to">To: {{ $rescheduleData['new_duration'] }} days</span>
                </div>
                @endif
            </div>
            
            <div class="action-buttons">
                <a href="{{ url('/admin/reschedule-requests') }}" class="btn btn-approve">
                    ✅ Review Request
                </a>
            </div>
            
            <p style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e9ecef; color: #6c757d; font-size: 14px;">
                <strong>📝 Note:</strong> Please review this reschedule request in your admin panel. You can approve or reject the request based on availability and studio policies.
            </p>
        </div>
        
        <div class="footer">
            <p>This is an automated notification from {{ $studioName }}.<br>
            Please do not reply to this email.</p>
            <p><a href="{{ url('/admin') }}">Admin Dashboard</a> | <a href="{{ url('/') }}">Studio Website</a></p>
        </div>
    </div>
</body>
</html>