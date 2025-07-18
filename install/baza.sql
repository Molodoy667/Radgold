-- Дошка Оголошень - База даних
-- Версія: 1.0
-- Дата створення: 2024

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

-- Структура таблиці `categories`
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `icon` varchar(50) DEFAULT 'fas fa-tag',
  `color` varchar(7) DEFAULT '#007bff',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Дані для таблиці `categories`
INSERT INTO `categories` (`id`, `name`, `description`, `icon`, `color`, `sort_order`, `is_active`) VALUES
(1, 'Транспорт', 'Автомобілі, мотоцикли, велосипеди та інший транспорт', 'fas fa-car', '#007bff', 1, 1),
(2, 'Нерухомість', 'Квартири, будинки, земельні ділянки', 'fas fa-home', '#28a745', 2, 1),
(3, 'Електроніка', 'Телефони, комп\'ютери, побутова техніка', 'fas fa-laptop', '#6f42c1', 3, 1),
(4, 'Меблі та інтер\'єр', 'Меблі, декор, предмети інтер\'єру', 'fas fa-couch', '#fd7e14', 4, 1),
(5, 'Одяг та взуття', 'Одяг, взуття, аксесуари', 'fas fa-tshirt', '#e83e8c', 5, 1),
(6, 'Спорт та відпочинок', 'Спортивні товари, туризм, хобі', 'fas fa-futbol', '#20c997', 6, 1),
(7, 'Робота', 'Вакансії та резюме', 'fas fa-briefcase', '#6c757d', 7, 1),
(8, 'Послуги', 'Різноманітні послуги', 'fas fa-tools', '#17a2b8', 8, 1),
(9, 'Дитячі товари', 'Іграшки, одяг, коляски', 'fas fa-baby', '#ffc107', 9, 1),
(10, 'Інше', 'Інші товари та послуги', 'fas fa-ellipsis-h', '#adb5bd', 10, 1);

-- --------------------------------------------------------

-- Структура таблиці `users`
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `about` text,
  `is_admin` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `is_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(255) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- Структура таблиці `ads`
CREATE TABLE `ads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `currency` varchar(3) DEFAULT 'UAH',
  `condition_type` enum('new','used','not_specified') DEFAULT 'not_specified',
  `location` varchar(100) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `is_negotiable` tinyint(1) DEFAULT 0,
  `is_urgent` tinyint(1) DEFAULT 0,
  `is_top` tinyint(1) DEFAULT 0,
  `status` enum('draft','active','sold','archived','moderation','rejected') DEFAULT 'active',
  `views_count` int(11) DEFAULT 0,
  `favorites_count` int(11) DEFAULT 0,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `category_id` (`category_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`),
  CONSTRAINT `ads_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ads_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- Структура таблиці `ad_images`
CREATE TABLE `ad_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `image_name` varchar(255) NOT NULL,
  `image_size` int(11) DEFAULT NULL,
  `is_main` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ad_id` (`ad_id`),
  CONSTRAINT `ad_images_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- Структура таблиці `favorites`
CREATE TABLE `favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `ad_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_ad_unique` (`user_id`,`ad_id`),
  KEY `ad_id` (`ad_id`),
  CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- Структура таблиці `messages`
CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ad_id` (`ad_id`),
  KEY `sender_id` (`sender_id`),
  KEY `receiver_id` (`receiver_id`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- Структура таблиці `views`
CREATE TABLE `views` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ad_id` (`ad_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `views_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `views_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- Структура таблиці `settings`
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text,
  `setting_type` enum('string','integer','boolean','json') DEFAULT 'string',
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Дані для таблиці `settings`
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `description`) VALUES
-- Основні налаштування сайту
('site_name', 'Дошка Оголошень', 'string', 'Назва сайту'),
('site_title', 'Дошка Оголошень - Купуй та продавай легко', 'string', 'META title сайту'),
('site_description', 'Найбільша дошка оголошень в Україні. Купуй та продавай товари та послуги легко та безпечно. Тисячі оголошень щодня.', 'string', 'META description сайту'),
('site_keywords', 'оголошення, купівля, продаж, товари, послуги, дошка оголошень, безкоштовні оголошення, Україна', 'string', 'META keywords сайту'),
('site_logo', 'assets/images/logo.svg', 'string', 'Логотип сайту'),
('site_favicon', 'assets/images/favicon.ico', 'string', 'Фавікон сайту'),
('site_url', 'http://localhost', 'string', 'URL сайту'),
('site_language', 'uk', 'string', 'Мова сайту'),
('site_timezone', 'Europe/Kiev', 'string', 'Часовий пояс'),

-- Технічні налаштування
('admin_email', 'admin@example.com', 'string', 'Email адміністратора'),
('items_per_page', '12', 'integer', 'Кількість оголошень на сторінці'),
('max_images_per_ad', '5', 'integer', 'Максимальна кількість зображень на оголошення'),
('max_image_size', '5242880', 'integer', 'Максимальний розмір зображення в байтах (5MB)'),
('allowed_image_types', 'jpg,jpeg,png,gif,webp', 'string', 'Дозволені типи зображень'),
('registration_enabled', '1', 'boolean', 'Чи дозволена реєстрація'),
('moderation_enabled', '0', 'boolean', 'Чи увімкнена модерація оголошень'),
('ads_auto_expire_days', '30', 'integer', 'Через скільки днів оголошення автоматично архівуються'),

-- Контактна інформація
('contact_phone', '+380 (44) 123-45-67', 'string', 'Контактний телефон'),
('contact_email', 'info@example.com', 'string', 'Контактний email'),
('contact_address', 'м. Київ, вул. Хрещатик, 1', 'string', 'Контактна адреса'),

-- Соціальні мережі
('social_facebook', '', 'string', 'Посилання на Facebook'),
('social_instagram', '', 'string', 'Посилання на Instagram'),
('social_telegram', '', 'string', 'Посилання на Telegram'),
('social_twitter', '', 'string', 'Посилання на Twitter'),
('social_youtube', '', 'string', 'Посилання на YouTube'),

-- SEO та аналітика
('analytics_google', '', 'string', 'Google Analytics код'),
('analytics_yandex', '', 'string', 'Yandex Metrica код'),
('google_site_verification', '', 'string', 'Google Site Verification'),
('yandex_verification', '', 'string', 'Yandex Verification'),

-- Зовнішній вигляд
('theme_color', '#007bff', 'string', 'Основний колір теми'),
('theme_secondary_color', '#6c757d', 'string', 'Додатковий колір теми'),
('header_background', '#ffffff', 'string', 'Колір фону шапки'),
('footer_background', '#343a40', 'string', 'Колір фону підвалу'),

-- Функціонал
('enable_comments', '1', 'boolean', 'Дозволити коментарі'),
('enable_ratings', '1', 'boolean', 'Дозволити рейтинги'),
('enable_favorites', '1', 'boolean', 'Дозволити вподобання'),
('enable_sharing', '1', 'boolean', 'Дозволити поділитися'),
('enable_search', '1', 'boolean', 'Дозволити пошук'),

-- Системні налаштування
('maintenance_mode', '0', 'boolean', 'Режим обслуговування'),
('maintenance_message', 'Сайт тимчасово недоступний через технічні роботи. Вибачте за незручності.', 'string', 'Повідомлення при обслуговуванні'),
('max_login_attempts', '5', 'integer', 'Максимальна кількість спроб входу'),
('session_lifetime', '3600', 'integer', 'Час життя сесії в секундах'),
('backup_enabled', '1', 'boolean', 'Автоматичне резервне копіювання'),
('debug_mode', '0', 'boolean', 'Режим налагодження');

-- --------------------------------------------------------

-- Структура таблиці `reports`
CREATE TABLE `reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ad_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `reason` enum('spam','inappropriate','fraud','duplicate','other') NOT NULL,
  `description` text,
  `status` enum('pending','reviewed','resolved','dismissed') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `ad_id` (`ad_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- Структура таблиці `admin_logs`
CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `target_type` varchar(50) DEFAULT NULL,
  `target_id` int(11) DEFAULT NULL,
  `description` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `admin_id` (`admin_id`),
  CONSTRAINT `admin_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- Створення індексів для оптимізації
CREATE INDEX `idx_ads_status_created` ON `ads` (`status`, `created_at`);
CREATE INDEX `idx_ads_category_status` ON `ads` (`category_id`, `status`);
CREATE INDEX `idx_ads_user_status` ON `ads` (`user_id`, `status`);
CREATE INDEX `idx_favorites_user` ON `favorites` (`user_id`);
CREATE INDEX `idx_messages_receiver` ON `messages` (`receiver_id`, `is_read`);
CREATE INDEX `idx_views_ip_ad` ON `views` (`ip_address`, `ad_id`);

COMMIT;