# 🎵 Music Studio Database Reset Instructions

## Overview
This guide provides multiple methods to reset your booking and rental data while preserving all user accounts.

## ⚠️ Important Notes
- **User accounts will be preserved** - No admin or customer accounts will be deleted
- **Only booking and rental data will be cleared**
- **Analytics will be reset** and will regenerate based on new data
- **This action cannot be undone** - make sure you want to proceed

## Method 1: Laravel Artisan Command (Recommended)

### Step 1: Connect to your server
```bash
ssh root@72.60.232.155
```

### Step 2: Navigate to your application directory
```bash
cd /var/www/music-studio
```

### Step 3: Run the reset command
```bash
php artisan bookings:reset
```

### Step 4: Confirm when prompted
The command will show you current data counts and ask for confirmation.

### Step 5: Force reset (optional)
If you want to skip confirmation:
```bash
php artisan bookings:reset --force
```

## Method 2: Web Interface

### Step 1: Upload the web reset tool
```bash
scp web_reset.php root@72.60.232.155:/var/www/music-studio/
```

### Step 2: Access via browser
Visit: `http://72.60.232.155/web_reset.php`

### Step 3: Follow the web interface
- Review current data counts
- Click "CONFIRM RESET DATABASE"
- Wait for completion

### Step 4: Clean up (IMPORTANT)
Delete the reset file for security:
```bash
ssh root@72.60.232.155 "rm /var/www/music-studio/web_reset.php"
```

## What Gets Reset

### ✅ Data That Will Be Cleared:
- All booking records
- All feedback records  
- All rental/instrument records
- Analytics cache
- Laravel application cache

### ✅ Data That Will Be Preserved:
- User accounts (admin and customers)
- User passwords and settings
- Application configuration
- Database structure

## After Reset

1. **Analytics Dashboard**: Will show zero data initially
2. **New Bookings**: Can be created immediately
3. **User Accounts**: All existing users can still log in
4. **Application**: Will function normally with clean data

## Verification

After reset, you can verify success by:
1. Checking the analytics dashboard shows zero bookings
2. Confirming user accounts still exist and can log in
3. Creating a test booking to ensure functionality

## Troubleshooting

### If the Artisan command fails:
1. Check Laravel is properly installed: `php artisan --version`
2. Verify database connection: `php artisan migrate:status`
3. Check file permissions: `ls -la app/Console/Commands/`

### If web interface doesn't work:
1. Ensure the file was uploaded correctly
2. Check web server is running
3. Verify PHP has database access

## Support

If you encounter any issues:
1. Check the Laravel logs: `tail -f storage/logs/laravel.log`
2. Verify database connectivity
3. Ensure proper file permissions

---
**Created**: $(Get-Date)
**Purpose**: Safe database reset for Music Studio booking system