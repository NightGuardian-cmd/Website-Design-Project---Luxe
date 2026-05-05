<?php
require_once __DIR__ . '/includes/config.php';
requireLogin();
$pageTitle = 'My Wishlist';

$db = getDB();
$wishlistItems = $db->prepare("
    SELECT p.*, c.name as cat_name
    FROM wishlist w
    JOIN products p ON w.product_id = p.id
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE w.user_id = ?
    ORDER BY w.added_at DESC
");
$wishlistItems->execute([$_SESSION['user_id']]);
$items = $wishlistItems->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
    <h1>My Wishlist</h1>
    <div class="breadcrumb"><a href="<?= SITE_URL ?>">Home</a> / Wishlist</div>
</div>

<?php if (empty($items)): ?>
<div class="wishlist-empty">
    <i class="fa-regular fa-heart"></i>
    <h2>Your wishlist is empty</h2>
    <p>Save items you love to come back to them later.</p>
    <a href="<?= SITE_URL ?>/pages/shop.php" class="btn btn-primary" style="margin-top:28px;">Explore Products</a>
</div>
<?php else: ?>
<section class="products-section">
    <div class="section-header">
        <span class="subtitle"><?= count($items) ?> saved items</span>
        <h2>Your Favourites</h2>
    </div>
    <div class="product-grid">
        <?php foreach ($items as $p): ?>
        <div class="product-card reveal">
            <div class="product-img">
                <img src="<?= sanitize($p['image']) ?>" alt="<?= sanitize($p['name']) ?>" loading="lazy">
                <div class="product-actions">
                    <button class="action-btn add-to-cart" data-id="<?= $p['id'] ?>" title="Add to Bag">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </button>
                    <button class="action-btn wishlist-toggle wishlist-active" data-id="<?= $p['id'] ?>" title="Remove from Wishlist">
                        <i class="fa-solid fa-heart"></i>
                    </button>
                    <a href="<?= SITE_URL ?>/pages/product.php?slug=<?= urlencode($p['slug']) ?>" class="action-btn" title="View">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </a>
                </div>
            </div>
            <div class="product-info">
                <p class="product-category"><?= sanitize($p['cat_name'] ?? '') ?></p>
                <h3 class="product-title"><?= sanitize($p['name']) ?></h3>
                <div class="product-price-row">
                    <?php if ($p['sale_price']): ?>
                        <span class="product-price">$<?= number_format($p['sale_price'], 2) ?></span>
                        <span class="product-old-price">$<?= number_format($p['price'], 2) ?></span>
                    <?php else: ?>
                        <span class="product-price">$<?= number_format($p['price'], 2) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<script>const SITE_URL = '<?= SITE_URL ?>';</script>
<?php include __DIR__ . '/includes/footer.php'; ?>