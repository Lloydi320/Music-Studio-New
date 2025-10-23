# Music Studio Deployment Guide
## Deploy to SSH Server: 72.60.232.155

This guide will help you deploy your updated Music Studio system to your server with a fully working database.

## 🚀 Quick Start

### Option 1: Automated Deployment (Recommended)
```powershell
# Run the quick deployment script
.\quick-deploy.ps1
```

### Option 2: Manual Deployment
```powershell
# Create deployment package
.\deploy.ps1

# Follow the manual steps below
```

## 📋 Prerequisites

1. **Server Access**: SSH access to 72.60.232.155
2. **Server OS**: Ubuntu 20.04+ or similar Linux distribution
3. **Root Access**: Required for installing packages and configuring services
4. **MySQL Password**: You'll need to set a secure password for the database

## 🔧 What Gets Installed

The deployment script will install and configure:

- **Web Server**: Nginx
- **Database**: MySQL 8.0
- **PHP**: PHP 8.2 with required extensions
- **Process Manager**: PHP-FPM
- **Package Manager**: Composer
- **Frontend Build**: Node.js and npm

## 📁 Project Structure on Server

```
/var/www/music-studio/
├── app/                    # Laravel application files
├── config/                 # Configuration files
├── database/              # Database migrations and seeders
├── public/                # Web-accessible files (document root)
├── resources/             # Views, CSS, JS source files
├── routes/                # Route definitions
├── storage/               # Logs, cache, uploads
├── vendor/                # PHP dependencies
├── .env                   # Environment configuration
├── composer.json          # PHP dependencies
├── package.json           # Node.js dependencies
└── artisan               # Laravel command-line tool
```

## 🗄️ Database Configuration

The deployment creates:
- **Database**: `music_studio`
- **User**: `music_studio_user`
- **Encoding**: UTF8MB4 (supports emojis and special characters)

### Tables Created:
- `users` - User accounts and authentication
- `bookings` - Studio booking records
- `instrument_rentals` - Equipment rental tracking
- `feedback` - Customer feedback
- `activity_logs` - System activity tracking
- `carousel_items` - Homepage carousel content
- `pending_users` - User registration queue
- And many more...

## 🌐 Web Server Configuration

### Nginx Configuration
- **Document Root**: `/var/www/music-studio/public`
- **PHP Processing**: PHP-FPM 8.2
- **URL Rewriting**: Enabled for Laravel routing
- **Security Headers**: Added for protection

### SSL/HTTPS (Optional)
After deployment, you can add SSL:
```bash
# Install Certbot
apt install certbot python3-certbot-nginx

# Get SSL certificate
certbot --nginx -d yourdomain.com
```

## 🔐 Security Features

- **Environment Variables**: Sensitive data stored in .env
- **Database User**: Limited privileges (not root)
- **File Permissions**: Proper ownership and permissions
- **Security Headers**: X-Frame-Options, X-Content-Type-Options
- **Error Handling**: Production error pages

## 📧 Email Configuration

Update these in your `.env` file:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@musicstudio.com"
MAIL_FROM_NAME="Music Studio"
```

## 🔄 Deployment Steps

### Step 1: Prepare Deployment Package
```powershell
# Run deployment script
.\deploy.ps1

# This creates a 'deploy' folder with all necessary files
```

### Step 2: Upload to Server
```bash
# Using SCP
scp -r deploy/* root@72.60.232.155:/var/www/music-studio/

# Or use any SFTP client (FileZilla, WinSCP, etc.)
```

### Step 3: Server Setup (First Time Only)
```bash
# SSH into server
ssh root@72.60.232.155

# Make scripts executable
cd /var/www/music-studio
chmod +x *.sh

# Run server setup
./server-setup.sh
```

### Step 4: Application Setup
```bash
# Run application setup
./app-setup.sh
```

### Step 5: Verify Deployment
Visit: `http://72.60.232.155`

## 🛠️ Troubleshooting

### Common Issues and Solutions

#### 1. Permission Errors
```bash
chown -R www-data:www-data /var/www/music-studio
chmod -R 755 /var/www/music-studio
chmod -R 775 /var/www/music-studio/storage
chmod -R 775 /var/www/music-studio/bootstrap/cache
```

#### 2. Database Connection Issues
```bash
# Check MySQL status
systemctl status mysql

# Test database connection
mysql -u music_studio_user -p music_studio

# Reset database password if needed
mysql -u root -p
ALTER USER 'music_studio_user'@'localhost' IDENTIFIED BY 'new_password';
FLUSH PRIVILEGES;
```

#### 3. Nginx Configuration Issues
```bash
# Test configuration
nginx -t

# Check error logs
tail -f /var/log/nginx/error.log

# Restart Nginx
systemctl restart nginx
```

#### 4. PHP-FPM Issues
```bash
# Check PHP-FPM status
systemctl status php8.2-fpm

# Check PHP error logs
tail -f /var/log/php8.2-fpm.log

# Restart PHP-FPM
systemctl restart php8.2-fpm
```

#### 5. Laravel Application Errors
```bash
# Check Laravel logs
tail -f /var/www/music-studio/storage/logs/laravel.log

# Clear cache
cd /var/www/music-studio
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 🔄 Updates and Maintenance

### Updating the Application
1. Upload new files to `/var/www/music-studio`
2. Run migrations: `php artisan migrate`
3. Clear cache: `php artisan cache:clear`
4. Restart services: `systemctl restart nginx php8.2-fpm`

### Backup Strategy
```bash
# Database backup
mysqldump -u music_studio_user -p music_studio > backup_$(date +%Y%m%d).sql

# File backup
tar -czf music_studio_backup_$(date +%Y%m%d).tar.gz /var/www/music-studio
```

### Monitoring
```bash
# Check all services
systemctl status nginx mysql php8.2-fpm

# Monitor logs
tail -f /var/log/nginx/access.log
tail -f /var/www/music-studio/storage/logs/laravel.log
```

## 📞 Support

If you encounter any issues:

1. Check the troubleshooting section above
2. Review the log files for error messages
3. Ensure all services are running
4. Verify file permissions are correct

## 🎉 Success!

Once deployed successfully, your Music Studio application will be fully functional with:

- ✅ User registration and authentication
- ✅ Studio booking system
- ✅ Instrument rental management
- ✅ Admin dashboard
- ✅ Email notifications
- ✅ Database with all tables and relationships
- ✅ Responsive web interface
- ✅ Security features enabled

Access your application at: **http://72.60.232.155**