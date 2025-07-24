# Як працює таблиця site_settings в AdBoard Pro

## 🎯 Коротка відповідь на ваше питання

Базові налаштування вставляються **ДВІЧІ**:

1. **Спочатку** - з файлу `database.sql` під час імпорту схеми БД (базові значення)
2. **Потім** - з інсталятора в `step_8.php` (персоналізовані значення з форм)

## 📋 Структура таблиці site_settings

```sql
CREATE TABLE IF NOT EXISTS `site_settings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `setting_key` varchar(100) NOT NULL,               -- Унікальний ключ
    `setting_value` text,                              -- Значення
    `setting_type` enum('string','text','int','bool','json','email','url'),
    `setting_group` varchar(50) DEFAULT 'general',     -- Група (general, theme, email тощо)
    `description` varchar(255),                        -- Опис налаштування
    `is_public` boolean DEFAULT FALSE,                 -- Чи публічне
    PRIMARY KEY (`id`),
    UNIQUE KEY `setting_key` (`setting_key`)           -- Унікальність ключа
);
```

## 🔄 Процес заповнення налаштувань

### Етап 1: Імпорт database.sql
Коли інсталятор імпортує `database.sql`, виконується:

```sql
INSERT IGNORE INTO `site_settings` (...) VALUES
('site_name', 'AdBoard Pro', 'string', 'general', 'Назва сайту', TRUE),
('site_description', 'Сучасна дошка оголошень...', 'text', 'general', 'Опис сайту', TRUE),
('site_url', '', 'url', 'general', 'URL сайту', FALSE),  -- ПОРОЖНЄ!
('admin_email', '', 'email', 'general', 'Email адміністратора', FALSE),  -- ПОРОЖНЄ!
-- ... та багато інших
```

**Результат**: Створюється ~40 базових налаштувань, деякі з значеннями за замовчуванням, деякі порожні.

### Етап 2: Оновлення через інсталятор (step_8.php)

Після того як користувач пройшов всі кроки інсталятора, в `step_8.php` виконується:

```php
// Збираємо дані з сесії (введені користувачем)
$siteConfig = $_SESSION['install_data']['site']; // Крок 4
$adminConfig = $_SESSION['install_data']['admin']; // Крок 7
$additionalConfig = $_SESSION['install_data']['additional']; // Крок 5
$themeConfig = $_SESSION['install_data']['theme']; // Крок 6

// Створюємо масив для оновлення
$settings = [
    ['site_name', $siteConfig['site_name'], 'string', 'general', 'Назва сайту'],
    ['site_url', $siteConfig['site_url'], 'url', 'general', 'URL сайту'],
    ['admin_email', $adminConfig['admin_email'], 'email', 'general', 'Email адміністратора'],
    // ... та інші
];

// Оновлюємо існуючі записи
$stmt = $mysqli->prepare("
    INSERT INTO site_settings (setting_key, setting_value, setting_type, setting_group, description) 
    VALUES (?, ?, ?, ?, ?) 
    ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
");
```

**Результат**: Існуючі записи оновлюються реальними даними від користувача.

## 📊 Приклад: Як змінюється site_name

### Після імпорту database.sql:
```
setting_key: 'site_name'
setting_value: 'AdBoard Pro'  ← Значення за замовчуванням
```

### Після проходження інсталятора (користувач ввів "Моя дошка"):
```
setting_key: 'site_name' 
setting_value: 'Моя дошка'   ← Оновлено значенням від користувача
```

## 🗂️ Групи налаштувань в БД

Всі налаштування розділені на логічні групи:

### **general** (Основні)
- site_name, site_url, admin_email, contact_email
- timezone, language, currency

### **theme** (Тема) 
- current_theme, current_gradient
- enable_animations, enable_particles

### **email** (Пошта)
- smtp_host, smtp_port, smtp_username
- email_from_name, email_from_address

### **payments** (Платежі)
- liqpay_public_key, fondy_merchant_id
- paypal_client_id, enable_payments

### **security** (Безпека)
- enable_recaptcha, max_login_attempts
- enable_ssl_redirect

### **system** (Система)
- maintenance_mode, debug_mode
- backup_enabled, backup_frequency

## ✅ Чому саме така схема?

1. **INSERT IGNORE** в database.sql - створює базові налаштування тільки якщо їх ще немає
2. **ON DUPLICATE KEY UPDATE** в step_8.php - оновлює існуючі значення користувацькими даними
3. **Це гарантує** що:
   - Усі налаштування завжди присутні в БД
   - Користувацькі дані не затираються при повторній установці
   - Є резервні значення для всіх параметрів

## 🎯 Висновок

**Базові налаштування вставляються в таблицю site_settings з файлу database.sql**, а потім **оновлюються реальними даними користувача через інсталятор**. 

Така схема забезпечує надійність та гнучкість системи налаштувань! 🚀