<?php
/**
 * API: Register
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$auth = new Auth();

$requiredFields = ['first_name', 'last_name', 'date_of_birth', 'password', 'security_question', 'security_answer'];
$data = [];

foreach ($requiredFields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
        exit;
    }
    $data[$field] = trim($_POST[$field]);
}

// Optional fields
$data['email'] = $_POST['email'] ?? null;
$data['phone'] = $_POST['phone'] ?? null;
$data['role'] = 'Client'; // Default role for self-registration

$result = $auth->register($data);
echo json_encode($result);
