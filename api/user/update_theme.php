<?php
/**
 * API: Update User Theme Preference
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
$theme = $input['theme'] ?? '';
$userId = $_SESSION['user_id'];

if (!in_array($theme, ['light', 'dark'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid theme']);
    exit;
}

$db = Database::getInstance()->getConnection();

try {
    $stmt = $db->prepare("UPDATE users SET theme_preference = ? WHERE user_id = ?");
    $stmt->execute([$theme, $userId]);
    
    $_SESSION['theme'] = $theme;
    
    echo json_encode(['success' => true, 'message' => 'Theme updated']);
    
} catch (PDOException $e) {
    error_log("Theme update error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error updating theme']);
}
