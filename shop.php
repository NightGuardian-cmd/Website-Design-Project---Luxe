<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Shop';
$db = getDB();

// Filters
$cat      = $_GET['cat']    ?? '';
$sale     = $_GET['sale']   ?? '';
$sort     = $_GET['sort']   ?? 'newest';
$search   = trim($_GET['q'] ?? '');

$where  = ['1=1'];
$params = [];

if ($cat) {
    $where[]  = 'c.slug = ?';
    $params[] = $cat;
}
if ($sale) {
    $where[] = 'p.sale_price IS NOT NULL';
}
if ($search) {
    $where[]  = '(p.name LIKE ? OR p.description LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sortSQL = match($sort) {
    'price_asc'  => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    'popular'    => 'p.review_count DESC',
    default      => 'p.created_at DESC',
};

$sql = "SELECT p.*, c.name as cat_name, c.slug as cat_slug
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE " . implode(' AND ', $where) . "
        ORDER BY $sortSQL";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
    <h1 class="reveal">Shop All</h1>
    <p class="reveal">Curated pieces for the modern wardrobe</p>
    <div class="breadcrumb"><a href="<?= SITE_URL ?>">Home</a> / Shop</div>
</div>

<div class="shop-layout">
    <!-- Filters Sidebar -->
    <aside class="filter-sidebar">
        <h3>Filters</h3>
        <form method="GET" id="filter-form">
            <?php if ($search): ?><input type="hidden" name="q" value="<?= sanitize($search) ?>"><?php endif; ?>

            <div class="filter-group">
                <h4>Category</h4>
                <label class="filter-option">
                    <input type="radio" name="cat" value="" <?= !$cat ? 'checked' : '' ?>> All Categories
                </label>
                <?php foreach ($categories as $c): ?>
                <label class="filter-option">
                    <input type="radio" name="cat" value="<?= $c['slug'] ?>" <?= $cat === $c['slug'] ? 'checked' : '' ?>>
                    <?= sanitize($c['name']) ?>
                </label>
                <?php endforeach; ?>
            </div>

            <div class="filter-group">
                <h4>Deals</h4>
                <label class="filter-option">
                    <input type="checkbox" name="sale" value="1" <?= $sale ? 'checked' : '' ?>> On Sale
                </label>
            </div>

            <div class="filter-group">
                <h4>Sort By</h4>
                <select name="sort" class="sort-select" style="width:100%;" onchange="this.form.submit()">
                    <option value="newest"     <?= $sort==='newest'     ?'selected':'' ?>>Newest First</option>
                    <option value="popular"    <?= $sort==='popular'    ?'selected':'' ?>>Most Popular</option>
                    <option value="price_asc"  <?= $sort==='price_asc'  ?'selected':'' ?>>Price: Low → High</option>
                    <option value="price_desc" <?= $sort==='price_desc' ?'selected':'' ?>>Price: High → Low</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-full btn-sm">Apply Filters</button>
            <a href="<?= SITE_URL ?>/pages/shop.php" class="btn btn-secondary btn-full btn-sm" style="margin-top:10px;">Clear All</a>
        </form>
    </aside>

    <!-- Product Results -->
    <div>
        <div class="shop-toolbar">
            <span><?= count($products) ?> product<?= count($products) !== 1 ? 's' : '' ?> found
                <?= $cat ? 'in <strong>' . sanitize(ucfirst($cat)) . '</strong>' : '' ?>
                <?= $search ? 'for <strong>"' . sanitize($search) . '"</strong>' : '' ?>
            </span>
            <form method="GET" style="display:flex;gap:8px;">
                <?php if ($cat):  ?><input type="hidden" name="cat"  value="<?= sanitize($cat) ?>"><?php endif; ?>
                <?php if ($sale): ?><input type="hidden" name="sale" value="1"><?php endif; ?>
                <input type="text" name="q" value="<?= sanitize($search) ?>" placeholder="Search products..." style="padding:8px 14px;border:1px solid var(--border);border-radius:4px;font-size:0.85rem;outline:none;">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>

        <?php if (empty($products)): ?>
            <div style="text-align:center;padding:80px 0;">
                <i class="fa-solid fa-box-open" style="font-size:3rem;color:var(--border);"></i>
                <h3 style="margin-top:20px;">No products found</h3>
                <p style="color:var(--text-muted);margin-top:8px;">Try a different search or remove filters.</p>
            </div>
        <?php else: ?>
        <div class="product-grid">
            <?php foreach ($products as $p): ?>
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
        <?php endif; ?>
    </div>
</div>

<script>const SITE_URL = '<?= SITE_URL ?>';</script>
<?php include __DIR__ . '/includes/footer.php'; ?>