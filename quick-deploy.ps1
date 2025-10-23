# Quick Deploy Script for Music Studio
# This script will deploy your application to 72.60.232.155

param(
    [string]$ServerIP = "72.60.232.155",
    [string]$Username = "root",
    [string]$SSHKey = "",
    [string]$Password = ""
)

Write-Host "=== Music Studio Quick Deploy ===" -ForegroundColor Green
Write-Host "Deploying to: $Username@$ServerIP" -ForegroundColor Yellow

# Check if we have SSH access method
if ([string]::IsNullOrEmpty($SSHKey) -and [string]::IsNullOrEmpty($Password)) {
    Write-Host "Please provide either SSH key path or password for authentication." -ForegroundColor Red
    $Password = Read-Host "Enter SSH password" -AsSecureString
    $Password = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($Password))
}

# Create deployment package
Write-Host "Creating deployment package..." -ForegroundColor Yellow
& .\deploy.ps1 -ServerIP $ServerIP -Username $Username

# Check if deployment package was created
if (-not (Test-Path "deploy")) {
    Write-Host "Failed to create deployment package!" -ForegroundColor Red
    exit 1
}

Write-Host "Deployment package created successfully!" -ForegroundColor Green

# Create a batch script for Windows to handle the deployment
$BatchScript = @"
@echo off
echo === Uploading files to server ===

REM Create directory on server
echo Creating directory on server...
plink -batch -pw "$Password" $Username@$ServerIP "mkdir -p /var/www/music-studio"

REM Upload files using pscp
echo Uploading application files...
pscp -r -pw "$Password" deploy\* $Username@$ServerIP:/var/www/music-studio/

REM Make scripts executable and run setup
echo Running server setup...
plink -batch -pw "$Password" $Username@$ServerIP "cd /var/www/music-studio && chmod +x *.sh"

echo Running server setup script...
plink -batch -pw "$Password" $Username@$ServerIP "cd /var/www/music-studio && ./server-setup.sh"

echo Running application setup script...
plink -batch -pw "$Password" $Username@$ServerIP "cd /var/www/music-studio && ./app-setup.sh"

echo === Deployment Complete ===
echo Your Music Studio is now accessible at: http://$ServerIP
pause
"@

$BatchScript | Out-File -FilePath "deploy-to-server.bat" -Encoding ASCII

Write-Host ""
Write-Host "=== Deployment Options ===" -ForegroundColor Cyan
Write-Host ""
Write-Host "Option 1: Automatic Deployment (if you have PuTTY tools installed)" -ForegroundColor Yellow
Write-Host "Run: deploy-to-server.bat" -ForegroundColor White
Write-Host ""
Write-Host "Option 2: Manual Deployment" -ForegroundColor Yellow
Write-Host "1. Upload the 'deploy' folder contents to your server:" -ForegroundColor White
Write-Host "   scp -r deploy/* $Username@$ServerIP:/var/www/music-studio/" -ForegroundColor Gray
Write-Host ""
Write-Host "2. SSH into your server and run:" -ForegroundColor White
Write-Host "   ssh $Username@$ServerIP" -ForegroundColor Gray
Write-Host "   cd /var/www/music-studio" -ForegroundColor Gray
Write-Host "   chmod +x *.sh" -ForegroundColor Gray
Write-Host "   ./server-setup.sh" -ForegroundColor Gray
Write-Host "   ./app-setup.sh" -ForegroundColor Gray
Write-Host ""
Write-Host "Option 3: Use any SFTP client (FileZilla, WinSCP, etc.)" -ForegroundColor Yellow
Write-Host "Upload the contents of the 'deploy' folder to /var/www/music-studio/" -ForegroundColor White
Write-Host "Then SSH in and run the setup scripts as shown in Option 2" -ForegroundColor White
Write-Host ""
Write-Host "After deployment, your Music Studio will be accessible at:" -ForegroundColor Green
Write-Host "http://$ServerIP" -ForegroundColor Cyan
Write-Host ""

# Create a simple SSH command file for easy access
$SSHCommands = @"
# SSH Commands for Music Studio Deployment

# Connect to server
ssh $Username@$ServerIP

# Once connected, run these commands:
cd /var/www/music-studio
chmod +x *.sh
./server-setup.sh
./app-setup.sh

# Check status
systemctl status nginx
systemctl status mysql
systemctl status php8.2-fpm

# View logs if needed
tail -f /var/log/nginx/error.log
tail -f /var/www/music-studio/storage/logs/laravel.log
"@

$SSHCommands | Out-File -FilePath "ssh-commands.txt" -Encoding UTF8

Write-Host "Additional files created:" -ForegroundColor Green
Write-Host "- deploy-to-server.bat (Windows batch script for automatic deployment)" -ForegroundColor White
Write-Host "- ssh-commands.txt (SSH commands reference)" -ForegroundColor White