<?php
/**
 * Application Bootstrap
 * Initializes database connection and authentication system
 */

// Load configuration
$db_host = getenv('DB_HOST') ?: 'ep-wispy-dew-aigdgy1u-pooler.c-4.us-east-1.aws.neon.tech';
$db_port = getenv('DB_PORT') ?: '5432';
$db_name = getenv('DB_NAME') ?: 'neondb';
$db_user = getenv('DB_USER') ?: 'neondb_owner';
$db_password = getenv('DB_PASSWORD') ?: '';

$dsn = "pgsql:host={$db_host};port={$db_port};dbname={$db_name};sslmode=require";

try {
    $pdo = new PDO(
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
    die('Database connection failed. Please try again later.');
}

// Load class files
require_once __DIR__ . '/SessionHandler.php';
require_once __DIR__ . '/AuthHandler.php';

// Initialize components
$session = new SessionHandler($pdo);
$auth = new AuthHandler($pdo, $session);

// Make globally available
global $pdo, $session, $auth;
?>
