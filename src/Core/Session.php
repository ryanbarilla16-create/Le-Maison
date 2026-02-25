<?php

namespace LeMaison\Core;

/**
 * Session Manager
 * Handles persistent user sessions stored in the database
 */
class Session {
    private $pdo;
    private $session_timeout = 86400; // 24 hours
    private $user_id = null;
    private $session_id = null;
    private $cookie_name = 'le_maison_session';

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->initialize();
    }

    private function initialize() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_COOKIE[$this->cookie_name])) {
            $this->session_id = $_COOKIE[$this->cookie_name];
            $this->validate();
        }
    }

    public function create($user_id) {
        $this->session_id = bin2hex(random_bytes(64));
        $expires_at = date('Y-m-d H:i:s', time() + $this->session_timeout);
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

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

            $this->setCookie();
            $this->user_id = $user_id;

            // Sync with $_SESSION for legacy support
            $_SESSION['user_id'] = $user_id;
            $_SESSION['logged_in'] = true;

            return true;
        } catch (\PDOException $e) {
            error_log('Session creation failed: ' . $e->getMessage());
            return false;
        }
    }

    private function validate() {
        try {
            $stmt = $this->pdo->prepare("
                SELECT user_id, expires_at FROM sessions 
                WHERE id = :id AND is_active = TRUE AND expires_at > CURRENT_TIMESTAMP
            ");
            $stmt->execute([':id' => $this->session_id]);
            $session = $stmt->fetch();

            if ($session) {
                $this->user_id = $session['user_id'];
                
                // Keep session alive
                $stmt = $this->pdo->prepare("UPDATE sessions SET last_activity = CURRENT_TIMESTAMP WHERE id = :id");
                $stmt->execute([':id' => $this->session_id]);
                
                return true;
            }
        } catch (\PDOException $e) {
            error_log('Session validation error: ' . $e->getMessage());
        }

        $this->destroy();
        return false;
    }

    public function destroy() {
        if ($this->session_id) {
            try {
                $stmt = $this->pdo->prepare("UPDATE sessions SET is_active = FALSE WHERE id = :id");
                $stmt->execute([':id' => $this->session_id]);
            } catch (\PDOException $e) {}
        }

        setcookie($this->cookie_name, '', time() - 3600, '/');
        session_destroy();
        $this->session_id = null;
        $this->user_id = null;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function isAuthenticated() {
        return !is_null($this->user_id);
    }

    private function setCookie() {
        setcookie(
            $this->cookie_name,
            $this->session_id,
            [
                'expires' => time() + $this->session_timeout,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
            ]
        );
    }
}
