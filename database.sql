-- Створення бази даних для дошки оголошень
CREATE DATABASE IF NOT EXISTS classifieds_board CHARACTER SET utf8 COLLATE utf8_general_ci;
USE classifieds_board;

-- Таблиця користувачів
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1
);

-- Таблиця категорій
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Таблиця оголошень
CREATE TABLE ads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2),
    location VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    status ENUM('active', 'sold', 'expired', 'blocked') DEFAULT 'active',
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    expires_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    INDEX idx_category (category_id),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
);

-- Таблиця зображень оголошень
CREATE TABLE ad_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ad_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    is_main TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ad_id) REFERENCES ads(id) ON DELETE CASCADE
);

-- Таблиця вподобань
CREATE TABLE favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    ad_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (ad_id) REFERENCES ads(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorite (user_id, ad_id)
);

-- Вставка категорій
INSERT INTO categories (id, name, description, icon) VALUES
(1, 'Транспорт', 'Автомобілі, мотоцикли, велосипеди', 'fas fa-car'),
(2, 'Нерухомість', 'Квартири, будинки, земельні ділянки', 'fas fa-home'),
(3, 'Робота', 'Вакансії та резюме', 'fas fa-briefcase'),
(4, 'Послуги', 'Різноманітні послуги', 'fas fa-tools'),
(5, 'Для дому та саду', 'Меблі, техніка, садові товари', 'fas fa-couch'),
(6, 'Електроніка', 'Телефони, комп\'ютери, техніка', 'fas fa-laptop'),
(7, 'Мода і стиль', 'Одяг, взуття, аксесуари', 'fas fa-tshirt'),
(8, 'Хобі, відпочинок і спорт', 'Спортивні товари, музичні інструменти', 'fas fa-futbol'),
(9, 'Віддам безкоштовно', 'Безкоштовні речі', 'fas fa-gift'),
(10, 'Бізнес та послуги', 'Готовий бізнес, обладнання', 'fas fa-handshake');

-- Створення тестового користувача
INSERT INTO users (username, email, password, phone) VALUES
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+380501234567');