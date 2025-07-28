# Admin Users Database Management Guide

## Overview
This guide explains how to manage admin users using the dedicated `admin_users` table in phpMyAdmin. This table provides a centralized way to manage administrator accounts with detailed permissions and roles.

## Database Structure

### Table: `admin_users`
The `admin_users` table contains the following fields:

| Field | Type | Description |
|-------|------|-------------|
| `id` | Primary Key | Unique identifier for each admin |
| `name` | String | Full name of the admin user |
| `email` | String (Unique) | Email address (used as username) |
| `role` | String | Admin role (`admin` or `super_admin`) |
| `permissions` | JSON | Array of permissions granted to the user |
| `is_active` | Boolean | Whether the admin account is active (1) or inactive (0) |
| `last_login_at` | Timestamp | Last login time |
| `created_by` | String | Who created this admin account |
| `notes` | Text | Additional notes about the admin |
| `created_at` | Timestamp | When the record was created |
| `updated_at` | Timestamp | When the record was last updated |

### View: `admin_users_view`
A simplified view for easier browsing with formatted data.

## Accessing phpMyAdmin

### Common URLs:
- **XAMPP/WAMP**: http://localhost/phpmyadmin
- **MAMP**: http://localhost:8888/phpMyAdmin
- **Custom Port**: http://localhost:8080/phpmyadmin

### Database Information:
- **Database Name**: `music_studio_new`
- **Table Name**: `admin_users`
- **View Name**: `admin_users_view`

## Common Admin Management Tasks

### 1. View All Admin Users
```sql
SELECT id, name, email, role, is_active, created_at 
FROM admin_users 
ORDER BY created_at DESC;
```

### 2. View Only Active Admins
```sql
SELECT * FROM admin_users WHERE is_active = 1;
```

### 3. Add New Admin User
```sql
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
    'John Doe',
    'john@musicstudio.com',
    'admin',
    '["manage_bookings","view_dashboard","manage_calendar"]',
    1,
    'manual_phpMyAdmin',
    'Added via phpMyAdmin',
    NOW(),
    NOW()
);
```

### 4. Deactivate an Admin
```sql
UPDATE admin_users 
SET is_active = 0, updated_at = NOW() 
WHERE email = 'admin@example.com';
```

### 5. Reactivate an Admin
```sql
UPDATE admin_users 
SET is_active = 1, updated_at = NOW() 
WHERE email = 'admin@example.com';
```

### 6. Update Admin Role
```sql
UPDATE admin_users 
SET role = 'super_admin', updated_at = NOW() 
WHERE email = 'admin@example.com';
```

### 7. Update Admin Permissions
```sql
UPDATE admin_users 
SET permissions = '["manage_bookings","manage_users","view_dashboard","manage_calendar","database_access","system_settings"]',
    updated_at = NOW() 
WHERE email = 'admin@example.com';
```

### 8. View Admin Permissions
```sql
SELECT name, email, role, 
       JSON_EXTRACT(permissions, '$') as permissions 
FROM admin_users;
```

### 9. Search Admins by Name or Email
```sql
SELECT * FROM admin_users 
WHERE name LIKE '%john%' OR email LIKE '%john%';
```

### 10. Delete an Admin (Use with caution!)
```sql
DELETE FROM admin_users WHERE email = 'admin@example.com';
```

## Permission Types

The following permissions are available:

- `manage_bookings` - Can create, edit, and delete bookings
- `manage_users` - Can manage regular users
- `view_dashboard` - Can access admin dashboard
- `manage_calendar` - Can manage Google Calendar integration
- `database_access` - Can access database management tools
- `system_settings` - Can modify system settings
- `user_management` - Can manage other admin users
- `backup_restore` - Can create backups and restore data

## Admin Roles

### Regular Admin (`admin`)
- Standard administrative privileges
- Can manage bookings and users
- Limited system access

### Super Admin (`super_admin`)
- Full system access
- Can manage other admins
- Can access all system settings
- Can perform backup/restore operations

## Best Practices

1. **Always use the view first**: Browse `admin_users_view` for a cleaner overview
2. **Don't delete, deactivate**: Instead of deleting admin records, set `is_active = 0`
3. **Track changes**: Always update the `updated_at` field when making changes
4. **Use meaningful notes**: Add descriptive notes when creating or modifying admins
5. **Backup before major changes**: Create a database backup before bulk operations

## Syncing with Main Users Table

The `admin_users` table is separate from the main `users` table. To sync admin status:

### Make a user admin in both tables:
```sql
-- Update main users table
UPDATE users SET is_admin = 1 WHERE email = 'user@example.com';

-- Add to admin_users table
INSERT INTO admin_users (
    name, email, role, permissions, is_active, created_by, notes, created_at, updated_at
) SELECT 
    name, email, 'admin', 
    '["manage_bookings","view_dashboard"]',
    1, 'sync_operation', 'Synced from users table', NOW(), NOW()
FROM users WHERE email = 'user@example.com';
```

## Troubleshooting

### Table doesn't exist?
Run the setup script again:
```bash
php setup_admin_database.php
```

### Can't access phpMyAdmin?
Try these URLs:
- http://localhost/phpmyadmin
- http://localhost:8080/phpmyadmin
- http://localhost:8888/phpMyAdmin

### Permission errors?
Ensure your database user has the necessary privileges:
```sql
GRANT ALL PRIVILEGES ON music_studio_new.* TO 'your_user'@'localhost';
FLUSH PRIVILEGES;
```

## Security Notes

- Always use strong, unique emails for admin accounts
- Regularly review and audit admin permissions
- Deactivate unused admin accounts
- Monitor the `last_login_at` field for inactive accounts
- Keep the `notes` field updated for accountability

---

**Created**: January 2025  
**Database**: music_studio_new  
**Table**: admin_users  
**View**: admin_users_view