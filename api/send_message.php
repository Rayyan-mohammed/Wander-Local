<?php
// api/send_message.php
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
    echo json_encode(['success' => false, 'message' => 'You must be logged in to send a message.']);
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

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid Request']);
    exit;
}

$receiver_id = $input['receiver_id'] ?? null;
$experience_id = $input['experience_id'] ?? null;
$message_text = $input['message'] ?? '';

if (!$receiver_id || empty(trim($message_text))) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, experience_id, message_text) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user['id'], $receiver_id, $experience_id ?: null, trim($message_text)]);
    
    echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
} catch (PDOException $e) {
    error_log('send_message.php: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
