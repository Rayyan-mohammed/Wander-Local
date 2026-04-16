<?php
// pages/experiences.php
require_once __DIR__ . '/../includes/header.php';
?>

<div class="bg-light py-4" x-data="searchApp()" x-init="init()">
    <div class="container">
        
        <!-- Mobile Filter Toggle -->
        <button class="btn btn-outline-primary d-lg-none w-100 mb-4" type="button" @click="mobileFiltersOpen = true">
            <i class="fa-solid fa-sliders me-2"></i> Show Filters
        </button>

        <div class="row g-4">
            
            <!-- SIDEBAR: Filters (Desktop: Col-3, Sticky | Mobile: Offcanvas via Alpine) -->
            <div class="col-lg-3">
                <div class="filter-sidebar bg-white p-4 rounded-4 shadow-sm border sticky-top" style="top: 100px; z-index: 1020;" 
                     :class="{ 'd-none d-lg-block': !mobileFiltersOpen, 'position-fixed top-0 start-0 w-75 h-100 overflow-auto m-0 rounded-0': mobileFiltersOpen }">
                     
                    <!-- Mobile Close -->
                    <div class="d-flex justify-content-between align-items-center d-lg-none mb-4">
                        <h5 class="mb-0 fw-bold">Filters</h5>
                        <button type="button" class="btn-close" @click="mobileFiltersOpen = false"></button>
                    </div>

                    <h5 class="fw-bold mb-4 font-heading d-none d-lg-block">Filters</h5>

                    <!-- Destination Search -->
                    <div class="mb-4 position-relative">
                        <label class="form-label fw-bold small text-uppercase">Destination</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 ps-0" placeholder="E.g., Mumbai" x-model="filters.city" @input.debounce.500ms="fetchCitySuggestions()" autocomplete="off">
                        </div>
                        
                        <!-- AJAX Suggestions dropdown -->
                        <ul class="list-group position-absolute w-100 shadow-sm" style="z-index: 10; top: 100%;" x-show="citySuggestions.length > 0" @click.away="citySuggestions = []">
                            <template x-for="city in citySuggestions" :key="city.city">
                                <li class="list-group-item list-group-item-action cursor-pointer" @click="selectCity(city)">
                                    <i class="fa-solid fa-location-dot me-2 text-primary-custom"></i>
                                    <span x-text="city.city + ', ' + city.country"></span>
                                </li>
                            </template>
                        </ul>
                    </div>

                    <!-- Category Checkboxes -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase">Category</label>
                        <div class="d-flex flex-column gap-2">
                            <template x-for="cat in ['Food & Drink', 'Workshop', 'Nature & Outdoors', 'Art & Culture', 'History & Heritage', 'Nightlife', 'Adventure', 'Wellness']">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" :value="cat" :id="'cat_'+cat" x-model="filters.category" @change="applyFilters()">
                                    <label class="form-check-label text-muted" :for="'cat_'+cat" x-text="cat"></label>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase">Price Range ($)</label>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <input type="number" class="form-control form-control-sm text-center" x-model="filters.min_price" @change="applyFilters()">
                            <span class="text-muted">-</span>
                            <input type="number" class="form-control form-control-sm text-center" x-model="filters.max_price" @change="applyFilters()">
                        </div>
                        <input type="range" class="form-range custom-range" min="0" max="5000" x-model="filters.max_price" @change="applyFilters()">
                    </div>

                    <!-- Duration Radio -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase">Duration</label>
                        <div class="d-flex flex-column gap-2">
                            <template x-for="dur in [{val: '', label: 'Any'}, {val: 'half-day', label: 'Half-day (< 4 hrs)'}, {val: 'full-day', label: 'Full-day (4 - 8 hrs)'}, {val: 'multi-day', label: 'Multi-day (8+ hrs)'}]">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="duration" :value="dur.val" :id="'dur_'+dur.val" x-model="filters.duration" @change="applyFilters()">
                                    <label class="form-check-label text-muted" :for="'dur_'+dur.val" x-text="dur.label"></label>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Rating Radio -->
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase">Rating</label>
                        <div class="d-flex flex-column gap-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="0" id="rating_0" x-model="filters.rating" @change="applyFilters()">
                                <label class="form-check-label text-muted" for="rating_0">Any</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="4" id="rating_4" x-model="filters.rating" @change="applyFilters()">
                                <label class="form-check-label text-muted" for="rating_4"><i class="fa-solid fa-star text-accent-custom"></i> 4.0 & up</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" value="3" id="rating_3" x-model="filters.rating" @change="applyFilters()">
                                <label class="form-check-label text-muted" for="rating_3"><i class="fa-solid fa-star text-accent-custom"></i> 3.0 & up</label>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary fw-bold" @click="applyFilters(); mobileFiltersOpen = false;">Apply Filters</button>
                        <button type="button" class="btn btn-link text-muted text-decoration-none" @click="clearFilters()">Clear All</button>
                    </div>
                </div>
                <!-- Offcanvas Backdrop -->
                <div class="position-fixed top-0 start-0 w-100 h-100 bg-dark opacity-50 d-lg-none" style="z-index: 1010;" x-show="mobileFiltersOpen" @click="mobileFiltersOpen = false"></div>
            </div>

            <!-- RESULTS AREA -->
            <div class="col-lg-9">
                <!-- Results Header -->
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center bg-white p-3 rounded-4 shadow-sm border mb-4">
                    <span class="text-muted fw-medium mb-2 mb-sm-0" x-show="!loading">
                        Showing <span class="fw-bold text-dark" x-text="experiences.length"></span> of <span class="fw-bold text-dark" x-text="totalResults"></span> experiences <span x-show="filters.city">in <span class="text-primary-custom fw-bold" x-text="filters.city"></span></span>
                    </span>
                    <span class="text-muted fw-medium mb-2 mb-sm-0" x-show="loading">
                        Loading results...
                    </span>
                    <div class="d-flex align-items-center gap-2">
                        <label class="text-muted small fw-bold mb-0">Sort By:</label>
                        <select class="form-select form-select-sm border-0 fw-bold border-bottom rounded-0" style="width: auto;" x-model="filters.sort" @change="applyFilters()">
                            <option value="recommended">Recommended</option>
                            <option value="price_asc">Price: Low to High</option>
                            <option value="price_desc">Price: High to Low</option>
                            <option value="rating">Highest Rated</option>
                            <option value="newest">Newest</option>
                        </select>
                    </div>
                </div>

                <!-- Skeletons & Grid -->
                <div class="row g-4">
                    
                    <!-- Skeletons (Alpine x-show) -->
                    <template x-if="loading">
                        <template x-for="i in 9">
                            <div class="col-md-6 col-xl-4">
                                <div class="custom-card h-100 border-0">
                                    <div class="skeleton bg-secondary w-100" style="aspect-ratio: 4/3; opacity: 0.1;"></div>
                                    <div class="p-3">
                                        <div class="skeleton bg-secondary w-75 h-4 mb-2 rounded" style="opacity: 0.1;"></div>
                                        <div class="skeleton bg-secondary w-50 h-4 mb-3 rounded" style="opacity: 0.1;"></div>
                                        <div class="d-flex justify-content-between mt-3 pt-3 border-top">
                                            <div class="skeleton bg-secondary rounded-circle" style="width: 24px; height: 24px; opacity: 0.1;"></div>
                                            <div class="skeleton bg-secondary w-25 h-4 rounded" style="opacity: 0.1;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </template>

                    <!-- Real Content -->
                    <template x-if="!loading && experiences.length > 0">
                        <template x-for="exp in experiences" :key="exp.id">
                            <div class="col-md-6 col-xl-4">
                                <div class="custom-card h-100 rounded-4 border bg-white position-relative">
                                    <div class="card-img-wrap position-relative">
                                        <span class="badge-category shadow-sm" x-text="exp.category"></span>
                                        <!-- Wishlist Heart -->
                                        <button class="btn btn-light rounded-circle shadow-sm position-absolute text-muted" 
                                                style="top: 1rem; right: 1rem; z-index: 10; width: 40px; height: 40px;"
                                                @click.prevent="toggleWishlist(exp)">
                                            <i class="fa-heart fs-5 transition" :class="exp.is_wishlisted > 0 ? 'fa-solid text-danger' : 'fa-regular'"></i>
                                        </button>
                                        <a :href="'<?= BASE_URL ?>/pages/experience_detail.php?id=' + exp.id">
                                            <img :src="exp.cover_image" :alt="exp.title" class="w-100 h-100 object-fit-cover">
                                        </a>
                                    </div>
                                    <div class="p-3 pb-4 d-flex flex-column h-100">
                                        <div class="d-flex justify-content-between align-items-start mb-2 gap-2">
                                            <a :href="'<?= BASE_URL ?>/pages/experience_detail.php?id=' + exp.id" class="text-decoration-none text-dark hover-primary transition">
                                                <h5 class="fw-bold mb-0 lh-sm" style="font-size: 1.1rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" x-text="exp.title"></h5>
                                            </a>
                                        </div>
                                        
                                        <p class="text-muted small mb-2 d-flex align-items-center gap-1">
                                            <i class="fa-solid fa-location-dot"></i> 
                                            <span x-text="exp.city + ', ' + exp.country"></span>
                                        </p>

                                        <div class="d-flex align-items-center gap-3 text-muted small fw-medium mb-3">
                                            <span title="Duration"><i class="fa-regular fa-clock me-1"></i> <span x-text="exp.duration_hours + 'h'"></span></span>
                                            <span title="Max Guests"><i class="fa-solid fa-users me-1"></i> <span x-text="'Max ' + exp.max_guests"></span></span>
                                        </div>

                                        <div class="mt-auto pt-3 border-top d-flex flex-column gap-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="d-flex align-items-center gap-2 small fw-bold">
                                                    <img :src="exp.host_avatar" class="rounded-circle" width="24" height="24">
                                                    <span x-text="exp.host_name.split(' ')[0]"></span>
                                                    <i x-show="exp.is_verified > 0" class="fa-solid fa-circle-check text-secondary-custom" title="Verified Host"></i>
                                                </div>
                                                <div class="rating-stars fw-bold">
                                                    <i class="fa-solid fa-star"></i> <span x-text="exp.rating_formatted"></span> <span class="text-muted fw-normal" x-text="'(' + exp.review_count + ')'"></span>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                <div class="fs-5 fw-bold text-dark">$<span x-text="exp.price_formatted"></span> <span class="fs-6 fw-normal text-muted">/ pp</span></div>
                                                <a :href="'<?= BASE_URL ?>/pages/experience_detail.php?id=' + exp.id" class="btn btn-primary rounded-pill btn-sm px-3 fw-bold">Book Now</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </template>

                    <!-- Empty State -->
                    <template x-if="!loading && experiences.length === 0">
                        <div class="col-12 text-center py-5">
                            <div class="text-muted mb-4 opacity-50"><i class="fa-solid fa-map-location-dot" style="font-size: 5rem;"></i></div>
                            <h3 class="font-heading fw-bold text-dark">No experiences found</h3>
                            <p class="text-muted mb-4">We couldn't find any experiences matching your current filters.</p>
                            <button class="btn btn-outline-primary rounded-pill fw-bold" @click="clearFilters()">Clear All Filters</button>
                        </div>
                    </template>
                </div>

                <!-- Pagination -->
                <nav class="mt-5" x-show="!loading && totalPages > 1">
                    <ul class="pagination justify-content-center">
                        <li class="page-item" :class="{'disabled': filters.page <= 1}">
                            <button class="page-link shadow-none border-0 text-primary-custom fw-bold rounded-pill mx-1" @click="goToPage(filters.page - 1)">Previous</button>
                        </li>
                        
                        <template x-for="p in totalPages" :key="p">
                            <li class="page-item" :class="{'active': p === filters.page}">
                                <button class="page-link shadow-none border-0 rounded-circle text-center mx-1 fw-bold" 
                                        style="width: 40px; height: 40px; line-height: 24px;"
                                        :class="p === filters.page ? 'bg-primary text-white' : 'text-dark bg-white'"
                                        @click="goToPage(p)" x-text="p"></button>
                            </li>
                        </template>

                        <li class="page-item" :class="{'disabled': filters.page >= totalPages}">
                            <button class="page-link shadow-none border-0 text-primary-custom fw-bold rounded-pill mx-1" @click="goToPage(filters.page + 1)">Next</button>
                        </li>
                    </ul>
                </nav>

            </div>
        </div>
    </div>
</div>

<style>
/* CSS Animations for Skeletons */
@keyframes shimmer {
    0% { background-position: -1000px 0; }
    100% { background-position: 1000px 0; }
}
.skeleton {
    animation : shimmer 2s infinite linear;
    background: linear-gradient(to right, #eff1f3 4%, #e2e2e2 25%, #eff1f3 36%);
    background-size: 1000px 100%;
}
.custom-range::-webkit-slider-thumb {
    background: var(--primary);
}
.custom-range::-moz-range-thumb {
    background: var(--primary);
}
.hover-primary:hover {
    color: var(--primary) !important;
}
</style>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('searchApp', () => ({
        loading: true,
        mobileFiltersOpen: false,
        experiences: [],
        totalResults: 0,
        totalPages: 1,
        citySuggestions: [],
        filters: {
            city: '',
            category: [],
            min_price: 0,
            max_price: 5000,
            duration: '',
            rating: 0,
            lang: [],
            sort: 'recommended',
            page: 1
        },

        init() {
            // Parse initial URL parameters into filters objects
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('city')) this.filters.city = urlParams.get('city');
            if (urlParams.has('min_price')) this.filters.min_price = parseInt(urlParams.get('min_price'));
            if (urlParams.has('max_price')) this.filters.max_price = parseInt(urlParams.get('max_price'));
            if (urlParams.has('duration')) this.filters.duration = urlParams.get('duration');
            if (urlParams.has('rating')) this.filters.rating = parseInt(urlParams.get('rating'));
            if (urlParams.has('sort')) this.filters.sort = urlParams.get('sort');
            if (urlParams.has('page')) this.filters.page = parseInt(urlParams.get('page'));
            
            // Arrays
            if (urlParams.has('category')) {
                const catStr = urlParams.get('category');
                this.filters.category = catStr ? catStr.split(',') : [];
            }
            if (urlParams.has('lang')) {
                const langStr = urlParams.get('lang');
                this.filters.lang = langStr ? langStr.split(',') : [];
            }

            this.applyFilters(false); // Initial load without pushing history
        },

        async fetchCitySuggestions() {
            if (this.filters.city.length < 2) {
                this.citySuggestions = [];
                return;
            }
            try {
                const res = await fetch(`<?= BASE_URL ?>/api/city_search.php?q=${encodeURIComponent(this.filters.city)}`);
                this.citySuggestions = await res.json();
            } catch (err) {
                console.error(err);
            }
        },

        selectCity(cityObj) {
            this.filters.city = cityObj.city;
            this.citySuggestions = [];
            this.applyFilters();
        },

        clearFilters() {
            this.filters = { city: '', category: [], min_price: 0, max_price: 5000, duration: '', rating: 0, lang: [], sort: 'recommended', page: 1 };
            this.applyFilters();
        },

        goToPage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.filters.page = page;
                this.applyFilters();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        },

        async applyFilters(pushHistory = true) {
            this.loading = true;
            this.mobileFiltersOpen = false;
            
            // Build query string
            const params = new URLSearchParams();
            if (this.filters.city) params.append('city', this.filters.city);
            if (this.filters.min_price > 0) params.append('min_price', this.filters.min_price);
            if (this.filters.max_price < 5000) params.append('max_price', this.filters.max_price);
            if (this.filters.duration) params.append('duration', this.filters.duration);
            if (this.filters.rating > 0) params.append('rating', this.filters.rating);
            if (this.filters.sort !== 'recommended') params.append('sort', this.filters.sort);
            if (this.filters.page > 1) params.append('page', this.filters.page);
            
            // Handle arrays (Category, Lang) correctly so PHP parses them as arrays 
            // In API we fetch via $_GET['category'][] if we pass category[]=val.
            // Let's pass multiple for API compatibility:
            this.filters.category.forEach(cat => params.append('category[]', cat));
            this.filters.lang.forEach(l => params.append('lang[]', l));

            const queryString = params.toString();

            // Update UI URL for sharing (use comma-separated for cleaner frontend URL)
            if (pushHistory) {
                const frontendParams = new URLSearchParams(params);
                frontendParams.delete('category[]');
                frontendParams.delete('lang[]');
                if (this.filters.category.length) frontendParams.append('category', this.filters.category.join(','));
                if (this.filters.lang.length) frontendParams.append('lang', this.filters.lang.join(','));
                const newUrl = `${window.location.pathname}${frontendParams.toString() ? '?' + frontendParams.toString() : ''}`;
                window.history.pushState({path: newUrl}, '', newUrl);
            }

            try {
                const res = await fetch(`<?= BASE_URL ?>/api/search_experiences.php?${queryString}`);
                const data = await res.json();
                if(data.success) {
                    this.experiences = data.experiences;
                    this.totalResults = data.total;
                    this.totalPages = data.total_pages;
                    this.filters.page = data.current_page;
                }
            } catch (err) {
                console.error('Filter fetch error:', err);
            } finally {
                this.loading = false;
            }
        },

        async toggleWishlist(exp) {
            try {
                const res = await fetch(`<?= BASE_URL ?>/api/wishlist_toggle.php`, {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({experience_id: exp.id})
                });
                const data = await res.json();
                if (data.redirect) {
                    window.location.href = `<?= BASE_URL ?>/pages/auth/login.php`;
                    return;
                }
                if (data.success) {
                    exp.is_wishlisted = data.action === 'added' ? 1 : 0;
                }
            } catch (err) {
                console.error(err);
            }
        }
    }));
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>