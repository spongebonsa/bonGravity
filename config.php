<?php
// Config for BON Application

// Database Credentials
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'cans_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// App Constants
define('APP_NAME', 'BON');
define('APP_URL', 'http://127.0.0.1/bonGravity');
// Site description used for SEO meta tags
define('SITE_DESCRIPTION', 'Premium beverages made with pure ingredients. No sugar, no sweeteners, just natural goodness in every can.');
define('CURRENCY', '$');
// Exchange rates (simple constants). ETB -> USD multiplier
define('ETB_TO_USD', 0.018); // 1 ETB = 0.018 USD (adjust as needed)

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
