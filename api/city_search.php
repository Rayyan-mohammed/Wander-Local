<?php
// api/city_search.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../app/core/Logger.php';

header('Content-Type: application/json');
header('Cache-Control: public, max-age=600');

$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT DISTINCT city, country FROM experiences WHERE status = 'active' AND (city LIKE ? OR country LIKE ?) LIMIT 10");
    $searchTerm = "%{$query}%";
    $stmt->execute([$searchTerm, $searchTerm]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($results);
} catch (PDOException $e) {
    Logger::warning('city_search.php failed', ['exception' => $e->getMessage()]);
    echo json_encode([]);
}
