<?php require_once '../app/views/components/header.php'; ?>

<main class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-2xl leading-6 font-bold text-gray-900">
                Create a New Experience
            </h3>
            <div class="mt-2 max-w-xl text-sm text-gray-500">
                <p>Share your local passion with travelers.</p>
            </div>
            
            <form class="mt-5 space-y-6" action="<?= URLROOT ?>/experiences/create" method="POST" enctype="multipart/form-data">
                <div class="grid grid-cols-6 gap-6">
                    
                    <div class="col-span-6 sm:col-span-4">
                        <label for="title" class="block text-sm font-medium text-gray-700">Experience Title</label>
                        <input type="text" name="title" id="title" required value="<?= $data['title'] ?? '' ?>" class="mt-1 focus:ring-orange-500 focus:border-orange-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border p-2">
                        <span class="text-xs text-red-500"><?= $data['title_err'] ?? '' ?></span>
                    </div>

                    <div class="col-span-6 shrink-0 sm:col-span-2">
                        <label for="price" class="block text-sm font-medium text-gray-700">Price (USD)</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">₹</span> 
                            </div>
                            <input type="number" name="price" id="price" required value="<?= $data['price'] ?? '' ?>" class="focus:ring-orange-500 focus:border-orange-500 block w-full pl-7 sm:text-sm border-gray-300 rounded-md border p-2" placeholder="0.00" step="0.01">
                        </div>
                    </div>
                    
                    <div class="col-span-6 sm:col-span-3">
                        <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                        <select id="category" name="category" required class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                            <option value="Food & Drink">Food & Drink</option>
                            <option value="Arts & Craft">Arts & Craft</option>
                            <option value="Nature & Outdoors">Nature & Outdoors</option>
                            <option value="History & Culture">History & Culture</option>
                            <option value="Nightlife">Nightlife</option>
                        </select>
                    </div>

                    <div class="col-span-6 sm:col-span-3">
                        <label for="duration" class="block text-sm font-medium text-gray-700">Duration</label>
                        <input type="text" name="duration" id="duration" placeholder="e.g. 3 hours, Half day" required value="<?= $data['duration'] ?? '' ?>" class="mt-1 focus:ring-orange-500 focus:border-orange-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border p-2">
                    </div>
                    
                    <div class="col-span-6 sm:col-span-6">
                        <label for="location" class="block text-sm font-medium text-gray-700">Meeting Point / Location</label>
                        <input type="text" name="location" id="location" required value="<?= $data['location'] ?? '' ?>" class="mt-1 focus:ring-orange-500 focus:border-orange-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md border p-2">
                    </div>

                    <div class="col-span-6">
                        <label for="description" class="block text-sm font-medium text-gray-700">Detailed Description</label>
                        <div class="mt-1">
                            <textarea id="description" name="description" rows="5" required class="shadow-sm focus:ring-orange-500 focus:border-orange-500 mt-1 block w-full sm:text-sm border border-gray-300 rounded-md p-2"><?= $data['description'] ?? '' ?></textarea>
                        </div>
                        <span class="text-xs text-red-500"><?= $data['description_err'] ?? '' ?></span>
                        <p class="mt-2 text-sm text-gray-500">Brief description of the experience. What will they do, eat, or see?</p>
                    </div>

                    <!-- Simplified Cover Image form -->
                    <div class="col-span-6">
                        <label class="block text-sm font-medium text-gray-700">Cover Image</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600 justify-center">
                                    <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-orange-600 hover:text-orange-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-orange-500">
                                        <span>Upload a file</span>
                                        <input id="file-upload" name="image" type="file" class="sr-only">
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="pt-5 border-t border-gray-200 flex justify-end">
                    <a href="<?= URLROOT ?>/dashboard" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        Cancel
                    </a>
                    <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                        Publish Listing
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once '../app/views/components/footer.php'; ?>