<?php
/**
 * Client Appointments - Placeholder
 */

session_start();

require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';

$auth = new Auth();

if (!$auth->isLoggedIn()) {
    header('Location: ../../index.php');
    exit;
}

$firstName = $_SESSION['first_name'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - CHWR</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="nav-left">
                <a href="../../dashboard.php" class="logo">
                    <i class="fas fa-heart"></i>
                    <span>CHWR</span>
                </a>
            </div>
            <div class="nav-right">
                <div class="profile-dropdown">
                    <div class="profile-avatar" onclick="CHWR.toggleProfileDropdown()">
                        <?php echo strtoupper(substr($firstName, 0, 1)); ?>
                    </div>
                    <div class="dropdown-menu" id="profileDropdown">
                        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                        <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
                        <button onclick="CHWR.toggleTheme()"><i class="fas fa-moon"></i> Toggle Theme</button>
                        <a href="../../api/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container" style="margin-top: 2rem;">
        <h1><i class="fas fa-calendar-alt"></i> Appointments</h1>
        <div class="feature-card" style="margin-top: 2rem; text-align: center; padding: 3rem;">
            <i class="fas fa-calendar-check" style="font-size: 4rem; color: var(--text-light); margin-bottom: 1rem;"></i>
            <p style="color: var(--text-secondary);">Appointment scheduling feature coming soon. You'll be able to view and manage appointments with your support team.</p>
            <a href="dashboard.php" class="btn btn-primary" style="margin-top: 1rem;">Back to Dashboard</a>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
</body>
</html>
