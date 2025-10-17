<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole([ROLE_ADMIN, ROLE_MANAGEMENT]);

header('Content-Type: application/json');

$action = $_REQUEST['action'] ?? '';
$response = ['success' => false, 'message' => ''];

try {
    $conn = getDBConnection();
    
    switch ($action) {
        case 'get':
            // Get single user
            $id = intval($_GET['id'] ?? 0);
            
            if ($id > 0) {
                $stmt = $conn->prepare("
                    SELECT u.id, u.username, u.email, u.first_name, u.last_name, u.role_id, u.is_active 
                    FROM users u 
                    WHERE u.id = ?
                ");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows === 1) {
                    $response['success'] = true;
                    $response['data'] = $result->fetch_assoc();
                } else {
                    $response['message'] = 'User not found';
                }
                
                $stmt->close();
            } else {
                $response['message'] = 'Invalid user ID';
            }
            break;
            
        case 'create':
            if (!hasRole(ROLE_ADMIN)) {
                $response['message'] = 'Access denied';
                break;
            }
            
            $username = sanitizeInput($_POST['username'] ?? '');
            $email = sanitizeInput($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $firstName = sanitizeInput($_POST['first_name'] ?? '');
            $lastName = sanitizeInput($_POST['last_name'] ?? '');
            $roleId = intval($_POST['role_id'] ?? 0);
            $isActive = isset($_POST['is_active']) ? 1 : 0;
            
            if (empty($username) || empty($email) || empty($password) || $roleId === 0) {
                $response['message'] = 'All required fields must be filled';
                break;
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response['message'] = 'Invalid email address';
                break;
            }
            
            // Check if username or email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $response['message'] = 'Username or email already exists';
                $stmt->close();
                break;
            }
            $stmt->close();
            
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert user
            $stmt = $conn->prepare("
                INSERT INTO users (username, email, password, first_name, last_name, role_id, is_active) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("sssssii", $username, $email, $hashedPassword, $firstName, $lastName, $roleId, $isActive);
            
            if ($stmt->execute()) {
                $newUserId = $conn->insert_id;
                logAuditAction($_SESSION['user_id'], 'CREATE_USER', "Created user: $username (ID: $newUserId)");
                
                $response['success'] = true;
                $response['message'] = 'User created successfully';
                $response['data'] = ['id' => $newUserId];
            } else {
                $response['message'] = 'Failed to create user: ' . $conn->error;
            }
            
            $stmt->close();
            break;
            
        case 'update':
            if (!hasRole(ROLE_ADMIN)) {
                $response['message'] = 'Access denied';
                break;
            }
            
            $id = intval($_POST['id'] ?? 0);
            $username = sanitizeInput($_POST['username'] ?? '');
            $email = sanitizeInput($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $firstName = sanitizeInput($_POST['first_name'] ?? '');
            $lastName = sanitizeInput($_POST['last_name'] ?? '');
            $roleId = intval($_POST['role_id'] ?? 0);
            $isActive = isset($_POST['is_active']) ? 1 : 0;
            
            if ($id === 0 || empty($username) || empty($email) || $roleId === 0) {
                $response['message'] = 'All required fields must be filled';
                break;
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response['message'] = 'Invalid email address';
                break;
            }
            
            // Check if username or email already exists for other users
            $stmt = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
            $stmt->bind_param("ssi", $username, $email, $id);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $response['message'] = 'Username or email already exists';
                $stmt->close();
                break;
            }
            $stmt->close();
            
            // Update user
            if (!empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("
                    UPDATE users 
                    SET username = ?, email = ?, password = ?, first_name = ?, last_name = ?, role_id = ?, is_active = ? 
                    WHERE id = ?
                ");
                $stmt->bind_param("ssssssii", $username, $email, $hashedPassword, $firstName, $lastName, $roleId, $isActive, $id);
            } else {
                $stmt = $conn->prepare("
                    UPDATE users 
                    SET username = ?, email = ?, first_name = ?, last_name = ?, role_id = ?, is_active = ? 
                    WHERE id = ?
                ");
                $stmt->bind_param("sssssii", $username, $email, $firstName, $lastName, $roleId, $isActive, $id);
            }
            
            if ($stmt->execute()) {
                logAuditAction($_SESSION['user_id'], 'UPDATE_USER', "Updated user: $username (ID: $id)");
                
                $response['success'] = true;
                $response['message'] = 'User updated successfully';
            } else {
                $response['message'] = 'Failed to update user: ' . $conn->error;
            }
            
            $stmt->close();
            break;
            
        case 'delete':
            if (!hasRole(ROLE_ADMIN)) {
                $response['message'] = 'Access denied';
                break;
            }
            
            $id = intval($_POST['id'] ?? 0);
            
            if ($id === 0) {
                $response['message'] = 'Invalid user ID';
                break;
            }
            
            // Don't allow deleting the default admin user
            $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                $response['message'] = 'User not found';
                $stmt->close();
                break;
            }
            
            $user = $result->fetch_assoc();
            if ($user['username'] === 'admin') {
                $response['message'] = 'Cannot delete the default admin user';
                $stmt->close();
                break;
            }
            
            $stmt->close();
            
            // Delete user
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                logAuditAction($_SESSION['user_id'], 'DELETE_USER', "Deleted user ID: $id");
                
                $response['success'] = true;
                $response['message'] = 'User deleted successfully';
            } else {
                $response['message'] = 'Failed to delete user: ' . $conn->error;
            }
            
            $stmt->close();
            break;
            
        default:
            $response['message'] = 'Invalid action';
            break;
    }
    
    $conn->close();
    
} catch (Exception $e) {
    $response['message'] = 'An error occurred: ' . $e->getMessage();
}

echo json_encode($response);
?>
