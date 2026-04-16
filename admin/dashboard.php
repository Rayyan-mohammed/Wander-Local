<?php
// admin/dashboard.php
require_once __DIR__ . '/includes/admin_header.php';
require_once __DIR__ . '/../config/db.php';

$host_id = $currentUser['id'];

try {
    // 1. Revenue This Month
    $revStmt = $pdo->prepare("
        SELECT SUM(b.total_price) as monthly_revenue 
        FROM bookings b 
        JOIN experiences e ON b.experience_id = e.id 
        WHERE e.host_id = ? AND MONTH(b.created_at) = MONTH(CURRENT_DATE()) AND YEAR(b.created_at) = YEAR(CURRENT_DATE()) AND b.status IN ('confirmed', 'completed')
    ");
    $revStmt->execute([$host_id]);
    $revenue = $revStmt->fetchColumn() ?: 0;

    // 2. Total Bookings (This Month vs Last Month)
    $bkStmt = $pdo->prepare("
        SELECT 
            SUM(CASE WHEN MONTH(b.created_at) = MONTH(CURRENT_DATE()) AND YEAR(b.created_at) = YEAR(CURRENT_DATE()) THEN 1 ELSE 0 END) as this_month,
            SUM(CASE WHEN MONTH(b.created_at) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR(b.created_at) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH) THEN 1 ELSE 0 END) as last_month
        FROM bookings b 
        JOIN experiences e ON b.experience_id = e.id 
        WHERE e.host_id = ? AND b.status != 'cancelled'
    ");
    $bkStmt->execute([$host_id]);
    $bkStats = $bkStmt->fetch(PDO::FETCH_ASSOC);
    
    $thisMonthBk = $bkStats['this_month'] ?? 0;
    $lastMonthBk = $bkStats['last_month'] ?? 0;
    
    $bkChange = 0;
    if ($lastMonthBk > 0) {
        $bkChange = (($thisMonthBk - $lastMonthBk) / $lastMonthBk) * 100;
    } elseif ($thisMonthBk > 0) {
        $bkChange = 100;
    }

    // 3. Upcoming Bookings (Next 7 days)
    $upStmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM bookings b 
        JOIN experiences e ON b.experience_id = e.id 
        WHERE e.host_id = ? AND b.status = 'confirmed' AND b.booking_date BETWEEN CURRENT_DATE() AND DATE_ADD(CURRENT_DATE(), INTERVAL 7 DAY)
    ");
    $upStmt->execute([$host_id]);
    $upcoming = $upStmt->fetchColumn() ?: 0;

    // 4. Lifetime Profile/Experience Views 
    // Uses the views column we added to the experiences table
    $viewStmt = $pdo->prepare("SELECT SUM(views) FROM experiences WHERE host_id = ?");
    $viewStmt->execute([$host_id]);
    $totalViews = $viewStmt->fetchColumn() ?: 0;

    // 5. Recent Bookings (Last 5)
    $recStmt = $pdo->prepare("
        SELECT b.id, b.booking_date, b.guest_count, b.total_price, b.status, b.booking_ref, 
               e.title as experience_title, u.name as traveler_name, u.avatar as traveler_avatar 
        FROM bookings b 
        JOIN experiences e ON b.experience_id = e.id 
        JOIN users u ON b.traveler_id = u.id 
        WHERE e.host_id = ? 
        ORDER BY b.created_at DESC 
        LIMIT 5
    ");
    $recStmt->execute([$host_id]);
    $recentBookings = $recStmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database Error on Dashboard: " . $e->getMessage());
}
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-5">
    <div>
        <h2 class="fw-bold font-heading mb-1">Welcome back, <?= explode(' ', htmlspecialchars($currentUser['name']))[0] ?>! 👋</h2>
        <p class="text-muted mb-0">Here's what's happening with your experiences today.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= BASE_URL ?>/admin/experiences.php" class="btn btn-primary fw-bold rounded-pill px-4 shadow-sm"><i class="fa-solid fa-plus me-2"></i> Add Experience</a>
    </div>
</div>

<!-- Stats Row -->
<div class="row g-4 mb-5">
    <!-- Revenue -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted fw-bold mb-0 text-uppercase small">Revenue (This Month)</h6>
                    <div class="bg-success bg-opacity-10 text-success p-2 rounded-circle"><i class="fa-solid fa-sack-dollar fs-5"></i></div>
                </div>
                <h3 class="fw-bold mb-2">$<?= number_format($revenue, 2) ?></h3>
                <span class="text-muted small">Generated in <?= date('F') ?></span>
            </div>
        </div>
    </div>
    
    <!-- Bookings -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted fw-bold mb-0 text-uppercase small">Total Bookings</h6>
                    <div class="bg-primary bg-opacity-10 text-primary-custom p-2 rounded-circle"><i class="fa-solid fa-calendar-check fs-5"></i></div>
                </div>
                <h3 class="fw-bold mb-2"><?= intval($thisMonthBk) ?></h3>
                <span class="small fw-medium <?= $bkChange >= 0 ? 'text-success' : 'text-danger' ?>">
                    <i class="fa-solid <?= $bkChange >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' ?> me-1"></i>
                    <?= abs(round($bkChange, 1)) ?>% 
                    <span class="text-muted fw-normal">vs last month</span>
                </span>
            </div>
        </div>
    </div>
    
    <!-- Upcoming -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted fw-bold mb-0 text-uppercase small">Upcoming Bookings</h6>
                    <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-circle"><i class="fa-solid fa-clock fs-5"></i></div>
                </div>
                <h3 class="fw-bold mb-2"><?= intval($upcoming) ?></h3>
                <span class="text-muted small">In the next 7 days</span>
            </div>
        </div>
    </div>
    
    <!-- Views -->
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted fw-bold mb-0 text-uppercase small">Profile Views</h6>
                    <div class="bg-info bg-opacity-10 text-info p-2 rounded-circle"><i class="fa-solid fa-eye fs-5"></i></div>
                </div>
                <h3 class="fw-bold mb-2"><?= intval($totalViews) ?></h3>
                <span class="text-muted small">Lifetime exposure</span>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Bookings Table -->
    <div class="col-xl-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold font-heading mb-0">Recent Bookings</h5>
                <a href="#" class="btn btn-sm btn-link text-decoration-none fw-bold">View All</a>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-borderless table-hover align-middle mb-0">
                        <thead class="text-muted small text-uppercase fw-bold border-bottom">
                            <tr>
                                <th class="pb-3 text-nowrap">Traveler</th>
                                <th class="pb-3 text-nowrap">Experience</th>
                                <th class="pb-3 text-nowrap">Date</th>
                                <th class="pb-3 text-nowrap">Amount</th>
                                <th class="pb-3 text-nowrap">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($recentBookings): foreach($recentBookings as $bk): ?>
                            <tr>
                                <td class="py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?= htmlspecialchars($bk['traveler_avatar'] ?: 'https://ui-avatars.com/api/?name='.urlencode($bk['traveler_name'])) ?>" class="rounded-circle" width="40" height="40">
                                        <div>
                                            <div class="fw-bold text-dark mb-0"><?= htmlspecialchars($bk['traveler_name']) ?></div>
                                            <div class="text-muted small">Ref: <?= htmlspecialchars($bk['booking_ref']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 text-dark fw-medium" style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($bk['experience_title']) ?>">
                                    <?= htmlspecialchars($bk['experience_title']) ?>
                                </td>
                                <td class="py-3">
                                    <div class="fw-medium text-dark mb-0"><?= date('M j, Y', strtotime($bk['booking_date'])) ?></div>
                                    <div class="text-muted small"><?= intval($bk['guest_count']) ?> Guests</div>
                                </td>
                                <td class="py-3 fw-bold text-dark">$<?= number_format($bk['total_price'], 2) ?></td>
                                <td class="py-3">
                                    <span class="badge badge-<?= strtolower($bk['status']) ?> px-3 py-2 rounded-pill border fw-bold text-uppercase border-<?= strtolower($bk['status']) == 'confirmed' ? 'success' : (strtolower($bk['status']) == 'cancelled' ? 'danger' : 'warning') ?> border-opacity-25">
                                        <?= htmlspecialchars($bk['status']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                            <tr>
                                <td colspan="5" class="py-5 text-center text-muted">
                                    No bookings found yet. Keep your profile active!
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-xl-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-bottom-0 p-4 pb-0">
                <h5 class="fw-bold font-heading mb-0">Quick Actions</h5>
            </div>
            <div class="card-body p-4">
                <div class="d-flex flex-column gap-3">
                    <a href="<?= BASE_URL ?>/admin/experiences.php" class="btn btn-light d-flex align-items-center justify-content-between p-3 rounded-3 text-start border-0 hover-shadow transition">
                        <div>
                            <div class="fw-bold text-dark">Add New Experience</div>
                            <div class="text-muted small">Create a new local journey</div>
                        </div>
                        <div class="bg-white rounded-circle d-flex align-items-center justify-content-center text-primary-custom shadow-sm" style="width: 40px; height: 40px;">
                            <i class="fa-solid fa-arrow-right"></i>
                        </div>
                    </a>

                    <a href="<?= BASE_URL ?>/pages/host.php?id=<?= $currentUser['id'] ?>" target="_blank" class="btn btn-light d-flex align-items-center justify-content-between p-3 rounded-3 text-start border-0 hover-shadow transition">
                        <div>
                            <div class="fw-bold text-dark">View Public Profile</div>
                            <div class="text-muted small">See how travelers view you</div>
                        </div>
                        <div class="bg-white rounded-circle d-flex align-items-center justify-content-center text-success shadow-sm" style="width: 40px; height: 40px;">
                            <i class="fa-solid fa-arrow-right"></i>
                        </div>
                    </a>

                    <a href="#" class="btn btn-light d-flex align-items-center justify-content-between p-3 rounded-3 text-start border-0 hover-shadow transition">
                        <div>
                            <div class="fw-bold text-dark">Read Messages</div>
                            <div class="text-muted small">Respond to traveler inquiries</div>
                        </div>
                        <div class="bg-white rounded-circle d-flex align-items-center justify-content-center text-info shadow-sm" style="width: 40px; height: 40px;">
                            <i class="fa-solid fa-arrow-right"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>