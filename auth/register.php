<?php
// auth/register.php
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../config/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: /');
    exit;
}

$error = '';
$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$role = isset($_GET['role']) ? $_GET['role'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? '';
    
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $error = "Email is already registered.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role, created_at) VALUES (?, ?, ?, ?, NOW())");
                $stmt->execute([$name, $email, password_hash($password, PASSWORD_BCRYPT), $role]);
                $user_id = $pdo->lastInsertId();
                
                if ($role === 'host') {
                    $stmt = $pdo->prepare("INSERT INTO host_profiles (user_id) VALUES (?)");
                    $stmt->execute([$user_id]);
                }
                
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_role'] = $role;
                flash('success', 'Registration successful! completing onboarding...');
                
                if ($role === 'host') {
                    header('Location: /onboarding/host.php');
                } else {
                    header('Location: /onboarding/traveler.php');
                }
                exit;
            }
        } catch (PDOException $e) {
            $error = "Database error occurred.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Wander Local</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js" defer></script>
</head>
<body class="bg-light">
<div class="container py-5" x-data="{ step: <?= $step ?>, role: '<?= $role ?>' }">
    <div class="row justify-content-center">
        <!-- Step 1: Role Selection -->
        <div class="col-md-8 text-center" x-show="step === 1">
            <h2 class="fw-bold mb-4">How do you want to use Wander Local?</h2>
            <div class="d-flex justify-content-center gap-4">
                <div class="card p-4 shadow-sm w-50" style="cursor:pointer;" @click="role = 'traveler'; step = 2">
                    <h4 class="fw-bold">I want to explore</h4>
                    <p class="text-muted">Find amazing local experiences.</p>
                </div>
                <div class="card p-4 shadow-sm w-50" style="cursor:pointer;" @click="role = 'host'; step = 2">
                    <h4 class="fw-bold">I want to host</h4>
                    <p class="text-muted">Share your local knowledge.</p>
                </div>
            </div>
            <p class="text-center mt-4">Already have an account? <a href="/auth/login.php" class="text-decoration-none fw-bold">Log in</a></p>
        </div>
        
        <!-- Step 2: Registration Form -->
        <div class="col-md-5" x-show="step === 2" style="display:none;">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <div class="text-end mb-3">
                     <span class="badge bg-primary text-capitalize" x-text="role"></span>
                     <a href="#" class="small text-decoration-none ms-2" @click.prevent="step = 1; role = ''">Change</a>
                </div>
                <h3 class="text-center fw-bold mb-4">Create your account</h3>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <form action="" method="post">
                    <input type="hidden" name="role" x-model="role">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Full Name</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill py-2">Agree & Continue</button>
                    <p class="text-center mt-3 small text-muted">By continuing, you agree to our Terms of Service.</p>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>