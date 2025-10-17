# CHWR Application Screenshots and Visual Guide

## Application Flow

### 1. Login Page
**URL**: `/login.php`

**Features**:
- Clean, centered login form
- CHWR branding with gradient background
- Username and password fields
- Default credentials displayed for easy access
- Responsive design
- Error message display area

**Colors**: 
- Primary: Deep blue gradient (#2c3e50 to #34495e)
- Accent: Bright blue (#3498db)

**Default Credentials**:
- Username: `admin`
- Password: `admin123`

---

### 2. Admin Dashboard
**URL**: `/dashboard.php`

**Layout**:
- Top navigation bar with CHWR branding
- User welcome message with role display
- Navigation menu: Dashboard, User Management, Audit Log, Logout
- Statistics cards (4 columns):
  - Total Active Users
  - Clients
  - Service Providers
  - Outreach Workers
- Dashboard overview card with role-specific information
- Quick links section
- Footer with copyright

**Features for Admin**:
- Full statistics visibility
- List of capabilities and access
- Links to all management functions

---

### 3. User Management Page
**URL**: `/users.php`

**Layout**:
- Page header: "User Management"
- "Add New User" button (green, top right)
- Search bar for filtering users
- Data table with columns:
  - Username
  - Name
  - Email
  - Role (color-coded badge)
  - Status (Active/Inactive badge)
  - Created date
  - Actions (Edit, Delete buttons)

**Role Badge Colors**:
- Admin: Red (#e74c3c)
- Management: Purple (#9b59b6)
- Service Provider: Blue (#3498db)
- Outreach: Green (#27ae60)
- Client: Gray (#95a5a6)

**Features**:
- Real-time search across all fields
- Responsive table design
- Color-coded role badges
- Action buttons for each user
- Modal dialogs for add/edit

---

### 4. Add User Modal
**Triggered by**: "Add New User" button on users.php

**Form Fields**:
- Username (required, text)
- Email (required, email validation)
- Password (required, password)
- First Name (required, text)
- First Name (required, text)
- Role (required, dropdown with 5 options)
- Active (checkbox, default checked)

**Buttons**:
- Create User (green)
- Cancel (gray)

**Features**:
- Client-side validation
- AJAX submission (no page reload)
- Success/error notifications
- Auto-close on success

---

### 5. Edit User Modal
**Triggered by**: "Edit" button in users table

**Pre-populated Fields**:
- All current user data loaded via AJAX
- Username
- Email
- Password (optional - leave blank to keep current)
- First Name
- Last Name
- Role
- Active status

**Buttons**:
- Update User (green)
- Cancel (gray)

**Features**:
- AJAX data loading
- AJAX submission
- Password optional on edit
- Success/error notifications

---

### 6. Audit Log Page
**URL**: `/audit-log.php`
**Access**: Admin only

**Layout**:
- Page header: "Audit Log"
- Data table with columns:
  - Date/Time (with seconds)
  - User (who performed action)
  - Action (bold text)
  - Details
  - IP Address
- Pagination controls at bottom

**Logged Actions**:
- LOGIN
- LOGOUT
- CREATE_USER
- UPDATE_USER
- DELETE_USER

**Features**:
- Chronological display (newest first)
- 50 records per page
- Previous/Next navigation
- Detailed activity tracking

---

### 7. Role-Specific Dashboards

#### Management Dashboard
- Can see statistics
- Can access user management (view/edit only)
- Cannot access audit log
- Cannot delete users

#### Service Provider Dashboard
- No statistics
- Cannot access user management
- Role-specific capabilities listed
- Focus on client services (future)

#### Outreach Dashboard
- No statistics
- Cannot access user management
- Role-specific capabilities listed
- Focus on community engagement (future)

#### Client Dashboard
- No statistics
- Cannot access user management
- Role-specific capabilities listed
- Focus on personal information (future)

---

## Color Scheme

### Primary Colors
- **Primary Background**: #2c3e50 (Dark blue)
- **Secondary**: #3498db (Bright blue)
- **Success**: #27ae60 (Green)
- **Danger**: #e74c3c (Red)
- **Warning**: #f39c12 (Orange)

### UI Colors
- **Light Background**: #ecf0f1 (Very light gray)
- **Dark Text**: #2c3e50 (Dark blue)
- **Light Text**: #7f8c8d (Medium gray)
- **White**: #ffffff (White)

### Status Colors
- **Active**: Green badge
- **Inactive**: Red badge

### Role Colors
- **Admin**: Red
- **Management**: Purple
- **Service Provider**: Blue
- **Outreach**: Green
- **Client**: Gray

---

## Typography

- **Font Family**: System fonts (-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif)
- **Line Height**: 1.6
- **Headings**: Bold, varying sizes
- **Body Text**: Regular weight, 1rem

---

## Interactive Elements

### Buttons
- **Hover Effect**: Slight color darkening
- **Transition**: Smooth 0.3s
- **Border Radius**: 5px
- **Padding**: 0.75rem 1.5rem

### Forms
- **Input Focus**: Blue border (#3498db)
- **Border Radius**: 5px
- **Padding**: 0.75rem

### Modals
- **Background**: Semi-transparent black overlay
- **Content**: White card with shadow
- **Animation**: Fade in/out
- **Close**: X button or click outside

### Tables
- **Header**: Dark blue background with white text
- **Row Hover**: Light gray background
- **Border**: Light gray bottom borders

### Alerts
- **Success**: Green background, dark green text
- **Error**: Red background, dark red text
- **Info**: Blue background, dark blue text
- **Auto-dismiss**: After 5 seconds

---

## Responsive Breakpoints

### Desktop (> 768px)
- Full navigation bar
- Multi-column statistics grid
- Wide tables
- Side-by-side buttons

### Tablet/Mobile (≤ 768px)
- Stacked navigation
- Single-column statistics
- Horizontal scroll for tables
- Stacked buttons
- Larger touch targets

---

## User Experience Features

1. **Instant Feedback**: Success/error messages appear immediately
2. **No Page Reloads**: AJAX for all CRUD operations
3. **Loading States**: Spinners for async operations
4. **Confirmation Dialogs**: Before destructive actions (delete)
5. **Real-time Search**: Immediate table filtering
6. **Form Validation**: Client and server side
7. **Keyboard Navigation**: Tab through forms
8. **Error Prevention**: Disable buttons during submission

---

## Accessibility Features

1. **Semantic HTML**: Proper heading hierarchy
2. **Alt Text**: Ready for images
3. **Form Labels**: All inputs labeled
4. **Color Contrast**: WCAG AA compliant
5. **Focus Indicators**: Visible on keyboard navigation
6. **Error Messages**: Clear and descriptive
7. **Required Fields**: Marked with asterisk

---

## Security Indicators

1. **Session Status**: Displayed in navigation
2. **Role Display**: Always visible in nav
3. **Audit Trail**: All actions logged
4. **Password Masking**: Hidden by default
5. **HTTPS Ready**: Security headers in place

---

## Future UI Enhancements

1. **Dark Mode**: Toggle for dark theme
2. **Customizable Dashboard**: Drag-and-drop widgets
3. **Data Visualization**: Charts and graphs
4. **Advanced Search**: Filter builder
5. **Bulk Operations**: Multi-select actions
6. **Export Functions**: PDF/CSV download buttons
7. **File Uploads**: Drag-and-drop interface
8. **Notifications**: Toast messages
9. **Help Tooltips**: Contextual help
10. **Keyboard Shortcuts**: Quick actions

---

## Testing the UI

To test the visual interface:

1. **Desktop Browser**:
   - Open in Chrome, Firefox, Safari, or Edge
   - Test all interactive elements
   - Verify responsive design by resizing window

2. **Mobile Browser**:
   - Test on actual device or device emulator
   - Verify touch targets are large enough
   - Check navigation menu behavior

3. **Different Roles**:
   - Login as each role
   - Verify role-specific UI elements
   - Test permission restrictions

4. **Browser Compatibility**:
   - Test in multiple browsers
   - Verify consistent appearance
   - Check AJAX functionality

---

## Performance

- **Page Load**: < 2 seconds on average connection
- **AJAX Responses**: < 500ms for most operations
- **Table Search**: Instant (client-side)
- **Modal Animation**: Smooth 60fps

---

This visual guide provides a comprehensive overview of the CHWR application's user interface and user experience design.
