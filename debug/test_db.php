<?php
require_once __DIR__ . '/../config/bootstrap.php';

use LeMaison\Core\Database;

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    $stmt = $pdo->query('SELECT current_database(), current_user, version()');
    $result = $stmt->fetch();
    
    echo "Connection Successful!\n";
    echo "Database: " . $result['current_database'] . "\n";
    echo "User: " . $result['current_user'] . "\n";
    echo "Version: " . $result['version'] . "\n";
} catch (Exception $e) {
    echo "Connection Failed: " . $e->getMessage() . "\n";
    exit(1);
}
