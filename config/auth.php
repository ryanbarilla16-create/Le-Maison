<?php
/**
 * Authentication Functions
 * Handles user login, registration, logout, and session management
 */

// Ensure database connection is available
if (!isset($pdo)) {
    $pdo = require_once __DIR__ . '/database.php';
}

class AuthManager {
    private $pdo;
    private $session_duration = 86400 * 7; // 7 days
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    /**
     * Register a new user
     */
    public function register($email, $password, $full_name) {
        try {
            // Check if user exists
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            
            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'error' => 'Email already registered'];
            }
            
            // Hash password
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            
            // Insert user
            $stmt = $this->pdo->prepare("
                INSERT INTO users (email, password_hash, full_name, role)
                VALUES (:email, :password, :name, 'customer')
            ");
            
            $stmt->execute([
                ':email' => $email,
                ':password' => $password_hash,
                ':name' => $full_name
            ]);
            
            $user_id = $this->pdo->lastInsertId();
            
            // Create session
            $this->createSession($user_id);
            
            return ['success' => true, 'user_id' => $user_id];
            
        } catch (PDOException $e) {
            error_log('Registration error: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Registration failed'];
        }
    }
    
    /**
     * Login user
     */
    public function login($email, $password) {
        try {
            // Get user
            $stmt = $this->pdo->prepare("
                SELECT id, password_hash, full_name, email, avatar_url, role, status
                FROM users 
                WHERE email = :email AND status = 'active'
            ");
            
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                return ['success' => false, 'error' => 'Invalid email or password'];
            }
            
            // Verify password
            if (!password_verify($password, $user['password_hash'])) {
                return ['success' => false, 'error' => 'Invalid email or password'];
            }
            
            // Create session
            $this->createSession($user['id'], $user);
            
            return [
                'success' => true,
                'user_id' => $user['id'],
                'name' => $user['full_name'],
                'email' => $user['email'],
                'role' => $user['role']
            ];
            
        } catch (PDOException $e) {
            error_log('Login error: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Login failed'];
        }
    }
    
    /**
     * Create session
     */
    private function createSession($user_id, $user_data = null) {
        // Get user data if not provided
        if (!$user_data) {
            $stmt = $this->pdo->prepare("
                SELECT id, email, full_name, avatar_url, role
                FROM users WHERE id = :id
            ");
            $stmt->execute([':id' => $user_id]);
            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        // Store in $_SESSION
        $_SESSION['user_id'] = $user_id;
        $_SESSION['email'] = $user_data['email'];
        $_SESSION['user_name'] = $user_data['full_name'];
        $_SESSION['user_avatar'] = $user_data['avatar_url'];
        $_SESSION['user_role'] = $user_data['role'];
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();
        
        // Store in database
        try {
            $session_id = session_id();
            $expires_at = date('Y-m-d H:i:s', time() + $this->session_duration);
            
            $stmt = $this->pdo->prepare("
                INSERT INTO sessions (id, user_id, data, expires_at)
                VALUES (:id, :user_id, :data, :expires_at)
                ON CONFLICT (id) DO UPDATE SET data = :data, expires_at = :expires_at
            ");
            
            $stmt->execute([
                ':id' => $session_id,
                ':user_id' => $user_id,
                ':data' => json_encode($_SESSION),
                ':expires_at' => $expires_at
            ]);
        } catch (PDOException $e) {
            error_log('Session storage error: ' . $e->getMessage());
        }
    }
    
    /**
     * Logout user
     */
    public function logout() {
        // Delete session from database
        try {
            $stmt = $this->pdo->prepare("DELETE FROM sessions WHERE id = :id");
            $stmt->execute([':id' => session_id()]);
        } catch (PDOException $e) {
            error_log('Session deletion error: ' . $e->getMessage());
        }
        
        // Clear session
        session_destroy();
    }
    
    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && $_SESSION['logged_in'] === true;
    }
    
    /**
     * Get current user
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['email'],
            'name' => $_SESSION['user_name'],
            'avatar' => $_SESSION['user_avatar'],
            'role' => $_SESSION['user_role']
        ];
    }
    
    /**
     * Update user profile
     */
    public function updateProfile($user_id, $data) {
        try {
            $allowed_fields = ['full_name', 'phone', 'address', 'city', 'barangay', 'street', 'avatar_url'];
            $update_parts = [];
            $bind_params = [':id' => $user_id];
            
            foreach ($data as $field => $value) {
                if (in_array($field, $allowed_fields)) {
                    $update_parts[] = "$field = :$field";
                    $bind_params[":$field"] = $value;
                }
            }
            
            if (empty($update_parts)) {
                return ['success' => false, 'error' => 'No valid fields to update'];
            }
            
            $sql = "UPDATE users SET " . implode(', ', $update_parts) . ", updated_at = CURRENT_TIMESTAMP WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($bind_params);
            
            // Update session
            if (isset($data['full_name'])) {
                $_SESSION['user_name'] = $data['full_name'];
            }
            if (isset($data['avatar_url'])) {
                $_SESSION['user_avatar'] = $data['avatar_url'];
            }
            
            return ['success' => true];
            
        } catch (PDOException $e) {
            error_log('Profile update error: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Update failed'];
        }
    }
}

?>
