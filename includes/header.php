<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - CHWR' : 'CHWR - Cobourg Homeless Warming Room'; ?></title>
    <link rel="stylesheet" href="/public/css/styles.css">
</head>
<body>
    <?php if (isLoggedIn()): ?>
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <h1>CHWR</h1>
                <span class="nav-subtitle">Cobourg Homeless Warming Room</span>
            </div>
            <ul class="nav-menu">
                <li><a href="/dashboard.php">Dashboard</a></li>
                
                <?php if (hasRole([ROLE_ADMIN, ROLE_MANAGEMENT])): ?>
                <li><a href="/users.php">User Management</a></li>
                <?php endif; ?>
                
                <?php if (hasRole([ROLE_ADMIN])): ?>
                <li><a href="/audit-log.php">Audit Log</a></li>
                <?php endif; ?>
                
                <li class="nav-user">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['first_name'] ?? $_SESSION['username']); ?></span>
                    <span class="user-role">(<?php echo htmlspecialchars($_SESSION['role_name']); ?>)</span>
                </li>
                <li><a href="/logout.php" class="btn-logout">Logout</a></li>
            </ul>
        </div>
    </nav>
    <?php endif; ?>
    
    <div class="container">
