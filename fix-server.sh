#!/bin/bash

echo "=== Fixing Music Studio Server Issues ==="

# Fix MySQL sql_mode issue
echo "Fixing MySQL sql_mode..."
mysql -u root -e "SET GLOBAL sql_mode = 'STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO';"

# Update MySQL configuration permanently
echo "Updating MySQL configuration..."
cat >> /etc/mysql/mysql.conf.d/mysqld.cnf << 'EOF'

[mysqld]
sql_mode = "STRICT_TRANS_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO"
EOF

# Restart MySQL
echo "Restarting MySQL..."
systemctl restart mysql

# Check if PHP-FPM is installed
echo "Checking PHP-FPM service..."
if ! systemctl is-active --quiet php8.2-fpm; then
    echo "Installing/starting PHP-FPM..."
    apt update
    apt install -y php8.2-fpm
    systemctl enable php8.2-fpm
    systemctl start php8.2-fpm
fi

# Restart services
echo "Restarting web services..."
systemctl restart nginx
systemctl restart php8.2-fpm

# Run database migrations again
echo "Running database migrations..."
cd /var/www/music-studio
php artisan migrate --force

# Clear and cache configuration
echo "Clearing application cache..."
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan route:cache
php artisan view:clear
php artisan view:cache

# Set proper permissions
echo "Setting file permissions..."
chown -R www-data:www-data /var/www/music-studio
chmod -R 755 /var/www/music-studio
chmod -R 775 /var/www/music-studio/storage
chmod -R 775 /var/www/music-studio/bootstrap/cache

echo "=== Fix completed! ==="
echo "Testing application..."
curl -I http://72.60.232.155

echo ""
echo "Application should now be accessible at: http://72.60.232.155"