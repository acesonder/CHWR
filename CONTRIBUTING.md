# Contributing to CHWR

Thank you for your interest in contributing to the Cobourg Homeless Warming Room web application! This project aims to support vulnerable community members, and your contributions make a real difference.

## Code of Conduct

### Our Pledge
We are committed to providing a welcoming and inclusive environment for all contributors, regardless of background or identity.

### Our Standards
- Be respectful and considerate
- Focus on what's best for the community we serve
- Show empathy toward other contributors
- Accept constructive criticism gracefully
- Respect privacy and confidentiality

## How to Contribute

### Reporting Bugs

Before creating a bug report:
1. Check existing issues to avoid duplicates
2. Use the latest version of the codebase
3. Verify the bug is reproducible

When creating a bug report, include:
- Clear, descriptive title
- Steps to reproduce
- Expected vs actual behavior
- Screenshots (if applicable)
- Environment details (OS, PHP version, browser)

### Suggesting Enhancements

Enhancement suggestions are welcome! Include:
- Clear description of the feature
- Use case / problem it solves
- Proposed implementation (if you have ideas)
- Any potential drawbacks or concerns

### Pull Requests

#### Before You Start
1. Check existing PRs to avoid duplication
2. Open an issue to discuss major changes first
3. Fork the repository
4. Create a feature branch from `main`

#### Development Process
1. **Clone your fork:**
   ```bash
   git clone https://github.com/YOUR_USERNAME/CHWR.git
   cd CHWR
   git remote add upstream https://github.com/acesonder/CHWR.git
   ```

2. **Create a branch:**
   ```bash
   git checkout -b feature/your-feature-name
   # or
   git checkout -b fix/bug-description
   ```

3. **Make your changes:**
   - Follow existing code style
   - Write clear, concise comments
   - Test your changes thoroughly
   - Update documentation as needed

4. **Commit your changes:**
   ```bash
   git add .
   git commit -m "Brief description of changes"
   ```
   
   Good commit messages:
   - `Add user profile editing feature`
   - `Fix assessment auto-save on slow connections`
   - `Update security documentation`

5. **Push to your fork:**
   ```bash
   git push origin feature/your-feature-name
   ```

6. **Create Pull Request:**
   - Go to the original repository
   - Click "New Pull Request"
   - Select your branch
   - Fill out the PR template

#### Pull Request Guidelines
- **Keep PRs focused** - One feature or fix per PR
- **Write clear descriptions** - Explain what and why
- **Include screenshots** - For UI changes
- **Update tests** - If test infrastructure exists
- **Update documentation** - README, API docs, etc.
- **Follow code style** - Match existing patterns

## Code Style Guidelines

### PHP
```php
// Use PSR-12 coding standards
// Class names in PascalCase
class UserManager {
    // Method names in camelCase
    public function getUserById($userId) {
        // Variables in camelCase
        $userName = '';
        
        // Use prepared statements
        $stmt = $db->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$userId]);
        
        return $stmt->fetch();
    }
}

// Always escape output
echo htmlspecialchars($userInput);

// Clear comments for complex logic
// Calculate traffic light score based on response severity
$trafficLight = $this->calculateTrafficLight($responses);
```

### JavaScript
```javascript
// Use ES6+ features
// Function names in camelCase
async function fetchUserData(userId) {
    try {
        const response = await fetch(`/api/user/${userId}`);
        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error fetching user:', error);
        throw error;
    }
}

// Use meaningful variable names
const isUserLoggedIn = checkAuthStatus();

// Add comments for complex logic
// Debounce auto-save to prevent excessive API calls
const debouncedSave = debounce(saveSection, 2000);
```

### CSS
```css
/* Use CSS variables for theming */
:root {
    --primary-color: #2563eb;
    --text-primary: #1f2937;
}

/* BEM-like naming for complex components */
.feature-card {
    /* Base styles */
}

.feature-card__icon {
    /* Element styles */
}

.feature-card--highlighted {
    /* Modifier styles */
}

/* Mobile-first approach */
.container {
    padding: 1rem;
}

@media (min-width: 768px) {
    .container {
        padding: 2rem;
    }
}
```

### SQL
```sql
-- Clear table and column names
-- Use prepared statements (via PHP PDO)
-- Add indexes for performance
CREATE INDEX idx_user_id ON assessments(user_id);

-- Document complex queries
-- This query finds users with incomplete assessments older than 30 days
SELECT u.*, a.status
FROM users u
JOIN assessments a ON u.user_id = a.user_id
WHERE a.status != 'Completed'
  AND a.created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);
```

## File Organization

### Adding New Files

**PHP Classes:**
- Place in `/includes/`
- One class per file
- Name file same as class (e.g., `AssessmentManager.php`)

**API Endpoints:**
- Place in `/api/[category]/`
- RESTful naming (e.g., `create.php`, `update.php`)
- Return JSON responses

**Views:**
- Place in `/views/[role]/`
- Group related pages
- Include role-based security checks

**Assets:**
- CSS: `/assets/css/`
- JS: `/assets/js/`
- Images: `/assets/images/`

## Testing Guidelines

### Manual Testing Checklist
- [ ] Test on multiple browsers (Chrome, Firefox, Safari)
- [ ] Test responsive design (mobile, tablet, desktop)
- [ ] Test with different user roles
- [ ] Test error cases and edge cases
- [ ] Verify security (SQL injection, XSS)
- [ ] Check accessibility (keyboard navigation, screen readers)

### Testing User Flows
1. **Registration & Login**
   - Register new user
   - Verify username generation
   - Test login with correct/incorrect credentials
   - Test "Remember Me" feature
   - Test password recovery flow

2. **Assessment**
   - Start new assessment
   - Fill out sections
   - Verify auto-save
   - Test section completion
   - Submit full assessment

3. **Role-Specific Features**
   - Test each role's dashboard
   - Verify proper access restrictions
   - Test role-specific actions

## Documentation

### When to Update Documentation
- Adding new features → Update README.md
- Adding API endpoints → Update API.md
- Security changes → Update SECURITY.md
- Installation changes → Update INSTALL.md

### Documentation Style
- Clear, concise language
- Code examples where helpful
- Screenshots for UI features
- Keep formatting consistent

## Security Considerations

### Never Commit:
- Database credentials
- API keys or secrets
- Real user data
- Production configuration files

### Always:
- Use parameterized queries
- Validate and sanitize input
- Escape output
- Use HTTPS in production
- Log security events
- Follow principle of least privilege

### Reporting Security Issues
**Do not** open public issues for security vulnerabilities. Email: security@chwr.org (or designated contact)

## Community

### Getting Help
- Check existing documentation
- Search closed issues
- Ask in discussions (if enabled)
- Reach out to maintainers

### Recognition
Contributors will be recognized in:
- README.md contributors section
- Release notes
- Project documentation

## License

By contributing, you agree that your contributions will be licensed under the same license as the project.

## Questions?

Feel free to:
- Open an issue for questions
- Join community discussions
- Contact the maintainers directly

---

Thank you for contributing to CHWR and helping support our community's most vulnerable members! 🙏
