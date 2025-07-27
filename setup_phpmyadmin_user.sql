-- SQL Script to Create New phpMyAdmin User
-- Run these commands in your MySQL/phpMyAdmin SQL tab

-- 1. Create a new MySQL user (replace 'newuser' and 'password123' with desired credentials)
CREATE USER 'newuser'@'localhost' IDENTIFIED BY 'password123';

-- 2. Grant specific privileges (choose one of the options below)

-- OPTION A: Full access to all databases (Admin level)
GRANT ALL PRIVILEGES ON *.* TO 'newuser'@'localhost' WITH GRANT OPTION;

-- OPTION B: Access only to your Music Studio database (Safer option)
-- GRANT ALL PRIVILEGES ON `music_studio_db`.* TO 'newuser'@'localhost';

-- OPTION C: Read-only access to specific database
-- GRANT SELECT ON `music_studio_db`.* TO 'newuser'@'localhost';

-- OPTION D: Limited access (can view and edit data, but not structure)
-- GRANT SELECT, INSERT, UPDATE, DELETE ON `music_studio_db`.* TO 'newuser'@'localhost';

-- 3. Apply the changes
FLUSH PRIVILEGES;

-- 4. Verify the user was created
SELECT User, Host FROM mysql.user WHERE User = 'newuser';

-- 5. Show granted privileges
SHOW GRANTS FOR 'newuser'@'localhost';

-- Instructions for the new user:
-- 1. Go to: http://localhost/phpmyadmin (or your phpMyAdmin URL)
-- 2. Username: newuser
-- 3. Password: password123
-- 4. Server: localhost (usually default) 