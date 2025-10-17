<?php
/**
 * API: Submit Assessment
 */

header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';

$auth = new Auth();

if (!$auth->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$assessmentId = $input['assessment_id'] ?? '';
$userId = $_SESSION['user_id'];

if (empty($assessmentId)) {
    echo json_encode(['success' => false, 'message' => 'Assessment ID required']);
    exit;
}

$db = Database::getInstance()->getConnection();

// Verify assessment belongs to user
$stmt = $db->prepare("SELECT user_id FROM assessments WHERE assessment_id = ?");
$stmt->execute([$assessmentId]);
$assessment = $stmt->fetch();

if (!$assessment || $assessment['user_id'] != $userId) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden']);
    exit;
}

try {
    // Update assessment status
    $stmt = $db->prepare("
        UPDATE assessments 
        SET status = 'Completed',
            completed_at = NOW(),
            updated_at = NOW()
        WHERE assessment_id = ?
    ");
    $stmt->execute([$assessmentId]);
    
    // Auto-generate tasks based on assessment results (simplified example)
    // In a real system, this would be more sophisticated based on responses
    
    $stmt = $db->prepare("
        SELECT section_code, traffic_light 
        FROM assessment_sections 
        WHERE assessment_id = ? AND traffic_light IN ('Yellow', 'Red')
    ");
    $stmt->execute([$assessmentId]);
    $criticalSections = $stmt->fetchAll();
    
    foreach ($criticalSections as $section) {
        $taskTitle = "Follow up on Section {$section['section_code']}";
        $taskDesc = "This section was flagged as {$section['traffic_light']} priority";
        $priority = $section['traffic_light'] === 'Red' ? 'Urgent' : 'High';
        
        $stmt = $db->prepare("
            INSERT INTO tasks (user_id, title, description, priority, status)
            VALUES (?, ?, ?, ?, 'Pending')
        ");
        $stmt->execute([$userId, $taskTitle, $taskDesc, $priority]);
    }
    
    echo json_encode(['success' => true, 'message' => 'Assessment submitted successfully']);
    
} catch (PDOException $e) {
    error_log("Submit assessment error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error submitting assessment']);
}
