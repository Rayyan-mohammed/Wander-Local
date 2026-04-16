<?php
// sitemap.xml.php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/config.php';

header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

$urls = [];
$urls[] = BASE_URL . '/';
$urls[] = BASE_URL . '/pages/experiences.php';
$urls[] = BASE_URL . '/pages/blog.php';

// Add URLs
foreach ($urls as $url) {
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($url) . "</loc>\n";
    echo "    <changefreq>daily</changefreq>\n";
    echo "    <priority>1.0</priority>\n";
    echo "  </url>\n";
}

// Fetch dynamic content
try {
    // Blog logic
    $stmt = $pdo->query("SELECT slug, updated_at FROM blog_posts WHERE status = 'published'");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  <url>\n";
        echo "    <loc>" . htmlspecialchars(BASE_URL . '/pages/post.php?slug=' . $row['slug']) . "</loc>\n";
        echo "    <lastmod>" . date('Y-m-d', strtotime($row['updated_at'] ?? 'now')) . "</lastmod>\n";
        echo "    <changefreq>weekly</changefreq>\n";
        echo "    <priority>0.8</priority>\n";
        echo "  </url>\n";
    }
} catch (Exception $e) {}

echo '</urlset>';
