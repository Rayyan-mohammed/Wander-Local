<?php
// pages/404.php
require_once __DIR__ . '/../includes/header.php';
?>
<div class="container py-5 text-center min-vh-100 d-flex flex-column justify-content-center align-items-center">
    <div class="mb-4">
        <i class="fa-solid fa-map-location-dot text-muted" style="font-size: 6rem;"></i>
    </div>
    <h1 class="display-1 fw-bold text-dark font-heading">404</h1>
    <h3 class="mb-4 text-muted">Looks like this path leads nowhere. Even the best explorers get lost.</h3>
    <div class="d-flex gap-3 justify-content-center">
        <a href="<?= BASE_URL ?>/" class="btn btn-primary rounded-pill px-4 py-2 fw-bold">Back to Homepage</a>
        <a href="<?= BASE_URL ?>/pages/experiences.php" class="btn btn-outline-dark rounded-pill px-4 py-2 fw-bold">Browse Experiences</a>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>