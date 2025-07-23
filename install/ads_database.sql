-- Розширена база даних для дошки оголошень та рекламної компанії
-- AdBoard Pro v2.0

-- Таблиця категорій оголошень
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(50),
    parent_id INT NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    meta_title VARCHAR(255),
    meta_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE CASCADE,
    INDEX idx_parent (parent_id),
    INDEX idx_slug (slug),
    INDEX idx_active (is_active)
);

-- Таблиця міст/локацій
CREATE TABLE IF NOT EXISTS locations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    region VARCHAR(100),
    country VARCHAR(100) DEFAULT 'Ukraine',
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_region (region),
    INDEX idx_active (is_active)
);

-- Таблиця оголошень
CREATE TABLE IF NOT EXISTS ads (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    location_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NOT NULL,
    price DECIMAL(12, 2),
    currency VARCHAR(3) DEFAULT 'UAH',
    contact_name VARCHAR(100),
    contact_phone VARCHAR(20),
    contact_email VARCHAR(100),
    address TEXT,
    condition_type ENUM('new', 'used', 'refurbished') DEFAULT 'used',
    status ENUM('draft', 'pending', 'active', 'sold', 'expired', 'rejected', 'archived') DEFAULT 'pending',
    moderation_comment TEXT,
    views_count INT DEFAULT 0,
    favorites_count INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    featured_until DATETIME NULL,
    is_urgent BOOLEAN DEFAULT FALSE,
    urgent_until DATETIME NULL,
    auto_republish BOOLEAN DEFAULT FALSE,
    expires_at DATETIME,
    published_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE RESTRICT,
    INDEX idx_user (user_id),
    INDEX idx_category (category_id),
    INDEX idx_location (location_id),
    INDEX idx_status (status),
    INDEX idx_featured (is_featured),
    INDEX idx_urgent (is_urgent),
    INDEX idx_published (published_at),
    INDEX idx_expires (expires_at),
    FULLTEXT idx_search (title, description)
);

-- Таблиця зображень оголошень
CREATE TABLE IF NOT EXISTS ad_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ad_id INT NOT NULL,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255),
    file_size INT,
    mime_type VARCHAR(100),
    is_main BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ad_id) REFERENCES ads(id) ON DELETE CASCADE,
    INDEX idx_ad (ad_id),
    INDEX idx_main (is_main)
);

-- Таблиця атрибутів категорій (додаткові поля)
CREATE TABLE IF NOT EXISTS category_attributes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    type ENUM('text', 'number', 'select', 'checkbox', 'textarea') NOT NULL,
    options JSON NULL, -- для select та checkbox
    is_required BOOLEAN DEFAULT FALSE,
    is_searchable BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    INDEX idx_category (category_id)
);

-- Таблиця значень атрибутів оголошень
CREATE TABLE IF NOT EXISTS ad_attributes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ad_id INT NOT NULL,
    attribute_id INT NOT NULL,
    value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ad_id) REFERENCES ads(id) ON DELETE CASCADE,
    FOREIGN KEY (attribute_id) REFERENCES category_attributes(id) ON DELETE CASCADE,
    INDEX idx_ad (ad_id),
    INDEX idx_attribute (attribute_id),
    INDEX idx_value (value(100))
);

-- Таблиця улюблених оголошень
CREATE TABLE IF NOT EXISTS favorites (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    ad_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (ad_id) REFERENCES ads(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorite (user_id, ad_id),
    INDEX idx_user (user_id),
    INDEX idx_ad (ad_id)
);

-- Таблиця переглядів оголошень
CREATE TABLE IF NOT EXISTS ad_views (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ad_id INT NOT NULL,
    user_id INT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ad_id) REFERENCES ads(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_ad (ad_id),
    INDEX idx_user (user_id),
    INDEX idx_ip (ip_address),
    INDEX idx_date (created_at)
);

-- Таблиця чатів між користувачами
CREATE TABLE IF NOT EXISTS chats (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ad_id INT NOT NULL,
    buyer_id INT NOT NULL,
    seller_id INT NOT NULL,
    last_message_id INT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ad_id) REFERENCES ads(id) ON DELETE CASCADE,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_chat (ad_id, buyer_id, seller_id),
    INDEX idx_ad (ad_id),
    INDEX idx_buyer (buyer_id),
    INDEX idx_seller (seller_id)
);

-- Таблиця повідомлень в чатах
CREATE TABLE IF NOT EXISTS chat_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    chat_id INT NOT NULL,
    sender_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    message_type ENUM('text', 'image', 'file', 'system') DEFAULT 'text',
    attachment_url VARCHAR(500) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (chat_id) REFERENCES chats(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_chat (chat_id),
    INDEX idx_sender (sender_id),
    INDEX idx_read (is_read),
    INDEX idx_created (created_at)
);

-- Оновлення зовнішнього ключа для chats
ALTER TABLE chats ADD FOREIGN KEY (last_message_id) REFERENCES chat_messages(id) ON DELETE SET NULL;

-- Таблиця платних послуг
CREATE TABLE IF NOT EXISTS paid_services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    duration_days INT NOT NULL,
    service_type ENUM('featured', 'urgent', 'top', 'highlight', 'republish') NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_type (service_type),
    INDEX idx_active (is_active)
);

-- Таблиця покупок платних послуг
CREATE TABLE IF NOT EXISTS service_purchases (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    ad_id INT NOT NULL,
    service_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(50),
    payment_id VARCHAR(100),
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    starts_at DATETIME,
    expires_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (ad_id) REFERENCES ads(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES paid_services(id) ON DELETE RESTRICT,
    INDEX idx_user (user_id),
    INDEX idx_ad (ad_id),
    INDEX idx_service (service_id),
    INDEX idx_status (status),
    INDEX idx_expires (expires_at)
);

-- Таблиця балансу користувачів
CREATE TABLE IF NOT EXISTS user_balance (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL UNIQUE,
    balance DECIMAL(10, 2) DEFAULT 0.00,
    total_spent DECIMAL(10, 2) DEFAULT 0.00,
    total_earned DECIMAL(10, 2) DEFAULT 0.00,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Таблиця транзакцій
CREATE TABLE IF NOT EXISTS transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type ENUM('deposit', 'withdraw', 'purchase', 'refund', 'bonus') NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    balance_before DECIMAL(10, 2) NOT NULL,
    balance_after DECIMAL(10, 2) NOT NULL,
    description TEXT,
    reference_type VARCHAR(50), -- 'ad', 'service', 'manual'
    reference_id INT,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'completed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_type (type),
    INDEX idx_status (status),
    INDEX idx_reference (reference_type, reference_id),
    INDEX idx_created (created_at)
);

-- Таблиця пошукових запитів (для аналітики)
CREATE TABLE IF NOT EXISTS search_queries (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL,
    query TEXT NOT NULL,
    category_id INT NULL,
    location_id INT NULL,
    filters JSON,
    results_count INT DEFAULT 0,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_category (category_id),
    INDEX idx_location (location_id),
    INDEX idx_created (created_at),
    FULLTEXT idx_query (query)
);

-- Таблиця повідомлень про порушення
CREATE TABLE IF NOT EXISTS reports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    reporter_id INT NULL,
    ad_id INT NOT NULL,
    reason ENUM('spam', 'fraud', 'inappropriate', 'duplicate', 'wrong_category', 'other') NOT NULL,
    description TEXT,
    status ENUM('pending', 'reviewed', 'resolved', 'dismissed') DEFAULT 'pending',
    admin_comment TEXT,
    resolved_by INT NULL,
    resolved_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reporter_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (ad_id) REFERENCES ads(id) ON DELETE CASCADE,
    FOREIGN KEY (resolved_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_reporter (reporter_id),
    INDEX idx_ad (ad_id),
    INDEX idx_status (status),
    INDEX idx_reason (reason)
);

-- Таблиця збережених пошуків
CREATE TABLE IF NOT EXISTS saved_searches (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    query TEXT,
    category_id INT NULL,
    location_id INT NULL,
    filters JSON,
    email_notifications BOOLEAN DEFAULT FALSE,
    last_notified_at DATETIME NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (location_id) REFERENCES locations(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_category (category_id),
    INDEX idx_location (location_id),
    INDEX idx_active (is_active)
);

-- Вставка базових категорій
INSERT IGNORE INTO categories (name, slug, description, icon, sort_order) VALUES
('Нерухомість', 'real-estate', 'Квартири, будинки, комерційна нерухомість', 'fas fa-home', 1),
('Транспорт', 'transport', 'Автомобілі, мотоцикли, запчастини', 'fas fa-car', 2),
('Робота', 'jobs', 'Вакансії та резюме', 'fas fa-briefcase', 3),
('Послуги', 'services', 'Різноманітні послуги', 'fas fa-tools', 4),
('Електроніка', 'electronics', 'Телефони, комп\'ютери, техніка', 'fas fa-laptop', 5),
('Мода та одяг', 'fashion', 'Одяг, взуття, аксесуари', 'fas fa-tshirt', 6),
('Дитячі товари', 'kids', 'Товари для дітей', 'fas fa-baby', 7),
('Інше', 'other', 'Різне', 'fas fa-ellipsis-h', 8);

-- Підкатегорії для нерухомості
INSERT IGNORE INTO categories (name, slug, description, icon, parent_id, sort_order) VALUES
('Квартири', 'apartments', 'Продаж та оренда квартир', 'fas fa-building', 1, 1),
('Будинки', 'houses', 'Приватні будинки та котеджі', 'fas fa-house-user', 1, 2),
('Кімнати', 'rooms', 'Оренда кімнат', 'fas fa-bed', 1, 3),
('Комерційна', 'commercial', 'Офіси, магазини, склади', 'fas fa-store', 1, 4),
('Земельні ділянки', 'land', 'Продаж земельних ділянок', 'fas fa-map', 1, 5);

-- Підкатегорії для транспорту
INSERT IGNORE INTO categories (name, slug, description, icon, parent_id, sort_order) VALUES
('Легкові автомобілі', 'cars', 'Легкові автомобілі', 'fas fa-car', 2, 1),
('Вантажівки', 'trucks', 'Вантажний транспорт', 'fas fa-truck', 2, 2),
('Мотоцикли', 'motorcycles', 'Мотоцикли та скутери', 'fas fa-motorcycle', 2, 3),
('Запчастини', 'auto-parts', 'Автозапчастини', 'fas fa-cog', 2, 4),
('Водний транспорт', 'boats', 'Човни, яхти, катери', 'fas fa-ship', 2, 5);

-- Вставка основних міст України
INSERT IGNORE INTO locations (name, slug, region, latitude, longitude, sort_order) VALUES
('Київ', 'kyiv', 'Київська область', 50.4501, 30.5234, 1),
('Харків', 'kharkiv', 'Харківська область', 49.9935, 36.2304, 2),
('Одеса', 'odesa', 'Одеська область', 46.4825, 30.7233, 3),
('Дніпро', 'dnipro', 'Дніпропетровська область', 48.4647, 35.0462, 4),
('Донецьк', 'donetsk', 'Донецька область', 48.0159, 37.8031, 5),
('Запоріжжя', 'zaporizhzhia', 'Запорізька область', 47.8388, 35.1396, 6),
('Львів', 'lviv', 'Львівська область', 49.8397, 24.0297, 7),
('Кривий Ріг', 'kryvyi-rih', 'Дніпропетровська область', 47.9077, 33.3820, 8);

-- Вставка базових платних послуг
INSERT IGNORE INTO paid_services (name, description, price, duration_days, service_type) VALUES
('Виділити оголошення', 'Ваше оголошення буде виділено кольором', 50.00, 7, 'highlight'),
('Закріпити зверху', 'Оголошення з\'явиться в топі списку', 100.00, 3, 'top'),
('Термінове оголошення', 'Позначка "Термінове" привертає увагу', 30.00, 3, 'urgent'),
('Рекомендоване', 'Показ в блоці рекомендованих', 150.00, 7, 'featured'),
('Повторна публікація', 'Автоматичне оновлення дати', 25.00, 30, 'republish');

-- Додаткові атрибути для категорії "Автомобілі"
INSERT IGNORE INTO category_attributes (category_id, name, type, options, is_required, is_searchable, sort_order) VALUES
((SELECT id FROM categories WHERE slug = 'cars'), 'Марка', 'select', '["BMW", "Mercedes-Benz", "Toyota", "Volkswagen", "Audi", "Ford", "Nissan", "Hyundai", "Kia", "Renault", "Opel", "Skoda", "Mazda", "Honda", "Mitsubishi", "Chevrolet", "Peugeot", "Citroën", "Fiat", "Daewoo", "Інше"]', true, true, 1),
((SELECT id FROM categories WHERE slug = 'cars'), 'Рік випуску', 'number', NULL, true, true, 2),
((SELECT id FROM categories WHERE slug = 'cars'), 'Пробіг (км)', 'number', NULL, false, true, 3),
((SELECT id FROM categories WHERE slug = 'cars'), 'Тип палива', 'select', '["Бензин", "Дизель", "Газ", "Гібрид", "Електро"]', false, true, 4),
((SELECT id FROM categories WHERE slug = 'cars'), 'Коробка передач', 'select', '["Механічна", "Автоматична", "Варіатор", "Робот"]', false, true, 5),
((SELECT id FROM categories WHERE slug = 'cars'), 'Тип кузова', 'select', '["Седан", "Хетчбек", "Універсал", "Купе", "Кабріолет", "Позашляховик", "Мінівен", "Пікап"]', false, true, 6),
((SELECT id FROM categories WHERE slug = 'cars'), 'Об\'єм двигуна (л)', 'number', NULL, false, true, 7),
((SELECT id FROM categories WHERE slug = 'cars'), 'Колір', 'select', '["Білий", "Чорний", "Сірий", "Срібний", "Червоний", "Синій", "Зелений", "Жовтий", "Коричневий", "Інший"]', false, false, 8);

-- Атрибути для категорії "Квартири"
INSERT IGNORE INTO category_attributes (category_id, name, type, options, is_required, is_searchable, sort_order) VALUES
((SELECT id FROM categories WHERE slug = 'apartments'), 'Кількість кімнат', 'select', '["1", "2", "3", "4", "5+"]', true, true, 1),
((SELECT id FROM categories WHERE slug = 'apartments'), 'Площа (м²)', 'number', NULL, false, true, 2),
((SELECT id FROM categories WHERE slug = 'apartments'), 'Поверх', 'number', NULL, false, true, 3),
((SELECT id FROM categories WHERE slug = 'apartments'), 'Поверховість будинку', 'number', NULL, false, true, 4),
((SELECT id FROM categories WHERE slug = 'apartments'), 'Тип будинку', 'select', '["Панельний", "Цегляний", "Монолітний", "Сталінка", "Хрущовка", "Новобудова"]', false, true, 5),
((SELECT id FROM categories WHERE slug = 'apartments'), 'Стан', 'select', '["Євроремонт", "Косметичний ремонт", "Після будівельників", "Потребує ремонту"]', false, true, 6),
((SELECT id FROM categories WHERE slug = 'apartments'), 'Меблі', 'checkbox', '["Є меблі", "Частково мебльована", "Без меблів"]', false, false, 7);

-- Створення індексів для оптимізації
CREATE INDEX idx_ads_search ON ads(category_id, location_id, status, price);
CREATE INDEX idx_ads_featured_active ON ads(is_featured, status, published_at);
CREATE INDEX idx_ads_urgent_active ON ads(is_urgent, status, published_at);
CREATE INDEX idx_messages_unread ON chat_messages(chat_id, is_read);
CREATE INDEX idx_transactions_user_date ON transactions(user_id, created_at);
CREATE INDEX idx_views_ad_date ON ad_views(ad_id, created_at);

-- Тригери для автоматичного управління
DELIMITER $$

-- Тригер для оновлення лічильника переглядів
CREATE TRIGGER update_ad_views_count 
AFTER INSERT ON ad_views 
FOR EACH ROW 
BEGIN 
    UPDATE ads SET views_count = views_count + 1 WHERE id = NEW.ad_id;
END$$

-- Тригер для оновлення лічільника улюблених
CREATE TRIGGER update_favorites_count_add 
AFTER INSERT ON favorites 
FOR EACH ROW 
BEGIN 
    UPDATE ads SET favorites_count = favorites_count + 1 WHERE id = NEW.ad_id;
END$$

CREATE TRIGGER update_favorites_count_remove 
AFTER DELETE ON favorites 
FOR EACH ROW 
BEGIN 
    UPDATE ads SET favorites_count = favorites_count - 1 WHERE id = OLD.ad_id;
END$$

-- Тригер для оновлення балансу при транзакціях
CREATE TRIGGER update_user_balance 
AFTER INSERT ON transactions 
FOR EACH ROW 
BEGIN 
    INSERT INTO user_balance (user_id, balance) 
    VALUES (NEW.user_id, NEW.balance_after) 
    ON DUPLICATE KEY UPDATE 
        balance = NEW.balance_after,
        total_spent = CASE WHEN NEW.type IN ('purchase', 'withdraw') THEN total_spent + NEW.amount ELSE total_spent END,
        total_earned = CASE WHEN NEW.type IN ('deposit', 'refund', 'bonus') THEN total_earned + NEW.amount ELSE total_earned END;
END$$

-- Тригер для оновлення last_message_id в чатах
CREATE TRIGGER update_chat_last_message 
AFTER INSERT ON chat_messages 
FOR EACH ROW 
BEGIN 
    UPDATE chats SET 
        last_message_id = NEW.id,
        updated_at = CURRENT_TIMESTAMP 
    WHERE id = NEW.chat_id;
END$$

DELIMITER ;

-- Створення користувача для бази даних (опціонально)
-- CREATE USER 'adboard_user'@'localhost' IDENTIFIED BY 'secure_password_here';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON adboard_db.* TO 'adboard_user'@'localhost';
-- FLUSH PRIVILEGES;