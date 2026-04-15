    </main>
    <footer class="bg-textdark text-white pt-20 pb-10">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-4 gap-12 mb-16 border-b border-gray-700 pb-16">
            <div class="col-span-1 md:col-span-1">
                <div class="text-3xl font-bold mb-4 font-serif text-white">Wander<span class="text-primary italic">Local</span></div>
                <p class="text-gray-400 text-sm leading-relaxed mb-6">Connecting right travelers with right locals, turning every journey into a genuine and unforgettable adventure.</p>
                <div class="flex gap-4 text-gray-400">
                    <span class="cursor-pointer hover:text-white transition">Instagram</span>
                    <span class="cursor-pointer hover:text-white transition">Twitter</span>
                </div>
            </div>
            
            <div>
                <h4 class="font-bold mb-4">Discover</h4>
                <ul class="space-y-2 text-gray-400 text-sm">
                    <li><a href="<?= URLROOT ?>/experiences" class="hover:text-white transition">All Experiences</a></li>
                    <li><a href="#" class="hover:text-white transition">Destinations</a></li>
                    <li><a href="<?= URLROOT ?>/blog" class="hover:text-white transition">Travel Journal</a></li>
                    <li><a href="#" class="hover:text-white transition">Safety Standards</a></li>
                </ul>
            </div>
            
            <div>
                <h4 class="font-bold mb-4">Hosting</h4>
                <ul class="space-y-2 text-gray-400 text-sm">
                    <li><a href="#" class="hover:text-white transition">Why Host</a></li>
                    <li><a href="#" class="hover:text-white transition">Host Resources</a></li>
                    <li><a href="#" class="hover:text-white transition">Community Forum</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-bold mb-4">Join our newsletter</h4>
                <p class="text-gray-400 text-sm mb-4">Get curated underground experiences sent to your inbox monthly.</p>
                <form class="flex" action="#" method="POST" onsubmit="event.preventDefault();">
                    <input type="email" placeholder="Email address" class="bg-gray-800 text-white px-4 py-2 w-full rounded-l-md outline-none focus:ring-1 focus:ring-primary">
                    <button type="submit" class="bg-primary px-4 py-2 rounded-r-md font-medium hover:bg-primaryHover transition">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>
        
        <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row justify-between items-center text-xs text-gray-500">
            <p>&copy; <?= date('Y') ?> Wander Local. All rights reserved.</p>
            <div class="flex gap-6 mt-4 md:mt-0">
                <a href="#" class="hover:text-white transition">Privacy Policy</a>
                <a href="#" class="hover:text-white transition">Terms of Service</a>
            </div>
        </div>
    </footer>
</body>
</html>
