-- Виправлена база даних AdBoard Pro (без дублікатів)
-- Версія: 2.1.1
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
('site_keywords', 'оголошення, купити, продати, послуги, реклама', 'text', 'seo', 'Ключові слова', FALSE),
('site_author', 'AdBoard Pro Team', 'string', 'general', 'Автор сайту', TRUE),
('site_url', '', 'url', 'general', 'URL сайту', FALSE),
('admin_email', '', 'email', 'general', 'Email адміністратора', FALSE),
('contact_email', '', 'email', 'general', 'Email для контактів', TRUE),
('contact_phone', '', 'string', 'general', 'Телефон для контактів', TRUE),
('contact_address', '', 'text', 'general', 'Адреса для контактів', TRUE),
('timezone', 'Europe/Kiev', 'string', 'general', 'Часовий пояс', FALSE),
('language', 'uk', 'string', 'general', 'Мова за замовчуванням', TRUE),
('currency', 'UAH', 'string', 'general', 'Валюта', TRUE),

-- Тема та дизайн
('current_theme', 'light', 'string', 'theme', 'Поточна тема', TRUE),
('current_gradient', 'gradient-1', 'string', 'theme', 'Поточний градієнт', TRUE),
('enable_animations', '1', 'bool', 'theme', 'Увімкнути анімації', TRUE),
('enable_particles', '0', 'bool', 'theme', 'Частинки на фоні', TRUE),
('smooth_scroll', '1', 'bool', 'theme', 'Плавна прокрутка', TRUE),
('enable_tooltips', '1', 'bool', 'theme', 'Підказки', TRUE),
('logo_url', 'images/logo.svg', 'string', 'theme', 'URL логотипу', TRUE),
('favicon_url', 'images/favicon.ico', 'string', 'theme', 'URL фавікону', TRUE),

-- Налаштування оголошень
('max_ad_duration_days', '30', 'int', 'ads', 'Максимальна тривалість оголошення (днів)', FALSE),
('max_images_per_ad', '10', 'int', 'ads', 'Максимум зображень на оголошення', FALSE),
('ads_per_page', '12', 'int', 'ads', 'Оголошень на сторінку', TRUE),
('auto_approve_ads', '0', 'bool', 'ads', 'Автоматичне схвалення оголошень', FALSE),
('featured_ads_count', '6', 'int', 'ads', 'Кількість рекомендованих оголошень', TRUE),
('enable_geolocation', '1', 'bool', 'ads', 'Увімкнути геолокацію', TRUE),

-- Email налаштування  
('smtp_host', '', 'string', 'email', 'SMTP хост', FALSE),
('smtp_port', '587', 'int', 'email', 'SMTP порт', FALSE),
('smtp_username', '', 'string', 'email', 'SMTP користувач', FALSE),
('smtp_password', '', 'string', 'email', 'SMTP пароль', FALSE),
('smtp_encryption', 'tls', 'string', 'email', 'SMTP шифрування', FALSE),
('email_from_name', 'AdBoard Pro', 'string', 'email', 'Ім\'я відправника', FALSE),
('email_from_address', '', 'email', 'email', 'Email відправника', FALSE),
('email_footer', '', 'text', 'email', 'Підпис email', FALSE),

-- Платіжні системи
('payment_currency', 'UAH', 'string', 'payments', 'Валюта платежів', FALSE),
('liqpay_public_key', '', 'string', 'payments', 'LiqPay публічний ключ', FALSE),
('liqpay_private_key', '', 'string', 'payments', 'LiqPay приватний ключ', FALSE),
('fondy_merchant_id', '', 'string', 'payments', 'Fondy ID мерчанта', FALSE),
('fondy_secret_key', '', 'string', 'payments', 'Fondy секретний ключ', FALSE),
('paypal_client_id', '', 'string', 'payments', 'PayPal Client ID', FALSE),
('paypal_client_secret', '', 'string', 'payments', 'PayPal Client Secret', FALSE),
('enable_payments', '0', 'bool', 'payments', 'Увімкнути платежі', FALSE),

-- Безпека
('enable_recaptcha', '0', 'bool', 'security', 'Увімкнути reCAPTCHA', FALSE),
('recaptcha_site_key', '', 'string', 'security', 'reCAPTCHA site key', FALSE),
('recaptcha_secret_key', '', 'string', 'security', 'reCAPTCHA secret key', FALSE),
('max_login_attempts', '5', 'int', 'security', 'Максимум спроб входу', FALSE),
('login_block_time', '15', 'int', 'security', 'Час блокування (хвилин)', FALSE),
('enable_ssl_redirect', '0', 'bool', 'security', 'Перенаправлення на HTTPS', FALSE),

-- Соціальні мережі
('facebook_url', '', 'url', 'social', 'Facebook URL', TRUE),
('instagram_url', '', 'url', 'social', 'Instagram URL', TRUE),
('twitter_url', '', 'url', 'social', 'Twitter URL', TRUE),
('linkedin_url', '', 'url', 'social', 'LinkedIn URL', TRUE),
('youtube_url', '', 'url', 'social', 'YouTube URL', TRUE),
('telegram_url', '', 'url', 'social', 'Telegram URL', TRUE),

-- Аналітика
('google_analytics_id', '', 'string', 'analytics', 'Google Analytics ID', FALSE),
('facebook_pixel_id', '', 'string', 'analytics', 'Facebook Pixel ID', FALSE),
('yandex_metrica_id', '', 'string', 'analytics', 'Yandex Metrica ID', FALSE),
('enable_analytics', '0', 'bool', 'analytics', 'Увімкнути аналітику', FALSE),

-- Система налаштувань
('maintenance_mode', '0', 'bool', 'system', 'Режим обслуговування', FALSE),
('maintenance_message', 'Сайт тимчасово недоступний. Ведуться технічні роботи.', 'text', 'system', 'Повідомлення про обслуговування', FALSE),
('debug_mode', '0', 'bool', 'system', 'Режим налагодження', FALSE),
('log_errors', '1', 'bool', 'system', 'Логування помилок', FALSE),
('backup_enabled', '1', 'bool', 'system', 'Увімкнути backup', FALSE),
('backup_frequency', 'daily', 'string', 'system', 'Частота backup', FALSE),
('backup_retention_days', '30', 'int', 'system', 'Зберігати backup (днів)', FALSE);

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
    KEY `idx_email_verified` (`email_verified`),
    FOREIGN KEY (`group_id`) REFERENCES `user_groups`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця remember tokens
CREATE TABLE IF NOT EXISTS `remember_tokens` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `token` varchar(255) NOT NULL,
    `expires_at` timestamp NOT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `token` (`token`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_expires` (`expires_at`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця скидання паролів
CREATE TABLE IF NOT EXISTS `password_resets` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `email` varchar(255) NOT NULL,
    `token` varchar(255) NOT NULL,
    `expires_at` timestamp NOT NULL,
    `used` boolean DEFAULT FALSE,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_email` (`email`),
    KEY `idx_token` (`token`),
    KEY `idx_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця категорій
CREATE TABLE IF NOT EXISTS `categories` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `slug` varchar(100) NOT NULL,
    `description` text DEFAULT NULL,
    `icon` varchar(50) DEFAULT NULL,
    `parent_id` int(11) DEFAULT NULL,
    `sort_order` int(11) DEFAULT 0,
    `is_active` boolean DEFAULT TRUE,
    `meta_title` varchar(255) DEFAULT NULL,
    `meta_description` text DEFAULT NULL,
    `meta_keywords` text DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`),
    KEY `idx_parent` (`parent_id`),
    KEY `idx_active` (`is_active`),
    KEY `idx_sort` (`sort_order`),
    FOREIGN KEY (`parent_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця локацій
CREATE TABLE IF NOT EXISTS `locations` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `slug` varchar(100) NOT NULL,
    `region` varchar(100) DEFAULT NULL,
    `country` varchar(100) DEFAULT 'Ukraine',
    `latitude` decimal(10,8) DEFAULT NULL,
    `longitude` decimal(11,8) DEFAULT NULL,
    `is_active` boolean DEFAULT TRUE,
    `sort_order` int(11) DEFAULT 0,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`),
    KEY `idx_region` (`region`),
    KEY `idx_active` (`is_active`),
    KEY `idx_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця оголошень
CREATE TABLE IF NOT EXISTS `ads` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `category_id` int(11) NOT NULL,
    `location_id` int(11) NOT NULL,
    `title` varchar(255) NOT NULL,
    `slug` varchar(255) NOT NULL,
    `description` text NOT NULL,
    `price` decimal(12,2) DEFAULT NULL,
    `currency` varchar(3) DEFAULT 'UAH',
    `contact_name` varchar(100) DEFAULT NULL,
    `contact_phone` varchar(20) DEFAULT NULL,
    `contact_email` varchar(100) DEFAULT NULL,
    `address` text DEFAULT NULL,
    `latitude` decimal(10,8) DEFAULT NULL,
    `longitude` decimal(11,8) DEFAULT NULL,
    `condition_type` enum('new','used','refurbished') DEFAULT 'used',
    `status` enum('draft','pending','active','sold','expired','rejected','archived') DEFAULT 'pending',
    `moderation_comment` text DEFAULT NULL,
    `views_count` int(11) DEFAULT 0,
    `favorites_count` int(11) DEFAULT 0,
    `is_featured` boolean DEFAULT FALSE,
    `featured_until` datetime DEFAULT NULL,
    `is_urgent` boolean DEFAULT FALSE,
    `urgent_until` datetime DEFAULT NULL,
    `is_top` boolean DEFAULT FALSE,
    `top_until` datetime DEFAULT NULL,
    `auto_republish` boolean DEFAULT FALSE,
    `expires_at` datetime DEFAULT NULL,
    `published_at` datetime DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`),
    KEY `idx_user` (`user_id`),
    KEY `idx_category` (`category_id`),
    KEY `idx_location` (`location_id`),
    KEY `idx_status` (`status`),
    KEY `idx_featured` (`is_featured`),
    KEY `idx_urgent` (`is_urgent`),
    KEY `idx_top` (`is_top`),
    KEY `idx_published` (`published_at`),
    KEY `idx_expires` (`expires_at`),
    KEY `idx_search` (`category_id`,`location_id`,`status`,`price`),
    FULLTEXT KEY `idx_fulltext` (`title`,`description`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE RESTRICT,
    FOREIGN KEY (`location_id`) REFERENCES `locations`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця зображень оголошень
CREATE TABLE IF NOT EXISTS `ad_images` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ad_id` int(11) NOT NULL,
    `filename` varchar(255) NOT NULL,
    `original_name` varchar(255) DEFAULT NULL,
    `file_size` int(11) DEFAULT NULL,
    `mime_type` varchar(100) DEFAULT NULL,
    `width` int(11) DEFAULT NULL,
    `height` int(11) DEFAULT NULL,
    `is_main` boolean DEFAULT FALSE,
    `sort_order` int(11) DEFAULT 0,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_ad` (`ad_id`),
    KEY `idx_main` (`is_main`),
    KEY `idx_sort` (`sort_order`),
    FOREIGN KEY (`ad_id`) REFERENCES `ads`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця атрибутів категорій
CREATE TABLE IF NOT EXISTS `category_attributes` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `category_id` int(11) NOT NULL,
    `name` varchar(100) NOT NULL,
    `type` enum('text','number','select','checkbox','textarea','date','email','url') NOT NULL,
    `options` json DEFAULT NULL,
    `is_required` boolean DEFAULT FALSE,
    `is_searchable` boolean DEFAULT TRUE,
    `sort_order` int(11) DEFAULT 0,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_category` (`category_id`),
    KEY `idx_sort` (`sort_order`),
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця значень атрибутів
CREATE TABLE IF NOT EXISTS `ad_attributes` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ad_id` int(11) NOT NULL,
    `attribute_id` int(11) NOT NULL,
    `value` text DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_ad` (`ad_id`),
    KEY `idx_attribute` (`attribute_id`),
    KEY `idx_value` (`value`(100)),
    FOREIGN KEY (`ad_id`) REFERENCES `ads`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`attribute_id`) REFERENCES `category_attributes`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця улюблених
CREATE TABLE IF NOT EXISTS `favorites` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `ad_id` int(11) NOT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_favorite` (`user_id`,`ad_id`),
    KEY `idx_user` (`user_id`),
    KEY `idx_ad` (`ad_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`ad_id`) REFERENCES `ads`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця переглядів
CREATE TABLE IF NOT EXISTS `ad_views` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ad_id` int(11) NOT NULL,
    `user_id` int(11) DEFAULT NULL,
    `ip_address` varchar(45) DEFAULT NULL,
    `user_agent` text DEFAULT NULL,
    `referer` text DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_ad` (`ad_id`),
    KEY `idx_user` (`user_id`),
    KEY `idx_ip` (`ip_address`),
    KEY `idx_date` (`created_at`),
    FOREIGN KEY (`ad_id`) REFERENCES `ads`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця платних послуг
CREATE TABLE IF NOT EXISTS `paid_services` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `description` text DEFAULT NULL,
    `price` decimal(10,2) NOT NULL,
    `duration_days` int(11) NOT NULL,
    `service_type` enum('featured','urgent','top','highlight','republish','boost') NOT NULL,
    `is_active` boolean DEFAULT TRUE,
    `sort_order` int(11) DEFAULT 0,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_type` (`service_type`),
    KEY `idx_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця покупок послуг
CREATE TABLE IF NOT EXISTS `service_purchases` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `ad_id` int(11) NOT NULL,
    `service_id` int(11) NOT NULL,
    `price` decimal(10,2) NOT NULL,
    `status` enum('pending','active','expired','cancelled') DEFAULT 'pending',
    `starts_at` datetime DEFAULT NULL,
    `expires_at` datetime DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user` (`user_id`),
    KEY `idx_ad` (`ad_id`),
    KEY `idx_service` (`service_id`),
    KEY `idx_status` (`status`),
    KEY `idx_expires` (`expires_at`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`ad_id`) REFERENCES `ads`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`service_id`) REFERENCES `paid_services`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця транзакцій
CREATE TABLE IF NOT EXISTS `transactions` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `type` enum('deposit','withdraw','purchase','refund','bonus','commission') NOT NULL,
    `amount` decimal(10,2) NOT NULL,
    `balance_before` decimal(10,2) NOT NULL,
    `balance_after` decimal(10,2) NOT NULL,
    `description` text DEFAULT NULL,
    `payment_method` varchar(50) DEFAULT NULL,
    `external_transaction_id` varchar(255) DEFAULT NULL,
    `reference_type` enum('ad','service','withdrawal','deposit') DEFAULT NULL,
    `reference_id` int(11) DEFAULT NULL,
    `status` enum('pending','completed','failed','cancelled') DEFAULT 'pending',
    `metadata` json DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user` (`user_id`),
    KEY `idx_type` (`type`),
    KEY `idx_status` (`status`),
    KEY `idx_reference` (`reference_type`,`reference_id`),
    KEY `idx_created` (`created_at`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця чатів
CREATE TABLE IF NOT EXISTS `chats` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ad_id` int(11) NOT NULL,
    `buyer_id` int(11) NOT NULL,
    `seller_id` int(11) NOT NULL,
    `status` enum('active','archived','blocked') DEFAULT 'active',
    `last_message_id` int(11) DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_chat` (`ad_id`,`buyer_id`,`seller_id`),
    KEY `idx_ad` (`ad_id`),
    KEY `idx_buyer` (`buyer_id`),
    KEY `idx_seller` (`seller_id`),
    KEY `idx_updated` (`updated_at`),
    FOREIGN KEY (`ad_id`) REFERENCES `ads`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`buyer_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`seller_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця повідомлень чату
CREATE TABLE IF NOT EXISTS `chat_messages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `chat_id` int(11) NOT NULL,
    `sender_id` int(11) NOT NULL,
    `receiver_id` int(11) NOT NULL,
    `message` text NOT NULL,
    `message_type` enum('text','image','file','system') DEFAULT 'text',
    `attachment_url` varchar(500) DEFAULT NULL,
    `attachment_size` int(11) DEFAULT NULL,
    `is_read` boolean DEFAULT FALSE,
    `read_at` timestamp NULL DEFAULT NULL,
    `is_deleted` boolean DEFAULT FALSE,
    `deleted_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_chat` (`chat_id`),
    KEY `idx_sender` (`sender_id`),
    KEY `idx_receiver` (`receiver_id`),
    KEY `idx_is_read` (`is_read`),
    KEY `idx_created` (`created_at`),
    FOREIGN KEY (`chat_id`) REFERENCES `chats`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`sender_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`receiver_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця сповіщень
CREATE TABLE IF NOT EXISTS `notifications` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `type` varchar(50) NOT NULL,
    `title` varchar(255) NOT NULL,
    `message` text NOT NULL,
    `action_url` varchar(500) DEFAULT NULL,
    `priority` enum('low','normal','high','urgent') DEFAULT 'normal',
    `icon` varchar(50) DEFAULT NULL,
    `data` json DEFAULT NULL,
    `is_read` boolean DEFAULT FALSE,
    `read_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user` (`user_id`),
    KEY `idx_type` (`type`),
    KEY `idx_is_read` (`is_read`),
    KEY `idx_created` (`created_at`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця push підписок
CREATE TABLE IF NOT EXISTS `push_subscriptions` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `endpoint` text NOT NULL,
    `p256dh` varchar(255) NOT NULL,
    `auth` varchar(255) NOT NULL,
    `user_agent` text DEFAULT NULL,
    `is_active` boolean DEFAULT TRUE,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user` (`user_id`),
    KEY `idx_active` (`is_active`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця логів активності
CREATE TABLE IF NOT EXISTS `activity_logs` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) DEFAULT NULL,
    `action` varchar(100) NOT NULL,
    `description` text NOT NULL,
    `data` json DEFAULT NULL,
    `ip_address` varchar(45) DEFAULT NULL,
    `user_agent` text DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user` (`user_id`),
    KEY `idx_action` (`action`),
    KEY `idx_created` (`created_at`),
    KEY `idx_ip` (`ip_address`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця повідомлень від адміністраторів
CREATE TABLE IF NOT EXISTS `admin_messages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `admin_id` int(11) NOT NULL,
    `subject` varchar(255) NOT NULL,
    `message` text NOT NULL,
    `is_read` boolean DEFAULT FALSE,
    `read_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user` (`user_id`),
    KEY `idx_admin` (`admin_id`),
    KEY `idx_is_read` (`is_read`),
    KEY `idx_created` (`created_at`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`admin_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця рейтингів користувачів
CREATE TABLE IF NOT EXISTS `user_ratings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `rater_id` int(11) NOT NULL,
    `rating` tinyint(1) NOT NULL CHECK (`rating` >= 1 AND `rating` <= 5),
    `comment` text DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_rating` (`user_id`,`rater_id`),
    KEY `idx_user` (`user_id`),
    KEY `idx_rater` (`rater_id`),
    KEY `idx_rating` (`rating`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`rater_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця блокування користувачів
CREATE TABLE IF NOT EXISTS `user_blocks` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `blocked_user_id` int(11) NOT NULL,
    `reason` text DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_block` (`user_id`,`blocked_user_id`),
    KEY `idx_user` (`user_id`),
    KEY `idx_blocked` (`blocked_user_id`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`blocked_user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця скарг
CREATE TABLE IF NOT EXISTS `reports` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `reporter_id` int(11) DEFAULT NULL,
    `reported_user_id` int(11) DEFAULT NULL,
    `ad_id` int(11) DEFAULT NULL,
    `reason` enum('spam','inappropriate','fraud','duplicate','other') NOT NULL,
    `description` text DEFAULT NULL,
    `status` enum('pending','reviewed','resolved','dismissed') DEFAULT 'pending',
    `admin_comment` text DEFAULT NULL,
    `resolved_by` int(11) DEFAULT NULL,
    `resolved_at` timestamp NULL DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_reporter` (`reporter_id`),
    KEY `idx_reported_user` (`reported_user_id`),
    KEY `idx_ad` (`ad_id`),
    KEY `idx_status` (`status`),
    KEY `idx_reason` (`reason`),
    FOREIGN KEY (`reporter_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`reported_user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`ad_id`) REFERENCES `ads`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`resolved_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця збережених пошуків
CREATE TABLE IF NOT EXISTS `saved_searches` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `name` varchar(100) NOT NULL,
    `query` text DEFAULT NULL,
    `category_id` int(11) DEFAULT NULL,
    `location_id` int(11) DEFAULT NULL,
    `filters` json DEFAULT NULL,
    `email_notifications` boolean DEFAULT FALSE,
    `last_notified_at` datetime DEFAULT NULL,
    `is_active` boolean DEFAULT TRUE,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user` (`user_id`),
    KEY `idx_category` (`category_id`),
    KEY `idx_location` (`location_id`),
    KEY `idx_active` (`is_active`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`location_id`) REFERENCES `locations`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця щоденної статистики
CREATE TABLE IF NOT EXISTS `daily_stats` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `date` date NOT NULL,
    `total_views` int(11) DEFAULT 0,
    `unique_visitors` int(11) DEFAULT 0,
    `ads_created` int(11) DEFAULT 0,
    `ads_activated` int(11) DEFAULT 0,
    `users_registered` int(11) DEFAULT 0,
    `revenue` decimal(10,2) DEFAULT 0.00,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `date` (`date`),
    KEY `idx_date` (`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблиця системних подій
CREATE TABLE IF NOT EXISTS `system_events` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `event_type` varchar(50) NOT NULL,
    `event_name` varchar(100) NOT NULL,
    `description` text DEFAULT NULL,
    `data` json DEFAULT NULL,
    `severity` enum('info','warning','error','critical') DEFAULT 'info',
    `user_id` int(11) DEFAULT NULL,
    `ip_address` varchar(45) DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_type` (`event_type`),
    KEY `idx_severity` (`severity`),
    KEY `idx_user` (`user_id`),
    KEY `idx_created` (`created_at`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
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
    KEY `idx_created_by` (`created_by`),
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;