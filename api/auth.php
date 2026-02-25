<?php
/**
 * Authentication API Endpoint
 * Handles login, logout, register, and user data retrieval
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/bootstrap.php';

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

switch ($action) {
    case 'login':
        handleLogin();
        break;
    case 'register':
        handleRegister();
        break;
    case 'logout':
        handleLogout();
        break;
    case 'get-user':
        getCurrentUser();
        break;
    case 'change-password':
        handleChangePassword();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}

function handleLogin() {
    global $auth;
    
    $data = json_decode(file_get_contents('php://input'), true);
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    
    $result = $auth->login($email, $password);
    echo json_encode($result);
}

function handleRegister() {
    global $auth;
    
    $data = json_decode(file_get_contents('php://input'), true);
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    $first_name = $data['first_name'] ?? '';
    $last_name = $data['last_name'] ?? '';
    $phone = $data['phone'] ?? '';
    
    $result = $auth->register($email, $password, $first_name, $last_name, $phone);
    echo json_encode($result);
}

function handleLogout() {
    global $auth;
    $auth->logout();
    echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
}

function getCurrentUser() {
    global $auth;
    
    if ($auth->isUserAuthenticated()) {
        $user = $auth->getCurrentUser();
        echo json_encode([
            'success' => true,
            'user' => $user
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Not authenticated'
        ]);
    }
}

function handleChangePassword() {
    global $auth;
    
    $data = json_decode(file_get_contents('php://input'), true);
    $current_password = $data['current_password'] ?? '';
    $new_password = $data['new_password'] ?? '';
    
    if (!$auth->isUserAuthenticated()) {
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        return;
    }
    
    $user = $auth->getCurrentUser();
    $result = $auth->changePassword($user['id'], $current_password, $new_password);
    echo json_encode($result);
}
?>
