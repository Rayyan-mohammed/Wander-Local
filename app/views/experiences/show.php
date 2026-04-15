<?php require_once '../app/views/components/header.php'; ?>

<main class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="lg:grid lg:grid-cols-3 lg:gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                <?= htmlspecialchars($data['experience']->title) ?>
            </h1>
            
            <div class="mt-4 flex items-center justify-between text-sm text-gray-500">
                <div class="flex items-center space-x-4">
                    <span class="flex items-center">
                        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        </svg>
                        <?= htmlspecialchars($data['experience']->location) ?>
                    </span>
                    <span class="flex items-center">
                        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <?= htmlspecialchars($data['experience']->duration) ?>
                    </span>
                    <span class="font-medium text-orange-600 bg-orange-100 px-2 py-0.5 rounded">
                        <?= htmlspecialchars($data['experience']->category) ?>
                    </span>
                </div>
            </div>

            <div class="mt-6 bg-gray-200 rounded-xl overflow-hidden h-96">
                <img src="<?= URLROOT ?>/images/<?= htmlspecialchars($data['experience']->image_url) ?>" class="w-full h-full object-cover">
            </div>

            <div class="mt-8">
                <h2 class="text-2xl font-bold text-gray-900">About this experience</h2>
                <div class="mt-4 prose prose-orange text-gray-500">
                    <p><?= nl2br(htmlspecialchars($data['experience']->description)) ?></p>
                </div>
            </div>

            <!-- Host Profile Section inside XP View -->
            <div class="mt-12 bg-gray-50 rounded-xl p-8 flex items-start space-x-6 border">
                <img class="h-20 w-20 rounded-full object-cover" src="<?= URLROOT ?>/images/<?= htmlspecialchars($data['experience']->avatar_url) ?>" alt="">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        Hosted by <?= htmlspecialchars($data['experience']->host_name) ?>
                        <?php if($data['experience']->is_verified): ?>
                            <svg class="h-5 w-5 text-blue-500 ml-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        <?php endif; ?>
                    </h2>
                    <p class="mt-1 text-sm text-gray-500 font-medium">Languages: <span class="text-gray-900"><?= htmlspecialchars($data['experience']->host_languages ?? 'English') ?></span></p>
                    <p class="mt-3 text-gray-600 text-sm"><?= htmlspecialchars($data['experience']->host_bio ?? 'A local passionate about sharing authentic experiences.') ?></p>
                </div>
            </div>
        </div>

        <!-- Sidebar / Booking Card -->
        <div class="mt-10 lg:mt-0">
            <div class="bg-white border rounded-2xl shadow-lg p-6 sticky top-6">
                <div class="flex justify-between items-end border-b pb-4">
                    <div>
                        <span class="text-2xl font-extrabold text-gray-900">$<?= htmlspecialchars($data['experience']->price) ?></span>
                        <span class="text-gray-500">/ person</span>
                    </div>
                </div>
                
                <form action="<?= URLROOT ?>/bookings/create" method="POST" class="mt-6">
                    <input type="hidden" name="experience_id" value="<?= $data['experience']->id ?>">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pick a Date</label>
                        <input type="date" name="booking_date" required class="w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 p-2 border">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Message to Host (Optional)</label>
                        <textarea name="message" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 p-2 border" placeholder="Hi! I'm really excited about..."></textarea>
                    </div>

                    <?php if(isset($_SESSION['user_id'])): ?>
                        <?php if($_SESSION['user_role'] != 'host'): ?>
                            <button type="submit" class="w-full bg-orange-600 text-white font-bold py-3 px-4 rounded-xl hover:bg-orange-700 transition">
                                Request to Book
                            </button>
                        <?php else: ?>
                            <button type="button" disabled class="w-full bg-gray-300 text-gray-800 font-bold py-3 px-4 rounded-xl cursor-not-allowed">
                                Hosts cannot book
                            </button>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="<?= URLROOT ?>/auth/login" class="block text-center w-full bg-orange-600 text-white font-bold py-3 px-4 rounded-xl hover:bg-orange-700 transition">
                            Log in to Book
                        </a>
                    <?php endif; ?>
                </form>
                <p class="text-center text-xs text-gray-500 mt-4">You won't be charged yet.</p>
            </div>
        </div>
    </div>
</main>

<?php require_once '../app/views/components/footer.php'; ?>