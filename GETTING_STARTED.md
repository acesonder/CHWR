# CHWR - Getting Started Guide

Welcome to the Cobourg Homeless Warming Room web application! This guide will help you get started quickly.

## 🚀 Quick Start

### For Local Development (5 minutes)

1. **Install XAMPP** (if not already installed)
   - Download from: https://www.apachefriends.org/
   - Start Apache and MySQL

2. **Clone the Repository**
   ```bash
   cd C:/xampp/htdocs/  # or your XAMPP directory
   git clone https://github.com/acesonder/CHWR.git
   cd CHWR
   ```

3. **Setup Database**
   - Open http://localhost/phpmyadmin
   - Create database: `chwr_db`
   - Import: `sql/schema.sql`

4. **Configure Connection**
   ```bash
   cp config/database.example.php config/database.php
   ```
   Edit `database.php` if needed (default XAMPP settings should work)

5. **Open Application**
   - Navigate to: http://localhost/CHWR
   - Click "Register" to create your first account

**That's it!** You now have a fully functional CHWR instance.

---

## 📱 What You Can Do Right Now

### As a Client (Default Role)

✅ **Register an Account**
- Your username is auto-generated for privacy
- Example: JOHSMI01151990 (First 3 letters of first name + first 3 of last name + MMDDYYYY)
- Set a security question for password recovery

✅ **Complete Your Assessment**
- 12 comprehensive sections covering all needs
- All questions are optional
- Auto-saves every 2 seconds
- Track progress with visual indicators
- Traffic light scoring (Green/Yellow/Red)

✅ **View Your Dashboard**
- See upcoming appointments
- Check pending tasks
- View messages
- Monitor assessment progress

✅ **Customize Your Experience**
- Toggle between light/dark themes
- Set communication preferences
- Update profile information

### As Admin/Staff (After Creating Account)

You'll need to manually update the database to change roles:

```sql
UPDATE users SET role = 'Administrator' WHERE user_id = 1;
-- or
UPDATE users SET role = 'Outreach Worker' WHERE user_id = 1;
-- or
UPDATE users SET role = 'Service Provider' WHERE user_id = 1;
```

Then logout and login again to see the role-specific dashboard.

---

## 🎯 Key Features Implemented

### ✅ Authentication & Security
- Secure registration with auto-generated usernames
- Password hashing with bcrypt
- Security question-based password recovery
- "Remember Me" (4-day persistence)
- CSRF protection
- SQL injection prevention
- XSS protection
- Activity logging

### ✅ Assessment System
**12 Complete Sections:**
- **A**: Consent & Preferences (5 questions)
- **B**: Mental Health, Safety, Crisis (6 questions) 🔴 Critical flags
- **C**: Housing & Shelter (5 questions)
- **D**: Medical, Dental, Vision (6 questions)
- **E**: Counseling / Support (4 questions)
- **F**: Substance Use (5 questions)
- **G**: Legal Issues (4 questions)
- **H**: Income, Benefits, ID (5 questions)
- **I**: Family & Social Network (4 questions)
- **J**: Employment & Education (5 questions)
- **K**: Life Skills & Daily Living (3 questions)
- **L**: Recovery Planning (4 questions)

**Features:**
- Auto-save (every 2 seconds of inactivity)
- Progress tracking
- Traffic light scoring
- Section completion status
- Overall assessment submission
- Auto-generated tasks from critical responses

### ✅ User Experience
- Mobile-first responsive design
- Dark/light theme toggle
- Keyboard navigation support
- Screen reader compatible
- Progress indicators
- Visual feedback for all actions
- Professional, compassionate interface

### ✅ Multi-Role Support
- **Client**: Self-service assessment and tracking
- **Outreach Worker**: Client management (dashboard ready)
- **Service Provider**: Referral management (dashboard ready)
- **Administrator**: System management (dashboard ready)

---

## 📚 Essential Documentation

Before diving deeper, check out:

- **[INSTALL.md](INSTALL.md)** - Detailed installation for local and production
- **[SECURITY.md](SECURITY.md)** - Security best practices and guidelines
- **[API.md](API.md)** - Complete API endpoint documentation
- **[CONTRIBUTING.md](CONTRIBUTING.md)** - How to contribute to the project

---

## 🔐 Default Security Settings

### Passwords
- Minimum 8 characters
- Hashed with bcrypt
- Never stored in plain text

### Sessions
- 2-hour timeout (configurable)
- Secure session handling
- Automatic cleanup

### Remember Me
- 4-day expiration (configurable)
- Secure token-based
- Revoked on logout

### File Uploads
- 5MB maximum size
- Type restrictions enforced
- No PHP execution in upload directories

---

## 🛠️ Configuration Options

Edit `config/config.php` to customize:

```php
'app_name' => 'Your Organization Name',
'base_url' => 'https://your-domain.com',
'session_lifetime' => 7200,  // 2 hours
'remember_me_duration' => 345600,  // 4 days
'password_min_length' => 8,
'autosave_delay' => 2000,  // 2 seconds
```

---

## 🧪 Testing the Application

### Test User Flow
1. **Register** → Note your generated username
2. **Login** → Test remember me feature
3. **Start Assessment** → Fill out Section B (Mental Health)
4. **Auto-save** → Type something and wait 2 seconds
5. **Mark Complete** → Complete a section
6. **View Dashboard** → See updated progress
7. **Toggle Theme** → Click profile → Toggle Theme
8. **Logout** → Test password recovery

### Test Different Roles
Create multiple accounts and promote them to different roles via SQL:

```sql
-- Make user an admin
UPDATE users SET role = 'Administrator' WHERE username = 'JOHSMI01151990';

-- Make user an outreach worker
UPDATE users SET role = 'Outreach Worker' WHERE username = 'JAMDOE01011985';
```

---

## 📊 Database Overview

### Key Tables
- `users` - User accounts with roles
- `assessments` - Client intake assessments
- `assessment_sections` - Section progress and scores
- `assessment_responses` - Individual question responses
- `tasks` - Auto-generated and manual tasks
- `messages` - Communication threads
- `appointments` - Scheduled meetings
- `referrals` - Service referrals
- `case_notes` - Case management notes
- `activity_logs` - Audit trail

### Sample Queries

**View all registered users:**
```sql
SELECT user_id, username, CONCAT(first_name, ' ', last_name) as name, 
       role, created_at 
FROM users 
ORDER BY created_at DESC;
```

**Check assessment progress:**
```sql
SELECT u.first_name, u.last_name, a.status, a.overall_score, 
       a.traffic_light, a.completed_at
FROM users u
JOIN assessments a ON u.user_id = a.user_id
WHERE u.role = 'Client';
```

**View assessment responses:**
```sql
SELECT ar.question_id, ar.question_text, ar.response_value
FROM assessment_responses ar
WHERE ar.assessment_id = 1
ORDER BY ar.section_code, ar.created_at;
```

---

## 🚧 What's Coming Next

The foundation is complete. Future development will add:

### Phase 2 (Planned)
- Real-time messaging between clients and staff
- Task management with notifications
- Appointment scheduling with calendar
- Service referral engine
- Case notes and file attachments

### Phase 3 (Planned)
- Admin analytics dashboard
- Report generation
- Email/SMS notifications
- Advanced search and filtering
- Data export capabilities

### Phase 4 (Planned)
- Mobile app companion
- QR code check-ins
- Offline sync
- Voice-to-text assessments
- Multi-language support

---

## ❓ Common Questions

### Q: How do I change the app name?
**A:** Edit `config/config.php` and update `app_name`

### Q: Can I add more security questions?
**A:** Yes! Edit the dropdown in `index.php` around line 200

### Q: How do I add custom assessment questions?
**A:** Edit `views/client/assessment_section.php` in the switch statement

### Q: Can I change the traffic light thresholds?
**A:** Yes! Edit `config/config.php` under `traffic_light` settings

### Q: How do I backup the database?
**A:** In phpMyAdmin, select database → Export → Go
Or via command line: `mysqldump -u root -p chwr_db > backup.sql`

### Q: Can I use PostgreSQL instead of MySQL?
**A:** You'll need to modify the PDO connection and potentially some SQL queries

### Q: Is this HIPAA compliant?
**A:** Additional measures would be needed. See SECURITY.md for healthcare considerations

---

## 📞 Getting Help

- **Documentation Issues**: Check the docs in the repository
- **Bug Reports**: Open an issue on GitHub
- **Feature Requests**: Open an issue with "Enhancement" label
- **Security Concerns**: Email security@chwr.org (do not open public issue)
- **General Questions**: Use GitHub Discussions (if enabled)

---

## 🤝 Contributing

We welcome contributions! See [CONTRIBUTING.md](CONTRIBUTING.md) for:
- Code style guidelines
- Pull request process
- Testing requirements
- Documentation standards

---

## 📜 License

This project is licensed under the MIT License - see [LICENSE](LICENSE) file for details.

---

## 🙏 Acknowledgments

This platform is built to support the vital work of the Cobourg Homeless Warming Room in serving our community's most vulnerable members. Thank you to all contributors and supporters!

---

**Need more help?** Check the full documentation or open an issue on GitHub!
