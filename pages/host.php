<?php
// pages/host.php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

$host_id = $_GET['id'] ?? null;

if (!$host_id) {
    header("HTTP/1.0 404 Not Found");
    echo "<div class='container py-5 text-center'><h1>Host Not Found</h1></div>";
    require_once __DIR__ . '/../includes/footer.php';
    exit;
}

try {
    // Fetch host and profile
    $stmt = $pdo->prepare("
        SELECT u.id, u.name, u.avatar, u.created_at, u.is_verified, u.languages, u.bio, 
               hp.city, hp.country, hp.speciality_tags, hp.response_rate, hp.cover_photo
        FROM users u
        LEFT JOIN host_profiles hp ON u.id = hp.user_id
        WHERE u.id = ? AND u.role = 'host'
    ");
    $stmt->execute([$host_id]);
    $host = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$host) {
        header("HTTP/1.0 404 Not Found");
        echo "<div class='container py-5 text-center'><h1>Host Not Found</h1><p>We couldn't find a host with that ID.</p></div>";
        require_once __DIR__ . '/../includes/footer.php';
        exit;
    }

    // Fetch active experiences
    $expStmt = $pdo->prepare("
        SELECT e.*, 
               (SELECT COUNT(*) FROM reviews r WHERE r.experience_id = e.id) as review_count
        FROM experiences e 
        WHERE e.host_id = ? AND e.status = 'active'
    ");
    $expStmt->execute([$host_id]);
    $experiences = $expStmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch reviews for this host's experiences
    $revStmt = $pdo->prepare("
        SELECT r.*, u.name as reviewer_name, u.avatar as reviewer_avatar, u.nationality, 
               e.title as experience_title, e.slug
        FROM reviews r 
        JOIN users u ON r.reviewer_id = u.id 
        JOIN experiences e ON r.experience_id = e.id 
        WHERE e.host_id = ? 
        ORDER BY r.created_at DESC LIMIT 10
    ");
    $revStmt->execute([$host_id]);
    $reviews = $revStmt->fetchAll(PDO::FETCH_ASSOC);

    // Aggregates
    $aggStmt = $pdo->prepare("
        SELECT 
            (SELECT SUM(b.guest_count) 
             FROM bookings b JOIN experiences e ON b.experience_id = e.id 
             WHERE e.host_id = ? AND b.status IN ('confirmed', 'completed')) as travelers_hosted,
            (SELECT AVG(r.rating) 
             FROM reviews r JOIN experiences e ON r.experience_id = e.id 
             WHERE e.host_id = ?) as avg_rating,
            (SELECT COUNT(*) 
             FROM reviews r JOIN experiences e ON r.experience_id = e.id 
             WHERE e.host_id = ?) as total_reviews
    ");
    $aggStmt->execute([$host_id, $host_id, $host_id]);
    $stats = $aggStmt->fetch(PDO::FETCH_ASSOC);

    // Photo Gallery (Extracting all images from this host's experiences)
    $imgStmt = $pdo->prepare("
        SELECT ei.image_path, e.title 
        FROM experience_images ei 
        JOIN experiences e ON ei.experience_id = e.id 
        WHERE e.host_id = ? 
        LIMIT 10
    ");
    $imgStmt->execute([$host_id]);
    $gallery = $imgStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Supplement gallery with cover images if low
    if (count($gallery) < 4 && $experiences) {
        foreach($experiences as $e) {
            if ($e['cover_image']) {
                $gallery[] = ['image_path' => $e['cover_image'], 'title' => $e['title']];
            }
        }
        $gallery = array_unique($gallery, SORT_REGULAR);
    }

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

$languages = $host['languages'] ? array_map('trim', explode(',', $host['languages'])) : ['English'];
$specialities = $host['speciality_tags'] ? array_map('trim', explode(',', $host['speciality_tags'])) : [];
$coverPhoto = $host['cover_photo'] ?: 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=80';
$avatar = $host['avatar'] ?: 'https://ui-avatars.com/api/?name=' . urlencode($host['name']) . '&size=150';
?>

<!-- Cover & Intro Section -->
<div class="position-relative" style="margin-bottom: 80px;">
    <!-- Cover Photo -->
    <div class="w-100" style="height: 300px; background: url('<?= htmlspecialchars($coverPhoto) ?>') center/cover no-repeat;">
        <div class="w-100 h-100 bg-dark bg-opacity-25"></div>
    </div>
    
    <!-- Avatar overlapping -->
    <div class="container position-absolute start-0 end-0 translate-middle-y text-center text-md-start" style="bottom: -130px; z-index: 10;">
        <div class="d-md-flex align-items-end gap-4">
            <div class="position-relative d-inline-block">
                <img src="<?= htmlspecialchars($avatar) ?>" alt="<?= htmlspecialchars($host['name']) ?>" 
                     class="rounded-circle border border-white border-5 shadow" 
                     style="width: 150px; height: 150px; object-fit: cover; background: white;">
                <?php if ($host['is_verified']): ?>
                    <span class="position-absolute bottom-0 end-0 bg-success text-white rounded-circle d-flex align-items-center justify-content-center border border-white border-3" 
                          style="width: 32px; height: 32px; margin-bottom: 10px; margin-right: 10px;" title="Verified Local">
                        <i class="fa-solid fa-check small"></i>
                    </span>
                <?php endif; ?>
            </div>
            
            <div class="mb-2 mt-3 mt-md-0 pb-md-2 text-md-start text-center">
                <h1 class="fw-bold font-heading mb-1"><?= htmlspecialchars($host['name']) ?></h1>
                <p class="text-muted mb-0 fs-5">
                    <i class="fa-solid fa-location-dot me-1 text-primary-custom"></i> 
                    <?= htmlspecialchars(($host['city'] ?? 'Global') . ', ' . ($host['country'] ?? 'Citizen')) ?> 
                    <span class="mx-2">•</span> 
                    Member since <?= date('Y', strtotime($host['created_at'])) ?>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="bg-light py-5">
    <div class="container py-4">
        <div class="row g-5">
            <!-- Left Column (Bio & Details) -->
            <div class="col-lg-4">
                
                <!-- Quick Stats Box -->
                <div class="bg-white rounded-4 shadow-sm border p-4 mb-4">
                    <h5 class="fw-bold mb-3 font-heading">Host Stats</h5>
                    <ul class="list-unstyled d-flex flex-column gap-3 mb-0">
                        <li class="d-flex justify-content-between">
                            <span class="text-muted"><i class="fa-solid fa-suitcase-rolling w-20px text-primary-custom"></i> Experiences</span>
                            <span class="fw-bold"><?= count($experiences) ?></span>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span class="text-muted"><i class="fa-solid fa-star w-20px text-accent-custom"></i> Avg Rating</span>
                            <span class="fw-bold"><?= $stats['avg_rating'] ? number_format($stats['avg_rating'], 2) : 'New' ?></span>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span class="text-muted"><i class="fa-regular fa-face-smile w-20px text-success"></i> Travelers Hosted</span>
                            <span class="fw-bold"><?= intval($stats['travelers_hosted']) ?></span>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span class="text-muted"><i class="fa-solid fa-reply w-20px text-info"></i> Response Rate</span>
                            <span class="fw-bold"><?= htmlspecialchars($host['response_rate'] ?? 100) ?>%</span>
                        </li>
                    </ul>
                </div>

                <!-- Languages & Specialities -->
                <div class="bg-white rounded-4 shadow-sm border p-4 mb-4">
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3 font-heading">Languages</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach($languages as $lang): ?>
                            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fw-medium">
                                <i class="fa-solid fa-language text-primary-custom me-1"></i> <?= htmlspecialchars($lang) ?>
                            </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <?php if ($specialities): ?>
                    <div>
                        <h5 class="fw-bold mb-3 font-heading">Specialities</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach($specialities as $spec): ?>
                            <span class="badge bg-primary bg-opacity-10 text-primary-custom px-3 py-2 rounded-pill fw-medium">
                                <?= htmlspecialchars($spec) ?>
                            </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- About / Bio -->
                <div class="bg-white rounded-4 shadow-sm border p-4 mb-4">
                    <h5 class="fw-bold mb-3 font-heading">About <?= explode(' ', htmlspecialchars($host['name']))[0] ?></h5>
                    <div class="text-secondary lh-lg mb-0">
                        <?= nl2br(htmlspecialchars($host['bio'] ?: "Hi! I'm a local host passionate about showing travelers the authentic side of my city. I can't wait to welcome you on one of my experiences.")) ?>
                    </div>
                </div>

                <!-- Contact Button -->
                <button class="btn btn-outline-primary w-100 fw-bold rounded-pill shadow-sm" onclick="alert('Login required to message host')">Message Host</button>

            </div>

            <!-- Right Column (Experiences & Reviews) -->
            <div class="col-lg-8">
                
                <!-- Active Experiences Grid -->
                <div class="mb-5">
                    <h3 class="fw-bold mb-4 font-heading">Experiences by <?= explode(' ', htmlspecialchars($host['name']))[0] ?></h3>
                    <div class="row g-4">
                        <?php if ($experiences): foreach($experiences as $exp): ?>
                            <div class="col-md-6">
                                <a href="<?= BASE_URL ?>/pages/experience.php?slug=<?= htmlspecialchars($exp['slug']) ?>" class="text-decoration-none">
                                    <div class="card h-100 border-0 shadow-sm rounded-4 hover-shadow transition bg-white">
                                        <div class="card-body p-0 position-relative">
                                            <span class="badge bg-white text-dark position-absolute mt-3 ms-3 px-3 py-2 rounded-pill shadow-sm fw-bold"><?= htmlspecialchars($exp['category']) ?></span>
                                            <img src="<?= htmlspecialchars($exp['cover_image']) ?>" class="w-100 object-fit-cover rounded-top-4" style="height: 200px;">
                                            <div class="p-3">
                                                <h5 class="fw-bold mb-2 text-dark" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?= htmlspecialchars($exp['title']) ?></h5>
                                                <div class="text-muted small fw-medium mb-3">
                                                    <i class="fa-solid fa-star text-accent-custom me-1"></i> <span class="text-dark fw-bold"><?= number_format($exp['avg_rating'], 1) ?></span> (<?= $exp['review_count'] ?>) · <?= floatval($exp['duration_hours']) ?> Hours
                                                </div>
                                                <div class="fw-bold text-dark fs-5">
                                                    $<?= number_format($exp['price'], 2) ?> <span class="fs-6 fw-normal text-muted">/ pp</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; else: ?>
                            <div class="col-12"><p class="text-muted">No active experiences right now.</p></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Reviews Section -->
                <?php if ($reviews): ?>
                <div class="mb-5">
                    <h3 class="fw-bold mb-4 font-heading">Guest Reviews (<?= intval($stats['total_reviews']) ?>)</h3>
                    <div class="d-flex flex-column gap-4 bg-white p-4 rounded-4 shadow-sm border">
                        <?php foreach($reviews as $rev): ?>
                            <div class="border-bottom pb-4 last-no-border">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="d-flex gap-3">
                                        <img src="<?= htmlspecialchars($rev['reviewer_avatar'] ?: 'https://ui-avatars.com/api/?name='.urlencode($rev['reviewer_name'])) ?>" class="rounded-circle" width="50" height="50">
                                        <div>
                                            <h6 class="fw-bold mb-0"><?= htmlspecialchars($rev['reviewer_name']) ?></h6>
                                            <span class="text-muted small"><?= date('F Y', strtotime($rev['created_at'])) ?> · <?= htmlspecialchars($rev['nationality'] ?? '🌍') ?></span>
                                            <div class="mt-1 small fw-medium">
                                                <span class="text-muted">Reviewed:</span> <a href="<?= BASE_URL ?>/pages/experience.php?slug=<?= htmlspecialchars($rev['slug']) ?>" class="text-primary-custom text-decoration-none"><?= htmlspecialchars($rev['experience_title']) ?></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-accent-custom small">
                                        <?php for($i=1; $i<=5; $i++) echo $i <= $rev['rating'] ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>'; ?>
                                    </div>
                                </div>
                                <p class="text-secondary mb-0"><?= nl2br(htmlspecialchars($rev['comment'])) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Photo Gallery (Masonry CSS) -->
                <?php if ($gallery): ?>
                <div>
                    <h3 class="fw-bold mb-4 font-heading">Photos from <?= explode(' ', htmlspecialchars($host['name']))[0] ?>'s Experiences</h3>
                    <div class="masonry-wrap">
                        <?php foreach($gallery as $img): ?>
                        <div class="masonry-item mb-3">
                            <img src="<?= htmlspecialchars($img['image_path']) ?>" class="img-fluid rounded-3 shadow-sm w-100" alt="<?= htmlspecialchars($img['title']) ?>" title="<?= htmlspecialchars($img['title']) ?>">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<style>
.w-20px { width: 20px; text-align: center; }
.hover-shadow { transition: transform 0.2s, box-shadow 0.2s; }
.hover-shadow:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
.last-no-border:last-child { border-bottom: none !important; padding-bottom: 0 !important; }

/* Pure CSS Masonry */
.masonry-wrap {
    column-count: 2;
    column-gap: 1rem;
}
@media (max-width: 575.98px) {
    .masonry-wrap { column-count: 1; }
}
.masonry-item {
    break-inside: avoid;
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>