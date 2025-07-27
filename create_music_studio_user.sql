-- Create New User for Music Studio Database
-- Copy and paste these commands into phpMyAdmin SQL tab

-- Step 1: Create the new user
-- Replace 'music_studio_user' and 'MusicStudio2025!' with your preferred credentials
CREATE USER 'music_studio_user'@'localhost' IDENTIFIED BY 'MusicStudio2025!';

-- Step 2: Grant permissions to your database
-- Replace 'laravel' with your actual database name if different
GRANT ALL PRIVILEGES ON `laravel`.* TO 'music_studio_user'@'localhost';

-- Alternative database names (uncomment the one that matches yours):
-- GRANT ALL PRIVILEGES ON `music_studio`.* TO 'music_studio_user'@'localhost';
-- GRANT ALL PRIVILEGES ON `music_studio_new`.* TO 'music_studio_user'@'localhost';
-- GRANT ALL PRIVILEGES ON `lemon_hub_studio`.* TO 'music_studio_user'@'localhost';

-- Step 3: Apply the changes
FLUSH PRIVILEGES;

-- Step 4: Verify the user was created
SELECT User, Host FROM mysql.user WHERE User = 'music_studio_user';

-- Step 5: Check what databases the user can access
SHOW GRANTS FOR 'music_studio_user'@'localhost';

-- New User Login Information:
-- URL: http://localhost/phpmyadmin
-- Username: music_studio_user
-- Password: MusicStudio2025!
-- Server: localhost (default)

-- Security Notes:
-- 1. Change the password to something more secure
-- 2. This user will have full access to the specified database only
-- 3. The user cannot access other databases or create new ones 