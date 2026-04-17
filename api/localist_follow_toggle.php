<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';
require_once __DIR__ . '/../includes/helpers.php';

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

$rateAction = 'api_localist_follow_toggle';
if (!check_rate_limit($rateAction, 120, 3600)) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Too many requests. Please try again later.']);
    exit;
}
increment_rate_limit($rateAction);

$currentUser = getCurrentUser($pdo);
if (!$currentUser) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$localistId = isset($input['localist_id']) ? (int)$input['localist_id'] : 0;
if ($localistId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid localist ID']);
    exit;
}

if ((int)$currentUser['id'] === $localistId) {
    echo json_encode(['success' => false, 'message' => 'You cannot follow yourself']);
    exit;
}

try {
    $localistStmt = $pdo->prepare("SELECT id FROM users WHERE id = ? AND role = 'host' AND is_active = 1");
    $localistStmt->execute([$localistId]);
    if (!$localistStmt->fetchColumn()) {
        echo json_encode(['success' => false, 'message' => 'Localist not found']);
        exit;
    }

    $checkStmt = $pdo->prepare('SELECT id FROM localist_follows WHERE follower_id = ? AND localist_id = ?');
    $checkStmt->execute([(int)$currentUser['id'], $localistId]);
    $existing = $checkStmt->fetchColumn();

    if ($existing) {
        $pdo->prepare('DELETE FROM localist_follows WHERE follower_id = ? AND localist_id = ?')
            ->execute([(int)$currentUser['id'], $localistId]);
        $following = false;
    } else {
        $pdo->prepare('INSERT INTO localist_follows (follower_id, localist_id) VALUES (?, ?)')
            ->execute([(int)$currentUser['id'], $localistId]);
        $following = true;

        createNotification(
            $pdo,
            $localistId,
            (int)$currentUser['id'],
            'new_follower',
            'You have a new follower',
            $currentUser['name'] . ' started following you.',
            '/pages/host.php?id=' . $localistId,
            (int)$currentUser['id']
        );
    }

    $countStmt = $pdo->prepare('SELECT COUNT(*) FROM localist_follows WHERE localist_id = ?');
    $countStmt->execute([$localistId]);
    $followersCount = (int)$countStmt->fetchColumn();

    echo json_encode([
        'success' => true,
        'following' => $following,
        'followers_count' => $followersCount,
    ]);
} catch (PDOException $e) {
    error_log('localist_follow_toggle.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
