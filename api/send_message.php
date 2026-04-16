<?php
// api/send_message.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to send a message.']);
    exit;
}

$user = getCurrentUser();
$input = json_decode(file_get_contents('php://input'), true);

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

// Safely ensure messages table exists or create it
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        sender_id INT NOT NULL,
        receiver_id INT NOT NULL,
        experience_id INT DEFAULT NULL,
        message_text TEXT NOT NULL,
        is_read BOOLEAN DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
    )");
} catch (Exception $e) {}


try {
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, experience_id, message_text) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user['id'], $receiver_id, $experience_id ?: null, trim($message_text)]);
    
    echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
}
