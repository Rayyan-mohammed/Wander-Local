<?php
// api/wishlist_toggle.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/security.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please log in to add to wishlist', 'redirect' => true]);
    exit;
}

$rateAction = 'api_wishlist_toggle';
if (!check_rate_limit($rateAction, 120, 3600)) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Too many requests. Please try again later.']);
    exit;
}
increment_rate_limit($rateAction);

$data = json_decode(file_get_contents('php://input'), true);
$csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? (is_array($data) ? ($data['csrf_token'] ?? '') : '');
if (!verify_csrf_token($csrfToken)) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
    exit;
}

$exp_id = isset($data['experience_id']) ? (int)$data['experience_id'] : 0;
$user_id = $_SESSION['user_id'];

if (!$exp_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid Experience ID']);
    exit;
}

try {
    // Check if already in wishlist
    $check = $pdo->prepare("SELECT id FROM wishlists WHERE user_id = ? AND experience_id = ?");
    $check->execute([$user_id, $exp_id]);
    
    if ($check->rowCount() > 0) {
        // Remove from wishlist
        $stmt = $pdo->prepare("DELETE FROM wishlists WHERE user_id = ? AND experience_id = ?");
        $stmt->execute([$user_id, $exp_id]);
        echo json_encode(['success' => true, 'action' => 'removed']);
    } else {
        // Add to wishlist
        $stmt = $pdo->prepare("INSERT INTO wishlists (user_id, experience_id) VALUES (?, ?)");
        $stmt->execute([$user_id, $exp_id]);
        echo json_encode(['success' => true, 'action' => 'added']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
