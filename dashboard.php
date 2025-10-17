<?php
/**
 * CHWR - Dashboard
 * Role-based dashboard routing
 */

session_start();

require_once __DIR__ . '/includes/Database.php';
require_once __DIR__ . '/includes/Auth.php';

$auth = new Auth();

// Check if user is logged in
if (!$auth->isLoggedIn()) {
    // Check remember me cookie
    if (!$auth->checkRememberMe()) {
        header('Location: index.php');
        exit;
    }
}

// Route to role-specific dashboard
$role = $_SESSION['role'] ?? 'Client';

switch ($role) {
    case 'Administrator':
        require_once 'views/admin/dashboard.php';
        break;
    case 'Outreach Worker':
        require_once 'views/staff/dashboard.php';
        break;
    case 'Service Provider':
        require_once 'views/provider/dashboard.php';
        break;
    case 'Client':
    default:
        require_once 'views/client/dashboard.php';
        break;
}
