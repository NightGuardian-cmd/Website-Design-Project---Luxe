-- =============================================
--  LUXE Premium E-Commerce Database Schema
-- =============================================

CREATE DATABASE IF NOT EXISTS luxe_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE luxe_db;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    avatar VARCHAR(255) DEFAULT NULL,
    role ENUM('customer','admin') DEFAULT 'customer',
    is_verified TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories Table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    image VARCHAR(255)
);

-- Products Table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    sale_price DECIMAL(10,2) DEFAULT NULL,
    category_id INT,
    image VARCHAR(255),
    stock INT DEFAULT 100,
    rating DECIMAL(3,2) DEFAULT 4.50,
    review_count INT DEFAULT 0,
    featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    status ENUM('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
    total_amount DECIMAL(10,2) NOT NULL,
    shipping_address TEXT,
    payment_method VARCHAR(50) DEFAULT 'card',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Wishlist Table
CREATE TABLE IF NOT EXISTS wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_wishlist (user_id, product_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Reviews Table
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_review (user_id, product_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Contact Messages Table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Newsletter Subscribers
CREATE TABLE IF NOT EXISTS newsletter (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) UNIQUE NOT NULL,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
--  SEED DATA
-- =============================================

INSERT INTO categories (name, slug, description, image) VALUES
('Dresses',    'dresses',    'Elegant dresses for every occasion', 'https://images.unsplash.com/photo-1539008835279-43468ef9304e?w=600&q=80'),
('Accessories','accessories','Handbags, belts, scarves & more',   'https://images.unsplash.com/photo-1584917033904-493bb3c39d8d?w=600&q=80'),
('Tops',       'tops',       'Blouses, shirts and knitwear',       'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=600&q=80'),
('Outerwear',  'outerwear',  'Coats, jackets and blazers',         'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=600&q=80'),
('Footwear',   'footwear',   'Shoes, boots and sandals',           'https://images.unsplash.com/photo-1638247025967-b4e38f787b76?w=600&q=80'),
('Jewelry',    'jewelry',    'Necklaces, rings and earrings',      'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=600&q=80');

INSERT INTO products (name, slug, description, price, sale_price, category_id, image, stock, rating, review_count, featured) VALUES
('Minimalist Silk Dress',   'minimalist-silk-dress',   'A timeless piece crafted from 100% pure silk, perfect for day-to-evening wear.', 129.00, NULL,  1, 'https://images.unsplash.com/photo-1539008835279-43468ef9304e?w=600&q=80', 50, 4.8, 124, 1),
('Classic Leather Tote',    'classic-leather-tote',    'Full-grain leather tote with suede lining and gold-tone hardware.',              89.00,  69.00, 2, 'https://images.unsplash.com/photo-1584917033904-493bb3c39d8d?w=600&q=80', 35, 4.6, 89,  1),
('Linen Summer Shirt',      'linen-summer-shirt',      'Breathable linen shirt with a relaxed fit. Available in 6 colors.',              59.00,  NULL,  3, 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=600&q=80', 80, 4.5, 67,  1),
('Premium Wool Blazer',     'premium-wool-blazer',     'Italian wool blend blazer with a tailored silhouette.',                          199.00, NULL,  4, 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=600&q=80', 25, 4.9, 210, 1),
('Suede Chelsea Boots',     'suede-chelsea-boots',     'Hand-crafted suede Chelsea boots with elasticated side panels.',                 149.00, 119.00,5, 'https://images.unsplash.com/photo-1638247025967-b4e38f787b76?w=600&q=80', 40, 4.7, 156, 1),
('Gold Chain Necklace',     'gold-chain-necklace',     '18k gold-plated chain necklace with lobster clasp closure.',                     45.00,  NULL,  6, 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=600&q=80', 100,4.4, 43,  1),
('Floral Maxi Dress',       'floral-maxi-dress',       'Flowing maxi dress in vintage floral print, made from sustainable cotton.',      95.00,  75.00, 1, 'https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=600&q=80', 30, 4.6, 98,  0),
('Cashmere Sweater',        'cashmere-sweater',        'Ultra-soft Grade A cashmere sweater in classic crew neck style.',                 175.00, NULL,  3, 'https://images.unsplash.com/photo-1576566588028-4147f3842f27?w=600&q=80', 45, 4.8, 187, 0),
('Leather Belt',            'leather-belt',            'Full-grain leather belt with a brushed-gold pin buckle.',                        55.00,  NULL,  2, 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=600&q=80', 60, 4.3, 34,  0),
('Trench Coat',             'trench-coat',             'Classic double-breasted trench coat in water-resistant cotton gabardine.',        285.00, 225.00,4, 'https://images.unsplash.com/photo-1520975954732-35dd22299614?w=600&q=80', 20, 4.9, 312, 0),
('Pearl Drop Earrings',     'pearl-drop-earrings',     'Freshwater pearl drop earrings set in sterling silver.',                         65.00,  NULL,  6, 'https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?w=600&q=80', 75, 4.7, 56,  0),
('Ankle Strap Heels',       'ankle-strap-heels',       'Elegant block-heel sandals with adjustable ankle strap.',                        135.00, NULL,  5, 'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?w=600&q=80', 35, 4.5, 78,  0);

-- Demo admin user (password: Admin@1234)
INSERT INTO users (first_name, last_name, email, password, role) VALUES
('Admin', 'LUXE', 'admin@luxe.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
-- Note: password above is a bcrypt hash of 'password' — change it immediately in production!