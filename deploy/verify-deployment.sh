#!/bin/bash
# 🔍 Music Studio Verification Script

echo "🔍 Verifying Music Studio Deployment..."
echo "=================================="

# Check services
echo "📊 Service Status:"
if systemctl is-active nginx >/dev/null 2>&1; then
    echo "✅ Nginx: Running"
else
    echo "❌ Nginx: Not running"
fi

if systemctl is-active mysql >/dev/null 2>&1; then
    echo "✅ MySQL: Running"
else
    echo "❌ MySQL: Not running"
fi

if systemctl is-active php8.2-fpm >/dev/null 2>&1; then
    echo "✅ PHP-FPM: Running"
else
    echo "❌ PHP-FPM: Not running"
fi

echo ""
echo "🗄️ Database Status:"
if mysql -u music_studio_user -pSecurePassword123! -e "USE music_studio; SHOW TABLES;" >/dev/null 2>&1; then
    echo "✅ Database: Connected"
    table_count=$(mysql -u music_studio_user -pSecurePassword123! -e "USE music_studio; SHOW TABLES;" 2>/dev/null | wc -l)
    echo "📋 Tables created: $((table_count - 1))"
else
    echo "❌ Database: Connection failed"
fi

echo ""
echo "🌐 Web Server Status:"
if curl -s -o /dev/null -w "%{http_code}" http://localhost | grep -q "200"; then
    echo "✅ Website: Accessible"
else
    echo "❌ Website: Not accessible"
fi

echo ""
echo "📁 File Permissions:"
if ls -la /var/www/music-studio/storage/ | grep -q "www-data"; then
    echo "✅ Storage: Correct permissions"
else
    echo "❌ Storage: Permission issues"
fi

echo ""
echo "📋 Laravel Status:"
cd /var/www/music-studio
if php artisan --version >/dev/null 2>&1; then
    echo "✅ Laravel: Working"
    php artisan --version
else
    echo "❌ Laravel: Not working"
fi

echo ""
echo "🎉 Verification Complete!"
echo "Visit: http://72.60.232.155 to access your Music Studio"

# Show useful commands
echo ""
echo "🔧 Useful Commands:"
echo "View logs: tail -f /var/www/music-studio/storage/logs/laravel.log"
echo "Restart services: systemctl restart nginx mysql php8.2-fpm"
echo "Check database: mysql -u music_studio_user -pSecurePassword123! music_studio"