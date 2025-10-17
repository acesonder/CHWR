<?php
require_once __DIR__ . '/includes/auth.php';
requireRole(ROLE_ADMIN);

$pageTitle = 'Audit Log';
include __DIR__ . '/includes/header.php';

$conn = getDBConnection();

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$perPage = 50;
$offset = ($page - 1) * $perPage;

// Get total count
$totalResult = $conn->query("SELECT COUNT(*) as count FROM audit_log");
$totalRecords = $totalResult->fetch_assoc()['count'];
$totalPages = ceil($totalRecords / $perPage);

// Get audit logs
$logsQuery = "
    SELECT a.id, a.action, a.details, a.ip_address, a.created_at, u.username 
    FROM audit_log a 
    LEFT JOIN users u ON a.user_id = u.id 
    ORDER BY a.created_at DESC 
    LIMIT ? OFFSET ?
";
$stmt = $conn->prepare($logsQuery);
$stmt->bind_param("ii", $perPage, $offset);
$stmt->execute();
$logsResult = $stmt->get_result();

$conn->close();
?>

<div class="dashboard-header">
    <h2>Audit Log</h2>
    <p>System activity and user actions</p>
</div>

<div class="content-card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Date/Time</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Details</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($logsResult->num_rows > 0): ?>
                    <?php while ($log = $logsResult->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('M d, Y H:i:s', strtotime($log['created_at'])); ?></td>
                        <td><?php echo htmlspecialchars($log['username'] ?? 'System'); ?></td>
                        <td><strong><?php echo htmlspecialchars($log['action']); ?></strong></td>
                        <td><?php echo htmlspecialchars($log['details']); ?></td>
                        <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">No audit records found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php if ($totalPages > 1): ?>
    <div style="margin-top: 1rem; text-align: center;">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>" class="btn btn-secondary btn-sm">Previous</a>
        <?php endif; ?>
        
        <span style="margin: 0 1rem;">Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
        
        <?php if ($page < $totalPages): ?>
            <a href="?page=<?php echo $page + 1; ?>" class="btn btn-secondary btn-sm">Next</a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
