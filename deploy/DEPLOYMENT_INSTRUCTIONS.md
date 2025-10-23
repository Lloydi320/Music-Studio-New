# Music Studio Deployment Instructions

## Prerequisites
1. Ensure you have SSH access to the server: 72.60.232.155
2. Make sure you have the MySQL root password ready

## Deployment Steps

### Step 1: Upload files to server
Upload the entire 'deploy' folder contents to your server at /var/www/music-studio

You can use SCP, SFTP, or any file transfer method:
`
scp -r deploy/* root@72.60.232.155:/var/www/music-studio/
`

### Step 2: Run server setup (first time only)
SSH into your server and run:
`
ssh root@72.60.232.155
chmod +x /var/www/music-studio/server-setup.sh
/var/www/music-studio/server-setup.sh
`

### Step 3: Run application setup
`
chmod +x /var/www/music-studio/app-setup.sh
/var/www/music-studio/app-setup.sh
`

### Step 4: Access your application
Open your browser and navigate to: http://72.60.232.155

## Troubleshooting

### If you encounter permission issues:
`
chown -R www-data:www-data /var/www/music-studio
chmod -R 755 /var/www/music-studio
chmod -R 775 /var/www/music-studio/storage
chmod -R 775 /var/www/music-studio/bootstrap/cache
`

### If database connection fails:
1. Check MySQL service: systemctl status mysql
2. Verify database credentials in .env file
3. Test database connection: mysql -u music_studio_user -p music_studio

### If Nginx fails to start:
1. Check configuration: 
ginx -t
2. Check error logs: 	ail -f /var/log/nginx/error.log
3. Restart Nginx: systemctl restart nginx

## Post-Deployment
1. Set up SSL certificate (recommended)
2. Configure backup system
3. Set up monitoring
4. Configure email settings in .env file
