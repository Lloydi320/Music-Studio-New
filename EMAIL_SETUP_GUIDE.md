# 📧 Email Notification Setup Guide

## 🚨 Current Issue

Your Google notification system is **NOT WORKING** due to Gmail authentication failure. The error shows:

```
535-5.7.8 Username and Password not accepted. For more information, go to
535 5.7.8 https://support.google.com/mail/?p=BadCredentials
```

## 🔍 Problem Analysis

The current configuration in `.env` file:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=lemonhubstudioweb@gmail.com
MAIL_PASSWORD=lemonhubstudio123
MAIL_ENCRYPTION=tls
```

**Issue**: Gmail no longer accepts regular passwords for SMTP authentication. You need to use **App Passwords** instead.

## ✅ Solution Options

### Option 1: Gmail App Password (Recommended)

#### Step 1: Enable 2-Factor Authentication
1. Go to [Google Account Settings](https://myaccount.google.com/)
2. Click **Security** in the left sidebar
3. Under "Signing in to Google", click **2-Step Verification**
4. Follow the setup process to enable 2FA

#### Step 2: Generate App Password
1. After enabling 2FA, go back to **Security**
2. Under "Signing in to Google", click **App passwords**
3. Select **Mail** as the app
4. Select **Other (Custom name)** as the device
5. Enter "Lemon Hub Studio" as the name
6. Click **Generate**
7. Copy the 16-character app password (e.g., `abcd efgh ijkl mnop`)

#### Step 3: Update .env File
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=lemonhubstudioweb@gmail.com
MAIL_PASSWORD=your_16_character_app_password_here
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=lemonhubstudioweb@gmail.com
MAIL_FROM_NAME="Lemon Hub Studio"
```

#### Step 4: Clear Config Cache
```bash
php artisan config:cache
```

#### Step 5: Test Email
```bash
php artisan test:email your-test-email@gmail.com
```

### Option 2: Use Log Driver (For Testing)

If you want to test without setting up Gmail:

```env
MAIL_MAILER=log
```

Emails will be saved to `storage/logs/laravel.log` instead of being sent.

### Option 3: Alternative Email Services

#### Mailtrap (Development)
1. Sign up at [Mailtrap.io](https://mailtrap.io/)
2. Create a new inbox
3. Use the provided SMTP credentials:

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
```

#### SendGrid (Production)
1. Sign up at [SendGrid](https://sendgrid.com/)
2. Create an API key
3. Configure:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your_sendgrid_api_key
MAIL_ENCRYPTION=tls
```

## 🧪 Testing Email Functionality

After configuring email, test with:

```bash
# Test basic email sending
php artisan test:email your-email@example.com

# Check logs for any errors
tail -f storage/logs/laravel.log
```

## 📋 Email Features Currently Implemented

### ✅ Working Features
- **Booking Confirmation Emails**: Sent when users create bookings
- **Email Template**: Professional HTML template with booking details
- **User Information**: Includes user name, email, booking reference
- **Booking Details**: Date, time, duration, status
- **Studio Branding**: Lemon Hub Studio branding and contact info

### 📧 Email Content Includes
- Booking reference number
- Formatted date and time
- Session duration
- Booking status
- Studio contact information
- Professional styling

## 🔧 Troubleshooting

### Common Issues

1. **"Username and Password not accepted"**
   - Solution: Use Gmail App Password instead of regular password

2. **"Connection timeout"**
   - Check firewall settings
   - Verify SMTP port (587 for TLS, 465 for SSL)

3. **"Authentication failed"**
   - Verify credentials are correct
   - Ensure 2FA is enabled for Gmail
   - Regenerate App Password if needed

### Debug Commands

```bash
# Clear all caches
php artisan config:cache
php artisan cache:clear

# Check current mail configuration
php artisan tinker
>>> config('mail')

# Test email sending
php artisan test:email

# View recent logs
tail -n 50 storage/logs/laravel.log
```

## 🎯 Next Steps

1. **Choose your preferred email solution** (Gmail App Password recommended)
2. **Update your `.env` file** with correct credentials
3. **Clear config cache**: `php artisan config:cache`
4. **Test email functionality**: `php artisan test:email`
5. **Create a test booking** to verify notifications work
6. **Monitor logs** for any issues

## 📞 Support

If you continue having issues:
1. Check `storage/logs/laravel.log` for detailed error messages
2. Verify your email service credentials
3. Test with a simple email service like Mailtrap first
4. Ensure your server can make outbound SMTP connections

---

## 🔒 Security Notes

- Never commit real email passwords to version control
- Use environment variables for all sensitive credentials
- Consider using App Passwords instead of main account passwords
- Regularly rotate email service credentials
- Monitor email sending logs for suspicious activity

**Status**: ❌ Email notifications are currently **NOT WORKING** due to Gmail authentication failure. Follow the steps above to fix.