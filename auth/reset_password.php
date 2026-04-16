<?php
// auth/reset_password.php
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../config/db.php';

$token = $_GET['token'] ?? '';
$error = '';
$success = '';

if (empty($token)) {
    die("Invalid token.");
}

$stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()");
$stmt->execute([$token]);
$reset = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reset) {
    $error = "Token is invalid or has expired.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($password) || $password !== $confirm_password) {
        $error = "Passwords do not match or are empty.";
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        
        $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?")->execute([$hash, $reset['user_id']]);
        $pdo->prepare("DELETE FROM password_resets WHERE id = ?")->execute([$reset['id']]);
        
        $success = "Password successfully updated. You can now login.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - Wander Local</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h3 class="text-center fw-bold mb-4">Reset Password</h3>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                    <div class="text-center"><a href="/auth/login.php" class="btn btn-primary rounded-pill">Go to Login</a></div>
                <?php elseif (!$error): ?>
                <form action="" method="post">
                    <div class="mb-3">
                        <label class="form-label fw-bold">New Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill py-2">Reset Password</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>