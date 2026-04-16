<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/security.php';

$currentUser = getCurrentUser($pdo);
$csrfToken = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
    <title><?= SITE_NAME ?> | Authentic Local Experiences</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,700;0,900;1,500&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6.5 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body>

<!-- Navbar (Alpine.js used for mobile menu & dropdowns) -->
<nav class="navbar navbar-expand-lg sticky-top py-3" x-data="{ mobileMenuOpen: false, userDropdownOpen: false }">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand font-heading fs-4 text-primary-custom fw-bold" href="<?= BASE_URL ?>/">
            <i class="fa-solid fa-compass me-2"></i>WanderLocal
        </a>

        <!-- Mobile Toggle (Alpine) -->
        <button class="navbar-toggler border-0 shadow-none text-dark" type="button" @click="mobileMenuOpen = !mobileMenuOpen">
            <i class="fa-solid fa-bars fs-2"></i>
        </button>

        <!-- Desktop Nav -->
        <div class="collapse navbar-collapse d-none d-lg-flex">
            <!-- Search Bar -->
            <form class="d-flex mx-auto position-relative" style="max-width: 300px; width: 100%;" action="<?= BASE_URL ?>/pages/search.php" method="GET">
                <input class="form-control rounded-pill ps-4 bg-light border-0" type="search" name="q" placeholder="Search destinations..." aria-label="Search">
                <button class="btn position-absolute end-0 top-50 translate-middle-y text-muted" type="submit">
                    <i class="fa-solid fa-search"></i>
                </button>
            </form>

            <ul class="navbar-nav ms-auto align-items-center mb-2 mb-lg-0 gap-3">
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/pages/search.php">Explore</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>/pages/blog.php">Blog</a>
                </li>
                
                <?php if($currentUser): ?>
                    <li class="nav-item position-relative" @click.away="userDropdownOpen = false">
                        <button class="btn btn-ghost d-flex align-items-center gap-2 p-1 rounded-pill" @click="userDropdownOpen = !userDropdownOpen">
                            <img src="<?= htmlspecialchars($currentUser['avatar'] ?? 'https://ui-avatars.com/api/?name='.urlencode($currentUser['name'])) ?>" class="rounded-circle" width="36" height="36" alt="Avatar">
                            <span class="fw-bold fs-6 d-none d-xl-inline"><?= htmlspecialchars(explode(' ', $currentUser['name'])[0]) ?></span>
                        </button>
                        
                        <!-- Alpine Dropdown -->
                        <div x-show="userDropdownOpen" x-transition.opacity.duration.200ms class="position-absolute end-0 mt-2 bg-white border rounded-3 shadow-sm py-2" style="width: 200px; z-index: 1050; display: none;">
                            <?php if($currentUser['role'] === 'host'): ?>
                                <a class="dropdown-item py-2" href="<?= BASE_URL ?>/admin/dashboard.php"><i class="fa-solid fa-gauge text-muted me-2"></i> Host Dashboard</a>
                            <?php else: ?>
                                <a class="dropdown-item py-2" href="<?= BASE_URL ?>/pages/traveler/dashboard.php"><i class="fa-solid fa-suitcase-rolling text-muted me-2"></i> My Trips</a>
                            <?php endif; ?>
                            <hr class="dropdown-divider">
                            <a class="dropdown-item py-2 text-danger" href="<?= BASE_URL ?>/pages/auth/logout.php"><i class="fa-solid fa-sign-out-alt me-2"></i> Logout</a>
                        </div>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="btn btn-ghost" href="<?= BASE_URL ?>/pages/auth/login.php">Log in</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary" href="<?= BASE_URL ?>/pages/auth/register.php">Sign up</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
    
    <!-- Mobile Off-Canvas Sidebar (Alpine) -->
    <div x-show="mobileMenuOpen" style="display: none;" class="position-fixed top-0 start-0 w-100 h-100" style="z-index: 1060;">
        <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark opacity-50" @click="mobileMenuOpen = false" x-transition.opacity></div>
        <div class="position-absolute top-0 end-0 h-100 bg-white w-75 p-4 shadow-lg overflow-auto" x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="transform translate-x-100" x-transition:enter-end="transform translate-x-0" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="transform translate-x-0" x-transition:leave-end="transform translate-x-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <span class="font-heading fs-5 text-primary-custom fw-bold"><i class="fa-solid fa-compass me-2"></i>WanderLocal</span>
                <button class="btn btn-close" @click="mobileMenuOpen = false"></button>
            </div>
            
            <form class="mb-4" action="<?= BASE_URL ?>/pages/search.php" method="GET">
                <div class="input-group">
                    <input class="form-control bg-light border-0" type="search" name="q" placeholder="Search...">
                    <button class="btn bg-light text-muted" type="submit"><i class="fa-solid fa-search"></i></button>
                </div>
            </form>

            <ul class="nav flex-column gap-3 fs-5">
                <li class="nav-item"><a class="nav-link px-0 text-dark" href="<?= BASE_URL ?>/pages/search.php">Explore</a></li>
                <li class="nav-item"><a class="nav-link px-0 text-dark" href="<?= BASE_URL ?>/pages/blog.php">Blog</a></li>
                <hr>
                <?php if($currentUser): ?>
                    <li class="nav-item"><a class="nav-link px-0 text-dark" href="<?= BASE_URL ?>/<?= $currentUser['role'] === 'host' ? 'admin' : 'pages/traveler' ?>/dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link px-0 text-danger" href="<?= BASE_URL ?>/pages/auth/logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="btn btn-outline-primary w-100" href="<?= BASE_URL ?>/pages/auth/login.php">Log in</a></li>
                    <li class="nav-item"><a class="btn btn-primary w-100" href="<?= BASE_URL ?>/pages/auth/register.php">Sign up</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
