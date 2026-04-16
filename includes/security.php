<?php
// includes/security.php
if (session_status() === PHP_SESSION_NONE) {
    // Basic session security params before starting
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// 2-hour timeout
$timeout_duration = 7200;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['last_activity'] = time();

function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_input() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(generate_csrf_token()) . '">';
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function sanitize_input($data) {
    if (is_array($data)) {
        return array_map('sanitize_input', $data);
    }
    return htmlspecialchars(filter_var(trim($data), FILTER_UNSAFE_RAW), ENT_QUOTES, 'UTF-8');
}

function validate_upload($file_array, $max_mb = 5, $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp']) {
    if ($file_array['error'] !== UPLOAD_ERR_OK) return false;
    
    // Check size
    if ($file_array['size'] > ($max_mb * 1024 * 1024)) return false;
    
    // Check MIME type securely
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file_array['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime, $allowed_mimes)) return false;
    
    return true;
}

function check_rate_limit($action, $max_attempts = 5, $time_window = 900) {
    $key = 'rate_limit_' . $action;
    $time_key = $key . '_time';
    
    if (isset($_SESSION[$key]) && $_SESSION[$key] >= $max_attempts) {
        if (time() - $_SESSION[$time_key] < $time_window) {
            return false; // Locked out
        }
        $_SESSION[$key] = 0; // Reset
    }
    return true;
}

function increment_rate_limit($action) {
    $key = 'rate_limit_' . $action;
    $time_key = $key . '_time';
    $_SESSION[$key] = ($_SESSION[$key] ?? 0) + 1;
    $_SESSION[$time_key] = time();
}
