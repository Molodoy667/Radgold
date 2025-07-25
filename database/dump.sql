-- Game Marketplace - Полная структура базы данных
-- Кодировка: cp1251

CREATE DATABASE IF NOT EXISTS game_marketplace CHARACTER SET cp1251 COLLATE cp1251_general_ci;
USE game_marketplace;

-- ========================================
-- ТАБЛИЦА ПОЛЬЗОВАТЕЛЕЙ
-- ========================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    login VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) DEFAULT NULL,
    status ENUM('active','banned') DEFAULT 'active',
    role ENUM('user','seller','admin') DEFAULT 'user',
    balance DECIMAL(10,2) DEFAULT 0.00,
    rating DECIMAL(3,2) DEFAULT 0.00,
    total_sales INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_users_status (status),
    INDEX idx_users_role (role),
    INDEX idx_users_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

-- ========================================
-- ТАБЛИЦА ТОВАРОВ/УСЛУГ
-- ========================================
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('account','service','rent') NOT NULL,
    game VARCHAR(100) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'RUB',
    images TEXT,
    status ENUM('active','sold','banned','pending') DEFAULT 'pending',
    views INT DEFAULT 0,
    rating DECIMAL(3,2) DEFAULT 0.00,
    total_reviews INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_products_status (status),
    INDEX idx_products_game (game),
    INDEX idx_products_type (type),
    INDEX idx_products_price (price),
    INDEX idx_products_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

-- ========================================
-- ТАБЛИЦА ПОКУПОК
-- ========================================
CREATE TABLE purchases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    buyer_id INT NOT NULL,
    seller_id INT NOT NULL,
    product_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'RUB',
    commission DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('pending','completed','cancelled','disputed','refunded') DEFAULT 'pending',
    payment_method VARCHAR(50) DEFAULT NULL,
    transaction_id VARCHAR(255) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_purchases_status (status),
    INDEX idx_purchases_buyer (buyer_id),
    INDEX idx_purchases_seller (seller_id)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

-- ========================================
-- ТАБЛИЦА АРЕНДЫ
-- ========================================
CREATE TABLE rentals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    renter_id INT NOT NULL,
    owner_id INT NOT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'RUB',
    status ENUM('active','completed','cancelled','expired') DEFAULT 'active',
    payment_method VARCHAR(50) DEFAULT NULL,
    transaction_id VARCHAR(255) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (renter_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_rentals_status (status),
    INDEX idx_rentals_dates (start_date, end_date)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

-- ========================================
-- ТАБЛИЦА ПОДПИСОК
-- ========================================
CREATE TABLE subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('premium','boost','vip') NOT NULL,
    plan VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'RUB',
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    status ENUM('active','expired','cancelled') DEFAULT 'active',
    auto_renew BOOLEAN DEFAULT FALSE,
    payment_method VARCHAR(50) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_subscriptions_status (status),
    INDEX idx_subscriptions_dates (start_date, end_date)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

-- ========================================
-- ТАБЛИЦА ИЗБРАННОГО
-- ========================================
CREATE TABLE favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorite (user_id, product_id),
    INDEX idx_favorites_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

-- ========================================
-- ТАБЛИЦА ОТЗЫВОВ И РЕЙТИНГОВ
-- ========================================
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    purchase_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    title VARCHAR(255),
    comment TEXT,
    status ENUM('pending','approved','rejected') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (purchase_id) REFERENCES purchases(id) ON DELETE CASCADE,
    UNIQUE KEY unique_review (purchase_id),
    INDEX idx_reviews_product (product_id),
    INDEX idx_reviews_rating (rating),
    INDEX idx_reviews_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

-- ========================================
-- ТАБЛИЦА ЖАЛОБ/ДИСПУТОВ
-- ========================================
CREATE TABLE disputes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_id INT NOT NULL,
    user_id INT NOT NULL,
    type ENUM('refund','quality','delivery','other') NOT NULL,
    subject VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('open','in_progress','resolved','closed') DEFAULT 'open',
    priority ENUM('low','medium','high','urgent') DEFAULT 'medium',
    admin_id INT DEFAULT NULL,
    admin_response TEXT,
    resolution TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (purchase_id) REFERENCES purchases(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_disputes_status (status),
    INDEX idx_disputes_priority (priority),
    INDEX idx_disputes_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

-- ========================================
-- ТАБЛИЦА СООБЩЕНИЙ ДИСПУТА
-- ========================================
CREATE TABLE dispute_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    dispute_id INT NOT NULL,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (dispute_id) REFERENCES disputes(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_dispute_messages_dispute (dispute_id)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

-- ========================================
-- ТАБЛИЦА ЧАТА
-- ========================================
CREATE TABLE chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    message_type ENUM('text','image','file') DEFAULT 'text',
    file_url VARCHAR(255) DEFAULT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    read_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_chat_users (sender_id, receiver_id),
    INDEX idx_chat_unread (receiver_id, is_read)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

-- ========================================
-- ТАБЛИЦА УВЕДОМЛЕНИЙ
-- ========================================
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('purchase','sale','message','dispute','system') NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    data JSON DEFAULT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    read_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_notifications_user (user_id),
    INDEX idx_notifications_unread (user_id, is_read)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

-- ========================================
-- ТАБЛИЦА ЛОГОВ
-- ========================================
CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50) DEFAULT NULL,
    entity_id INT DEFAULT NULL,
    details JSON DEFAULT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_logs_user (user_id),
    INDEX idx_logs_action (action),
    INDEX idx_logs_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

-- ========================================
-- ТАБЛИЦА НАСТРОЕК САЙТА
-- ========================================
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_type ENUM('string','number','boolean','json') DEFAULT 'string',
    description TEXT,
    is_public BOOLEAN DEFAULT FALSE,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

-- ========================================
-- ВСТАВКА ТЕСТОВЫХ ДАННЫХ
-- ========================================

-- Пользователи (пароль: 123456)
INSERT INTO users (email, login, password, role, balance, rating) VALUES
('admin@marketplace.com', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 0.00, 5.00),
('seller@marketplace.com', 'seller', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'seller', 15000.00, 4.85),
('user@marketplace.com', 'user', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 5000.00, 4.20),
('gamer@marketplace.com', 'gamer', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 2500.00, 4.75);

-- Товары
INSERT INTO products (user_id, type, game, title, description, price, currency, status, views, rating) VALUES
(2, 'account', 'CS:GO', 'Prime аккаунт CS:GO', 'Prime аккаунт CS:GO с 100+ часами игры, ранг Legendary Eagle, много скинов, VAC чистый', 2500.00, 'RUB', 'active', 45, 4.8),
(2, 'service', 'Dota 2', 'Буст MMR Dota 2', 'Буст MMR от Herald до Immortal, профессиональный игрок, гарантия результата, быстрая работа', 5000.00, 'RUB', 'active', 32, 4.9),
(2, 'rent', 'GTA V', 'Аренда GTA V', 'Аренда аккаунта GTA V на 7 дней, все DLC, много денег в игре, моды установлены', 500.00, 'RUB', 'active', 18, 4.6),
(2, 'account', 'League of Legends', 'Аккаунт LoL', 'Аккаунт LoL с 50+ чемпионами, ранг Gold, много скинов, редкие иконки', 3000.00, 'RUB', 'active', 28, 4.7),
(2, 'service', 'Valorant', 'Буст ранга Valorant', 'Буст ранга в Valorant, от Iron до Diamond, быстрая работа, гарантия', 3500.00, 'RUB', 'active', 22, 4.5),
(3, 'account', 'World of Warcraft', 'WoW аккаунт', 'Аккаунт WoW с 60 уровнем, много золота, редкие предметы, все дополнения', 8000.00, 'RUB', 'active', 15, 4.9);

-- Настройки сайта
INSERT INTO settings (setting_key, setting_value, setting_type, description, is_public) VALUES
('site_title', 'Game Marketplace', 'string', 'Название сайта', TRUE),
('site_description', 'Современный маркетплейс для игровых аккаунтов и услуг', 'string', 'Описание сайта', TRUE),
('contact_email', 'support@marketplace.com', 'string', 'Email поддержки', TRUE),
('commission_percent', '5', 'number', 'Процент комиссии с продаж', FALSE),
('registration_enabled', '1', 'boolean', 'Включена ли регистрация новых пользователей', FALSE),
('maintenance_mode', '0', 'boolean', 'Режим обслуживания', FALSE),
('max_images_per_product', '10', 'number', 'Максимальное количество изображений на товар', FALSE),
('min_price', '100', 'number', 'Минимальная цена товара', FALSE),
('max_price', '100000', 'number', 'Максимальная цена товара', FALSE),
('auto_approve_products', '0', 'boolean', 'Автоматическое одобрение товаров', FALSE);

-- Тестовые покупки
INSERT INTO purchases (buyer_id, seller_id, product_id, price, currency, status) VALUES
(3, 2, 1, 2500.00, 'RUB', 'completed'),
(4, 2, 2, 5000.00, 'RUB', 'completed'),
(3, 2, 3, 500.00, 'RUB', 'completed');

-- Тестовые отзывы
INSERT INTO reviews (product_id, user_id, purchase_id, rating, title, comment, status) VALUES
(1, 3, 1, 5, 'Отличный аккаунт!', 'Все работает отлично, продавец быстро передал данные. Рекомендую!', 'approved'),
(2, 4, 2, 5, 'Быстрый буст', 'Буст выполнен быстро и качественно. Достигли нужного ранга за 2 дня.', 'approved'),
(3, 3, 3, 4, 'Хорошая аренда', 'Аккаунт в хорошем состоянии, все работает. Небольшие задержки при передаче.', 'approved');

-- Тестовые избранные
INSERT INTO favorites (user_id, product_id) VALUES
(3, 4),
(3, 5),
(4, 1),
(4, 6);

-- Обновляем рейтинги товаров
UPDATE products p SET 
    rating = (SELECT AVG(rating) FROM reviews WHERE product_id = p.id AND status = 'approved'),
    total_reviews = (SELECT COUNT(*) FROM reviews WHERE product_id = p.id AND status = 'approved')
WHERE id IN (1, 2, 3);

-- Обновляем рейтинги пользователей
UPDATE users u SET 
    rating = (SELECT AVG(rating) FROM reviews r JOIN purchases p ON r.purchase_id = p.id WHERE p.seller_id = u.id AND r.status = 'approved'),
    total_sales = (SELECT COUNT(*) FROM purchases WHERE seller_id = u.id AND status = 'completed')
WHERE role = 'seller';
