#!/bin/bash

# Music Studio Server Setup Script
echo "=== Setting up Music Studio on Ubuntu Server ==="

# Update system
echo "Updating system packages..."
apt update && apt upgrade -y

# Install required packages
echo "Installing required packages..."
apt install -y nginx mysql-server php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-gd php8.2-curl php8.2-mbstring php8.2-zip php8.2-bcmath php8.2-intl unzip curl git

# Install Composer
echo "Installing Composer..."
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer

# Install Node.js and npm
echo "Installing Node.js..."
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt install -y nodejs

# Create project directory
echo "Setting up project directory..."
mkdir -p /var/www/music-studio
chown -R www-data:www-data /var/www/music-studio

# Setup MySQL
echo "Setting up MySQL database..."
mysql -u root << 'EOF'
CREATE DATABASE IF NOT EXISTS music_studio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'music_studio_user'@'localhost' IDENTIFIED BY 'SecurePassword123!';
GRANT ALL PRIVILEGES ON music_studio.* TO 'music_studio_user'@'localhost';
FLUSH PRIVILEGES;
EOF

# Configure Nginx
echo "Configuring Nginx..."
cat > /etc/nginx/sites-available/music-studio << 'NGINX_EOF'
server {
    listen 80;
    server_name 72.60.232.155;
    root /var/www/music-studio/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
NGINX_EOF

# Enable site
echo "Enabling Nginx site..."
ln -sf /etc/nginx/sites-available/music-studio /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default

# Test and reload Nginx
echo "Testing and reloading Nginx..."
nginx -t && systemctl reload nginx

# Set proper permissions
echo "Setting file permissions..."
chown -R www-data:www-data /var/www/music-studio
chmod -R 755 /var/www/music-studio
chmod -R 775 /var/www/music-studio/storage
chmod -R 775 /var/www/music-studio/bootstrap/cache

echo "Server setup completed successfully!"
echo "Next step: Run ./app-setup.sh"
