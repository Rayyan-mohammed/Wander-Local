<?php require_once '../app/views/components/header.php'; ?>

<?php
// Mock Data for the new UI to augment $data['experience'] until DB merges
$exp = [
    'category' => $data['experience']->category ?? 'Culinary',
    'location' => $data['experience']->location ?? 'Florence, Italy',
    'title' => $data['experience']->title ?? 'Authentic Tuscan Pasta Making',
    'duration' => $data['experience']->duration ?? '3 hours',
    'groupSize' => 'Max 6 people',
    'languages' => ['English', 'Italian'],
    'price' => $data['experience']->price ?? 85,
    'currency' => '₹', // Changed to INR
    'images' => [
        (isset($data['experience']->image_url) && !empty($data['experience']->image_url)) ? (strpos($data['experience']->image_url, 'http') === 0 ? $data['experience']->image_url : URLROOT . '/images/' . $data['experience']->image_url) : 'https://images.unsplash.com/photo-1549040003-88220f188fa6?auto=format&fit=crop&q=80&w=1200&h=800',
        'https://images.unsplash.com/photo-1556911220-e15b29be8c8f?auto=format&fit=crop&q=80&w=600&h=400',
        'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&q=80&w=600&h=400',
        'https://images.unsplash.com/photo-1510422119106-930467a840e5?auto=format&fit=crop&q=80&w=600&h=400',
        'https://images.unsplash.com/photo-1513622470522-26c3c8a854bc?auto=format&fit=crop&q=80&w=600&h=400'
    ],
    'host' => [
        'name' => $data['experience']->host_name ?? 'Elena Rossi',
        'avatar' => isset($data['experience']->avatar_url) ? URLROOT . '/images/' . $data['experience']->avatar_url : 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=200',
        'verified' => $data['experience']->is_verified ?? true,
        'responseRate' => '100%',
        'responseTime' => 'within an hour',
        'languages' => $data['experience']->host_languages ?? 'English, Italian',
        'bio' => $data['experience']->host_bio ?? 'Ciao! I\'m Elena, born and raised in Florence. Cooking is my passion, passed down from my grandmother.',
    ],
    'overview' => $data['experience']->description ?? 'Step into a traditional Florentine kitchen and learn the secrets of perfect handmade pasta.',
    'itinerary' => [
        ['time' => '10:00 AM', 'title' => 'Welcome & Aperitivo', 'description' => 'Meet at my home, enjoy local wine, meats and cheeses.'],
        ['time' => '10:30 AM', 'title' => 'Pasta Dough Masterclass', 'description' => 'Learn to mix and knead the perfect egg pasta dough.'],
        ['time' => '11:30 AM', 'title' => 'Shaping & Filling', 'description' => 'Create tagliatelle, and craft ricotta-filled ravioli.'],
        ['time' => '12:30 PM', 'title' => 'The Feast', 'description' => 'Sit down at the family dining table to enjoy the pasta.']
    ],
    'included' => ['All ingredients and equipment', '3 types of pasta', 'Local wine and water', 'Digital recipe book'],
    'meetingPoint' => 'Piazza del Carmine, 14, 50124 Firenze FI, Italy.',
    'reviews' => [
        'average' => 4.96,
        'total' => 124,
        'breakdown' => [5 => 115, 4 => 7, 3 => 2, 2 => 0, 1 => 0],
        'samples' => [
            ['id' => 1, 'author' => 'Sarah Jenkins', 'avatar' => 'https://i.pravatar.cc/150?u=sarah', 'country' => '🇺🇸', 'date' => 'October 2023', 'text' => 'Beautiful home and the pasta was the best I had in Italy.'],
            ['id' => 2, 'author' => 'David Chen', 'avatar' => 'https://i.pravatar.cc/150?u=david', 'country' => '🇨🇦', 'date' => 'September 2023', 'text' => 'Heartfelt experience. The ravioli with sage butter was incredible.'],
            ['id' => 3, 'author' => 'Emma & Tom', 'avatar' => 'https://i.pravatar.cc/150?u=emma', 'country' => '🇬🇧', 'date' => 'August 2023', 'text' => 'A highlight of our honeymoon. Fantastic food & endless wine.']
        ]
    ],
    'similar' => [
        ['id' => 'sim-1', 'title' => 'Tuscan Truffle Hunting & Lunch', 'image' => 'https://images.unsplash.com/photo-1542382103399-52e85eb66228?auto=format&fit=crop&q=80&w=400', 'price' => 120, 'rating' => 4.9, 'location' => 'San Miniato'],
        ['id' => 'sim-2', 'title' => 'Gelato Masterclass in Rome', 'image' => 'https://images.unsplash.com/photo-1579954115545-a95591f28b48?auto=format&fit=crop&q=80&w=400', 'price' => 65, 'rating' => 4.8, 'location' => 'Rome'],
        ['id' => 'sim-3', 'title' => 'Street Food Tour by Vintage Vespa', 'image' => 'https://images.unsplash.com/photo-1517524008697-84bbe3c3fd98?auto=format&fit=crop&q=80&w=400', 'price' => 95, 'rating' => 5.0, 'location' => 'Florence']
    ]
];
?>

<div class="bg-white min-h-screen text-gray-900 pb-24 md:pb-0 font-sans">
    
    <!-- Experience Hero Header -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Header Texts -->
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-6 gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="bg-orange-100 text-orange-800 text-xs font-bold px-2 py-1 rounded uppercase tracking-wide"><?= htmlspecialchars($exp['category']) ?></span>
                    <span class="text-gray-500 text-sm flex items-center gap-1">📍 <?= htmlspecialchars($exp['location']) ?></span>
                </div>
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-2 text-gray-900"><?= htmlspecialchars($exp['title']) ?></h1>
                <div class="flex items-center text-sm gap-4 text-gray-600 font-medium">
                    <span>⭐ <?= $exp['reviews']['average'] ?> (<?= ltrim($exp['reviews']['total']) ?> reviews)</span>
                    <span>⏱️ <?= htmlspecialchars($exp['duration']) ?></span>
                    <span>👥 <?= htmlspecialchars($exp['groupSize']) ?></span>
                </div>
            </div>
            <div class="flex gap-2">
                <button class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-gray-100 font-medium transition underline text-sm">📤 Share</button>
                <button class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-gray-100 font-medium transition underline text-sm">❤️ Save</button>
            </div>
        </div>

        <!-- Full-Width Photo Gallery Grid -->
        <div class="relative rounded-2xl overflow-hidden mb-12 group cursor-pointer" onclick="openLightbox()">
            <div class="grid grid-cols-1 md:grid-cols-4 grid-rows-2 h-[60vh] gap-2 lg:h-96">
                <?php foreach(array_slice($exp['images'], 0, 5) as $i => $img): ?>
                    <img src="<?= $img ?>" class="w-full h-full object-cover hover:opacity-95 transition <?= $i === 0 ? 'md:col-span-2 md:row-span-2' : 'hidden md:block' ?>" alt="Experience image <?= $i+1 ?>" />
                <?php endforeach; ?>
            </div>
            <div class="absolute bottom-4 right-4 bg-white/90 backdrop-filter backdrop-blur px-4 py-2 rounded-lg text-sm font-semibold shadow-sm border border-gray-200">
                Show all photos
            </div>
        </div>

        <!-- Lightbox -->
        <div id="lightbox" class="hidden fixed inset-0 bg-black/90 z-50 flex items-center justify-center p-8">
            <button onclick="closeLightbox()" class="absolute top-6 right-6 text-white text-4xl font-bold hover:text-gray-300">✕</button>
            <img src="<?= $exp['images'][0] ?>" class="max-w-full max-h-full object-contain" alt="Fullscreen Hero" />
        </div>

        <!-- Main Content & Sidebar -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 relative border-t pt-8 border-gray-200">
            
            <!-- Left Content Area -->
            <div class="lg:col-span-2 space-y-12">
                
                <!-- Host Card -->
                <div class="flex gap-6 items-center p-6 border border-gray-200 rounded-2xl shadow-sm bg-gray-50">
                    <img src="<?= $exp['host']['avatar'] ?>" class="w-20 h-20 rounded-full object-cover shadow-sm border-2 border-white" alt="Host" />
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="font-bold text-xl">Hosted by <?= htmlspecialchars($exp['host']['name']) ?></h3>
                            <?php if($exp['host']['verified']): ?>
                                <span class="text-blue-600 bg-blue-50 px-2 py-0.5 rounded text-xs font-bold border border-blue-100">✓ Verified</span>
                            <?php endif; ?>
                        </div>
                        <p class="text-gray-600 text-sm mb-3 max-w-lg leading-relaxed"><?= htmlspecialchars($exp['host']['bio']) ?></p>
                        <div class="flex flex-wrap text-sm gap-x-6 gap-y-2 text-gray-500 mb-4 font-medium">
                            <span>💬 Responds <?= htmlspecialchars($exp['host']['responseTime']) ?></span>
                            <span>⚡ <?= htmlspecialchars($exp['host']['responseRate']) ?> rate</span>
                            <span>🗣️ <?= htmlspecialchars($exp['host']['languages']) ?></span>
                        </div>
                        <a href="#" class="text-blue-600 font-semibold hover:underline text-sm">View Full Profile →</a>
                    </div>
                </div>

                <!-- Tabbed Navigation -->
                <div>
                    <div class="border-b border-gray-200 flex gap-6 overflow-x-auto sticky top-0 bg-white z-10 pt-4" id="tab-buttons">
                        <button onclick="switchTab('overview', this)" class="tab-btn border-b-2 border-orange-600 pb-4 whitespace-nowrap font-bold text-sm text-gray-900">Overview</button>
                        <button onclick="switchTab('itinerary', this)" class="tab-btn text-gray-500 hover:text-gray-800 pb-4 whitespace-nowrap font-bold text-sm border-b-2 border-transparent">Itinerary</button>
                        <button onclick="switchTab('included', this)" class="tab-btn text-gray-500 hover:text-gray-800 pb-4 whitespace-nowrap font-bold text-sm border-b-2 border-transparent">What's Included</button>
                        <button onclick="switchTab('meeting', this)" class="tab-btn text-gray-500 hover:text-gray-800 pb-4 whitespace-nowrap font-bold text-sm border-b-2 border-transparent">Meeting Point</button>
                        <button onclick="switchTab('reviews', this)" class="tab-btn text-gray-500 hover:text-gray-800 pb-4 whitespace-nowrap font-bold text-sm border-b-2 border-transparent">Reviews</button>
                    </div>

                    <!-- Tab Content Panels -->
                    <div class="py-8 min-h-[300px]">
                        
                        <!-- Overview Tab -->
                        <div id="tab-overview" class="tab-content block">
                            <div class="prose prose-orange max-w-none text-lg text-gray-700 leading-relaxed">
                                <p><?= nl2br(htmlspecialchars($exp['overview'])) ?></p>
                                <div class="mt-8 grid grid-cols-2 gap-4 not-prose">
                                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                                        <div class="text-xs font-bold uppercase tracking-wide text-gray-500 mb-1">Languages</div>
                                        <div class="font-medium text-gray-900"><?= implode(', ', $exp['languages']) ?></div>
                                    </div>
                                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                                        <div class="text-xs font-bold uppercase tracking-wide text-gray-500 mb-1">Duration</div>
                                        <div class="font-medium text-gray-900"><?= htmlspecialchars($exp['duration']) ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Itinerary Tab -->
                        <div id="tab-itinerary" class="tab-content hidden space-y-6 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gray-200">
                            <?php foreach($exp['itinerary'] as $item): ?>
                                <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full border border-white bg-white shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10">
                                        <div class="w-3 h-3 bg-orange-500 rounded-full"></div>
                                    </div>
                                    <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                                        <div class="text-xs font-bold text-orange-600 mb-1"><?= htmlspecialchars($item['time']) ?></div>
                                        <h4 class="font-bold text-lg mb-1 text-gray-900"><?= htmlspecialchars($item['title']) ?></h4>
                                        <p class="text-sm text-gray-600"><?= htmlspecialchars($item['description']) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- What's Included Tab -->
                        <div id="tab-included" class="tab-content hidden">
                            <ul class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <?php foreach($exp['included'] as $item): ?>
                                    <li class="flex gap-4 p-4 border border-gray-200 rounded-xl bg-gray-50/50 items-center text-gray-700 font-medium">
                                        <span class="text-orange-500 text-xl font-bold bg-orange-100 w-8 h-8 rounded-full flex items-center justify-center shrink-0">✓</span> <?= htmlspecialchars($item) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <!-- Meeting Point Tab -->
                        <div id="tab-meeting" class="tab-content hidden">
                            <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200">
                                <div class="flex items-start gap-4 mb-4">
                                    <span class="text-2xl mt-1">📍</span>
                                    <div>
                                        <h4 class="font-bold text-lg mb-1 text-gray-900">Meet your host</h4>
                                        <p class="text-gray-700"><?= htmlspecialchars($exp['meetingPoint']) ?></p>
                                    </div>
                                </div>
                                <div class="w-full h-64 bg-gray-200 rounded-xl mt-6 flex items-center justify-center text-gray-500 font-mono text-sm border border-gray-300">
                                    [ Map Integration ]
                                </div>
                            </div>
                        </div>

                        <!-- Reviews Tab -->
                        <div id="tab-reviews" class="tab-content hidden">
                            <div class="flex flex-col md:flex-row items-center gap-8 mb-10 pb-10 border-b border-gray-200">
                                <div class="text-center w-full md:w-1/3">
                                    <div class="text-6xl font-extrabold text-gray-900"><?= $exp['reviews']['average'] ?></div>
                                    <div class="text-sm font-bold text-gray-500 uppercase tracking-widest mt-2"><?= $exp['reviews']['total'] ?> Reviews</div>
                                </div>
                                <div class="flex-1 w-full space-y-2">
                                    <?php 
                                    $reversedBreakdown = array_reverse($exp['reviews']['breakdown'], true);
                                    foreach($reversedBreakdown as $stars => $count): 
                                        $percent = ($count / $exp['reviews']['total']) * 100;
                                    ?>
                                        <div class="flex items-center text-sm">
                                            <span class="w-4 font-bold text-gray-400"><?= $stars ?></span>
                                            <span class="mx-2 text-yellow-400">★</span>
                                            <div class="flex-1 h-3 bg-gray-100 rounded-full overflow-hidden">
                                                <div class="h-full bg-gray-900 rounded-full" style="width: <?= $percent ?>%"></div>
                                            </div>
                                            <span class="w-8 text-right text-gray-400 text-xs font-mono"><?= $count ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-10">
                                <?php foreach($exp['reviews']['samples'] as $review): ?>
                                    <div class="space-y-4">
                                        <div class="flex items-center gap-4">
                                            <img src="<?= $review['avatar'] ?>" class="w-12 h-12 rounded-full object-cover" alt="Reviewer" />
                                            <div>
                                                <div class="font-bold flex items-center gap-2 text-gray-900"><?= htmlspecialchars($review['author']) ?> <span><?= $review['country'] ?></span></div>
                                                <div class="text-xs text-gray-500"><?= htmlspecialchars($review['date']) ?></div>
                                            </div>
                                        </div>
                                        <p class="text-gray-700 leading-relaxed text-sm"><?= htmlspecialchars($review['text']) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Similar Experiences -->
                <div class="pt-12 border-t border-gray-200 mt-12">
                    <h2 class="text-2xl font-bold mb-6 text-gray-900">You might also like</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        <?php foreach($exp['similar'] as $sim): ?>
                            <div class="group cursor-pointer">
                                <div class="relative aspect-w-4 aspect-h-3 rounded-xl overflow-hidden mb-3 bg-gray-100 h-48">
                                    <img src="<?= $sim['image'] ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" alt="Similar Experience" />
                                </div>
                                <div class="flex justify-between items-start">
                                    <h3 class="font-medium text-sm line-clamp-2 leading-snug text-gray-900"><?= htmlspecialchars($sim['title']) ?></h3>
                                    <span class="flex items-center gap-1 text-sm font-bold whitespace-nowrap">★ <?= $sim['rating'] ?></span>
                                </div>
                                <p class="text-gray-500 text-sm mt-1"><?= htmlspecialchars($sim['location']) ?></p>
                                <p class="font-bold mt-1 text-sm text-gray-900">
                                    <?= $exp['currency'] ?><?= $sim['price'] ?> <span class="text-gray-500 font-normal">/ person</span>
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Booking Sidebar / Tool (Desktop) -->
            <div class="hidden lg:block relative">
                <div class="sticky top-24 border border-gray-200 rounded-2xl p-6 shadow-xl shadow-gray-200/50 bg-white">
                    <div class="flex justify-between items-end mb-6">
                        <div>
                            <span class="text-3xl font-extrabold text-gray-900" id="base-price" data-price="<?= $exp['price'] ?>"><?= $exp['currency'] ?><?= $exp['price'] ?></span>
                            <span class="text-gray-500 ml-1 font-medium">/ person</span>
                        </div>
                        <div class="text-sm flex items-center gap-1 font-medium"><span class="text-yellow-500">★</span> <?= $exp['reviews']['average'] ?></div>
                    </div>
                    
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <?php if($_SESSION['user_role'] != 'host'): ?>
                            <button onclick="openBookingFlow()" class="w-full bg-orange-600 text-white py-4 rounded-xl font-bold text-lg hover:bg-orange-700 transition shadow-lg shadow-orange-600/20 mb-3">
                                Book Now
                            </button>
                        <?php else: ?>
                            <button type="button" disabled class="w-full bg-gray-300 text-gray-800 py-4 rounded-xl font-bold text-lg mb-3 cursor-not-allowed">
                                Hosts cannot book
                            </button>
                        <?php endif; ?>
                    <?php else: ?>
                        <a href="<?= URLROOT ?>/auth/login" class="block text-center w-full bg-orange-600 text-white py-4 rounded-xl font-bold text-lg hover:bg-orange-700 transition shadow-lg shadow-orange-600/20 mb-3">
                            Log in to Book
                        </a>
                    <?php endif; ?>

                    <button class="w-full py-4 border border-gray-300 rounded-xl font-bold text-gray-700 hover:bg-gray-50 transition">
                        Message Host
                    </button>
                    <div class="mt-4 text-center text-xs text-gray-500">
                        🔒 Secure payment • Instant confirmation
                    </div>
                </div>
            </div>

            <!-- Mobile Bottom Action Bar (visible only sm/md screens) -->
            <div class="lg:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 flex justify-between items-center z-40 shadow-[0_-10px_40px_rgba(0,0,0,0.1)]">
                <div>
                    <div class="font-bold text-lg"><?= $exp['currency'] ?><?= $exp['price'] ?> <span class="text-sm font-normal text-gray-500">/ person</span></div>
                    <div class="text-xs text-gray-500 font-medium mt-0.5">Select dates to view total</div>
                </div>
                <?php if(isset($_SESSION['user_id']) && $_SESSION['user_role'] != 'host'): ?>
                    <button onclick="openBookingFlow()" class="bg-orange-600 px-8 py-3 rounded-xl text-white font-bold tracking-wide shadow-md shadow-orange-600/20">
                        Book Now
                    </button>
                <?php elseif(!isset($_SESSION['user_id'])): ?>
                    <a href="<?= URLROOT ?>/auth/login" class="bg-orange-600 px-8 py-3 rounded-xl text-white font-bold tracking-wide shadow-md shadow-orange-600/20">
                        Log in
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </main>

    <!-- ====== BOOKING FLOW MODAL (HTML/Vanilla JS) ====== -->
    <div id="booking-flow" class="hidden fixed inset-0 bg-black/60 z-50 flex justify-center items-center p-4 sm:p-6 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto flex flex-col relative">
            
            <!-- Modal Header -->
            <div class="border-b border-gray-200 p-4 sm:p-5 flex justify-between items-center sticky top-0 bg-white z-10">
                <h2 id="booking-title" class="font-bold text-lg sm:text-xl text-gray-900">Select Date & Guests</h2>
                <button onclick="closeBookingFlow()" class="p-2 hover:bg-gray-100 rounded-full text-gray-500 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-6">
                <!-- Data for JS computations -->
                <input type="hidden" id="exp-currency" value="<?= $exp['currency'] ?>">
                <input type="hidden" id="exp-price" value="<?= $exp['price'] ?>">
                
                <!-- Step 1: Date & Guests -->
                <form id="step-1" class="booking-step space-y-6" onsubmit="nextStep(event, 2)">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Select Date</label>
                        <input type="date" id="booking-date" required class="w-full border border-gray-300 rounded-xl p-3 outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 bg-white" min="<?= date('Y-m-d') ?>" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Number of Guests</label>
                        <select id="booking-guests" class="w-full border border-gray-300 rounded-xl p-3 outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 bg-white" onchange="updateTotal()">
                            <?php for($i=1; $i<=6; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?> Guest<?= $i > 1 ? 's' : '' ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <button type="submit" class="w-full bg-orange-600 text-white py-4 rounded-xl font-bold text-lg hover:bg-orange-700 transition">
                        Continue • <span id="display-total"><?= $exp['currency'] ?><?= $exp['price'] ?></span>
                    </button>
                </form>

                <!-- Step 2: Traveler Details -->
                <form id="step-2" class="booking-step hidden space-y-5" onsubmit="nextStep(event, 3)">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                            <input required id="t-name" class="w-full border border-gray-300 rounded-xl p-3 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 focus:outline-none" placeholder="John Doe" value="<?= $_SESSION['user_name'] ?? '' ?>" />
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nationality <span class="text-red-500">*</span></label>
                            <input required id="t-national" class="w-full border border-gray-300 rounded-xl p-3 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 focus:outline-none" placeholder="e.g. USA" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" required id="t-email" class="w-full border border-gray-300 rounded-xl p-3 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 focus:outline-none" placeholder="john@example.com" value="<?= $_SESSION['user_email'] ?? '' ?>"/>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Phone Number (Optional)</label>
                        <input type="tel" id="t-phone" class="w-full border border-gray-300 rounded-xl p-3 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 focus:outline-none" placeholder="+1 234 567 8900" />
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Special Requests or Dietary Requirements</label>
                        <textarea id="t-requests" class="w-full border border-gray-300 rounded-xl p-3 focus:border-orange-500 focus:ring-1 focus:ring-orange-500 focus:outline-none" rows="3" placeholder="e.g. Vegetarian, allergies..."></textarea>
                    </div>
                    <div class="flex gap-3 pt-4 border-t border-gray-200">
                        <button type="button" onclick="goToStep(1)" class="px-6 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 font-bold text-gray-700">Back</button>
                        <button type="submit" class="flex-1 bg-orange-600 text-white py-3 rounded-xl font-bold hover:bg-orange-700">Continue to Payment</button>
                    </div>
                </form>

                <!-- Step 3: Payment (Razorpay Mock UI) -->
                <div id="step-3" class="booking-step hidden space-y-6">
                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-200 shadow-inner">
                        <h3 class="font-bold text-gray-800 mb-3 text-sm uppercase tracking-wide">Order Summary</h3>
                        <div class="flex justify-between py-2 border-b border-gray-200">
                            <span class="text-gray-800 font-bold"><?= htmlspecialchars($exp['title']) ?></span>
                        </div>
                        <div class="flex justify-between py-3 text-sm text-gray-600">
                            <span><span id="summary-date" class="font-medium"></span> • <span id="summary-guests" class="font-medium"></span> Guests</span>
                            <span><span id="summary-guests-calc"></span> × <?= $exp['currency'] ?><?= $exp['price'] ?></span>
                        </div>
                        <div class="flex justify-between py-3 font-extrabold text-lg text-gray-900 border-t border-gray-200 shadow-[0_-8px_10px_-10px_rgba(0,0,0,0.1)]">
                            <span>Total</span>
                            <span id="summary-total" class="text-orange-600"></span>
                        </div>
                    </div>

                    <!-- Mock Razorpay Checkout UI -->
                    <div class="border border-gray-200 rounded-xl overflow-hidden shadow-sm">
                        <div class="bg-[#02042b] text-white p-5 flex justify-between items-center">
                            <div>
                                <h3 class="font-bold text-lg leading-tight">Razorpay</h3>
                                <p class="text-[11px] text-gray-300 font-medium uppercase tracking-wider opacity-80">WanderLocal Secure</p>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-xl font-mono tracking-wide" id="rzp-total"></div>
                                <div class="text-[10px] bg-white/20 px-2 py-0.5 rounded-full inline-block mt-1 uppercase font-bold tracking-widest">Test Mode</div>
                            </div>
                        </div>
                        <div class="p-5 bg-white space-y-4">
                            <div class="flex justify-between items-center mb-2">
                                <h3 class="font-semibold text-gray-800 text-sm">Pay with Card</h3>
                                <div class="flex gap-1 text-[10px] bg-gray-100 px-2 py-1 rounded font-bold">
                                    <span class="text-[#1A1F71]">VISA</span>
                                    <span class="text-[#EB001B]">MC</span>
                                </div>
                            </div>
                            
                            <div>
                                <div class="border border-gray-300 rounded-md focus-within:ring-2 ring-blue-500 focus-within:border-blue-500 transition-all mb-4 relative">
                                    <span class="absolute left-3 top-3 text-gray-400">💳</span>
                                    <input class="w-full p-3 pl-10 text-sm font-mono outline-none bg-transparent" placeholder="4111 1111 1111 1111" />
                                </div>
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div class="border border-gray-300 rounded-md focus-within:ring-2 ring-blue-500 focus-within:border-blue-500 transition-all">
                                        <input class="w-full p-3 text-sm font-mono outline-none bg-transparent" placeholder="MM/YY" />
                                    </div>
                                    <div class="border border-gray-300 rounded-md focus-within:ring-2 ring-blue-500 focus-within:border-blue-500 transition-all relative">
                                        <input class="w-full p-3 text-sm font-mono outline-none bg-transparent" placeholder="CVV" type="password" maxlength="4" />
                                    </div>
                                </div>
                                <div class="border border-gray-300 rounded-md focus-within:ring-2 ring-blue-500 focus-within:border-blue-500 transition-all">
                                    <input class="w-full p-3 text-sm outline-none bg-transparent" placeholder="Cardholder Name" />
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2 mt-2 text-sm text-gray-600 bg-gray-50 p-2 rounded border border-gray-100">
                                <input type="checkbox" id="save-card" class="rounded cursor-pointer w-4 h-4 border-gray-300" checked />
                                <label for="save-card" class="cursor-pointer select-none text-xs font-medium">Save card securely for future payments</label>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" onclick="goToStep(2)" class="px-6 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 font-bold text-gray-700 transition">Back</button>
                        <!-- Form submits mock data to local controller. Replace with real integration later -->
                        <form action="<?= URLROOT ?>/bookings/create" method="POST" class="flex-1" id="mock-payment-form">
                            <!-- Hidden inputs tracking our multi-step form data -->
                            <input type="hidden" name="experience_id" value="<?= $data['experience']->id ?? '' ?>">
                            <input type="hidden" name="booking_date" id="final-date">
                            <input type="hidden" name="guests" id="final-guests">
                            <input type="hidden" name="traveler_name" id="final-name">
                            <input type="hidden" name="traveler_email" id="final-email">
                            <input type="hidden" name="traveler_phone" id="final-phone">
                            <input type="hidden" name="message" id="final-message">
                            <!-- In a real world scenario, you intercept via JS, tokenize with Razorpay, then submit the token -->
                            
                            <button type="button" onclick="processMockPayment()" class="w-full bg-[#3399cc] text-white py-3 rounded-xl font-bold hover:bg-[#2b82ad] transition shadow-md shadow-blue-500/20 text-lg flex justify-center items-center gap-2">
                                <span id="btn-pay-text">Pay Now</span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Step 4: Success / Confirmation (Simulated via JS before backend redirect) -->
                <div id="step-4" class="booking-step hidden text-center py-8 space-y-5">
                    <div class="w-24 h-24 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto text-5xl mb-2">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-3xl font-extrabold mb-2 text-gray-900">Booking Confirmed!</h2>
                        <p class="text-gray-600 font-medium">Your Booking ID is <span class="text-gray-900 font-extrabold tracking-wide">#WL-<span id="display-booking-id"></span></span></p>
                        <p class="text-gray-500 text-sm mt-1">A confirmation has been sent to <span id="display-email" class="font-bold text-gray-800"></span></p>
                    </div>
                    
                    <div class="bg-blue-50/50 border border-blue-100 p-5 rounded-2xl text-left text-sm mb-6 mt-6 shadow-inner mx-4">
                        <p class="font-bold text-blue-900 mb-3 text-xs uppercase tracking-widest border-b border-blue-100 pb-2">Host Contact Info</p>
                        <div class="flex items-center gap-3 mb-3">
                            <img src="<?= $exp['host']['avatar'] ?>" class="w-10 h-10 rounded-full" alt="Host">
                            <div>
                                <p class="font-bold text-gray-900 text-base"><?= htmlspecialchars($exp['host']['name']) ?></p>
                                <p class="text-gray-600 font-medium">+39 345 678 9012 (WhatsApp)</p>
                            </div>
                        </div>
                        <p class="mt-4 pt-3 border-t border-blue-100 text-gray-700 leading-snug">
                            <strong>📍 Meeting Point:</strong><br/>
                            <?= htmlspecialchars($exp['meetingPoint']) ?>
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 justify-center pt-4 px-4">
                        <!-- Submit the form to back end as the final mock resolution -->
                        <button type="button" onclick="document.getElementById('mock-payment-form').submit()" class="px-6 py-3 border border-gray-300 rounded-xl hover:bg-gray-50 font-bold text-gray-700 w-full sm:w-auto transition">Go to Dashboard</button>
                        <button type="button" onclick="alert('Calendar event downloaded! (Mock)')" class="px-6 py-3 bg-gray-900 text-white rounded-xl hover:bg-black font-bold flex gap-2 justify-center items-center w-full sm:w-auto shadow-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Add to Calendar
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<script>
    /* ----- UI Navigation (Tabs & Gallery) ----- */
    function openLightbox() {
        document.getElementById('lightbox').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    
    function closeLightbox() {
        document.getElementById('lightbox').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function switchTab(tabId, clickedBtn) {
        // Reset buttons to inactive style
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.className = "tab-btn text-gray-500 hover:text-gray-800 pb-4 whitespace-nowrap font-bold text-sm border-b-2 border-transparent transition-colors";
        });
        
        // Hide all tab panels
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
            content.classList.remove('block');
        });
        
        // Set active button style
        clickedBtn.className = "tab-btn border-b-2 border-orange-600 pb-4 whitespace-nowrap font-bold text-sm text-gray-900 transition-colors";
        
        // Show target panel
        const target = document.getElementById('tab-' + tabId);
        target.classList.remove('hidden');
        target.classList.add('block');
    }

    /* ----- Booking Flow Multi-Step Logic ----- */
    let currentBookingTotal = 0;

    function openBookingFlow() {
        document.getElementById('booking-flow').classList.remove('hidden');
        document.body.style.overflow = 'hidden'; 
        updateTotal(); 
        goToStep(1);
    }

    function closeBookingFlow() {
        document.getElementById('booking-flow').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function updateTotal() {
        const guests = document.getElementById('booking-guests').value;
        const price = document.getElementById('exp-price').value;
        const currency = document.getElementById('exp-currency').value;
        
        currentBookingTotal = guests * price;
        document.getElementById('display-total').innerText = currency + currentBookingTotal;
    }

    function nextStep(event, nextStepNum) {
        event.preventDefault(); 
        
        // Before step 3 (Payment), push input data into the hidden form we will submit eventually
        if(nextStepNum === 3) {
            document.getElementById('final-date').value = document.getElementById('booking-date').value;
            document.getElementById('final-guests').value = document.getElementById('booking-guests').value;
            document.getElementById('final-name').value = document.getElementById('t-name').value;
            document.getElementById('final-email').value = document.getElementById('t-email').value;
            document.getElementById('final-phone').value = document.getElementById('t-phone').value;
            document.getElementById('final-message').value = document.getElementById('t-requests').value;
            populateSummary();
        }

        goToStep(nextStepNum);
    }

    function goToStep(stepNum) {
        // Hide all steps
        document.querySelectorAll('.booking-step').forEach(step => {
            step.classList.add('hidden');
        });

        // Show target step
        document.getElementById('step-' + stepNum).classList.remove('hidden');

        // Update Header Title
        const titleEl = document.getElementById('booking-title');
        
        if (stepNum === 1) titleEl.innerText = "Select Date & Guests";
        else if (stepNum === 2) titleEl.innerText = "Traveler Details";
        else if (stepNum === 3) titleEl.innerText = "Confirm & Pay";
        else if (stepNum === 4) {
            titleEl.innerText = "Payment Successful";
            // Populate success screen mock data
            document.getElementById('display-booking-id').innerText = Math.floor(10000 + Math.random() * 90000);
            document.getElementById('display-email').innerText = document.getElementById('t-email').value || 'your email';
        }
    }

    function populateSummary() {
        const date = document.getElementById('booking-date').value;
        const guests = document.getElementById('booking-guests').value;
        const currency = document.getElementById('exp-currency').value;
        
        document.getElementById('summary-date').innerText = date || 'Unspecified Date';
        document.getElementById('summary-guests').innerText = guests;
        document.getElementById('summary-guests-calc').innerText = guests;
        
        const totalStr = currency + currentBookingTotal;
        document.getElementById('summary-total').innerText = totalStr;
        document.getElementById('rzp-total').innerText = totalStr;
    }

    function processMockPayment() {
        const btnText = document.getElementById('btn-pay-text');
        btnText.innerText = "Processing...";
        
        // Mock a 1.5s delay to simulate Razorpay Tokenization
        setTimeout(() => {
            btnText.innerText = "Pay Now";
            goToStep(4);
            
            // NOTE: In production, instead of goToStep(4), you'd normally just let the form
            // submit to your PHP Backend `/bookings/create` which then generates the actual 
            // success page/redirect. We are halting it here for the UI mock requirement!
            
        }, 1500);
    }
</script>

<?php require_once '../app/views/components/footer.php'; ?>