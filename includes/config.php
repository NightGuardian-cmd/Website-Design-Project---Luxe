<?php
// =============================================
//  LUXE — Configuration & DB Connection
// =============================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // ← change to your MySQL username
define('DB_PASS', '');            // ← change to your MySQL password
define('DB_NAME', 'luxe_db');

define('SITE_URL', 'http://localhost/luxe');  // ← change to your local URL
define('SITE_NAME', 'LUXE Premium');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection (PDO)
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            die('<div style="font-family:sans-serif;padding:40px;color:red;">
                <h2>Database Connection Error</h2>
                <p>' . htmlspecialchars($e->getMessage()) . '</p>
                <p>Please check your <strong>includes/config.php</strong> credentials.</p>
            </div>');
        }
    }
    return $pdo;
}

// ---- Helper Functions ----

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function currentUser(): ?array {
    if (!isLoggedIn()) return null;
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ' . SITE_URL . '/pages/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

function redirect(string $url): void {
    header("Location: $url");
    exit;
}

function sanitize(string $str): string {
    return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
}

function flash(string $key, string $msg = ''): string {
    if ($msg) {
        $_SESSION['flash'][$key] = $msg;
        return '';
    }
    $out = $_SESSION['flash'][$key] ?? '';
    unset($_SESSION['flash'][$key]);
    return $out;
}

function cartCount(): int {
    return array_sum(array_column($_SESSION['cart'] ?? [], 'qty'));
}

function wishlistCount(): int {
    if (!isLoggedIn()) return 0;
    $db = getDB();
    $stmt = $db->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return (int)$stmt->fetchColumn();
}