<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/auth/login.php');
    exit;
}

$userId = (int)$_SESSION['user_id'];
$page = max(1, (int)($_GET['p'] ?? 1));
$limit = 8;
$offset = ($page - 1) * $limit;

try {
    $countStmt = $pdo->prepare(" 
        SELECT COUNT(*)
        FROM blog_posts bp
        INNER JOIN localist_follows lf ON lf.localist_id = bp.author_id
        WHERE lf.follower_id = ?
          AND bp.status = 'published'
    ");
    $countStmt->execute([$userId]);
    $totalPosts = (int)$countStmt->fetchColumn();
    $totalPages = max(1, (int)ceil($totalPosts / $limit));

    $feedStmt = $pdo->prepare(" 
        SELECT bp.id, bp.title, bp.slug, bp.excerpt, bp.cover_image, bp.category, bp.created_at, bp.read_time_mins,
               u.id AS author_id, u.name AS author_name, u.avatar AS author_avatar,
               (SELECT COUNT(*) FROM blog_likes bl WHERE bl.post_id = bp.id) AS likes_count
        FROM blog_posts bp
        INNER JOIN localist_follows lf ON lf.localist_id = bp.author_id
        INNER JOIN users u ON u.id = bp.author_id
        WHERE lf.follower_id = ?
          AND bp.status = 'published'
        ORDER BY bp.created_at DESC
        LIMIT $limit OFFSET $offset
    ");
    $feedStmt->execute([$userId]);
    $posts = $feedStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('following.php: ' . $e->getMessage());
    $posts = [];
    $totalPosts = 0;
    $totalPages = 1;
}
?>

<div class="bg-light py-5 min-vh-100">
    <div class="container py-2">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h1 class="fw-bold font-heading mb-1">Following Feed</h1>
                <p class="text-muted mb-0">Latest stories from localists you follow.</p>
            </div>
            <a href="<?= BASE_URL ?>/pages/blog.php" class="btn btn-outline-primary rounded-pill fw-bold px-4">Discover More</a>
        </div>

        <?php if (!empty($posts)): ?>
            <div class="row g-4">
                <?php foreach ($posts as $post): ?>
                    <div class="col-md-6 col-xl-4">
                        <a href="<?= BASE_URL ?>/pages/post.php?slug=<?= urlencode($post['slug']) ?>" class="card h-100 border-0 shadow-sm rounded-4 text-decoration-none text-dark overflow-hidden hover-shadow transition">
                            <img src="<?= htmlspecialchars($post['cover_image'] ?: 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=900&q=80') ?>" alt="<?= htmlspecialchars($post['title']) ?>" class="w-100 object-fit-cover" style="height: 180px;">
                            <div class="card-body d-flex flex-column p-4">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-primary bg-opacity-10 text-primary-custom rounded-pill"><?= htmlspecialchars($post['category'] ?: 'Story') ?></span>
                                    <span class="text-muted small"><i class="fa-solid fa-heart text-danger me-1"></i><?= (int)$post['likes_count'] ?></span>
                                </div>

                                <h5 class="fw-bold mb-2 text-truncate-2"><?= htmlspecialchars($post['title']) ?></h5>
                                <p class="text-muted small text-truncate-3 mb-3 flex-grow-1"><?= htmlspecialchars($post['excerpt'] ?: 'New story from a localist you follow.') ?></p>

                                <div class="d-flex align-items-center gap-2 pt-3 border-top">
                                    <img src="<?= htmlspecialchars($post['author_avatar'] ?: 'https://ui-avatars.com/api/?name=' . urlencode($post['author_name'])) ?>" class="rounded-circle" width="34" height="34" alt="<?= htmlspecialchars($post['author_name']) ?>">
                                    <div class="small">
                                        <div class="fw-bold lh-1"><?= htmlspecialchars($post['author_name']) ?></div>
                                        <div class="text-muted mt-1"><?= date('M j, Y', strtotime($post['created_at'])) ?> · <?= (int)$post['read_time_mins'] ?> min</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <nav class="mt-5">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link border-0 rounded-pill mx-1" href="?<?= http_build_query(['p' => max(1, $page - 1)]) ?>">Previous</a>
                        </li>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                <a class="page-link border-0 rounded-circle mx-1 <?= $i === $page ? 'bg-primary text-white' : '' ?>" href="?<?= http_build_query(['p' => $i]) ?>" style="width: 40px; height: 40px; line-height: 24px;"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link border-0 rounded-pill mx-1" href="?<?= http_build_query(['p' => min($totalPages, $page + 1)]) ?>">Next</a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="bg-white border rounded-4 p-5 text-center shadow-sm">
                <i class="fa-solid fa-user-group fs-1 text-primary-custom mb-3"></i>
                <h4 class="fw-bold mb-2">Your feed is empty</h4>
                <p class="text-muted mb-4">Follow localists to get a personalized stream of fresh stories.</p>
                <a href="<?= BASE_URL ?>/pages/blog.php" class="btn btn-primary rounded-pill fw-bold px-4">Find Localists</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.text-truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.text-truncate-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
.hover-shadow { transition: transform 0.2s, box-shadow 0.2s; }
.hover-shadow:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
