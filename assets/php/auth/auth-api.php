<?php
/**
 * Authentication API Endpoint
 * Handles login, registration, logout, and profile updates
 */

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pdo = require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/auth.php';

$auth = new AuthManager($pdo);
$response = ['success' => false, 'error' => 'Invalid request'];

try {
    $action = $_GET['action'] ?? $_POST['action'] ?? null;
    $method = $_SERVER['REQUEST_METHOD'];
    
    switch ($action) {
        
        case 'login':
            if ($method !== 'POST') {
                $response = ['success' => false, 'error' => 'Method not allowed'];
                break;
            }
            
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                $response = ['success' => false, 'error' => 'Email and password required'];
                break;
            }
            
            $response = $auth->login($email, $password);
            break;
        
        case 'register':
            if ($method !== 'POST') {
                $response = ['success' => false, 'error' => 'Method not allowed'];
                break;
            }
            
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $full_name = $_POST['full_name'] ?? '';
            
            if (empty($email) || empty($password) || empty($full_name)) {
                $response = ['success' => false, 'error' => 'All fields required'];
                break;
            }
            
            $response = $auth->register($email, $password, $full_name);
            break;
        
        case 'logout':
            $auth->logout();
            $response = ['success' => true, 'message' => 'Logged out successfully'];
            break;
        
        case 'getCurrentUser':
            if (!$auth->isLoggedIn()) {
                $response = ['success' => false, 'error' => 'Not logged in'];
                break;
            }
            
            $user = $auth->getCurrentUser();
            $response = ['success' => true, 'user' => $user];
            break;
        
        case 'updateProfile':
            if (!$auth->isLoggedIn()) {
                $response = ['success' => false, 'error' => 'Not logged in'];
                break;
            }
            
            $user_id = $_SESSION['user_id'];
            $data = $_POST;
            unset($data['action']);
            
            $response = $auth->updateProfile($user_id, $data);
            break;
        
        default:
            $response = ['success' => false, 'error' => 'Invalid action'];
    }
    
} catch (Exception $e) {
    error_log('Auth API error: ' . $e->getMessage());
    $response = ['success' => false, 'error' => 'Server error'];
}

echo json_encode($response);
exit;

?>
