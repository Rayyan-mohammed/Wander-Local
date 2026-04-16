<?php
// includes/components/experience_card.php
function render_experience_card($experience) {
    if (!$experience) return '';
    $img = htmlspecialchars($experience['cover_image'] ?? 'https://via.placeholder.com/400x300');
    $title = htmlspecialchars($experience['title'] ?? 'Experience');
    $price = number_format($experience['price'] ?? 0);
    $url = htmlspecialchars($experience['url'] ?? '#');
    
    return <<<HTML
    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden experience-card">
        <a href="{$url}" class="text-decoration-none text-dark">
            <img src="{$img}" class="card-img-top object-fit-cover" alt="{$title}" loading="lazy" width="400" height="250">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-2 text-truncate">{$title}</h5>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <span class="fw-bold fs-5 text-primary">₹{$price}</span>
                    <span class="text-muted small">per person</span>
                </div>
            </div>
        </a>
    </div>
HTML;
}
