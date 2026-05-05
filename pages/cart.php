<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle = 'Your Bag';

// Cart is session-based: $_SESSION['cart'] = ['product_id' => qty, ...]
$cart = $_SESSION['cart'] ?? [];
$cartItems = [];
$subtotal = 0;

if (!empty($cart)) {
    $db  = getDB();
    $ids = implode(',', array_map('intval', array_keys($cart)));
    $products = $db->query("SELECT * FROM products WHERE id IN ($ids)")->fetchAll();
    foreach ($products as $p) {
        $qty = $cart[$p['id']] ?? 0;
        $price = $p['sale_price'] ?? $p['price'];
        $cartItems[] = array_merge($p, ['qty' => $qty, 'line_total' => $qty * $price, 'effective_price' => $price]);
        $subtotal += $qty * $price;
    }
}

$shipping = $subtotal >= 100 ? 0 : 9.99;
$total    = $subtotal + $shipping;

include __DIR__ . '/../includes/header.php';
?>

<div class="page-hero">
    <h1>Your Bag</h1>
    <div class="breadcrumb"><a href="<?= SITE_URL ?>">Home</a> / Cart</div>
</div>

<?php if (empty($cartItems)): ?>
<div style="text-align:center;padding:100px 5%;">
    <i class="fa-solid fa-bag-shopping" style="font-size:4rem;color:var(--border);"></i>
    <h2 style="margin:24px 0 12px;font-size:2rem;">Your bag is empty</h2>
    <p style="color:var(--text-muted);margin-bottom:32px;">Looks like you haven't added anything yet.</p>
    <a href="<?= SITE_URL ?>/pages/shop.php" class="btn btn-primary">Continue Shopping</a>
</div>
<?php else: ?>
<div class="cart-layout">
    <!-- Items -->
    <div>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $item): ?>
                <tr data-product="<?= $item['id'] ?>">
                    <td>
                        <div class="cart-product">
                            <img src="<?= sanitize($item['image']) ?>" alt="<?= sanitize($item['name']) ?>">
                            <div>
                                <h4><?= sanitize($item['name']) ?></h4>
                                <small style="color:var(--text-muted);">In stock</small>
                            </div>
                        </div>
                    </td>
                    <td>$<?= number_format($item['effective_price'], 2) ?></td>
                    <td>
                        <div class="qty-control">
                            <button class="qty-decrease" <?= $item['qty'] <= 1 ? 'disabled' : '' ?>>−</button>
                            <span><?= $item['qty'] ?></span>
                            <button class="qty-increase">+</button>
                        </div>
                    </td>
                    <td><strong>$<?= number_format($item['line_total'], 2) ?></strong></td>
                    <td>
                        <button class="cart-remove remove-from-cart" data-id="<?= $item['id'] ?>">
                            <i class="fa-solid fa-xmark"></i> Remove
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div style="margin-top:24px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px;">
            <a href="<?= SITE_URL ?>/pages/shop.php" class="btn btn-secondary btn-sm">← Continue Shopping</a>
        </div>
    </div>

    <!-- Summary -->
    <div class="order-summary">
        <h3>Order Summary</h3>
        <div class="summary-row"><span>Subtotal</span><span>$<?= number_format($subtotal, 2) ?></span></div>
        <div class="summary-row">
            <span>Shipping</span>
            <span><?= $shipping == 0 ? '<span style="color:var(--success)">Free</span>' : '$' . number_format($shipping, 2) ?></span>
        </div>
        <?php if ($shipping > 0): ?>
        <p style="font-size:0.8rem;color:var(--text-muted);margin-bottom:16px;">Add $<?= number_format(100 - $subtotal, 2) ?> more for free shipping!</p>
        <?php endif; ?>
        <div class="summary-row total"><span>Total</span><span>$<?= number_format($total, 2) ?></span></div>

        <?php if (isLoggedIn()): ?>
            <a href="<?= SITE_URL ?>/pages/checkout.php" class="btn btn-primary btn-full" style="margin-top:12px;">Proceed to Checkout</a>
        <?php else: ?>
            <a href="<?= SITE_URL ?>/pages/login.php?redirect=<?= urlencode(SITE_URL . '/pages/checkout.php') ?>" class="btn btn-primary btn-full" style="margin-top:12px;">Sign In to Checkout</a>
            <a href="<?= SITE_URL ?>/pages/checkout.php" class="btn btn-secondary btn-full" style="margin-top:10px;">Guest Checkout</a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<script>const SITE_URL = '<?= SITE_URL ?>';</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>