# 🌐 Multi-PC Access Fix - Complete Solution

## 🎯 THE PROBLEM
Your Google OAuth redirect URI is set to `127.0.0.1` which only works on your computer. Other PCs can't access `127.0.0.1` because it's localhost.

## ✅ THE SOLUTION
Use your computer's actual IP address: `192.168.1.11`

---

## 🔧 STEP 1: Update Application Configuration

### Option A: Create/Update .env file
Create or update your `.env` file with:

```env
APP_URL=http://192.168.1.11:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=music studio
DB_USERNAME=root
DB_PASSWORD=

GOOGLE_CLIENT_ID=your_actual_client_id
GOOGLE_CLIENT_SECRET=your_actual_client_secret
GOOGLE_REDIRECT_URI=http://192.168.1.11:8000/auth/google/callback
```

### Option B: If no .env file exists
Your system might be using default configuration. Check `config/services.php` and ensure it's set correctly.

---

## 🔧 STEP 2: Update Google OAuth Settings

1. **Go to Google Cloud Console:**
   - Visit: https://console.cloud.google.com/
   - Select your project

2. **Update Authorized Redirect URIs:**
   - Go to APIs & Services → Credentials
   - Click on your OAuth 2.0 Client ID
   - In "Authorized redirect URIs", ADD:
     ```
     http://192.168.1.11:8000/auth/google/callback
     ```
   - Keep the old one too: `http://127.0.0.1:8000/auth/google/callback`
   - Click SAVE

---

## 🚀 STEP 3: Start Server on Network IP

Instead of just `php artisan serve`, use:

```bash
php artisan serve --host=192.168.1.11 --port=8000
```

This makes your server accessible from other computers.

---

## 🧪 STEP 4: Test from Another Computer

1. **From another PC on the same network:**
   - Open browser
   - Go to: `http://192.168.1.11:8000`
   - Click "Login with Google" 
   - Complete the login process

2. **Check your database:**
   - You should see a new user appear in phpMyAdmin
   - User count should increase from 2 to 3

---

## 🔍 STEP 5: Monitor & Verify

Run the debug script while testing:
```bash
php debug_login_system.php
```

This will show real-time user detection when someone logs in.

---

## 🛡️ FIREWALL NOTE

If it still doesn't work, check Windows Firewall:
1. Open Windows Defender Firewall
2. Click "Allow an app through firewall"
3. Add PHP or allow port 8000

---

## 📊 FINAL VERIFICATION

After setup:
- ✅ Your computer: `http://127.0.0.1:8000` ✓
- ✅ Other PCs: `http://192.168.1.11:8000` ✓  
- ✅ Google OAuth works from both ✓
- ✅ New users saved to database ✓

---

## 🎯 QUICK TEST COMMAND

Run this to start properly configured server:
```bash
php artisan serve --host=192.168.1.11 --port=8000
```

Then test from another PC: `http://192.168.1.11:8000` 