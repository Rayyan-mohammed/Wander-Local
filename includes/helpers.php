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

/**
 * Create a notification row.
 */
function createNotification($pdo, $recipientId, $actorId, $type, $title, $message, $url = null, $targetId = null) {
    $stmt = $pdo->prepare('INSERT INTO notifications (recipient_id, actor_id, type, title, message, url, target_id) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        (int)$recipientId,
        $actorId !== null ? (int)$actorId : null,
        (string)$type,
        (string)$title,
        (string)$message,
        $url,
        $targetId !== null ? (int)$targetId : null
    ]);
}

/**
 * Notify all followers that a localist published a new post.
 */
function notifyFollowersOfNewPost($pdo, $localistId, $postId, $postSlug, $postTitle) {
    $followersStmt = $pdo->prepare('SELECT follower_id FROM localist_follows WHERE localist_id = ?');
    $followersStmt->execute([(int)$localistId]);
    $followers = $followersStmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($followers)) {
        return;
    }

    $localistStmt = $pdo->prepare('SELECT name FROM users WHERE id = ? LIMIT 1');
    $localistStmt->execute([(int)$localistId]);
    $localistName = (string)($localistStmt->fetchColumn() ?: 'A localist');

    $checkStmt = $pdo->prepare('SELECT id FROM notifications WHERE recipient_id = ? AND type = ? AND target_id = ? AND actor_id = ? LIMIT 1');
    $insertStmt = $pdo->prepare('INSERT INTO notifications (recipient_id, actor_id, type, title, message, url, target_id) VALUES (?, ?, ?, ?, ?, ?, ?)');

    foreach ($followers as $followerId) {
        $checkStmt->execute([(int)$followerId, 'new_post', (int)$postId, (int)$localistId]);
        if ($checkStmt->fetchColumn()) {
            continue;
        }

        $shortTitle = strlen($postTitle) > 90 ? substr($postTitle, 0, 87) . '...' : $postTitle;

        $insertStmt->execute([
            (int)$followerId,
            (int)$localistId,
            'new_post',
            'New story from a localist you follow',
            $localistName . ' published "' . $shortTitle . '".',
            '/pages/post.php?slug=' . urlencode($postSlug),
            (int)$postId,
        ]);
    }
}
