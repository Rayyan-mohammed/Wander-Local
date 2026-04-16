<?php
// includes/helpers.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Set or get a flash message.
 * 
 * @param string $key The session key.
 * @param string|null $message The message to set (if null, gets and clears the message).
 * @return string|null The message if getting, or null if setting or not found.
 */
function flash($key, $message = null) {
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
    } else {
        if (isset($_SESSION['flash'][$key])) {
            $msg = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $msg;
        }
        return null;
    }
}

/**
 * Alias for getting a flash message.
 */
function getFlash($key) {
    return flash($key);
}
