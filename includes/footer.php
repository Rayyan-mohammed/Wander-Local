<?php
// includes/footer.php
?>
<footer class="bg-dark text-white pt-5 pb-4 mt-5">
    <div class="container pt-4">
        <div class="row gy-4">
            <!-- Brand & Connect -->
            <div class="col-12 col-lg-4 pe-lg-5">
                <h4 class="font-heading mb-3"><i class="fa-solid fa-compass me-2 text-accent-custom"></i>WanderLocal</h4>
                <p class="text-white-50 mb-4">Discover the soul of every city through authentic experiences led by passionate locals. Travel deeper, not wider.</p>
                <div class="d-flex gap-3 fs-5 text-white-50">
                    <a href="#" class="text-white-50 text-decoration-none hover-white transition"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="text-white-50 text-decoration-none hover-white transition"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#" class="text-white-50 text-decoration-none hover-white transition"><i class="fa-brands fa-tiktok"></i></a>
                    <a href="#" class="text-white-50 text-decoration-none hover-white transition"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>

            <!-- Links -->
            <div class="col-6 col-md-3 col-lg-2">
                <h6 class="fw-bold mb-3">Explore</h6>
                <ul class="nav flex-column gap-2 text-white-50">
                    <li><a href="#" class="text-reset text-decoration-none">Destinations</a></li>
                    <li><a href="#" class="text-reset text-decoration-none">Culinary Tours</a></li>
                    <li><a href="#" class="text-reset text-decoration-none">Art & Culture</a></li>
                    <li><a href="#" class="text-reset text-decoration-none">Our Blog</a></li>
                </ul>
            </div>

            <div class="col-6 col-md-3 col-lg-2">
                <h6 class="fw-bold mb-3">For Hosts</h6>
                <ul class="nav flex-column gap-2 text-white-50">
                    <li><a href="#" class="text-reset text-decoration-none">Become a Host</a></li>
                    <li><a href="#" class="text-reset text-decoration-none">Host Guidelines</a></li>
                    <li><a href="#" class="text-reset text-decoration-none">Community Center</a></li>
                    <li><a href="#" class="text-reset text-decoration-none">Safety</a></li>
                </ul>
            </div>

            <!-- Newsletter -->
            <div class="col-12 col-md-6 col-lg-4">
                <h6 class="fw-bold mb-3">Travel Inspiration to your Inbox</h6>
                <p class="text-white-50 small mb-3">Get curated local experiences and travel tips.</p>
                <form action="<?= BASE_URL ?>/api/newsletter.php" method="POST" class="d-flex gap-2">
                    <input type="email" name="email" class="form-control bg-transparent border-secondary text-white" placeholder="Email address" required>
                    <button type="submit" class="btn btn-primary px-3"><i class="fa-solid fa-paper-plane"></i></button>
                </form>
            </div>
        </div>

        <hr class="border-secondary my-4">

        <div class="row align-items-center text-white-50 small">
            <div class="col-md-6 text-center text-md-start mb-2 mb-md-0">
                &copy; <?= date('Y') ?> WanderLocal. All rights reserved.
            </div>
            <div class="col-md-6 text-center text-md-end">
                <a href="#" class="text-reset text-decoration-none me-3">Privacy Policy</a>
                <a href="#" class="text-reset text-decoration-none">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<!-- Initialize Global Plugins -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        AOS.init({
            duration: 800,
            once: true,
            offset: 100
        });
    });
</script>

</body>
</html>
