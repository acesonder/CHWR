// Main JavaScript file for CHWR application

// Utility functions
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    
    const container = document.querySelector('.container');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
}

// AJAX helper function
function ajaxRequest(url, method = 'GET', data = null) {
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        
        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    resolve(response);
                } catch (e) {
                    resolve(xhr.responseText);
                }
            } else {
                reject(new Error(`Request failed with status ${xhr.status}`));
            }
        };
        
        xhr.onerror = function() {
            reject(new Error('Network error'));
        };
        
        xhr.open(method, url, true);
        
        if (method === 'POST' || method === 'PUT') {
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        }
        
        xhr.send(data);
    });
}

// Modal functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('show');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('show');
    }
}

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.remove('show');
    }
});

// Form validation
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    const inputs = form.querySelectorAll('[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            input.style.borderColor = 'red';
        } else {
            input.style.borderColor = '';
        }
        
        if (input.type === 'email' && input.value && !validateEmail(input.value)) {
            isValid = false;
            input.style.borderColor = 'red';
        }
    });
    
    return isValid;
}

// User Management Functions
function editUser(userId) {
    ajaxRequest(`/api/users.php?action=get&id=${userId}`, 'GET')
        .then(response => {
            if (response.success) {
                const user = response.data;
                document.getElementById('edit_user_id').value = user.id;
                document.getElementById('edit_username').value = user.username;
                document.getElementById('edit_email').value = user.email;
                document.getElementById('edit_first_name').value = user.first_name;
                document.getElementById('edit_last_name').value = user.last_name;
                document.getElementById('edit_role_id').value = user.role_id;
                document.getElementById('edit_is_active').checked = user.is_active == 1;
                
                openModal('editUserModal');
            } else {
                showAlert(response.message || 'Failed to load user data', 'danger');
            }
        })
        .catch(error => {
            showAlert('Error loading user data: ' + error.message, 'danger');
        });
}

function deleteUser(userId, username) {
    if (confirm(`Are you sure you want to delete user "${username}"?`)) {
        const data = `action=delete&id=${userId}`;
        
        ajaxRequest('/api/users.php', 'POST', data)
            .then(response => {
                if (response.success) {
                    showAlert(response.message, 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showAlert(response.message || 'Failed to delete user', 'danger');
                }
            })
            .catch(error => {
                showAlert('Error deleting user: ' + error.message, 'danger');
            });
    }
}

// Handle form submissions with AJAX
document.addEventListener('DOMContentLoaded', function() {
    // Add User Form
    const addUserForm = document.getElementById('addUserForm');
    if (addUserForm) {
        addUserForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!validateForm('addUserForm')) {
                showAlert('Please fill in all required fields correctly', 'danger');
                return;
            }
            
            const formData = new FormData(addUserForm);
            const data = new URLSearchParams(formData).toString();
            
            ajaxRequest('/api/users.php', 'POST', data)
                .then(response => {
                    if (response.success) {
                        showAlert(response.message, 'success');
                        addUserForm.reset();
                        closeModal('addUserModal');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showAlert(response.message || 'Failed to add user', 'danger');
                    }
                })
                .catch(error => {
                    showAlert('Error adding user: ' + error.message, 'danger');
                });
        });
    }
    
    // Edit User Form
    const editUserForm = document.getElementById('editUserForm');
    if (editUserForm) {
        editUserForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!validateForm('editUserForm')) {
                showAlert('Please fill in all required fields correctly', 'danger');
                return;
            }
            
            const formData = new FormData(editUserForm);
            const data = new URLSearchParams(formData).toString();
            
            ajaxRequest('/api/users.php', 'POST', data)
                .then(response => {
                    if (response.success) {
                        showAlert(response.message, 'success');
                        closeModal('editUserModal');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        showAlert(response.message || 'Failed to update user', 'danger');
                    }
                })
                .catch(error => {
                    showAlert('Error updating user: ' + error.message, 'danger');
                });
        });
    }
});

// Search functionality
function searchTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    
    if (!input || !table) return;
    
    input.addEventListener('keyup', function() {
        const filter = input.value.toLowerCase();
        const rows = table.getElementsByTagName('tr');
        
        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            let found = false;
            
            for (let j = 0; j < cells.length; j++) {
                const cell = cells[j];
                if (cell.textContent.toLowerCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
            
            row.style.display = found ? '' : 'none';
        }
    });
}

// Initialize search on page load
document.addEventListener('DOMContentLoaded', function() {
    searchTable('searchInput', 'usersTable');
});
