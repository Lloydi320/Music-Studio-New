# Complete Music Studio Deployment Script
# This script uploads files and runs deployment commands

param(
    [string]$ServerIP = "72.60.232.155",
    [string]$Username = "root"
)

Write-Host "Complete Music Studio Deployment Starting..." -ForegroundColor Green
Write-Host "Target Server: $ServerIP" -ForegroundColor Yellow

# Step 1: Upload deployment files using SCP
Write-Host "Uploading deployment files..." -ForegroundColor Cyan
$scpCommand = "scp -r deploy/* ${Username}@${ServerIP}:/var/www/music-studio/"
Write-Host "Running: $scpCommand" -ForegroundColor Gray

# Step 2: Create SSH commands to run on server
$sshCommands = @"
mkdir -p /var/www/music-studio
cd /var/www/music-studio
chmod +x *.sh
echo 'Running server setup...'
./server-setup.sh
echo 'Running application setup...'
./app-setup.sh
echo 'Deployment completed!'
echo 'Testing application...'
curl -I http://72.60.232.155
echo 'Music Studio is now live at http://72.60.232.155'
"@

Write-Host "SSH Commands to run:" -ForegroundColor Yellow
Write-Host $sshCommands -ForegroundColor White

Write-Host ""
Write-Host "MANUAL DEPLOYMENT STEPS:" -ForegroundColor Green -BackgroundColor Black
Write-Host "========================" -ForegroundColor Green
Write-Host ""
Write-Host "1. Upload files to server:" -ForegroundColor Yellow
Write-Host "   $scpCommand" -ForegroundColor White
Write-Host ""
Write-Host "2. Connect to server:" -ForegroundColor Yellow
Write-Host "   ssh ${Username}@${ServerIP}" -ForegroundColor White
Write-Host ""
Write-Host "3. Run these commands on server:" -ForegroundColor Yellow
Write-Host $sshCommands -ForegroundColor White
Write-Host ""
Write-Host "Your Music Studio will be live at: http://$ServerIP" -ForegroundColor Green