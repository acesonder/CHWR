<?php
/**
 * Service Provider Dashboard - Placeholder
 */

$firstName = $_SESSION['first_name'] ?? 'Provider';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provider Dashboard - CHWR</title>
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
        <h1>Service Provider Dashboard</h1>
        <div class="features-grid" style="margin-top: 2rem;">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-inbox"></i></div>
                <h3>Referrals</h3>
                <p>View and manage incoming referrals</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-handshake"></i></div>
                <h3>Active Cases</h3>
                <p>Track clients you're serving</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-chart-bar"></i></div>
                <h3>Reports</h3>
                <p>Service delivery reports</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-cog"></i></div>
                <h3>Services</h3>
                <p>Manage your service offerings</p>
            </div>
        </div>
        <div class="feature-card" style="margin-top: 2rem; text-align: center; padding: 2rem;">
            <p style="color: var(--text-secondary);">Full service provider features will be implemented in future updates.</p>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
</body>
</html>
