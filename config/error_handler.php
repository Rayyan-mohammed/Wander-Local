<?php
// config/error_handler.php
require_once __DIR__ . '/config.php';

function custom_error_handler($errno, $errstr, $errfile, $errline) {
    $log_message = date('[Y-m-d H:i:s]') . " Error [$errno]: $errstr in $errfile on line $errline\n";
    $log_dir = __DIR__ . '/../logs';
    if (!is_dir($log_dir)) mkdir($log_dir, 0777, true);
    error_log($log_message, 3, $log_dir . '/error.log');

    if (APP_ENV === 'development') {
        echo "<div style='border:1px solid red; padding:10px; margin:10px; background:#ffeeee;'>";
        echo "<b>Error [$errno]</b>: $errstr<br>File: $errfile<br>Line: $errline";
        echo "</div>";
    } else {
        // In production, redirect to friendly 500 page or just 404
        if ($errno == E_USER_ERROR || $errno == E_ERROR) {
            header('Location: ' . BASE_URL . '/pages/404.php');
            exit;
        }
    }
    return true;
}

set_error_handler("custom_error_handler");
