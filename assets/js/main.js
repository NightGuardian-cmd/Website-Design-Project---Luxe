// =============================================
//  LUXE Premium — Main JavaScript
// =============================================

document.addEventListener('DOMContentLoaded', () => {

    // ---- Live Clock ----
    function updateClock() {
        const now = new Date();
        const time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const date = now.toLocaleDateString('en-US', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' });

        const topClock    = document.getElementById('live-clock');
        const footerClock = document.getElementById('footer-clock');

        if (topClock)    topClock.textContent    = `${date}  •  ${time}`;
        if (footerClock) footerClock.textContent = time;
    }
    updateClock();
    setInterval(updateClock, 1000);

    // ---- Loader ----
    const loader = document.querySelector('.loader');
    if (loader) {
        window.addEventListener('load', () => {
            setTimeout(() => loader.classList.add('hidden'), 600);
        });
    }

    // ---- Scroll Reveal ----
    const revealElements = document.querySelectorAll('.reveal');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });
    revealElements.forEach(el => observer.observe(el));

    // ---- Mobile Nav Toggle ----
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks   = document.querySelector('.nav-links');
    if (menuToggle && navLinks) {
        menuToggle.addEventListener('click', () => {
            const isOpen = navLinks.style.display === 'flex';
            navLinks.style.cssText = isOpen
                ? ''
                : 'display:flex;flex-direction:column;position:absolute;top:100%;left:0;width:100%;background:#fff;padding:20px 5%;gap:16px;box-shadow:0 10px 30px rgba(0,0,0,0.1);z-index:800;';
        });
    }

    // ---- Cart AJAX: Add to Cart ----
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            const productId = btn.dataset.id;
            const qty = btn.dataset.qty || 1;

            try {
                const res  = await fetch(`${SITE_URL}/api/cart.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'add', product_id: productId, qty: qty })
                });
                const data = await res.json();
                if (data.success) {
                    updateCartBadge(data.cart_count);
                    showToast('Item added to your bag!', 'success');
                }
            } catch (err) {
                showToast('Could not add item. Try again.', 'error');
            }
        });
    });

    // ---- Wishlist Toggle ----
    document.querySelectorAll('.wishlist-toggle').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            const productId = btn.dataset.id;

            try {
                const res  = await fetch(`${SITE_URL}/api/wishlist.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ product_id: productId })
                });
                const data = await res.json();
                if (data.success) {
                    btn.classList.toggle('wishlist-active', data.added);
                    const icon = btn.querySelector('i');
                    if (icon) icon.className = data.added ? 'fa-solid fa-heart' : 'fa-regular fa-heart';
                    showToast(data.added ? 'Added to wishlist!' : 'Removed from wishlist.', 'success');
                } else if (data.login) {
                    window.location.href = `${SITE_URL}/pages/login.php`;
                }
            } catch (err) {
                showToast('Something went wrong.', 'error');
            }
        });
    });

    // ---- Cart Badge Update ----
    function updateCartBadge(count) {
        const badges = document.querySelectorAll('.cart-badge');
        badges.forEach(b => b.textContent = count);
    }

    // ---- Toast Notification ----
    function showToast(msg, type = 'success') {
        let toast = document.getElementById('toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'toast';
            toast.style.cssText = `
                position:fixed;bottom:30px;right:30px;padding:14px 24px;border-radius:6px;
                font-size:0.9rem;font-weight:600;z-index:9999;
                display:flex;align-items:center;gap:10px;
                transform:translateY(20px);opacity:0;
                transition:all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
                font-family:'Inter',sans-serif;box-shadow:0 10px 30px rgba(0,0,0,0.2);
            `;
            document.body.appendChild(toast);
        }
        toast.style.background = type === 'success' ? '#1a1a1a' : '#e05252';
        toast.style.color = '#fff';
        toast.innerHTML = `<i class="fa-solid fa-${type === 'success' ? 'circle-check' : 'circle-xmark'}"></i> ${msg}`;
        requestAnimationFrame(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
        });
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(20px)';
        }, 3000);
    }

    // ---- Size Selector ----
    document.querySelectorAll('.size-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        });
    });

    // ---- Qty Controls on Cart Page ----
    document.querySelectorAll('.qty-increase, .qty-decrease').forEach(btn => {
        btn.addEventListener('click', async () => {
            const action    = btn.classList.contains('qty-increase') ? 'increase' : 'decrease';
            const productId = btn.closest('[data-product]').dataset.product;

            const res  = await fetch(`${SITE_URL}/api/cart.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action, product_id: productId })
            });
            const data = await res.json();
            if (data.success) window.location.reload();
        });
    });

    // ---- Delete from Cart ----
    document.querySelectorAll('.remove-from-cart').forEach(btn => {
        btn.addEventListener('click', async () => {
            const productId = btn.dataset.id;
            const res  = await fetch(`${SITE_URL}/api/cart.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'remove', product_id: productId })
            });
            const data = await res.json();
            if (data.success) window.location.reload();
        });
    });

    // ---- Newsletter Form ----
    const nlForm = document.querySelector('.newsletter-form');
    if (nlForm) {
        nlForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = nlForm.querySelector('input[type=email]').value;
            try {
                const res  = await fetch(`${SITE_URL}/api/newsletter.php`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email })
                });
                const data = await res.json();
                showToast(data.message || 'Subscribed!', data.success ? 'success' : 'error');
                if (data.success) nlForm.reset();
            } catch (err) {
                showToast('Could not subscribe. Try again.', 'error');
            }
        });
    }

    // ---- Shop Filter Form (auto-submit on change) ----
    document.querySelectorAll('.filter-option input').forEach(input => {
        input.addEventListener('change', () => {
            const form = input.closest('form');
            if (form) form.submit();
        });
    });

    // ---- Smooth scroll for anchor links ----
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', (e) => {
            const target = document.querySelector(anchor.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
});