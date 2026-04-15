<?php require_once '../app/views/components/header.php'; ?>

<header class="bg-white shadow">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900">
            Welcome back, <?= htmlspecialchars($data['name']) ?>!
        </h1>
        <p class="text-sm text-gray-500 mt-1 capitalize h-auto">Role: <span class="font-semibold text-orange-600"><?= $data['role'] ?></span></p>
    </div>
</header>

<main>
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <?php if ($data['role'] == 'host'): ?>
            <!-- Host Dashboard Grid -->
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Your Experiences</h2>
                <a href="<?= URLROOT ?>/experiences/create" class="bg-orange-600 hover:bg-orange-700 text-white font-medium py-2 px-4 rounded-md transition shadow-sm text-sm">
                    + Add New Listing
                </a>
            </div>
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6 bg-white border-b border-gray-200 text-gray-600">
                    <?php if (empty($data['stats']['experiences'])): ?>
                        You have no active experiences. Start hosting by adding a listing!
                    <?php else: ?>
                        <ul class="divide-y divide-gray-200">
                            <?php foreach ($data['stats']['experiences'] as $exp): ?>
                                <li class="py-4 font-medium"><?= htmlspecialchars($exp->title) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
            
            <h2 class="text-xl font-semibold text-gray-800 mt-8 mb-4">Pending Requests</h2>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6 bg-white border-b border-gray-200 text-gray-600">
                    <?php if (empty($data['stats']['bookings'])): ?>
                        No booking requests yet.
                    <?php else: ?>
                        <ul class="divide-y divide-gray-200">
                            <?php foreach ($data['stats']['bookings'] as $booking): ?>
                                <li class="py-4">
                                    <div class="flex justify-between">
                                        <span class="font-medium"><?= htmlspecialchars($booking->traveler_name) ?> requested "<?= htmlspecialchars($booking->experience_title) ?>"</span>
                                        <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded"><?= htmlspecialchars($booking->status) ?></span>
                                    </div>
                                    <p class="text-sm mt-1 text-gray-500">Date: <?= htmlspecialchars($booking->booking_date) ?></p>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

        <?php else: ?>
            <!-- Traveler Dashboard Grid -->
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-800">Your Upcoming Bookings</h2>
                <a href="<?= URLROOT ?>/experiences" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-4 rounded-md transition shadow-md border text-sm">
                    Explore More
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="p-6 bg-white border-b border-gray-200 text-gray-600">
                    <?php if (empty($data['stats']['bookings'])): ?>
                        No upcoming bookings found. Time to explore!
                    <?php else: ?>
                        <ul class="divide-y divide-gray-200">
                            <?php foreach ($data['stats']['bookings'] as $booking): ?>
                                <li class="py-4">
                                    <div class="flex justify-between">
                                        <span class="font-medium">Experience: <?= htmlspecialchars($booking->experience_title) ?></span>
                                        <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded capitalize"><?= htmlspecialchars($booking->status) ?></span>
                                    </div>
                                    <p class="text-sm mt-1 text-gray-500">Date: <?= htmlspecialchars($booking->booking_date) ?> | Hosted by <?= htmlspecialchars($booking->host_name) ?></p>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once '../app/views/components/footer.php'; ?>