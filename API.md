# CHWR API Documentation

Base URL: `/api/`

All API endpoints return JSON responses in the format:
```json
{
    "success": true|false,
    "message": "Description",
    "data": {...}  // Optional
}
```

---

## Authentication Endpoints

### POST `/api/auth/register.php`
Register a new user account.

**Request Body (FormData):**
```
first_name: string (required)
last_name: string (required)
date_of_birth: date (required, format: YYYY-MM-DD)
email: string (optional)
phone: string (optional)
password: string (required, min 8 chars)
security_question: string (required)
security_answer: string (required)
```

**Response:**
```json
{
    "success": true,
    "username": "JOHSMI01151990",
    "user_id": 1
}
```

---

### POST `/api/auth/login.php`
Authenticate a user.

**Request Body (FormData):**
```
username: string (required)
password: string (required)
remember_me: boolean (optional)
```

**Response:**
```json
{
    "success": true,
    "user": {
        "user_id": 1,
        "username": "JOHSMI01151990",
        "first_name": "John",
        "last_name": "Smith",
        "role": "Client"
    }
}
```

**Session Variables Set:**
- `user_id`
- `username`
- `role`
- `first_name`
- `last_name`
- `theme`
- `logged_in`

---

### POST `/api/auth/recover_username.php`
Recover forgotten username.

**Request Body (FormData):**
```
first_name: string (required)
last_name: string (required)
date_of_birth: date (required, format: YYYY-MM-DD)
```

**Response:**
```json
{
    "success": true,
    "username": "JOHSMI01151990"
}
```

---

### POST `/api/auth/get_security_question.php`
Get security question for a username.

**Request Body (JSON):**
```json
{
    "username": "JOHSMI01151990"
}
```

**Response:**
```json
{
    "success": true,
    "question": "What is your mother's maiden name?"
}
```

---

### POST `/api/auth/verify_security_answer.php`
Verify security question answer.

**Request Body (JSON):**
```json
{
    "username": "JOHSMI01151990",
    "answer": "Jones"
}
```

**Response:**
```json
{
    "success": true,
    "user_id": 1
}
```

---

### POST `/api/auth/reset_password.php`
Reset password after security answer verification.

**Request Body (JSON):**
```json
{
    "user_id": 1,
    "new_password": "newpassword123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Password reset successfully"
}
```

---

### GET `/api/auth/logout.php`
Logout current user and redirect to index.

**No request body required.**

Destroys session and removes remember-me token.

---

## Assessment Endpoints

### POST `/api/assessment/save_section.php`
Auto-save assessment section responses.

**Authentication Required:** Yes (session)

**Request Body (FormData):**
```
assessment_id: integer (required)
section_code: string (required, A-L)
[question_id]: value (dynamic based on questions)
```

**Example:**
```
assessment_id=1
section_code=B
mental_health_concern=Moderate
safety_concern=Yes
```

**Response:**
```json
{
    "success": true,
    "message": "Responses saved"
}
```

**Notes:**
- Automatically called on input change (debounced 2 seconds)
- Multi-select values sent as arrays
- Creates or updates responses

---

### POST `/api/assessment/complete_section.php`
Mark a section as complete and calculate score.

**Authentication Required:** Yes (session)

**Request Body (JSON):**
```json
{
    "assessment_id": 1,
    "section_code": "B"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Section marked as complete"
}
```

**Side Effects:**
- Calculates section score
- Determines traffic light status
- Updates overall assessment score

---

### POST `/api/assessment/submit.php`
Submit completed assessment.

**Authentication Required:** Yes (session)

**Request Body (JSON):**
```json
{
    "assessment_id": 1
}
```

**Response:**
```json
{
    "success": true,
    "message": "Assessment submitted successfully"
}
```

**Side Effects:**
- Marks assessment as "Completed"
- Auto-generates tasks based on flagged sections
- Triggers notification to assigned worker (future)

---

## User Endpoints

### POST `/api/user/update_theme.php`
Update user's theme preference.

**Authentication Required:** Yes (session)

**Request Body (JSON):**
```json
{
    "theme": "dark"
}
```

**Valid Values:** `"light"`, `"dark"`

**Response:**
```json
{
    "success": true,
    "message": "Theme updated"
}
```

---

## Error Responses

All endpoints may return error responses:

### 401 Unauthorized
```json
{
    "success": false,
    "message": "Unauthorized"
}
```

### 403 Forbidden
```json
{
    "success": false,
    "message": "Forbidden"
}
```

### 405 Method Not Allowed
```json
{
    "success": false,
    "message": "Method not allowed"
}
```

### 400 Bad Request
```json
{
    "success": false,
    "message": "Missing required fields"
}
```

### 500 Internal Server Error
```json
{
    "success": false,
    "message": "An error occurred"
}
```

---

## Future Endpoints (Planned)

### Messages
- `GET /api/messages/list.php` - Get messages
- `POST /api/messages/send.php` - Send message
- `PUT /api/messages/mark_read.php` - Mark as read

### Tasks
- `GET /api/tasks/list.php` - Get tasks
- `POST /api/tasks/create.php` - Create task
- `PUT /api/tasks/update.php` - Update task status

### Appointments
- `GET /api/appointments/list.php` - Get appointments
- `POST /api/appointments/create.php` - Create appointment
- `PUT /api/appointments/update.php` - Update appointment

### Referrals
- `GET /api/referrals/list.php` - Get referrals
- `POST /api/referrals/create.php` - Create referral
- `PUT /api/referrals/update.php` - Update referral status

### Admin
- `GET /api/admin/users.php` - List all users
- `GET /api/admin/analytics.php` - System analytics
- `GET /api/admin/logs.php` - Activity logs

---

## Rate Limiting (Recommended)

Future implementation should include rate limiting:
- Login attempts: 5 per account per 15 minutes
- Registration: 3 per IP per hour
- API requests: 100 per user per minute

---

## CORS Headers

Currently configured for same-origin requests only. If building a separate frontend:

```php
header('Access-Control-Allow-Origin: https://your-frontend-domain.com');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');
```

---

## Testing API Endpoints

### Using cURL

**Register:**
```bash
curl -X POST http://localhost/CHWR/api/auth/register.php \
  -F "first_name=John" \
  -F "last_name=Smith" \
  -F "date_of_birth=1990-01-15" \
  -F "password=password123" \
  -F "security_question=What is your favorite color?" \
  -F "security_answer=Blue"
```

**Login:**
```bash
curl -X POST http://localhost/CHWR/api/auth/login.php \
  -F "username=JOHSMI01151990" \
  -F "password=password123" \
  -c cookies.txt
```

**Authenticated Request:**
```bash
curl -X POST http://localhost/CHWR/api/user/update_theme.php \
  -b cookies.txt \
  -H "Content-Type: application/json" \
  -d '{"theme":"dark"}'
```

### Using JavaScript Fetch

```javascript
// Login
const response = await fetch('/api/auth/login.php', {
    method: 'POST',
    body: new FormData(loginForm)
});
const data = await response.json();

// Authenticated request
const response = await fetch('/api/user/update_theme.php', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({ theme: 'dark' })
});
```

---

## Version History

- **v1.0.0** (Current) - Initial API implementation
  - Authentication endpoints
  - Assessment endpoints
  - User preferences

---

For questions or issues with the API, please create an issue on GitHub or contact the development team.
