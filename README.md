# CHWR - Cobourg Homeless Warming Room

A comprehensive web-based platform for supporting individuals experiencing homelessness through smart assessments, service connections, and case management.

## Features

- **Role-Based Access Control**: Client, Outreach Worker, Service Provider, and Administrator roles
- **Smart Assessment System**: 12-section intake form with auto-save and traffic light scoring
- **Messaging System**: Direct communication between clients and support staff
- **Task Management**: Auto-generated and manually assigned tasks with priority levels
- **Appointment Scheduling**: Calendar integration with reminders
- **Service Referrals**: Automated matching and referral tracking
- **Case Management**: Comprehensive notes, progress tracking, and reporting
- **Mobile-First Design**: Responsive interface for all devices
- **Security**: Password hashing, CSRF protection, session management

## Tech Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **AJAX**: Fetch API
- **Security**: bcrypt password hashing, prepared statements

## Installation

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx) or XAMPP for local development

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone https://github.com/acesonder/CHWR.git
   cd CHWR
   ```

2. **Database Setup**
   - Create a new MySQL database named `chwr_db`
   - Import the schema:
     ```bash
     mysql -u root -p chwr_db < sql/schema.sql
     ```

3. **Configure Database Connection**
   - Copy the example config file:
     ```bash
     cp config/database.example.php config/database.php
     ```
   - Edit `config/database.php` with your database credentials

4. **Set Permissions**
   ```bash
   chmod 755 uploads temp cache sessions backups
   ```

5. **Access the Application**
   - For XAMPP: Place in `htdocs` folder and visit `http://localhost/CHWR`
   - For production: Configure your web server to point to the project directory

## Directory Structure

```
CHWR/
├── api/                  # API endpoints
│   ├── auth/            # Authentication APIs
│   ├── user/            # User management APIs
│   └── assessment/      # Assessment APIs
├── assets/              # Static assets
│   ├── css/            # Stylesheets
│   ├── js/             # JavaScript files
│   ├── images/         # Images
│   └── fonts/          # Custom fonts
├── config/              # Configuration files
├── includes/            # PHP classes and utilities
├── sql/                 # Database schemas and migrations
├── views/               # Role-specific views
│   ├── client/         # Client dashboard and pages
│   ├── admin/          # Admin dashboard
│   ├── staff/          # Outreach worker views
│   └── provider/       # Service provider views
├── uploads/             # User uploads
├── temp/                # Temporary files
├── cache/               # Cache directory
├── sessions/            # PHP sessions
└── backups/             # Database backups
```

## Default Security Questions

- What is your mother's maiden name?
- What was the name of your first pet?
- What city were you born in?
- What is your favorite color?
- What was your childhood nickname?

## Assessment Sections

The intake assessment consists of 12 sections:

- **A**: Consent & Preferences
- **B**: Mental Health, Safety, Crisis
- **C**: Housing & Shelter
- **D**: Medical, Dental, Vision
- **E**: Counseling / Support
- **F**: Substance Use
- **G**: Legal Issues
- **H**: Income, Benefits, ID
- **I**: Family & Social Network
- **J**: Employment & Education
- **K**: Life Skills & Daily Living
- **L**: Recovery Planning

## Traffic Light Scoring

- 🟢 **Green**: Low Acuity (0-33%)
- 🟡 **Yellow**: Moderate Acuity (34-66%)
- 🔴 **Red**: High/Vital Acuity (67-100%)

## User Roles

1. **Client**: Main users seeking help and completing assessments
2. **Outreach Worker**: Staff assigned to support clients
3. **Service Provider**: Organizations providing services
4. **Administrator**: Full system access and analytics

## Security Features

- Auto-generated usernames for privacy
- bcrypt password hashing
- Security question recovery
- Session management with timeout
- "Remember Me" cookie-based login (4 days)
- CSRF token protection
- SQL injection prevention with prepared statements
- Activity logging

## Contributing

This is a community-focused project. Contributions are welcome!

## License

This project is developed for the Cobourg Homeless Warming Room.

## Support

For issues or questions, please contact the development team or create an issue in the repository.

## Acknowledgments

Developed to support the vital work of the Cobourg Homeless Warming Room in serving our community's most vulnerable members.
