# CHWR Security Guidelines

## Security Best Practices

### 1. Password Security
- **Minimum Length**: 8 characters (configured in `config/config.php`)
- **Hashing**: Uses bcrypt via PHP's `password_hash()` with cost factor 10
- **Storage**: Never store plain text passwords
- **Recovery**: Uses security questions (answers also hashed)

### 2. Authentication Security

#### Session Management
- Sessions stored server-side with secure IDs
- Session timeout: 2 hours (configurable)
- Session regeneration on login
- Proper session destruction on logout

#### Remember Me Feature
- Uses cryptographically secure random tokens
- Tokens stored hashed in database
- 4-day expiration (configurable)
- Token invalidation on logout

#### Login Protection
- Rate limiting: 5 attempts per account (recommended to implement)
- Account lockout: 15 minutes after max attempts
- Activity logging of all login attempts

### 3. SQL Injection Prevention
- **All queries use PDO prepared statements**
- No string concatenation in SQL queries
- Parameterized queries throughout application
- Input validation on all user inputs

### 4. Cross-Site Scripting (XSS) Prevention
- All output escaped using `htmlspecialchars()`
- User-generated content sanitized
- CSP headers in `.htaccess`
- No `eval()` or similar dangerous functions

### 5. Cross-Site Request Forgery (CSRF) Protection
- CSRF tokens generated for all forms
- Token validation on form submission
- Tokens stored in user session
- Single-use tokens for sensitive operations

Example implementation:
```php
// Generate token
$token = $auth->generateCSRFToken();

// In form
<input type="hidden" name="csrf_token" value="<?php echo $token; ?>">

// Verify token
if (!$auth->verifyCSRFToken($_POST['csrf_token'])) {
    die('Invalid request');
}
```

### 6. File Upload Security
- Whitelist allowed file types
- Maximum file size limits (5MB default)
- Files stored outside web root when possible
- No PHP execution in upload directories
- Filename sanitization
- MIME type validation

### 7. Database Security
- Separate database user with minimal privileges
- Database credentials stored in separate config file
- Config file excluded from version control
- Regular backups encrypted at rest
- Connection over localhost or secured connection

### 8. Error Handling
**Development:**
```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

**Production:**
```php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);
```

- Generic error messages to users
- Detailed errors logged to file
- No stack traces exposed to users

### 9. Input Validation

#### Server-Side Validation (Required)
```php
// Email validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // Invalid email
}

// Date validation
if (!DateTime::createFromFormat('Y-m-d', $date)) {
    // Invalid date
}

// Whitelist validation for enums
if (!in_array($role, ['Client', 'Outreach Worker', 'Service Provider', 'Administrator'])) {
    // Invalid role
}
```

#### Client-Side Validation (UX Enhancement)
- HTML5 validation attributes
- JavaScript validation before submission
- Never rely solely on client-side validation

### 10. API Security
- Session-based authentication required
- JSON response format
- HTTP method validation (POST, GET, etc.)
- Rate limiting (recommended to implement)
- Input validation on all endpoints

### 11. Privacy Protection

#### Auto-Generated Usernames
- Format: FIRSTLAST + MMDDYY (e.g., JOHSMI01151990)
- Prevents use of identifiable usernames
- Reduces risk of targeted attacks

#### Data Minimization
- Only collect necessary information
- Optional fields clearly marked
- Users can skip sensitive questions

#### Access Control
- Role-based access control (RBAC)
- Users can only access their own data
- Staff can only access assigned clients
- Admins have full access (logged)

### 12. Secure Configuration

#### Database Configuration
```php
// config/database.php
return [
    'host' => 'localhost',
    'database' => 'chwr_db',
    'username' => 'chwr_user',  // NOT root
    'password' => 'strong_random_password',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_EMULATE_PREPARES => false,  // Use real prepared statements
    ]
];
```

#### File Permissions
```bash
# Configuration files
chmod 600 config/database.php

# Web-writable directories
chmod 775 uploads temp cache sessions backups

# Application files
chmod 644 *.php
chmod 755 api/ includes/ views/

# Web server ownership
chown -R www-data:www-data /path/to/CHWR
```

### 13. HTTPS/SSL
**Production Requirements:**
- Force HTTPS for all connections
- Use TLS 1.2 or higher
- Proper SSL certificate (Let's Encrypt free)
- HSTS header enabled
- Secure cookie flag set

```php
// Set secure cookie in production
if ($_SERVER['HTTPS'] ?? false) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'],
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
}
```

### 14. Activity Logging
All security-relevant events are logged:
- Login attempts (success/failure)
- Password changes
- Account creation
- Permission changes
- Data access by staff
- Failed authentication attempts

Logs stored in `activity_logs` table with:
- User ID
- Action type
- Timestamp
- IP address
- User agent

### 15. Regular Maintenance

#### Weekly
- Review activity logs for suspicious activity
- Check for failed login attempts
- Monitor disk space for logs/uploads

#### Monthly
- Update dependencies (PHP, MySQL, libraries)
- Review and rotate logs
- Test backup restoration
- Review user accounts for inactive/suspicious accounts

#### Quarterly
- Security audit
- Password policy review
- Permission review
- Penetration testing (if resources available)

### 16. Incident Response Plan

If security breach detected:
1. **Immediate**: Disable affected accounts
2. **Investigate**: Review logs to determine scope
3. **Contain**: Patch vulnerability
4. **Notify**: Inform affected users if data compromised
5. **Document**: Record incident details
6. **Review**: Update security procedures

### 17. Known Limitations

Current implementation focuses on core security. Consider adding:
- Rate limiting on login attempts
- Two-factor authentication (2FA)
- Password complexity requirements
- Password history (prevent reuse)
- Account recovery via email
- IP-based access restrictions
- Brute force protection
- Advanced audit logging
- Encryption at rest for sensitive data

### 18. Compliance Considerations

#### Privacy
- GDPR considerations if serving EU users
- PIPEDA compliance (Canada)
- Right to be forgotten
- Data portability
- Consent management

#### Healthcare
- If handling protected health information (PHI):
  - HIPAA compliance (US)
  - PHIPA compliance (Ontario, Canada)
  - Enhanced encryption requirements
  - Business associate agreements

### 19. Security Checklist for Production

- [ ] Force HTTPS
- [ ] Configure secure session settings
- [ ] Disable PHP error display
- [ ] Enable error logging
- [ ] Set proper file permissions
- [ ] Use non-root database user
- [ ] Strong database password
- [ ] Configure firewall (UFW, iptables)
- [ ] Enable Apache security modules
- [ ] Install and configure fail2ban
- [ ] Set up automated backups
- [ ] Configure log rotation
- [ ] Review and harden php.ini
- [ ] Implement rate limiting
- [ ] Set up monitoring/alerts
- [ ] SSL certificate installed
- [ ] Security headers configured
- [ ] Remove development tools
- [ ] Change default ports (if applicable)
- [ ] Regular security updates enabled

### 20. Reporting Security Issues

If you discover a security vulnerability:
1. **Do NOT** open a public GitHub issue
2. Email security concerns to: security@chwr.org (or designated contact)
3. Include:
   - Description of the vulnerability
   - Steps to reproduce
   - Potential impact
   - Suggested fix (if any)

We aim to respond within 48 hours and provide a fix within 7 days for critical issues.

---

## Additional Resources

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [PHP Security Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/PHP_Configuration_Cheat_Sheet.html)
- [MySQL Security Best Practices](https://dev.mysql.com/doc/refman/8.0/en/security-guidelines.html)
- [Let's Encrypt - Free SSL](https://letsencrypt.org/)

---

**Remember**: Security is an ongoing process, not a one-time setup. Stay vigilant and keep the system updated!
