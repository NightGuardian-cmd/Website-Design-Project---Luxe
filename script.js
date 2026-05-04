// Product Data
const products = [
    {
        id: 1,
        name: "Minimalist Silk Dress",
        price: 129.00,
        category: "Dresses",
        image: "https://images.unsplash.com/photo-1539008835279-43468ef9304e?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80"
    },
    {
        id: 2,
        name: "Classic Leather Tote",
        price: 89.00,
        category: "Accessories",
        image: "https://images.unsplash.com/photo-1584917033904-493bb3c39d8d?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80"
    },
    {
        id: 3,
        name: "Linen Summer Shirt",
        price: 59.00,
        category: "Tops",
        image: "https://images.unsplash.com/photo-1596755094514-f87e34085b2c?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80"
    },
    {
        id: 4,
        name: "Premium Wool Blazer",
        price: 199.00,
        category: "Outerwear",
        image: "https://images.unsplash.com/photo-1591047139829-d91aecb6caea?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80"
    },
    {
        id: 5,
        name: "Suede Chelsea Boots",
        price: 149.00,
        category: "Footwear",
        image: "https://images.unsplash.com/photo-1638247025967-b4e38f787b76?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80"
    },
    {
        id: 6,
        name: "Gold Chain Necklace",
        price: 45.00,
        category: "Jewelry",
        image: "https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80"
    }
];

// State
let cart = [];

// DOM Elements
const productGrid = document.getElementById('product-grid');
const cartSidebar = document.getElementById('cart-sidebar');
const cartItemsContainer = document.getElementById('cart-items');
const cartTotalAmount = document.getElementById('cart-total-amount');
const cartCount = document.querySelector('.cart-count');
const cartIcon = document.querySelector('.cart-icon');
const closeCart = document.querySelector('.close-cart');
const overlay = document.getElementById('overlay');
const header = document.querySelector('header');
const loader = document.querySelector('.loader');

// Initialize
window.addEventListener('DOMContentLoaded', () => {
    // Hide loader after 1.5s
    setTimeout(() => {
        loader.classList.add('hidden');
        revealOnScroll();
    }, 1500);

    renderProducts();
    updateCartUI();
});

// Render Products
function renderProducts() {
    productGrid.innerHTML = products.map(product => `
        <div class="product-card reveal-text">
            <div class="product-img">
                <img src="${product.image}" alt="${product.name}">
                <div class="product-actions">
                    <div class="action-btn add-to-cart" data-id="${product.id}">
                        <i class="fa-solid fa-bag-shopping"></i>
                    </div>
                    <div class="action-btn">
                        <i class="fa-regular fa-heart"></i>
                    </div>
                    <div class="action-btn">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                </div>
            </div>
            <div class="product-info">
                <p class="product-category">${product.category}</p>
                <h3 class="product-title">${product.name}</h3>
                <p class="product-price">$${product.price.toFixed(2)}</p>
            </div>
        </div>
    `).join('');

    // Add event listeners to new buttons
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const id = parseInt(e.currentTarget.dataset.id);
            addToCart(id);
        });
    });
}

// Cart Functions
function addToCart(id) {
    const product = products.find(p => p.id === id);
    const existingItem = cart.find(item => item.id === id);

    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({ ...product, quantity: 1 });
    }

    updateCartUI();
    openCartSidebar();
}

function removeFromCart(id) {
    cart = cart.filter(item => item.id !== id);
    updateCartUI();
}

function updateCartUI() {
    // Update count
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartCount.textContent = totalItems;

    // Update items list
    if (cart.length === 0) {
        cartItemsContainer.innerHTML = '<p style="text-align:center; margin-top:20px;">Your bag is empty</p>';
    } else {
        cartItemsContainer.innerHTML = cart.map(item => `
            <div class="cart-item">
                <img src="${item.image}" alt="${item.name}" class="cart-item-img">
                <div class="cart-item-info">
                    <h4>${item.name}</h4>
                    <p class="price">$${item.price.toFixed(2)} x ${item.quantity}</p>
                    <button onclick="removeFromCart(${item.id})" style="background:none; border:none; color:red; cursor:pointer; font-size:0.8rem; margin-top:5px;">Remove</button>
                </div>
            </div>
        `).join('');
    }

    // Update total
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    cartTotalAmount.textContent = `$${total.toFixed(2)}`;
}

// Sidebar Controls
function openCartSidebar() {
    cartSidebar.classList.add('active');
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeCartSidebar() {
    cartSidebar.classList.remove('active');
    overlay.classList.remove('active');
    document.body.style.overflow = 'auto';
}

cartIcon.addEventListener('click', (e) => {
    e.preventDefault();
    openCartSidebar();
});

closeCart.addEventListener('click', closeCartSidebar);
overlay.addEventListener('click', closeCartSidebar);

// Scroll Effects
window.addEventListener('scroll', () => {
    // Header background
    if (window.scrollY > 50) {
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
    }

    revealOnScroll();
});

function revealOnScroll() {
    const reveals = document.querySelectorAll('.reveal-text');
    reveals.forEach(reveal => {
        const windowHeight = window.innerHeight;
        const revealTop = reveal.getBoundingClientRect().top;
        const revealPoint = 150;

        if (revealTop < windowHeight - revealPoint) {
            reveal.classList.add('active');
        }
    });
}

// Newsletter Form
document.querySelector('.newsletter-form').addEventListener('submit', (e) => {
    e.preventDefault();
    const email = e.target.querySelector('input').value;
    if (email) {
        alert('Thank you for subscribing! Check your inbox for a welcome gift.');
        e.target.reset();
    }
});

// Smooth Scroll for Nav Links
document.querySelectorAll('.nav-links a').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        const targetSection = document.querySelector(targetId);
        
        // Update active link
        document.querySelectorAll('.nav-links a').forEach(a => a.classList.remove('active'));
        this.classList.add('active');

        window.scrollTo({
            top: targetSection.offsetTop - 80,
            behavior: 'smooth'
        });
    });
});