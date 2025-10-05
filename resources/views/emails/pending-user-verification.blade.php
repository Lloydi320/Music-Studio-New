<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - Lemon Hub Studio</title>
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
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #ff6b35;
            margin-bottom: 10px;
        }
        .welcome-message {
            background: linear-gradient(135deg, #ff6b35, #f7931e);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 25px;
        }
        .verify-button {
            display: inline-block;
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            margin: 20px 0;
            transition: transform 0.2s;
        }
        .verify-button:hover {
            transform: translateY(-2px);
            text-decoration: none;
            color: white;
        }
        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .warning-box {
            background: #fff3e0;
            border-left: 4px solid #ff9800;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
        .features-list {
            list-style: none;
            padding: 0;
        }
        .features-list li {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .features-list li:before {
            content: "✅ ";
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="logo">🍋 Lemon Hub Studio</div>
            <h1>Welcome to Our Music Community!</h1>
        </div>

        <div class="welcome-message">
            <h2>Hi {{ $pendingUser->name }}! 👋</h2>
            <p>Thank you for joining Lemon Hub Studio. You're just one step away from accessing our amazing music services!</p>
        </div>

        <div class="info-box">
            <strong>📧 Email Verification Required</strong><br>
            To complete your registration and access all features, please verify your email address by clicking the button below.
        </div>

        <div style="text-align: center;">
            <a href="{{ $verificationUrl }}" class="verify-button">
                🔐 Verify My Email Address
            </a>
        </div>

        <div class="warning-box">
            <strong>⏰ Important:</strong> This verification link will expire in 24 hours for security reasons. If you don't verify within this time, you'll need to register again.
        </div>

        <h3>What you'll get access to after verification:</h3>
        <ul class="features-list">
            <li>Book studio sessions and rehearsal rooms</li>
            <li>Rent musical instruments</li>
            <li>Schedule music lessons</li>
            <li>Manage your bookings and preferences</li>
            <li>Receive important notifications</li>
            <li>Access exclusive member benefits</li>
        </ul>

        <div class="info-box">
            <strong>🔗 Can't click the button?</strong><br>
            Copy and paste this link into your browser:<br>
            <a href="{{ $verificationUrl }}" style="word-break: break-all; color: #2196f3;">{{ $verificationUrl }}</a>
        </div>

        <div class="footer">
            <p><strong>Lemon Hub Studio</strong><br>
            Your Premier Music Destination</p>
            
            <p>If you didn't create an account with us, please ignore this email.</p>
            
            <p style="margin-top: 20px;">
                <small>This is an automated email. Please do not reply to this message.</small>
            </p>
        </div>
    </div>
</body>
</html>