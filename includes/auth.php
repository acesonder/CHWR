<?php
session_start();

// Security headers
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");

// Include database config
require_once __DIR__ . '/../config/database.php';

// Define roles constants
define('ROLE_ADMIN', 'Admin');
define('ROLE_MANAGEMENT', 'Management');
define('ROLE_SERVICE_PROVIDER', 'Service Provider');
define('ROLE_OUTREACH', 'Outreach');
define('ROLE_CLIENT', 'Client');

// Authentication functions
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit();
    }
}

function hasRole($allowedRoles) {
    if (!isLoggedIn()) {
        return false;
    }
    
    if (!is_array($allowedRoles)) {
        $allowedRoles = [$allowedRoles];
    }
    
    return in_array($_SESSION['role_name'], $allowedRoles);
}

function requireRole($allowedRoles) {
    requireLogin();
    
    if (!hasRole($allowedRoles)) {
        header('HTTP/1.0 403 Forbidden');
        die('Access denied. You do not have permission to access this resource.');
    }
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'email' => $_SESSION['email'],
        'role_name' => $_SESSION['role_name'],
        'first_name' => $_SESSION['first_name'],
        'last_name' => $_SESSION['last_name']
    ];
}

function logout() {
    session_destroy();
    header('Location: /login.php');
    exit();
}

function logAuditAction($userId, $action, $details = '', $ipAddress = null) {
    $conn = getDBConnection();
    
    if ($ipAddress === null) {
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
    }
    
    $stmt = $conn->prepare("INSERT INTO audit_log (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $userId, $action, $details, $ipAddress);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>
