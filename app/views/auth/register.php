<?php require_once '../app/views/components/header.php'; ?>

<div class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-xl shadow-lg border border-gray-100">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Join Wander Local
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Already have an account?
                <a href="<?= URLROOT ?>/auth/login" class="font-medium text-orange-600 hover:text-orange-500">
                    Log in instead
                </a>
            </p>
        </div>
        
        <form class="mt-8 space-y-6" action="<?= URLROOT ?>/auth/register" method="POST">
            <div class="rounded-md shadow-sm space-y-3">
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer w-1/2 p-3 border rounded-lg hover:bg-gray-50 bg-white shadow-sm transition">
                        <input type="radio" name="role" value="traveler" class="accent-orange-600 w-5 h-5 text-orange-600 focus:ring-orange-500" <?= (isset($data['role']) && $data['role'] == 'traveler' ? 'checked' : 'checked') ?>>
                        <span class="text-sm font-medium text-gray-700">I am a Traveler</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer w-1/2 p-3 border rounded-lg hover:bg-orange-50 bg-white border-orange-200 transition shadow-sm">
                        <input type="radio" name="role" value="host" class="accent-orange-600 w-5 h-5 text-orange-600 focus:ring-orange-500" <?= (isset($data['role']) && $data['role'] == 'host' ? 'checked' : '') ?>>
                        <span class="text-sm font-medium text-orange-800">I want to Host</span>
                    </label>
                </div>

                <div>
                    <label for="name" class="sr-only">Full Name</label>
                    <input id="name" name="name" type="text" value="<?= $data['name'] ?? '' ?>" required class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-orange-500 focus:border-orange-500 focus:z-10 sm:text-sm" placeholder="Full Name">
                    <span class="text-red-500 text-xs"><?= $data['name_err'] ?? '' ?></span>
                </div>
                
                <div>
                    <label for="email-address" class="sr-only">Email address</label>
                    <input id="email-address" name="email" type="email" value="<?= $data['email'] ?? '' ?>" required class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-orange-500 focus:border-orange-500 focus:z-10 sm:text-sm" placeholder="Email address">
                    <span class="text-red-500 text-xs"><?= $data['email_err'] ?? '' ?></span>
                </div>
                
                <div>
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" required class="appearance-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-orange-500 focus:border-orange-500 focus:z-10 sm:text-sm" placeholder="Password (Min 6 characters)">
                    <span class="text-red-500 text-xs"><?= $data['password_err'] ?? '' ?></span>
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 shadow-md">
                    Create Account
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../app/views/components/footer.php'; ?>