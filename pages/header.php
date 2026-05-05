<?php
// includes/header.php
// Requires $pageTitle to be set by the including page
$user = currentUser();
$cartQty  = cartCount();
$wishQty  = wishlistCount();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitize($pageTitle ?? 'LUXE Premium') ?> — LUXE</title>
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<!-- Live Clock Bar -->
<div class="top-bar">
    <span><i class="fa-solid fa-truck-fast"></i> Free shipping on orders over $100</span>
    <div id="live-clock" class="live-clock"></div>
    <span>
        <?php if ($user): ?>
            Welcome, <strong><?= sanitize($user['first_name']) ?></strong> &nbsp;|&nbsp;
            <a href="<?= SITE_URL ?>/pages/account.php">My Account</a> &nbsp;|&nbsp;
            <a href="<?= SITE_URL ?>/pages/logout.php">Logout</a>
        <?php else: ?>
            <a href="<?= SITE_URL ?>/pages/login.php">Login</a> &nbsp;/&nbsp;
            <a href="<?= SITE_URL ?>/pages/register.php">Register</a>
        <?php endif; ?>
    </span>
</div>

<header id="site-header">
    <nav class="main-nav">
        <a href="<?= SITE_URL ?>/index.php" class="logo">LUXE<span>.</span></a>

        <ul class="nav-links">
            <li><a href="<?= SITE_URL ?>/index.php"             class="<?= $currentPage==='index'      ?'active':'' ?>">Home</a></li>
            <li><a href="<?= SITE_URL ?>/pages/shop.php"        class="<?= $currentPage==='shop'       ?'active':'' ?>">Shop</a></li>
            <li><a href="<?= SITE_URL ?>/pages/collections.php" class="<?= $currentPage==='collections'?'active':'' ?>">Collections</a></li>
            <li><a href="<?= SITE_URL ?>/pages/wishlist.php"    class="<?= $currentPage==='wishlist'   ?'active':'' ?>">Wishlist</a></li>
            <li><a href="<?= SITE_URL ?>/pages/about.php"       class="<?= $currentPage==='about'      ?'active':'' ?>">About</a></li>
            <li><a href="<?= SITE_URL ?>/pages/contact.php"     class="<?= $currentPage==='contact'    ?'active':'' ?>">Contact</a></li>
        </ul>

        <div class="nav-actions">
            <a href="<?= SITE_URL ?>/pages/shop.php" class="nav-icon" title="Search"><i class="fa-solid fa-magnifying-glass"></i></a>
            <a href="<?= SITE_URL ?>/pages/wishlist.php" class="nav-icon" title="Wishlist">
                <i class="fa-regular fa-heart"></i>
                <?php if ($wishQty > 0): ?><span class="badge"><?= $wishQty ?></span><?php endif; ?>
            </a>
            <a href="<?= SITE_URL ?>/pages/cart.php" class="nav-icon cart-icon" title="Cart">
                <i class="fa-solid fa-bag-shopping"></i>
                <span class="badge cart-badge"><?= $cartQty ?></span>
            </a>
            <a href="<?= $user ? SITE_URL.'/pages/account.php' : SITE_URL.'/pages/login.php' ?>" class="nav-icon" title="Account">
                <i class="fa-regular fa-user"></i>
            </a>
            <button class="menu-toggle" aria-label="Menu"><i class="fa-solid fa-bars"></i></button>
        </div>
    </nav>
</header>

<!-- Flash Messages -->
<?php $success = flash('success'); $error = flash('error'); ?>
<?php if ($success): ?><div class="flash flash-success"><i class="fa-solid fa-circle-check"></i> <?= $success ?></div><?php endif; ?>
<?php if ($error):   ?><div class="flash flash-error"><i class="fa-solid fa-circle-xmark"></i> <?= $error ?></div><?php endif; ?>

<main>