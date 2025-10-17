# CHWR Quick Reference Guide

## Quick Start (5 minutes)

1. **Install XAMPP** (includes PHP and MySQL)
   ```
   Download from: https://www.apachefriends.org/
   ```

2. **Clone or extract CHWR to htdocs**
   ```bash
   cd C:/xampp/htdocs/
   git clone https://github.com/acesonder/CHWR.git
   ```

3. **Start XAMPP services**
   - Start Apache
   - Start MySQL

4. **Initialize database**
   ```bash
   cd CHWR
   php database/setup.php
   ```

5. **Open in browser**
   ```
   http://localhost/CHWR/
   ```

6. **Login**
   - Username: `admin`
   - Password: `admin123`

## Default Credentials

| Role | Username | Password | Access Level |
|------|----------|----------|--------------|
| Admin | admin | admin123 | Full access |

## Common Tasks

### Add a New User
1. Login as admin
2. Click "Manage Users"
3. Click "Add New User" button
4. Fill in the form
5. Select role from dropdown
6. Click "Create User"

### Edit a User
1. Go to "Manage Users"
2. Find user in table
3. Click "Edit" button
4. Modify fields
5. Click "Update User"

### Delete a User
1. Go to "Manage Users"
2. Find user in table
3. Click "Delete" button
4. Confirm deletion

### View Audit Log
1. Login as admin
2. Click "Audit Log" in navigation
3. View activities with pagination

### Change Your Password
1. Go to "Manage Users"
2. Find your account
3. Click "Edit"
4. Enter new password
5. Click "Update User"

## File Locations

### Configuration
- Database config: `config/database.php`
- Authentication: `includes/auth.php`

### Pages
- Login: `login.php`
- Dashboard: `dashboard.php`
- User Management: `users.php`
- Audit Log: `audit-log.php`

### Assets
- Styles: `public/css/styles.css`
- JavaScript: `public/js/main.js`

### Database
- Schema: `database/schema.sql`
- Setup: `database/setup.php`

### API
- Users API: `api/users.php`

## User Roles

| Role | User Management | Audit Log | Own Profile |
|------|----------------|-----------|-------------|
| Admin | Full CRUD | Yes | Yes |
| Management | View/Edit | No | Yes |
| Service Provider | No | No | Yes |
| Outreach | No | No | Yes |
| Client | No | No | Yes |

## Database Tables

### roles
- id (PK)
- role_name
- description
- created_at

### users
- id (PK)
- username (unique)
- email (unique)
- password (hashed)
- first_name
- last_name
- role_id (FK)
- is_active
- created_at
- updated_at

### audit_log
- id (PK)
- user_id (FK)
- action
- details
- ip_address
- created_at

## API Endpoints

### GET /api/users.php?action=get&id={id}
Get single user details

### POST /api/users.php
Create, update, or delete users

**Parameters**:
- action: create | update | delete
- For create: username, email, password, first_name, last_name, role_id, is_active
- For update: id + fields to update
- For delete: id

## Color Codes

### Role Badges
- Admin: #e74c3c (Red)
- Management: #9b59b6 (Purple)
- Service Provider: #3498db (Blue)
- Outreach: #27ae60 (Green)
- Client: #95a5a6 (Gray)

### Status
- Active: #27ae60 (Green)
- Inactive: #e74c3c (Red)

### UI
- Primary: #2c3e50 (Dark Blue)
- Secondary: #3498db (Blue)
- Success: #27ae60 (Green)
- Danger: #e74c3c (Red)

## Security Best Practices

1. **Change default admin password immediately**
2. **Use strong passwords** (min 8 chars, mix of upper/lower/numbers)
3. **Don't share credentials**
4. **Regularly review audit log**
5. **Keep software updated**
6. **Use HTTPS in production**
7. **Regular database backups**
8. **Limit failed login attempts** (future enhancement)

## Troubleshooting

### Can't login
- Check username/password
- Verify account is active
- Check database connection
- Review audit log for errors

### Page not found
- Check web server is running
- Verify correct URL
- Check .htaccess configuration

### Database errors
- Verify MySQL is running
- Check database credentials
- Run database/setup.php again
- Check database exists

### Missing users
- Login as admin
- Recreate from User Management
- Check is_active status

### AJAX not working
- Check browser console for errors
- Verify JavaScript is enabled
- Check API endpoint accessibility
- Review network tab in dev tools

## Development Tips

### Adding New Features
1. Create new PHP file in root
2. Include `includes/auth.php`
3. Use `requireRole()` for access control
4. Include header/footer templates
5. Add navigation link in header.php

### Custom Styling
1. Edit `public/css/styles.css`
2. Use existing CSS variables
3. Test responsive design
4. Clear browser cache

### Adding JavaScript
1. Edit `public/js/main.js`
2. Use existing AJAX helper
3. Follow naming conventions
4. Test in multiple browsers

### Database Changes
1. Update `database/schema.sql`
2. Create migration script if needed
3. Test with fresh install
4. Document changes

## Keyboard Shortcuts

- Tab: Navigate form fields
- Enter: Submit forms
- Esc: Close modals (future)

## Browser Compatibility

✓ Chrome 90+
✓ Firefox 88+
✓ Safari 14+
✓ Edge 90+
✗ Internet Explorer (not supported)

## Support Resources

- **Installation Guide**: INSTALLATION.md
- **Features Documentation**: FEATURES.md
- **UI Guide**: UI-GUIDE.md
- **README**: README.md
- **Validation Script**: ./validate.sh

## Useful Commands

```bash
# Check PHP version
php -v

# Validate PHP syntax
php -l filename.php

# Start PHP built-in server
php -S localhost:8000

# Initialize database
php database/setup.php

# Run validation
./validate.sh

# Check MySQL status (Linux)
sudo service mysql status

# Start MySQL (Linux)
sudo service mysql start
```

## Database Commands

```sql
-- Check database exists
SHOW DATABASES LIKE 'chwr_db';

-- View all users
SELECT u.*, r.role_name FROM users u JOIN roles r ON u.role_id = r.id;

-- Count users by role
SELECT r.role_name, COUNT(*) as count 
FROM users u 
JOIN roles r ON u.role_id = r.id 
GROUP BY r.role_name;

-- View recent audit entries
SELECT * FROM audit_log ORDER BY created_at DESC LIMIT 10;

-- Reset admin password (use in MySQL)
UPDATE users 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE username = 'admin';
-- This sets password to: admin123
```

## Common Customizations

### Change Database Name
Edit: `config/database.php`
```php
define('DB_NAME', 'your_database_name');
```

### Add New Role
1. Insert into roles table:
   ```sql
   INSERT INTO roles (role_name, description) 
   VALUES ('NewRole', 'Description');
   ```
2. Add constant in `includes/auth.php`
3. Update header navigation logic
4. Add role-specific dashboard content

### Customize Colors
Edit: `public/css/styles.css`
```css
:root {
    --primary-color: #your-color;
    --secondary-color: #your-color;
}
```

## Performance Tips

- Enable PHP opcache in production
- Use database indexes (already implemented)
- Enable gzip compression (see .htaccess)
- Minify CSS/JS for production
- Use CDN for assets
- Enable browser caching

## Backup Procedure

```bash
# Backup database
mysqldump -u root -p chwr_db > backup_$(date +%Y%m%d).sql

# Backup files
tar -czf chwr_backup_$(date +%Y%m%d).tar.gz /path/to/CHWR

# Restore database
mysql -u root -p chwr_db < backup_20231215.sql
```

## Version Information

- **Version**: 1.0.0
- **PHP Required**: 7.4+
- **MySQL Required**: 5.7+
- **Release Date**: 2024

## License

MIT License - See LICENSE file

---

**Need more help?** Check the detailed documentation files or open an issue on GitHub.
