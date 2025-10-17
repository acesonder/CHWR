<?php
/**
 * API: Login
 */

header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$auth = new Auth();

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$rememberMe = isset($_POST['remember_me']);

if (empty($username) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Username and password are required']);
    exit;
}

$result = $auth->login($username, $password, $rememberMe);
echo json_encode($result);
