<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();
$pageTitle = 'My Account';

$db   = getDB();
$user = currentUser();

// Orders
$orders = $db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 20");
$orders->execute([$user['id']]);
$orderRows = $orders->fetchAll();

// Profile update
$updateMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $fn    = trim($_POST['first_name'] ?? '');
    $ln    = trim($_POST['last_name']  ?? '');
    $phone = trim($_POST['phone']      ?? '');
    if ($fn && $ln) {
        $db->prepare("UPDATE users SET first_name=?, last_name=?, phone=? WHERE id=?")
           ->execute([$fn, $ln, $phone, $user['id']]);
        flash('success', 'Profile updated!');
        redirect(SITE_URL . '/pages/account.php');
    }
}

$tab = $_GET['tab'] ?? 'overview';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-hero">
    <h1>My Account</h1>
    <div class="breadcrumb"><a href="<?= SITE_URL ?>">Home</a> / Account</div>
</div>

<div class="account-layout">
    <!-- Sidebar -->
    <aside class="account-sidebar">
        <div style="text-align:center;margin-bottom:24px;">
            <div style="width:70px;height:70px;background:var(--accent);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;color:white;font-size:1.8rem;font-family:var(--font-serif);">
                <?= strtoupper(substr($user['first_name'], 0, 1)) ?>
            </div>
            <strong><?= sanitize($user['first_name'] . ' ' . $user['last_name']) ?></strong><br>
            <small style="color:var(--text-muted);"><?= sanitize($user['email']) ?></small>
        </div>
        <nav class="account-nav">
            <a href="?tab=overview"  class="<?= $tab==='overview' ?'active':'' ?>"><i class="fa-solid fa-house"></i> Overview</a>
            <a href="?tab=orders"    class="<?= $tab==='orders'   ?'active':'' ?>"><i class="fa-solid fa-box"></i> Orders</a>
            <a href="?tab=wishlist"  class="<?= $tab==='wishlist' ?'active':'' ?>"><i class="fa-regular fa-heart"></i> Wishlist</a>
            <a href="?tab=profile"   class="<?= $tab==='profile'  ?'active':'' ?>"><i class="fa-regular fa-user"></i> Edit Profile</a>
            <a href="<?= SITE_URL ?>/pages/logout.php" style="color:var(--error)!important;"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        </nav>
    </aside>

    <!-- Main -->
    <div class="account-main">

        <?php if ($tab === 'overview'): ?>
            <h2>Welcome back, <?= sanitize($user['first_name']) ?>!</h2>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:40px;">
                <div style="background:var(--bg-alt);padding:24px;border-radius:4px;text-align:center;border:1px solid var(--border);">
                    <div style="font-family:var(--font-serif);font-size:2.5rem;color:var(--accent);"><?= count($orderRows) ?></div>
                    <div style="font-size:0.8rem;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);">Total Orders</div>
                </div>
                <div style="background:var(--bg-alt);padding:24px;border-radius:4px;text-align:center;border:1px solid var(--border);">
                    <div style="font-family:var(--font-serif);font-size:2.5rem;color:var(--accent);"><?= wishlistCount() ?></div>
                    <div style="font-size:0.8rem;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);">Wishlist Items</div>
                </div>
                <div style="background:var(--bg-alt);padding:24px;border-radius:4px;text-align:center;border:1px solid var(--border);">
                    <div style="font-family:var(--font-serif);font-size:2.5rem;color:var(--accent);"><?= date('Y', strtotime($user['created_at'])) ?></div>
                    <div style="font-size:0.8rem;text-transform:uppercase;letter-spacing:1px;color:var(--text-muted);">Member Since</div>
                </div>
            </div>

            <h3 style="margin-bottom:20px;">Recent Orders</h3>
            <?php if (empty($orderRows)): ?>
                <p style="color:var(--text-muted);">No orders yet. <a href="<?= SITE_URL ?>/pages/shop.php" style="color:var(--accent);">Start shopping →</a></p>
            <?php else: ?>
                <table class="orders-table">
                    <thead><tr><th>Order</th><th>Date</th><th>Total</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php foreach (array_slice($orderRows, 0, 3) as $o): ?>
                        <tr>
                            <td><?= sanitize($o['order_number']) ?></td>
                            <td><?= date('M d, Y', strtotime($o['created_at'])) ?></td>
                            <td>$<?= number_format($o['total_amount'], 2) ?></td>
                            <td><span class="status-badge status-<?= $o['status'] ?>"><?= $o['status'] ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        <?php elseif ($tab === 'orders'): ?>
            <h2>Order History</h2>
            <?php if (empty($orderRows)): ?>
                <p style="color:var(--text-muted);">No orders yet.</p>
            <?php else: ?>
                <table class="orders-table">
                    <thead><tr><th>Order #</th><th>Date</th><th>Total</th><th>Payment</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php foreach ($orderRows as $o): ?>
                        <tr>
                            <td><?= sanitize($o['order_number']) ?></td>
                            <td><?= date('M d, Y', strtotime($o['created_at'])) ?></td>
                            <td>$<?= number_format($o['total_amount'], 2) ?></td>
                            <td><?= sanitize($o['payment_method']) ?></td>
                            <td><span class="status-badge status-<?= $o['status'] ?>"><?= $o['status'] ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        <?php elseif ($tab === 'wishlist'): ?>
            <?php redirect(SITE_URL . '/pages/wishlist.php'); ?>

        <?php elseif ($tab === 'profile'): ?>
            <h2>Edit Profile</h2>
            <form method="POST" style="max-width:500px;">
                <input type="hidden" name="update_profile" value="1">
                <div class="form-row">
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" name="first_name" value="<?= sanitize($user['first_name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" name="last_name" value="<?= sanitize($user['last_name']) ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" value="<?= sanitize($user['email']) ?>" disabled style="background:var(--bg-alt);cursor:not-allowed;">
                    <p class="form-hint">Email cannot be changed for security reasons.</p>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="phone" value="<?= sanitize($user['phone'] ?? '') ?>">
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>const SITE_URL = '<?= SITE_URL ?>';</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>