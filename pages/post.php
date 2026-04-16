<?php
// pages/post.php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) {
    header('Location: ' . BASE_URL . '/pages/blog.php');
    exit;
}

try {
    // Increment view count
    $vStmt = $pdo->prepare("UPDATE blog_posts SET views = views + 1 WHERE slug = ?");
    $vStmt->execute([$slug]);

    // Fetch the post
    $stmt = $pdo->prepare("
        SELECT bp.*, u.name as author_name, u.avatar as author_avatar, u.role as author_role, u.id as current_author_id
        FROM blog_posts bp
        JOIN users u ON bp.author_id = u.id
        WHERE bp.slug = ? AND bp.status = 'published'
    ");
    $stmt->execute([$slug]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        die("<div class='container py-5 text-center'><h1>Post not found</h1><a href='".BASE_URL."/pages/blog.php' class='btn btn-primary mt-3'>Back to Blog</a></div>");
    }

    // Check if user liked it
    $liked = false;
    $likes_count = 0;
    
    // Get total likes
    $lStmt = $pdo->prepare("SELECT COUNT(*) FROM blog_likes WHERE post_id = ?");
    $lStmt->execute([$post['id']]);
    $likes_count = $lStmt->fetchColumn();

    if (isset($_SESSION['user_id'])) {
        $uStmt = $pdo->prepare("SELECT id FROM blog_likes WHERE post_id = ? AND user_id = ?");
        $uStmt->execute([$post['id'], $_SESSION['user_id']]);
        $liked = (bool)$uStmt->fetchColumn();
    }

    // Fetch "You might also like" (Same category, distinct ID)
    $rStmt = $pdo->prepare("
        SELECT title, slug, cover_image, created_at 
        FROM blog_posts 
        WHERE category = ? AND id != ? AND status = 'published' 
        ORDER BY views DESC LIMIT 3
    ");
    $rStmt->execute([$post['category'], $post['id']]);
    $related = $rStmt->fetchAll(PDO::FETCH_ASSOC);

    $tags = array_filter(array_map('trim', explode(',', $post['tags'] ?? '')));

} catch (PDOException $e) {
    die("Database Error");
}

$currentUrl = urlencode("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
$shareTitle = urlencode($post['title']);

// Calculate approximate read time if not set
$word_count = str_word_count(strip_tags($post['content']));
$read_time = $post['read_time_mins'] ?: max(1, ceil($word_count / 200));

?>

<div x-data="postApp(<?= $post['id'] ?>, <?= $liked ? 'true' : 'false' ?>, <?= $likes_count ?>)">
    <!-- Article Header -->
    <header class="bg-dark text-white position-relative" style="min-height: 450px;">
        <!-- Background Image -->
        <?php if($post['cover_image']): ?>
            <div class="position-absolute w-100 h-100" style="background: url('<?= htmlspecialchars($post['cover_image']) ?>') center/cover no-repeat; opacity: 0.4;"></div>
        <?php else: ?>
            <div class="position-absolute w-100 h-100 bg-secondary opacity-50"></div>
        <?php endif; ?>
        
        <div class="container position-relative z-1 d-flex flex-column justify-content-center h-100 py-5" style="min-height: 450px;">
            <div class="row pt-5">
                <div class="col-lg-8 col-xl-7 mx-auto text-center">
                    <a href="<?= BASE_URL ?>/pages/blog.php?c=<?= urlencode($post['category']) ?>" class="badge bg-primary bg-opacity-75 text-white rounded-pill mb-3 px-3 py-2 text-decoration-none hover-bg-primary transition"><?= htmlspecialchars($post['category']) ?></a>
                    <h1 class="display-4 fw-bold font-heading mb-4 lh-sm shadow-sm"><?= htmlspecialchars($post['title']) ?></h1>
                    
                    <div class="d-flex align-items-center justify-content-center gap-3 text-white-50 flex-wrap">
                        <div class="d-flex align-items-center gap-2">
                            <img src="<?= htmlspecialchars($post['author_avatar'] ?: 'https://ui-avatars.com/api/?name='.urlencode($post['author_name'])) ?>" class="rounded-circle border border-white" width="40" height="40">
                            <span class="text-white fw-bold"><a href="<?= BASE_URL ?>/pages/host.php?id=<?= $post['current_author_id'] ?>" class="text-white text-decoration-none"><?= htmlspecialchars($post['author_name']) ?></a></span>
                        </div>
                        <span class="fs-5 pb-1">&middot;</span>
                        <span><i class="fa-regular fa-calendar me-1"></i> <?= date('F j, Y', strtotime($post['created_at'])) ?></span>
                        <span class="fs-5 pb-1">&middot;</span>
                        <span><i class="fa-regular fa-clock me-1"></i> <?= intval($read_time) ?> min read</span>
                        <span class="fs-5 pb-1">&middot;</span>
                        <span><i class="fa-regular fa-eye me-1"></i> <?= number_format($post['views']) ?> views</span>
                    </div>

                    <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['current_author_id']): ?>
                        <div class="mt-4">
                            <a href="<?= BASE_URL ?>/pages/write_post.php?id=<?= $post['id'] ?>" class="btn btn-outline-light rounded-pill btn-sm px-4"><i class="fa-solid fa-pen"></i> Edit Article</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <div class="container py-5">
        <div class="row">
            <!-- Social Share Left Column (Desktop) / Top (Mobile) -->
            <div class="col-lg-1 d-flex flex-lg-column gap-3 fs-5 justify-content-center justify-content-lg-start pt-lg-5 mb-4 mb-lg-0 order-2 order-lg-1 position-sticky top-0 h-100" style="top: 100px;">
                <a href="https://twitter.com/intent/tweet?url=<?= $currentUrl ?>&text=<?= $shareTitle ?>" target="_blank" class="text-secondary hover-primary transition"><i class="fa-brands fa-twitter bg-light rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:40px;height:40px;"></i></a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $currentUrl ?>" target="_blank" class="text-secondary hover-primary transition"><i class="fa-brands fa-facebook-f bg-light rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:40px;height:40px;"></i></a>
                <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= $currentUrl ?>&title=<?= $shareTitle ?>" target="_blank" class="text-secondary hover-primary transition"><i class="fa-brands fa-linkedin-in bg-light rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:40px;height:40px;"></i></a>
                <button @click="copyToClipboard" class="btn btn-link text-secondary hover-primary transition p-0 disabled-outline"><i class="fa-solid fa-link bg-light rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:40px;height:40px;"></i></button>
            </div>

            <!-- Content Column -->
            <div class="col-lg-8 order-1 order-lg-2">
                
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom d-lg-none">
                    <h5 class="fw-bold mb-0">Follow Story</h5>
                    <button @click="toggleLike" class="btn btn-light rounded-pill border fw-bold d-flex align-items-center gap-2" :class="{'text-danger': isLiked}">
                        <i class="fa-heart" :class="isLiked ? 'fa-solid' : 'fa-regular'"></i> <span x-text="likeCount"></span>
                    </button>
                </div>

                <!-- Trusted Content Render: Note htmlspecialchars_decode to render the editor's output -->
                <article class="blog-content fs-5 lh-lg text-dark mb-5" style="letter-spacing: -0.01em;">
                    <?= htmlspecialchars_decode($post['content']) ?>
                </article>

                <!-- Tags -->
                <?php if ($tags): ?>
                    <div class="d-flex flex-wrap gap-2 mb-5">
                        <span class="fw-bold me-2 py-1">Tags:</span>
                        <?php foreach($tags as $tag): ?>
                            <a href="<?= BASE_URL ?>/pages/blog.php?q=<?= urlencode($tag) ?>" class="badge bg-light text-secondary border px-3 py-2 text-decoration-none rounded-pill fw-medium hover-bg-primary transition">#<?= htmlspecialchars($tag) ?></a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Bottom Engagement Bar -->
                <div class="bg-light rounded-4 p-4 d-flex justify-content-between align-items-center mb-5">
                    <div class="d-flex align-items-center gap-3">
                        <button @click="toggleLike" class="btn btn-white rounded-circle shadow-sm d-flex align-items-center justify-content-center transition" style="width: 50px; height: 50px; font-size: 1.25rem;" :class="{'text-danger border-danger': isLiked, 'text-muted border': !isLiked}">
                            <i class="fa-heart transition" :class="isLiked ? 'fa-solid scale-up' : 'fa-regular scale-down'"></i>
                        </button>
                        <div>
                            <span class="fs-5 fw-bold d-block" x-text="likeCount"></span>
                            <span class="text-muted small">Likes</span>
                        </div>
                    </div>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <div class="text-muted small"><a href="<?= BASE_URL ?>/pages/auth/login.php" class="text-decoration-underline text-secondary">Log in</a> to like</div>
                    <?php endif; ?>
                </div>

                <!-- Author Bio Box -->
                <div class="card border-0 bg-white shadow-sm rounded-4 mb-5">
                    <div class="card-body p-4 p-lg-5">
                        <div class="d-flex flex-column flex-sm-row gap-4 align-items-center align-items-sm-start">
                            <img src="<?= htmlspecialchars($post['author_avatar'] ?: 'https://ui-avatars.com/api/?name='.urlencode($post['author_name'])) ?>" class="rounded-circle border" width="100" height="100">
                            <div>
                                <h4 class="fw-bold font-heading mb-1 text-center text-sm-start">Written by <?= htmlspecialchars($post['author_name']) ?> <span class="badge bg-primary bg-opacity-10 text-primary-custom ms-2 align-middle" style="font-size: 0.7rem;"><?= htmlspecialchars(ucfirst($post['author_role'])) ?></span></h4>
                                <p class="text-muted text-center text-sm-start">Passionate explorer and storyteller sharing local insights and hidden gems from around the world.</p>
                                <div class="text-center text-sm-start">
                                    <a href="<?= BASE_URL ?>/pages/host.php?id=<?= $post['current_author_id'] ?>" class="btn btn-outline-dark rounded-pill btn-sm fw-bold px-4">View Profile</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Sidebar Container -->
            <div class="col-lg-3 order-3 d-none d-lg-block">
                <!-- Desktop Like button floating -->
                <div class="bg-white rounded-4 shadow-sm border p-4 mb-4 text-center sticky-top" style="top: 100px;">
                    <h5 class="fw-bold font-heading mb-3 lh-sm">Enjoying the story?</h5>
                    <button @click="toggleLike" class="btn w-100 rounded-pill fw-bold py-2 mb-2 d-flex justify-content-center align-items-center gap-2 transition" :class="isLiked ? 'btn-danger' : 'btn-outline-danger'">
                        <i class="fa-heart" :class="isLiked ? 'fa-solid' : 'fa-regular'"></i> <span x-text="isLiked ? 'Liked' : 'Give a Like'"></span> (<span x-text="likeCount"></span>)
                    </button>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <small class="text-muted d-block mt-2"><a href="<?= BASE_URL ?>/pages/auth/login.php" class="text-secondary">Sign in</a> to show some love.</small>
                    <?php endif; ?>
                </div>
                
                <?php if ($related): ?>
                <div class="bg-white rounded-4 shadow-sm border p-4 sticky-top mb-4" style="top: 290px;">
                    <h5 class="fw-bold font-heading border-bottom pb-3 mb-4">You Might Also Like</h5>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach($related as $rel): ?>
                            <a href="<?= BASE_URL ?>/pages/post.php?slug=<?= $rel['slug'] ?>" class="text-decoration-none text-dark hover-primary transition">
                                <img src="<?= htmlspecialchars($rel['cover_image'] ?: 'https://via.placeholder.com/300x150') ?>" class="rounded-3 object-fit-cover w-100 mb-2" style="height: 120px;">
                                <h6 class="fw-bold lh-sm mb-1 text-truncate-2" style="font-size: 0.95rem;"><?= htmlspecialchars($rel['title']) ?></h6>
                                <div class="text-muted" style="font-size: 0.8rem;"><?= date('M j, Y', strtotime($rel['created_at'])) ?></div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>

        </div>

        <!-- Mobile Related -->
        <?php if ($related): ?>
        <div class="row mt-5 d-lg-none">
            <div class="col-12 border-top pt-5">
                <h3 class="fw-bold font-heading mb-4">You Might Also Like</h3>
                <div class="row g-4">
                    <?php foreach($related as $rel): ?>
                        <div class="col-md-4 col-sm-6">
                            <a href="<?= BASE_URL ?>/pages/post.php?slug=<?= $rel['slug'] ?>" class="text-decoration-none text-dark hover-primary transition card h-100 border-0 shadow-sm rounded-4">
                                <img src="<?= htmlspecialchars($rel['cover_image'] ?: 'https://via.placeholder.com/300x150') ?>" class="card-img-top object-fit-cover rounded-top-4" style="height: 150px;">
                                <div class="card-body">
                                    <h6 class="fw-bold lh-sm mb-1 text-truncate-2"><?= htmlspecialchars($rel['title']) ?></h6>
                                    <div class="text-muted small"><?= date('M j, Y', strtotime($rel['created_at'])) ?></div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<style>
.blog-content p { margin-bottom: 1.5rem; }
.blog-content h2, .blog-content h3 { font-weight: 700; margin-top: 2.5rem; margin-bottom: 1rem; color: #1a1a1a; font-family: 'Playfair Display', serif; }
.blog-content img { max-width: 100%; height: auto; border-radius: 0.75rem; margin: 2rem 0; box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075); }
.blog-content blockquote { font-style: italic; border-left: 4px solid var(--primary); padding-left: 1.5rem; margin: 2rem 0; color: #6c757d; font-size: 1.25rem; }
.blog-content a { color: var(--primary); text-decoration: underline; text-underline-offset: 3px; }
.blog-content a:hover { color: #0056b3; }
.blog-content ul, .blog-content ol { margin-bottom: 1.5rem; padding-left: 2rem; }
.blog-content li { margin-bottom: 0.5rem; }
.text-truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
.hover-primary:hover { color: var(--primary) !important; }
.hover-bg-primary:hover, .hover-bg-primary:focus { background-color: var(--primary) !important; color: white !important; border-color: var(--primary) !important; }
.disabled-outline:focus { box-shadow: none; outline: none; }
.scale-up { transform: scale(1.1); }
.scale-down { transform: scale(1); }
.transition { transition: all 0.2s ease-in-out; }
</style>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('postApp', (postId, initialLiked, initialCount) => ({
        postId: postId,
        isLiked: initialLiked,
        likeCount: initialCount,
        isLoggedIn: <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>,

        async toggleLike() {
            if (!this.isLoggedIn) {
                window.location.href = '<?= BASE_URL ?>/pages/auth/login.php';
                return;
            }

            try {
                const response = await fetch('<?= BASE_URL ?>/api/like_post.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ post_id: this.postId })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.isLiked = data.liked;
                    this.likeCount = data.likeCount;
                } else {
                    alert(data.error || 'Failed to like post.');
                }
            } catch (error) {
                console.error("Error toggling like:", error);
                alert('An error occurred. Please try again.');
            }
        },

        copyToClipboard() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                alert('Link copied to clipboard!');
            });
        }
    }));
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>