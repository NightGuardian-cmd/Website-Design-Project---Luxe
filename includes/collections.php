<?php
require_once __DIR__ . '/../includes/config.php';
$pageTitle = 'Collections';
$db = getDB();
$categories = $db->query("SELECT c.*, COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON c.id = p.category_id GROUP BY c.id ORDER BY c.name")->fetchAll();
include __DIR__ . '/../includes/header.php';
?>

<div class="page-hero">
    <h1 class="reveal">Collections</h1>
    <p class="reveal">Shop by category — discover your next favourite piece</p>
    <div class="breadcrumb"><a href="<?= SITE_URL ?>">Home</a> / Collections</div>
</div>

<div class="collections-grid">
    <?php foreach ($categories as $c): ?>
    <a href="<?= SITE_URL ?>/pages/shop.php?cat=<?= urlencode($c['slug']) ?>" class="collection-card reveal">
        <img src="<?= sanitize($c['image']) ?>" alt="<?= sanitize($c['name']) ?>" loading="lazy">
        <div class="collection-overlay">
            <h3><?= sanitize($c['name']) ?></h3>
            <span><?= $c['product_count'] ?> Products</span>
        </div>
    </a>
    <?php endforeach; ?>
</div>

<!-- Banner -->
<section class="banner-section" style="height:45vh;">
    <div class="banner-content">
        <h2>New Season</h2>
        <h3>Summer 2026</h3>
        <p>The freshest arrivals for the warmest days. Light fabrics, bold colours, effortless style.</p>
        <a href="<?= SITE_URL ?>/pages/shop.php" class="btn btn-primary">Shop New Arrivals</a>
    </div>
</section>

<script>const SITE_URL = '<?= SITE_URL ?>';</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>