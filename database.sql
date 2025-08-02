-- Создание базы данных для Telegram бота гарантийных услуг
CREATE DATABASE IF NOT EXISTS escrow_bot CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE escrow_bot;

-- Таблица пользователей
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    telegram_id BIGINT UNIQUE NOT NULL,
    username VARCHAR(255),
    first_name VARCHAR(255),
    last_name VARCHAR(255),
    phone VARCHAR(20),
    email VARCHAR(255),
    balance DECIMAL(10,2) DEFAULT 0.00,
    rating DECIMAL(3,2) DEFAULT 0.00,
    deals_count INT DEFAULT 0,
    is_verified BOOLEAN DEFAULT FALSE,
    is_banned BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Таблица сделок
CREATE TABLE deals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    deal_number VARCHAR(20) UNIQUE NOT NULL,
    seller_id BIGINT NOT NULL,
    buyer_id BIGINT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    amount DECIMAL(10,2) NOT NULL,
    commission DECIMAL(10,2) NOT NULL,
    status ENUM('created', 'paid', 'confirmed', 'disputed', 'completed', 'cancelled') DEFAULT 'created',
    payment_method VARCHAR(50),
    payment_id VARCHAR(255),
    seller_confirmed BOOLEAN DEFAULT FALSE,
    buyer_confirmed BOOLEAN DEFAULT FALSE,
    dispute_reason TEXT,
    expires_at TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES users(telegram_id),
    FOREIGN KEY (buyer_id) REFERENCES users(telegram_id)
);

-- Таблица сообщений сделок
CREATE TABLE deal_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    deal_id INT NOT NULL,
    user_id BIGINT NOT NULL,
    message TEXT NOT NULL,
    is_system BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (deal_id) REFERENCES deals(id),
    FOREIGN KEY (user_id) REFERENCES users(telegram_id)
);

-- Таблица платежей
CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    deal_id INT NOT NULL,
    user_id BIGINT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    payment_id VARCHAR(255),
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (deal_id) REFERENCES deals(id),
    FOREIGN KEY (user_id) REFERENCES users(telegram_id)
);

-- Таблица отзывов
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    deal_id INT NOT NULL,
    reviewer_id BIGINT NOT NULL,
    reviewed_id BIGINT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (deal_id) REFERENCES deals(id),
    FOREIGN KEY (reviewer_id) REFERENCES users(telegram_id),
    FOREIGN KEY (reviewed_id) REFERENCES users(telegram_id)
);

-- Таблица настроек бота
CREATE TABLE bot_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Таблица логов админ-панели
CREATE TABLE admin_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id BIGINT NOT NULL,
    action VARCHAR(255) NOT NULL,
    target_type VARCHAR(50),
    target_id INT,
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Вставка базовых настроек
INSERT INTO bot_settings (setting_key, setting_value, description) VALUES
('commission_percent', '5', 'Комиссия сервиса в процентах'),
('min_deal_amount', '100', 'Минимальная сумма сделки'),
('max_deal_amount', '100000', 'Максимальная сумма сделки'),
('deal_timeout_hours', '72', 'Время на выполнение сделки в часах'),
('maintenance_mode', '0', 'Режим технического обслуживания (0/1)'),
('welcome_message', '🤝 Добро пожаловать в сервис гарантийных сделок!', 'Приветственное сообщение');

-- Индексы для оптимизации
CREATE INDEX idx_users_telegram_id ON users(telegram_id);
CREATE INDEX idx_deals_status ON deals(status);
CREATE INDEX idx_deals_seller ON deals(seller_id);
CREATE INDEX idx_deals_buyer ON deals(buyer_id);
CREATE INDEX idx_payments_status ON payments(status);
CREATE INDEX idx_deal_messages_deal_id ON deal_messages(deal_id);