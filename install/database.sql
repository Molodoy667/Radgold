-- Створення бази даних AdBoard Pro
CREATE DATABASE IF NOT EXISTS adboard_site CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE adboard_site;

-- Таблиця налаштувань сайту
CREATE TABLE IF NOT EXISTS site_settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    value TEXT,
    description VARCHAR(255),
    type ENUM('string', 'text', 'int', 'bool', 'json') DEFAULT 'string',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Вставляємо початкові налаштування
INSERT IGNORE INTO site_settings (setting_key, value, description, type) VALUES
('site_title', 'AdBoard Pro', 'Назва сайту', 'string'),
('site_description', 'Сучасна дошка оголошень та рекламна компанія', 'Опис сайту', 'text'),
('site_keywords', 'оголошення, купити, продати, послуги, реклама', 'Ключові слова', 'text'),
('site_author', 'AdBoard Pro Team', 'Автор сайту', 'string'),
('logo_url', 'images/default_logo.svg', 'URL логотипу', 'string'),
('favicon_url', 'images/favicon.svg', 'URL фавікону', 'string'),
('contact_email', '', 'Email для контактів', 'string'),
('contact_phone', '', 'Телефон для контактів', 'string'),
('contact_address', '', 'Адреса для контактів', 'text'),
('timezone', 'Europe/Kiev', 'Часовий пояс', 'string'),
('language', 'uk', 'Мова за замовчуванням', 'string'),
('available_languages', '["uk","ru","en"]', 'Доступні мови', 'json'),
('social_facebook', '', 'Facebook URL', 'string'),
('social_twitter', '', 'Twitter URL', 'string'),
('social_instagram', '', 'Instagram URL', 'string'),
('social_linkedin', '', 'LinkedIn URL', 'string'),
('social_youtube', '', 'YouTube URL', 'string'),
('analytics_code', '', 'Код аналітики', 'text'),
('meta_robots', 'index, follow', 'Meta robots', 'string'),
('current_theme', 'light', 'Поточна тема', 'string'),
('current_gradient', 'gradient-1', 'Поточний градієнт', 'string'),
('enable_animations', '1', 'Увімкнути анімації', 'bool'),
('enable_particles', '0', 'Частинки на фоні', 'bool'),
('smooth_scroll', '1', 'Плавна прокрутка', 'bool'),
('enable_tooltips', '1', 'Підказки', 'bool'),
('custom_css', '', 'Кастомний CSS', 'text'),
('custom_js', '', 'Кастомний JavaScript', 'text'),
('admin_email', '', 'Email адміністратора', 'string'),
('admin_name', '', 'Ім\'я адміністратора', 'string'),
('site_url', '', 'URL сайту', 'string'),
('max_ad_duration_days', '30', 'Максимальна тривалість оголошення (днів)', 'int'),
('ads_per_page', '12', 'Оголошень на сторінку', 'int'),
('auto_approve_ads', '0', 'Автоматичне схвалення оголошень', 'bool'),
('maintenance_mode', '0', 'Режим обслуговування', 'bool');



-- Таблиця користувачів
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    avatar VARCHAR(255) NULL DEFAULT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(20) NULL,
    bio TEXT NULL,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('user', 'partner', 'admin') DEFAULT 'user',
    role ENUM('user', 'admin', 'moderator', 'partner') DEFAULT 'user',
    status ENUM('active', 'inactive', 'banned', 'pending') DEFAULT 'active',
    email_verified BOOLEAN DEFAULT FALSE,
    newsletter BOOLEAN DEFAULT FALSE,
    google_id VARCHAR(100) NULL,
    email_verification_token VARCHAR(255),
    last_login TIMESTAMP NULL,
    login_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_user_type (user_type),
    INDEX idx_role (role),
    INDEX idx_status (status),
    INDEX idx_google_id (google_id)
);

-- Таблиця для токенів запам'ятовування
CREATE TABLE IF NOT EXISTS remember_tokens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_user_id (user_id),
    INDEX idx_expires (expires_at)
);

-- Таблиця для відновлення паролю
CREATE TABLE IF NOT EXISTS password_resets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_token (token),
    INDEX idx_expires (expires_at)
);

-- Таблиця категорій
CREATE TABLE IF NOT EXISTS categories (
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
CREATE TABLE IF NOT EXISTS ads (
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
CREATE TABLE IF NOT EXISTS pages (
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
CREATE TABLE IF NOT EXISTS menu_items (
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
CREATE TABLE IF NOT EXISTS messages (
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

-- Таблиця логів
CREATE TABLE IF NOT EXISTS activity_logs (
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
INSERT IGNORE INTO site_settings (
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
INSERT IGNORE INTO theme_settings (current_theme, current_gradient) VALUES ('light', 'gradient-1');

-- Створення адміністратора
INSERT IGNORE INTO users (
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
INSERT IGNORE INTO categories (name, slug, description, icon, sort_order) VALUES
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
INSERT IGNORE INTO categories (name, slug, description, icon, parent_id, sort_order) VALUES
('Квартири', 'apartments', 'Продаж та оренда квартир', 'fas fa-building', 1, 1),
('Будинки', 'houses', 'Продаж та оренда будинків', 'fas fa-home', 1, 2),
('Комерційна нерухомість', 'commercial', 'Офіси, магазини, склади', 'fas fa-store', 1, 3),
('Земельні ділянки', 'land', 'Продаж земельних ділянок', 'fas fa-mountain', 1, 4);

-- Підкategorії для транспорту
INSERT IGNORE INTO categories (name, slug, description, icon, parent_id, sort_order) VALUES
('Легкові автомобілі', 'cars', 'Продаж легкових автомобілів', 'fas fa-car', 2, 1),
('Мотоцикли', 'motorcycles', 'Мотоцикли та скутери', 'fas fa-motorcycle', 2, 2),
('Вантажівки', 'trucks', 'Вантажний транспорт', 'fas fa-truck', 2, 3),
('Запчастини', 'auto-parts', 'Автозапчастини та аксесуари', 'fas fa-cog', 2, 4);

-- Базові сторінки
INSERT IGNORE INTO pages (title, slug, content, meta_title, meta_description, status) VALUES
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

-- Початкові дані для налаштувань сайту
INSERT IGNORE INTO site_settings (
    site_title, 
    site_description, 
    site_keywords, 
    site_author,
    logo_url,
    favicon_url,
    contact_email,
    timezone,
    language,
    meta_robots
) VALUES (
    'AdBoard Pro',
    'Рекламна компанія та дошка оголошень',
    'реклама, оголошення, дошка оголошень, маркетинг',
    'AdBoard Pro Team',
    'images/default_logo.svg',
    'images/favicon.svg',
    'info@adboardpro.com',
    'Europe/Kiev',
    'uk',
    'index, follow'
);

-- Початкові дані для налаштувань теми
INSERT IGNORE INTO theme_settings (
    current_theme,
    current_gradient,
    enable_animations,
    enable_particles,
    smooth_scroll,
    enable_tooltips
) VALUES (
    'light',
    'gradient-1',
    TRUE,
    FALSE,
    TRUE,
    TRUE
);

-- Таблиця системних оновлень
CREATE TABLE IF NOT EXISTS system_updates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    version VARCHAR(50) NOT NULL,
    description TEXT,
    file_path VARCHAR(500),
    file_size BIGINT DEFAULT 0,
    status ENUM('success', 'failed', 'pending') DEFAULT 'pending',
    install_log LONGTEXT,
    installed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    installed_by INT,
    INDEX idx_status (status),
    INDEX idx_installed_at (installed_at),
    FOREIGN KEY (installed_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Розширення таблиці site_settings для додаткових налаштувань
ALTER TABLE site_settings 
ADD COLUMN setting_key VARCHAR(100) UNIQUE,
ADD COLUMN value TEXT,
ADD INDEX idx_setting_key (setting_key);

-- Таблиця додаткової інформації про партнерів
CREATE TABLE IF NOT EXISTS partner_info (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    company_name VARCHAR(255) NOT NULL,
    website VARCHAR(500),
    business_type VARCHAR(100),
    annual_revenue DECIMAL(15,2),
    employees_count INT,
    description TEXT,
    verified BOOLEAN DEFAULT FALSE,
    verification_documents JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_verified (verified)
);

-- Додаткові таблиці для дошки оголошень

-- Таблиця категорій оголошень (розширена)
ALTER TABLE categories ADD COLUMN IF NOT EXISTS meta_title VARCHAR(255);
ALTER TABLE categories ADD COLUMN IF NOT EXISTS meta_description TEXT;

-- Таблиця міст/локацій для оголошень
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

-- Вставка міст України
INSERT IGNORE INTO locations (name, slug, region, latitude, longitude, sort_order) VALUES
('Київ', 'kyiv', 'Київська область', 50.4501, 30.5234, 1),
('Харків', 'kharkiv', 'Харківська область', 49.9935, 36.2304, 2),
('Одеса', 'odesa', 'Одеська область', 46.4825, 30.7233, 3),
('Дніпро', 'dnipro', 'Дніпропетровська область', 48.4647, 35.0462, 4),
('Львів', 'lviv', 'Львівська область', 49.8397, 24.0297, 5),
('Запоріжжя', 'zaporizhzhia', 'Запорізька область', 47.8388, 35.1396, 6);

-- Вставка платних послуг
INSERT IGNORE INTO paid_services (name, description, price, duration_days, service_type) VALUES
('Виділити оголошення', 'Ваше оголошення буде виділено кольором', 50.00, 7, 'highlight'),
('Закріпити зверху', 'Оголошення з\'явиться в топі списку', 100.00, 3, 'top'),
('Термінове оголошення', 'Позначка "Термінове" привертає увагу', 30.00, 3, 'urgent'),
('Рекомендоване', 'Показ в блоці рекомендованих', 150.00, 7, 'featured');

-- Створення директорій для завантажень
-- Це буде зроблено через PHP код

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

-- Додаємо поля для блокування користувачів та балансу
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS ban_reason TEXT,
ADD COLUMN IF NOT EXISTS ban_until DATETIME NULL,
ADD COLUMN IF NOT EXISTS balance DECIMAL(10,2) DEFAULT 0.00,
ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;

-- Таблиця транзакцій
CREATE TABLE IF NOT EXISTS transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type ENUM('income', 'expense') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    description TEXT,
    payment_method VARCHAR(50),
    transaction_id VARCHAR(255),
    status ENUM('pending', 'completed', 'failed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_type (type),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Таблиця чатів
CREATE TABLE IF NOT EXISTS chats (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ad_id INT NOT NULL,
    buyer_id INT NOT NULL,
    seller_id INT NOT NULL,
    status ENUM('active', 'archived', 'blocked') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_ad_id (ad_id),
    INDEX idx_buyer_id (buyer_id),
    INDEX idx_seller_id (seller_id),
    INDEX idx_updated_at (updated_at),
    UNIQUE KEY unique_chat (ad_id, buyer_id, seller_id),
    FOREIGN KEY (ad_id) REFERENCES ads(id) ON DELETE CASCADE,
    FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Таблиця повідомлень чату
CREATE TABLE IF NOT EXISTS chat_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    chat_id INT NOT NULL,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    ad_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    message_type ENUM('text', 'image', 'file') DEFAULT 'text',
    attachment_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_chat_id (chat_id),
    INDEX idx_sender_id (sender_id),
    INDEX idx_receiver_id (receiver_id),
    INDEX idx_ad_id (ad_id),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (chat_id) REFERENCES chats(id) ON DELETE CASCADE,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (ad_id) REFERENCES ads(id) ON DELETE CASCADE
);

-- Таблиця блокування користувачів
CREATE TABLE IF NOT EXISTS user_blocks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    blocked_user_id INT NOT NULL,
    reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_blocked_user_id (blocked_user_id),
    UNIQUE KEY unique_block (user_id, blocked_user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (blocked_user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Таблиця заявок на консультації
CREATE TABLE IF NOT EXISTS consultation_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    service_type ENUM('smm', 'seo', 'web', 'design', 'complex') NOT NULL,
    message TEXT,
    status ENUM('new', 'processing', 'completed', 'cancelled') DEFAULT 'new',
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_service_type (service_type),
    INDEX idx_created_at (created_at)
);

-- Таблиця push сповіщень
CREATE TABLE IF NOT EXISTS notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    action_url VARCHAR(500),
    priority ENUM('low', 'normal', 'high') DEFAULT 'normal',
    icon VARCHAR(50),
    data JSON,
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_type (type),
    INDEX idx_is_read (is_read),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Таблиця підписок на push сповіщення
CREATE TABLE IF NOT EXISTS push_subscriptions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    endpoint TEXT NOT NULL,
    p256dh VARCHAR(255) NOT NULL,
    auth VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Таблиця денної статистики
CREATE TABLE IF NOT EXISTS daily_stats (
    id INT PRIMARY KEY AUTO_INCREMENT,
    date DATE NOT NULL UNIQUE,
    total_views INT DEFAULT 0,
    unique_visitors INT DEFAULT 0,
    ads_created INT DEFAULT 0,
    ads_activated INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_date (date)
);

-- Оптимізуємо індекси для існуючих таблиць
ALTER TABLE users ADD INDEX IF NOT EXISTS idx_status (status);
ALTER TABLE users ADD INDEX IF NOT EXISTS idx_role (role);
ALTER TABLE users ADD INDEX IF NOT EXISTS idx_last_login (last_login);

-- Завершення
SELECT 'База даних AdBoard Pro успішно створена з усіма таблицями!' as message;
