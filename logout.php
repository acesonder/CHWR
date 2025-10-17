<?php
require_once __DIR__ . '/includes/auth.php';

logAuditAction($_SESSION['user_id'] ?? 0, 'LOGOUT', 'User logged out');
logout();
?>
