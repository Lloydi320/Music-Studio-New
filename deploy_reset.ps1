# Database Reset Deployment Script
Write-Host "=== Music Studio Database Reset Deployment ===" -ForegroundColor Green
Write-Host "This will upload and execute the database reset script on the live server." -ForegroundColor Yellow
Write-Host ""

# Server details
$server = "72.60.232.155"
$username = "root"
$appPath = "/var/www/music-studio"

Write-Host "1. Uploading reset script to server..." -ForegroundColor Cyan
scp reset_bookings.php "${username}@${server}:${appPath}/"

if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ Reset script uploaded successfully" -ForegroundColor Green
    
    Write-Host "2. Executing database reset on server..." -ForegroundColor Cyan
    ssh "${username}@${server}" "cd ${appPath}; php reset_bookings.php"
    
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✓ Database reset completed successfully" -ForegroundColor Green
        
        Write-Host "3. Cleaning up temporary files..." -ForegroundColor Cyan
        ssh "${username}@${server}" "cd ${appPath}; rm -f reset_bookings.php"
        
        Write-Host "4. Clearing Laravel cache..." -ForegroundColor Cyan
        ssh "${username}@${server}" "cd ${appPath}; php artisan cache:clear; php artisan config:clear; php artisan view:clear"
        
        Write-Host "" -ForegroundColor Green
        Write-Host "=== RESET COMPLETED SUCCESSFULLY ===" -ForegroundColor Green
        Write-Host "- All booking data has been cleared" -ForegroundColor White
        Write-Host "- User accounts have been preserved" -ForegroundColor White
        Write-Host "- Analytics cache has been cleared" -ForegroundColor White
        Write-Host "- Your application is ready for fresh bookings!" -ForegroundColor White
        
    } else {
        Write-Host "❌ Database reset failed" -ForegroundColor Red
    }
} else {
    Write-Host "❌ Failed to upload reset script" -ForegroundColor Red
}

Write-Host ""
Write-Host "Press any key to continue..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")