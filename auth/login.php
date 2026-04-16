<?php
// auth/login.php
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../config/db.php'; // Assuming db.php is here

if (isset($_SESSION['user_id'])) {
    header('Location: /');
    exit;
}

$error = '';
$emailClass = '';
$passClass = '';

// Rate limiting check
$maxAttempts = 5;
$lockoutTime = 15 * 60; // 15 minutes

if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= $maxAttempts) {
    if (time() - $_SESSION['last_attempt_time'] < $lockoutTime) {
        $error = "Too many failed attempts. Please try again after 15 minutes.";
    } else {
        // Reset attempts
        $_SESSION['login_attempts'] = 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email)) {
        $error = "Email is required.";
        $emailClass = 'is-invalid';
    } elseif (empty($password)) {
        $error = "Password is required.";
        $passClass = 'is-invalid';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password_hash'])) {
                // Success
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['login_attempts'] = 0;
                
                if (!empty($_POST['remember'])) {
                    // Set remember me cookie (simplified)
                    setcookie('remember_me', $user['id'], time() + (30 * 24 * 60 * 60), '/'); // 30 days
                }
                
                flash('success', "Welcome back, " . htmlspecialchars($user['name']) . "!");
                
                if ($user['role'] === 'host') {
                    header('Location: /admin/dashboard.php');
                } else {
                    header('Location: /');
                }
                exit;
            } else {
                $error = "Invalid email or password.";
                $emailClass = 'is-invalid';
                $passClass = 'is-invalid';
                
                $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
                $_SESSION['last_attempt_time'] = time();
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
    <title>Login - Wander Local</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .is-invalid { border-color: #dc3545 !important; }
        .invalid-feedback { display: block; color: #dc3545; }
    </style>
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h3 class="text-center fw-bold mb-4">Welcome Back</h3>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if ($msg = getFlash('success')): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
                <?php endif; ?>

                <form action="" method="post">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control <?= $emailClass ?>" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <label class="form-label fw-bold">Password</label>
                            <a href="/auth/forgot_password.php" class="text-decoration-none small">Forgot password?</a>
                        </div>
                        <input type="password" name="password" class="form-control <?= $passClass ?>" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Remember me for 30 days</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill py-2">Log In</button>
                    
                    <div class="text-center mt-3">
                        <button type="button" class="btn btn-outline-dark w-100 rounded-pill py-2" disabled title="Coming Soon">
                            <img src="https://cdn.iconscout.com/icon/free/png-256/google-1772223-1507807.png" width="20" class="me-2"> Continue with Google
                        </button>
                    </div>
                </form>
                
                <p class="text-center mt-4 mb-0">Don't have an account? <a href="/auth/register.php" class="text-decoration-none fw-bold">Sign up</a></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>