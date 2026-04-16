<?php
// includes/auth.php
require_once __DIR__ . '/../config/db.php'; // Include connection & config

/**
 * Check if the user is currently logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Validate if currently logged in user is a Host
 */
function isHost() {
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'host';
}

/**
 * Validate if currently logged in user is a Traveler
 */
function isTraveler() {
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'traveler';
}

/**
 * Helper strictly enforcing login state. Redirects if unauthenticated.
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['error_msg'] = "You must be logged in to view this page.";
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}

/**
 * Guard routes making sure user explicitly has specific roles
 * @param string $role The required role (e.g. 'host', 'traveler')
 */
function requireRole($role) {
    requireLogin(); // Must be logged in first

    if ($_SESSION['user_role'] !== $role) {
        // Log unauthorized attempt if needed, standard response below
        $_SESSION['error_msg'] = "You do not have permission to view that page.";
        header('Location: ' . BASE_URL . ($_SESSION['user_role'] === 'host' ? '/host-dashboard' : '/dashboard'));
        exit;
    }
}

/**
 * Load the current user object directly from the DB
 * returns False if the user isn't logged in, or the current user record.
 */
function getCurrentUser($pdo) {
    if (isLoggedIn()) {
        $stmt = $pdo->prepare("SELECT id, name, email, role, avatar, bio, nationality FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
    return false;
}
