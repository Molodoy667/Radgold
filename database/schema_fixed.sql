-- ====================================
-- GAMEMARKET PRO - ИСПРАВЛЕННАЯ СХЕМА БД
-- Современный маркетплейс для игрового контента
-- Кодировка: UTF-8
-- ====================================

-- Отключаем проверку внешних ключей
SET FOREIGN_KEY_CHECKS = 0;

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
    
    INDEX idx_purchases_buyer (buyer_id),
    INDEX idx_purchases_seller (seller_id),
    INDEX idx_purchases_product (product_id),
    INDEX idx_purchases_status (status),
    INDEX idx_purchases_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- ТАБЛИЦА ОТЗЫВОВ
-- ====================================
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_id INT DEFAULT NULL,
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
    
    INDEX idx_reviews_purchase (purchase_id),
    INDEX idx_reviews_product (product_id),
    INDEX idx_reviews_reviewer (reviewer_id),
    INDEX idx_reviews_seller (seller_id),
    INDEX idx_reviews_rating (rating),
    INDEX idx_reviews_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- ТАБЛИЦА ИЗБРАННЫХ ТОВАРОВ
-- ====================================
CREATE TABLE favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_favorites_user (user_id),
    INDEX idx_favorites_product (product_id),
    UNIQUE KEY unique_favorite (user_id, product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- ТАБЛИЦА СООБЩЕНИЙ
-- ====================================
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    recipient_id INT NOT NULL,
    product_id INT DEFAULT NULL,
    purchase_id INT DEFAULT NULL,
    subject VARCHAR(255) DEFAULT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    read_at DATETIME DEFAULT NULL,
    parent_id INT DEFAULT NULL,
    thread_id VARCHAR(100) DEFAULT NULL,
    message_type ENUM('text', 'system', 'media', 'file') DEFAULT 'text',
    attachments JSON DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
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
    purchase_id INT DEFAULT NULL,
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
    
    INDEX idx_disputes_purchase (purchase_id),
    INDEX idx_disputes_initiator (initiator_id),
    INDEX idx_disputes_moderator (moderator_id),
    INDEX idx_disputes_status (status),
    INDEX idx_disputes_created (created_at)
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
    
    INDEX idx_notifications_user (user_id),
    INDEX idx_notifications_type (type),
    INDEX idx_notifications_unread (user_id, is_read),
    INDEX idx_notifications_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- ТАБЛИЦА ТРАНЗАКЦИЙ
-- ====================================
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    purchase_id INT DEFAULT NULL,
    type ENUM('deposit', 'withdrawal', 'purchase', 'sale', 'commission', 'refund', 'bonus') NOT NULL,
    amount DECIMAL(12,2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'RUB',
    status ENUM('pending', 'completed', 'failed', 'cancelled') DEFAULT 'pending',
    payment_method VARCHAR(50) DEFAULT NULL,
    external_id VARCHAR(255) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    metadata JSON DEFAULT NULL,
    processed_at DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_transactions_user (user_id),
    INDEX idx_transactions_purchase (purchase_id),
    INDEX idx_transactions_type (type),
    INDEX idx_transactions_status (status),
    INDEX idx_transactions_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- ТАБЛИЦА НАСТРОЕК СИСТЕМЫ
-- ====================================
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_type ENUM('string', 'integer', 'boolean', 'json', 'text') DEFAULT 'string',
    description VARCHAR(500) DEFAULT NULL,
    is_public BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_settings_key (setting_key),
    INDEX idx_settings_public (is_public)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================
-- ДОБАВЛЯЕМ ВНЕШНИЕ КЛЮЧИ
-- ====================================

-- Продукты
ALTER TABLE products 
ADD CONSTRAINT fk_products_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

-- Покупки
ALTER TABLE purchases 
ADD CONSTRAINT fk_purchases_buyer FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_purchases_seller FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_purchases_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE;

-- Отзывы
ALTER TABLE reviews 
ADD CONSTRAINT fk_reviews_purchase FOREIGN KEY (purchase_id) REFERENCES purchases(id) ON DELETE SET NULL,
ADD CONSTRAINT fk_reviews_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_reviews_reviewer FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_reviews_seller FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE;

-- Избранное
ALTER TABLE favorites 
ADD CONSTRAINT fk_favorites_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_favorites_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE;

-- Сообщения
ALTER TABLE messages 
ADD CONSTRAINT fk_messages_sender FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_messages_recipient FOREIGN KEY (recipient_id) REFERENCES users(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_messages_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL,
ADD CONSTRAINT fk_messages_purchase FOREIGN KEY (purchase_id) REFERENCES purchases(id) ON DELETE SET NULL;

-- Споры
ALTER TABLE disputes 
ADD CONSTRAINT fk_disputes_purchase FOREIGN KEY (purchase_id) REFERENCES purchases(id) ON DELETE SET NULL,
ADD CONSTRAINT fk_disputes_initiator FOREIGN KEY (initiator_id) REFERENCES users(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_disputes_respondent FOREIGN KEY (respondent_id) REFERENCES users(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_disputes_moderator FOREIGN KEY (moderator_id) REFERENCES users(id) ON DELETE SET NULL;

-- Уведомления
ALTER TABLE notifications 
ADD CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

-- Транзакции
ALTER TABLE transactions 
ADD CONSTRAINT fk_transactions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
ADD CONSTRAINT fk_transactions_purchase FOREIGN KEY (purchase_id) REFERENCES purchases(id) ON DELETE SET NULL;

-- ====================================
-- ВСТАВЛЯЕМ НАЧАЛЬНЫЕ ДАННЫЕ
-- ====================================

-- Настройки системы
INSERT INTO settings (setting_key, setting_value, setting_type, description, is_public) VALUES
('site_name', 'GameMarket Pro', 'string', 'Название сайта', true),
('site_description', 'Современный маркетплейс для игрового контента', 'string', 'Описание сайта', true),
('commission_rate', '0.05', 'string', 'Комиссия сайта (5%)', false),
('min_payout', '100', 'string', 'Минимальная сумма вывода', true),
('currency_default', 'RUB', 'string', 'Валюта по умолчанию', true),
('registration_enabled', '1', 'boolean', 'Разрешена ли регистрация', true),
('maintenance_mode', '0', 'boolean', 'Режим обслуживания', true);

-- Включаем проверку внешних ключей
SET FOREIGN_KEY_CHECKS = 1;

-- Завершено
SELECT 'Схема базы данных GameMarket Pro успешно создана!' as message;