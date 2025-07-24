-- Виправлена база даних AdBoard Pro (без зовнішніх ключів в CREATE TABLE)
-- Версія: 2.1.2
-- Дата: 2024

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- База даних має бути створена заздалегідь
-- Підключення відбувається через конфігурацію інсталятора

-- Таблиця налаштувань сайту
CREATE TABLE IF NOT EXISTS `site_settings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `setting_key` varchar(100) NOT NULL,
    `setting_value` text,
    `setting_type` enum('string','text','int','bool','json','email','url') DEFAULT 'string',
    `setting_group` varchar(50) DEFAULT 'general',
    `description` varchar(255),
    `is_public` boolean DEFAULT FALSE,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `setting_key` (`setting_key`),
    KEY `idx_group` (`setting_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Вставка базових налаштувань
INSERT IGNORE INTO `site_settings` (`setting_key`, `setting_value`, `setting_type`, `setting_group`, `description`, `is_public`) VALUES
-- Основні налаштування
('site_name', 'AdBoard Pro', 'string', 'general', 'Назва сайту', TRUE),
('site_description', 'Сучасна дошка оголошень та рекламна компанія', 'text', 'general', 'Опис сайту', TRUE),
('site_keywords', 'дошка оголошень, купівля, продаж, оренда, послуги', 'text', 'general', 'Ключові слова', TRUE),
('contact_email', 'info@adboard.local', 'email', 'general', 'Email для контактів', TRUE),
('contact_phone', '+380123456789', 'string', 'general', 'Телефон для контактів', TRUE),
('admin_email', 'admin@adboard.local', 'email', 'general', 'Email адміністратора', FALSE),
('default_language', 'uk', 'string', 'general', 'Мова за замовчуванням', TRUE),
('timezone', 'Europe/Kiev', 'string', 'general', 'Часовий пояс', FALSE),

-- Налаштування теми
('default_theme', 'modern', 'string', 'theme', 'Тема за замовчуванням', TRUE),
('enable_dark_mode', '1', 'bool', 'theme', 'Увімкнути темний режим', TRUE),
('primary_color', '#007bff', 'string', 'theme', 'Основний колір', TRUE),
('secondary_color', '#6c757d', 'string', 'theme', 'Вторинний колір', TRUE),
('enable_animations', '1', 'bool', 'theme', 'Увімкнути анімації', TRUE),
('enable_particles', '0', 'bool', 'theme', 'Увімкнути частинки', TRUE),
('enable_smooth_scroll', '1', 'bool', 'theme', 'Плавна прокрутка', TRUE),
('enable_tooltips', '1', 'bool', 'theme', 'Увімкнути підказки', TRUE),

-- Email налаштування  
('smtp_enabled', '0', 'bool', 'email', 'Увімкнути SMTP', FALSE),
('smtp_host', '', 'string', 'email', 'SMTP хост', FALSE),
('smtp_port', '587', 'int', 'email', 'SMTP порт', FALSE),
('smtp_username', '', 'string', 'email', 'SMTP користувач', FALSE),
('smtp_password', '', 'string', 'email', 'SMTP пароль', FALSE),
('smtp_encryption', 'tls', 'string', 'email', 'SMTP шифрування', FALSE),
('from_email', 'noreply@adboard.local', 'email', 'email', 'Відправник email', FALSE),
('from_name', 'AdBoard Pro', 'string', 'email', 'Ім\'я відправника', FALSE),

-- Платіжні системи
('payments_enabled', '0', 'bool', 'payments', 'Увімкнути платежі', FALSE),
('currency', 'UAH', 'string', 'payments', 'Валюта', TRUE),
('liqpay_enabled', '0', 'bool', 'payments', 'Увімкнути LiqPay', FALSE),
('liqpay_public_key', '', 'string', 'payments', 'LiqPay публічний ключ', FALSE),
('liqpay_private_key', '', 'string', 'payments', 'LiqPay приватний ключ', FALSE),
('fondy_enabled', '0', 'bool', 'payments', 'Увімкнути Fondy', FALSE),
('fondy_merchant_id', '', 'string', 'payments', 'Fondy ID мерчанта', FALSE),
('fondy_secret_key', '', 'string', 'payments', 'Fondy секретний ключ', FALSE),
('paypal_enabled', '0', 'bool', 'payments', 'Увімкнути PayPal', FALSE),
('paypal_client_id', '', 'string', 'payments', 'PayPal Client ID', FALSE),
('paypal_secret', '', 'string', 'payments', 'PayPal секрет', FALSE),

-- Безпека
('ssl_enabled', '0', 'bool', 'security', 'Увімкнути SSL', FALSE),
('force_https', '0', 'bool', 'security', 'Примусовий HTTPS', FALSE),
('session_lifetime', '1440', 'int', 'security', 'Час життя сесії (хв)', FALSE),
('password_min_length', '6', 'int', 'security', 'Мін. довжина паролю', FALSE),
('enable_2fa', '0', 'bool', 'security', 'Увімкнути 2FA', FALSE),
('max_login_attempts', '5', 'int', 'security', 'Макс. спроб входу', FALSE),
('lockout_duration', '15', 'int', 'security', 'Блокування (хв)', FALSE),

-- Соціальні мережі
('facebook_app_id', '', 'string', 'social', 'Facebook App ID', FALSE),
('google_client_id', '', 'string', 'social', 'Google Client ID', FALSE),
('google_analytics_id', '', 'string', 'social', 'Google Analytics ID', FALSE),
('facebook_pixel_id', '', 'string', 'social', 'Facebook Pixel ID', FALSE),

-- Системні налаштування
('debug_mode', '0', 'bool', 'system', 'Режим налагодження', FALSE),
('maintenance_mode', '0', 'bool', 'system', 'Режим обслуговування', FALSE),
('cache_enabled', '1', 'bool', 'system', 'Увімкнути кеш', FALSE),
('cache_lifetime', '3600', 'int', 'system', 'Час життя кешу (сек)', FALSE),
('log_level', 'error', 'string', 'system', 'Рівень логування', FALSE),
('backup_enabled', '1', 'bool', 'system', 'Увімкнути backup', FALSE),
('backup_frequency', 'daily', 'string', 'system', 'Частота backup', FALSE),
('backup_retention_days', '30', 'int', 'system', 'Зберігати backup (днів)', FALSE);

-- Таблиця груп користувачів
CREATE TABLE IF NOT EXISTS `user_groups` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `description` text DEFAULT NULL,
    `permissions` text DEFAULT NULL,
    `color` varchar(7) DEFAULT '#6c757d',
    `sort_order` int(11) DEFAULT 0,
    `is_default` boolean DEFAULT FALSE,
    `is_system` boolean DEFAULT FALSE,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `name` (`name`),
    KEY `idx_sort_order` (`sort_order`),
    KEY `idx_is_default` (`is_default`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця користувачів
CREATE TABLE IF NOT EXISTS `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(50) DEFAULT NULL,
    `first_name` varchar(100) NOT NULL,
    `last_name` varchar(100) NOT NULL,
    `email` varchar(255) NOT NULL,
    `phone` varchar(20) DEFAULT NULL,
    `avatar` varchar(255) DEFAULT NULL,
    `bio` text DEFAULT NULL,
    `password` varchar(255) NOT NULL,
    `role` enum('user','admin','moderator','partner') DEFAULT 'user',
    `user_type` enum('user','admin','moderator','partner') DEFAULT 'user',
    `group_id` int(11) DEFAULT NULL,
    `status` enum('active','inactive','banned','pending') DEFAULT 'active',
    `email_verified` boolean DEFAULT FALSE,
    `phone_verified` boolean DEFAULT FALSE,
    `newsletter` boolean DEFAULT TRUE,
    `google_id` varchar(100) DEFAULT NULL,
    `facebook_id` varchar(100) DEFAULT NULL,
    `email_verification_token` varchar(255) DEFAULT NULL,
    `phone_verification_code` varchar(10) DEFAULT NULL,
    `two_factor_enabled` boolean DEFAULT FALSE,
    `two_factor_secret` varchar(255) DEFAULT NULL,
    `language` varchar(5) DEFAULT 'uk',
    `timezone` varchar(50) DEFAULT 'Europe/Kiev',
    `last_login` timestamp NULL DEFAULT NULL,
    `last_activity` timestamp NULL DEFAULT NULL,
    `login_count` int(11) DEFAULT 0,
    `failed_login_attempts` int(11) DEFAULT 0,
    `blocked_until` timestamp NULL DEFAULT NULL,
    `balance` decimal(10,2) DEFAULT 0.00,
    `ban_reason` text DEFAULT NULL,
    `ban_until` datetime DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`),
    UNIQUE KEY `username` (`username`),
    KEY `idx_role` (`role`),
    KEY `idx_user_type` (`user_type`),
    KEY `idx_group_id` (`group_id`),
    KEY `idx_status` (`status`),
    KEY `idx_google_id` (`google_id`),
    KEY `idx_last_login` (`last_login`),
    KEY `idx_email_verified` (`email_verified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця remember tokens
CREATE TABLE IF NOT EXISTS `remember_tokens` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `token` varchar(255) NOT NULL,
    `expires_at` timestamp NOT NULL,
    `used` boolean DEFAULT FALSE,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `token` (`token`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця відновлення паролів
CREATE TABLE IF NOT EXISTS `password_resets` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(255) NOT NULL,
    `token` varchar(255) NOT NULL,
    `expires_at` timestamp NOT NULL,
    `used` boolean DEFAULT FALSE,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `token` (`token`),
    KEY `idx_email` (`email`),
    KEY `idx_expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця локацій
CREATE TABLE IF NOT EXISTS `locations` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `slug` varchar(100) NOT NULL,
    `region` varchar(100) DEFAULT NULL,
    `latitude` decimal(10,8) DEFAULT NULL,
    `longitude` decimal(11,8) DEFAULT NULL,
    `sort_order` int(11) DEFAULT 0,
    `status` enum('active','inactive') DEFAULT 'active',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`),
    KEY `idx_region` (`region`),
    KEY `idx_status` (`status`),
    KEY `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця категорій
CREATE TABLE IF NOT EXISTS `categories` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `slug` varchar(100) NOT NULL,
    `description` text DEFAULT NULL,
    `icon` varchar(50) DEFAULT NULL,
    `image` varchar(255) DEFAULT NULL,
    `parent_id` int(11) DEFAULT NULL,
    `sort_order` int(11) DEFAULT 0,
    `is_active` boolean DEFAULT TRUE,
    `meta_title` varchar(255) DEFAULT NULL,
    `meta_description` text DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`),
    KEY `idx_parent_id` (`parent_id`),
    KEY `idx_sort_order` (`sort_order`),
    KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця оголошень
CREATE TABLE IF NOT EXISTS `ads` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `category_id` int(11) NOT NULL,
    `location_id` int(11) NOT NULL,
    `title` varchar(255) NOT NULL,
    `description` text NOT NULL,
    `price` decimal(10,2) DEFAULT NULL,
    `currency` varchar(3) DEFAULT 'UAH',
    `condition_type` enum('new','used','not_specified') DEFAULT 'not_specified',
    `contact_phone` varchar(20) DEFAULT NULL,
    `contact_email` varchar(255) DEFAULT NULL,
    `address` varchar(255) DEFAULT NULL,
    `latitude` decimal(10,8) DEFAULT NULL,
    `longitude` decimal(11,8) DEFAULT NULL,
    `status` enum('active','pending','sold','expired','deleted') DEFAULT 'pending',
    `is_featured` boolean DEFAULT FALSE,
    `is_urgent` boolean DEFAULT FALSE,
    `is_top` boolean DEFAULT FALSE,
    `views_count` int(11) DEFAULT 0,
    `favorites_count` int(11) DEFAULT 0,
    `featured_until` datetime DEFAULT NULL,
    `urgent_until` datetime DEFAULT NULL,
    `top_until` datetime DEFAULT NULL,
    `expires_at` datetime DEFAULT NULL,
    `meta_title` varchar(255) DEFAULT NULL,
    `meta_description` text DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_category_id` (`category_id`),
    KEY `idx_location_id` (`location_id`),
    KEY `idx_status` (`status`),
    KEY `idx_is_featured` (`is_featured`),
    KEY `idx_created_at` (`created_at`),
    KEY `idx_price` (`price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця зображень оголошень
CREATE TABLE IF NOT EXISTS `ad_images` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ad_id` int(11) NOT NULL,
    `filename` varchar(255) NOT NULL,
    `original_name` varchar(255) NOT NULL,
    `file_size` int(11) DEFAULT NULL,
    `sort_order` int(11) DEFAULT 0,
    `is_primary` boolean DEFAULT FALSE,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_ad_id` (`ad_id`),
    KEY `idx_sort_order` (`sort_order`),
    KEY `idx_is_primary` (`is_primary`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця атрибутів категорій
CREATE TABLE IF NOT EXISTS `category_attributes` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `category_id` int(11) NOT NULL,
    `name` varchar(100) NOT NULL,
    `type` enum('text','number','select','multiselect','checkbox','radio','date') DEFAULT 'text',
    `options` text DEFAULT NULL,
    `is_required` boolean DEFAULT FALSE,
    `is_filterable` boolean DEFAULT TRUE,
    `sort_order` int(11) DEFAULT 0,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_category_id` (`category_id`),
    KEY `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця значень атрибутів оголошень
CREATE TABLE IF NOT EXISTS `ad_attributes` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ad_id` int(11) NOT NULL,
    `attribute_id` int(11) NOT NULL,
    `value` text DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_ad_id` (`ad_id`),
    KEY `idx_attribute_id` (`attribute_id`),
    UNIQUE KEY `ad_attribute` (`ad_id`, `attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця збережених оголошень
CREATE TABLE IF NOT EXISTS `favorites` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `ad_id` int(11) NOT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_ad_id` (`ad_id`),
    UNIQUE KEY `user_ad` (`user_id`, `ad_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця переглядів оголошень
CREATE TABLE IF NOT EXISTS `ad_views` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ad_id` int(11) NOT NULL,
    `user_id` int(11) DEFAULT NULL,
    `ip_address` varchar(45) NOT NULL,
    `user_agent` text DEFAULT NULL,
    `viewed_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_ad_id` (`ad_id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_ip_address` (`ip_address`),
    KEY `idx_viewed_at` (`viewed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця платних послуг
CREATE TABLE IF NOT EXISTS `paid_services` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `description` text DEFAULT NULL,
    `price` decimal(10,2) NOT NULL,
    `duration_days` int(11) NOT NULL,
    `service_type` enum('featured','top','urgent','highlight','boost','republish') NOT NULL,
    `is_active` boolean DEFAULT TRUE,
    `sort_order` int(11) DEFAULT 0,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_service_type` (`service_type`),
    KEY `idx_is_active` (`is_active`),
    KEY `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця покупок послуг
CREATE TABLE IF NOT EXISTS `service_purchases` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `ad_id` int(11) NOT NULL,
    `service_id` int(11) NOT NULL,
    `amount` decimal(10,2) NOT NULL,
    `currency` varchar(3) DEFAULT 'UAH',
    `payment_method` varchar(50) DEFAULT NULL,
    `payment_id` varchar(255) DEFAULT NULL,
    `status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
    `expires_at` datetime DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_ad_id` (`ad_id`),
    KEY `idx_service_id` (`service_id`),
    KEY `idx_status` (`status`),
    KEY `idx_expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця push-підписок
CREATE TABLE IF NOT EXISTS `push_subscriptions` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `endpoint` text NOT NULL,
    `p256dh_key` varchar(255) NOT NULL,
    `auth_key` varchar(255) NOT NULL,
    `user_agent` text DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    UNIQUE KEY `user_endpoint` (`user_id`, `endpoint`(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця транзакцій
CREATE TABLE IF NOT EXISTS `transactions` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `ad_id` int(11) DEFAULT NULL,
    `buyer_id` int(11) DEFAULT NULL,
    `seller_id` int(11) NOT NULL,
    `amount` decimal(10,2) NOT NULL,
    `currency` varchar(3) DEFAULT 'UAH',
    `type` enum('purchase','refund','fee','bonus') DEFAULT 'purchase',
    `status` enum('pending','completed','failed','cancelled') DEFAULT 'pending',
    `payment_method` varchar(50) DEFAULT NULL,
    `payment_id` varchar(255) DEFAULT NULL,
    `description` text DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_ad_id` (`ad_id`),
    KEY `idx_buyer_id` (`buyer_id`),
    KEY `idx_seller_id` (`seller_id`),
    KEY `idx_status` (`status`),
    KEY `idx_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця чатів
CREATE TABLE IF NOT EXISTS `chats` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ad_id` int(11) NOT NULL,
    `buyer_id` int(11) NOT NULL,
    `seller_id` int(11) NOT NULL,
    `last_message_id` int(11) DEFAULT NULL,
    `last_message_at` timestamp NULL DEFAULT NULL,
    `status` enum('active','archived','blocked') DEFAULT 'active',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_ad_id` (`ad_id`),
    KEY `idx_buyer_id` (`buyer_id`),
    KEY `idx_seller_id` (`seller_id`),
    KEY `idx_last_message_at` (`last_message_at`),
    UNIQUE KEY `chat_unique` (`ad_id`, `buyer_id`, `seller_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця повідомлень
CREATE TABLE IF NOT EXISTS `messages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `chat_id` int(11) NOT NULL,
    `sender_id` int(11) NOT NULL,
    `receiver_id` int(11) NOT NULL,
    `message` text NOT NULL,
    `type` enum('text','image','file','system') DEFAULT 'text',
    `attachment` varchar(255) DEFAULT NULL,
    `is_read` boolean DEFAULT FALSE,
    `read_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_chat_id` (`chat_id`),
    KEY `idx_sender_id` (`sender_id`),
    KEY `idx_receiver_id` (`receiver_id`),
    KEY `idx_created_at` (`created_at`),
    KEY `idx_is_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця сповіщень
CREATE TABLE IF NOT EXISTS `notifications` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `type` varchar(50) NOT NULL,
    `title` varchar(255) NOT NULL,
    `message` text NOT NULL,
    `data` text DEFAULT NULL,
    `priority` enum('low','normal','high') DEFAULT 'normal',
    `is_read` boolean DEFAULT FALSE,
    `read_at` timestamp NULL DEFAULT NULL,
    `expires_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_type` (`type`),
    KEY `idx_is_read` (`is_read`),
    KEY `idx_priority` (`priority`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця логів активності
CREATE TABLE IF NOT EXISTS `activity_logs` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) DEFAULT NULL,
    `action` varchar(100) NOT NULL,
    `description` text DEFAULT NULL,
    `meta_data` text DEFAULT NULL,
    `ip_address` varchar(45) DEFAULT NULL,
    `user_agent` text DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_action` (`action`),
    KEY `idx_ip_address` (`ip_address`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця адмін повідомлень
CREATE TABLE IF NOT EXISTS `admin_messages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `admin_id` int(11) NOT NULL,
    `subject` varchar(255) NOT NULL,
    `message` text NOT NULL,
    `type` enum('info','warning','success','danger') DEFAULT 'info',
    `is_read` boolean DEFAULT FALSE,
    `read_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_admin_id` (`admin_id`),
    KEY `idx_type` (`type`),
    KEY `idx_is_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця рейтингів користувачів
CREATE TABLE IF NOT EXISTS `user_ratings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `rater_id` int(11) NOT NULL,
    `rating` tinyint(1) NOT NULL,
    `comment` text DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_rater_id` (`rater_id`),
    UNIQUE KEY `user_rater` (`user_id`, `rater_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця блокувань користувачів
CREATE TABLE IF NOT EXISTS `user_blocks` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `blocked_user_id` int(11) NOT NULL,
    `reason` text DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_blocked_user_id` (`blocked_user_id`),
    UNIQUE KEY `user_blocked` (`user_id`, `blocked_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця скарг
CREATE TABLE IF NOT EXISTS `reports` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `reporter_id` int(11) DEFAULT NULL,
    `reported_user_id` int(11) DEFAULT NULL,
    `ad_id` int(11) DEFAULT NULL,
    `type` enum('spam','inappropriate','fake','other') NOT NULL,
    `reason` text NOT NULL,
    `status` enum('pending','reviewed','resolved','dismissed') DEFAULT 'pending',
    `admin_notes` text DEFAULT NULL,
    `resolved_by` int(11) DEFAULT NULL,
    `resolved_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_reporter_id` (`reporter_id`),
    KEY `idx_reported_user_id` (`reported_user_id`),
    KEY `idx_ad_id` (`ad_id`),
    KEY `idx_type` (`type`),
    KEY `idx_status` (`status`),
    KEY `idx_resolved_by` (`resolved_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця збережених пошуків
CREATE TABLE IF NOT EXISTS `saved_searches` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `name` varchar(100) NOT NULL,
    `search_params` text NOT NULL,
    `category_id` int(11) DEFAULT NULL,
    `location_id` int(11) DEFAULT NULL,
    `min_price` decimal(10,2) DEFAULT NULL,
    `max_price` decimal(10,2) DEFAULT NULL,
    `notifications_enabled` boolean DEFAULT TRUE,
    `last_checked` timestamp NULL DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_category_id` (`category_id`),
    KEY `idx_location_id` (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця щоденної статистики
CREATE TABLE IF NOT EXISTS `daily_stats` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `date` date NOT NULL,
    `new_users` int(11) DEFAULT 0,
    `new_ads` int(11) DEFAULT 0,
    `active_ads` int(11) DEFAULT 0,
    `sold_ads` int(11) DEFAULT 0,
    `page_views` int(11) DEFAULT 0,
    `unique_visitors` int(11) DEFAULT 0,
    `revenue` decimal(10,2) DEFAULT 0.00,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця системних подій
CREATE TABLE IF NOT EXISTS `system_events` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `event_type` varchar(50) NOT NULL,
    `event_data` text DEFAULT NULL,
    `severity` enum('info','warning','error','critical') DEFAULT 'info',
    `user_id` int(11) DEFAULT NULL,
    `ip_address` varchar(45) DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_event_type` (`event_type`),
    KEY `idx_severity` (`severity`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця backup'ів
CREATE TABLE IF NOT EXISTS `backups` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `filename` varchar(255) NOT NULL,
    `file_path` varchar(500) NOT NULL,
    `file_size` bigint(20) DEFAULT NULL,
    `backup_type` enum('database','files','full') NOT NULL,
    `status` enum('running','completed','failed') DEFAULT 'running',
    `started_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `completed_at` timestamp NULL DEFAULT NULL,
    `error_message` text DEFAULT NULL,
    `created_by` int(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `idx_type` (`backup_type`),
    KEY `idx_status` (`status`),
    KEY `idx_started` (`started_at`),
    KEY `idx_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;