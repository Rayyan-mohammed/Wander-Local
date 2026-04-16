<?php
// pages/blog.php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

$category = $_GET['c'] ?? '';
$search = $_GET['q'] ?? '';
$localist = (int)($_GET['localist'] ?? 0);
$page = (int)($_GET['p'] ?? 1);
$limit = 6;
$offset = ($page - 1) * $limit;

$params = [];
$whereStr = "WHERE bp.status = 'published'";

if ($category && $category !== 'All') {
    $whereStr .= " AND bp.category = ?";
    $params[] = $category;
}

if ($search) {
    $whereStr .= " AND (bp.title LIKE ? OR bp.excerpt LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($localist > 0) {
    $whereStr .= " AND bp.author_id = ?";
    $params[] = $localist;
}

// Ensure columns exist (Development helper if table doesn't have likes count)
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS blog_likes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post_id INT NOT NULL,
        user_id INT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        UNIQUE(post_id, user_id)
    )");
} catch (Exception $e) {}

try {
    // Total count for pagination
    $cStmt = $pdo->prepare("SELECT COUNT(*) FROM blog_posts bp $whereStr");
    $cStmt->execute($params);
    $total_posts = $cStmt->fetchColumn();
    $total_pages = ceil($total_posts / $limit);

    // Fetch posts
    $pQuery = "
        SELECT bp.*, u.name as author_name, u.avatar as author_avatar, u.role as author_role,
               (SELECT COUNT(*) FROM blog_likes bl WHERE bl.post_id = bp.id) as likes_count
        FROM blog_posts bp
        JOIN users u ON bp.author_id = u.id
        $whereStr
        ORDER BY bp.created_at DESC
        LIMIT $limit OFFSET $offset
    ";
    $pStmt = $pdo->prepare($pQuery);
    $pStmt->execute($params);
    $posts = $pStmt->fetchAll(PDO::FETCH_ASSOC);

    // Trending posts (top 4 by views in last 30 days roughly, assuming views is lifetime we'll just sort by views)
    $tStmt = $pdo->query("
        SELECT bp.title, bp.slug, bp.cover_image, bp.created_at
        FROM blog_posts bp
        WHERE bp.status = 'published' AND bp.created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 30 DAY)
        ORDER BY bp.views DESC
        LIMIT 4
    ");
    $trending = $tStmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$trending) {
        $trending = $pdo->query("SELECT title, slug, cover_image, created_at FROM blog_posts WHERE status='published' ORDER BY views DESC LIMIT 4")->fetchAll(PDO::FETCH_ASSOC);
    }

    $activeLocalist = null;
    if ($localist > 0) {
        $activeLocalistStmt = $pdo->prepare("SELECT id, name FROM users WHERE id = ? AND role = 'host'");
        $activeLocalistStmt->execute([$localist]);
        $activeLocalist = $activeLocalistStmt->fetch(PDO::FETCH_ASSOC);
    }

    // Categories Breakdown
    $catStmt = $pdo->query("SELECT category, COUNT(*) as cnt FROM blog_posts WHERE status = 'published' AND category IS NOT NULL GROUP BY category ORDER BY cnt DESC");
    $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

    // Tags Breakdown
    $tagStmt = $pdo->query("SELECT tags FROM blog_posts WHERE status = 'published' AND tags IS NOT NULL");
    $allTags = [];
    while ($row = $tagStmt->fetchColumn()) {
        $tags = array_map('trim', explode(',', $row));
        foreach ($tags as $t) {
            if ($t) {
                $allTags[$t] = ($allTags[$t] ?? 0) + 1;
            }
        }
    }
    arsort($allTags);
    $popularTags = array_slice(array_keys($allTags), 0, 15);

    // Featured Host (Random host)
    $fhStmt = $pdo->query("
        SELECT u.id, u.name, u.avatar, hp.city, hp.cover_photo 
        FROM users u 
        JOIN host_profiles hp ON u.id = hp.user_id 
        WHERE u.role = 'host' 
        ORDER BY RAND() LIMIT 1
    ");
    $featuredHost = $fhStmt->fetch(PDO::FETCH_ASSOC);

    // Most active localists by published stories
    $localistsStmt = $pdo->query(" 
        SELECT u.id, u.name, u.avatar, hp.city, COUNT(bp.id) as post_count
        FROM users u
        JOIN blog_posts bp ON bp.author_id = u.id AND bp.status = 'published'
        LEFT JOIN host_profiles hp ON hp.user_id = u.id
        WHERE u.role = 'host'
        GROUP BY u.id, u.name, u.avatar, hp.city
        ORDER BY post_count DESC, u.name ASC
        LIMIT 5
    ");
    $topLocalists = $localistsStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

$catsList = ['All', 'Food', 'Adventure', 'Culture', 'Hidden Gems', 'Host Stories', 'Traveler Diaries'];
$featuredPost = $page === 1 && empty($search) && empty($category) && !empty($posts) ? array_shift($posts) : null;
?>

<div class="bg-light py-5" x-data="blogApp()">
    <div class="container py-3">
        <!-- Header -->
        <div class="text-center mb-5 pb-3">
            <h1 class="display-4 fw-bold font-heading text-dark mb-3">Stories from the Road</h1>
            <p class="text-muted fs-5 mb-0">Discover local tips, travel diaries, and hidden gems.</p>
            <?php if (!empty($activeLocalist)): ?>
                <div class="mt-3">
                    <span class="badge bg-primary bg-opacity-10 text-primary-custom px-3 py-2 rounded-pill">Filtered by localist: <?= htmlspecialchars($activeLocalist['name']) ?></span>
                    <a href="<?= BASE_URL ?>/pages/blog.php" class="btn btn-sm btn-outline-secondary rounded-pill ms-2">Clear</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white rounded-5 shadow-sm p-2 mb-5 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="d-flex overflow-auto w-100 px-3 pb-2 pb-md-0 hide-scrollbar" style="white-space: nowrap;">
                <?php foreach($catsList as $cat): ?>
                    <button class="btn <?= ($category ?: 'All') === $cat ? 'btn-primary' : 'btn-light text-muted' ?> rounded-pill fw-bold border-0 px-4 me-2 flex-shrink-0"
                            @click="setCategory('<?= htmlspecialchars($cat) ?>')">
                        <?= htmlspecialchars($cat) ?>
                    </button>
                <?php endforeach; ?>
            </div>
            <div class="w-100 w-md-auto pe-2">
                <form @submit.prevent="searchPosts" class="position-relative">
                    <i class="fa-solid fa-magnifying-glass position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input type="text" class="form-control rounded-pill bg-light border-0 ps-5" placeholder="Search stories..." x-model="searchQuery" style="width: 100%; max-width: 300px;">
                </form>
            </div>
        </div>

        <div class="row g-5">
            <!-- Main Content Area -->
            <div class="col-lg-8">
                
                <!-- Featured Post -->
                <?php if ($featuredPost): ?>
                <div class="card border-0 rounded-4 shadow-sm overflow-hidden mb-5 bg-white text-decoration-none d-block hover-shadow transition">
                    <div class="row g-0 h-100">
                        <div class="col-md-7">
                            <a href="<?= BASE_URL ?>/pages/post.php?slug=<?= $featuredPost['slug'] ?>">
                                <div class="bg-light h-100 w-100" style="min-height: 350px;">
                                    <?php if($featuredPost['cover_image']): ?>
                                        <img src="<?= htmlspecialchars($featuredPost['cover_image']) ?>" class="w-100 h-100 object-fit-cover" alt="Cover">
                                    <?php else: ?>
                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted"><i class="fa-solid fa-image fa-3x"></i></div>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-5 d-flex flex-column">
                            <div class="card-body p-4 d-flex flex-column h-100">
                                <span class="badge bg-primary bg-opacity-10 text-primary-custom rounded-pill align-self-start mb-3"><?= htmlspecialchars($featuredPost['category']) ?></span>
                                <a href="<?= BASE_URL ?>/pages/post.php?slug=<?= $featuredPost['slug'] ?>" class="text-decoration-none text-dark">
                                    <h2 class="fw-bold font-heading mb-3 lh-sm hover-primary transition"><?= htmlspecialchars($featuredPost['title']) ?></h2>
                                </a>
                                <p class="text-muted text-truncate-2 mb-4 flex-grow-1"><?= htmlspecialchars($featuredPost['excerpt']) ?></p>
                                
                                <div class="mt-auto pt-3 border-top d-flex flex-column gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?= htmlspecialchars($featuredPost['author_avatar'] ?: 'https://ui-avatars.com/api/?name='.urlencode($featuredPost['author_name'])) ?>" class="rounded-circle" width="40" height="40">
                                        <div>
                                            <div class="fw-bold text-dark mb-0 lh-1"><?= htmlspecialchars($featuredPost['author_name']) ?> <span class="badge bg-secondary ms-1 fw-normal" style="font-size:0.6rem;"><?= htmlspecialchars(ucfirst($featuredPost['author_role'])) ?></span></div>
                                            <span class="text-muted small"><?= date('M j, Y', strtotime($featuredPost['created_at'])) ?> · <?= intval($featuredPost['read_time_mins']) ?> min read</span>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="<?= BASE_URL ?>/pages/post.php?slug=<?= $featuredPost['slug'] ?>" class="btn btn-outline-primary rounded-pill fw-bold btn-sm px-3">Read Story</a>
                                        <div class="text-muted small fw-medium"><i class="fa-solid fa-heart text-danger me-1"></i> <?= intval($featuredPost['likes_count']) ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Grid Posts -->
                <div class="row g-4">
                    <?php if ($posts || $featuredPost): ?>
                        <?php foreach($posts as $post): ?>
                            <div class="col-md-6 col-xl-6">
                                <div class="card h-100 border-0 shadow-sm rounded-4 hover-shadow transition bg-white overflow-hidden">
                                    <a href="<?= BASE_URL ?>/pages/post.php?slug=<?= $post['slug'] ?>" class="text-decoration-none text-dark d-flex flex-column h-100">
                                        <div class="position-relative bg-light" style="aspect-ratio: 16/9;">
                                            <span class="badge bg-white text-dark position-absolute mt-3 ms-3 px-3 py-2 rounded-pill shadow-sm fw-bold" style="z-index: 2;"><?= htmlspecialchars($post['category']) ?></span>
                                            <?php if($post['cover_image']): ?>
                                                <img src="<?= htmlspecialchars($post['cover_image']) ?>" class="w-100 h-100 object-fit-cover">
                                            <?php else: ?>
                                                <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted"><i class="fa-solid fa-image fa-2x"></i></div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-body p-4 d-flex flex-column flex-grow-1">
                                            <h5 class="fw-bold font-heading mb-2 lh-sm hover-primary transition text-truncate-2"><?= htmlspecialchars($post['title']) ?></h5>
                                            <p class="text-muted text-truncate-2 small mb-3 flex-grow-1"><?= htmlspecialchars($post['excerpt']) ?></p>
                                            
                                            <div class="mt-auto d-flex justify-content-between align-items-center pt-3 border-top">
                                                <div class="d-flex align-items-center gap-2">
                                                    <img src="<?= htmlspecialchars($post['author_avatar'] ?: 'https://ui-avatars.com/api/?name='.urlencode($post['author_name'])) ?>" class="rounded-circle" width="30" height="30">
                                                    <div>
                                                        <div class="fw-bold small lh-1 mb-1 text-dark"><?= explode(' ', htmlspecialchars($post['author_name']))[0] ?></div>
                                                        <div class="text-muted" style="font-size:0.75rem;"><?= date('M j', strtotime($post['created_at'])) ?> · <?= intval($post['read_time_mins']) ?> min</div>
                                                    </div>
                                                </div>
                                                <div class="text-muted small fw-medium"><i class="fa-solid fa-heart text-danger me-1"></i> <?= intval($post['likes_count']) ?></div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 py-5 text-center text-muted">
                            <i class="fa-solid fa-pen-nib fa-3x mb-3 text-light"></i>
                            <h4>No stories found.</h4>
                            <p>Try refining your search or checking another category.</p>
                            <a href="<?= BASE_URL ?>/pages/blog.php" class="btn btn-outline-primary rounded-pill mt-2">Clear Filters</a>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <nav class="mt-5 pt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link shadow-none border-0 text-primary-custom fw-bold rounded-pill mx-1" href="?<?= http_build_query(array_merge($_GET, ['p' => max(1, $page-1)])) ?>">Previous</a>
                        </li>
                        <?php for($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link shadow-none border-0 rounded-circle text-center mx-1 fw-bold <?= $i == $page ? 'bg-primary text-white' : 'text-dark bg-white' ?>" 
                                   style="width: 40px; height: 40px; line-height: 24px;"
                                   href="?<?= http_build_query(array_merge($_GET, ['p' => $i])) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                            <a class="page-link shadow-none border-0 text-primary-custom fw-bold rounded-pill mx-1" href="?<?= http_build_query(array_merge($_GET, ['p' => min($total_pages, $page+1)])) ?>">Next</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>

            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                
                <!-- Trending This Week -->
                <div class="bg-white rounded-4 shadow-sm border p-4 mb-4">
                    <h5 class="fw-bold font-heading border-bottom pb-3 mb-4">Trending Stories</h5>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach($trending as $trend): ?>
                            <a href="<?= BASE_URL ?>/pages/post.php?slug=<?= $trend['slug'] ?>" class="text-decoration-none text-dark hover-primary transition d-flex gap-3 align-items-center">
                                <img src="<?= htmlspecialchars($trend['cover_image'] ?: 'https://via.placeholder.com/150') ?>" class="rounded-3 object-fit-cover" style="width: 80px; height: 60px;">
                                <div>
                                    <h6 class="fw-bold lh-sm mb-1 text-truncate-2" style="font-size: 0.95rem;"><?= htmlspecialchars($trend['title']) ?></h6>
                                    <div class="text-muted" style="font-size: 0.8rem;"><?= date('M j, Y', strtotime($trend['created_at'])) ?></div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Categories -->
                <div class="bg-white rounded-4 shadow-sm border p-4 mb-4">
                    <h5 class="fw-bold font-heading border-bottom pb-3 mb-4">Categories</h5>
                    <div class="d-flex flex-column gap-2">
                        <?php foreach($categories as $cat): ?>
                            <a href="<?= BASE_URL ?>/pages/blog.php?c=<?= urlencode($cat['category']) ?>" class="text-decoration-none d-flex justify-content-between align-items-center text-secondary hover-primary">
                                <span class="fw-medium"><?= htmlspecialchars($cat['category']) ?></span>
                                <span class="badge bg-light text-dark rounded-pill"><?= $cat['cnt'] ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Popular Tags -->
                <?php if ($popularTags): ?>
                <div class="bg-white rounded-4 shadow-sm border p-4 mb-4">
                    <h5 class="fw-bold font-heading border-bottom pb-3 mb-4">Popular Tags</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach($popularTags as $tag): ?>
                            <a href="#" class="badge bg-light text-secondary text-decoration-none border px-3 py-2 rounded-pill fw-medium hover-bg-primary transition">#<?= htmlspecialchars($tag) ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Top Localists -->
                <?php if (!empty($topLocalists)): ?>
                <div class="bg-white rounded-4 shadow-sm border p-4 mb-4">
                    <h5 class="fw-bold font-heading border-bottom pb-3 mb-4">Top Localist Writers</h5>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($topLocalists as $writer): ?>
                            <div class="d-flex align-items-center justify-content-between">
                                <a href="<?= BASE_URL ?>/pages/host.php?id=<?= (int)$writer['id'] ?>" class="text-decoration-none d-flex align-items-center gap-2 text-dark">
                                    <img src="<?= htmlspecialchars($writer['avatar'] ?: 'https://ui-avatars.com/api/?name=' . urlencode($writer['name'])) ?>" class="rounded-circle" width="34" height="34" alt="<?= htmlspecialchars($writer['name']) ?>">
                                    <div>
                                        <div class="fw-bold small"><?= htmlspecialchars($writer['name']) ?></div>
                                        <div class="text-muted" style="font-size: 0.75rem;"><?= htmlspecialchars($writer['city'] ?: 'Local Host') ?></div>
                                    </div>
                                </a>
                                <a href="<?= BASE_URL ?>/pages/blog.php?localist=<?= (int)$writer['id'] ?>" class="badge bg-light text-dark border text-decoration-none"><?= (int)$writer['post_count'] ?> stories</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Featured Host Widget -->
                <?php if ($featuredHost): ?>
                <div class="card border-0 rounded-4 shadow-sm overflow-hidden border">
                    <div class="position-relative" style="height: 120px;">
                        <img src="<?= htmlspecialchars($featuredHost['cover_photo'] ?: 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=600&q=80') ?>" class="w-100 h-100 object-fit-cover opacity-75">
                        <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-25"></div>
                    </div>
                    <div class="card-body text-center pt-0 position-relative pb-4">
                        <img src="<?= htmlspecialchars($featuredHost['avatar'] ?: 'https://ui-avatars.com/api/?name='.urlencode($featuredHost['name'])) ?>" class="rounded-circle border border-white border-4 shadow-sm mb-3 position-relative" style="width: 80px; height: 80px; top: -40px; margin-bottom: -40px;" alt="Host">
                        <h5 class="fw-bold font-heading mb-1 text-dark"><?= htmlspecialchars($featuredHost['name']) ?></h5>
                        <p class="text-muted small mb-3"><i class="fa-solid fa-location-dot me-1 text-primary-custom"></i> Local Host in <?= htmlspecialchars($featuredHost['city']) ?></p>
                        <a href="<?= BASE_URL ?>/pages/host.php?id=<?= $featuredHost['id'] ?>" class="btn btn-outline-primary rounded-pill btn-sm fw-bold px-4">View Profile</a>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<style>
.hide-scrollbar::-webkit-scrollbar { display: none; }
.hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
.text-truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.hover-primary:hover { color: var(--primary) !important; }
.hover-bg-primary:hover { background-color: var(--primary) !important; color: white !important; border-color: var(--primary) !important; }
.hover-shadow { transition: transform 0.2s, box-shadow 0.2s; }
.hover-shadow:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
</style>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('blogApp', () => ({
        searchQuery: '<?= addslashes($search) ?>',
        currentCategory: '<?= addslashes($category) ?>',
        
        setCategory(cat) {
            let url = new URL(window.location.href);
            if (cat === 'All') {
                url.searchParams.delete('c');
            } else {
                url.searchParams.set('c', cat);
            }
            url.searchParams.delete('p');
            window.location.href = url.href;
        },

        searchPosts() {
            let url = new URL(window.location.href);
            if (this.searchQuery) {
                url.searchParams.set('q', this.searchQuery);
            } else {
                url.searchParams.delete('q');
            }
            url.searchParams.delete('p');
            window.location.href = url.href;
        }
    }));
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>