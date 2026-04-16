<?php
// api/create_booking.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'You must be logged in to book.']);
    exit;
}

$user = getCurrentUser();

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid Request']);
    exit;
}

$experience_id = $input['experience_id'] ?? null;
$booking_date = $input['date'] ?? null;
$guests = $input['guests'] ?? null;
$requests = $input['special_requests'] ?? '';

if (!$experience_id || !$booking_date || !$guests) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    // Check experience and availability
    $stmt = $pdo->prepare("SELECT id, price, max_guests FROM experiences WHERE id = ?");
    $stmt->execute([$experience_id]);
    $exp = $stmt->fetch();

    if (!$exp) {
        echo json_encode(['success' => false, 'message' => 'Experience not found']);
        exit;
    }

    if ($guests > $exp['max_guests']) {
        echo json_encode(['success' => false, 'message' => 'Exceeds maximum guests allowed']);
        exit;
    }

    // Check duplicate booking same date (availability logic)
    $stmt = $pdo->prepare("SELECT SUM(guest_count) as total_booked FROM bookings WHERE experience_id = ? AND booking_date = ? AND status != 'cancelled'");
    $stmt->execute([$experience_id, $booking_date]);
    $booked = $stmt->fetch()['total_booked'] ?? 0;

    if (($booked + $guests) > $exp['max_guests']) {
        echo json_encode(['success' => false, 'message' => 'Not enough spots available for this date']);
        exit;
    }

    $total_price = ($exp['price'] * $guests) * 1.05; // 5% fee
    
    // Create Ref
    $ref = 'WL-' . strtoupper(substr(uniqid(), -6));

    // Insert Booking
    $stmt = $pdo->prepare("INSERT INTO bookings (experience_id, traveler_id, booking_date, guest_count, total_price, status, special_requests, booking_ref) VALUES (?, ?, ?, ?, ?, 'confirmed', ?, ?)");
    $stmt->execute([
        $experience_id, 
        $user['id'], 
        $booking_date, 
        $guests, 
        $total_price, 
        $requests, 
        $ref
    ]);

    echo json_encode([
        'success' => true,
        'booking_ref' => $ref,
        'message' => 'Booking completed successfully'
    ]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
}
