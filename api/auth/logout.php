<?php
/**
 * API: Logout
 */

session_start();

require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';

$auth = new Auth();
$auth->logout();

header('Location: ../../index.php');
exit;
