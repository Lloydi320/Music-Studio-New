<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - Lemon Hub Studio</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
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
        .verify-btn {
            display: inline-block;
            background-color: #ff6b35;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
        }
        .verify-btn:hover {
            background-color: #e55a2b;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🍋 Lemon Hub Studio</div>
            <h1>Welcome to Lemon Hub Studio!</h1>
        </div>

        <p>Hello {{ $user->name }},</p>

        <p>Thank you for registering with Lemon Hub Studio! To complete your registration and start booking our amazing music services, please verify your email address by clicking the button below:</p>

        <div style="text-align: center;">
            <a href="{{ $verificationUrl }}" class="verify-btn">Verify Email Address</a>
        </div>

        <div class="warning">
            <strong>Important:</strong> This verification link will expire in 60 minutes for security reasons. If you don't verify your email within this time, you'll need to request a new verification email.
        </div>

        <p>Once your email is verified, you'll be able to:</p>
        <ul>
            <li>📅 Book studio sessions for solo rehearsals, band practice, and lessons</li>
            <li>🎸 Rent musical instruments</li>
            <li>📞 Access our full range of music services</li>
            <li>💬 Leave feedback and reviews</li>
        </ul>

        <p>If you didn't create an account with us, please ignore this email.</p>

        <p>If the button above doesn't work, you can copy and paste this link into your browser:</p>
        <p style="word-break: break-all; color: #666; font-size: 12px;">{{ $verificationUrl }}</p>

        <div class="footer">
            <p>This email was sent from Lemon Hub Studio</p>
            <p>If you have any questions, please contact our support team.</p>
        </div>
    </div>
</body>
</html>