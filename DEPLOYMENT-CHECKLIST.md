# CHWR Deployment Checklist

Use this checklist when deploying the CHWR application to production.

## Pre-Deployment

### 1. Environment Setup
- [ ] Web server installed (Apache/Nginx)
- [ ] PHP 7.4+ installed
- [ ] MySQL/MariaDB installed
- [ ] Required PHP extensions enabled (mysqli, session, json)
- [ ] SSL certificate obtained (for HTTPS)

### 2. Security Configuration
- [ ] Change default admin password
- [ ] Update database credentials in `config/database.php`
- [ ] Set strong database password
- [ ] Review and adjust security headers in `.htaccess`
- [ ] Enable HTTPS/SSL
- [ ] Configure firewall rules

### 3. Database Setup
- [ ] Create production database
- [ ] Run `php database/setup.php`
- [ ] Verify all tables created successfully
- [ ] Create database backup user (read-only)
- [ ] Test database connection

### 4. File Permissions
- [ ] Set appropriate file permissions (644 for files, 755 for directories)
- [ ] Ensure web server can read all files
- [ ] Restrict write access to necessary directories only
- [ ] Verify `config/` directory is not web-accessible

### 5. Configuration
- [ ] Review `config/database.php` settings
- [ ] Configure session settings in `includes/auth.php`
- [ ] Set up error logging (disable `display_errors` in php.ini)
- [ ] Configure timezone in php.ini
- [ ] Set `session.cookie_secure = 1` for HTTPS
- [ ] Set `session.cookie_httponly = 1`

## Deployment

### 6. Code Deployment
- [ ] Upload/clone files to production server
- [ ] Verify all files transferred correctly
- [ ] Run `./validate.sh` to check installation
- [ ] Clear any cached files

### 7. Testing
- [ ] Test login with admin account
- [ ] Create test users for each role
- [ ] Verify role-based access control
- [ ] Test CRUD operations
- [ ] Check audit log functionality
- [ ] Test on different browsers
- [ ] Test on mobile devices
- [ ] Verify AJAX operations work
- [ ] Test error handling

### 8. Performance
- [ ] Enable PHP opcache
- [ ] Configure database query caching
- [ ] Enable gzip compression
- [ ] Optimize images (if added)
- [ ] Test page load times
- [ ] Configure CDN (if needed)

### 9. Monitoring
- [ ] Set up error logging
- [ ] Configure log rotation
- [ ] Set up uptime monitoring
- [ ] Configure backup alerts
- [ ] Set up security monitoring

## Post-Deployment

### 10. Backup Setup
- [ ] Configure automated database backups
- [ ] Test backup restoration
- [ ] Configure file backups
- [ ] Document backup procedures
- [ ] Store backups in secure location

### 11. Documentation
- [ ] Document production URLs
- [ ] Document admin credentials (securely)
- [ ] Create user guides
- [ ] Document deployment process
- [ ] Update README with production notes

### 12. User Management
- [ ] Create production user accounts
- [ ] Assign appropriate roles
- [ ] Deactivate test accounts
- [ ] Communicate credentials securely
- [ ] Train users on system usage

### 13. Maintenance Plan
- [ ] Schedule regular security updates
- [ ] Plan for database maintenance
- [ ] Schedule backup verification
- [ ] Plan for scaling (if needed)
- [ ] Document support procedures

## Security Hardening

### 14. Additional Security
- [ ] Implement rate limiting on login
- [ ] Add CAPTCHA to login form (optional)
- [ ] Set up intrusion detection
- [ ] Configure fail2ban or similar
- [ ] Review and minimize server information disclosure
- [ ] Implement Content Security Policy headers
- [ ] Regular security audits scheduled

## Compliance & Legal

### 15. Compliance
- [ ] Review data privacy requirements
- [ ] Implement GDPR compliance (if applicable)
- [ ] Review accessibility standards
- [ ] Document data retention policies
- [ ] Review terms of service
- [ ] Privacy policy in place

## Launch

### 16. Go-Live
- [ ] Final backup before launch
- [ ] Switch DNS to production
- [ ] Monitor for errors
- [ ] Verify all functionality
- [ ] Announce to users
- [ ] Monitor first 24 hours closely

### 17. Post-Launch
- [ ] Review error logs daily
- [ ] Monitor performance metrics
- [ ] Gather user feedback
- [ ] Address any issues promptly
- [ ] Document lessons learned

## Ongoing Maintenance

### 18. Regular Tasks
- [ ] Weekly: Review audit logs
- [ ] Weekly: Check error logs
- [ ] Monthly: Update software
- [ ] Monthly: Review user accounts
- [ ] Quarterly: Security audit
- [ ] Quarterly: Performance review
- [ ] Yearly: Disaster recovery test

## Rollback Plan

### 19. Emergency Procedures
- [ ] Document rollback procedure
- [ ] Test rollback process
- [ ] Keep previous version backup
- [ ] Document emergency contacts
- [ ] Plan for downtime communication

## Sign-Off

- [ ] Development team approval
- [ ] Security team approval
- [ ] Management approval
- [ ] User acceptance testing completed
- [ ] Documentation review completed

---

**Deployment Date**: ________________

**Deployed By**: ________________

**Approved By**: ________________

**Notes**:
_____________________________________________________________
_____________________________________________________________
_____________________________________________________________

