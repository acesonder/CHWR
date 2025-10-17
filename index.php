<?php
require_once __DIR__ . '/includes/auth.php';

// Redirect to dashboard if logged in, otherwise to login page
if (isLoggedIn()) {
    header('Location: /dashboard.php');
} else {
    header('Location: /login.php');
}
exit();
?>
