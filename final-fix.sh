#!/bin/bash

echo "=== Final PHP-FPM Fix for Music Studio ==="

# Install PHP-FPM and MySQL extension
echo "Installing PHP-FPM and PHP-MySQL..."
apt install -y php-fpm php-mysql

# Enable and start PHP 8.3 FPM service
echo "Enabling and starting PHP 8.3 FPM..."
systemctl enable php8.3-fpm
systemctl start php8.3-fpm

# Update Nginx configuration to use PHP 8.3 instead of 8.2
echo "Updating Nginx configuration..."
sed -i 's/php8\.2-fpm/php8.3-fpm/g' /etc/nginx/sites-available/music-studio

# Restart Nginx
echo "Restarting Nginx..."
systemctl restart nginx

# Check service status
echo "=== Service Status ==="
echo "PHP-FPM Status:"
systemctl status php8.3-fpm --no-pager -l

echo "Nginx Status:"
systemctl status nginx --no-pager -l

echo "=== Testing Configuration ==="
nginx -t

echo "=== Final Fix Completed! ==="
echo "Music Studio should now be accessible at: http://72.60.232.155"