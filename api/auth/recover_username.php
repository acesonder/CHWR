<?php
/**
 * API: Recover Username
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

$firstName = $_POST['first_name'] ?? '';
$lastName = $_POST['last_name'] ?? '';
$dateOfBirth = $_POST['date_of_birth'] ?? '';

if (empty($firstName) || empty($lastName) || empty($dateOfBirth)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

$result = $auth->recoverUsername($firstName, $lastName, $dateOfBirth);
echo json_encode($result);
