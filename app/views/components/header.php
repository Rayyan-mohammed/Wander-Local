<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?? SITENAME ?></title>
    <!-- Tailwind CSS (via CDN for rapid development, utilizing our custom design system tokens) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#C05640',
                        primaryHover: '#A3422E',
                        secondary: '#2E4F4F',
                        accent: '#E7D8C9',
                        bgwarm: '#FAF9F6',
                        textdark: '#2C3333',
                        textmuted: '#64748B'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;0,800;1,400;1,600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-bgwarm text-textdark flex flex-col min-h-screen selection:bg-primary selection:text-white">
    
    <!-- Navbar -->
    <header class="bg-white shadow-sm border-b border-accent/20 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex-shrink-0">
                    <a href="<?= URLROOT ?>" class="text-3xl font-bold font-serif tracking-tight text-secondary">
                        Wander<span class="text-primary italic">Local</span>
                    </a>
                </div>
                <!-- Navigation -->
                <nav class="hidden md:flex space-x-8 items-center">
                    <a href="<?= URLROOT ?>/experiences" class="text-textdark hover:text-primary px-3 py-2 rounded-md font-medium transition">Experiences</a>
                    <a href="<?= URLROOT ?>/blog" class="text-textdark hover:text-primary px-3 py-2 rounded-md font-medium transition">Journal</a>
                    
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="<?= URLROOT ?>/dashboard" class="text-textdark hover:text-primary px-3 py-2 rounded-md font-medium transition">Dashboard</a>
                        <a href="<?= URLROOT ?>/auth/logout" class="bg-accent/20 text-secondary hover:bg-accent/40 border border-accent/50 px-5 py-2.5 rounded hover:-translate-y-0.5 transition shadow-sm font-medium">Logout</a>
                    <?php else: ?>
                        <a href="<?= URLROOT ?>/auth/login" class="text-textdark hover:text-primary px-3 py-2 rounded-md font-medium transition">Log in</a>
                        <a href="<?= URLROOT ?>/auth/register" class="bg-primary text-white hover:bg-primaryHover px-5 py-2.5 rounded hover:-translate-y-0.5 transition shadow-sm font-bold">Become a Host</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>
    
    <!-- Main Content wrapper -->
    <main class="flex-grow">
