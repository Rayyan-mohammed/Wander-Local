<?php
// api/like_post.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Login required']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? (is_array($input) ? ($input['csrf_token'] ?? '') : '');
if (!verify_csrf_token($csrfToken)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

$user = getCurrentUser($pdo);
if (!$user) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

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

    echo json_encode(['success' => true, 'liked' => $is_liked, 'count' => $count, 'likeCount' => $count]);
} catch (PDOException $e) {
    error_log('like_post.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
