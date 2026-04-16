<?php
// config/config.php
define('APP_ENV', 'development'); // 'development' or 'production'
define('BASE_URL', APP_ENV === 'production' ? 'https://yourdomain.com' : 'http://localhost/Wander_Local');

if (APP_ENV === 'development') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}
// End config additions
session_start(); // Automatically start session globally

// ------------------------------------------------------------------------
// SITE CONSTANTS
// ------------------------------------------------------------------------
define('SITE_NAME', 'Wander Local');
define('BASE_URL', 'http://localhost/Wander_Local');
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// ------------------------------------------------------------------------
// DATABASE CONSTANTS
// ------------------------------------------------------------------------
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'wander_local');

// ------------------------------------------------------------------------
// PAGINATION LIMITS
// ------------------------------------------------------------------------
define('EXP_PER_PAGE', 12);
define('BLOG_PER_PAGE', 9);
define('REVIEWS_PER_PAGE', 5);
