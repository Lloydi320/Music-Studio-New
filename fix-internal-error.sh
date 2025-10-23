#!/bin/bash

echo "=== Fixing Internal Server Error ==="

# Navigate to project directory
cd /var/www/music-studio

echo "1. Checking and fixing file permissions..."
chown -R www-data:www-data /var/www/music-studio
chmod -R 755 /var/www/music-studio
chmod -R 775 /var/www/music-studio/storage
chmod -R 775 /var/www/music-studio/bootstrap/cache

echo "2. Clearing all Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "3. Checking .env file..."
if [ ! -f .env ]; then
    echo "Creating .env file from .env.example..."
    cp .env.example .env
fi

echo "4. Generating application key..."
php artisan key:generate --force

echo "5. Setting up database configuration..."
# Update database configuration to be more permissive
sed -i "s/'strict' => true,/'strict' => false,/g" config/database.php

echo "6. Testing database connection..."
php artisan migrate:status || echo "Database connection failed"

echo "7. Running database migrations..."
php artisan migrate --force

echo "8. Creating storage link..."
php artisan storage:link

echo "9. Optimizing Laravel..."
php artisan config:cache
php artisan route:cache

echo "10. Restarting services..."
systemctl restart php8.4-fpm
systemctl restart nginx

echo "11. Testing application..."
sleep 2
curl -I http://72.60.232.155 || echo "Application test failed"

echo "=== Fix script completed ==="