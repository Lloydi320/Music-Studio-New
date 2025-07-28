-- SQL Script to Make lemonhubstudioweb@gmail.com a Full Super Admin
-- This script updates both the main users table and admin_users table

-- Step 1: Update the main users table to ensure admin status
UPDATE users 
SET is_admin = 1 
WHERE email = 'lemonhubstudioweb@gmail.com';

-- Step 2: Update the admin_users table with full super admin permissions
UPDATE admin_users 
SET 
    role = 'super_admin',
    permissions = JSON_ARRAY(
        'manage_bookings',
        'manage_users', 
        'view_dashboard',
        'manage_calendar',
        'database_access',
        'system_settings',
        'user_management',
        'backup_restore'
    ),
    is_active = 1,
    notes = 'Upgraded to full super admin with all permissions',
    updated_at = NOW()
WHERE email = 'lemonhubstudioweb@gmail.com';

-- Step 3: Verify the update was successful
SELECT 
    id,
    name,
    email,
    role,
    JSON_EXTRACT(permissions, '$') as permissions,
    is_active,
    notes,
    updated_at
FROM admin_users 
WHERE email = 'lemonhubstudioweb@gmail.com';

-- Step 4: Check if user exists in main users table
SELECT 
    id,
    name,
    email,
    is_admin,
    created_at
FROM users 
WHERE email = 'lemonhubstudioweb@gmail.com';

-- Alternative: If the user doesn't exist in admin_users table, create the record
-- (Only run this if the UPDATE above affected 0 rows)
/*
INSERT INTO admin_users (
    name, 
    email, 
    role, 
    permissions, 
    is_active, 
    created_by, 
    notes, 
    created_at, 
    updated_at
) VALUES (
    'Lemon Hub Studio Admin',
    'lemonhubstudioweb@gmail.com',
    'super_admin',
    JSON_ARRAY(
        'manage_bookings',
        'manage_users',
        'view_dashboard', 
        'manage_calendar',
        'database_access',
        'system_settings',
        'user_management',
        'backup_restore'
    ),
    1,
    'sql_script',
    'Created as full super admin via SQL script',
    NOW(),
    NOW()
);
*/

-- Step 5: View all super admins to confirm
SELECT 
    id,
    name,
    email,
    role,
    is_active,
    created_at
FROM admin_users 
WHERE role = 'super_admin' 
ORDER BY created_at DESC;