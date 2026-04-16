<?php
// api/like_post.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Login required']);
    exit;
}

$user = getCurrentUser();
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || empty($input['post_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$post_id = intval($input['post_id']);

try {
    // Check if liked
    $stmt = $pdo->prepare("SELECT id FROM blog_likes WHERE post_id = ? AND user_id = ?");
    $stmt->execute([$post_id, $user['id']]);
    $liked = $stmt->fetchColumn();

    if ($liked) {
        $pdo->prepare("DELETE FROM blog_likes WHERE post_id = ? AND user_id = ?")->execute([$post_id, $user['id']]);
        $is_liked = false;
    } else {
        $pdo->prepare("INSERT INTO blog_likes (post_id, user_id) VALUES (?, ?)")->execute([$post_id, $user['id']]);
        $is_liked = true;
    }

    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM blog_likes WHERE post_id = ?");
    $countStmt->execute([$post_id]);
    $count = $countStmt->fetchColumn();

    echo json_encode(['success' => true, 'liked' => $is_liked, 'count' => $count]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
