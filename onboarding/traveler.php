<?php
// onboarding/traveler.php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'traveler') {
    header('Location: /');
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nationality = $_POST['nationality'] ?? '';
    $bio = $_POST['bio'] ?? '';
    
    // File upload
    $avatar_path = null;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $dir = __DIR__ . '/../uploads/avatars/';
        if (!is_dir($dir)) mkdir($dir, 0777, true);
        $filename = 'traveler_' . $user_id . '_' . time() . '.jpg';
        move_uploaded_file($_FILES['avatar']['tmp_name'], $dir . $filename);
        $avatar_path = '/uploads/avatars/' . $filename;
        $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?")->execute([$avatar_path, $user_id]);
    }
    
    // Save minimal prefs
    // Logic goes here... (truncated for brevity)
    
    header('Location: /');
    exit;
}
?>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js" defer></script>
<style>
.progress-bar-custom { transition: width 0.3s ease; }
</style>
<div class="container py-5" x-data="{ step: 1, bio: '' }">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h2 class="fw-bold mb-4 text-center">Complete Your Profile</h2>
            <div class="progress mb-4" style="height: 10px;">
                <div class="progress-bar progress-bar-custom bg-primary" :style="'width: ' + ((step/3)*100) + '%'"></div>
            </div>

            <form action="" method="POST" enctype="multipart/form-data" class="card p-4 shadow-sm border-0 rounded-4">
                
                <!-- Step 1 -->
                <div x-show="step === 1">
                    <h4 class="mb-4">Step 1: Your Profile</h4>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Profile Photo</label>
                        <input type="file" name="avatar" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nationality</label>
                        <select name="nationality" class="form-select">
                            <option value="">Select country...</option>
                            <option value="US">United States</option>
                            <option value="UK">United Kingdom</option>
                            <option value="IN">India</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Short Bio</label>
                        <textarea name="bio" class="form-control" rows="3" x-model="bio" maxlength="300"></textarea>
                        <div class="small text-muted text-end mt-1"><span x-text="bio.length"></span> / 300 characters</div>
                    </div>
                    <button type="button" class="btn btn-primary rounded-pill px-4" @click="step = 2">Next Step</button>
                </div>

                <!-- Step 2 -->
                <div x-show="step === 2" style="display:none;">
                    <h4 class="mb-4">Step 2: Preferences</h4>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Travel Style</label><br>
                        <label class="me-3"><input type="checkbox" name="styles[]" value="Foodie"> Foodie</label>
                        <label class="me-3"><input type="checkbox" name="styles[]" value="Adventurer"> Adventurer</label>
                        <label class="me-3"><input type="checkbox" name="styles[]" value="Culture"> Culture Seeker</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Budget Range</label><br>
                        <label class="me-3"><input type="radio" name="budget" value="Budget"> Budget &lt;₹500</label>
                        <label class="me-3"><input type="radio" name="budget" value="Mid"> Mid ₹500-2000</label>
                    </div>
                    <button type="button" class="btn btn-secondary rounded-pill px-4" @click="step = 1">Back</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4" @click="step = 3">Next Step</button>
                </div>

                <!-- Step 3 -->
                <div x-show="step === 3" style="display:none;">
                    <h4 class="mb-4">Step 3: Almost Done</h4>
                    <div class="mb-3">
                        <label class="form-label fw-bold">How did you find us?</label>
                        <select name="source" class="form-select">
                            <option>Social Media</option>
                            <option>Friend</option>
                            <option>Search Engine</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-secondary rounded-pill px-4" @click="step = 2">Back</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Complete Setup</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>