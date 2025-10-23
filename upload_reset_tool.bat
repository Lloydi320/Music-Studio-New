@echo off
echo === Uploading Database Reset Tool ===
echo.

echo Uploading web reset tool to server...
scp web_reset.php root@72.60.232.155:/var/www/music-studio/

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ✓ Upload successful!
    echo.
    echo You can now access the reset tool at:
    echo http://72.60.232.155/web_reset.php
    echo.
    echo IMPORTANT: Delete this file after use for security!
    echo.
) else (
    echo.
    echo ❌ Upload failed. Please check your connection.
    echo.
)

pause