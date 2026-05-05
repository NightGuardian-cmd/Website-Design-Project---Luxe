<?php // includes/footer.php ?>
</main>

<footer>
    <div class="footer-grid">
        <div class="footer-brand">
            <div class="logo">LUXE<span>.</span></div>
            <p>Elevating your lifestyle with premium products and exceptional service since 2020.</p>
            <div class="social-links">
                <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                <a href="#" aria-label="Pinterest"><i class="fa-brands fa-pinterest-p"></i></a>
                <a href="#" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>
            </div>
        </div>
        <div class="footer-col">
            <h4>Shop</h4>
            <ul>
                <li><a href="<?= SITE_URL ?>/pages/shop.php?cat=dresses">Dresses</a></li>
                <li><a href="<?= SITE_URL ?>/pages/shop.php?cat=accessories">Accessories</a></li>
                <li><a href="<?= SITE_URL ?>/pages/shop.php?cat=outerwear">Outerwear</a></li>
                <li><a href="<?= SITE_URL ?>/pages/shop.php?cat=footwear">Footwear</a></li>
                <li><a href="<?= SITE_URL ?>/pages/shop.php?cat=jewelry">Jewelry</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Help</h4>
            <ul>
                <li><a href="<?= SITE_URL ?>/pages/about.php">About Us</a></li>
                <li><a href="<?= SITE_URL ?>/pages/contact.php">Contact</a></li>
                <li><a href="#">Shipping Policy</a></li>
                <li><a href="#">Returns & Exchanges</a></li>
                <li><a href="#">Size Guide</a></li>
            </ul>
        </div>
        <div class="footer-col">
            <h4>Newsletter</h4>
            <p>Get exclusive offers and first access to new collections.</p>
            <form class="newsletter-form" action="<?= SITE_URL ?>/api/newsletter.php" method="POST">
                <input type="email" name="email" placeholder="Your email address" required>
                <button type="submit"><i class="fa-solid fa-paper-plane"></i></button>
            </form>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> LUXE Premium. All rights reserved.</p>
        <div id="footer-clock"></div>
    </div>
</footer>

<script src="<?= SITE_URL ?>/assets/main.js"></script>
</body>
</html>