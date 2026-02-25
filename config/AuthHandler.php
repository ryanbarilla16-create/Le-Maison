<?php
/**
 * Authentication Handler
 * Manages user login, registration, and password reset
 */

class AuthHandler {
    private $pdo;
    private $session_handler;
    
    public function __construct($pdo, $session_handler) {
        $this->pdo = $pdo;
        $this->session_handler = $session_handler;
    }
    
    /**
     * Register a new user
     */
    public function register($email, $password, $first_name = '', $last_name = '', $phone = '') {
        // Validate email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }
        
        // Validate password strength
        if (strlen($password) < 8) {
            return ['success' => false, 'message' => 'Password must be at least 8 characters'];
        }
        
        try {
            // Check if email already exists
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Email already registered'];
            }
            
            // Hash password
            $password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            
            // Insert new user
            $stmt = $this->pdo->prepare("
                INSERT INTO users (email, password_hash, first_name, last_name, phone, username, role)
                VALUES (:email, :password_hash, :first_name, :last_name, :phone, :username, 'customer')
            ");
            
            $stmt->execute([
                ':email' => $email,
                ':password_hash' => $password_hash,
                ':first_name' => $first_name,
                ':last_name' => $last_name,
                ':phone' => $phone,
                ':username' => explode('@', $email)[0] . mt_rand(1000, 9999)
            ]);
            
            $user_id = $this->pdo->lastInsertId();
            
            // Create session
            $this->session_handler->createSession($user_id);
            
            return [
                'success' => true,
                'message' => 'Registration successful',
                'user_id' => $user_id
            ];
        } catch (PDOException $e) {
            error_log('Registration failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Registration failed. Please try again.'];
        }
    }
    
    /**
     * Login user
     */
    public function login($email, $password) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email format'];
        }
        
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, password_hash, is_active, role
                FROM users
                WHERE email = :email
            ");
            
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();
            
            if (!$user) {
                return ['success' => false, 'message' => 'Invalid email or password'];
            }
            
            if (!$user['is_active']) {
                return ['success' => false, 'message' => 'Account is inactive'];
            }
            
            // Verify password
            if (!password_verify($password, $user['password_hash'])) {
                return ['success' => false, 'message' => 'Invalid email or password'];
            }
            
            // Create session
            $this->session_handler->createSession($user['id']);
            
            return [
                'success' => true,
                'message' => 'Login successful',
                'user_id' => $user['id'],
                'role' => $user['role']
            ];
        } catch (PDOException $e) {
            error_log('Login failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Login failed. Please try again.'];
        }
    }
    
    /**
     * Change password
     */
    public function changePassword($user_id, $current_password, $new_password) {
        if (strlen($new_password) < 8) {
            return ['success' => false, 'message' => 'New password must be at least 8 characters'];
        }
        
        try {
            $stmt = $this->pdo->prepare("
                SELECT password_hash FROM users WHERE id = :id
            ");
            $stmt->execute([':id' => $user_id]);
            $user = $stmt->fetch();
            
            if (!$user) {
                return ['success' => false, 'message' => 'User not found'];
            }
            
            // Verify current password
            if (!password_verify($current_password, $user['password_hash'])) {
                return ['success' => false, 'message' => 'Current password is incorrect'];
            }
            
            // Hash new password
            $new_password_hash = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);
            
            // Update password
            $stmt = $this->pdo->prepare("
                UPDATE users 
                SET password_hash = :password_hash 
                WHERE id = :id
            ");
            
            $stmt->execute([
                ':password_hash' => $new_password_hash,
                ':id' => $user_id
            ]);
            
            return ['success' => true, 'message' => 'Password changed successfully'];
        } catch (PDOException $e) {
            error_log('Password change failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Password change failed'];
        }
    }
    
    /**
     * Request password reset (generates token)
     */
    public function requestPasswordReset($email) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id FROM users WHERE email = :email AND is_active = TRUE
            ");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();
            
            if (!$user) {
                // Don't reveal if email exists (security best practice)
                return ['success' => true, 'message' => 'If email exists, reset link will be sent'];
            }
            
            // Generate reset token
            $reset_token = bin2hex(random_bytes(32));
            $reset_expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour expiry
            
            // Store reset token in user record or separate table
            // For now, we'll use the session approach
            
            // TODO: Send email with reset link containing token
            
            return [
                'success' => true,
                'message' => 'Password reset link sent to your email',
                'token' => $reset_token
            ];
        } catch (PDOException $e) {
            error_log('Password reset request failed: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to process reset request'];
        }
    }
    
    /**
     * Verify user
     */
    public function isUserAuthenticated() {
        return $this->session_handler->isAuthenticated();
    }
    
    /**
     * Get current user
     */
    public function getCurrentUser() {
        return $this->session_handler->getUserData();
    }
    
    /**
     * Logout
     */
    public function logout() {
        $this->session_handler->logout();
    }
}
?>
