# CHWR Installation and Testing Guide

## Prerequisites

- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10.3+
- Apache or Nginx web server
- phpMyAdmin (optional, for database management)

## Quick Setup

### Option 1: Using XAMPP/WAMP/MAMP (Recommended for local testing)

1. **Install XAMPP** (includes Apache, MySQL, and PHP):
   - Download from: https://www.apachefriends.org/
   - Install and start Apache and MySQL services

2. **Clone the repository**:
   ```bash
   cd C:/xampp/htdocs/  # Windows
   # or cd /Applications/XAMPP/htdocs/  # macOS
   # or cd /opt/lampp/htdocs/  # Linux
   
   git clone https://github.com/acesonder/CHWR.git
   cd CHWR
   ```

3. **Configure database** (if needed):
   - Edit `config/database.php` if your MySQL credentials differ
   - Default: localhost, root, (no password)

4. **Initialize database**:
   ```bash
   php database/setup.php
   ```
   
   Expected output:
   ```
   Database initialized successfully!
   ✓ Statement executed successfully
   ✓ Statement executed successfully
   ...
   ✓ Database setup completed!
   Default admin credentials:
     Username: admin
     Password: admin123
   ```

5. **Access the application**:
   - Open browser: http://localhost/CHWR/
   - Login with: admin / admin123

### Option 2: Using PHP Built-in Server (Quick Testing)

1. **Setup database** using XAMPP MySQL or standalone MySQL

2. **Initialize database**:
   ```bash
   php database/setup.php
   ```

3. **Start PHP server**:
   ```bash
   php -S localhost:8000
   ```

4. **Access the application**:
   - Open browser: http://localhost:8000/
   - Login with: admin / admin123

### Option 3: Using Docker (Advanced)

Coming soon - Docker configuration will be added for containerized deployment.

## Testing the Application

### 1. Test Login System

1. Navigate to http://localhost/CHWR/ (or your configured URL)
2. You should be redirected to the login page
3. Login with default credentials:
   - Username: `admin`
   - Password: `admin123`
4. You should be redirected to the dashboard

### 2. Test User Management (Admin only)

1. From dashboard, click "Manage Users" or navigate to Users page
2. **Create a new user**:
   - Click "Add New User" button
   - Fill in the form with test data:
     - Username: testuser
     - Email: test@example.com
     - Password: test123
     - First Name: Test
     - Last Name: User
     - Role: Select any role
     - Active: Checked
   - Click "Create User"
   - Verify success message appears
   - Check that new user appears in the table

3. **Edit a user**:
   - Click "Edit" button on the test user
   - Modify some fields (e.g., first name)
   - Click "Update User"
   - Verify changes are saved

4. **Search functionality**:
   - Type in the search box
   - Verify table filters correctly

5. **Delete a user**:
   - Click "Delete" on the test user
   - Confirm deletion
   - Verify user is removed from table

### 3. Test Role-Based Access

1. **Create users with different roles**:
   - Create a Management user
   - Create a Service Provider user
   - Create an Outreach user
   - Create a Client user

2. **Test each role**:
   - Logout (admin)
   - Login as each role
   - Verify dashboard shows role-specific content
   - Try accessing restricted pages:
     - Management: Can access users page (view only)
     - Service Provider: Cannot access users page
     - Outreach: Cannot access users page
     - Client: Cannot access users page
     - Only Admin can access Audit Log

### 4. Test Audit Log

1. Login as admin
2. Navigate to "Audit Log"
3. Verify all actions are logged:
   - User logins
   - User creation
   - User updates
   - User deletions
4. Check pagination if there are many records

### 5. Test AJAX Functionality

1. Open browser developer tools (F12)
2. Go to Network tab
3. Perform user operations (create, edit, delete)
4. Verify AJAX requests are made to `/api/users.php`
5. Check that page doesn't reload (modern AJAX behavior)
6. Verify success/error messages appear dynamically

### 6. Test Security Features

1. **Session Management**:
   - Login as user
   - Open new tab, verify still logged in
   - Logout, verify redirected to login
   - Try accessing dashboard.php directly, verify redirected to login

2. **SQL Injection Protection**:
   - Try entering SQL in username: `' OR '1'='1`
   - Verify login fails safely

3. **XSS Protection**:
   - Try creating user with name: `<script>alert('XSS')</script>`
   - Verify script doesn't execute, shows as text

4. **Password Hashing**:
   - Check database (phpMyAdmin or command line)
   - Verify passwords are hashed, not plain text

## Common Issues and Solutions

### Issue: "Connection failed" error

**Solution**: 
- Verify MySQL is running
- Check database credentials in `config/database.php`
- Ensure MySQL port 3306 is not blocked

### Issue: "Cannot modify header information" error

**Solution**:
- Check for output before PHP headers
- Ensure no whitespace before `<?php` tags
- Check file encoding is UTF-8 without BOM

### Issue: "404 Not Found" for pages

**Solution**:
- Verify web server is running
- Check document root points to project directory
- For Apache, ensure mod_rewrite is enabled

### Issue: CSS/JS not loading

**Solution**:
- Check file paths in browser developer tools
- Verify `/public/css/styles.css` and `/public/js/main.js` are accessible
- May need to adjust paths based on web server configuration

### Issue: "Permission denied" errors

**Solution**:
- Ensure web server has read permissions on all files
- On Linux/Mac: `chmod -R 755 /path/to/CHWR`

## Database Manual Setup (Alternative)

If `database/setup.php` doesn't work, you can manually setup:

1. Open phpMyAdmin or MySQL command line
2. Create database:
   ```sql
   CREATE DATABASE chwr_db;
   ```
3. Import schema:
   ```bash
   mysql -u root -p chwr_db < database/schema.sql
   ```

## Default Credentials

After setup, you can login with:
- **Username**: admin
- **Password**: admin123

**Important**: Change the admin password after first login!

## Next Steps

1. Change default admin password
2. Create users for each role
3. Customize dashboard content for your needs
4. Add additional features as requirements evolve
5. Configure backup procedures for database

## Support

For issues or questions:
- Check this guide first
- Review code comments in PHP files
- Open an issue on GitHub
- Contact system administrator

## Production Deployment Checklist

Before deploying to production:

- [ ] Change database credentials
- [ ] Update admin password
- [ ] Enable HTTPS/SSL
- [ ] Configure proper file permissions
- [ ] Set up database backups
- [ ] Enable error logging (disable display_errors)
- [ ] Configure session security settings
- [ ] Set up monitoring and alerts
- [ ] Test all functionality thoroughly
- [ ] Document any custom configurations
