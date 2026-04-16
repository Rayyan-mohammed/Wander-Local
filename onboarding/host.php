<?php
// onboarding/host.php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'host') {
    header('Location: /');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle submission and insert drafted experience
    // Redirect to confirmation logic
}
?>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js" defer></script>
<style>
.checkmark-circle {
    stroke-dasharray: 166; stroke-dashoffset: 166; stroke-width: 2; stroke-miterlimit: 10; stroke: #4BB543; fill: none; animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
}
.checkmark { width: 56px; height: 56px; border-radius: 50%; display: block; stroke-width: 2; stroke: #fff; stroke-miterlimit: 10; margin: 10% auto; box-shadow: inset 0px 0px 0px #4BB543; animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both; }
.checkmark__check { transform-origin: 50% 50%; stroke-dasharray: 48; stroke-dashoffset: 48; animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards; }
@keyframes stroke { 100% { stroke-dashoffset: 0; } }
@keyframes scale { 0%, 100% { transform: none; } 50% { transform: scale3d(1.1, 1.1, 1); } }
@keyframes fill { 100% { box-shadow: inset 0px 0px 0px 30px #4BB543; } }
</style>
<div class="container py-5" x-data="{ step: 1, bio: '', desc: '', submitted: false }">
    <div class="row justify-content-center">
        <div class="col-md-9" x-show="!submitted">
            <h2 class="fw-bold mb-4 text-center">Host Onboarding</h2>
            <div class="progress mb-4" style="height: 10px;">
                <div class="progress-bar bg-primary" :style="'width: ' + ((step/5)*100) + '%'"></div>
            </div>

            <form @submit.prevent="submitted = true" class="card p-4 shadow-sm border-0 rounded-4">
                <!-- Step 1 -->
                <div x-show="step === 1">
                    <h4 class="mb-4">Step 1: Personal Details</h4>
                    <input type="file" class="form-control mb-3" accept="image/*">
                    <input type="text" class="form-control mb-3" placeholder="Tagline (e.g. Street food guide in Old Delhi)">
                    <textarea class="form-control mb-3" rows="3" placeholder="Bio..." x-model="bio"></textarea>
                    <div class="text-end small"><span x-text="bio.length"></span> min 100</div>
                    <button type="button" class="btn btn-primary rounded-pill" @click="step = 2" :disabled="bio.length < 100">Next</button>
                </div>
                <!-- Step 2 -->
                <div x-show="step === 2" style="display:none;">
                    <h4 class="mb-4">Step 2: Your Location</h4>
                    <input type="text" class="form-control mb-3" placeholder="City">
                    <input type="text" class="form-control mb-3" placeholder="Neighborhood">
                    <button type="button" class="btn btn-secondary rounded-pill" @click="step = 1">Back</button>
                    <button type="button" class="btn btn-primary rounded-pill" @click="step = 3">Next</button>
                </div>
                <!-- Step 3 -->
                <div x-show="step === 3" style="display:none;">
                    <h4 class="mb-4">Step 3: Languages & Style</h4>
                    <select class="form-select mb-3"><option>Friendly</option><option>Formal</option></select>
                    <button type="button" class="btn btn-secondary rounded-pill" @click="step = 2">Back</button>
                    <button type="button" class="btn btn-primary rounded-pill" @click="step = 4">Next</button>
                </div>
                <!-- Step 4 -->
                <div x-show="step === 4" style="display:none;">
                    <h4 class="mb-4">Step 4: First Listing Draft</h4>
                    <input type="text" class="form-control mb-3" placeholder="Title">
                    <textarea class="form-control mb-3" placeholder="Description" x-model="desc"></textarea>
                    <div class="text-end small"><span x-text="desc.length"></span> min 200</div>
                    <button type="button" class="btn btn-secondary rounded-pill" @click="step = 3">Back</button>
                    <button type="button" class="btn btn-primary rounded-pill" @click="step = 5" :disabled="desc.length < 200">Next</button>
                </div>
                <!-- Step 5 -->
                <div x-show="step === 5" style="display:none;">
                    <h4 class="mb-4">Step 5: Verification</h4>
                    <input type="file" class="form-control mb-3" accept="image/*,application/pdf">
                    <label class="mb-3"><input type="checkbox" required> I accept the Host Agreement</label>
                    <br>
                    <button type="button" class="btn btn-secondary rounded-pill" @click="step = 4">Back</button>
                    <button type="submit" class="btn btn-success rounded-pill">Submit for Review</button>
                </div>
            </form>
        </div>

        <!-- Confirmation -->
        <div class="col-md-6 text-center pt-5" x-show="submitted" style="display:none;">
            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
              <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
              <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
            </svg>
            <h2 class="fw-bold mb-3">Your profile is under review</h2>
            <p class="text-muted mb-4">You can still set up your experience while we verify you.</p>
            <a href="/admin/dashboard.php" class="btn btn-primary rounded-pill px-5">Go to Dashboard</a>
        </div>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>