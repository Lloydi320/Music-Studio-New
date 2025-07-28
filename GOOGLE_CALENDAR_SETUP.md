# Google Calendar Integration Setup Guide

This guide will help you set up Google Calendar integration for your Music Studio booking system.

## Overview

The Google Calendar integration allows **admin users** to:
- Automatically sync all customer bookings to their Google Calendar
- Receive notifications for upcoming studio sessions
- Manage their schedule in one place
- View client information directly in calendar events

## Prerequisites

1. ✅ Laravel application with Google OAuth already configured
2. ✅ Database migrations completed
3. ✅ Google API Client installed
4. 🔧 Google Calendar API enabled (see setup below)

## Step 1: Google Calendar API Setup

### 1.1 Enable Google Calendar API

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Select your existing project (the one used for Google OAuth)
3. Go to **APIs & Services > Library**
4. Search for "Google Calendar API"
5. Click **Enable**

### 1.2 Update OAuth Scopes

Your existing Google OAuth setup needs to include Calendar access:

1. In Google Cloud Console, go to **APIs & Services > Credentials**
2. Click on your OAuth 2.0 Client ID
3. Under **Authorized redirect URIs**, add:
   ```
   http://localhost:8000/admin/google-calendar/callback
   https://yourdomain.com/admin/google-calendar/callback
   ```

### 1.3 Environment Variables

Add these to your `.env` file:

```env
# Google Calendar specific redirect (optional - defaults to /admin/google-calendar/callback)
GOOGLE_CALENDAR_REDIRECT_URI=http://localhost:8000/admin/google-calendar/callback
```

## Step 2: Make Users Admin

### Option A: Using the Admin Panel (Recommended)

1. First, manually make yourself admin using Option B
2. Login and go to `/admin/dashboard`
3. Use the "Grant Admin Access" form to make other users admin

### Option B: Using Database/Code

Run this command to make a user admin:

```bash
php artisan tinker
```

Then in the tinker console:
```php
// Replace with the email of the user you want to make admin
$user = \App\Models\User::where('email', 'your-email@gmail.com')->first();
$user->update(['is_admin' => true]);
$user->save();
echo "User {$user->name} is now an admin!";
exit;
```

### Option C: Using SQL

```sql
UPDATE users SET is_admin = 1 WHERE email = 'your-email@gmail.com';
```

## Step 3: Connect Google Calendar

1. **Login as Admin**: Use Google OAuth to login with an admin account
2. **Access Admin Panel**: Go to `/admin/dashboard`
3. **Connect Calendar**: Click "Google Calendar Setup" → "Connect Google Calendar"
4. **Authorize**: Grant permissions for Calendar access
5. **Verify Connection**: You should see "✓ Connected" status

## Step 4: Test the Integration

### 4.1 Test Booking Creation

1. **Create a Test Booking**: 
   - Login as a regular user
   - Go to `/booking`
   - Create a new booking

2. **Verify Calendar Sync**:
   - Check your Google Calendar
   - Look for "Music Studio Bookings" calendar
   - Verify the event was created with:
     - Client name and details
     - Correct date and time
     - Booking reference
     - Email notifications set up

### 4.2 Sync Existing Bookings

1. Go to `/admin/calendar`
2. Click "Sync X Bookings" to sync existing bookings
3. Check Google Calendar for all events

## Step 5: Features Overview

### Admin Dashboard (`/admin/dashboard`)
- View booking statistics
- Quick access to calendar integration
- User management (grant/remove admin access)
- Recent bookings with sync status

### Calendar Management (`/admin/calendar`)
- Connect/disconnect Google Calendar
- Sync existing bookings
- View recent calendar events
- Setup instructions

## Troubleshooting

### Common Issues

**1. "Google Calendar connection failed"**
- Verify Google Calendar API is enabled
- Check redirect URIs are correct
- Ensure your Google account has calendar access

**2. "No admin users with Google Calendar access found"**
- Make sure the user is marked as admin (`is_admin = true`)
- Verify the admin has connected their Google Calendar

**3. Events not appearing in calendar**
- Check the "Music Studio Bookings" calendar is visible
- Verify the booking status is 'confirmed'
- Check Laravel logs for error messages

**4. Token expired errors**
- The system should automatically refresh tokens
- If persistent, disconnect and reconnect Google Calendar

### Debug Commands

```bash
# Check if user is admin
php artisan tinker
>>> \App\Models\User::where('email', 'user@example.com')->first()->isAdmin()

# Check Google Calendar connection
>>> \App\Models\User::where('email', 'user@example.com')->first()->hasGoogleCalendarAccess()

# View recent bookings
>>> \App\Models\Booking::with('user')->latest()->take(5)->get()
```

## Security Notes

- Only admin users can access calendar integration
- Google tokens are stored encrypted in the database
- Calendar events include minimal client information
- Regular users cannot access admin features

## Event Details

Each calendar event includes:
- **Title**: "Studio Session - [Client Name]"
- **Description**: Booking reference, client email, duration, status
- **Attendees**: Client email for notifications
- **Reminders**: 24 hours (email) and 1 hour (popup) before session

## API Rate Limits

- Google Calendar API has generous rate limits
- The system handles token refresh automatically
- Events are created/updated individually per booking

## Support

If you encounter issues:

1. Check Laravel logs: `storage/logs/laravel.log`
2. Verify Google Cloud Console settings
3. Test with a fresh Google account
4. Ensure all migrations have run: `php artisan migrate`

---

## Quick Test Checklist

- [ ] Google Calendar API enabled
- [ ] User marked as admin
- [ ] Admin can access `/admin/dashboard`
- [ ] Google Calendar connected successfully
- [ ] Test booking creates calendar event
- [ ] Existing bookings can be synced
- [ ] Calendar events contain correct information
- [ ] Notifications are working

🎉 **Congratulations!** Your Google Calendar integration is now set up and ready to use! 