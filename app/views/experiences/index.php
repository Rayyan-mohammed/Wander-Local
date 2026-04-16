<?php require_once '../app/views/components/header.php'; ?>

<div class="min-h-screen bg-[#FDFBF7] flex flex-col font-sans">
    <!-- Search Header -->
    <header class="sticky top-0 z-40 bg-white border-b border-accent/30 px-4 py-4 md:px-8 shadow-sm">
        <form action="<?= URLROOT ?>/experiences" method="GET" class="max-w-7xl mx-auto flex flex-col md:flex-row gap-4 items-center justify-between" id="searchForm">
            <!-- Hidden inputs to persist filters when searching -->
            <input type="hidden" name="category" id="hidden_category" value="<?= htmlspecialchars($data['filters']['category']) ?>">
            <input type="hidden" name="duration" id="hidden_duration" value="<?= htmlspecialchars($data['filters']['duration']) ?>">
            <input type="hidden" name="minRating" id="hidden_minRating" value="<?= htmlspecialchars($data['filters']['minRating']) ?>">
            <input type="hidden" name="maxPrice" id="hidden_maxPrice" value="<?= htmlspecialchars($data['filters']['maxPrice']) ?>">
            
            <div class="w-full md:w-1/2 relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input 
                    type="text" 
                    name="q"
                    id="searchInput"
                    list="cityOptions"
                    placeholder="Search destinations (e.g., Tokyo, Rome)..." 
                    value="<?= htmlspecialchars($data['filters']['q']) ?>"
                    class="w-full pl-12 pr-4 py-3 rounded-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all text-textdark font-medium"
                >
                <datalist id="cityOptions">
                    <?php foreach($data['cities'] as $city): ?>
                        <option value="<?= htmlspecialchars($city) ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
            
            <div class="flex gap-2 w-full md:w-auto overflow-x-auto pb-2 md:pb-0 items-center">
                <button type="button" onclick="document.getElementById('mobileFilters').classList.toggle('translate-y-full'); document.getElementById('filterOverlay').classList.toggle('opacity-0'); document.getElementById('filterOverlay').classList.toggle('pointer-events-none');" class="md:hidden flex items-center gap-2 px-4 py-2 bg-gray-100 rounded-full text-sm font-medium text-textdark">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                    Filters
                </button>
                <select name="sort" onchange="document.getElementById('searchForm').submit()" class="bg-gray-100 rounded-full px-4 py-2.5 text-sm font-bold text-textdark border-none outline-none focus:ring-2 focus:ring-primary cursor-pointer appearance-none pr-8 relative">
                    <option value="recommended" <?= $data['filters']['sort'] === 'recommended' ? 'selected' : '' ?>>Recommended</option>
                    <option value="price_asc" <?= $data['filters']['sort'] === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price_desc" <?= $data['filters']['sort'] === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                    <option value="rating" <?= $data['filters']['sort'] === 'rating' ? 'selected' : '' ?>>Top Rated</option>
                </select>
                <button type="submit" class="hidden md:block bg-primary text-white px-6 py-2.5 rounded-full font-bold ml-2 hover:bg-primaryHover transition">Search</button>
            </div>
        </form>
    </header>

    <div class="flex-1 max-w-7xl mx-auto w-full flex flex-col md:flex-row px-4 md:px-8 py-8 gap-8 text-textdark">
        <!-- Filters Sidebar / Mobile Bottom Sheet -->
        <aside id="filterOverlay" class="fixed inset-0 z-50 bg-black/50 transition-opacity opacity-0 pointer-events-none md:static md:bg-transparent md:w-64 md:block shrink-0 md:opacity-100 md:pointer-events-auto">
            <form id="sidebarForm" action="<?= URLROOT ?>/experiences" method="GET" class="fixed bottom-0 left-0 right-0 bg-white rounded-t-3xl p-6 transition-transform duration-300 translate-y-full md:static md:translate-y-0 md:p-0 md:bg-transparent md:block h-[85vh] md:h-auto overflow-y-auto">
                <input type="hidden" name="q" value="<?= htmlspecialchars($data['filters']['q']) ?>">
                <input type="hidden" name="sort" value="<?= htmlspecialchars($data['filters']['sort']) ?>">

                <div class="flex justify-between items-center mb-6 md:hidden">
                    <h2 class="text-xl font-extrabold font-serif">Filters</h2>
                    <button type="button" onclick="document.getElementById('mobileFilters').classList.add('translate-y-full'); document.getElementById('filterOverlay').classList.add('opacity-0'); document.getElementById('filterOverlay').classList.add('pointer-events-none');" class="p-2 bg-gray-100 rounded-full"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>

                <div id="mobileFilters" class="space-y-8 h-full">
                    <!-- Category -->
                    <div>
                        <h3 class="font-bold mb-4 text-secondary text-lg">Category</h3>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach($data['categories'] as $cat): ?>
                                <label class="cursor-pointer">
                                    <input type="radio" name="category" value="<?= $cat ?>" <?= $data['filters']['category'] === $cat ? 'checked' : '' ?> onchange="document.getElementById('sidebarForm').submit()" class="peer hidden">
                                    <div class="px-4 py-2 rounded-full text-sm font-medium transition-colors peer-checked:bg-primary peer-checked:text-white bg-white border border-accent text-gray-700 hover:bg-accent/20">
                                        <?= htmlspecialchars($cat) ?>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                            <?php if(!empty($data['filters']['category'])): ?>
                                <label class="cursor-pointer">
                                    <input type="radio" name="category" value="" onchange="document.getElementById('sidebarForm').submit()" class="peer hidden">
                                    <div class="px-4 py-2 rounded-full text-sm font-medium transition-colors bg-gray-100 text-gray-600 hover:bg-gray-200">
                                        Clear
                                    </div>
                                </label>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Price -->
                    <div>
                        <h3 class="font-bold mb-4 text-secondary text-lg flex justify-between">
                            <span>Max Price</span>
                            <span class="text-primary" id="priceDisplay">₹<?= $data['filters']['maxPrice'] ?></span>
                        </h3>
                        <input type="range" name="maxPrice" min="10" max="500" step="10" value="<?= $data['filters']['maxPrice'] ?>"
                            oninput="document.getElementById('priceDisplay').innerText = '₹' + this.value"
                    </div>

                    <!-- Duration -->
                    <div>
                        <h3 class="font-bold mb-4 text-secondary text-lg">Duration</h3>
                        <div class="space-y-3">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="radio" name="duration" value="" <?= empty($data['filters']['duration']) ? 'checked' : '' ?> onchange="document.getElementById('sidebarForm').submit()" class="w-4 h-4 text-primary focus:ring-primary accent-primary">
                                <span class="text-gray-700 font-medium">Any</span>
                            </label>
                            <?php foreach($data['durations'] as $dur): ?>
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="radio" name="duration" value="<?= $dur ?>" <?= $data['filters']['duration'] === $dur ? 'checked' : '' ?> onchange="document.getElementById('sidebarForm').submit()" class="w-4 h-4 text-primary focus:ring-primary accent-primary">
                                    <span class="text-gray-700 font-medium"><?= htmlspecialchars($dur) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Rating -->
                    <div>
                        <h3 class="font-bold mb-4 text-secondary text-lg">Minimum Rating</h3>
                        <div class="flex flex-wrap gap-2">
                            <label class="cursor-pointer">
                                <input type="radio" name="minRating" value="0" <?= $data['filters']['minRating'] == 0 ? 'checked' : '' ?> onchange="document.getElementById('sidebarForm').submit()" class="peer hidden">
                                <div class="px-3 py-1.5 rounded-full border text-sm font-bold peer-checked:border-primary peer-checked:bg-primary/10 peer-checked:text-primary border-accent text-gray-600 bg-white">
                                    Any
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="minRating" value="4" <?= $data['filters']['minRating'] == 4 ? 'checked' : '' ?> onchange="document.getElementById('sidebarForm').submit()" class="peer hidden">
                                <div class="flex items-center gap-1 px-3 py-1.5 rounded-full border text-sm font-bold peer-checked:border-primary peer-checked:bg-primary/10 peer-checked:text-primary border-accent text-gray-600 bg-white">
                                    4.0+ <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                </div>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="minRating" value="4.5" <?= $data['filters']['minRating'] == 4.5 ? 'checked' : '' ?> onchange="document.getElementById('sidebarForm').submit()" class="peer hidden">
                                <div class="flex items-center gap-1 px-3 py-1.5 rounded-full border text-sm font-bold peer-checked:border-primary peer-checked:bg-primary/10 peer-checked:text-primary border-accent text-gray-600 bg-white">
                                    4.5+ <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div class="sticky bottom-0 bg-white pt-4 pb-2 mt-8 md:hidden border-t border-accent">
                    <button type="button" onclick="document.getElementById('filterOverlay').classList.add('opacity-0'); document.getElementById('filterOverlay').classList.add('pointer-events-none');" class="w-full bg-primary text-white py-3 rounded-xl font-bold hover:bg-primaryHover">Show Results</button>
                </div>
            </form>
        </aside>

        <!-- Results Grid -->
        <main class="flex-1 min-w-0">
            <div class="mb-8 flex justify-between items-end border-b border-accent/40 pb-4">
                <h1 class="text-3xl font-extrabold text-secondary font-serif">
                    <?php if(!empty($data['filters']['q'])): ?>
                        Experiences in "<?= htmlspecialchars($data['filters']['q']) ?>"
                    <?php else: ?>
                        All Discoveries
                    <?php endif; ?>
                    <span class="block text-sm font-bold text-gray-500 mt-2 tracking-wide uppercase"><?= count($data['experiences']) ?> activities found</span>
                </h1>
            </div>

            <?php if(empty($data['experiences'])): ?>
                <div class="text-center py-24 bg-white rounded-3xl border border-accent shadow-sm">
                    <div class="text-6xl mb-6">🏜️</div>
                    <h3 class="text-2xl font-bold text-secondary font-serif mb-2">No experiences found</h3>
                    <p class="text-gray-500 mb-8 max-w-md mx-auto font-medium">We couldn't find any activities matching your exact filters. Try adjusting your search, category, or price range to find hidden gems.</p>
                    <a href="<?= URLROOT ?>/experiences" class="inline-block px-8 py-3 bg-secondary text-white rounded-full font-bold hover:bg-opacity-90 transition shadow-md hover:-translate-y-0.5">Clear all filters</a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach($data['experiences'] as $exp): ?>
                        <div class="group relative bg-white pos-relative rounded-2xl overflow-hidden hover:shadow-xl transition-all duration-300 border border-accent/40 flex flex-col h-full hover:-translate-y-1">
                            <div class="relative aspect-[4/3] overflow-hidden bg-gray-100">
                                <img src="<?= htmlspecialchars($exp->image) ?>" alt="<?= htmlspecialchars($exp->title) ?>" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-out" />
                                
                                <!-- Vanilla JS Save toggle -->
                                <button onclick="this.querySelector('svg').classList.toggle('fill-primary'); this.querySelector('svg').classList.toggle('text-primary'); this.querySelector('svg').classList.toggle('text-gray-600'); event.preventDefault();" class="absolute top-3 right-3 z-10 p-2 bg-white/90 backdrop-blur rounded-full hover:bg-white transition-colors shadow-sm">
                                    <svg class="w-5 h-5 text-gray-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                </button>
                                
                                <div class="absolute top-3 left-3 px-3 py-1 z-10 bg-white/95 backdrop-blur shadow-sm rounded-full text-xs font-black text-secondary uppercase tracking-wider">
                                    <?= htmlspecialchars($exp->category) ?>
                                </div>
                            </div>
                            
                            <div class="p-5 flex flex-col flex-1 relative z-10">
                                <div class="flex justify-between items-start mb-3 gap-2">
                                    <a href="<?= URLROOT ?>/experiences/show/<?= $exp->id ?>" class="font-bold text-lg text-secondary leading-tight group-hover:text-primary transition-colors line-clamp-2">
                                        <?= htmlspecialchars($exp->title) ?>
                                    </a>
                                    <div class="flex items-center gap-1 text-sm font-black text-textdark bg-bgwarm border border-accent/50 px-2 py-1 rounded-md shrink-0">
                                        <svg class="w-3.5 h-3.5 fill-primary text-primary" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                        <?= number_format($exp->rating, 1) ?>
                                        <span class="text-gray-400 text-xs font-medium ml-0.5">(<?= $exp->reviews ?>)</span>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-4 text-sm text-gray-500 mb-5 font-medium">
                                    <div class="flex items-center gap-1.5"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> <span class="truncate max-w-[120px]"><?= htmlspecialchars($exp->location) ?></span></div>
                                    <div class="flex items-center gap-1.5"><svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> <?= htmlspecialchars($exp->duration) ?></div>
                                </div>

                                <div class="mt-auto pt-4 border-t border-accent/40 flex items-center justify-between">
                                    <div class="flex items-center gap-2.5">
                                        <div class="relative">
                                            <img src="<?= htmlspecialchars($exp->hostAvatar) ?>" alt="<?= htmlspecialchars($exp->host) ?>" class="w-8 h-8 rounded-full border-2 border-white shadow-sm object-cover" />
                                            <?php if($exp->verified): ?>
                                                <div class="absolute -bottom-1 -right-1 bg-blue-500 rounded-full p-0.5 border-2 border-white text-white">
                                                    <svg class="w-2 h-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <span class="text-sm font-black text-secondary">by <?= htmlspecialchars($exp->host) ?></span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-2xl font-black text-secondary tracking-tight">₹<?= number_format($exp->price) ?></span>
                                        <span class="text-[10px] uppercase font-bold text-gray-400 block tracking-widest">/ PERSON</span>
                                    </div>
                                </div>
                            </div>
                            <a href="<?= URLROOT ?>/experiences/show/<?= $exp->id ?>" class="absolute inset-0 z-0"><span class="sr-only">View Experience</span></a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-16 text-center">
                    <button class="bg-white border-2 border-primary text-primary hover:bg-primary hover:text-white px-10 py-3.5 rounded-full font-bold shadow-md transition transform hover:-translate-y-0.5 duration-300">
                        Load More Experiences
                    </button>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php require_once '../app/views/components/footer.php'; ?>
                                            <img src="<?= htmlspecialchars($exp->hostAvatar) ?>" alt="<?= htmlspecialchars($exp->host) ?>" class="w-9 h-9 rounded-full border-2 border-white shadow-sm object-cover" />
                                            <?php if($exp->verified): ?>
                                                <div class="absolute -bottom-1 -right-1 bg-blue-500 rounded-full p-0.5 border-2 border-white" title="Verified Host">
                                                    <svg class="w-2.5 h-2.5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <span class="text-sm font-bold text-secondary">by <?= htmlspecialchars($exp->host) ?></span>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-extrabold text-xl text-primary">₹<?= htmlspecialchars($exp->price) ?></span>
                                        <span class="text-xs text-gray-500 font-bold block uppercase tracking-wide">/ person</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php require_once '../app/views/components/footer.php'; ?>