<?php
require_once __DIR__ . '/../includes/config.php';

if (isLoggedIn()) redirect(SITE_URL . '/index.php');

$errors = [];
$values = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values = [
        'first_name' => trim($_POST['first_name'] ?? ''),
        'last_name'  => trim($_POST['last_name']  ?? ''),
        'email'      => trim($_POST['email']       ?? ''),
        'phone'      => trim($_POST['phone']       ?? ''),
        'password'   => $_POST['password']         ?? '',
        'confirm'    => $_POST['confirm_password'] ?? '',
    ];

    if (empty($values['first_name'])) $errors[] = 'First name is required.';
    if (empty($values['last_name']))  $errors[] = 'Last name is required.';
    if (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (strlen($values['password']) < 8) $errors[] = 'Password must be at least 8 characters.';
    if ($values['password'] !== $values['confirm']) $errors[] = 'Passwords do not match.';

    if (empty($errors)) {
        $db = getDB();
        $check = $db->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$values['email']]);
        if ($check->fetch()) {
            $errors[] = 'An account with this email already exists.';
        } else {
            $hash = password_hash($values['password'], PASSWORD_BCRYPT);
            $stmt = $db->prepare("INSERT INTO users (first_name, last_name, email, phone, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$values['first_name'], $values['last_name'], $values['email'], $values['phone'], $hash]);
            $_SESSION['user_id'] = $db->lastInsertId();
            flash('success', 'Welcome to LUXE, ' . $values['first_name'] . '! Your account has been created.');
            redirect(SITE_URL . '/index.php');
        }
    }
}

$pageTitle = 'Create Account';
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
    <div class="auth-visual">
        <div class="auth-visual-content">
            <a href="<?= SITE_URL ?>/index.php" class="logo" style="color:white;font-size:2rem;margin-bottom:auto;">LUXE<span style="color:#c5a059;">.</span></a>
            <div>
                <h2>Join the LUXE community.</h2>
                <p>Members get early access to new collections, exclusive offers, and personalised recommendations.</p>
            </div>
        </div>
    </div>

    <!-- Form Side -->
    <div class="auth-form-side">
        <a href="<?= SITE_URL ?>/index.php" class="logo">LUXE<span>.</span></a>
        <h1>Create Account</h1>
        <p>Already have an account? <a href="<?= SITE_URL ?>/pages/login.php" style="color:var(--accent);">Sign in</a></p>

        <?php if ($errors): ?>
        <div class="flash flash-error" style="border-radius:4px;margin-bottom:24px;">
            <div>
                <strong>Please fix the following:</strong>
                <ul style="margin-top:8px;padding-left:16px;">
                    <?php foreach ($errors as $e): ?>
                        <li><?= sanitize($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <div class="form-row">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" value="<?= sanitize($values['first_name'] ?? '') ?>" placeholder="Jane" required>
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" value="<?= sanitize($values['last_name'] ?? '') ?>" placeholder="Doe" required>
                </div>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" value="<?= sanitize($values['email'] ?? '') ?>" placeholder="jane@example.com" required>
            </div>
            <div class="form-group">
                <label>Phone (optional)</label>
                <input type="tel" name="phone" value="<?= sanitize($values['phone'] ?? '') ?>" placeholder="+1 234 567 8900">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Min. 8 characters" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" placeholder="Repeat password" required>
                </div>
            </div>
            <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:20px;">By creating an account you agree to our <a href="#" style="color:var(--accent);">Terms</a> and <a href="#" style="color:var(--accent);">Privacy Policy</a>.</p>
            <button type="submit" class="btn btn-primary btn-full">Create Account</button>
        </form>
    </div>
</div>
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
<script>const SITE_URL = '<?= SITE_URL ?>';</script>
</body>
</html>