# 🔐 Gmail App Password Setup for lemonhubstudioweb@gmail.com

## ⚠️ IMPORTANT: Gmail Security Update Required

Your current Gmail password `lemonhubstudio123` will NOT work for SMTP authentication. Gmail requires **App Passwords** for third-party applications.

## 📋 Step-by-Step Setup

### Step 1: Enable 2-Factor Authentication
1. Go to [Google Account Settings](https://myaccount.google.com/)
2. Sign in with `lemonhubstudioweb@gmail.com`
3. Click **Security** in the left sidebar
4. Under "Signing in to Google", click **2-Step Verification**
5. Follow the setup process (you'll need your phone)

### Step 2: Generate App Password
1. After enabling 2FA, go back to **Security**
2. Under "Signing in to Google", click **App passwords**
3. Select **Mail** as the app type
4. Select **Other (Custom name)** as the device
5. Enter "Lemon Hub Studio Booking System" as the name
6. Click **Generate**
7. **COPY THE 16-CHARACTER PASSWORD** (e.g., `abcd efgh ijkl mnop`)

### Step 3: Update .env File
Replace the current password in your `.env` file:

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

### Step 4: Apply Changes
```bash
php artisan config:cache
php artisan test:email your-test-email@gmail.com
```

## 🚨 Current Issue
The system is trying to use the regular Gmail password `lemonhubstudio123`, but Gmail blocks this for security reasons.

**Error**: `535-5.7.8 Username and Password not accepted`

## ✅ After Setup
Once you complete the App Password setup:
- ✅ Emails will be sent to actual Gmail addresses
- ✅ Booking confirmations will reach customers
- ✅ No more authentication errors
- ✅ Professional email delivery

## 🔒 Security Notes
- Keep your App Password secure
- Don't share it in code repositories
- You can revoke and regenerate it anytime
- It's specific to this application only

## 📞 Need Help?
If you encounter issues:
1. Ensure 2FA is properly enabled
2. Make sure you're using the App Password, not regular password
3. Check that the Gmail account has sufficient permissions
4. Test with `php artisan test:email` command

---

**Next Step**: Complete the Gmail App Password setup above to enable actual email delivery! 📧