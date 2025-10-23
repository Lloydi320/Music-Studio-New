# 🚀 AUTOMATED DEPLOYMENT SCRIPT
# Music Studio System - Deploy to 72.60.232.155

param(
    [string]$ServerIP = "72.60.232.155",
    [string]$Username = "root"
)

Write-Host "🚀 Starting Music Studio Deployment..." -ForegroundColor Green
Write-Host "Target Server: $ServerIP" -ForegroundColor Yellow

# Check if deployment package exists
if (!(Test-Path "deploy")) {
    Write-Host "❌ Deployment package not found! Please run deploy.ps1 first." -ForegroundColor Red
    exit 1
}

Write-Host "✅ Deployment package found!" -ForegroundColor Green

# Create deployment commands
$deployCommands = @"
# Music Studio Deployment Commands
# Copy these commands and run them on your server

# 1. Connect to server
ssh root@72.60.232.155

# 2. Create project directory
mkdir -p /var/www/music-studio
cd /var/www/music-studio

# 3. After uploading files, make scripts executable
chmod +x *.sh

# 4. Run server setup
./server-setup.sh

# 5. Run application setup
./app-setup.sh

# 6. Check status
systemctl status nginx
systemctl status mysql
systemctl status php8.2-fpm

# 7. Test the application
curl -I http://72.60.232.155
"@

# Save deployment commands
$deployCommands | Out-File -FilePath "DEPLOYMENT_COMMANDS.txt" -Encoding UTF8

Write-Host "`n📋 Deployment Commands Created!" -ForegroundColor Green
Write-Host "Commands saved to: DEPLOYMENT_COMMANDS.txt" -ForegroundColor Yellow

Write-Host "`n🎯 DEPLOYMENT READY!" -ForegroundColor Green -BackgroundColor Black
Write-Host "===================" -ForegroundColor Green

Write-Host "`n📦 What's Ready:" -ForegroundColor Yellow
Write-Host "✅ Deployment package: deploy/ folder"
Write-Host "✅ Deployment commands: DEPLOYMENT_COMMANDS.txt"

Write-Host "`n🚀 Next Steps:" -ForegroundColor Yellow
Write-Host "1. Upload 'deploy' folder contents to server:/var/www/music-studio/"
Write-Host "2. SSH into server: ssh root@72.60.232.155"
Write-Host "3. Run setup scripts as shown in DEPLOYMENT_COMMANDS.txt"
Write-Host "4. Visit: http://72.60.232.155"

Write-Host "`n🎵 Your Music Studio will be LIVE!" -ForegroundColor Green -BackgroundColor Black