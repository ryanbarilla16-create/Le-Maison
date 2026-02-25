<?php

namespace LeMaison\Core;

/**
 * Authentication Service
 * Centralizes all user authentication and registration logic
 */
class Auth {
    private $pdo;
    private $session;

    public function __construct($pdo, $session) {
        $this->pdo = $pdo;
        $this->session = $session;
    }

    public function login($email, $password) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, email, password_hash, full_name, role, status, is_active
                FROM users 
                WHERE email = :email
            ");
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($password, $user['password_hash'])) {
                return ['success' => false, 'error' => 'Invalid email or password'];
            }

            if (isset($user['status']) && $user['status'] !== 'active') {
                return ['success' => false, 'error' => 'Account is not active'];
            }

            if (isset($user['is_active']) && !$user['is_active']) {
                return ['success' => false, 'error' => 'Account is deactivated'];
            }

            // Create session
            if ($this->session->create($user['id'])) {
                return [
                    'success' => true,
                    'user' => [
                        'id' => $user['id'],
                        'email' => $user['email'],
                        'name' => $user['full_name'] ?? $user['first_name'] . ' ' . $user['last_name'],
                        'role' => $user['role']
                    ]
                ];
            }

            return ['success' => false, 'error' => 'Session creation failed'];
        } catch (\PDOException $e) {
            error_log('Login error: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Internal server error'];
        }
    }

    public function register($email, $password, $name, $phone = '') {
        try {
            // Check if exists
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            if ($stmt->fetch()) {
                return ['success' => false, 'error' => 'Email already registered'];
            }

            $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            
            // Handle both table schemas (some versions use full_name, some first/last)
            // Let's assume the most recent one uses full_name based on AuthManager
            $sql = "INSERT INTO users (email, password_hash, full_name, phone, role, status) 
                    VALUES (:email, :hash, :name, :phone, 'customer', 'active')";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':email' => $email,
                ':hash' => $hash,
                ':name' => $name,
                ':phone' => $phone
            ]);

            $user_id = $this->pdo->lastInsertId();
            $this->session->create($user_id);

            return ['success' => true, 'message' => 'Registration successful'];
        } catch (\PDOException $e) {
            error_log('Registration error: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Registration failed'];
        }
    }

    public function logout() {
        $this->session->destroy();
    }

    public function getCurrentUser() {
        $userId = $this->session->getUserId();
        if (!$userId) return null;

        try {
            $stmt = $this->pdo->prepare("SELECT id, email, full_name, role, avatar_url FROM users WHERE id = :id");
            $stmt->execute([':id' => $userId]);
            $user = $stmt->fetch();
            
            if ($user && isset($user['full_name'])) {
                $parts = explode(' ', $user['full_name'], 2);
                $user['first_name'] = $parts[0] ?? '';
                $user['last_name'] = $parts[1] ?? '';
            }
            return $user;
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function isAuthenticated() {
        return $this->session->isAuthenticated();
    }

    public function isUserAuthenticated() {
        return $this->isAuthenticated();
    }
}
