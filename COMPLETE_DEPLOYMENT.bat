@echo off
echo ========================================
echo   MUSIC STUDIO - FINAL DEPLOYMENT STEP
echo ========================================
echo.
echo The deployment is 95%% complete!
echo Database: 18 tables created successfully
echo Files: All uploaded to /var/www/music-studio/
echo.
echo TO COMPLETE: Run these commands on the server
echo ========================================
echo.
echo 1. SSH into the server:
echo    ssh root@72.60.232.155
echo.
echo 2. Run these commands one by one:
echo.
echo    apt install -y php-fpm php-mysql
echo    systemctl enable php8.3-fpm
echo    systemctl start php8.3-fpm
echo    sed -i 's/php8\.2-fpm/php8.3-fpm/g' /etc/nginx/sites-available/music-studio
echo    systemctl restart nginx
echo.
echo 3. Test the website:
echo    curl -I http://72.60.232.155
echo.
echo ========================================
echo   AFTER COMPLETION:
echo ========================================
echo.
echo Your Music Studio will be live at:
echo http://72.60.232.155
echo.
echo Default admin login:
echo Email: admin@musicstudio.com
echo Password: password
echo.
echo Database credentials:
echo Host: localhost
echo Database: music_studio
echo Username: music_studio
echo Password: music_studio_password
echo.
echo ========================================
pause