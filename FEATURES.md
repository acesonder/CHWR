# CHWR Features Documentation

## Overview

The CHWR (Cobourg Homeless Warming Room) application is a comprehensive web-based system with role-based access control, designed to manage users and services efficiently.

## Core Features

### 1. Authentication System

#### Login
- Secure username/password authentication
- Password hashing using bcrypt (PHP's `password_hash()`)
- Session-based authentication
- Automatic redirection based on login status
- Login activity tracking in audit log

#### Logout
- Secure session destruction
- Automatic redirection to login page
- Logout activity logging

#### Session Management
- Persistent sessions across page loads
- Automatic session timeout (server-configured)
- Session hijacking prevention

### 2. Role-Based Access Control (RBAC)

The system implements five distinct user roles, each with specific permissions:

#### Admin Role
**Full System Access**
- User Management: Create, Read, Update, Delete all users
- Audit Log: View all system activities
- System Configuration: Future expansion capability
- Reports: Access to all reports and analytics
- Dashboard: View comprehensive statistics

**Restrictions**: None - full access to all features

#### Management Role
**Oversight and Reporting**
- User Management: View all users, limited editing
- Reports: Access operational reports
- Service Oversight: Monitor service delivery
- Dashboard: View statistics for active users and services

**Restrictions**: Cannot delete users, cannot access audit log

#### Service Provider Role
**Client and Service Management**
- Client Records: Manage client information (future feature)
- Service Documentation: Record services provided (future feature)
- Reports: Access client service history
- Dashboard: Role-specific information

**Restrictions**: Cannot access user management, no audit log access

#### Outreach Role
**Community Engagement**
- Outreach Activities: Log community engagement (future feature)
- Client Referrals: Refer clients to services (future feature)
- Field Reports: Submit community reports (future feature)
- Dashboard: Role-specific information

**Restrictions**: Cannot access user management, no audit log access

#### Client Role
**Personal Information Access**
- Personal Records: View own information (future feature)
- Service History: View received services (future feature)
- Profile Updates: Update contact information (future feature)
- Dashboard: Personal information only

**Restrictions**: Most restricted - cannot access other users' data or admin features

### 3. User Management (CRUD Operations)

#### Create User (Admin Only)
- Add new users with complete information
- Required fields: Username, Email, Password, First Name, Last Name, Role
- Optional: Active status
- Validation:
  - Unique username and email
  - Valid email format
  - Password strength (can be enhanced)
- Automatic password hashing
- AJAX-based form submission
- Real-time validation
- Success/error notifications

#### Read Users (Admin, Management)
- View all users in tabular format
- Display information:
  - Username
  - Full name (First + Last)
  - Email address
  - Role with color-coded badges
  - Active status
  - Creation date
- Search functionality across all fields
- Responsive table design
- Pagination support (future enhancement)

#### Update User (Admin Only)
- Edit existing user information
- Pre-populated form with current data
- Optional password change
- Same validation as create
- AJAX-based submission
- Activity logged in audit log

#### Delete User (Admin Only)
- Remove users from system
- Confirmation prompt before deletion
- Protection: Cannot delete default admin user
- Permanent deletion from database
- Activity logged in audit log

### 4. Dashboard

#### Role-Specific Content
Each role sees customized dashboard content:

- **Statistics Cards** (Admin, Management):
  - Total active users
  - Total clients
  - Total service providers
  - Total outreach workers

- **Role Description**: Personalized welcome with role capabilities
- **Quick Links**: Easy access to frequently used features
- **Future Expansion**: Placeholder for additional widgets

### 5. Audit Log (Admin Only)

#### Activity Tracking
- Comprehensive logging of all system activities
- Logged events:
  - User login
  - User logout
  - User creation
  - User updates
  - User deletion
  - Future: All CRUD operations

#### Audit Log Display
- Chronological listing of activities
- Information shown:
  - Date and time (with seconds)
  - Username who performed action
  - Action type
  - Detailed description
  - IP address
- Pagination (50 records per page)
- Searchable and filterable (future enhancement)

### 6. User Interface

#### Design Features
- **Responsive Design**: Works on desktop, tablet, and mobile
- **Modern Aesthetics**: Clean, professional appearance
- **Color-Coded Roles**: Visual distinction of user roles
- **Intuitive Navigation**: Easy-to-use menu system
- **Consistent Layout**: Uniform design across all pages

#### CSS Features
- Custom CSS framework (no external dependencies)
- Gradient backgrounds
- Card-based layouts
- Hover effects and transitions
- Form styling with focus states
- Modal dialogs
- Alert notifications
- Loading spinners
- Responsive grid system

#### JavaScript/AJAX Features
- **Dynamic Content Loading**: No page reloads for CRUD operations
- **Form Validation**: Client-side validation before submission
- **Modal Dialogs**: Smooth opening/closing animations
- **Search Functionality**: Real-time table filtering
- **Alert System**: Success/error notifications
- **AJAX Requests**: XMLHttpRequest for API calls
- **Error Handling**: Graceful error messages

### 7. Security Features

#### Input Validation
- Server-side validation for all inputs
- Client-side validation for better UX
- Email format validation
- Required field checking
- Data type validation

#### SQL Injection Prevention
- Prepared statements for all database queries
- Parameter binding with type checking
- No direct SQL concatenation

#### Cross-Site Scripting (XSS) Prevention
- Output sanitization using `htmlspecialchars()`
- Input sanitization on all user data
- Escaping special characters

#### Password Security
- Bcrypt hashing algorithm
- Automatic salt generation
- One-way encryption (cannot be reversed)
- Secure password verification

#### Session Security
- Secure session configuration
- Session ID regeneration on login
- HTTP-only session cookies
- Session timeout support

#### Access Control
- Role-based authorization checks
- Page-level access control
- Feature-level permission checks
- Redirect to login for unauthenticated users
- Deny access for unauthorized roles

### 8. Database Design

#### Normalized Schema
- Three main tables: roles, users, audit_log
- Foreign key relationships
- Indexed fields for performance
- Timestamp tracking (created_at, updated_at)

#### Data Integrity
- Primary keys on all tables
- Unique constraints on username/email
- Foreign key constraints
- NOT NULL constraints on required fields
- Default values where appropriate

### 9. API Endpoints

#### RESTful Design
- `/api/users.php` - User management API
- Actions: get, create, update, delete
- JSON responses
- HTTP status codes
- Error handling

#### API Security
- Session-based authentication required
- Role-based authorization
- Input validation
- SQL injection prevention

## Future Enhancements

### Planned Features
1. **Client Management**
   - Full CRUD for client records
   - Service history tracking
   - Document uploads

2. **Service Provider Features**
   - Service logging
   - Appointment scheduling
   - Client notes

3. **Outreach Management**
   - Activity logging
   - Community engagement tracking
   - Referral system

4. **Reporting System**
   - Custom reports
   - Data export (CSV, PDF)
   - Analytics dashboard

5. **Advanced Security**
   - Two-factor authentication
   - Password policies
   - Account lockout after failed attempts
   - Password reset via email

6. **Communication**
   - Email notifications
   - In-app messaging
   - Announcements system

7. **File Management**
   - Document uploads
   - File sharing
   - Storage management

8. **Advanced Search**
   - Full-text search
   - Advanced filters
   - Saved searches

## Technical Specifications

### System Requirements
- **PHP**: 7.4 or higher
- **Database**: MySQL 5.7+ or MariaDB 10.3+
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **Browser**: Modern browsers (Chrome, Firefox, Safari, Edge)

### Performance
- Optimized queries with indexes
- AJAX for reduced server load
- CSS/JS minification ready
- Caching support ready
- Image optimization ready

### Scalability
- Modular code structure
- Separation of concerns
- API-first design for future mobile apps
- Database optimization ready
- Load balancing ready

### Maintainability
- Clean, commented code
- Consistent naming conventions
- Modular file structure
- Reusable functions
- Documentation

## Best Practices Implemented

1. **Security First**: All inputs validated and sanitized
2. **DRY Principle**: Reusable functions and components
3. **Separation of Concerns**: Logic, presentation, and data separated
4. **Progressive Enhancement**: Works without JavaScript (basic features)
5. **Responsive Design**: Mobile-first approach
6. **Error Handling**: Graceful error messages
7. **Logging**: Comprehensive audit trail
8. **Documentation**: Inline comments and external docs

## Conclusion

The CHWR application provides a solid foundation for managing users with role-based access control. It implements industry-standard security practices and provides a modern, responsive user interface. The modular design allows for easy expansion and customization to meet evolving requirements.
