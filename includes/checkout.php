<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle = 'Checkout';

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) redirect(SITE_URL . '/pages/cart.php');

$db = getDB();
$ids = implode(',', array_map('intval', array_keys($cart)));
$products = $db->query("SELECT * FROM products WHERE id IN ($ids)")->fetchAll();

$cartItems = [];
$subtotal  = 0;
foreach ($products as $p) {
    $qty   = $cart[$p['id']] ?? 0;
    $price = $p['sale_price'] ?? $p['price'];
    $cartItems[] = array_merge($p, ['qty' => $qty, 'line_total' => $qty * $price, 'effective_price' => $price]);
    $subtotal += $qty * $price;
}

$shipping = $subtotal >= 100 ? 0 : 9.99;
$total    = $subtotal + $shipping;

$user = currentUser();

// Handle submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['full_name'] ?? '');
    $email   = trim($_POST['email']     ?? '');
    $address = trim($_POST['address']   ?? '');
    $city    = trim($_POST['city']      ?? '');
    $zip     = trim($_POST['zip']       ?? '');
    $country = trim($_POST['country']   ?? '');

    if ($name && $email && $address) {
        $orderNum   = 'LX-' . strtoupper(substr(md5(uniqid()), 0, 8));
        $fullAddr   = "$address, $city $zip, $country";
        $userId     = $user['id'] ?? null;

        if (!$userId) {
            // Guest: create a temporary user record or skip
            $userId = 0;
        }

        if ($userId) {
            $stmt = $db->prepare("INSERT INTO orders (user_id, order_number, total_amount, shipping_address, payment_method) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$userId, $orderNum, $total, $fullAddr, 'card']);
            $orderId = $db->lastInsertId();

            foreach ($cartItems as $item) {
                $db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)")
                   ->execute([$orderId, $item['id'], $item['qty'], $item['effective_price']]);
            }
        }

        // Clear cart
        unset($_SESSION['cart']);
        flash('success', "Order $orderNum placed successfully! Thank you, $name.");
        redirect(SITE_URL . '/index.php');
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="page-hero">
    <h1>Checkout</h1>
    <div class="breadcrumb"><a href="<?= SITE_URL ?>">Home</a> / <a href="<?= SITE_URL ?>/pages/cart.php">Cart</a> / Checkout</div>
</div>

<div style="display:grid;grid-template-columns:1fr 380px;gap:40px;padding:60px 5%;max-width:1300px;margin:0 auto;">
    <!-- Form -->
    <div>
        <h2 style="font-size:1.8rem;margin-bottom:28px;">Shipping Details</h2>
        <form method="POST" novalidate>
            <div class="form-row">
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" value="<?= $user ? sanitize($user['first_name'].' '.$user['last_name']) : '' ?>" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" value="<?= $user ? sanitize($user['email']) : '' ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label>Street Address</label>
                <input type="text" name="address" placeholder="123 Fashion Street" required>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>City</label>
                    <input type="text" name="city" placeholder="Paris" required>
                </div>
                <div class="form-group">
                    <label>ZIP / Postcode</label>
                    <input type="text" name="zip" placeholder="75001" required>
                </div>
            </div>
            <div class="form-group">
                <label>Country</label>
                <select name="country">
                    <option>France</option><option>United Kingdom</option><option>Germany</option>
                    <option>United States</option><option>Egypt</option><option>UAE</option>
                    <option>Other</option>
                </select>
            </div>

            <h2 style="font-size:1.8rem;margin:32px 0 20px;">Payment</h2>
            <div style="border:1.5px solid var(--border);border-radius:4px;padding:24px;background:var(--bg-alt);margin-bottom:24px;">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
                    <i class="fa-solid fa-lock" style="color:var(--accent);"></i>
                    <span style="font-size:0.85rem;color:var(--text-muted);">This is a demo — no real payment is processed.</span>
                </div>
                <div class="form-row">
                    <div class="form-group" style="grid-column:1/-1;">
                        <label>Card Number</label>
                        <input type="text" placeholder="4242 4242 4242 4242" maxlength="19">
                    </div>
                    <div class="form-group">
                        <label>Expiry</label>
                        <input type="text" placeholder="MM / YY" maxlength="7">
                    </div>
                    <div class="form-group">
                        <label>CVV</label>
                        <input type="text" placeholder="123" maxlength="4">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-full">
                <i class="fa-solid fa-lock"></i> Place Order — $<?= number_format($total, 2) ?>
            </button>
        </form>
    </div>

    <!-- Summary -->
    <div class="order-summary">
        <h3>Order Summary</h3>
        <?php foreach ($cartItems as $item): ?>
        <div style="display:flex;gap:14px;margin-bottom:18px;align-items:center;">
            <img src="<?= sanitize($item['image']) ?>" style="width:60px;height:75px;object-fit:cover;" alt="<?= sanitize($item['name']) ?>">
            <div style="flex:1;">
                <div style="font-size:0.9rem;font-weight:600;"><?= sanitize($item['name']) ?></div>
                <div style="font-size:0.8rem;color:var(--text-muted);">Qty: <?= $item['qty'] ?></div>
            </div>
            <div style="font-weight:600;font-size:0.9rem;">$<?= number_format($item['line_total'], 2) ?></div>
        </div>
        <?php endforeach; ?>
        <div style="border-top:1px solid var(--border);padding-top:16px;margin-top:8px;">
            <div class="summary-row"><span>Subtotal</span><span>$<?= number_format($subtotal, 2) ?></span></div>
            <div class="summary-row"><span>Shipping</span><span><?= $shipping == 0 ? 'Free' : '$'.number_format($shipping, 2) ?></span></div>
            <div class="summary-row total"><span>Total</span><span>$<?= number_format($total, 2) ?></span></div>
        </div>
    </div>
</div>

<script>const SITE_URL = '<?= SITE_URL ?>';</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>