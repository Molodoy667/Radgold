-- Додаткові таблиці для адмін функціоналу

-- Таблиця логування активності
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    data JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Таблиця повідомлень від адміністраторів користувачам
CREATE TABLE IF NOT EXISTS admin_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    admin_id INT NOT NULL,
    subject VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_admin_id (admin_id),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Таблиця для користувацьких рейтингів
CREATE TABLE IF NOT EXISTS user_ratings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    rater_id INT NOT NULL,
    rating TINYINT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_rater_id (rater_id),
    UNIQUE KEY unique_rating (user_id, rater_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (rater_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Додаємо поля для блокування користувачів до існуючої таблиці users
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS ban_reason TEXT,
ADD COLUMN IF NOT EXISTS ban_until DATETIME NULL,
ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Оптимізуємо індекси для існуючих таблиць
ALTER TABLE users ADD INDEX IF NOT EXISTS idx_status (status);
ALTER TABLE users ADD INDEX IF NOT EXISTS idx_role (role);
ALTER TABLE users ADD INDEX IF NOT EXISTS idx_last_login (last_login);

ALTER TABLE ads ADD INDEX IF NOT EXISTS idx_user_status (user_id, status);
ALTER TABLE ads ADD INDEX IF NOT EXISTS idx_category_status (category_id, status);
ALTER TABLE ads ADD INDEX IF NOT EXISTS idx_location_status (location_id, status);

-- Додаємо тригери для оновлення статистики

-- Тригер для оновлення last_login при авторизації
DROP TRIGGER IF EXISTS update_last_login;
DELIMITER $$
CREATE TRIGGER update_last_login 
AFTER UPDATE ON users
FOR EACH ROW
BEGIN
    IF NEW.status = 'active' AND OLD.status != 'active' THEN
        UPDATE users SET last_login = NOW() WHERE id = NEW.id;
    END IF;
END$$
DELIMITER ;

-- Видалення старої події якщо існує
DROP EVENT IF EXISTS auto_unban_users;

-- Тригер для автоматичного розблокування користувачів
DELIMITER $$
CREATE EVENT auto_unban_users
ON SCHEDULE EVERY 1 HOUR
DO
BEGIN
  UPDATE users 
  SET status = 'active', ban_reason = NULL, ban_until = NULL 
  WHERE status = 'banned' AND ban_until IS NOT NULL AND ban_until <= NOW();
END$$
DELIMITER ;

-- Вставляємо початкові дані для демонстрації
INSERT IGNORE INTO activity_logs (user_id, action, description, data, created_at) VALUES
(1, 'system_install', 'Система встановлена', '{"version": "2.1.1"}', NOW()),
(1, 'admin_login', 'Перший вхід адміністратора', '{"ip": "127.0.0.1"}', NOW());

-- Створюємо індекси для покращення продуктивності
CREATE INDEX IF NOT EXISTS idx_ads_search ON ads (title, description);
CREATE INDEX IF NOT EXISTS idx_ads_price ON ads (price);
CREATE INDEX IF NOT EXISTS idx_locations_active ON locations (is_active);
CREATE INDEX IF NOT EXISTS idx_categories_active ON categories (is_active);

-- Оновлюємо статистичні дані
UPDATE categories SET 
    meta_title = CONCAT(name, ' - Оголошення на AdBoard Pro'),
    meta_description = CONCAT('Дивіться всі оголошення в категорії ', name, ' на нашій дошці оголошень')
WHERE meta_title IS NULL;

UPDATE locations SET 
    latitude = CASE 
        WHEN name LIKE '%Київ%' THEN 50.4501
        WHEN name LIKE '%Харків%' THEN 49.9935
        WHEN name LIKE '%Одеса%' THEN 46.4825
        WHEN name LIKE '%Дніпро%' THEN 48.4647
        WHEN name LIKE '%Львів%' THEN 49.8397
        ELSE NULL
    END,
    longitude = CASE 
        WHEN name LIKE '%Київ%' THEN 30.5234
        WHEN name LIKE '%Харків%' THEN 36.2304
        WHEN name LIKE '%Одеса%' THEN 30.7233
        WHEN name LIKE '%Дніпро%' THEN 35.0462
        WHEN name LIKE '%Львів%' THEN 24.0297
        ELSE NULL
    END
WHERE latitude IS NULL;