<?php
/**
 * API: Get Security Question
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../includes/Database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$username = $input['username'] ?? '';

if (empty($username)) {
    echo json_encode(['success' => false, 'message' => 'Username is required']);
    exit;
}

$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT security_question FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user) {
    echo json_encode(['success' => true, 'question' => $user['security_question']]);
} else {
    echo json_encode(['success' => false, 'message' => 'Username not found']);
}
