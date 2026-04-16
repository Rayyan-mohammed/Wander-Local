<?php
// admin/includes/admin_header.php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/auth.php';

// Auth Middleware: restrict to hosts
requireRole('host');
$currentUser = getCurrentUser();

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Host Dashboard - WanderLocal</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Inter:wght@300..800&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { width: 280px; min-height: 100vh; z-index: 1040; transition: transform 0.3s; }
        .main-content { margin-left: 280px; width: calc(100% - 280px); transition: margin-left 0.3s; }
        .nav-link.active { background-color: var(--primary); color: white !important; font-weight: bold; border-radius: 8px; }
        .nav-link:hover:not(.active) { background-color: rgba(200, 85, 61, 0.1); color: var(--primary) !important; border-radius: 8px; }
        .nav-link i { width: 24px; text-align: center; margin-right: 10px; }
        
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); position: fixed; }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; width: 100%; }
        }
        
        /* Status Badges */
        .badge-confirmed { background-color: #d1e7dd; color: #0f5132; }
        .badge-pending { background-color: #fff3cd; color: #664d03; }
        .badge-cancelled { background-color: #f8d7da; color: #842029; }
        .badge-completed { background-color: #cff4fc; color: #055160; }
    </style>
</head>
<body x-data="{ sidebarOpen: false }">

<!-- Mobile Navbar -->
<nav class="navbar navbar-light bg-white border-bottom d-lg-none px-3 sticky-top" style="z-index: 1030;">
    <a class="navbar-brand fw-bold font-heading text-primary-custom" href="<?= BASE_URL ?>/admin/dashboard.php">WanderLocal Admin</a>
    <button class="navbar-toggler shadow-none border-0" type="button" @click="sidebarOpen = !sidebarOpen">
        <i class="fa-solid fa-bars fs-4"></i>
    </button>
</nav>

<!-- Sidebar -->
<aside class="sidebar bg-white border-end position-fixed top-0 start-0 d-flex flex-column" :class="{'show': sidebarOpen}">
    <div class="px-4 py-4 border-bottom d-flex justify-content-between align-items-center">
        <a href="<?= BASE_URL ?>/admin/dashboard.php" class="text-decoration-none">
            <h3 class="fw-bold font-heading text-primary-custom mb-0">WanderLocal</h3>
        </a>
        <button class="btn-close d-lg-none" @click="sidebarOpen = false"></button>
    </div>
    
    <div class="p-4 border-bottom text-center">
        <img src="<?= htmlspecialchars($currentUser['avatar'] ?: 'https://ui-avatars.com/api/?name='.urlencode($currentUser['name'])) ?>" 
             alt="<?= htmlspecialchars($currentUser['name']) ?>" class="rounded-circle shadow-sm mb-3" width="80" height="80">
        <h5 class="fw-bold mb-1"><?= htmlspecialchars($currentUser['name']) ?></h5>
        <span class="badge bg-secondary">Host Account</span>
    </div>

    <div class="p-3 flex-grow-1 overflow-auto">
        <ul class="nav flex-column gap-2 mb-0">
            <li class="nav-item">
                <a class="nav-link text-dark <?= $currentPage == 'dashboard.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/dashboard.php">
                    <i class="fa-solid fa-chart-pie"></i> Overview
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark <?= $currentPage == 'experiences.php' || $currentPage == 'edit_experience.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/admin/experiences.php">
                    <i class="fa-solid fa-suitcase-rolling"></i> My Experiences
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark <?= $currentPage == 'bookings.php' ? 'active' : '' ?>" href="#">
                    <i class="fa-solid fa-calendar-check"></i> Bookings
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark <?= $currentPage == 'messages.php' ? 'active' : '' ?>" href="#">
                    <i class="fa-solid fa-envelope"></i> Messages
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark <?= $currentPage == 'profile.php' ? 'active' : '' ?>" href="<?= BASE_URL ?>/pages/host.php?id=<?= $currentUser['id'] ?>" target="_blank">
                    <i class="fa-solid fa-user-circle"></i> My Public Profile
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark <?= $currentPage == 'blog.php' ? 'active' : '' ?>" href="#">
                    <i class="fa-solid fa-pen-nib"></i> Blog Posts
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-dark <?= $currentPage == 'settings.php' ? 'active' : '' ?>" href="#">
                    <i class="fa-solid fa-gear"></i> Settings
                </a>
            </li>
        </ul>
    </div>
    
    <div class="p-3 border-top">
        <a href="<?= BASE_URL ?>/api/logout.php" class="btn btn-outline-danger w-100 fw-bold rounded-pill">
            <i class="fa-solid fa-arrow-right-from-bracket me-2"></i> Logout
        </a>
    </div>
</aside>

<!-- Mobile Overlay Backdrop -->
<div class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-lg-none" 
     style="z-index: 1035;" x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity></div>

<!-- Main Content Wrapper -->
<main class="main-content">
    <div class="p-4 p-md-5">