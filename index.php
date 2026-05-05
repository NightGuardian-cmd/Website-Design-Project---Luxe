<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Home';
$db = getDB();
$featuredProducts = $db->query("SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.featured=1 LIMIT 6")->fetchAll();
include __DIR__ . '/includes/header.php';
?>

<div class="loader"><div class="loader-ring"></div></div>

<!-- Hero -->
<section class="hero">
    <div class="hero-content">
        <h2 class="reveal">Elegance in Every Detail</h2>
        <h1 class="reveal">New Summer<br>Collection 2026</h1>
        <p class="reveal">Discover the perfect blend of style and comfort with our latest arrivals.</p>
        <div class="hero-btns reveal">
            <a href="<?= SITE_URL ?>/pages/shop.php" class="btn btn-primary">Shop Now</a>
            <a href="<?= SITE_URL ?>/pages/collections.php" class="btn btn-secondary">View Collections</a>
        </div>
    </div>
    <div class="hero-image reveal">
        <div class="image-wrapper">
            <img src="https://images.unsplash.com/photo-1483985988355-763728e1935b?w=1000&q=80" alt="Fashion Model" loading="lazy">
        </div>
        <div class="floating-card card-1">
            <i class="fa-solid fa-star"></i><span>Premium Quality</span>
        </div>
        <div class="floating-card card-2">
            <i class="fa-solid fa-truck-fast"></i><span>Fast Delivery</span>
        </div>
    </div>
</section>

<!-- Stats -->
<div class="stats-strip">
    <div class="stat-item"><div class="stat-num">12K+</div><div class="stat-label">Happy Clients</div></div>
    <div class="stat-item"><div class="stat-num">500+</div><div class="stat-label">Products</div></div>
    <div class="stat-item"><div class="stat-num">50+</div><div class="stat-label">Designers</div></div>
    <div class="stat-item"><div class="stat-num">99%</div><div class="stat-label">Satisfaction</div></div>
</div>

<!-- Featured Products -->
<section class="products-section">
    <div class="section-header">
        <span class="subtitle">Our Selection</span>
        <h2 class="reveal">Featured Products</h2>
    </div>
    <div class="product-grid">
        <?php foreach ($featuredProducts as $p): ?>
        <div class="product-card reveal">
            <div class="product-img">
                <img src="<?= sanitize($p['image']) ?>" alt="<?= sanitize($p['name']) ?>" loading="lazy">
                <?php if ($p['sale_price']): ?>
                    <span class="product-badge badge-sale">Sale</span>
                <?php endif; ?>
                <div class="product-actions">
                    <button class="action-btn add-to-cart" data-id="<?= $p['id'] ?>" title="Add to Bag">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </button>
                    <button class="action-btn wishlist-toggle" data-id="<?= $p['id'] ?>" title="Wishlist">
                        <i class="fa-regular fa-heart"></i>
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
    <div style="text-align:center;margin-top:50px;">
        <a href="<?= SITE_URL ?>/pages/shop.php" class="btn btn-secondary">View All Products</a>
    </div>
</section>

<!-- Banner CTA -->
<section class="banner-section">
    <div class="banner-content">
        <h2>Limited Edition</h2>
        <h3>Up to 50% Off</h3>
        <p>Exclusive designs for the modern individual. Don't miss out on our seasonal sale.</p>
        <a href="<?= SITE_URL ?>/pages/shop.php?sale=1" class="btn btn-primary">Explore Sale</a>
    </div>
</section>

<script>const SITE_URL = '<?= SITE_URL ?>';</script>
<?php include __DIR__ . '/includes/footer.php'; ?>