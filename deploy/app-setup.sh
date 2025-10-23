#!/bin/bash

# Music Studio Application Setup Script
echo "=== Setting up Music Studio Application ==="

cd /var/www/music-studio

# Install PHP dependencies
echo "Installing PHP dependencies..."
composer install --optimize-autoloader --no-dev

# Install Node.js dependencies and build assets
echo "Installing Node.js dependencies..."
npm install

echo "Building frontend assets..."
npm run build

# Generate application key
echo "Generating application key..."
php artisan key:generate --force

# Clear and cache configuration
echo "Optimizing application..."
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Create storage link
echo "Creating storage link..."
php artisan storage:link

# Set proper permissions
echo "Setting final permissions..."
chown -R www-data:www-data /var/www/music-studio
chmod -R 755 /var/www/music-studio
chmod -R 775 /var/www/music-studio/storage
chmod -R 775 /var/www/music-studio/bootstrap/cache

# Restart services
echo "Restarting services..."
systemctl restart nginx
systemctl restart php8.2-fpm

echo ""
echo "=== Application setup completed successfully! ==="
echo ""
echo "Your Music Studio is now accessible at: http://72.60.232.155"
echo ""
echo "Default admin credentials (if seeded):"
echo "Email: admin@musicstudio.com"
echo "Password: password"
echo ""
echo "To check application status:"
echo "- Nginx: systemctl status nginx"
echo "- PHP-FPM: systemctl status php8.2-fpm"
echo "- MySQL: systemctl status mysql"
echo ""
echo "Log files:"
echo "- Nginx: /var/log/nginx/error.log"
echo "- Laravel: /var/www/music-studio/storage/logs/laravel.log"
