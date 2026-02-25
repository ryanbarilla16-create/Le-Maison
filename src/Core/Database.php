<?php

namespace LeMaison\Core;

use PDO;
use PDOException;

/**
 * Database Connection Handler
 * Manages connection to the PostgreSQL database
 */
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $db_host = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? null);
        $db_port = getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? '5432');
        $db_name = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? null);
        $db_user = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? null);
        $db_password = getenv('DB_PASSWORD') ?: ($_ENV['DB_PASSWORD'] ?? '');

        if (!$db_host || !$db_name || !$db_user) {
            die(json_encode([
                'success' => false,
                'error' => 'Missing database configuration',
                'details' => [
                    'host' => $db_host ? 'SET' : 'MISSING',
                    'name' => $db_name ? 'SET' : 'MISSING',
                    'user' => $db_user ? 'SET' : 'MISSING'
                ]
            ]));
        }

        $endpoint_id = explode('.', $db_host)[0];
        $dsn = "pgsql:host={$db_host};port={$db_port};dbname={$db_name};sslmode=require;options='endpoint={$endpoint_id}'";

        try {
            $this->pdo = new PDO(
                $dsn,
                $db_user,
                $db_password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            error_log('Database Connection Error: ' . $e->getMessage());
            die(json_encode(['success' => false, 'error' => 'Database connection failed']));
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }
}
