/**
 * CHWR - Main JavaScript
 * Handles modals, forms, AJAX requests, and UI interactions
 */

// Modal Management
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
        document.body.style.overflow = 'auto';
        
        // Clear form and messages
        const form = modal.querySelector('form');
        const message = modal.querySelector('.message');
        if (form) form.reset();
        if (message) {
            message.classList.remove('show', 'success', 'error', 'info');
            message.textContent = '';
        }
    }
}

function switchModal(currentModalId, targetModalId) {
    closeModal(currentModalId);
    setTimeout(() => openModal(targetModalId), 100);
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        const modals = document.querySelectorAll('.modal.show');
        modals.forEach(modal => {
            closeModal(modal.id);
        });
    }
}

// Smooth scroll to section
function scrollToSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// Show message helper
function showMessage(elementId, message, type = 'info') {
    const messageEl = document.getElementById(elementId);
    if (messageEl) {
        messageEl.textContent = message;
        messageEl.className = 'message show ' + type;
        
        // Auto-hide success messages after 5 seconds
        if (type === 'success') {
            setTimeout(() => {
                messageEl.classList.remove('show');
            }, 5000);
        }
    }
}

// Login Form Handler
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(loginForm);
            
            try {
                const response = await fetch('api/auth/login.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showMessage('loginMessage', 'Login successful! Redirecting...', 'success');
                    setTimeout(() => {
                        window.location.href = 'dashboard.php';
                    }, 1000);
                } else {
                    showMessage('loginMessage', data.message || 'Login failed', 'error');
                }
            } catch (error) {
                console.error('Login error:', error);
                showMessage('loginMessage', 'An error occurred. Please try again.', 'error');
            }
        });
    }
    
    // Register Form Handler
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const password = document.getElementById('regPassword').value;
            const confirmPassword = document.getElementById('regConfirmPassword').value;
            
            if (password !== confirmPassword) {
                showMessage('registerMessage', 'Passwords do not match', 'error');
                return;
            }
            
            const formData = new FormData(registerForm);
            
            try {
                const response = await fetch('api/auth/register.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showMessage('registerMessage', 
                        `Registration successful! Your username is: ${data.username}. Please remember it for login.`, 
                        'success');
                    
                    // Clear form
                    registerForm.reset();
                    
                    // Redirect to login after 3 seconds
                    setTimeout(() => {
                        switchModal('registerModal', 'loginModal');
                    }, 3000);
                } else {
                    showMessage('registerMessage', data.message || 'Registration failed', 'error');
                }
            } catch (error) {
                console.error('Registration error:', error);
                showMessage('registerMessage', 'An error occurred. Please try again.', 'error');
            }
        });
    }
    
    // Forgot Username Form Handler
    const forgotUsernameForm = document.getElementById('forgotUsernameForm');
    if (forgotUsernameForm) {
        forgotUsernameForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(forgotUsernameForm);
            
            try {
                const response = await fetch('api/auth/recover_username.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showMessage('forgotUsernameMessage', 
                        `Your username is: ${data.username}`, 
                        'success');
                } else {
                    showMessage('forgotUsernameMessage', data.message || 'Recovery failed', 'error');
                }
            } catch (error) {
                console.error('Username recovery error:', error);
                showMessage('forgotUsernameMessage', 'An error occurred. Please try again.', 'error');
            }
        });
    }
    
    // Forgot Password Form Handler
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    if (forgotPasswordForm) {
        let step = 1;
        let userId = null;
        
        forgotPasswordForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (step === 1) {
                // Get security question
                const username = document.getElementById('resetUsername').value;
                
                try {
                    const response = await fetch('api/auth/get_security_question.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ username })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        document.getElementById('displaySecurityQuestion').textContent = data.question;
                        document.getElementById('securityQuestionGroup').style.display = 'block';
                        document.getElementById('resetSubmitBtn').textContent = 'Verify Answer';
                        step = 2;
                    } else {
                        showMessage('forgotPasswordMessage', data.message || 'Username not found', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showMessage('forgotPasswordMessage', 'An error occurred. Please try again.', 'error');
                }
            } else if (step === 2) {
                // Verify security answer
                const username = document.getElementById('resetUsername').value;
                const answer = document.getElementById('resetSecurityAnswer').value;
                
                try {
                    const response = await fetch('api/auth/verify_security_answer.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ username, answer })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        userId = data.user_id;
                        document.getElementById('newPasswordGroup').style.display = 'block';
                        document.getElementById('confirmPasswordGroup').style.display = 'block';
                        document.getElementById('resetSubmitBtn').textContent = 'Reset Password';
                        showMessage('forgotPasswordMessage', 'Answer verified! Enter your new password.', 'success');
                        step = 3;
                    } else {
                        showMessage('forgotPasswordMessage', data.message || 'Incorrect answer', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showMessage('forgotPasswordMessage', 'An error occurred. Please try again.', 'error');
                }
            } else if (step === 3) {
                // Reset password
                const newPassword = document.getElementById('resetNewPassword').value;
                const confirmPassword = document.getElementById('resetConfirmPassword').value;
                
                if (newPassword !== confirmPassword) {
                    showMessage('forgotPasswordMessage', 'Passwords do not match', 'error');
                    return;
                }
                
                try {
                    const response = await fetch('api/auth/reset_password.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ user_id: userId, new_password: newPassword })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        showMessage('forgotPasswordMessage', 'Password reset successful! Redirecting to login...', 'success');
                        setTimeout(() => {
                            switchModal('forgotPasswordModal', 'loginModal');
                            step = 1;
                            userId = null;
                        }, 2000);
                    } else {
                        showMessage('forgotPasswordMessage', data.message || 'Password reset failed', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showMessage('forgotPasswordMessage', 'An error occurred. Please try again.', 'error');
                }
            }
        });
    }
});

// Profile Dropdown Toggle
function toggleProfileDropdown() {
    const dropdown = document.getElementById('profileDropdown');
    if (dropdown) {
        dropdown.classList.toggle('show');
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('profileDropdown');
    const avatar = document.querySelector('.profile-avatar');
    
    if (dropdown && avatar) {
        if (!avatar.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.remove('show');
        }
    }
});

// Theme Switcher
function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    
    // Update via AJAX if logged in
    fetch('api/user/update_theme.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ theme: newTheme })
    }).catch(error => console.error('Theme update error:', error));
}

// Load saved theme
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);
});

// Auto-save functionality for forms (debounced)
let autoSaveTimeout;
function autoSave(formId, endpoint) {
    clearTimeout(autoSaveTimeout);
    
    autoSaveTimeout = setTimeout(async () => {
        const form = document.getElementById(formId);
        if (!form) return;
        
        const formData = new FormData(form);
        
        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Show subtle save indicator
                showSaveIndicator('Saved');
            }
        } catch (error) {
            console.error('Auto-save error:', error);
        }
    }, 2000); // 2 second delay
}

function showSaveIndicator(text) {
    let indicator = document.getElementById('saveIndicator');
    
    if (!indicator) {
        indicator = document.createElement('div');
        indicator.id = 'saveIndicator';
        indicator.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--success-color);
            color: white;
            padding: 10px 20px;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s;
        `;
        document.body.appendChild(indicator);
    }
    
    indicator.textContent = text;
    indicator.style.opacity = '1';
    
    setTimeout(() => {
        indicator.style.opacity = '0';
    }, 2000);
}

// Utility: Format date
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString(undefined, options);
}

// Utility: Format time
function formatTime(dateString) {
    const options = { hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleTimeString(undefined, options);
}

// Utility: Calculate progress percentage
function calculateProgress(completed, total) {
    return total > 0 ? Math.round((completed / total) * 100) : 0;
}

// Update progress bar
function updateProgressBar(elementId, percentage) {
    const progressBar = document.getElementById(elementId);
    if (progressBar) {
        const fill = progressBar.querySelector('.progress-fill');
        if (fill) {
            fill.style.width = percentage + '%';
        }
    }
}

// Keyboard navigation helpers
document.addEventListener('keydown', function(event) {
    // Escape key closes modals
    if (event.key === 'Escape') {
        const modals = document.querySelectorAll('.modal.show');
        modals.forEach(modal => {
            closeModal(modal.id);
        });
    }
});

// Form validation helpers
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validatePhone(phone) {
    const re = /^[\d\s\-\+\(\)]+$/;
    return re.test(phone);
}

// Export functions for use in other scripts
window.CHWR = {
    openModal,
    closeModal,
    switchModal,
    showMessage,
    toggleTheme,
    autoSave,
    formatDate,
    formatTime,
    calculateProgress,
    updateProgressBar,
    validateEmail,
    validatePhone,
    toggleProfileDropdown
};
