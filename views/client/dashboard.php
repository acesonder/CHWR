<?php
/**
 * Client Dashboard
 */

$config = require __DIR__ . '/../../config/config.php';
$firstName = $_SESSION['first_name'] ?? 'User';
$userId = $_SESSION['user_id'] ?? 0;

// Fetch client data
$db = Database::getInstance()->getConnection();

// Get assessment progress
$stmt = $db->prepare("
    SELECT status, overall_score, traffic_light 
    FROM assessments 
    WHERE user_id = ? 
    ORDER BY created_at DESC 
    LIMIT 1
");
$stmt->execute([$userId]);
$assessment = $stmt->fetch();

// Get upcoming appointments
$stmt = $db->prepare("
    SELECT * FROM appointments 
    WHERE client_id = ? AND appointment_date >= NOW() AND status != 'Cancelled'
    ORDER BY appointment_date ASC 
    LIMIT 5
");
$stmt->execute([$userId]);
$appointments = $stmt->fetchAll();

// Get pending tasks
$stmt = $db->prepare("
    SELECT * FROM tasks 
    WHERE user_id = ? AND status != 'Completed' AND status != 'Cancelled'
    ORDER BY priority DESC, due_date ASC 
    LIMIT 5
");
$stmt->execute([$userId]);
$tasks = $stmt->fetchAll();

// Get unread messages count
$stmt = $db->prepare("
    SELECT COUNT(*) as unread_count 
    FROM messages 
    WHERE recipient_id = ? AND is_read = 0
");
$stmt->execute([$userId]);
$messageData = $stmt->fetch();
$unreadMessages = $messageData['unread_count'] ?? 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard - CHWR</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Global Navigation Bar -->
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
                        <button onclick="CHWR.toggleTheme()">
                            <i class="fas fa-moon"></i> Toggle Theme
                        </button>
                        <a href="../../api/auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="container" style="margin-top: 2rem; margin-bottom: 2rem;">
        <!-- Welcome Section -->
        <div style="margin-bottom: 2rem;">
            <h1>Welcome back, <?php echo htmlspecialchars($firstName); ?>!</h1>
            <p style="color: var(--text-secondary);">Here's your overview for today</p>
        </div>

        <!-- Quick Stats -->
        <div class="features-grid" style="margin-bottom: 2rem;">
            <div class="feature-card" style="cursor: pointer;" onclick="window.location.href='assessment.php'">
                <div class="feature-icon">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <h3>Assessment</h3>
                <?php if ($assessment): ?>
                    <p>Status: <span class="badge badge-primary"><?php echo $assessment['status']; ?></span></p>
                    <?php if ($assessment['traffic_light']): ?>
                        <div style="margin-top: 0.5rem;">
                            <span class="traffic-light <?php echo strtolower($assessment['traffic_light']); ?>"></span>
                            <?php echo $assessment['traffic_light']; ?> Level
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <p>Not started</p>
                    <button class="btn btn-primary" style="margin-top: 0.5rem;">Start Assessment</button>
                <?php endif; ?>
            </div>

            <div class="feature-card" style="cursor: pointer;" onclick="window.location.href='messages.php'">
                <div class="feature-icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <h3>Messages</h3>
                <p><?php echo $unreadMessages; ?> unread</p>
            </div>

            <div class="feature-card" style="cursor: pointer;" onclick="window.location.href='tasks.php'">
                <div class="feature-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <h3>Tasks</h3>
                <p><?php echo count($tasks); ?> pending</p>
            </div>

            <div class="feature-card" style="cursor: pointer;" onclick="window.location.href='appointments.php'">
                <div class="feature-icon">
                    <i class="fas fa-calendar"></i>
                </div>
                <h3>Appointments</h3>
                <p><?php echo count($appointments); ?> upcoming</p>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem;">
            <!-- Upcoming Appointments -->
            <div class="feature-card">
                <h2 style="margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-calendar-alt" style="color: var(--primary-color);"></i>
                    Upcoming Appointments
                </h2>
                <?php if (empty($appointments)): ?>
                    <p style="color: var(--text-secondary);">No upcoming appointments</p>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <?php foreach ($appointments as $apt): ?>
                            <div style="padding: 1rem; background: var(--bg-secondary); border-radius: var(--radius-md);">
                                <div style="font-weight: bold; margin-bottom: 0.25rem;">
                                    <?php echo htmlspecialchars($apt['title']); ?>
                                </div>
                                <div style="color: var(--text-secondary); font-size: var(--font-size-sm);">
                                    <i class="fas fa-clock"></i>
                                    <?php echo date('M j, Y g:i A', strtotime($apt['appointment_date'])); ?>
                                </div>
                                <?php if ($apt['location']): ?>
                                    <div style="color: var(--text-secondary); font-size: var(--font-size-sm);">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?php echo htmlspecialchars($apt['location']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <a href="appointments.php" class="btn btn-outline" style="margin-top: 1rem; width: 100%;">View All</a>
            </div>

            <!-- Pending Tasks -->
            <div class="feature-card">
                <h2 style="margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-tasks" style="color: var(--primary-color);"></i>
                    Pending Tasks
                </h2>
                <?php if (empty($tasks)): ?>
                    <p style="color: var(--text-secondary);">No pending tasks</p>
                <?php else: ?>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <?php foreach ($tasks as $task): ?>
                            <div style="padding: 1rem; background: var(--bg-secondary); border-radius: var(--radius-md);">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.25rem;">
                                    <span style="font-weight: bold;"><?php echo htmlspecialchars($task['title']); ?></span>
                                    <span class="badge badge-<?php echo $task['priority'] === 'Urgent' ? 'danger' : ($task['priority'] === 'High' ? 'warning' : 'primary'); ?>">
                                        <?php echo $task['priority']; ?>
                                    </span>
                                </div>
                                <?php if ($task['description']): ?>
                                    <div style="color: var(--text-secondary); font-size: var(--font-size-sm); margin-bottom: 0.5rem;">
                                        <?php echo htmlspecialchars(substr($task['description'], 0, 100)); ?>
                                        <?php echo strlen($task['description']) > 100 ? '...' : ''; ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($task['due_date']): ?>
                                    <div style="color: var(--text-secondary); font-size: var(--font-size-sm);">
                                        <i class="fas fa-calendar"></i>
                                        Due: <?php echo date('M j, Y', strtotime($task['due_date'])); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <a href="tasks.php" class="btn btn-outline" style="margin-top: 1rem; width: 100%;">View All</a>
            </div>

            <!-- Quick Actions -->
            <div class="feature-card">
                <h2 style="margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-bolt" style="color: var(--primary-color);"></i>
                    Quick Actions
                </h2>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <a href="assessment.php" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-clipboard-check"></i> Continue Assessment
                    </a>
                    <a href="messages.php" class="btn btn-outline" style="width: 100%;">
                        <i class="fas fa-envelope"></i> Send Message
                    </a>
                    <a href="appointments.php" class="btn btn-outline" style="width: 100%;">
                        <i class="fas fa-calendar-plus"></i> Request Appointment
                    </a>
                    <a href="resources.php" class="btn btn-outline" style="width: 100%;">
                        <i class="fas fa-book"></i> Browse Resources
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
</body>
</html>
