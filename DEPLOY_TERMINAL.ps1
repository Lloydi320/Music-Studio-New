# 🚀 TERMINAL DEPLOYMENT SCRIPT
# Music Studio System - Deploy to 72.60.232.155

Write-Host "🚀 Music Studio Terminal Deployment" -ForegroundColor Green -BackgroundColor Black
Write-Host "====================================" -ForegroundColor Green

Write-Host "`n📋 Step 1: Upload Files" -ForegroundColor Yellow
Write-Host "Running SCP upload..." -ForegroundColor White

try {
    # Try to upload files
    $uploadResult = scp -r deploy/* root@72.60.232.155:/var/www/music-studio/ 2>&1
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Files uploaded successfully!" -ForegroundColor Green
    } else {
        Write-Host "⚠️ SCP upload requires password authentication" -ForegroundColor Yellow
        Write-Host "Please run this command manually and enter your password:" -ForegroundColor White
        Write-Host "scp -r deploy/* root@72.60.232.155:/var/www/music-studio/" -ForegroundColor Cyan
        Write-Host "`nPress Enter after uploading files..." -ForegroundColor Yellow
        Read-Host
    }
} catch {
    Write-Host "⚠️ Please upload files manually using:" -ForegroundColor Yellow
    Write-Host "scp -r deploy/* root@72.60.232.155:/var/www/music-studio/" -ForegroundColor Cyan
    Write-Host "`nPress Enter after uploading files..." -ForegroundColor Yellow
    Read-Host
}

Write-Host "`n📋 Step 2: Server Setup Commands" -ForegroundColor Yellow
Write-Host "Copy and paste these commands into your SSH session:" -ForegroundColor White

$commands = @"
# Connect to server
ssh root@72.60.232.155

# Navigate to project directory
cd /var/www/music-studio

# Make scripts executable
chmod +x *.sh

# Run server setup (installs Nginx, MySQL, PHP, etc.)
./server-setup.sh

# Run application setup (Laravel setup, migrations, etc.)
./app-setup.sh

# Verify deployment
./verify-deployment.sh
"@

Write-Host $commands -ForegroundColor Cyan

Write-Host "`n📋 Step 3: Manual SSH Session" -ForegroundColor Yellow
Write-Host "Opening SSH connection..." -ForegroundColor White

# Try to open SSH session
try {
    ssh root@72.60.232.155
} catch {
    Write-Host "Please manually connect using: ssh root@72.60.232.155" -ForegroundColor Cyan
}

Write-Host "`n🎉 Deployment Complete!" -ForegroundColor Green -BackgroundColor Black
Write-Host "Your Music Studio will be live at: http://72.60.232.155" -ForegroundColor Green