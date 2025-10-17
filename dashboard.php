<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$pageTitle = 'Dashboard';
include __DIR__ . '/includes/header.php';

$conn = getDBConnection();
$currentUser = getCurrentUser();

// Get statistics based on role
$stats = [];

if (hasRole([ROLE_ADMIN, ROLE_MANAGEMENT])) {
    // Total users
    $result = $conn->query("SELECT COUNT(*) as count FROM users WHERE is_active = 1");
    $stats['total_users'] = $result->fetch_assoc()['count'];
    
    // Total by role
    $result = $conn->query("SELECT COUNT(*) as count FROM users u JOIN roles r ON u.role_id = r.id WHERE r.role_name = 'Client' AND u.is_active = 1");
    $stats['total_clients'] = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM users u JOIN roles r ON u.role_id = r.id WHERE r.role_name = 'Service Provider' AND u.is_active = 1");
    $stats['total_providers'] = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM users u JOIN roles r ON u.role_id = r.id WHERE r.role_name = 'Outreach' AND u.is_active = 1");
    $stats['total_outreach'] = $result->fetch_assoc()['count'];
}

$conn->close();
?>

<div class="dashboard-header">
    <h2>Welcome, <?php echo htmlspecialchars($currentUser['first_name'] ?? $currentUser['username']); ?>!</h2>
    <p>Role: <strong><?php echo htmlspecialchars($currentUser['role_name']); ?></strong></p>
</div>

<?php if (hasRole([ROLE_ADMIN, ROLE_MANAGEMENT])): ?>
<div class="stats-grid">
    <div class="stat-card">
        <h3>Total Active Users</h3>
        <div class="stat-value"><?php echo $stats['total_users']; ?></div>
    </div>
    <div class="stat-card">
        <h3>Clients</h3>
        <div class="stat-value"><?php echo $stats['total_clients']; ?></div>
    </div>
    <div class="stat-card">
        <h3>Service Providers</h3>
        <div class="stat-value"><?php echo $stats['total_providers']; ?></div>
    </div>
    <div class="stat-card">
        <h3>Outreach Workers</h3>
        <div class="stat-value"><?php echo $stats['total_outreach']; ?></div>
    </div>
</div>
<?php endif; ?>

<div class="content-card">
    <h3>Dashboard Overview</h3>
    
    <?php if (hasRole(ROLE_ADMIN)): ?>
        <p>As an <strong>Administrator</strong>, you have full access to:</p>
        <ul>
            <li>User Management - Create, edit, and delete user accounts</li>
            <li>Audit Log - View all system activities and user actions</li>
            <li>System Configuration - Manage roles and permissions</li>
            <li>Reports and Analytics - Access comprehensive system reports</li>
        </ul>
    <?php elseif (hasRole(ROLE_MANAGEMENT)): ?>
        <p>As a <strong>Management</strong> user, you have access to:</p>
        <ul>
            <li>User Management - View and manage user accounts</li>
            <li>Reports and Analytics - View operational reports</li>
            <li>Service Oversight - Monitor service delivery</li>
        </ul>
    <?php elseif (hasRole(ROLE_SERVICE_PROVIDER)): ?>
        <p>As a <strong>Service Provider</strong>, you can:</p>
        <ul>
            <li>Manage Client Records - Add and update client information</li>
            <li>Service Documentation - Record services provided</li>
            <li>View Reports - Access client service history</li>
        </ul>
    <?php elseif (hasRole(ROLE_OUTREACH)): ?>
        <p>As an <strong>Outreach Worker</strong>, you can:</p>
        <ul>
            <li>Community Engagement - Log outreach activities</li>
            <li>Client Referrals - Refer clients to services</li>
            <li>Field Reports - Submit community reports</li>
        </ul>
    <?php elseif (hasRole(ROLE_CLIENT)): ?>
        <p>As a <strong>Client</strong>, you can:</p>
        <ul>
            <li>View Your Information - Access your personal records</li>
            <li>Service History - View services you've received</li>
            <li>Update Profile - Keep your contact information current</li>
        </ul>
    <?php endif; ?>
</div>

<div class="content-card">
    <h3>Quick Links</h3>
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <?php if (hasRole([ROLE_ADMIN, ROLE_MANAGEMENT])): ?>
            <a href="/users.php" class="btn btn-primary">Manage Users</a>
        <?php endif; ?>
        
        <?php if (hasRole(ROLE_ADMIN)): ?>
            <a href="/audit-log.php" class="btn btn-secondary">View Audit Log</a>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
