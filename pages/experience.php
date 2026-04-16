<?php
// pages/experience.php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

$slug = $_GET['slug'] ?? '';
$user = isLoggedIn() ? getCurrentUser() : null;

if (!$slug) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>Experience Not Found</h1>";
    exit;
}

try {
    // Safely add columns if missing (development helper)
    $pdo->exec("ALTER TABLE experiences ADD COLUMN IF NOT EXISTS views INT DEFAULT 0");
    $pdo->exec("ALTER TABLE experiences ADD COLUMN IF NOT EXISTS included TEXT");
    $pdo->exec("ALTER TABLE experiences ADD COLUMN IF NOT EXISTS not_included TEXT");
} catch (Exception $e) {}

try {
    // 1. Increment View
    $stmt = $pdo->prepare("UPDATE experiences SET views = views + 1 WHERE slug = ?");
    $stmt->execute([$slug]);

    // 2. Fetch Experience with Host
    $stmt = $pdo->prepare("
        SELECT e.*, 
            u.name as host_name, u.avatar as host_avatar, u.id as host_id, u.is_verified, 
            hp.response_rate, hp.total_reviews as host_reviews
        FROM experiences e 
        JOIN users u ON e.host_id = u.id 
        LEFT JOIN host_profiles hp ON hp.user_id = u.id 
        WHERE e.slug = ?
    ");
    $stmt->execute([$slug]);
    $exp = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$exp) {
        header("HTTP/1.0 404 Not Found");
        echo "<div class='container py-5 text-center'><h1>Experience Not Found</h1><p>We couldn't find the requested experience.</p></div>";
        require_once __DIR__ . '/../includes/footer.php';
        exit;
    }

    // 3. Wishlist Status
    $is_wishlisted = false;
    if ($user) {
        $wStmt = $pdo->prepare("SELECT 1 FROM wishlists WHERE user_id = ? AND experience_id = ?");
        $wStmt->execute([$user['id'], $exp['id']]);
        $is_wishlisted = (bool)$wStmt->fetchColumn();
    }

    // 4. Fetch Images
    $imgStmt = $pdo->prepare("SELECT image_path FROM experience_images WHERE experience_id = ? ORDER BY sort_order ASC");
    $imgStmt->execute([$exp['id']]);
    $gallery = $imgStmt->fetchAll(PDO::FETCH_COLUMN);
    $allImages = array_merge([$exp['cover_image']], $gallery); // 1 main + others
    $allImages = array_filter($allImages);

    // 5. Fetch Itinerary
    $itinStmt = $pdo->prepare("SELECT * FROM experience_itinerary WHERE experience_id = ? ORDER BY step_number ASC");
    $itinStmt->execute([$exp['id']]);
    $itinerary = $itinStmt->fetchAll();

    // 6. Fetch Similar
    $simStmt = $pdo->prepare("SELECT e.*, (SELECT COUNT(*) FROM reviews r WHERE r.experience_id = e.id) as review_count FROM experiences e WHERE e.category = ? AND e.city = ? AND e.id != ? LIMIT 3");
    $simStmt->execute([$exp['category'], $exp['city'], $exp['id']]);
    $similar = $simStmt->fetchAll();

    // 7. Fetch Reviews with pagination
    $page = $_GET['page'] ?? 1;
    $limit = 5;
    $offset = ($page - 1) * $limit;
    
    $revStmt = $pdo->prepare("
        SELECT r.*, u.name, u.avatar, u.nationality 
        FROM reviews r 
        JOIN users u ON r.reviewer_id = u.id 
        WHERE r.experience_id = ? 
        ORDER BY r.created_at DESC 
        LIMIT ? OFFSET ?
    ");
    $revStmt->bindValue(1, $exp['id'], PDO::PARAM_INT);
    $revStmt->bindValue(2, $limit, PDO::PARAM_INT);
    $revStmt->bindValue(3, $offset, PDO::PARAM_INT);
    $revStmt->execute();
    $reviews = $revStmt->fetchAll();

    // Total reviews count & breakdown
    $rTotStmt = $pdo->prepare("SELECT COUNT(*) as cnt, rating FROM reviews WHERE experience_id = ? GROUP BY rating");
    $rTotStmt->execute([$exp['id']]);
    $rCounts = $rTotStmt->fetchAll(PDO::FETCH_KEY_PAIR);
    $totalReviews = array_sum($rCounts);
    
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

// Inclusions parsing (mock if blank)
$included = $exp['included'] ? array_map('trim', explode(',', $exp['included'])) : ['Expert guide', 'All taxes and fees'];
$not_included = $exp['not_included'] ? array_map('trim', explode(',', $exp['not_included'])) : ['Gratuities', 'Hotel pickup/drop-off'];
?>

<div class="bg-light py-4" x-data="experienceApp()">
    <div class="container">
        
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb small">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/pages/experiences.php" class="text-decoration-none">Experiences</a></li>
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/pages/experiences.php?city=<?= urlencode($exp['city']) ?>" class="text-decoration-none"><?= htmlspecialchars($exp['city']) ?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($exp['title']) ?></li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="mb-4">
            <span class="badge bg-primary bg-opacity-10 text-primary-custom mb-2 px-3 py-2 rounded-pill"><?= htmlspecialchars($exp['category']) ?></span>
            <h1 class="fw-bold font-heading mb-3"><?= htmlspecialchars($exp['title']) ?></h1>
            
            <div class="d-flex flex-wrap align-items-center gap-4 text-muted small fw-medium">
                <div class="d-flex align-items-center"><i class="fa-solid fa-location-dot me-2 text-primary-custom"></i> <?= htmlspecialchars($exp['city'] . ', ' . $exp['country']) ?></div>
                <div class="d-flex align-items-center"><i class="fa-regular fa-clock me-2 text-primary-custom"></i> <?= floatval($exp['duration_hours']) ?> Hours</div>
                <div class="d-flex align-items-center"><i class="fa-solid fa-users me-2 text-primary-custom"></i> Max <?= intval($exp['max_guests']) ?> Guests</div>
                <div class="d-flex align-items-center"><i class="fa-solid fa-language me-2 text-primary-custom"></i> <?= htmlspecialchars($exp['languages']) ?></div>
                <div class="d-flex align-items-center"><i class="fa-solid fa-star me-1 text-accent-custom"></i> <span class="text-dark fw-bold me-1"><?= number_format($exp['avg_rating'], 1) ?></span> (<?= intval($totalReviews) ?> Reviews)</div>
            </div>
        </div>

        <!-- Gallery -->
        <div class="row g-2 mb-5">
            <div class="col-md-8">
                <img :src="activeImage" class="img-fluid w-100 rounded-start-4 object-fit-cover cursor-pointer" style="height: 500px;" alt="Main Image" @click="openLightbox(0)">
            </div>
            <div class="col-md-4 d-none d-md-flex flex-column gap-2">
                <div class="row g-2 h-100">
                    <?php foreach (array_slice($allImages, 1, 4) as $idx => $img): ?>
                        <div class="col-6">
                            <img src="<?= htmlspecialchars($img) ?>" class="img-fluid w-100 object-fit-cover cursor-pointer <?= ($idx == 1 || $idx == 3) ? 'rounded-end-4' : '' ?>" style="height: 245px;" @mouseover="activeImage = '<?= addslashes($img) ?>'" @click="openLightbox(<?= $idx + 1 ?>)">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Layout -->
        <div class="row g-5">
            <!-- Main Content (65%) -->
            <div class="col-lg-8">
                
                <!-- Navigation Tabs -->
                <ul class="nav nav-tabs border-bottom mb-4 fs-5 fw-bold sticky-top bg-light" style="top:70px; z-index: 1000;" id="expTabs">
                    <?php foreach (['Overview', 'Itinerary', 'Included', 'Meeting Point', 'Reviews'] as $tId => $tab): ?>
                        <li class="nav-item">
                            <a class="nav-link text-dark border-0 border-bottom border-3 px-3 py-3" 
                               :class="activeTab === '<?= strtolower(str_replace([' ', "'"], '', $tab)) ?>' ? 'border-primary text-primary-custom' : 'border-transparent text-muted'" 
                               href="#" @click.prevent="activeTab = '<?= strtolower(str_replace([' ', "'"], '', $tab)) ?>'"><?= $tab ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Content Area -->
                <div class="tab-content" style="min-height: 400px;">
                    
                    <!-- Overview -->
                    <div x-show="activeTab === 'overview'" x-transition.opacity>
                        <h3 class="fw-bold mb-3 font-heading">About this local experience</h3>
                        <div class="text-secondary fs-5 lh-lg mb-5">
                            <?= nl2br(htmlspecialchars($exp['description'])) ?>
                        </div>
                    </div>

                    <!-- Itinerary -->
                    <div x-show="activeTab === 'itinerary'" x-transition.opacity style="display: none;">
                        <h3 class="fw-bold mb-4 font-heading">What you'll do</h3>
                        <div class="itinerary-timeline position-relative ps-4 py-2 border-start border-2 border-primary-subtle border-opacity-50 ms-3">
                            <?php if ($itinerary): foreach ($itinerary as $step): ?>
                                <div class="position-relative mb-5">
                                    <span class="position-absolute bg-primary rounded-circle border border-4 border-light" style="width: 20px; height: 20px; left: -34px; top: 4px;"></span>
                                    <h5 class="fw-bold mb-2">Step <?= $step['step_number'] ?>: <?= htmlspecialchars($step['title']) ?></h5>
                                    <p class="text-muted mb-0"><?= nl2br(htmlspecialchars($step['description'])) ?></p>
                                </div>
                            <?php endforeach; else: ?>
                                <p class="text-muted">Itinerary details will be provided by your host upon booking.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Included -->
                    <div x-show="activeTab === 'included'" x-transition.opacity style="display: none;">
                        <h3 class="fw-bold mb-4 font-heading">What's included</h3>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h5 class="text-dark fw-bold mb-3"><i class="fa-solid fa-check text-success me-2"></i> Included</h5>
                                <ul class="list-unstyled text-muted d-flex flex-column gap-2 mb-0">
                                    <?php foreach($included as $inc): ?>
                                        <li><i class="fa-solid fa-check text-success me-2 opacity-50"></i> <?= htmlspecialchars($inc) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5 class="text-dark fw-bold mb-3"><i class="fa-solid fa-xmark text-danger me-2"></i> Not Included</h5>
                                <ul class="list-unstyled text-muted d-flex flex-column gap-2 mb-0">
                                    <?php foreach($not_included as $nInc): ?>
                                        <li><i class="fa-solid fa-xmark text-danger me-2 opacity-50"></i> <?= htmlspecialchars($nInc) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Meeting Point -->
                    <div x-show="activeTab === 'meetingpoint'" x-transition.opacity style="display: none;">
                        <h3 class="fw-bold mb-4 font-heading">Where we'll meet</h3>
                        <div class="d-flex align-items-start gap-3 mb-4 rounded-3 border p-3">
                            <div class="bg-primary bg-opacity-10 text-primary-custom p-3 rounded-circle"><i class="fa-solid fa-location-dot fs-4"></i></div>
                            <div>
                                <h5 class="fw-bold mb-1">Meeting Address</h5>
                                <p class="text-muted mb-0"><?= htmlspecialchars($exp['meeting_point']) ?><br><?= htmlspecialchars($exp['city'] . ', ' . $exp['country']) ?></p>
                            </div>
                        </div>
                        <div class="w-100 bg-secondary rounded-4 overflow-hidden" style="height: 350px;">
                            <!-- Mock Iframe -->
                            <iframe width="100%" height="100%" frameborder="0" style="border:0" 
                                    src="https://maps.google.com/maps?q=<?= urlencode($exp['meeting_point'].' '.$exp['city']) ?>&output=embed" allowfullscreen></iframe>
                        </div>
                    </div>

                    <!-- Reviews -->
                    <div x-show="activeTab === 'reviews'" x-transition.opacity style="display: none;">
                        <h3 class="fw-bold mb-4 font-heading">Guest Reviews</h3>
                        <div class="row mb-5 align-items-center">
                            <div class="col-md-4 text-center border-end">
                                <div class="display-3 fw-bold font-heading text-dark"><?= number_format($exp['avg_rating'], 1) ?></div>
                                <div class="text-accent-custom fs-4 mb-2">
                                    <?php 
                                    $starVal = round($exp['avg_rating']);
                                    for($i=1; $i<=5; $i++) {
                                        echo $i <= $starVal ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>';
                                    }
                                    ?>
                                </div>
                                <div class="text-muted small">Based on <?= $totalReviews ?> reviews</div>
                            </div>
                            <div class="col-md-8 ps-md-5">
                                <?php for($i=5; $i>=1; $i--): 
                                    $c = $rCounts[$i] ?? 0;
                                    $pct = $totalReviews > 0 ? ($c / $totalReviews) * 100 : 0;
                                ?>
                                <div class="d-flex align-items-center gap-3 mb-2 text-muted small fw-medium">
                                    <span style="width: 30px;"><?= $i ?> <i class="fa-solid fa-star text-accent-custom"></i></span>
                                    <div class="progress flex-grow-1" style="height: 8px;">
                                        <div class="progress-bar bg-accent" role="progressbar" style="width: <?= $pct ?>%;" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <span style="width: 30px; text-align: right;"><?= $pct > 0 ? round($pct).'%' : '0%' ?></span>
                                </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <div class="d-flex flex-column gap-4">
                            <?php if ($reviews): foreach($reviews as $rev): ?>
                                <div class="border-bottom pb-4">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="<?= htmlspecialchars($rev['avatar'] ?: 'https://ui-avatars.com/api/?name='.urlencode($rev['name'])) ?>" class="rounded-circle" width="48" height="48">
                                            <div>
                                                <h6 class="fw-bold mb-0"><?= htmlspecialchars($rev['name']) ?></h6>
                                                <span class="text-muted small"><?= date('F Y', strtotime($rev['created_at'])) ?> · <?= htmlspecialchars($rev['nationality']) ?? '🌍' ?></span>
                                            </div>
                                        </div>
                                        <div class="text-accent-custom small">
                                            <?php for($i=1; $i<=5; $i++) echo $i <= $rev['rating'] ? '<i class="fa-solid fa-star"></i>' : '<i class="fa-regular fa-star"></i>'; ?>
                                        </div>
                                    </div>
                                    <p class="text-secondary mb-0"><?= nl2br(htmlspecialchars($rev['comment'])) ?></p>
                                </div>
                            <?php endforeach; else: ?>
                                <p class="text-center text-muted py-4">No reviews yet. Be the first to book!</p>
                            <?php endif; ?>
                        </div>

                        <!-- Pagination (Mock hrefs to reload page with tab selected) -->
                        <?php if ($totalReviews > 5): ?>
                        <nav class="mt-4">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>"><a class="page-link shadow-none border-0 text-primary-custom fw-bold rounded-pill" href="?slug=<?= $slug ?>&page=<?= max(1, $page-1) ?>#reviews" @click="activeTab='reviews'">Previous</a></li>
                                <li class="page-item <?= ($page*5) >= $totalReviews ? 'disabled' : '' ?>"><a class="page-link shadow-none border-0 text-primary-custom fw-bold rounded-pill" href="?slug=<?= $slug ?>&page=<?= $page+1 ?>#reviews" @click="activeTab='reviews'">Next</a></li>
                            </ul>
                        </nav>
                        <?php endif; ?>
                    </div>
                </div>

                <hr class="my-5">

                <!-- Host Profile Section -->
                <div class="bg-white p-4 rounded-4 border">
                    <h3 class="fw-bold mb-4 font-heading">Meet your Host, <?= explode(' ', htmlspecialchars($exp['host_name']))[0] ?></h3>
                    <div class="d-flex align-items-center gap-4 mb-4">
                        <img src="<?= htmlspecialchars($exp['host_avatar'] ?: 'https://ui-avatars.com/api/?name='.urlencode($exp['host_name'])) ?>" class="rounded-circle object-fit-cover shadow-sm" width="80" height="80">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h5 class="fw-bold mb-0"><?= htmlspecialchars($exp['host_name']) ?></h5>
                                <?php if($exp['is_verified']): ?><i class="fa-solid fa-circle-check fs-6 text-secondary-custom" title="Verified"></i><?php endif; ?>
                            </div>
                            <p class="text-muted small mb-0"><i class="fa-solid fa-star text-accent-custom me-1"></i> <?= intval($exp['host_reviews']) ?> Host Reviews</p>
                        </div>
                    </div>
                    <div class="d-flex gap-3">
                        <button class="btn btn-outline-primary fw-bold rounded-pill px-4" @click="msgModal = true">Message Host</button>
                        <a href="<?= BASE_URL ?>/pages/profile.php?id=<?= $exp['host_id'] ?>" class="btn btn-link text-muted text-decoration-none fw-medium">View Full Profile</a>
                    </div>
                </div>

            </div>

            <!-- Booking Sidebar (35%) -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-lg rounded-4 p-4 sticky-top bg-white" style="top: 100px; z-index: 10;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <span class="fs-3 fw-bold font-heading text-dark">$<span x-text="experience.price"></span></span>
                            <span class="text-muted">/ person</span>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-light rounded-circle shadow-sm" title="Share" @click="shareLink()"><i class="fa-solid fa-share-nodes"></i></button>
                            <button class="btn <?= $is_wishlisted ? 'btn-danger text-light' : 'btn-light text-muted' ?> rounded-circle shadow-sm" title="Wishlist" @click="toggleWishlist()"><i class="fa-heart <?= $is_wishlisted ? 'fa-solid' : 'fa-regular' ?>"></i></button>
                        </div>
                    </div>

                    <form @submit.prevent="checkoutModal = true">
                        <!-- Date Picker -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase text-muted">Select Date</label>
                            <input type="text" class="form-control form-control-lg bg-light border-0" id="bookingDateInput" placeholder="Select date" x-model="booking.date" required>
                        </div>

                        <!-- Guests -->
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Guests</label>
                            <div class="d-flex justify-content-between align-items-center bg-light rounded-3 p-2">
                                <button type="button" class="btn btn-white rounded-circle shadow-sm fw-bold border" @click="booking.guests > 1 ? booking.guests-- : null">-</button>
                                <span class="fs-5 fw-bold" x-text="booking.guests"></span>
                                <button type="button" class="btn btn-white rounded-circle shadow-sm fw-bold border" @click="booking.guests < experience.max_guests ? booking.guests++ : null">+</button>
                            </div>
                        </div>

                        <!-- Price Breakdown -->
                        <div class="bg-light p-3 rounded-3 mb-4 small fw-medium">
                            <div class="d-flex justify-content-between mb-2 text-muted">
                                <span>$<span x-text="experience.price"></span> x <span x-text="booking.guests"></span> guests</span>
                                <span>$<span x-text="(experience.price * booking.guests).toFixed(2)"></span></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 text-muted border-bottom pb-2">
                                <span>Platform fee (5%)</span>
                                <span>$<span x-text="((experience.price * booking.guests) * 0.05).toFixed(2)"></span></span>
                            </div>
                            <div class="d-flex justify-content-between fw-bold text-dark fs-5">
                                <span>Total</span>
                                <span>$<span x-text="totalPrice"></span></span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold rounded-pill shadow-sm">Reserve Now</button>
                        <p class="text-center text-muted small mt-3 mb-0">You won't be charged yet.</p>
                    </form>
                </div>
            </div>
        </div>

        <!-- Similar Experiences -->
        <?php if ($similar): ?>
        <div class="mt-5 pt-5">
            <h3 class="fw-bold font-heading mb-4 border-top pt-5">Similar experiences in <?= htmlspecialchars($exp['city']) ?></h3>
            <div class="row g-4">
                <?php foreach($similar as $sim): ?>
                    <div class="col-md-4">
                        <a href="<?= BASE_URL ?>/pages/experience.php?slug=<?= $sim['slug'] ?>" class="text-decoration-none">
                            <div class="card h-100 border-0 shadow-sm rounded-4 hover-shadow transition">
                                <div class="card-body p-0 position-relative">
                                    <span class="badge bg-white text-dark position-absolute mt-3 ms-3 px-3 py-2 rounded-pill shadow-sm fw-bold"><?= htmlspecialchars($sim['category']) ?></span>
                                    <img src="<?= htmlspecialchars($sim['cover_image']) ?>" class="w-100 object-fit-cover rounded-top-4" style="height: 200px;">
                                    <div class="p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="fw-bold mb-0 text-dark" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?= htmlspecialchars($sim['title']) ?></h5>
                                        </div>
                                        <div class="text-muted small fw-medium mb-3">
                                            <i class="fa-solid fa-star text-accent-custom me-1"></i> <span class="text-dark fw-bold"><?= number_format($sim['avg_rating'],1) ?></span> (<?= $sim['review_count'] ?>)
                                        </div>
                                        <div class="fw-bold text-dark fs-5">
                                            $<?= number_format($sim['price'], 2) ?> <span class="fs-6 fw-normal text-muted">/ pp</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <!-- BOOKING MODAL -->
    <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);" tabindex="-1" x-show="checkoutModal" x-transition.opacity>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold font-heading fs-4" x-show="!bookingSuccess">Complete your booking</h5>
                    <button type="button" class="btn-close shadow-none" @click="checkoutModal = false" x-show="!bookingSuccess"></button>
                </div>
                <div class="modal-body p-4 pt-3">
                    
                    <!-- Steps tracker -->
                    <div class="d-flex justify-content-between mb-4 position-relative" x-show="!bookingSuccess">
                        <div class="progress position-absolute w-100" style="height: 4px; top: 15px; z-index: 1;">
                            <div class="progress-bar bg-primary" :style="`width: ${((checkoutStep-1)/2)*100}%`"></div>
                        </div>
                        <template x-for="step in 3">
                            <div class="d-flex flex-column align-items-center position-relative" style="z-index: 2;">
                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 32px; height: 32px;" :class="checkoutStep >= step ? 'bg-primary text-white' : 'bg-light text-muted'"> <span x-text="step"></span></div>
                            </div>
                        </template>
                    </div>

                    <!-- Step 1: Summary -->
                    <div x-show="checkoutStep === 1 && !bookingSuccess">
                        <div class="card border mb-4 rounded-4 shadow-sm bg-light">
                            <div class="row g-0">
                                <div class="col-4">
                                    <img src="<?= htmlspecialchars($exp['cover_image']) ?>" class="img-fluid rounded-start-4 h-100 object-fit-cover">
                                </div>
                                <div class="col-8">
                                    <div class="card-body py-2 px-3">
                                        <h5 class="card-title fw-bold mb-1 lh-sm"><?= htmlspecialchars($exp['title']) ?></h5>
                                        <p class="text-muted small mb-2"><i class="fa-regular fa-calendar me-1"></i> <span x-text="booking.date"></span></p>
                                        <p class="text-muted small mb-2"><i class="fa-solid fa-users me-1"></i> <span x-text="booking.guests"></span> Guests</p>
                                        <div class="fw-bold fs-5 text-dark mt-2 border-top pt-2">Total: $<span x-text="totalPrice"></span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold text-muted small text-uppercase">Special Requests (Optional)</label>
                            <textarea class="form-control bg-light border-0" rows="3" placeholder="Tell the host about allergies, requests..." x-model="booking.requests"></textarea>
                        </div>
                        <button class="btn btn-primary w-100 fw-bold rounded-pill btn-lg" @click="checkoutStep = 2">Continue</button>
                    </div>

                    <!-- Step 2: Traveler Details -->
                    <form x-show="checkoutStep === 2 && !bookingSuccess" @submit.prevent="checkoutStep = 3">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small text-uppercase">Full Name</label>
                                <input type="text" class="form-control bg-light border-0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small text-uppercase">Nationality</label>
                                <select class="form-select bg-light border-0" required>
                                    <option value="" disabled selected>Select country</option>
                                    <option value="US">United States</option>
                                    <option value="UK">United Kingdom</option>
                                    <option value="CA">Canada</option>
                                    <option value="AU">Australia</option>
                                    <option value="OT">Other</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold text-muted small text-uppercase">Email</label>
                                <input type="email" class="form-control bg-light border-0" <?= $user ? "value='{$user['email']}'" : '' ?> required>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold text-muted small text-uppercase">Phone Number</label>
                                <input type="tel" class="form-control bg-light border-0" required>
                            </div>
                        </div>
                        <div class="d-flex gap-3">
                            <button type="button" class="btn btn-light fw-bold rounded-pill text-muted px-4" @click="checkoutStep = 1">Back</button>
                            <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill btn-lg">Continue to Payment</button>
                        </div>
                    </form>

                    <!-- Step 3: Mock Payment -->
                    <form x-show="checkoutStep === 3 && !bookingSuccess" @submit.prevent="processBooking()">
                        <div class="alert alert-info rounded-3 mb-4 border-0 text-center"><i class="fa-solid fa-lock me-2"></i> This is a mock payment gateway.</div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small text-uppercase">Card Number</label>
                            <input type="text" class="form-control bg-light border-0" placeholder="XXXX XXXX XXXX XXXX" maxlength="19" required>
                        </div>
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <label class="form-label fw-bold text-muted small text-uppercase">Expiry</label>
                                <input type="text" class="form-control bg-light border-0" placeholder="MM/YY" maxlength="5" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold text-muted small text-uppercase">CVV</label>
                                <input type="text" class="form-control bg-light border-0" placeholder="123" maxlength="4" required>
                            </div>
                        </div>
                        <div class="d-flex gap-3">
                            <button type="button" class="btn btn-light fw-bold rounded-pill text-muted px-4" @click="checkoutStep = 2">Back</button>
                            <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill btn-lg" :disabled="bookingLoading">
                                <span x-show="!bookingLoading">Complete Booking &bull; $<span x-text="totalPrice"></span></span>
                                <span x-show="bookingLoading"><i class="fa-solid fa-circle-notch fa-spin me-2"></i> Processing...</span>
                            </button>
                        </div>
                    </form>

                    <!-- Success State -->
                    <div x-show="bookingSuccess" class="text-center py-4" x-transition.opacity>
                        <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                            <i class="fa-solid fa-check fs-1"></i>
                        </div>
                        <h2 class="fw-bold font-heading mb-2">Booking Confirmed!</h2>
                        <p class="text-muted mb-4">You're all set for <?= htmlspecialchars($exp['title']) ?>. A confirmation email has been sent.</p>
                        
                        <div class="bg-light p-4 rounded-4 border mb-4 text-start">
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <div class="small fw-bold text-muted text-uppercase">Booking Ref</div>
                                    <div class="fw-bold text-dark fs-5" x-text="bookingRef"></div>
                                </div>
                                <div class="col-6 mb-3">
                                    <div class="small fw-bold text-muted text-uppercase">Date</div>
                                    <div class="fw-bold text-dark fs-5" x-text="booking.date"></div>
                                </div>
                                <div class="col-6">
                                    <div class="small fw-bold text-muted text-uppercase">Guests</div>
                                    <div class="fw-bold text-dark fs-5" x-text="booking.guests"></div>
                                </div>
                                <div class="col-6">
                                    <div class="small fw-bold text-muted text-uppercase">Total Paid</div>
                                    <div class="fw-bold text-dark fs-5">$<span x-text="totalPrice"></span></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-center gap-3">
                            <button class="btn btn-outline-primary fw-bold rounded-pill px-4" @click="window.print()"><i class="fa-solid fa-print me-2"></i> Download / Print</button>
                            <a href="<?= BASE_URL ?>/pages/experiences.php" class="btn btn-primary fw-bold rounded-pill px-4">Back to Experiences</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- MESSAGE HOST MODAL -->
    <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);" tabindex="-1" x-show="msgModal" x-transition.opacity>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title fw-bold font-heading">Message <?= explode(' ', htmlspecialchars($exp['host_name']))[0] ?></h5>
                    <button type="button" class="btn-close shadow-none" @click="msgModal = false; msgSuccess=false;"></button>
                </div>
                <div class="modal-body p-4 pt-0">
                    <form @submit.prevent="sendMessage()" x-show="!msgSuccess">
                        <textarea class="form-control bg-light border-0 mb-3 rounded-3" rows="4" placeholder="Hello! I have a question about this experience..." x-model="msgText" required></textarea>
                        <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill" :disabled="msgLoading">
                            <span x-show="!msgLoading">Send Message</span>
                            <span x-show="msgLoading"><i class="fa-solid fa-circle-notch fa-spin"></i></span>
                        </button>
                    </form>
                    <div class="text-center py-4" x-show="msgSuccess" x-transition>
                        <i class="fa-solid fa-paper-plane text-primary-custom fs-1 mb-3"></i>
                        <h5 class="fw-bold">Message Sent!</h5>
                        <p class="text-muted small">The host will reply to your inbox soon.</p>
                        <button class="btn btn-light fw-bold rounded-pill px-4 mt-2" @click="msgModal = false; msgSuccess = false">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- LIGHTBOX (Mock logic via Alpine) -->
    <div class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-75 z-3 d-flex align-items-center justify-content-center" style="z-index: 2000;" x-show="lightboxOpen" @click.self="lightboxOpen = false">
        <button class="btn btn-light rounded-circle position-absolute top-0 end-0 m-4 shadow" @click="lightboxOpen = false"><i class="fa-solid fa-xmark"></i></button>
        <div class="container text-center text-white">
            <img :src="activeImage" class="img-fluid rounded shadow" style="max-height: 80vh; max-width: 90vw;">
        </div>
    </div>

</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('experienceApp', () => ({
        activeTab: 'overview',
        activeImage: '<?= addslashes($allImages[0]) ?>',
        lightboxOpen: false,
        
        // Modals
        checkoutModal: false,
        checkoutStep: 1,
        bookingLoading: false,
        bookingSuccess: false,
        bookingRef: '',
        
        msgModal: false,
        msgText: '',
        msgLoading: false,
        msgSuccess: false,

        experience: {
            id: <?= $exp['id'] ?>,
            price: <?= $exp['price'] ?>,
            max_guests: <?= $exp['max_guests'] ?>
        },
        
        booking: {
            date: '',
            guests: 1,
            requests: ''
        },

        get totalPrice() {
            let base = this.experience.price * this.booking.guests;
            let fee = base * 0.05;
            return (base + fee).toFixed(2);
        },

        openLightbox(idx) {
            this.lightboxOpen = true;
            // A more complex integration with swiper.js for true lightbox logic could go here, 
            // for now setting activeImage displays the clicked image.
        },

        async toggleWishlist() {
            try {
                const res = await fetch(`<?= BASE_URL ?>/api/wishlist_toggle.php`, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({experience_id: this.experience.id})
                });
                const data = await res.json();
                if (data.redirect) {
                    window.location.href = `<?= BASE_URL ?>/pages/auth/login.php`;
                    return;
                }
                if (data.success) {
                    location.reload(); 
                }
            } catch (err) {
                console.error(err);
            }
        },

        shareLink() {
            navigator.clipboard.writeText(window.location.href);
            alert("Link copied to clipboard!");
        },

        async processBooking() {
            this.bookingLoading = true;
            try {
                const res = await fetch(`<?= BASE_URL ?>/api/create_booking.php`, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        experience_id: this.experience.id,
                        date: this.booking.date,
                        guests: this.booking.guests,
                        special_requests: this.booking.requests
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.bookingRef = data.booking_ref;
                    this.bookingSuccess = true;
                } else {
                    alert('Booking failed: ' + data.message);
                }
            } catch (err) {
                alert('Network error. Please try again.');
            } finally {
                this.bookingLoading = false;
            }
        },

        async sendMessage() {
            this.msgLoading = true;
            try {
                const res = await fetch(`<?= BASE_URL ?>/api/send_message.php`, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({
                        experience_id: this.experience.id,
                        receiver_id: <?= $exp['host_id'] ?>,
                        message: this.msgText
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.msgSuccess = true;
                    this.msgText = '';
                } else {
                    alert('Failed to send: ' + data.message);
                    if(data.message.includes('logged in')) window.location.href = '<?= BASE_URL ?>/pages/auth/login.php';
                }
            } catch (err) {
                alert('Network error. Please try again.');
            } finally {
                this.msgLoading = false;
            }
        }
    }));
});
</script>

<style>
/* Utilities */
.hover-shadow { transition: transform 0.2s, box-shadow 0.2s; }
.hover-shadow:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
.cursor-pointer { cursor: pointer; }
.border-transparent { border-color: transparent !important; }
.nav-tabs .nav-link { margin-bottom: -2px; }
.nav-tabs .nav-link:hover { border-color: transparent; }
</style>

<!-- Flatpickr CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        flatpickr("#bookingDateInput", {
            minDate: "today",
            dateFormat: "Y-m-d",
            onChange: function(selectedDates, dateStr, instance) {
                // Manually trigger alpine x-model update
                document.getElementById('bookingDateInput').dispatchEvent(new Event('input'));
            }
        });
    });
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>