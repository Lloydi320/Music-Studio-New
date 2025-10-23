<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Instrument Rental Confirmed</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; }
        .container { max-width: 640px; margin: 24px auto; background: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden; }
        .header { background: #1a73e8; color: #fff; padding: 16px 20px; font-weight: bold; }
        .content { padding: 20px; color: #333; }
        .detail { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee; }
        .detail:last-child { border-bottom: none; }
        .label { color: #666; }
        .value { font-weight: 600; }
        .footer { padding: 12px 20px; background: #fafafa; color: #777; font-size: 12px; }
        .badge { display: inline-block; background: #16a34a; color: #fff; padding: 4px 8px; border-radius: 999px; font-size: 12px; font-weight: 600; }
        .note { color: #555; font-size: 14px; margin-top: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">Instrument Rental Confirmed • {{ $studioName }}</div>
        <div class="content">
            <p>Hi {{ $rental->user->name ?? ($rental->name ?? 'there') }},</p>
            <p>Your instrument rental <strong>{{ $rental->reference }}</strong> has been <span class="badge">confirmed</span>. Here are the details:</p>

            <h3 style="margin-top: 16px;">Rental Details</h3>
            <div class="detail"><span class="label">Instrument</span><span class="value">{{ $rental->instrument_type }}{{ $rental->instrument_name ? ' — ' . $rental->instrument_name : '' }}</span></div>
            <div class="detail"><span class="label">Start Date</span><span class="value">{{ \Carbon\Carbon::parse($rental->rental_start_date)->timezone(config('app.timezone'))->format('M j, Y') }}</span></div>
            <div class="detail"><span class="label">End Date</span><span class="value">{{ \Carbon\Carbon::parse($rental->rental_end_date)->timezone(config('app.timezone'))->format('M j, Y') }}</span></div>
            <div class="detail"><span class="label">Duration</span><span class="value">{{ $rental->rental_duration_days }} day(s)</span></div>
            @if(!empty($rental->transportation))
                <div class="detail"><span class="label">Transportation</span><span class="value">{{ ucfirst($rental->transportation) }}</span></div>
            @endif
            @if(!empty($rental->pickup_location))
                <div class="detail"><span class="label">Pickup</span><span class="value">{{ $rental->pickup_location }}</span></div>
            @endif
            @if(!empty($rental->return_location))
                <div class="detail"><span class="label">Return</span><span class="value">{{ $rental->return_location }}</span></div>
            @endif
            @if(!empty($rental->payment_reference))
                <div class="detail"><span class="label">GCash Reference</span><span class="value">{{ $rental->payment_reference }}</span></div>
            @endif

            <p class="note">If you need to make changes or have questions, just reply to this email and our team will assist you.</p>

            <p style="margin-top: 18px; color: #6c757d; font-size: 14px;">
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