#!/bin/bash

echo "=== Simple PHP Fix ==="

# Check what PHP version is available
echo "Available PHP versions:"
ls /usr/bin/php*

# Install basic PHP-FPM
echo "Installing PHP-FPM..."
apt update
apt install -y php-fpm php-mysql

# Find the PHP-FPM service
echo "Finding PHP-FPM service..."
systemctl list-units --type=service | grep php

# Enable and start the service
PHP_VERSION=$(php -v | head -n1 | cut -d' ' -f2 | cut -d'.' -f1,2)
echo "PHP Version detected: $PHP_VERSION"

if systemctl list-units --type=service | grep -q "php${PHP_VERSION}-fpm"; then
    echo "Starting php${PHP_VERSION}-fpm..."
    systemctl enable php${PHP_VERSION}-fpm
    systemctl start php${PHP_VERSION}-fpm
    systemctl status php${PHP_VERSION}-fpm
elif systemctl list-units --type=service | grep -q "php-fpm"; then
    echo "Starting php-fpm..."
    systemctl enable php-fpm
    systemctl start php-fpm
    systemctl status php-fpm
fi

# Update Nginx configuration
echo "Updating Nginx configuration..."
sed -i 's/php8\.2-fpm/php-fpm/g' /etc/nginx/sites-available/music-studio

# Test and restart Nginx
echo "Testing and restarting Nginx..."
nginx -t
systemctl restart nginx

echo "=== Fix completed! ==="
echo "Services status:"
systemctl is-active nginx
systemctl is-active php-fpm || systemctl is-active php${PHP_VERSION}-fpm