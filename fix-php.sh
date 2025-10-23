#!/bin/bash

echo "=== Fixing PHP-FPM Installation ==="

# Add PHP repository
echo "Adding PHP repository..."
apt update
apt install -y software-properties-common
add-apt-repository ppa:ondrej/php -y
apt update

# Install PHP-FPM
echo "Installing PHP-FPM..."
apt install -y php8.2-fpm php8.2-mysql php8.2-curl php8.2-mbstring php8.2-zip php8.2-bcmath php8.2-intl php8.2-xml php8.2-gd

# Configure PHP-FPM
echo "Configuring PHP-FPM..."
systemctl enable php8.2-fpm
systemctl start php8.2-fpm

# Check status
echo "Checking PHP-FPM status..."
systemctl status php8.2-fpm

# Restart Nginx
echo "Restarting Nginx..."
systemctl restart nginx

# Test configuration
echo "Testing Nginx configuration..."
nginx -t

echo "=== PHP-FPM Fix completed! ==="
echo "Services status:"
systemctl is-active nginx
systemctl is-active php8.2-fpm