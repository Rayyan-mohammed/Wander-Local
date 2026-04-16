<?php
// api/search_experiences.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

// --- 1. SANITIZATION & VALIDATION ---
$city = isset($_GET['city']) ? trim($_GET['city']) : '';
$categories = isset($_GET['category']) ? $_GET['category'] : ''; // Array
$min_price = isset($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) ? (float)$_GET['max_price'] : 5000;
$duration = isset($_GET['duration']) ? trim($_GET['duration']) : '';
$languages = isset($_GET['lang']) ? $_GET['lang'] : ''; // Array
$rating = isset($_GET['rating']) ? (int)$_GET['rating'] : 0;
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'recommended';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = EXP_PER_PAGE;
$offset = ($page - 1) * $limit;

$user_id = isLoggedIn() ? $_SESSION['user_id'] : 0;

// --- 2. BUILD DYNAMIC QUERY ---
$where = ["e.status = 'active'"];
$params = [];

if ($city !== '') {
    $where[] = "(e.city LIKE ? OR e.country LIKE ?)";
    $params[] = "%$city%";
    $params[] = "%$city%";
}

if (!empty($categories)) {
    // Handling array of categories
    $cat_placeholders = implode(',', array_fill(0, count($categories), '?'));
    $where[] = "e.category IN ($cat_placeholders)";
    $params = array_merge($params, $categories);
}

if ($min_price > 0) {
    $where[] = "e.price >= ?";
    $params[] = $min_price;
}

if ($max_price > 0 && $max_price < 5000) { // Assume 5000 is upper bound
    $where[] = "e.price <= ?";
    $params[] = $max_price;
}

if ($duration !== '') {
    if ($duration === 'half-day') {
        $where[] = "e.duration_hours < 4";
    } elseif ($duration === 'full-day') {
        $where[] = "e.duration_hours >= 4 AND e.duration_hours <= 8";
    } elseif ($duration === 'multi-day') {
        $where[] = "e.duration_hours > 8";
    }
}

if (!empty($languages)) {
    // Basic LIKE array matching for languages string
    $lang_conditions = [];
    foreach ($languages as $lang) {
        $lang_conditions[] = "e.languages LIKE ?";
        $params[] = "%$lang%";
    }
    $where[] = "(" . implode(' OR ', $lang_conditions) . ")";
}

if ($rating > 0) {
    $where[] = "COALESCE(r.avg_rating, e.avg_rating) >= ?";
    $params[] = $rating;
}

$where_clause = 'WHERE ' . implode(' AND ', $where);

// --- 3. SORTING ---
switch ($sort) {
    case 'price_asc': $order_by = 'ORDER BY e.price ASC'; break;
    case 'price_desc': $order_by = 'ORDER BY e.price DESC'; break;
    case 'rating': $order_by = 'ORDER BY final_rating DESC'; break;
    case 'newest': $order_by = 'ORDER BY e.created_at DESC'; break;
    case 'recommended':
    default:
        $order_by = 'ORDER BY e.total_bookings DESC, final_rating DESC';
        break;
}

// --- 4. EXECUTE ---
try {
    // Get total count for pagination
    $count_sql = "
        SELECT COUNT(e.id) as total 
        FROM experiences e 
        LEFT JOIN (
            SELECT experience_id, AVG(rating) as avg_rating 
            FROM reviews GROUP BY experience_id
        ) r ON e.id = r.experience_id
        $where_clause
    ";
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($params);
    $total_results = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
    $total_pages = ceil($total_results / $limit);

    // Get actual results
    $sql = "
        SELECT 
            e.id, e.title, e.cover_image, e.category, e.price, e.duration_hours, e.max_guests, e.city, e.country, e.slug,
            u.name as host_name, u.avatar as host_avatar, u.is_verified, 
            COALESCE(r.avg_rating, e.avg_rating) as final_rating, 
            COALESCE(r.cnt, e.total_bookings) as review_count,
            (SELECT COUNT(*) FROM wishlists w WHERE w.experience_id = e.id AND w.user_id = :user_id) as is_wishlisted
        FROM experiences e
        JOIN users u ON e.host_id = u.id
        LEFT JOIN (
            SELECT experience_id, AVG(rating) as avg_rating, COUNT(*) as cnt 
            FROM reviews GROUP BY experience_id
        ) r ON e.id = r.experience_id
        $where_clause
        $order_by
        LIMIT :limit OFFSET :offset
    ";
    
    $stmt = $pdo->prepare($sql);
    
    // Bind regular params
    $param_index = 1;
    foreach ($params as $param) {
        $stmt->bindValue($param_index++, $param, is_int($param) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    
    // Bind named params for LIMIT/OFFSET and user_id
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    $experiences = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format output
    foreach ($experiences as &$exp) {
        $exp['price_formatted'] = number_format($exp['price'], 2);
        $exp['rating_formatted'] = number_format($exp['final_rating'], 1);
        // Default image
        if (empty($exp['cover_image'])) $exp['cover_image'] = 'https://images.unsplash.com/photo-1542382103399-52e85eb66228?auto=format&fit=crop&q=80&w=600';
        if (empty($exp['host_avatar'])) $exp['host_avatar'] = 'https://ui-avatars.com/api/?name='.urlencode($exp['host_name']);
    }

    echo json_encode([
        'success' => true,
        'experiences' => $experiences,
        'total' => $total_results,
        'total_pages' => $total_pages,
        'current_page' => $page
    ]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
