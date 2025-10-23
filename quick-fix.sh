#!/bin/bash

echo "=== Quick Fix for Music Studio ==="

# Check PHP version
echo "Checking PHP version..."
php -v

# Install PHP-FPM
echo "Installing PHP-FPM..."
apt update
apt install -y php8.2-fpm php8.2-mysql php8.2-curl php8.2-mbstring php8.2-zip php8.2-bcmath php8.2-intl

# Enable and start PHP-FPM
echo "Starting PHP-FPM..."
systemctl enable php8.2-fpm
systemctl start php8.2-fpm
systemctl status php8.2-fpm

# Check database
echo "Checking database..."
mysql -u root -e "SHOW DATABASES;"
mysql -u root -e "SELECT User, Host FROM mysql.user WHERE User='music_studio';"

# Create database user if needed
echo "Creating database user..."
mysql -u root -e "CREATE USER IF NOT EXISTS 'music_studio'@'localhost' IDENTIFIED BY 'music_studio_password';"
mysql -u root -e "GRANT ALL PRIVILEGES ON music_studio.* TO 'music_studio'@'localhost';"
mysql -u root -e "FLUSH PRIVILEGES;"

# Run migrations
echo "Running Laravel migrations..."
cd /var/www/music-studio
php artisan migrate --force

# Check tables
echo "Checking database tables..."
mysql -u music_studio -pmusic_studio_password -e "USE music_studio; SHOW TABLES;"

# Restart services
echo "Restarting services..."
systemctl restart nginx
systemctl restart php8.2-fpm

echo "=== Fix completed! ==="
echo "Website should now be accessible at: http://72.60.232.155"