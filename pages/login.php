<?php
require_once __DIR__ . '/../includes/config.php';

if (isLoggedIn()) redirect(SITE_URL . '/index.php');

$error  = '';
$email  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    $redirect = $_POST['redirect'] ?? SITE_URL . '/index.php';

    if (empty($email) || empty($password)) {
        $error = 'Please enter your email and password.';
    } else {
        $db   = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            flash('success', 'Welcome back, ' . $user['first_name'] . '!');
            redirect(filter_var($redirect, FILTER_VALIDATE_URL) ? $redirect : SITE_URL . '/index.php');
        } else {
            $error = 'Invalid email or password. Please try again.';
        }
    }
}

$redirect = $_GET['redirect'] ?? SITE_URL . '/index.php';
$pageTitle = 'Sign In';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> — LUXE</title>
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>
<div class="auth-page">
    <!-- Visual Side -->
    <div class="auth-visual" style="background-image:url('https://images.unsplash.com/photo-1469334031218-e382a71b716b?w=1200&q=80');background-size:cover;background-position:center;">
        <div class="auth-visual-content">
            <a href="<?= SITE_URL ?>/index.php" class="logo" style="color:white;font-size:2rem;margin-bottom:auto;">LUXE<span style="color:#c5a059;">.</span></a>
            <div>
                <h2>Welcome back.</h2>
                <p>Sign in to access your orders, wishlist, and exclusive member benefits.</p>
            </div>
        </div>
    </div>

    <!-- Form Side -->
    <div class="auth-form-side">
        <a href="<?= SITE_URL ?>/index.php" class="logo">LUXE<span>.</span></a>
        <h1>Sign In</h1>
        <p>Don't have an account? <a href="<?= SITE_URL ?>/pages/register.php" style="color:var(--accent);">Create one</a></p>

        <?php if ($error): ?>
        <div class="flash flash-error" style="border-radius:4px;margin-bottom:24px;"><?= sanitize($error) ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <input type="hidden" name="redirect" value="<?= sanitize($redirect) ?>">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="<?= sanitize($email) ?>" placeholder="you@example.com" required autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Your password" required>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;font-size:0.85rem;">
                <label style="display:flex;align-items:center;gap:8px;text-transform:none;letter-spacing:0;font-weight:400;">
                    <input type="checkbox" name="remember"> Remember me
                </label>
                <a href="#" style="color:var(--accent);">Forgot password?</a>
            </div>
            <button type="submit" class="btn btn-primary btn-full">Sign In</button>
        </form>

        <div class="form-divider"><span>or continue as</span></div>
        <a href="<?= SITE_URL ?>/index.php" class="btn btn-secondary btn-full">Guest — Browse without account</a>

        <p style="font-size:0.78rem;color:var(--text-muted);margin-top:32px;text-align:center;">
            <strong>Demo admin:</strong> admin@luxe.com / password
        </p>
    </div>
</div>
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
<script>const SITE_URL = '<?= SITE_URL ?>';</script>
</body>
</html>