<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        $errors[] = 'Invalid request token.';
    } else {
        $action = $_POST['action'] ?? '';

        try {
            if ($action === 'mark_all_read') {
                $stmt = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE recipient_id = ?');
                $stmt->execute([$userId]);
            }

            if ($action === 'mark_read') {
                $notificationId = (int)($_POST['notification_id'] ?? 0);
                if ($notificationId > 0) {
                    $stmt = $pdo->prepare('UPDATE notifications SET is_read = 1 WHERE id = ? AND recipient_id = ?');
                    $stmt->execute([$notificationId, $userId]);
                }
            }
        } catch (PDOException $e) {
            error_log('notifications.php: ' . $e->getMessage());
            $errors[] = 'Unable to update notifications right now.';
        }
    }
}

try {
    $notificationsStmt = $pdo->prepare(" 
        SELECT n.*, u.name AS actor_name, u.avatar AS actor_avatar
        FROM notifications n
        LEFT JOIN users u ON u.id = n.actor_id
        WHERE n.recipient_id = ?
        ORDER BY n.created_at DESC
        LIMIT 50
    ");
    $notificationsStmt->execute([$userId]);
    $notifications = $notificationsStmt->fetchAll(PDO::FETCH_ASSOC);

    $unreadStmt = $pdo->prepare('SELECT COUNT(*) FROM notifications WHERE recipient_id = ? AND is_read = 0');
    $unreadStmt->execute([$userId]);
    $unreadCount = (int)$unreadStmt->fetchColumn();
} catch (PDOException $e) {
    error_log('notifications.php: ' . $e->getMessage());
    $notifications = [];
    $unreadCount = 0;
    $errors[] = 'Unable to load notifications right now.';
}
?>

<div class="bg-light py-5 min-vh-100">
    <div class="container py-2" style="max-width: 920px;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <div>
                <h1 class="fw-bold font-heading mb-1">Notifications</h1>
                <p class="text-muted mb-0">You have <?= $unreadCount ?> unread notification<?= $unreadCount === 1 ? '' : 's' ?>.</p>
            </div>
            <?php if ($unreadCount > 0): ?>
                <form method="POST" class="m-0">
                    <?= csrf_input() ?>
                    <input type="hidden" name="action" value="mark_all_read">
                    <button type="submit" class="btn btn-outline-primary rounded-pill fw-bold px-4">Mark all as read</button>
                </form>
            <?php endif; ?>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger rounded-4 shadow-sm border-0">
                <?php foreach ($errors as $error): ?>
                    <div><?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($notifications)): ?>
            <div class="d-flex flex-column gap-3">
                <?php foreach ($notifications as $item): ?>
                    <div class="bg-white rounded-4 shadow-sm border p-3 p-md-4 d-flex justify-content-between align-items-start gap-3 <?= (int)$item['is_read'] === 0 ? 'notification-unread' : '' ?>">
                        <div class="d-flex gap-3 align-items-start">
                            <img src="<?= htmlspecialchars($item['actor_avatar'] ?: 'https://ui-avatars.com/api/?name=' . urlencode($item['actor_name'] ?: 'User')) ?>" alt="Avatar" class="rounded-circle" width="42" height="42">
                            <div>
                                <div class="fw-bold mb-1"><?= htmlspecialchars($item['title']) ?></div>
                                <div class="text-muted mb-2"><?= htmlspecialchars($item['message']) ?></div>
                                <div class="d-flex gap-2 align-items-center flex-wrap">
                                    <?php if (!empty($item['url'])): ?>
                                        <a href="<?= BASE_URL . htmlspecialchars($item['url']) ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3">Open</a>
                                    <?php endif; ?>
                                    <span class="text-muted small"><i class="fa-regular fa-clock me-1"></i><?= date('M j, Y g:i A', strtotime($item['created_at'])) ?></span>
                                </div>
                            </div>
                        </div>

                        <?php if ((int)$item['is_read'] === 0): ?>
                            <form method="POST" class="m-0">
                                <?= csrf_input() ?>
                                <input type="hidden" name="action" value="mark_read">
                                <input type="hidden" name="notification_id" value="<?= (int)$item['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-light border rounded-pill">Mark read</button>
                            </form>
                        <?php else: ?>
                            <span class="badge bg-light text-muted border rounded-pill">Read</span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-4 border shadow-sm p-5 text-center">
                <i class="fa-regular fa-bell fs-1 text-primary-custom mb-3"></i>
                <h4 class="fw-bold mb-2">No notifications yet</h4>
                <p class="text-muted mb-0">When localists you follow post stories, or when someone follows you, updates will appear here.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.notification-unread {
    border-left: 4px solid var(--primary);
    background: linear-gradient(90deg, rgba(13, 110, 253, 0.07), rgba(255, 255, 255, 1));
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
