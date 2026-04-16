<?php
// pages/index.php
require_once __DIR__ . '/../includes/header.php';

// Try querying active experiences, gracefully handle if empty
$experiences = [];
try {
    $stmt = $pdo->query("SELECT e.*, u.name as host_name, u.avatar_url as host_avatar FROM experiences e JOIN users u ON e.host_id = u.id LIMIT 6");
    if ($stmt) $experiences = $stmt->fetchAll();
} catch (Exception $e) {}

// Try querying verified hosts
$hosts = [];
try {
    $stmt = $pdo->query("SELECT hp.*, u.name, u.avatar_url as host_avatar, u.languages FROM host_profiles hp JOIN users u ON hp.user_id = u.id WHERE u.is_verified = 1 LIMIT 3");
    if ($stmt) $hosts = $stmt->fetchAll();
} catch (Exception $e) {}

// Try querying blog posts
$blogs = [];
try {
    $stmt = $pdo->query("SELECT b.*, u.name as author_name FROM blog_posts b JOIN users u ON b.author_id = u.id ORDER BY b.created_at DESC LIMIT 3");
    if ($stmt) $blogs = $stmt->fetchAll();
} catch (Exception $e) {}
?>

<!-- 1. HERO SECTION -->
<section class="hero-section">
    <div class="container text-center py-5" data-aos="fade-up" data-aos-duration="1000">
        <h1 class="display-3 font-heading fw-bold mb-4 text-shadow" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5);">Every Place Has a Soul. Find Yours.</h1>
        <p class="lead fw-normal text-white-50 mb-5 d-none d-md-block" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">Immerse yourself in new cultures with authentic experiences led by local insiders.</p>
        
        <div class="hero-search-box mx-auto d-flex flex-column flex-md-row align-items-center justify-content-between p-2 p-md-3 bg-white rounded-pill mb-5 w-100" style="max-width: 800px;">
            <form class="d-flex w-100 flex-column flex-md-row gap-2" action="<?= BASE_URL ?>/pages/search.php" method="GET">
                <div class="col-md-5 d-flex align-items-center ps-md-4 pb-2 pb-md-0 border-bottom border-md-0">
                    <i class="fa-solid fa-location-dot text-primary-custom me-2"></i>
                    <input type="text" name="location" class="form-control rounded-0 text-dark fw-medium" placeholder="Where to?">
                </div>
                <div class="col-md-4 d-flex align-items-center ps-md-3 border-start border-light pt-2 pt-md-0">
                    <i class="fa-solid fa-layer-group text-primary-custom me-2"></i>
                    <select name="category" class="form-select rounded-0 text-muted fw-medium">
                        <option value="">Any Category</option>
                        <option value="Culinary">Food & Drink</option>
                        <option value="Culture">History & Culture</option>
                        <option value="Nature">Nature & Outdoors</option>
                    </select>
                </div>
                <div class="col-md-3 pt-2 pt-md-0 ps-md-2">
                    <button type="submit" class="btn btn-primary w-100 h-100 rounded-pill fw-bold">Search</button>
                </div>
            </form>
        </div>

        <div class="d-flex flex-wrap justify-content-center gap-4 gap-md-5 mt-5 text-white fw-medium" data-aos="fade-up" data-aos-delay="200">
            <div class="d-flex align-items-center gap-2"><i class="fa-solid fa-users text-accent-custom fs-4"></i> 200+ Hosts</div>
            <div class="d-flex align-items-center gap-2"><i class="fa-solid fa-star text-accent-custom fs-4"></i> 500+ Experiences</div>
            <div class="d-flex align-items-center gap-2"><i class="fa-solid fa-map-location-dot text-accent-custom fs-4"></i> 48 Cities</div>
        </div>
    </div>
</section>

<!-- 2. HOW IT WORKS (Alpine.js Tabs) -->
<section class="py-5 my-5 bg-white" x-data="{ tab: 'traveler' }">
    <div class="container text-center">
        <h2 class="font-heading fw-bold mb-4" data-aos="fade-up">How WanderLocal Works</h2>
        
        <!-- Toggle -->
        <div class="d-inline-flex bg-light p-1 rounded-pill mb-5 shadow-sm" data-aos="fade-up" data-aos-delay="100">
            <button class="btn rounded-pill fw-bold px-4 transition" :class="tab === 'traveler' ? 'btn-primary' : 'btn-ghost'" @click="tab = 'traveler'">I'm a Traveler</button>
            <button class="btn rounded-pill fw-bold px-4 transition" :class="tab === 'host' ? 'btn-primary' : 'btn-ghost'" @click="tab = 'host'">I'm a Host</button>
        </div>

        <!-- Traveler Steps -->
        <div x-show="tab === 'traveler'" x-transition.opacity.duration.400ms class="row g-4 px-lg-5">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="p-4 custom-card h-100 text-center border-0 shadow-none bg-transparent">
                    <div class="bg-light rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 80px; height: 80px;">
                        <span class="fs-2 text-primary-custom fw-bold font-heading">1</span>
                    </div>
                    <h5 class="fw-bold">Search</h5>
                    <p class="text-muted">Enter any destination and discover curated genuine local experiences tailored just for you.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="p-4 custom-card h-100 text-center border-0 shadow-none bg-transparent">
                    <div class="bg-light rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 80px; height: 80px;">
                        <span class="fs-2 text-primary-custom fw-bold font-heading">2</span>
                    </div>
                    <h5 class="fw-bold">Book Instantly</h5>
                    <p class="text-muted">Secure your spot online. Chat safely with your host and prepare for an unforgettable adventure.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="400">
                <div class="p-4 custom-card h-100 text-center border-0 shadow-none bg-transparent">
                    <div class="bg-light rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 80px; height: 80px;">
                        <span class="fs-2 text-primary-custom fw-bold font-heading">3</span>
                    </div>
                    <h5 class="fw-bold">Immerse</h5>
                    <p class="text-muted">Meet the locals, taste the true flavors, and experience a city beyond the standard tour guide.</p>
                </div>
            </div>
        </div>

        <!-- Host Steps -->
        <div x-show="tab === 'host'" x-transition.opacity.duration.400ms class="row g-4 px-lg-5" style="display: none;">
            <div class="col-md-4">
                <div class="p-4 custom-card h-100 text-center border-0 shadow-none bg-transparent">
                    <div class="bg-light rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fa-solid fa-pen-nib fs-2 text-primary-custom"></i>
                    </div>
                    <h5 class="fw-bold">Design</h5>
                    <p class="text-muted">Share your passion. Create your unique experience, set competitive pricing and availability logic.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 custom-card h-100 text-center border-0 shadow-none bg-transparent">
                    <div class="bg-light rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fa-solid fa-calendar-check fs-2 text-primary-custom"></i>
                    </div>
                    <h5 class="fw-bold">Host</h5>
                    <p class="text-muted">Welcome eager travelers into your world. Manage bookings and communicate directly through our app.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 custom-card h-100 text-center border-0 shadow-none bg-transparent">
                    <div class="bg-light rounded-circle d-inline-flex justify-content-center align-items-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fa-solid fa-sack-dollar fs-2 text-primary-custom"></i>
                    </div>
                    <h5 class="fw-bold">Earn</h5>
                    <p class="text-muted">Get paid reliably to do what you love while showcasing your city's hidden culture and gems.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. FEATURED EXPERIENCES (Swiper.js) -->
<section class="py-5 my-5 bg-light position-relative">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4" data-aos="fade-right">
            <div>
                <span class="text-accent-custom fw-bold text-uppercase tracking-wide small">Top Activities</span>
                <h2 class="font-heading fw-bold mb-0">Trending Local Experiences</h2>
            </div>
            <a href="<?= BASE_URL ?>/pages/search.php" class="btn btn-outline-primary d-none d-md-inline-block">View All</a>
        </div>

        <div class="swiper experienceSwiper pb-5" data-aos="fade-up" data-aos-delay="100">
            <div class="swiper-wrapper">
                <?php if(!empty($experiences)): ?>
                    <?php foreach($experiences as $exp): ?>
                        <div class="swiper-slide">
                            <a href="<?= BASE_URL ?>/pages/experience_detail.php?id=<?= $exp['id'] ?>" class="text-decoration-none">
                                <div class="custom-card h-100 cursor-pointer">
                                    <div class="card-img-wrap position-relative">
                                        <span class="badge-category shadow-sm"><?= htmlspecialchars($exp['category'] ?? 'General') ?></span>
                                        <img src="<?= htmlspecialchars($exp['image_url'] ?? 'https://images.unsplash.com/photo-1517524008697-84bbe3c3fd98?auto=format&fit=crop&q=80&w=600') ?>" alt="<?= htmlspecialchars($exp['title']) ?>">
                                    </div>
                                    <div class="p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="fw-bold mb-0 text-dark text-truncate" style="max-width: 85%;"><?= htmlspecialchars($exp['title']) ?></h5>
                                            <span class="rating-stars fw-bold"><i class="fa-solid fa-star"></i> 4.9</span>
                                        </div>
                                        <p class="text-muted small mb-3"><i class="fa-solid fa-location-dot me-1"></i> <?= htmlspecialchars($exp['location']) ?></p>
                                        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                            <div class="d-flex align-items-center gap-2 text-dark fw-medium small">
                                                <img src="<?= htmlspecialchars($exp['host_avatar'] ?? 'https://ui-avatars.com/api/?name='.urlencode($exp['host_name'])) ?>" class="rounded-circle" width="24" height="24">
                                                <?= htmlspecialchars(explode(' ', $exp['host_name'])[0]) ?>
                                            </div>
                                            <div class="fw-bold text-dark fs-5">
                                                $<?= number_format($exp['price'] ?? 0, 0) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center text-muted">No experiences available yet check back soon!</div>
                <?php endif; ?>
            </div>
            <!-- Swiper Pagination -->
            <div class="swiper-pagination mt-4"></div>
        </div>
        <div class="text-center mt-3 d-md-none">
            <a href="<?= BASE_URL ?>/pages/search.php" class="btn btn-outline-primary">View All</a>
        </div>
    </div>
</section>

<!-- 4. WHY WE'RE DIFFERENT -->
<section class="py-5 my-5 overflow-hidden">
    <div class="container">
        <div class="row align-items-center g-5">
            <!-- Left side features -->
            <div class="col-lg-6 pe-lg-5" data-aos="slide-right">
                <span class="text-secondary-custom fw-bold text-uppercase tracking-wide small mb-2 d-block">Our Mission</span>
                <h2 class="font-heading fw-bold mb-4">Travel Like You Live There.</h2>
                <p class="text-muted mb-4 lead">Forget the generic audio guides and giant tour buses. We connect you with passionate locals who treat you like visiting friends.</p>
                
                <div class="d-flex flex-column gap-4">
                    <div class="d-flex gap-3">
                        <div class="text-primary-custom fs-3"><i class="fa-solid fa-hands-holding-child"></i></div>
                        <div>
                            <h5 class="fw-bold mb-1">Empowering Local Economies</h5>
                            <p class="text-muted small mb-0">85% of every booking goes directly to the host and their local community providers.</p>
                        </div>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="text-secondary-custom fs-3"><i class="fa-solid fa-shield-halved"></i></div>
                        <div>
                            <h5 class="fw-bold mb-1">Vetted & Secure</h5>
                            <p class="text-muted small mb-0">Every host is personally verified and all payments are held in secure escrow until the day of.</p>
                        </div>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="text-accent-custom fs-3"><i class="fa-solid fa-comments"></i></div>
                        <div>
                            <h5 class="fw-bold mb-1">Authentic Connection</h5>
                            <p class="text-muted small mb-0">Direct messaging allows you to customize your experience and build genuine friendships.</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Right side table -->
            <div class="col-lg-6" data-aos="slide-left" data-aos-delay="100">
                <div class="custom-card shadow-lg p-0 bg-white border-0">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr class="bg-light">
                                <th class="p-4 border-0 w-50">Feature</th>
                                <th class="p-4 border-0 text-center text-primary-custom font-heading fw-bold fs-5">WanderLocal</th>
                                <th class="p-4 border-0 text-center text-muted fw-normal">Standard Tours</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-4 py-3 text-dark fw-medium">Group Size</td>
                                <td class="px-4 py-3 text-center fw-bold text-dark">Intimate (Max 6)</td>
                                <td class="px-4 py-3 text-center text-muted">Huge (40+)</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-dark fw-medium">Pacing</td>
                                <td class="px-4 py-3 text-center fw-bold text-dark">Flexible & Relaxed</td>
                                <td class="px-4 py-3 text-center text-muted">Rushed Itinerary</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 text-dark fw-medium">Host</td>
                                <td class="px-4 py-3 text-center fw-bold text-dark">Passionate Local</td>
                                <td class="px-4 py-3 text-center text-muted">Corporate Guide</td>
                            </tr>
                            <tr class="border-transparent">
                                <td class="px-4 py-3 text-dark fw-medium border-0">Customization</td>
                                <td class="px-4 py-3 text-center border-0"><i class="fa-solid fa-check text-success fs-5"></i></td>
                                <td class="px-4 py-3 text-center border-0"><i class="fa-solid fa-xmark text-danger fs-5"></i></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 5. FEATURED HOSTS -->
<section class="py-5 mb-5 border-top border-bottom border-secondary" style="background-color: rgba(45, 74, 62, 0.02);">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="font-heading fw-bold mb-3">Meet Your Locals</h2>
            <p class="text-muted col-md-6 mx-auto">They're artists, chefs, historians, and storytellers. Connect with verified local hosts ready to share their world.</p>
        </div>

        <div class="row min-h-[300px] g-4 justify-content-center">
            <?php if(!empty($hosts)): ?>
                <?php foreach($hosts as $host): ?>
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="100">
                        <div class="custom-card text-center h-100 shadow-sm border border-light">
                            <div class="bg-secondary-custom shadow-sm position-relative overflow-hidden w-100" style="height: 120px;">
                                <img src="https://images.unsplash.com/photo-1542382103399-52e85eb66228?auto=format&fit=crop&q=80&w=600" class="w-100 h-100 object-fit-cover opacity-75">
                            </div>
                            <div class="position-absolute top-0 start-0 w-100 d-flex justify-content-center" style="margin-top: 70px;">
                                <img src="<?= htmlspecialchars($host['host_avatar'] ?? 'https://ui-avatars.com/api/?name='.urlencode($host['name'])) ?>" class="rounded-circle border border-4 border-white shadow bg-white object-fit-cover" width="100" height="100" alt="<?= htmlspecialchars($host['name']) ?>">
                            </div>
                            
                            <div class="pt-5 pb-4 px-4 mt-3">
                                <h5 class="fw-bold mb-1 text-dark d-flex align-items-center justify-content-center gap-2">
                                    <?= htmlspecialchars($host['name']) ?> 
                                    <span class="badge-verified my-2 d-inline-flex" title="Verified Host"><i class="fa-solid fa-check text-secondary-custom"></i></span>
                                </h5>
                                <p class="text-muted small fw-medium mb-3"><i class="fa-solid fa-map-pin me-1"></i> <?= htmlspecialchars($host['city'] . ', ' . $host['country']) ?></p>
                                
                                <div class="d-flex flex-wrap justify-content-center gap-2 mb-4">
                                    <?php 
                                    $tags = explode(',', $host['speciality_tags'] ?? 'Local Guide');
                                    foreach(array_slice($tags, 0, 3) as $tag): 
                                    ?>
                                        <span class="badge bg-light text-dark border fw-medium rounded-pill px-3 py-2"><?= htmlspecialchars(trim($tag)) ?></span>
                                    <?php endforeach; ?>
                                </div>
                                <a href="<?= BASE_URL ?>/pages/host_profile.php?id=<?= $host['user_id'] ?>" class="btn btn-outline-primary rounded-pill btn-sm fw-bold w-100">View Profile</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center text-muted py-5">Verified host profiles launching soon.</div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- 6. BLOG TEASER -->
<section class="py-5 my-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-4" data-aos="fade-right">
            <div>
                <h2 class="font-heading fw-bold mb-0">From the Community</h2>
            </div>
            <a href="<?= BASE_URL ?>/pages/blog.php" class="text-primary-custom text-decoration-none fw-bold d-none d-md-inline-block hover-underline">Read All <i class="fa-solid fa-arrow-right ms-1"></i></a>
        </div>

        <div class="row g-4">
            <?php if(!empty($blogs)): ?>
                <!-- Main Featured Post -->
                <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="custom-card custom-card-hover h-100 position-relative text-white overflow-hidden border-0">
                        <img src="<?= htmlspecialchars($blogs[0]['image_url'] ?? 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&q=80&w=800') ?>" class="position-absolute w-100 h-100 object-fit-cover">
                        <div class="position-absolute w-100 h-100" style="background: linear-gradient(to top, rgba(0,0,0,0.9), rgba(0,0,0,0.2) 60%);"></div>
                        <div class="position-absolute bottom-0 w-100 p-4 p-md-5">
                            <span class="badge bg-primary rounded-pill mb-3 shadow-sm px-3 py-2 fw-medium text-uppercase tracking-wider fs-6">Travel Guide</span>
                            <h3 class="font-heading fw-bold text-shadow display-6 mb-3 text-white">
                                <a href="<?= BASE_URL ?>/pages/blog_detail.php?id=<?= $blogs[0]['id'] ?>" class="text-white text-decoration-none stretched-link"><?= htmlspecialchars($blogs[0]['title']) ?></a>
                            </h3>
                            <div class="d-flex align-items-center gap-3 text-white-50 small fw-medium">
                                <span><i class="fa-solid fa-pen-nib me-1"></i> <?= htmlspecialchars($blogs[0]['author_name']) ?></span>
                                <span><i class="fa-solid fa-clock me-1"></i> 5 min read</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Side Posts -->
                <div class="col-lg-6 d-flex flex-column gap-4">
                    <?php foreach(array_slice($blogs, 1, 2) as $index => $blog): ?>
                        <div class="custom-card d-flex flex-column flex-sm-row border-0 border-bottom rounded-0 py-3 h-50 align-items-center bg-transparent shadow-none" data-aos="fade-up" data-aos-delay="<?= 200 + ($index*100) ?>">
                            <div class="w-100 w-sm-50 mb-3 mb-sm-0 me-sm-4 rounded-4 overflow-hidden position-relative" style="max-height: 200px; aspect-ratio: 4/3;">
                                <img src="<?= htmlspecialchars($blog['image_url'] ?? 'https://images.unsplash.com/photo-1499856871958-5b9627545d1a?auto=format&fit=crop&q=80&w=600') ?>" class="w-100 h-100 object-fit-cover hover-scale-sm transition">
                            </div>
                            <div class="flex-grow-1">
                                <span class="text-accent-custom fw-bold text-uppercase tracking-wide small mb-1 d-block">Tips</span>
                                <h4 class="font-heading fw-bold mb-2">
                                    <a href="<?= BASE_URL ?>/pages/blog_detail.php?id=<?= $blog['id'] ?>" class="text-dark text-decoration-none"><?= htmlspecialchars($blog['title']) ?></a>
                                </h4>
                                <div class="d-flex align-items-center gap-3 text-muted small fw-medium mt-3">
                                    <span><?= htmlspecialchars($blog['author_name']) ?></span>
                                    <span>•</span>
                                    <span><?= date('M d, Y', strtotime($blog['created_at'])) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                 <div class="col-12 text-center text-muted">Stay tuned for amazing local travel stories and tips.</div>
            <?php endif; ?>
        </div>
        <div class="text-center mt-4 d-md-none">
             <a href="<?= BASE_URL ?>/pages/blog.php" class="text-primary-custom fw-bold text-decoration-none">Read All <i class="fa-solid fa-arrow-right"></i></a>
        </div>
    </div>
</section>

<!-- 7. HOST CTA SECTION -->
<section class="py-5" style="background-color: var(--primary-dark); color: white;">
    <div class="container py-5" data-aos="zoom-in">
        <div class="row align-items-center">
            <div class="col-md-7 mb-4 mb-md-0 pe-md-5">
                <span class="text-accent-custom fw-bold text-uppercase tracking-wide small mb-3 d-block letter-spacing-2">Become a Host</span>
                <h2 class="display-4 font-heading fw-bold mb-4">You Know Your City Better Than Anyone.</h2>
                <p class="lead fw-normal text-white-50 mb-0">Turn your passion into profit. Share your culture, your favorite unlisted restaurant, or your local craft with curious travelers from around the world.</p>
            </div>
            <div class="col-md-5 text-md-end">
                <a href="<?= BASE_URL ?>/pages/auth/register.php?role=host" class="btn bg-white text-primary-custom fw-bold px-5 py-3 rounded-pill shadow-lg hover-scale-sm transition fs-5">Start Hosting Today</a>
            </div>
        </div>
    </div>
</section>

<!-- Initialization Script for Swiper -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const swiper = new Swiper('.experienceSwiper', {
            slidesPerView: 1.2,
            spaceBetween: 20,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                640: { slidesPerView: 2.2, spaceBetween: 20 },
                992: { slidesPerView: 3, spaceBetween: 30 }
            }
        });
    });
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
