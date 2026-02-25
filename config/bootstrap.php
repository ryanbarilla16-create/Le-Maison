<?php
/**
 * Application Bootstrap
 * Initializes core components and handles autoloading
 */

// Load .env file
function loadEnv($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        putenv(sprintf('%s=%s', trim($name), trim($value)));
        $_ENV[trim($name)] = trim($value);
    }
}
loadEnv(dirname(__DIR__) . '/.env');

// Autoloader for LeMaison namespace
spl_autoload_register(function ($class) {
    $prefix = 'LeMaison\\';
    $base_dir = dirname(__DIR__) . '/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use LeMaison\Core\Database;
use LeMaison\Core\Session;
use LeMaison\Core\Auth;

// Initialize Core Components
$pdo = Database::getInstance()->getConnection();
$session = new Session($pdo);
$auth = new Auth($pdo, $session);

// Global access for legacy scripts
global $pdo, $session, $auth;

