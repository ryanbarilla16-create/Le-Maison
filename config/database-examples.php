<?php
/**
 * Database Setup & Usage Guide
 * Le Maison de Yelo Lane - Neon PostgreSQL
 */

// ================================
// HOW TO USE THE DATABASE CONNECTION
// ================================

// 1. Load environment variables (if using a package like vlucas/phpdotenv)
// require_once __DIR__ . '/../vendor/autoload.php';
// $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
// $dotenv->load();

// 2. Get the PDO connection
$pdo = require_once __DIR__ . '/database.php';

// ================================
// EXAMPLE QUERIES
// ================================

// CREATE TABLE example
/*
$pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
        id SERIAL PRIMARY KEY,
        email VARCHAR(255) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        role VARCHAR(50) DEFAULT 'customer',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");
*/

// INSERT example
/*
$stmt = $pdo->prepare("
    INSERT INTO users (email, password_hash, role)
    VALUES (:email, :password, :role)
");

$stmt->execute([
    ':email' => 'user@example.com',
    ':password' => password_hash('secure_password', PASSWORD_BCRYPT),
    ':role' => 'customer'
]);
*/

// SELECT example
/*
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute([':email' => 'user@example.com']);
$user = $stmt->fetch();
*/

// UPDATE example
/*
$stmt = $pdo->prepare("
    UPDATE users SET role = :role WHERE id = :id
");

$stmt->execute([
    ':role' => 'admin',
    ':id' => 1
]);
*/

// DELETE example
/*
$stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
$stmt->execute([':id' => 1]);
*/

// ================================
// NEON SPECIFIC NOTES
// ================================

/*
 * Connection Features:
 * - SSL Mode: Required by default (secure connection)
 * - Connection Pooling: Uses pgBouncer for better performance
 * - Auto-Sleep: Inactive databases sleep after 7 days (free tier)
 * - Full PostgreSQL Support: Compatible with php-pgsql extension
 *
 * Best Practices:
 * 1. Use prepared statements to prevent SQL injection
 * 2. Always use environment variables for credentials
 * 3. Never commit .env file to version control
 * 4. Use parameterized queries (:param syntax)
 * 5. Handle exceptions properly
 * 6. Close connections when done (PDO auto-closes)
 */

?>
