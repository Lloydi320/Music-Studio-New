# Music Studio Deployment Script
# Deploy to SSH Server: 72.60.232.155

param(
    [string]$ServerIP = "72.60.232.155",
    [string]$Username = "root",
    [string]$ProjectName = "music-studio",
    [string]$Domain = "",
    [string]$DBPassword = ""
)

Write-Host "=== Music Studio Deployment Script ===" -ForegroundColor Green
Write-Host "Target Server: $ServerIP" -ForegroundColor Yellow

# Check if required parameters are provided
if ([string]::IsNullOrEmpty($DBPassword)) {
    $DBPassword = Read-Host "Enter MySQL root password for the server" -AsSecureString
    $DBPassword = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($DBPassword))
}

if ([string]::IsNullOrEmpty($Domain)) {
    $Domain = Read-Host "Enter your domain name (e.g., yourdomain.com) or press Enter for IP-based access"
    if ([string]::IsNullOrEmpty($Domain)) {
        $Domain = $ServerIP
    }
}

# Create deployment directory
$DeployDir = "deploy"
if (Test-Path $DeployDir) {
    Remove-Item $DeployDir -Recurse -Force
}
New-Item -ItemType Directory -Path $DeployDir | Out-Null

Write-Host "Creating deployment package..." -ForegroundColor Yellow

# Copy project files (excluding unnecessary files)
$ExcludePatterns = @(
    "node_modules",
    ".git",
    "storage/logs/*",
    "storage/framework/cache/*",
    "storage/framework/sessions/*",
    "storage/framework/views/*",
    "vendor",
    ".env",
    "*.log",
    "deploy",
    "deploy.ps1"
)

# Copy all files except excluded ones
robocopy . "$DeployDir" /E /XD $ExcludePatterns /XF $ExcludePatterns /NFL /NDL /NJH /NJS

Write-Host "Creating production environment file..." -ForegroundColor Yellow

# Create production .env file
$EnvContent = @"
APP_NAME="Music Studio"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://$Domain

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
PHP_CLI_SERVER_WORKERS=4
BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=music_studio
DB_USERNAME=music_studio_user
DB_PASSWORD=$DBPassword

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="Music Studio"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="Music Studio"
"@

$EnvContent | Out-File -FilePath "$DeployDir\.env" -Encoding UTF8

Write-Host "Creating server setup script..." -ForegroundColor Yellow

# Create server setup script
$ServerSetupScript = @"
#!/bin/bash

# Music Studio Server Setup Script
echo "=== Setting up Music Studio on Ubuntu Server ==="

# Update system
apt update && apt upgrade -y

# Install required packages
apt install -y nginx mysql-server php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-gd php8.2-curl php8.2-mbstring php8.2-zip php8.2-bcmath php8.2-intl unzip curl git

# Install Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer

# Install Node.js and npm
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt install -y nodejs

# Create project directory
mkdir -p /var/www/music-studio
chown -R www-data:www-data /var/www/music-studio

# Setup MySQL
mysql -u root -p$DBPassword << EOF
CREATE DATABASE IF NOT EXISTS music_studio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'music_studio_user'@'localhost' IDENTIFIED BY '$DBPassword';
GRANT ALL PRIVILEGES ON music_studio.* TO 'music_studio_user'@'localhost';
FLUSH PRIVILEGES;
EOF

# Configure Nginx
cat > /etc/nginx/sites-available/music-studio << 'NGINX_EOF'
server {
    listen 80;
    server_name $Domain;
    root /var/www/music-studio/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
NGINX_EOF

# Enable site
ln -sf /etc/nginx/sites-available/music-studio /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default

# Test and reload Nginx
nginx -t && systemctl reload nginx

# Set proper permissions
chown -R www-data:www-data /var/www/music-studio
chmod -R 755 /var/www/music-studio
chmod -R 775 /var/www/music-studio/storage
chmod -R 775 /var/www/music-studio/bootstrap/cache

echo "Server setup completed!"
"@

$ServerSetupScript | Out-File -FilePath "$DeployDir\server-setup.sh" -Encoding UTF8

# Create application setup script
$AppSetupScript = @"
#!/bin/bash

# Music Studio Application Setup Script
echo "=== Setting up Music Studio Application ==="

cd /var/www/music-studio

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node.js dependencies and build assets
npm install
npm run build

# Generate application key
php artisan key:generate --force

# Clear and cache configuration
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
php artisan migrate --force

# Create storage link
php artisan storage:link

# Set proper permissions
chown -R www-data:www-data /var/www/music-studio
chmod -R 755 /var/www/music-studio
chmod -R 775 /var/www/music-studio/storage
chmod -R 775 /var/www/music-studio/bootstrap/cache

# Restart services
systemctl restart nginx
systemctl restart php8.2-fpm

echo "Application setup completed!"
echo "Your Music Studio is now accessible at: http://$Domain"
"@

$AppSetupScript | Out-File -FilePath "$DeployDir\app-setup.sh" -Encoding UTF8

Write-Host "Creating deployment instructions..." -ForegroundColor Yellow

# Create deployment instructions
$Instructions = @"
# Music Studio Deployment Instructions

## Prerequisites
1. Ensure you have SSH access to the server: $ServerIP
2. Make sure you have the MySQL root password ready

## Deployment Steps

### Step 1: Upload files to server
Upload the entire 'deploy' folder contents to your server at /var/www/music-studio

You can use SCP, SFTP, or any file transfer method:
```
scp -r deploy/* root@${ServerIP}:/var/www/music-studio/
```

### Step 2: Run server setup (first time only)
SSH into your server and run:
```
ssh root@${ServerIP}
chmod +x /var/www/music-studio/server-setup.sh
/var/www/music-studio/server-setup.sh
```

### Step 3: Run application setup
```
chmod +x /var/www/music-studio/app-setup.sh
/var/www/music-studio/app-setup.sh
```

### Step 4: Access your application
Open your browser and navigate to: http://$Domain

## Troubleshooting

### If you encounter permission issues:
```
chown -R www-data:www-data /var/www/music-studio
chmod -R 755 /var/www/music-studio
chmod -R 775 /var/www/music-studio/storage
chmod -R 775 /var/www/music-studio/bootstrap/cache
```

### If database connection fails:
1. Check MySQL service: `systemctl status mysql`
2. Verify database credentials in .env file
3. Test database connection: `mysql -u music_studio_user -p music_studio`

### If Nginx fails to start:
1. Check configuration: `nginx -t`
2. Check error logs: `tail -f /var/log/nginx/error.log`
3. Restart Nginx: `systemctl restart nginx`

## Post-Deployment
1. Set up SSL certificate (recommended)
2. Configure backup system
3. Set up monitoring
4. Configure email settings in .env file
"@

$Instructions | Out-File -FilePath "$DeployDir\DEPLOYMENT_INSTRUCTIONS.md" -Encoding UTF8

Write-Host "Deployment package created successfully!" -ForegroundColor Green
Write-Host "Location: $DeployDir" -ForegroundColor Yellow
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "1. Review the files in the '$DeployDir' directory" -ForegroundColor White
Write-Host "2. Upload the contents to your server at /var/www/music-studio" -ForegroundColor White
Write-Host "3. Follow the instructions in DEPLOYMENT_INSTRUCTIONS.md" -ForegroundColor White
Write-Host ""
Write-Host "Your application will be accessible at: http://$Domain" -ForegroundColor Green