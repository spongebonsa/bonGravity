<?php
// Config for BON Application

// Database Credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'cans_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// App Constants
define('APP_NAME', 'BON');
define('APP_URL', 'http://localhost/bonGravity');
define('CURRENCY', '$');

// Error Reporting (Turn off in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('UTC');

// Start Session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
