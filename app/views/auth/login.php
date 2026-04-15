<?php require_once '../app/views/components/header.php'; ?>

<div class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-xl shadow-lg border border-gray-100">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Sign in to your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Or
                <a href="<?= URLROOT ?>/auth/register" class="font-medium text-orange-600 hover:text-orange-500">
                    start your journey today
                </a>
            </p>
        </div>
        
        <form class="mt-8 space-y-6" action="<?= URLROOT ?>/auth/login" method="POST">
            <div class="rounded-md shadow-sm -space-y-px">
                <div class="pb-3">
                    <label for="email-address" class="sr-only">Email address</label>
                    <input id="email-address" name="email" type="email" autocomplete="email" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-orange-500 focus:border-orange-500 focus:z-10 sm:text-sm" placeholder="Email address">
                    <span class="text-red-500 text-xs"><?= $data['email_err'] ?? '' ?></span>
                </div>
                <div class="pb-3">
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-orange-500 focus:border-orange-500 focus:z-10 sm:text-sm" placeholder="Password">
                    <span class="text-red-500 text-xs"><?= $data['password_err'] ?? '' ?></span>
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                    Sign in
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../app/views/components/footer.php'; ?>