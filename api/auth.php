<?php
/**
 * Unified Authentication API
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/bootstrap.php';

$action = $_REQUEST['action'] ?? '';
$response = ['success' => false, 'error' => 'Invalid action'];

switch ($action) {
    case 'login':
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $response = $auth->login($email, $password);
        break;

    case 'register':
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $name = $_POST['full_name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $response = $auth->register($email, $password, $name, $phone);
        break;

    case 'logout':
        $auth->logout();
        $response = ['success' => true];
        break;

    case 'getCurrentUser':
        $user = $auth->getCurrentUser();
        $response = $user ? ['success' => true, 'user' => $user] : ['success' => false, 'error' => 'Not logged in'];
        break;

    default:
        $response = ['success' => false, 'error' => "Action '$action' not recognized"];
        break;
}

echo json_encode($response);
exit;
