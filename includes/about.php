<?php
require_once __DIR__ . '/config.php';
$pageTitle = 'About Us';
include __DIR__ . '/header.php';
?>

<!-- Hero -->
<div class="about-hero">
    <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1600&q=80" alt="LUXE Atelier" loading="lazy">
    <div class="about-hero-overlay">
        <h1 class="reveal">Our Story</h1>
        <p class="reveal">Built on a belief that fashion should feel as exceptional as it looks.</p>
    </div>
</div>

<!-- Story -->
<section class="about-story">
    <div class="reveal">
        <img src="https://images.unsplash.com/photo-1556742393-d75f468bfcb0?w=800&q=80" alt="Atelier">
    </div>
    <div class="reveal">
        <span class="subtitle">Since 2020</span>
        <h2>Crafted with Intention</h2>
        <p>LUXE was founded in 2020 with a simple idea: that premium fashion should be accessible without sacrificing craftsmanship. We work directly with artisan workshops in Milan, Paris, and Istanbul to bring you pieces that are made to last.</p>
        <p>Every fabric is hand-selected. Every stitch is considered. From our signature silk dresses to our hand-tooled leather goods, each piece carries the mark of genuine skill and care.</p>
        <a href="<?= SITE_URL ?>/pages/shop.php" class="btn btn-primary" style="margin-top:16px;">Explore Collection</a>
    </div>
</section>

<!-- Values -->
<div class="stats-strip" style="background:var(--bg-alt);color:var(--text);">
    <div class="stat-item" style="border-color:var(--border);">
        <div class="stat-num">100%</div>
        <div class="stat-label" style="color:var(--text-muted);">Ethically Sourced</div>
    </div>
    <div class="stat-item" style="border-color:var(--border);">
        <div class="stat-num">0</div>
        <div class="stat-label" style="color:var(--text-muted);">Single-Use Plastics</div>
    </div>
    <div class="stat-item" style="border-color:var(--border);">
        <div class="stat-num">50+</div>
        <div class="stat-label" style="color:var(--text-muted);">Partner Artisans</div>
    </div>
    <div class="stat-item" style="border-color:var(--border);">
        <div class="stat-num">5★</div>
        <div class="stat-label" style="color:var(--text-muted);">Average Rating</div>
    </div>
</div>

<!-- Team -->
<section style="padding:80px 5% 0;">
    <div class="section-header">
        <span class="subtitle">The People</span>
        <h2 class="reveal">Meet Our Team</h2>
    </div>
    <div class="team-grid">
        <?php
        $team = [
            ['name'=>'Sophia Laurent',  'role'=>'Creative Director',  'img'=>'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=600&q=80'],
            ['name'=>'Marcus Cole',     'role'=>'Head of Design',      'img'=>'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=600&q=80'],
            ['name'=>'Amara Ndiaye',    'role'=>'Brand Director',      'img'=>'https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?w=600&q=80'],
        ];
        foreach ($team as $m): ?>
        <div class="team-card reveal">
            <img src="<?= $m['img'] ?>" alt="<?= htmlspecialchars($m['name']) ?>">
            <h3><?= htmlspecialchars($m['name']) ?></h3>
            <p><?= htmlspecialchars($m['role']) ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<div style="padding-bottom:100px;"></div>

<script>const SITE_URL = '<?= SITE_URL ?>';</script>
<?php include __DIR__ . '/footer.php'; ?>