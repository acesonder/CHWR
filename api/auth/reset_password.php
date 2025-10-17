<?php
/**
 * API: Reset Password
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$userId = $input['user_id'] ?? '';
$newPassword = $input['new_password'] ?? '';

if (empty($userId) || empty($newPassword)) {
    echo json_encode(['success' => false, 'message' => 'User ID and new password are required']);
    exit;
}

if (strlen($newPassword) < 8) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters']);
    exit;
}

$auth = new Auth();
$result = $auth->resetPassword($userId, $newPassword);
echo json_encode($result);
