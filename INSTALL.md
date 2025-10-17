# CHWR Installation Guide

## Quick Setup for Local Development (XAMPP)

### 1. Prerequisites
- Install [XAMPP](https://www.apachefriends.org/) (includes PHP 7.4+ and MySQL)
- Git (to clone the repository)

### 2. Clone Repository
```bash
cd C:/xampp/htdocs/  # Windows
# or
cd /Applications/XAMPP/htdocs/  # macOS
# or  
cd /opt/lampp/htdocs/  # Linux

git clone https://github.com/acesonder/CHWR.git
cd CHWR
```

### 3. Database Setup

#### A. Create Database
1. Start XAMPP and run Apache and MySQL
2. Open phpMyAdmin: http://localhost/phpmyadmin
3. Create a new database named `chwr_db`:
   - Click "New" in the left sidebar
   - Database name: `chwr_db`
   - Collation: `utf8mb4_unicode_ci`
   - Click "Create"

#### B. Import Schema
1. Select `chwr_db` database
2. Click "Import" tab
3. Click "Choose File" and select `sql/schema.sql`
4. Click "Go" at the bottom

### 4. Configure Database Connection
```bash
cp config/database.example.php config/database.php
```

Edit `config/database.php` with your settings:
```php
return [
    'host' => 'localhost',
    'port' => 3306,
    'database' => 'chwr_db',
    'username' => 'root',      // Default XAMPP username
    'password' => '',          // Default XAMPP password (empty)
    'charset' => 'utf8mb4',
    // ... rest of config
];
```

### 5. Set Permissions (Linux/macOS only)
```bash
chmod 755 uploads temp cache sessions backups
```

### 6. Access the Application
Open your browser and navigate to:
```
http://localhost/CHWR
```

### 7. Create First User
1. Click "Register" on the landing page
2. Fill out the registration form
3. Your username will be auto-generated (e.g., JOHSMI01151990)
4. Remember your username - you'll need it to login!

### 8. Test Login
1. Click "Login"
2. Enter your generated username
3. Enter your password
4. Optionally check "Remember me"

---

## Production Setup (Linux Server)

### 1. Server Requirements
- Ubuntu 20.04+ or similar Linux distribution
- Apache 2.4+ or Nginx
- PHP 7.4+
- MySQL 5.7+ or MariaDB 10.3+
- SSL certificate (recommended)

### 2. Install Dependencies
```bash
sudo apt update
sudo apt install apache2 mysql-server php php-mysql php-cli php-mbstring php-xml git
```

### 3. Clone Repository
```bash
cd /var/www/html
sudo git clone https://github.com/acesonder/CHWR.git
cd CHWR
```

### 4. Configure Apache
Create virtual host config:
```bash
sudo nano /etc/apache2/sites-available/chwr.conf
```

Add:
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/html/CHWR
    
    <Directory /var/www/html/CHWR>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/chwr-error.log
    CustomLog ${APACHE_LOG_DIR}/chwr-access.log combined
</VirtualHost>
```

Enable site:
```bash
sudo a2ensite chwr.conf
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### 5. Set Permissions
```bash
sudo chown -R www-data:www-data /var/www/html/CHWR
sudo chmod -R 755 /var/www/html/CHWR
sudo chmod -R 775 uploads temp cache sessions backups
```

### 6. Secure Database Configuration
```bash
sudo mysql_secure_installation
```

Create database and user:
```sql
CREATE DATABASE chwr_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'chwr_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON chwr_db.* TO 'chwr_user'@'localhost';
FLUSH PRIVILEGES;
```

Import schema:
```bash
mysql -u chwr_user -p chwr_db < sql/schema.sql
```

### 7. Configure Application
```bash
cp config/database.example.php config/database.php
nano config/database.php
```

Update with production credentials.

### 8. SSL/HTTPS (Recommended)
```bash
sudo apt install certbot python3-certbot-apache
sudo certbot --apache -d your-domain.com
```

### 9. Security Hardening
- Change default database.php permissions:
  ```bash
  sudo chmod 600 config/database.php
  ```
- Review and update `config/config.php` with production URLs
- Enable PHP error logging (disable display_errors)
- Set up regular database backups
- Configure firewall (UFW)

---

## Troubleshooting

### Database Connection Error
- Verify MySQL is running
- Check credentials in `config/database.php`
- Ensure database exists: `SHOW DATABASES;`

### Permission Denied on Uploads
```bash
sudo chmod 775 uploads temp cache sessions backups
sudo chown -R www-data:www-data uploads temp cache sessions backups
```

### Session Issues
- Ensure `sessions` directory exists and is writable
- Check PHP session settings in `php.ini`

### Auto-generated Username Not Working
- Verify date format in registration
- Check browser console for JavaScript errors
- Ensure PHP date functions are working

### Assessment Not Saving
- Check browser console for network errors
- Verify API endpoints are accessible
- Check database connection and permissions

---

## Default Test Data (Optional)

To create test users for each role:

```sql
-- Admin user (username: ADMSYS01011990, password: admin123)
INSERT INTO users (username, password_hash, first_name, last_name, date_of_birth, role, security_question, security_answer_hash)
VALUES ('ADMSYS01011990', '$2y$10$YourHashedPasswordHere', 'Admin', 'System', '1990-01-01', 'Administrator', 'What is your favorite color?', '$2y$10$YourHashedAnswerHere');

-- Note: Generate proper password hashes using PHP:
-- password_hash('admin123', PASSWORD_BCRYPT)
```

---

## Next Steps

1. Register your first user account
2. Complete the assessment to test functionality
3. Customize branding in `config/config.php`
4. Review security settings
5. Set up backups
6. Configure email settings (if needed)

For issues or questions, please refer to the main README.md or create an issue on GitHub.
