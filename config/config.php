<?php
// config.php - Database Configuration

// Use environment variables for security
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: 'password');
define('DB_NAME', getenv('DB_NAME') ?: 'pos_system');

// Session configuration
session_start();
define('SESSION_EXPIRE', 1800); // 30 minutes

// Error reporting - Enable only in development
define('DEV_MODE', getenv('DEV_MODE') === 'true');
if (DEV_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

?>