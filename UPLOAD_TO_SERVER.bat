@echo off
echo.
echo ========================================
echo   🚀 Music Studio File Upload Script
echo ========================================
echo.

echo Choose your upload method:
echo.
echo 1. SCP Upload (requires SSH tools)
echo 2. Manual Upload Instructions
echo 3. Exit
echo.

set /p choice="Enter your choice (1, 2, or 3): "

if "%choice%"=="1" (
    echo.
    echo 📤 Uploading files via SCP...
    echo.
    scp -r deploy/* root@72.60.232.155:/var/www/music-studio/
    if %errorlevel% equ 0 (
        echo.
        echo ✅ Files uploaded successfully!
        echo.
        echo 🔧 Now run these commands on your server:
        echo ssh root@72.60.232.155
        echo cd /var/www/music-studio
        echo chmod +x *.sh
        echo ./server-setup.sh
        echo ./app-setup.sh
        echo.
        echo 🎉 Your Music Studio will be live at: http://72.60.232.155
    ) else (
        echo.
        echo ❌ SCP upload failed. Please try manual upload method.
        echo.
    )
) else if "%choice%"=="2" (
    echo.
    echo 📁 Manual Upload Instructions:
    echo ================================
    echo.
    echo 1. Open your SFTP client (FileZilla, WinSCP, etc.)
    echo 2. Connect to: 72.60.232.155
    echo 3. Username: root
    echo 4. Navigate to: /var/www/music-studio/
    echo 5. Upload ALL contents of 'deploy' folder
    echo.
    echo 🔧 After upload, SSH into server:
    echo ssh root@72.60.232.155
    echo cd /var/www/music-studio
    echo chmod +x *.sh
    echo ./server-setup.sh
    echo ./app-setup.sh
    echo.
    echo 🎉 Your Music Studio will be live at: http://72.60.232.155
) else if "%choice%"=="3" (
    echo.
    echo Goodbye!
    exit /b 0
) else (
    echo.
    echo ❌ Invalid choice. Please run the script again.
)

echo.
pause