<?php
/**
 * Client Assessment Page
 */

session_start();

require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';

$auth = new Auth();

if (!$auth->isLoggedIn()) {
    header('Location: ../../index.php');
    exit;
}

$config = require __DIR__ . '/../../config/config.php';
$firstName = $_SESSION['first_name'] ?? 'User';
$userId = $_SESSION['user_id'] ?? 0;

$db = Database::getInstance()->getConnection();

// Get or create assessment
$stmt = $db->prepare("
    SELECT * FROM assessments 
    WHERE user_id = ? 
    ORDER BY created_at DESC 
    LIMIT 1
");
$stmt->execute([$userId]);
$assessment = $stmt->fetch();

if (!$assessment) {
    // Create new assessment
    $stmt = $db->prepare("INSERT INTO assessments (user_id, status) VALUES (?, 'Not Started')");
    $stmt->execute([$userId]);
    $assessmentId = $db->lastInsertId();
    
    // Create sections
    foreach ($config['assessment_sections'] as $code => $name) {
        $stmt = $db->prepare("
            INSERT INTO assessment_sections (assessment_id, section_code, section_name)
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$assessmentId, $code, $name]);
    }
    
    // Reload assessment
    $stmt = $db->prepare("SELECT * FROM assessments WHERE assessment_id = ?");
    $stmt->execute([$assessmentId]);
    $assessment = $stmt->fetch();
}

$assessmentId = $assessment['assessment_id'];

// Get sections progress
$stmt = $db->prepare("
    SELECT * FROM assessment_sections 
    WHERE assessment_id = ? 
    ORDER BY section_code
");
$stmt->execute([$assessmentId]);
$sections = $stmt->fetchAll();

// Calculate overall progress
$completedSections = 0;
foreach ($sections as $section) {
    if ($section['section_status'] === 'Completed') {
        $completedSections++;
    }
}
$totalSections = count($sections);
$progressPercentage = $totalSections > 0 ? round(($completedSections / $totalSections) * 100) : 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment - CHWR</title>
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
        <!-- Header -->
        <div style="margin-bottom: 2rem;">
            <h1>Intake Assessment</h1>
            <p style="color: var(--text-secondary);">Complete this assessment at your own pace. All questions are optional and your progress is automatically saved.</p>
        </div>

        <!-- Progress Bar -->
        <div style="margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span style="font-weight: 500;">Overall Progress</span>
                <span style="color: var(--primary-color); font-weight: bold;"><?php echo $progressPercentage; ?>%</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo $progressPercentage; ?>%;"></div>
            </div>
        </div>

        <!-- Assessment Status -->
        <div class="feature-card" style="margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h3 style="margin-bottom: 0.5rem;">Assessment Status</h3>
                    <span class="badge badge-primary"><?php echo $assessment['status']; ?></span>
                </div>
                <?php if ($assessment['traffic_light']): ?>
                    <div>
                        <h3 style="margin-bottom: 0.5rem;">Current Level</h3>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span class="traffic-light <?php echo strtolower($assessment['traffic_light']); ?>"></span>
                            <span style="font-weight: bold;"><?php echo $assessment['traffic_light']; ?></span>
                        </div>
                    </div>
                <?php endif; ?>
                <div>
                    <h3 style="margin-bottom: 0.5rem;">Sections Complete</h3>
                    <span style="font-weight: bold; font-size: 1.25rem;"><?php echo $completedSections; ?> / <?php echo $totalSections; ?></span>
                </div>
            </div>
        </div>

        <!-- Sections Grid -->
        <div class="features-grid">
            <?php foreach ($sections as $section): ?>
                <div class="feature-card" style="cursor: pointer; position: relative;" onclick="window.location.href='assessment_section.php?section=<?php echo $section['section_code']; ?>'">
                    <!-- Status Indicator -->
                    <div style="position: absolute; top: 1rem; right: 1rem;">
                        <?php if ($section['section_status'] === 'Completed'): ?>
                            <i class="fas fa-check-circle" style="color: var(--success-color); font-size: 1.5rem;"></i>
                        <?php elseif ($section['section_status'] === 'In Progress'): ?>
                            <i class="fas fa-clock" style="color: var(--warning-color); font-size: 1.5rem;"></i>
                        <?php else: ?>
                            <i class="far fa-circle" style="color: var(--text-light); font-size: 1.5rem;"></i>
                        <?php endif; ?>
                    </div>

                    <div class="feature-icon">
                        <i class="fas fa-<?php 
                            echo match($section['section_code']) {
                                'A' => 'file-signature',
                                'B' => 'brain',
                                'C' => 'home',
                                'D' => 'heartbeat',
                                'E' => 'hands-helping',
                                'F' => 'pills',
                                'G' => 'gavel',
                                'H' => 'dollar-sign',
                                'I' => 'users',
                                'J' => 'briefcase',
                                'K' => 'life-ring',
                                'L' => 'chart-line',
                                default => 'clipboard'
                            };
                        ?>"></i>
                    </div>
                    <h3>Section <?php echo $section['section_code']; ?></h3>
                    <p style="font-weight: 500; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($section['section_name']); ?></p>
                    
                    <?php if ($section['traffic_light']): ?>
                        <div style="margin-top: 0.5rem;">
                            <span class="traffic-light <?php echo strtolower($section['traffic_light']); ?>"></span>
                            <?php echo $section['traffic_light']; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div style="margin-top: 1rem;">
                        <span class="badge badge-<?php 
                            echo $section['section_status'] === 'Completed' ? 'success' : 
                                ($section['section_status'] === 'In Progress' ? 'warning' : 'primary'); 
                        ?>">
                            <?php echo $section['section_status']; ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Instructions -->
        <div class="feature-card" style="margin-top: 2rem;">
            <h3 style="margin-bottom: 1rem;"><i class="fas fa-info-circle"></i> Instructions</h3>
            <ul style="color: var(--text-secondary); line-height: 1.8;">
                <li>All questions in the assessment are <strong>optional</strong>. Answer only what you're comfortable sharing.</li>
                <li>Your answers are <strong>automatically saved</strong> as you type (after 2 seconds of inactivity).</li>
                <li>You can <strong>complete sections in any order</strong> and return to them later.</li>
                <li>The <strong>traffic light system</strong> helps staff prioritize your needs:
                    <ul style="margin-top: 0.5rem; margin-left: 1.5rem;">
                        <li><span class="traffic-light green"></span> <strong>Green</strong> = Low Acuity</li>
                        <li><span class="traffic-light yellow"></span> <strong>Yellow</strong> = Moderate Acuity</li>
                        <li><span class="traffic-light red"></span> <strong>Red</strong> = High/Urgent Acuity</li>
                    </ul>
                </li>
                <li>Critical issues (e.g., immediate safety concerns) will be <strong>automatically flagged</strong> for staff review.</li>
                <li>Click on any section above to begin or continue.</li>
            </ul>
        </div>

        <!-- Action Buttons -->
        <div style="margin-top: 2rem; display: flex; gap: 1rem; flex-wrap: wrap;">
            <a href="dashboard.php" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <?php if ($progressPercentage === 100 && $assessment['status'] !== 'Completed'): ?>
                <button class="btn btn-primary" onclick="submitAssessment()">
                    <i class="fas fa-check"></i> Submit Assessment
                </button>
            <?php endif; ?>
        </div>
    </div>

    <script src="../../assets/js/main.js"></script>
    <script>
        async function submitAssessment() {
            if (!confirm('Are you ready to submit your assessment? You can still make changes after submission.')) {
                return;
            }
            
            try {
                const response = await fetch('../../api/assessment/submit.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ assessment_id: <?php echo $assessmentId; ?> })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('Assessment submitted successfully!');
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to submit assessment');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while submitting the assessment');
            }
        }
    </script>
</body>
</html>
