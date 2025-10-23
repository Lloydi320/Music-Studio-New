<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reschedule Approved</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; }
        .container { max-width: 640px; margin: 24px auto; background: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden; }
        .header { background: #222; color: #fff; padding: 16px 20px; font-weight: bold; }
        .content { padding: 20px; color: #333; }
        .detail { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee; }
        .detail:last-child { border-bottom: none; }
        .label { color: #666; }
        .value { font-weight: 600; }
        .footer { padding: 12px 20px; background: #fafafa; color: #777; font-size: 12px; }
        .badge { display: inline-block; background: #16a34a; color: #fff; padding: 4px 8px; border-radius: 999px; font-size: 12px; font-weight: 600; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">Reschedule Approved • {{ $studioName }}</div>
        <div class="content">
            <p>Hi {{ $booking->band_name ?? $booking->user->name ?? 'there' }},</p>
            <p>Your reschedule request for booking <strong>{{ $booking->reference }}</strong> has been <span class="badge">approved</span>. We’ve updated your booking to the new schedule below.</p>

            <h3 style="margin-top: 16px;">New Schedule</h3>
            <div class="detail"><span class="label">Date</span><span class="value">{{ \Carbon\Carbon::parse($booking->date)->timezone(config('app.timezone'))->format('M j, Y') }}</span></div>
            <div class="detail"><span class="label">Time Slot</span><span class="value">{{ $booking->time_slot }}</span></div>
            <div class="detail"><span class="label">Duration</span><span class="value">{{ $booking->duration }} hour(s)</span></div>

            @if(!empty($previousData))
                <h3 style="margin-top: 16px;">Previous Schedule</h3>
                <div class="detail"><span class="label">Date</span><span class="value">{{ \Carbon\Carbon::parse($previousData['date'] ?? $booking->date)->timezone(config('app.timezone'))->format('M j, Y') }}</span></div>
                <div class="detail"><span class="label">Time Slot</span><span class="value">{{ $previousData['time_slot'] ?? $booking->time_slot }}</span></div>
                <div class="detail"><span class="label">Duration</span><span class="value">{{ $previousData['duration'] ?? $booking->duration }} hour(s)</span></div>
            @endif

            <p style="margin-top: 16px;">If you have any questions, just reply to this email and we’ll help out.</p>
        </div>
        <div class="footer">Reference: {{ $booking->reference }} • {{ $studioName }}</div>
    </div>
</body>
</html>