<?php
/**
 * Neon PostgreSQL Database Configuration
 * Le Maison de Yelo Lane - Database Connection
 */

// Get database credentials from environment variables for security
$db_host = getenv('DB_HOST') ?: 'ep-wispy-dew-aigdgy1u-pooler.c-4.us-east-1.aws.neon.tech';
$db_port = getenv('DB_PORT') ?: '5432';
$db_name = getenv('DB_NAME') ?: 'neondb';
$db_user = getenv('DB_USER') ?: 'neondb_owner';
$db_password = getenv('DB_PASSWORD') ?: '';

// PostgreSQL DSN for PDO
$dsn = "pgsql:host={$db_host};port={$db_port};dbname={$db_name};sslmode=require";

try {
    // Create PDO connection with SSL enabled
    $pdo = new PDO(
        $dsn,
        $db_user,
        $db_password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );
    
    // Test connection
    $pdo->query('SELECT 1');
    
} catch (PDOException $e) {
    // Log error securely (not to user)
    error_log('Database Connection Error: ' . $e->getMessage());
    die('Database connection failed. Please try again later.');
}

// Return PDO object for use in application
return $pdo;
