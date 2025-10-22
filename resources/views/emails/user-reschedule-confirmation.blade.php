<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>We Received Your Reschedule Request</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8f9fa; }
        .card { background: #fff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); overflow: hidden; }
        .header { background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%); color: #333; padding: 24px; text-align: center; }
        .content { padding: 24px; }
        .detail { display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding: 8px 0; }
        .detail:last-child { border-bottom: none; }
        .label { font-weight: 600; color: #555; }
        .value { color: #222; }
        .footer { background: #f8f9fa; padding: 16px; text-align: center; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h2>Thanks! We received your reschedule request</h2>
            <p>{{ $studioName }}</p>
        </div>
        <div class="content">
            <p>Hi {{ $booking->user->name ?? 'there' }},</p>
            <p>We have received your request to reschedule booking <strong>{{ $booking->reference }}</strong>. Our team will review it and email you once it is approved or if we need more information.</p>

            <h3 style="margin-top: 16px;">Requested Changes</h3>
            <div class="detail"><span class="label">Date</span><span class="value">{{ \Carbon\Carbon::parse($rescheduleData['new_date'] ?? $rescheduleData['requested_date'] ?? $booking->date)->format('M j, Y') }}</span></div>
            <div class="detail"><span class="label">Time Slot</span><span class="value">{{ $rescheduleData['new_time_slot'] ?? $rescheduleData['requested_time_slot'] ?? $booking->time_slot }}</span></div>
            <div class="detail"><span class="label">Duration</span><span class="value">{{ $rescheduleData['new_duration'] ?? $rescheduleData['requested_duration'] ?? $booking->duration }} hour(s)</span></div>

            <p style="margin-top: 16px;">If you didn’t make this request, please reply to this email so we can assist you.</p>
        </div>
        <div class="footer">Reference: {{ $booking->reference }} • {{ $studioName }}</div>
    </div>
</body>
</html>