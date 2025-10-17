# CHWR - Cobourg Homeless Warming Room

A role-based login system web application with CRUD functionality for managing users and services.

## Features

- **Role-Based Access Control**: Five different user roles with specific permissions
  - Admin: Full system access
  - Management: Oversight and reporting
  - Service Provider: Client and service management
  - Outreach: Community engagement and referrals
  - Client: Personal information access

- **User Management**: Complete CRUD operations for user accounts
- **Authentication**: Secure login/logout with password hashing
- **Audit Logging**: Track all system activities
- **Responsive Design**: Works on desktop and mobile devices
- **AJAX-Powered**: Dynamic interactions without page reloads

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript
- **AJAX**: XMLHttpRequest for dynamic content

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/acesonder/CHWR.git
   cd CHWR
   ```

2. Configure database connection:
   - Edit `config/database.php` with your database credentials
   - Default settings:
     - Host: localhost
     - User: root
     - Password: (empty)
     - Database: chwr_db

3. Initialize the database:
   ```bash
   php database/setup.php
   ```

4. Set up web server:
   - Configure your web server (Apache/Nginx) to point to the project root
   - Ensure PHP is enabled
   - Enable `mod_rewrite` for Apache or equivalent for Nginx

5. Access the application:
   - Navigate to `http://localhost/` in your browser
   - Login with default credentials:
     - Username: `admin`
     - Password: `admin123`

## File Structure

```
CHWR/
├── api/                    # API endpoints
│   └── users.php          # User CRUD API
├── config/                 # Configuration files
│   └── database.php       # Database configuration
├── database/              # Database files
│   ├── schema.sql         # Database schema
│   └── setup.php          # Database initialization script
├── includes/              # PHP includes
│   ├── auth.php           # Authentication functions
│   ├── header.php         # Page header template
│   └── footer.php         # Page footer template
├── public/                # Public assets
│   ├── css/
│   │   └── styles.css     # Main stylesheet
│   └── js/
│       └── main.js        # Main JavaScript file
├── index.php              # Entry point
├── login.php              # Login page
├── logout.php             # Logout handler
├── dashboard.php          # Dashboard
├── users.php              # User management page
├── audit-log.php          # Audit log viewer
└── README.md              # This file
```

## User Roles & Permissions

### Admin
- Full system access
- User management (create, read, update, delete)
- Audit log access
- System configuration

### Management
- User viewing and limited management
- Reports and analytics
- Service oversight

### Service Provider
- Client record management
- Service documentation
- Report access

### Outreach
- Community engagement logging
- Client referrals
- Field reporting

### Client
- View personal information
- View service history
- Update profile

## Security Features

- Password hashing with bcrypt
- Session management
- SQL injection prevention with prepared statements
- XSS protection with output sanitization
- CSRF protection ready
- Role-based access control
- Audit logging for all actions

## Development

To contribute or customize:

1. Follow the existing code structure
2. Test all changes thoroughly
3. Maintain security best practices
4. Update documentation as needed

## Database Schema

### Tables

- **roles**: User role definitions
- **users**: User accounts and credentials
- **audit_log**: System activity tracking

## Support

For issues, questions, or contributions, please open an issue on GitHub.

## License

This project is licensed under the MIT License.

## Credits

Developed for Cobourg Homeless Warming Room (CHWR).
