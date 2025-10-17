<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Cobourg Homeless Warming Room - Smart support platform for individuals experiencing homelessness">
    <title>CHWR - Cobourg Homeless Warming Room</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="landing-page">
    <!-- Global Navigation Bar -->
    <nav class="navbar">
        <div class="container">
            <div class="nav-left">
                <a href="index.php" class="logo">
                    <i class="fas fa-heart"></i>
                    <span>CHWR</span>
                </a>
            </div>
            <div class="nav-right" id="navRight">
                <button class="btn btn-outline" onclick="openModal('loginModal')">Login</button>
                <button class="btn btn-primary" onclick="openModal('registerModal')">Register</button>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Welcome to CHWR</h1>
                <h2>Cobourg Homeless Warming Room</h2>
                <p class="tagline">A smart, compassionate platform connecting individuals with support services, assessments, and caring professionals.</p>
                <div class="hero-buttons">
                    <button class="btn btn-large btn-primary" onclick="openModal('registerModal')">
                        <i class="fas fa-user-plus"></i> Get Started
                    </button>
                    <button class="btn btn-large btn-outline" onclick="scrollToSection('features')">
                        <i class="fas fa-info-circle"></i> Learn More
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features">
        <div class="container">
            <h2 class="section-title">Our Services</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <h3>Smart Assessment</h3>
                    <p>Complete a comprehensive intake assessment at your own pace with auto-save functionality.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Connect with Support</h3>
                    <p>Direct messaging with outreach workers and service providers who care.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h3>Track Your Progress</h3>
                    <p>View tasks, appointments, and your journey toward stability.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>
                    <h3>Service Referrals</h3>
                    <p>Get connected to housing, healthcare, legal aid, and other vital services.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3>Appointments</h3>
                    <p>Schedule and manage appointments with reminders and notifications.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Mobile Friendly</h3>
                    <p>Access from any device - smartphone, tablet, or computer.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works">
        <div class="container">
            <h2 class="section-title">How It Works</h2>
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>Register</h3>
                    <p>Create your account with basic information. Your username is automatically generated for privacy.</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h3>Complete Assessment</h3>
                    <p>Fill out the intake form at your pace. All questions are optional and auto-saved.</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h3>Get Matched</h3>
                    <p>Based on your needs, you'll be connected with appropriate services and support.</p>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <h3>Track Progress</h3>
                    <p>View your tasks, appointments, and communicate with your support team.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Login</h2>
                <button class="close-modal" onclick="closeModal('loginModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="loginForm">
                    <div class="form-group">
                        <label for="loginUsername">Username</label>
                        <input type="text" id="loginUsername" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="loginPassword">Password</label>
                        <input type="password" id="loginPassword" name="password" required>
                    </div>
                    <div class="form-group checkbox-group">
                        <label>
                            <input type="checkbox" name="remember_me" id="rememberMe">
                            Remember me for 4 days
                        </label>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </div>
                    <div class="form-links">
                        <a href="#" onclick="switchModal('loginModal', 'forgotUsernameModal')">Forgot Username?</a>
                        <a href="#" onclick="switchModal('loginModal', 'forgotPasswordModal')">Forgot Password?</a>
                    </div>
                </form>
                <div id="loginMessage" class="message"></div>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div id="registerModal" class="modal">
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h2>Register</h2>
                <button class="close-modal" onclick="closeModal('registerModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="registerForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="regFirstName">First Name *</label>
                            <input type="text" id="regFirstName" name="first_name" required>
                        </div>
                        <div class="form-group">
                            <label for="regLastName">Last Name *</label>
                            <input type="text" id="regLastName" name="last_name" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="regDOB">Date of Birth *</label>
                        <input type="date" id="regDOB" name="date_of_birth" required>
                        <small>Your username will be auto-generated using your name and date of birth</small>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="regEmail">Email (Optional)</label>
                            <input type="email" id="regEmail" name="email">
                        </div>
                        <div class="form-group">
                            <label for="regPhone">Phone (Optional)</label>
                            <input type="tel" id="regPhone" name="phone">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="regPassword">Password *</label>
                        <input type="password" id="regPassword" name="password" required minlength="8">
                        <small>Minimum 8 characters</small>
                    </div>
                    <div class="form-group">
                        <label for="regConfirmPassword">Confirm Password *</label>
                        <input type="password" id="regConfirmPassword" name="confirm_password" required>
                    </div>
                    <div class="form-group">
                        <label for="securityQuestion">Security Question *</label>
                        <select id="securityQuestion" name="security_question" required>
                            <option value="">Select a question...</option>
                            <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
                            <option value="What was the name of your first pet?">What was the name of your first pet?</option>
                            <option value="What city were you born in?">What city were you born in?</option>
                            <option value="What is your favorite color?">What is your favorite color?</option>
                            <option value="What was your childhood nickname?">What was your childhood nickname?</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="securityAnswer">Security Answer *</label>
                        <input type="text" id="securityAnswer" name="security_answer" required>
                        <small>This will be used for password recovery</small>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Register</button>
                    </div>
                    <div class="form-links">
                        <a href="#" onclick="switchModal('registerModal', 'loginModal')">Already have an account? Login</a>
                    </div>
                </form>
                <div id="registerMessage" class="message"></div>
            </div>
        </div>
    </div>

    <!-- Forgot Username Modal -->
    <div id="forgotUsernameModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Recover Username</h2>
                <button class="close-modal" onclick="closeModal('forgotUsernameModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="forgotUsernameForm">
                    <p>Enter your information to recover your username:</p>
                    <div class="form-group">
                        <label for="forgotFirstName">First Name</label>
                        <input type="text" id="forgotFirstName" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="forgotLastName">Last Name</label>
                        <input type="text" id="forgotLastName" name="last_name" required>
                    </div>
                    <div class="form-group">
                        <label for="forgotDOB">Date of Birth</label>
                        <input type="date" id="forgotDOB" name="date_of_birth" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block">Recover Username</button>
                    </div>
                    <div class="form-links">
                        <a href="#" onclick="switchModal('forgotUsernameModal', 'loginModal')">Back to Login</a>
                    </div>
                </form>
                <div id="forgotUsernameMessage" class="message"></div>
            </div>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div id="forgotPasswordModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Reset Password</h2>
                <button class="close-modal" onclick="closeModal('forgotPasswordModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="forgotPasswordForm">
                    <p>Answer your security question to reset your password:</p>
                    <div class="form-group">
                        <label for="resetUsername">Username</label>
                        <input type="text" id="resetUsername" name="username" required>
                    </div>
                    <div class="form-group" id="securityQuestionGroup" style="display: none;">
                        <label id="displaySecurityQuestion"></label>
                        <input type="text" id="resetSecurityAnswer" name="security_answer" required>
                    </div>
                    <div class="form-group" id="newPasswordGroup" style="display: none;">
                        <label for="resetNewPassword">New Password</label>
                        <input type="password" id="resetNewPassword" name="new_password" required minlength="8">
                    </div>
                    <div class="form-group" id="confirmPasswordGroup" style="display: none;">
                        <label for="resetConfirmPassword">Confirm New Password</label>
                        <input type="password" id="resetConfirmPassword" name="confirm_password" required>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-block" id="resetSubmitBtn">Continue</button>
                    </div>
                    <div class="form-links">
                        <a href="#" onclick="switchModal('forgotPasswordModal', 'loginModal')">Back to Login</a>
                    </div>
                </form>
                <div id="forgotPasswordMessage" class="message"></div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 CHWR - Cobourg Homeless Warming Room. All rights reserved.</p>
            <p>Supporting our community with compassion and technology.</p>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>
