<?php
/**
 * API: Save Assessment Section Responses
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

$assessmentId = $_POST['assessment_id'] ?? '';
$sectionCode = $_POST['section_code'] ?? '';
$userId = $_SESSION['user_id'];

if (empty($assessmentId) || empty($sectionCode)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
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
    // Process each question response
    foreach ($_POST as $questionId => $value) {
        // Skip meta fields
        if (in_array($questionId, ['assessment_id', 'section_code'])) {
            continue;
        }
        
        // Handle multi-select (arrays)
        if (is_array($value)) {
            $value = json_encode($value);
        }
        
        // Skip empty values
        if (empty($value)) {
            continue;
        }
        
        // Check if response exists
        $stmt = $db->prepare("
            SELECT response_id FROM assessment_responses 
            WHERE assessment_id = ? AND section_code = ? AND question_id = ?
        ");
        $stmt->execute([$assessmentId, $sectionCode, $questionId]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            // Update existing response
            $stmt = $db->prepare("
                UPDATE assessment_responses 
                SET response_value = ?, updated_at = NOW()
                WHERE response_id = ?
            ");
            $stmt->execute([$value, $existing['response_id']]);
        } else {
            // Insert new response
            $stmt = $db->prepare("
                INSERT INTO assessment_responses 
                (assessment_id, section_code, question_id, question_text, response_value, response_type)
                VALUES (?, ?, ?, ?, ?, 'text')
            ");
            $stmt->execute([$assessmentId, $sectionCode, $questionId, $questionId, $value]);
        }
    }
    
    // Update section status to In Progress
    $stmt = $db->prepare("
        UPDATE assessment_sections 
        SET section_status = 'In Progress'
        WHERE assessment_id = ? AND section_code = ? AND section_status = 'Not Started'
    ");
    $stmt->execute([$assessmentId, $sectionCode]);
    
    // Update assessment status
    $stmt = $db->prepare("
        UPDATE assessments 
        SET status = 'In Progress', updated_at = NOW()
        WHERE assessment_id = ? AND status = 'Not Started'
    ");
    $stmt->execute([$assessmentId]);
    
    echo json_encode(['success' => true, 'message' => 'Responses saved']);
    
} catch (PDOException $e) {
    error_log("Save section error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error saving responses']);
}
