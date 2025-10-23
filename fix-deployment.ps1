# Music Studio Deployment Fix Script
# This script fixes the database deployment issues

Write-Host "=== Music Studio Deployment Fix ===" -ForegroundColor Green
Write-Host ""

# Check if we're in the right directory
if (!(Test-Path "deploy")) {
    Write-Host "Error: Please run this script from the project root directory" -ForegroundColor Red
    exit 1
}

Write-Host "Step 1: Generating Application Key..." -ForegroundColor Yellow
# Generate a new APP_KEY for the deployment
$appKey = "base64:" + [Convert]::ToBase64String([System.Text.Encoding]::UTF8.GetBytes((New-Guid).ToString().Replace("-", "")))

# Update the .env file with the generated key
$envPath = "deploy\.env"
$envContent = Get-Content $envPath
$envContent = $envContent -replace "APP_KEY=", "APP_KEY=$appKey"
$envContent | Set-Content $envPath
Write-Host "✓ Application key generated and set" -ForegroundColor Green

Write-Host ""
Write-Host "Step 2: Database Configuration Instructions" -ForegroundColor Yellow
Write-Host "Your .env file is configured for MySQL with these settings:" -ForegroundColor Cyan
Write-Host "  Database: music_studio" -ForegroundColor White
Write-Host "  Username: music_studio_user" -ForegroundColor White
Write-Host "  Password: your_secure_password_here" -ForegroundColor White
Write-Host ""
Write-Host "IMPORTANT: You need to:" -ForegroundColor Red
Write-Host "1. Create the MySQL database 'music_studio'" -ForegroundColor White
Write-Host "2. Create the MySQL user 'music_studio_user'" -ForegroundColor White
Write-Host "3. Update the password in deploy\.env file" -ForegroundColor White
Write-Host "4. Run migrations on your server" -ForegroundColor White

Write-Host ""
Write-Host "Step 3: Server Commands to Run" -ForegroundColor Yellow
Write-Host "Once you have access to your server, run these commands:" -ForegroundColor Cyan
Write-Host ""
Write-Host "# Navigate to your web directory" -ForegroundColor Gray
Write-Host "cd /var/www/html" -ForegroundColor White
Write-Host ""
Write-Host "# Install/update dependencies" -ForegroundColor Gray
Write-Host "composer install --no-dev --optimize-autoloader" -ForegroundColor White
Write-Host ""
Write-Host "# Run database migrations" -ForegroundColor Gray
Write-Host "php artisan migrate --force" -ForegroundColor White
Write-Host ""
Write-Host "# Clear and cache configuration" -ForegroundColor Gray
Write-Host "php artisan config:clear" -ForegroundColor White
Write-Host "php artisan config:cache" -ForegroundColor White
Write-Host "php artisan route:cache" -ForegroundColor White
Write-Host "php artisan view:cache" -ForegroundColor White
Write-Host ""
Write-Host "# Set proper permissions" -ForegroundColor Gray
Write-Host "chown -R www-data:www-data /var/www/html" -ForegroundColor White
Write-Host "chmod -R 755 /var/www/html" -ForegroundColor White
Write-Host "chmod -R 775 /var/www/html/storage" -ForegroundColor White
Write-Host "chmod -R 775 /var/www/html/bootstrap/cache" -ForegroundColor White

Write-Host ""
Write-Host "Step 4: MySQL Database Setup Commands" -ForegroundColor Yellow
Write-Host "Run these commands in MySQL to create the database and user:" -ForegroundColor Cyan
Write-Host ""
Write-Host "mysql -u root -p" -ForegroundColor White
Write-Host "CREATE DATABASE music_studio;" -ForegroundColor White
$createUserCmd = "CREATE USER 'music_studio_user'@'localhost' IDENTIFIED BY 'your_secure_password';"
Write-Host $createUserCmd -ForegroundColor White
$grantCmd = "GRANT ALL PRIVILEGES ON music_studio.* TO 'music_studio_user'@'localhost';"
Write-Host $grantCmd -ForegroundColor White
Write-Host "FLUSH PRIVILEGES;" -ForegroundColor White
Write-Host "EXIT;" -ForegroundColor White

Write-Host ""
Write-Host "=== Fix Summary ===" -ForegroundColor Green
Write-Host "✓ Application key generated" -ForegroundColor Green
Write-Host "⚠ Database setup required (see instructions above)" -ForegroundColor Yellow
Write-Host "⚠ Server migrations required (see commands above)" -ForegroundColor Yellow
Write-Host ""
Write-Host "After completing the database setup and running migrations," -ForegroundColor Cyan
Write-Host "your application should work without the table error." -ForegroundColor Cyan