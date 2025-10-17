<?php
/**
 * API: Verify Security Answer
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
$username = $input['username'] ?? '';
$answer = $input['answer'] ?? '';

if (empty($username) || empty($answer)) {
    echo json_encode(['success' => false, 'message' => 'Username and answer are required']);
    exit;
}

$auth = new Auth();
$result = $auth->verifySecurityAnswer($username, $answer);
echo json_encode($result);
