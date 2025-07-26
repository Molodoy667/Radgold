-- ====================================
-- GAMEMARKET PRO - ПОЛНАЯ СХЕМА БД
-- Современный маркетплейс для игрового контента
-- Кодировка: UTF-8
-- ====================================

DROP DATABASE IF EXISTS gamemarket_pro;
CREATE DATABASE gamemarket_pro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gamemarket_pro;

-- ====================================
-- ТАБЛИЦА ПОЛЬЗОВАТЕЛЕЙ
-- ====================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    avatar VARCHAR(500) DEFAULT NULL,
    status ENUM('active', 'banned', 'suspended') DEFAULT 'active',
    role ENUM('user', 'seller', 'moderator', 'admin') DEFAULT 'user',
    balance DECIMAL(12,2) DEFAULT 0.00,
    rating DECIMAL(3,2) DEFAULT 0.00,
    total_sales INT DEFAULT 0,
    total_purchases INT DEFAULT 0,
    subscription_type ENUM('basic', 'premium', 'pro') DEFAULT 'basic',
    subscription_expires_at DATETIME DEFAULT NULL,
    last_activity_at DATETIME DEFAULT NULL,
    email_verified_at DATETIME DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    phone_verified_at DATETIME DEFAULT NULL,
    timezone VARCHAR(50) DEFAULT 'Europe/Moscow',
    language VARCHAR(10) DEFAULT 'ru',
    two_factor_enabled BOOLEAN DEFAULT FALSE,
    two_factor_secret VARCHAR(255) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_users_email (email),
    INDEX idx_users_username (username),
    INDEX idx_users_status (status),
    INDEX idx_users_role (role),
    INDEX idx_users_rating (rating),
    INDEX idx_users_subscription (subscription_type),
    INDEX idx_users_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- ТАБЛИЦА ТОВАРОВ/УСЛУГ
-- ====================================
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('account', 'service', 'boost', 'rent', 'farm', 'coaching', 'currency', 'items') NOT NULL,
    game VARCHAR(100) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    short_description VARCHAR(500),
    price DECIMAL(12,2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'RUB',
    original_price DECIMAL(12,2) DEFAULT NULL,
    images JSON DEFAULT NULL,
    specifications JSON DEFAULT NULL,
    delivery_info TEXT,
    delivery_time VARCHAR(100) DEFAULT NULL,
    status ENUM('draft', 'pending', 'active', 'sold', 'banned', 'archived') DEFAULT 'draft',
    visibility ENUM('public', 'private', 'featured') DEFAULT 'public',
    views INT DEFAULT 0,
    favorites_count INT DEFAULT 0,
    rating DECIMAL(3,2) DEFAULT 0.00,
    total_reviews INT DEFAULT 0,
    auto_delivery BOOLEAN DEFAULT FALSE,
    instant_delivery BOOLEAN DEFAULT FALSE,
    warranty_days INT DEFAULT 0,
    stock_quantity INT DEFAULT 1,
    sold_count INT DEFAULT 0,
    tags JSON DEFAULT NULL,
    seo_title VARCHAR(255) DEFAULT NULL,
    seo_description VARCHAR(500) DEFAULT NULL,
    featured_until DATETIME DEFAULT NULL,
    expires_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_products_user (user_id),
    INDEX idx_products_type (type),
    INDEX idx_products_game (game),
    INDEX idx_products_status (status),
    INDEX idx_products_price (price),
    INDEX idx_products_rating (rating),
    INDEX idx_products_created (created_at),
    INDEX idx_products_featured (featured_until),
    FULLTEXT idx_products_search (title, description, short_description)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- ТАБЛИЦА ПОКУПОК
-- ====================================
CREATE TABLE purchases (
    id INT AUTO_INCREMENT PRIMARY KEY,
    buyer_id INT NOT NULL,
    seller_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    price DECIMAL(12,2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'RUB',
    commission DECIMAL(12,2) DEFAULT 0.00,
    total_amount DECIMAL(12,2) NOT NULL,
    status ENUM('pending', 'paid', 'processing', 'delivered', 'completed', 'cancelled', 'disputed', 'refunded') DEFAULT 'pending',
    payment_method VARCHAR(50) DEFAULT NULL,
    payment_id VARCHAR(255) DEFAULT NULL,
    transaction_id VARCHAR(255) DEFAULT NULL,
    delivery_data JSON DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    auto_complete_at DATETIME DEFAULT NULL,
    completed_at DATETIME DEFAULT NULL,
    cancelled_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_purchases_buyer (buyer_id),
    INDEX idx_purchases_seller (seller_id),
    INDEX idx_purchases_product (product_id),
    INDEX idx_purchases_status (status),
    INDEX idx_purchases_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- ТАБЛИЦА АРЕНДЫ
-- ====================================
CREATE TABLE rentals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    renter_id INT NOT NULL,
    owner_id INT NOT NULL,
    purchase_id INT DEFAULT NULL,
    start_date DATETIME NOT NULL,
    end_date DATETIME NOT NULL,
    price_per_day DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(12,2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'RUB',
    status ENUM('pending', 'active', 'paused', 'completed', 'cancelled', 'expired', 'terminated') DEFAULT 'pending',
    access_data JSON DEFAULT NULL,
    terms_accepted BOOLEAN DEFAULT FALSE,
    deposit_amount DECIMAL(12,2) DEFAULT 0.00,
    deposit_returned BOOLEAN DEFAULT FALSE,
    auto_extend BOOLEAN DEFAULT FALSE,
    reminder_sent BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (renter_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (purchase_id) REFERENCES purchases(id) ON DELETE SET NULL,
    INDEX idx_rentals_product (product_id),
    INDEX idx_rentals_renter (renter_id),
    INDEX idx_rentals_owner (owner_id),
    INDEX idx_rentals_status (status),
    INDEX idx_rentals_dates (start_date, end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- ТАБЛИЦА ИЗБРАННОГО
-- ====================================
CREATE TABLE favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorite (user_id, product_id),
    INDEX idx_favorites_user (user_id),
    INDEX idx_favorites_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- ТАБЛИЦА СООБЩЕНИЙ В ЧАТЕ
-- ====================================
CREATE TABLE chat_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    recipient_id INT NOT NULL,
    product_id INT DEFAULT NULL,
    purchase_id INT DEFAULT NULL,
    message TEXT NOT NULL,
    message_type ENUM('text', 'image', 'file', 'system') DEFAULT 'text',
    attachments JSON DEFAULT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    is_system BOOLEAN DEFAULT FALSE,
    read_at DATETIME DEFAULT NULL,
    deleted_by_sender BOOLEAN DEFAULT FALSE,
    deleted_by_recipient BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
    FOREIGN KEY (purchase_id) REFERENCES purchases(id) ON DELETE SET NULL,
    INDEX idx_messages_sender (sender_id),
    INDEX idx_messages_recipient (recipient_id),
    INDEX idx_messages_conversation (sender_id, recipient_id),
    INDEX idx_messages_product (product_id),
    INDEX idx_messages_created (created_at),
    INDEX idx_messages_unread (recipient_id, is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- ТАБЛИЦА СПОРОВ
-- ====================================
CREATE TABLE disputes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_id INT NOT NULL,
    initiator_id INT NOT NULL,
    respondent_id INT NOT NULL,
    moderator_id INT DEFAULT NULL,
    subject VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    category ENUM('not_delivered', 'wrong_item', 'account_invalid', 'payment_issue', 'other') NOT NULL,
    status ENUM('open', 'in_review', 'awaiting_response', 'resolved', 'closed', 'escalated') DEFAULT 'open',
    priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'medium',
    resolution TEXT DEFAULT NULL,
    resolution_type ENUM('refund', 'replacement', 'partial_refund', 'no_action') DEFAULT NULL,
    refund_amount DECIMAL(12,2) DEFAULT NULL,
    evidence JSON DEFAULT NULL,
    admin_notes TEXT DEFAULT NULL,
    resolved_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (purchase_id) REFERENCES purchases(id) ON DELETE CASCADE,
    FOREIGN KEY (initiator_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (respondent_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (moderator_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_disputes_purchase (purchase_id),
    INDEX idx_disputes_initiator (initiator_id),
    INDEX idx_disputes_moderator (moderator_id),
    INDEX idx_disputes_status (status),
    INDEX idx_disputes_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- ТАБЛИЦА ОТЗЫВОВ
-- ====================================
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_id INT NOT NULL,
    product_id INT NOT NULL,
    reviewer_id INT NOT NULL,
    seller_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT DEFAULT NULL,
    pros TEXT DEFAULT NULL,
    cons TEXT DEFAULT NULL,
    is_verified BOOLEAN DEFAULT TRUE,
    is_anonymous BOOLEAN DEFAULT FALSE,
    helpful_count INT DEFAULT 0,
    reply TEXT DEFAULT NULL,
    reply_at DATETIME DEFAULT NULL,
    status ENUM('published', 'pending', 'hidden', 'rejected') DEFAULT 'published',
    moderation_reason TEXT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (purchase_id) REFERENCES purchases(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_reviews_purchase (purchase_id),
    INDEX idx_reviews_product (product_id),
    INDEX idx_reviews_reviewer (reviewer_id),
    INDEX idx_reviews_seller (seller_id),
    INDEX idx_reviews_rating (rating),
    INDEX idx_reviews_created (created_at),
    UNIQUE KEY unique_review (purchase_id, reviewer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- ТАБЛИЦА УВЕДОМЛЕНИЙ
-- ====================================
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(100) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    action_url VARCHAR(500) DEFAULT NULL,
    data JSON DEFAULT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    read_at DATETIME DEFAULT NULL,
    expires_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_notifications_user (user_id),
    INDEX idx_notifications_type (type),
    INDEX idx_notifications_read (user_id, is_read),
    INDEX idx_notifications_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- ТАБЛИЦА НАСТРОЕК СИСТЕМЫ
-- ====================================
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    key_name VARCHAR(100) NOT NULL UNIQUE,
    value TEXT DEFAULT NULL,
    type ENUM('string', 'integer', 'float', 'boolean', 'json') DEFAULT 'string',
    description TEXT DEFAULT NULL,
    is_public BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_settings_key (key_name),
    INDEX idx_settings_public (is_public)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- ТАБЛИЦА ЛОГОВ АКТИВНОСТИ
-- ====================================
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    resource_type VARCHAR(50) DEFAULT NULL,
    resource_id INT DEFAULT NULL,
    details TEXT DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_logs_user (user_id),
    INDEX idx_logs_action (action),
    INDEX idx_logs_resource (resource_type, resource_id),
    INDEX idx_logs_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- ТАБЛИЦА ТРАНЗАКЦИЙ БАЛАНСА
-- ====================================
CREATE TABLE balance_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('deposit', 'withdrawal', 'purchase', 'sale', 'refund', 'commission', 'bonus', 'penalty') NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'RUB',
    balance_before DECIMAL(12,2) NOT NULL,
    balance_after DECIMAL(12,2) NOT NULL,
    reference_type VARCHAR(50) DEFAULT NULL,
    reference_id INT DEFAULT NULL,
    description TEXT DEFAULT NULL,
    metadata JSON DEFAULT NULL,
    status ENUM('pending', 'completed', 'failed', 'cancelled') DEFAULT 'completed',
    processed_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_balance_user (user_id),
    INDEX idx_balance_type (type),
    INDEX idx_balance_status (status),
    INDEX idx_balance_reference (reference_type, reference_id),
    INDEX idx_balance_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- ТАБЛИЦА КУПОНОВ И ПРОМОКОДОВ
-- ====================================
CREATE TABLE coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    type ENUM('fixed', 'percentage') NOT NULL,
    value DECIMAL(10,2) NOT NULL,
    min_purchase_amount DECIMAL(12,2) DEFAULT 0.00,
    max_discount_amount DECIMAL(12,2) DEFAULT NULL,
    usage_limit INT DEFAULT NULL,
    usage_count INT DEFAULT 0,
    user_limit INT DEFAULT 1,
    applicable_to ENUM('all', 'category', 'product', 'user') DEFAULT 'all',
    applicable_ids JSON DEFAULT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    starts_at DATETIME DEFAULT NULL,
    expires_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_coupons_code (code),
    INDEX idx_coupons_active (is_active),
    INDEX idx_coupons_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- ТАБЛИЦА ИСПОЛЬЗОВАНИЯ КУПОНОВ
-- ====================================
CREATE TABLE coupon_usages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coupon_id INT NOT NULL,
    user_id INT NOT NULL,
    purchase_id INT DEFAULT NULL,
    discount_amount DECIMAL(10,2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (purchase_id) REFERENCES purchases(id) ON DELETE SET NULL,
    INDEX idx_coupon_usage_coupon (coupon_id),
    INDEX idx_coupon_usage_user (user_id),
    INDEX idx_coupon_usage_purchase (purchase_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- ВСТАВКА НАЧАЛЬНЫХ ДАННЫХ
-- ====================================

-- Настройки системы
INSERT INTO settings (key_name, value, type, description, is_public) VALUES
('site_name', 'GameMarket Pro', 'string', 'Название сайта', true),
('site_description', 'Современный маркетплейс для игрового контента', 'string', 'Описание сайта', true),
('commission_rate', '0.05', 'float', 'Комиссия платформы (5%)', false),
('premium_commission_rate', '0.03', 'float', 'Комиссия для премиум пользователей (3%)', false),
('currency_primary', 'RUB', 'string', 'Основная валюта', true),
('registration_enabled', 'true', 'boolean', 'Разрешить регистрацию', true),
('maintenance_mode', 'false', 'boolean', 'Режим обслуживания', false),
('max_file_size', '5242880', 'integer', 'Максимальный размер файла (5MB)', false),
('auto_complete_days', '7', 'integer', 'Дни до автозавершения заказа', false),
('dispute_timeout_days', '30', 'integer', 'Время на подачу спора (дни)', false);

-- Администратор
INSERT INTO users (email, username, password_hash, status, role, balance, rating) VALUES
('admin@gamemarket.pro', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'active', 'admin', 1000.00, 5.00);

-- Тестовые пользователи
INSERT INTO users (email, username, password_hash, status, role, balance, rating, subscription_type) VALUES
('seller1@test.com', 'pro_seller', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'active', 'seller', 500.00, 4.8, 'premium'),
('buyer1@test.com', 'gamer_buyer', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'active', 'user', 200.00, 4.5, 'basic'),
('seller2@test.com', 'boost_master', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'active', 'seller', 750.00, 4.9, 'pro');

-- Тестовые товары
INSERT INTO products (user_id, type, game, title, description, short_description, price, currency, status, visibility, specifications, delivery_time, auto_delivery) VALUES
(2, 'account', 'valorant', 'Valorant аккаунт Radiant ранг', 'Продается топовый аккаунт Valorant с рангом Radiant. Все агенты разблокированы, много скинов включая Elderflame, Prime и другие коллекции. Аккаунт чистый, без нарушений.', 'Valorant Radiant ранг, все агенты, топовые скины', 15000.00, 'RUB', 'active', 'featured', '{"rank": "Radiant", "agents": "Все разблокированы", "skins": "Elderflame, Prime, Reaver", "rr": "350+"}', '1-2 часа', true),

(2, 'service', 'csgo', 'Буст ранга CS2 до Global Elite', 'Профессиональный буст ранга в Counter-Strike 2. Работаем с любого ранга до Global Elite. Гарантия результата, стрим по запросу. Опытная команда бустеров.', 'Буст CS2 до Global Elite, гарантия качества', 8000.00, 'RUB', 'active', 'public', '{"from_rank": "Любой", "to_rank": "Global Elite", "duration": "5-10 дней", "guarantee": "100%"}', '5-10 дней', false),

(4, 'boost', 'wow', 'Прокачка персонажа WoW до 80 lvl', 'Быстрая и безопасная прокачка персонажа в World of Warcraft до максимального уровня. Включает освоение всех ключевых механик, получение базового оборудования.', 'WoW прокачка 1-80 lvl, быстро и безопасно', 3500.00, 'RUB', 'active', 'public', '{"level_from": "1", "level_to": "80", "includes": "Квесты, подземелья, базовый гир", "method": "Ручная прокачка"}', '3-5 дней', false),

(2, 'rent', 'genshin', 'Аренда Genshin Impact с 5* персонажами', 'Сдается аккаунт Genshin Impact с множеством 5-звездочных персонажей: Hu Tao, Zhongli, Raiden Shogun, Kazuha и другие. Высокий Adventure Rank, много ресурсов.', 'Genshin аккаунт с топ персонажами в аренду', 500.00, 'RUB', 'active', 'public', '{"ar": "58", "characters": "Hu Tao, Zhongli, Raiden, Kazuha", "primogems": "15000+", "welkin": "Активен"}', 'Мгновенно', true),

(4, 'farm', 'dota2', 'Фарм валюты и предметов Dota 2', 'Профессиональный фарм внутриигровой валюты и редких предметов в Dota 2. Возможность фарма конкретных сетов или случайных предметов по вашему выбору.', 'Фарм валюты и предметов Dota 2', 1200.00, 'RUB', 'active', 'public', '{"items": "Сеты, имморталы, аркана", "duration": "1-7 дней", "method": "Ручной фарм", "safety": "100% безопасно"}', '1-7 дней', false);

-- Тестовые отзывы
INSERT INTO reviews (purchase_id, product_id, reviewer_id, seller_id, rating, comment, pros, cons, status) VALUES
(1, 1, 3, 2, 5, 'Отличный аккаунт! Все как в описании, продавец честный и отзывчивый. Рекомендую!', 'Быстрая доставка, качественный аккаунт, отличная поддержка', 'Не нашел недостатков', 'published'),
(2, 2, 3, 2, 4, 'Буст прошел хорошо, но заняло немного больше времени чем обещали.', 'Профессиональная работа, достигли нужного ранга', 'Немного дольше обещанного срока', 'published');

-- Тестовые купоны
INSERT INTO coupons (code, type, value, min_purchase_amount, usage_limit, is_active, expires_at) VALUES
('WELCOME10', 'percentage', 10.00, 500.00, 100, true, DATE_ADD(NOW(), INTERVAL 30 DAY)),
('NEWUSER500', 'fixed', 500.00, 2000.00, 50, true, DATE_ADD(NOW(), INTERVAL 60 DAY)),
('VIP20', 'percentage', 20.00, 1000.00, 20, true, DATE_ADD(NOW(), INTERVAL 7 DAY));

-- Обновляем счетчики
UPDATE products SET favorites_count = (SELECT COUNT(*) FROM favorites WHERE product_id = products.id);
UPDATE users SET total_sales = (SELECT COUNT(*) FROM purchases WHERE seller_id = users.id AND status = 'completed');
UPDATE users SET total_purchases = (SELECT COUNT(*) FROM purchases WHERE buyer_id = users.id AND status = 'completed');