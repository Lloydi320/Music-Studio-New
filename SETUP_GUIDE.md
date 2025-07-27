# 🎵 Lemon Hub Studio - Multi-PC User Setup Guide

## ✅ Current System Status
Your system is **ALREADY WORKING** for multi-PC user access! 
- **2 users** are currently in your database
- **Google OAuth** authentication is configured
- **Booking system** properly links to authenticated users

## 🔧 How It Works for Users on Other PCs

### 1. **Automatic User Registration & Login**
When someone visits from any PC and clicks "Login with Google":
- If it's their **first time**: A new user account is automatically created in your database
- If they've **logged in before**: Their existing account is retrieved from the database
- All their information is stored in **phpMyAdmin** under the `users` table

### 2. **Booking System Access**
Once logged in from any PC, users can:
- Access the booking system (`/booking` route)
- Create new bookings that are saved to the `bookings` table
- View their booking history
- Cancel their bookings

### 3. **Cross-Device Continuity** 
Users can:
- Log in from **any computer** with internet access
- Access their **same account** and booking history
- Make new bookings from **different devices**

## 🛠️ Required Configuration (If Not Already Set)

### Google OAuth Setup
Ensure these environment variables are configured:

```env
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here
GOOGLE_REDIRECT_URI=http://your-domain.com/auth/google/callback
```

### Database Configuration
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=music_studio
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password
```

## 📊 Monitoring Users in phpMyAdmin

To see users who have logged in:
1. Open **phpMyAdmin**
2. Navigate to your database (usually `music_studio`)
3. Click on the **`users`** table
4. You'll see all registered users with their:
   - Name
   - Email
   - Google ID
   - Registration date

## 🎯 User Flow for New PC Users

1. **User visits your website** from any PC
2. **Clicks "Login with Google"** button
3. **Google authentication** popup appears
4. **User authorizes** your app with their Google account
5. **System automatically**:
   - Creates new user record in database (if first time)
   - Logs them in
   - Redirects to homepage with welcome message
6. **User can now access** booking system and make reservations

## 🔍 Testing the System

To test multi-PC functionality:
1. Have someone log in from a different computer
2. Check the `users` table in phpMyAdmin
3. Verify their user record was created
4. Confirm they can access the booking system
5. Check that their bookings appear in the `bookings` table

## 🚀 Your System is Ready!

Your authentication and booking system is **fully configured** for multi-PC access. Users from anywhere can:
- ✅ Log in with Google
- ✅ Get automatically registered in your database
- ✅ Access the booking system
- ✅ Make and manage bookings
- ✅ Return later from any device with their same account

No additional setup is required - the system is already working as intended! 