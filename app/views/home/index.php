<?php require_once '../app/views/components/header.php'; ?>

<!-- 1. Hero Section -->
<section class="relative min-h-[90vh] flex items-center justify-center overflow-hidden bg-gradient-to-br from-accent via-bgwarm to-[#CBD5C0]">
    <!-- Subtle animated background gradient element -->
    <div class="absolute inset-0 bg-white opacity-40 mix-blend-overlay"></div>
    
    <div class="relative z-10 text-center px-4 max-w-4xl mx-auto mt-8">
        <span class="uppercase tracking-widest text-primary font-bold text-sm mb-4 block">Anti-mainstream travel</span>
        <h1 class="text-5xl md:text-7xl font-extrabold mb-6 leading-tight font-serif text-textdark">
            Discover the <span class="italic text-secondary">soul</span> of your next destination.
        </h1>
        <p class="text-lg md:text-xl text-gray-700 mb-10 max-w-2xl mx-auto font-medium">
            Skip the tourist traps. Connect with passionate locals for raw, authentic, and unforgettable dining, crafts, and storytelling experiences.
        </p>
        
        <!-- Search Bar -->
        <form action="<?= URLROOT ?>/experiences" method="GET" class="flex flex-col md:flex-row items-center bg-white p-2 rounded-lg shadow-xl shadow-secondary/10 max-w-3xl mx-auto border border-accent/50">
            <div class="flex-1 w-full relative">
                <span class="absolute left-4 top-3.5 text-gray-400">ðŸ“</span>
                <input 
                    type="text" 
                    name="q"
                    placeholder="Where are you going? (e.g. Kyoto, Oaxaca, Lisbon)"
                    class="w-full py-3 pl-10 pr-4 outline-none rounded-l-lg bg-transparent text-textdark font-medium"
                >
            </div>
            <button type="submit" class="bg-primary hover:bg-primaryHover text-white font-bold py-3 px-8 w-full md:w-auto rounded-md transition duration-300 mt-2 md:mt-0 shadow-md hover:-translate-y-0.5">
                Find Local Secrets
            </button>
        </form>
    </div>
</section>

<!-- 4. Why We're Different -->
<section class="py-24 bg-secondary text-white">
    <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-2 gap-16 items-center">
        <div>
            <h2 class="text-4xl md:text-5xl mb-6 font-serif font-bold text-white">We believe every place has a story.</h2>
            <p class="text-accent text-lg mb-8 opacity-90 leading-relaxed font-medium">
                Mainstream platforms sell you packaged tours and crowded landmarks. We connect you directly with the people who make a city breathe â€” the street food vendors, the underground musicians, and the hidden historians.
            </p>
            <ul class="space-y-4 text-white font-medium">
                <li class="flex items-center gap-3"><span class="text-primary font-bold text-xl">&check;</span> 100% Locally-led experiences</li>
                <li class="flex items-center gap-3"><span class="text-primary font-bold text-xl">&check;</span> Intimate groups (max 4 people)</li>
                <li class="flex items-center gap-3"><span class="text-primary font-bold text-xl">&check;</span> Direct support to local economies</li>
            </ul>
        </div>
        <div class="relative">
            <div class="aspect-[4/5] bg-gray-800 rounded-xl overflow-hidden shadow-2xl relative z-10 border-4 border-white/10">
                <img src="https://placehold.co/800x800.png" alt="Local crafting" class="object-cover w-full h-full" />
            </div>
            <div class="absolute -bottom-8 -left-8 w-48 h-48 bg-primary rounded-full mix-blend-multiply filter blur-2xl opacity-50"></div>
        </div>
    </div>
</section>

<!-- 2. How It Works -->
<section class="py-24 max-w-7xl mx-auto px-6">
    <div class="text-center mb-16">
        <h2 class="text-3xl md:text-4xl font-bold mb-4 font-serif text-textdark">Journey, simplified.</h2>
        <p class="text-gray-600 font-medium">Whether you're exploring or hosting, it's just authentic connections.</p>
    </div>

    <div class="grid md:grid-cols-2 gap-16">
        <!-- Travelers -->
        <div class="bg-white p-8 md:p-12 rounded-2xl shadow-sm border border-accent/50">
            <h3 class="text-xl font-bold mb-6 text-primary border-b border-accent/50 pb-4">For Travelers</h3>
            <div class="space-y-8">
                <div class="flex gap-4">
                    <div class="w-8 h-8 rounded-full bg-accent flex items-center justify-center font-bold text-secondary shrink-0">1</div>
                    <div>
                        <h4 class="font-bold text-lg text-textdark">Search the unsearchable</h4>
                        <p class="text-gray-600 mt-1">Browse experiences that aren't listed on regular maps.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="w-8 h-8 rounded-full bg-accent flex items-center justify-center font-bold text-secondary shrink-0">2</div>
                    <div>
                        <h4 class="font-bold text-lg text-textdark">Request a booking</h4>
                        <p class="text-gray-600 mt-1">Message your host, agree on a date, and secure your spot.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="w-8 h-8 rounded-full bg-accent flex items-center justify-center font-bold text-secondary shrink-0">3</div>
                    <div>
                        <h4 class="font-bold text-lg text-textdark">Experience the reality</h4>
                        <p class="text-gray-600 mt-1">Meet up, immerse yourself in their culture, and make a friend.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hosts -->
        <div class="bg-white p-8 md:p-12 rounded-2xl shadow-sm border border-accent/50">
            <h3 class="text-xl font-bold mb-6 text-secondary border-b border-accent/50 pb-4">For Hosts</h3>
            <div class="space-y-8">
                <div class="flex gap-4">
                    <div class="w-8 h-8 rounded-full bg-secondary/10 flex items-center justify-center font-bold text-secondary shrink-0">1</div>
                    <div>
                        <h4 class="font-bold text-lg text-textdark">Build your profile</h4>
                        <p class="text-gray-600 mt-1">Verify your identity, add languages, and tell your story.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="w-8 h-8 rounded-full bg-secondary/10 flex items-center justify-center font-bold text-secondary shrink-0">2</div>
                    <div>
                        <h4 class="font-bold text-lg text-textdark">List your passion</h4>
                        <p class="text-gray-600 mt-1">Create an experience for food, art, music, or hidden gems.</p>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="w-8 h-8 rounded-full bg-secondary/10 flex items-center justify-center font-bold text-secondary shrink-0">3</div>
                    <div>
                        <h4 class="font-bold text-lg text-textdark">Host and earn</h4>
                        <p class="text-gray-600 mt-1">Accept requests, meet travelers, and get paid directly.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 5. Featured Hosts -->
<section class="py-24 max-w-7xl mx-auto px-6 border-t border-accent/30">
    <h2 class="text-3xl md:text-4xl font-bold mb-12 text-center font-serif text-textdark">Meet your local guides</h2>
    <div class="grid md:grid-cols-3 gap-8">
        
        <div class="bg-white rounded-xl p-8 shadow-sm border border-accent/40 text-center hover:shadow-lg transition duration-300 hover:-translate-y-1">
            <div class="relative inline-block mb-4">
                <img src="https://placehold.co/800x800.png" alt="Maria Rossi" class="w-24 h-24 rounded-full object-cover border-4 border-bgwarm" />
                <div class="absolute bottom-0 right-0 bg-[#3b82f6] text-white rounded-full p-1 border-2 border-white" title="Verified Host">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                </div>
            </div>
            <h3 class="text-xl font-bold mb-1 text-secondary">Maria Rossi</h3>
            <div class="flex flex-wrap justify-center gap-2 mb-4">
                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full font-medium">English</span>
                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full font-medium">Italian</span>
            </div>
            <p class="text-gray-600 text-sm italic">"Born in Napoli, raised in the kitchen. I'll show you the pasta secrets my nonna forbade me to share."</p>
        </div>

        <div class="bg-white rounded-xl p-8 shadow-sm border border-accent/40 text-center hover:shadow-lg transition duration-300 hover:-translate-y-1">
            <div class="relative inline-block mb-4">
                <img src="https://placehold.co/800x800.png" alt="Omar Farooq" class="w-24 h-24 rounded-full object-cover border-4 border-bgwarm" />
                <div class="absolute bottom-0 right-0 bg-[#3b82f6] text-white rounded-full p-1 border-2 border-white" title="Verified Host">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                </div>
            </div>
            <h3 class="text-xl font-bold mb-1 text-secondary">Omar Farooq</h3>
            <div class="flex flex-wrap justify-center gap-2 mb-4">
                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full font-medium">Arabic</span>
                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full font-medium">French</span>
            </div>
            <p class="text-gray-600 text-sm italic">"Street photographer and tea enthusiast. Let's document the medina from rooftops normal tourists never find."</p>
        </div>

        <div class="bg-white rounded-xl p-8 shadow-sm border border-accent/40 text-center hover:shadow-lg transition duration-300 hover:-translate-y-1">
            <div class="relative inline-block mb-4">
                <img src="https://placehold.co/800x800.png" alt="Yuki Tanaka" class="w-24 h-24 rounded-full object-cover border-4 border-bgwarm" />
                <div class="absolute bottom-0 right-0 bg-[#3b82f6] text-white rounded-full p-1 border-2 border-white" title="Verified Host">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                </div>
            </div>
            <h3 class="text-xl font-bold mb-1 text-secondary">Yuki Tanaka</h3>
            <div class="flex flex-wrap justify-center gap-2 mb-4">
                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full font-medium">Japanese</span>
                <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full font-medium">English</span>
            </div>
            <p class="text-gray-600 text-sm italic">"Former salaryman turned vintage kimono restorer. Discover the fabric districts of Kyoto with me."</p>
        </div>

    </div>
</section>

<!-- 7. CTA Section -->
<section class="relative py-24 border-t border-b border-accent/60 overflow-hidden bg-accent/10">
    <!-- SVG Pattern Background -->
    <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#2E4F4F 1px, transparent 1px); background-size: 32px 32px;"></div>
    
    <div class="max-w-4xl mx-auto px-6 text-center relative z-10">
        <h2 class="text-4xl md:text-5xl font-bold mb-6 text-secondary font-serif">Your city. Your rules.</h2>
        <p class="text-xl text-gray-700 mb-10 font-medium">
            Have a unique passion, a secret recipe, or a key to the best rooftop in town? Turn it into a rewarding experience. Join our community of local hosts.
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-4">
            <a href="<?= URLROOT ?>/auth/register" class="bg-primary text-white font-bold py-4 px-10 rounded-md hover:bg-primaryHover transition shadow-lg hover:-translate-y-0.5 inline-block text-center">
                Start Hosting Today
            </a>
            <a href="<?= URLROOT ?>/auth/login" class="bg-transparent border-2 border-secondary text-secondary font-bold py-4 px-10 rounded-md hover:bg-secondary hover:text-white transition shadow-sm hover:-translate-y-0.5 inline-block text-center">
                Log in
            </a>
        </div>
    </div>
</section>

<?php require_once '../app/views/components/footer.php'; ?>
