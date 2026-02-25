<?php
/**
 * Session Handler
 * Manages user sessions stored in PostgreSQL
 */

class SessionHandler {
    private $pdo;
    private $session_timeout = 86400; // 24 hours
    private $session_id;
    private $user_id;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->startSession();
    }
    
    /**
     * Start a new session
     */
    public function startSession() {
        // Generate secure session ID
        $this->session_id = bin2hex(random_bytes(64));
        
        // Set session cookie
        setcookie(
            'le_maison_session',
            $this->session_id,
            [
                'expires' => time() + $this->session_timeout,
                'path' => '/',
                'secure' => true, // HTTPS only
                'httponly' => true, // JS cannot access
                'samesite' => 'Strict'
            ]
        );
    }
    
    /**
     * Create a new user session after login
     */
    public function createSession($user_id, $user_agent = '', $ip_address = '') {
        if (empty($user_agent)) {
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        }
        if (empty($ip_address)) {
            $ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
        
        $this->session_id = bin2hex(random_bytes(64));
        $expires_at = date('Y-m-d H:i:s', time() + $this->session_timeout);
        
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO sessions (id, user_id, user_agent, ip_address, expires_at, is_active)
                VALUES (:id, :user_id, :user_agent, :ip_address, :expires_at, TRUE)
            ");
            
            $stmt->execute([
                ':id' => $this->session_id,
                ':user_id' => $user_id,
                ':user_agent' => $user_agent,
                ':ip_address' => $ip_address,
                ':expires_at' => $expires_at
            ]);
            
            // Set session cookie
            setcookie(
                'le_maison_session',
                $this->session_id,
                [
                    'expires' => time() + $this->session_timeout,
                    'path' => '/',
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'Strict'
                ]
            );
            
            $this->user_id = $user_id;
            
            // Update user's last login
            $stmt = $this->pdo->prepare("
                UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = :id
            ");
            $stmt->execute([':id' => $user_id]);
            
            return true;
        } catch (PDOException $e) {
            error_log('Session creation failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Validate existing session
     */
    public function validateSession() {
        if (!isset($_COOKIE['le_maison_session'])) {
            return false;
        }
        
        $session_id = $_COOKIE['le_maison_session'];
        
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, user_id, expires_at, is_active 
                FROM sessions 
                WHERE id = :id AND is_active = TRUE
                AND expires_at > CURRENT_TIMESTAMP
            ");
            
            $stmt->execute([':id' => $session_id]);
            $session = $stmt->fetch();
            
            if ($session) {
                $this->session_id = $session['id'];
                $this->user_id = $session['user_id'];
                
                // Update last activity
                $stmt = $this->pdo->prepare("
                    UPDATE sessions 
                    SET last_activity = CURRENT_TIMESTAMP 
                    WHERE id = :id
                ");
                $stmt->execute([':id' => $session_id]);
                
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            error_log('Session validation failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get current user ID
     */
    public function getUserId() {
        return $this->user_id;
    }
    
    /**
     * Get current session ID
     */
    public function getSessionId() {
        return $this->session_id;
    }
    
    /**
     * Get user data
     */
    public function getUserData() {
        if (!$this->user_id) {
            return null;
        }
        
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, email, username, first_name, last_name, 
                       avatar_url, role, phone, created_at
                FROM users 
                WHERE id = :id AND is_active = TRUE
            ");
            
            $stmt->execute([':id' => $this->user_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Failed to get user data: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Check if user is authenticated
     */
    public function isAuthenticated() {
        return $this->validateSession();
    }
    
    /**
     * Check user role
     */
    public function hasRole($required_role) {
        $user = $this->getUserData();
        return $user && $user['role'] === $required_role;
    }
    
    /**
     * Check multiple roles
     */
    public function hasAnyRole($roles = []) {
        $user = $this->getUserData();
        return $user && in_array($user['role'], $roles);
    }
    
    /**
     * Logout user
     */
    public function logout() {
        if ($this->session_id) {
            try {
                $stmt = $this->pdo->prepare("
                    UPDATE sessions 
                    SET is_active = FALSE 
                    WHERE id = :id
                ");
                $stmt->execute([':id' => $this->session_id]);
            } catch (PDOException $e) {
                error_log('Logout failed: ' . $e->getMessage());
            }
        }
        
        // Clear cookie
        setcookie('le_maison_session', '', ['expires' => time() - 3600]);
        
        $this->session_id = null;
        $this->user_id = null;
    }
    
    /**
     * Clean up expired sessions (run periodically)
     */
    public function cleanupExpiredSessions() {
        try {
            $stmt = $this->pdo->prepare("
                DELETE FROM sessions 
                WHERE expires_at < CURRENT_TIMESTAMP 
                   OR (is_active = FALSE AND created_at < NOW() - INTERVAL '7 days')
            ");
            $stmt->execute();
            return $stmt->rowCount();
        } catch (PDOException $e) {
            error_log('Session cleanup failed: ' . $e->getMessage());
            return 0;
        }
    }
}
?>
