-- Створення бази даних AdBoard Pro
CREATE DATABASE IF NOT EXISTS adboard_site CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE adboard_site;

-- Таблиця налаштувань сайту
CREATE TABLE site_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    site_title VARCHAR(255) DEFAULT 'AdBoard Pro',
    site_description TEXT,
    site_keywords TEXT,
    site_author VARCHAR(255) DEFAULT 'AdBoard Pro Team',
    logo VARCHAR(255) DEFAULT 'images/logo.png',
    favicon VARCHAR(255) DEFAULT 'images/favicon.ico',
    contact_email VARCHAR(255),
    contact_phone VARCHAR(50),
    contact_address TEXT,
    social_facebook VARCHAR(255),
    social_twitter VARCHAR(255),
    social_instagram VARCHAR(255),
    social_linkedin VARCHAR(255),
    social_youtube VARCHAR(255),
    analytics_code TEXT,
    meta_robots VARCHAR(50) DEFAULT 'index, follow',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Таблиця налаштувань теми
CREATE TABLE theme_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    current_theme ENUM('light', 'dark') DEFAULT 'light',
    current_gradient VARCHAR(50) DEFAULT 'gradient-1',
    custom_css TEXT,
    custom_js TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Таблиця користувачів
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    phone VARCHAR(20),
    avatar VARCHAR(255),
    role ENUM('user', 'admin', 'moderator') DEFAULT 'user',
    status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
    email_verified BOOLEAN DEFAULT FALSE,
    email_verification_token VARCHAR(255),
    reset_password_token VARCHAR(255),
    reset_password_expires DATETIME,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_role (role),
    INDEX idx_status (status)
);

-- Таблиця категорій
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    parent_id INT NULL,
    sort_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_parent (parent_id),
    INDEX idx_status (status)
);

-- Таблиця оголошень
CREATE TABLE ads (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10, 2),
    currency VARCHAR(3) DEFAULT 'UAH',
    images JSON,
    location VARCHAR(255),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    contact_name VARCHAR(100),
    contact_phone VARCHAR(20),
    contact_email VARCHAR(255),
    is_featured BOOLEAN DEFAULT FALSE,
    is_urgent BOOLEAN DEFAULT FALSE,
    status ENUM('draft', 'active', 'inactive', 'expired', 'sold') DEFAULT 'draft',
    views_count INT DEFAULT 0,
    expires_at DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    INDEX idx_user (user_id),
    INDEX idx_category (category_id),
    INDEX idx_status (status),
    INDEX idx_location (location),
    INDEX idx_created (created_at),
    INDEX idx_featured (is_featured),
    INDEX idx_urgent (is_urgent),
    FULLTEXT idx_search (title, description)
);

-- Таблиця сторінок
CREATE TABLE pages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content LONGTEXT NOT NULL,
    meta_title VARCHAR(255),
    meta_description TEXT,
    meta_keywords TEXT,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    FULLTEXT idx_content (title, content)
);

-- Таблиця меню
CREATE TABLE menu_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    icon VARCHAR(50),
    parent_id INT NULL,
    sort_order INT DEFAULT 0,
    target VARCHAR(10) DEFAULT '_self',
    status ENUM('active', 'inactive') DEFAULT 'active',
    menu_location ENUM('header', 'footer', 'sidebar') DEFAULT 'header',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES menu_items(id) ON DELETE CASCADE,
    INDEX idx_parent (parent_id),
    INDEX idx_location (menu_location),
    INDEX idx_status (status)
);

-- Таблиця повідомлень
CREATE TABLE messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    from_user_id INT NOT NULL,
    to_user_id INT NOT NULL,
    ad_id INT NULL,
    subject VARCHAR(255),
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (from_user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (to_user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (ad_id) REFERENCES ads(id) ON DELETE SET NULL,
    INDEX idx_from_user (from_user_id),
    INDEX idx_to_user (to_user_id),
    INDEX idx_ad (ad_id),
    INDEX idx_read (is_read)
);

-- Таблиця обраних оголошень
CREATE TABLE favorites (
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

-- Таблиця логів
CREATE TABLE activity_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_table (table_name),
    INDEX idx_created (created_at)
);

-- Вставка початкових даних

-- Налаштування сайту
INSERT INTO site_settings (
    site_title, 
    site_description, 
    site_keywords, 
    site_author,
    contact_email,
    contact_phone,
    contact_address
) VALUES (
    'AdBoard Pro - Рекламна компанія та дошка оголошень',
    'Професійна рекламна компанія та сучасна дошка оголошень. Ефективне просування бізнесу та пошук товарів і послуг.',
    'реклама, оголошення, дошка оголошень, маркетинг, просування, бізнес, товари, послуги',
    'AdBoard Pro Team',
    'info@adboardpro.com',
    '+380 (50) 123-45-67',
    'вул. Хрещатик, 1, Київ, Україна'
);

-- Налаштування теми
INSERT INTO theme_settings (current_theme, current_gradient) VALUES ('light', 'gradient-1');

-- Створення адміністратора
INSERT INTO users (
    username, 
    email, 
    password, 
    first_name, 
    last_name, 
    role, 
    status, 
    email_verified
) VALUES (
    'admin',
    'admin@adboardpro.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'Адміністратор',
    'Сайту',
    'admin',
    'active',
    TRUE
);

-- Основні категорії
INSERT INTO categories (name, slug, description, icon, sort_order) VALUES
('Нерухомість', 'real-estate', 'Продаж та оренда нерухомості', 'fas fa-home', 1),
('Транспорт', 'transport', 'Автомобілі, мотоцикли та інший транспорт', 'fas fa-car', 2),
('Електроніка', 'electronics', 'Комп\'ютери, телефони, побутова техніка', 'fas fa-laptop', 3),
('Мода та стиль', 'fashion', 'Одяг, взуття, аксесуари', 'fas fa-tshirt', 4),
('Дім і сад', 'home-garden', 'Меблі, декор, садівництво', 'fas fa-couch', 5),
('Робота', 'jobs', 'Вакансії та пошук роботи', 'fas fa-briefcase', 6),
('Послуги', 'services', 'Різноманітні послуги', 'fas fa-cogs', 7),
('Тварини', 'animals', 'Домашні тварини та товари для них', 'fas fa-paw', 8),
('Хобі та відпочинок', 'hobby', 'Спорт, музика, книги, ігри', 'fas fa-gamepad', 9),
('Інше', 'other', 'Різні товари та послуги', 'fas fa-ellipsis-h', 10);

-- Підкategorії для нерухомості
INSERT INTO categories (name, slug, description, icon, parent_id, sort_order) VALUES
('Квартири', 'apartments', 'Продаж та оренда квартир', 'fas fa-building', 1, 1),
('Будинки', 'houses', 'Продаж та оренда будинків', 'fas fa-home', 1, 2),
('Комерційна нерухомість', 'commercial', 'Офіси, магазини, склади', 'fas fa-store', 1, 3),
('Земельні ділянки', 'land', 'Продаж земельних ділянок', 'fas fa-mountain', 1, 4);

-- Підкategorії для транспорту
INSERT INTO categories (name, slug, description, icon, parent_id, sort_order) VALUES
('Легкові автомобілі', 'cars', 'Продаж легкових автомобілів', 'fas fa-car', 2, 1),
('Мотоцикли', 'motorcycles', 'Мотоцикли та скутери', 'fas fa-motorcycle', 2, 2),
('Вантажівки', 'trucks', 'Вантажний транспорт', 'fas fa-truck', 2, 3),
('Запчастини', 'auto-parts', 'Автозапчастини та аксесуари', 'fas fa-cog', 2, 4);

-- Базові сторінки
INSERT INTO pages (title, slug, content, meta_title, meta_description, status) VALUES
('Про нас', 'about', 
'<h1>Про компанію AdBoard Pro</h1>
<p>AdBoard Pro - це інноваційна платформа, яка поєднує в собі функціональність рекламної компанії та сучасної дошки оголошень.</p>
<h2>Наша місія</h2>
<p>Ми прагнемо створити найкращу платформу для ефективного просування бізнесу та комфортного пошуку товарів і послуг.</p>',
'Про нас - AdBoard Pro',
'Дізнайтеся більше про нашу компанію, місію та цінності',
'published'),

('Контакти', 'contact',
'<h1>Контактна інформація</h1>
<div class="row">
<div class="col-md-6">
<h3>Наші контакти</h3>
<p><strong>Адреса:</strong> вул. Хрещатик, 1, Київ, Україна</p>
<p><strong>Телефон:</strong> +380 (50) 123-45-67</p>
<p><strong>Email:</strong> info@adboardpro.com</p>
</div>
<div class="col-md-6">
<h3>Режим роботи</h3>
<p>Пн-Пт: 9:00 - 18:00</p>
<p>Сб: 10:00 - 16:00</p>
<p>Нд: вихідний</p>
</div>
</div>',
'Контакти - AdBoard Pro',
'Зв\'яжіться з нами. Адреса, телефон, email та режим роботи',
'published'),

('Політика конфіденційності', 'privacy',
'<h1>Політика конфіденційності</h1>
<p>Ця політика конфіденційності описує, як ми збираємо, використовуємо та захищаємо вашу особисту інформацію.</p>
<h2>Збір інформації</h2>
<p>Ми збираємо інформацію, яку ви надаєте нам добровільно при реєстрації та використанні нашого сервісу.</p>',
'Політика конфіденційності - AdBoard Pro',
'Політика конфіденційності та захисту персональних даних',
'published');

-- Створення директорій для завантажень
-- Це буде зроблено через PHP код

-- Завершення
SELECT 'База даних AdBoard Pro успішно створена!' as message;
