<?php
/**
 * API: Mark Assessment Section as Complete
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
$sectionCode = $input['section_code'] ?? '';
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
    // Calculate section score (simple example - can be enhanced)
    $stmt = $db->prepare("
        SELECT COUNT(*) as response_count 
        FROM assessment_responses 
        WHERE assessment_id = ? AND section_code = ?
    ");
    $stmt->execute([$assessmentId, $sectionCode]);
    $responseData = $stmt->fetch();
    $responseCount = $responseData['response_count'] ?? 0;
    
    // Simple scoring: more responses = higher engagement score
    $sectionScore = min(100, $responseCount * 10);
    
    // Determine traffic light based on critical responses (simplified)
    $trafficLight = 'Green';
    if ($sectionScore > 66) {
        $trafficLight = 'Red';
    } elseif ($sectionScore > 33) {
        $trafficLight = 'Yellow';
    }
    
    // Update section
    $stmt = $db->prepare("
        UPDATE assessment_sections 
        SET section_status = 'Completed',
            section_score = ?,
            traffic_light = ?,
            completed_at = NOW()
        WHERE assessment_id = ? AND section_code = ?
    ");
    $stmt->execute([$sectionScore, $trafficLight, $assessmentId, $sectionCode]);
    
    // Recalculate overall assessment score
    $stmt = $db->prepare("
        SELECT AVG(section_score) as avg_score, 
               SUM(CASE WHEN section_status = 'Completed' THEN 1 ELSE 0 END) as completed_count,
               COUNT(*) as total_count
        FROM assessment_sections 
        WHERE assessment_id = ?
    ");
    $stmt->execute([$assessmentId]);
    $stats = $stmt->fetch();
    
    $overallScore = $stats['avg_score'] ?? 0;
    $overallTrafficLight = 'Green';
    
    if ($overallScore > 66) {
        $overallTrafficLight = 'Red';
    } elseif ($overallScore > 33) {
        $overallTrafficLight = 'Yellow';
    }
    
    // Update assessment
    $stmt = $db->prepare("
        UPDATE assessments 
        SET overall_score = ?,
            traffic_light = ?,
            updated_at = NOW()
        WHERE assessment_id = ?
    ");
    $stmt->execute([$overallScore, $overallTrafficLight, $assessmentId]);
    
    echo json_encode(['success' => true, 'message' => 'Section marked as complete']);
    
} catch (PDOException $e) {
    error_log("Complete section error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error completing section']);
}
