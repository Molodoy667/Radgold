# 📋 КАРТА САЙТУ - СТРУКТУРА ПРОЕКТУ

## 🏗️ **ЗАГАЛЬНА АРХІТЕКТУРА**

### **Основні папки:**
```
📁 /
├── 📁 admin/          # Адміністративна панель
├── 📁 api/            # API endpoints  
├── 📁 assets/         # Статичні файли (CSS, JS, images)
├── 📁 core/           # Основна логіка системи
├── 📁 docs/           # Документація
├── 📁 install/        # Інсталятор системи
├── 📁 pages/          # Публічні сторінки
├── 📁 themes/         # Теми оформлення
└── 📁 uploads/        # Завантажені файли
```

---

## 🗄️ **СТРУКТУРА БАЗИ ДАНИХ**

### **1️⃣ КОРИСТУВАЧІ ТА АВТОРИЗАЦІЯ**

#### **`users` - Основна таблиця користувачів**
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,      -- Ім'я користувача
    email VARCHAR(100) UNIQUE NOT NULL,        -- Email
    password VARCHAR(255) NOT NULL,            -- Хешований пароль
    role ENUM('user','admin','super_admin') DEFAULT 'user',  -- Роль
    user_type ENUM('user','partner','admin') DEFAULT 'user', -- Тип користувача
    status ENUM('active','inactive','banned') DEFAULT 'active', -- Статус
    avatar VARCHAR(255),                       -- Аватар
    phone VARCHAR(20),                         -- Телефон
    first_name VARCHAR(50),                    -- Ім'я
    last_name VARCHAR(50),                     -- Прізвище
    city_id INT,                              -- FK до cities
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP,                      -- Останній вхід
    email_verified BOOLEAN DEFAULT FALSE,      -- Верифікація email
    verification_token VARCHAR(255),           -- Токен верифікації
    
    FOREIGN KEY (city_id) REFERENCES cities(id)
);
```

#### **`user_sessions` - Сесії користувачів**
```sql
CREATE TABLE user_sessions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,                      -- FK до users
    session_token VARCHAR(255) UNIQUE NOT NULL, -- Токен сесії
    ip_address VARCHAR(45),                    -- IP адреса
    user_agent TEXT,                           -- User Agent
    expires_at TIMESTAMP NOT NULL,             -- Час закінчення
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

#### **`password_resets` - Скидання паролів**
```sql
CREATE TABLE password_resets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) NOT NULL,               -- Email користувача
    token VARCHAR(255) NOT NULL,               -- Токен скидання
    expires_at TIMESTAMP NOT NULL,             -- Час закінчення
    used BOOLEAN DEFAULT FALSE,                -- Чи використаний
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

### **2️⃣ ОГОЛОШЕННЯ ТА КОНТЕНТ**

#### **`posts` - Основна таблиця оголошень**
```sql
CREATE TABLE posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,                      -- FK до users (автор)
    category_id INT NOT NULL,                  -- FK до categories
    service_id INT,                            -- FK до services (опціонально)
    city_id INT NOT NULL,                      -- FK до cities
    title VARCHAR(255) NOT NULL,               -- Заголовок
    description TEXT NOT NULL,                 -- Опис
    content TEXT,                              -- Повний контент
    price DECIMAL(10,2),                       -- Ціна
    currency VARCHAR(3) DEFAULT 'UAH',         -- Валюта
    contact_phone VARCHAR(20),                 -- Контактний телефон
    contact_email VARCHAR(100),                -- Контактний email
    status ENUM('draft','published','archived','deleted') DEFAULT 'draft',
    is_featured BOOLEAN DEFAULT FALSE,         -- Рекомендоване
    is_urgent BOOLEAN DEFAULT FALSE,           -- Терміново
    views_count INT DEFAULT 0,                 -- Кількість переглядів
    likes_count INT DEFAULT 0,                 -- Кількість лайків
    expires_at TIMESTAMP,                      -- Дата закінчення
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (service_id) REFERENCES services(id),
    FOREIGN KEY (city_id) REFERENCES cities(id)
);
```

#### **`post_images` - Зображення до оголошень**
```sql
CREATE TABLE post_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,                      -- FK до posts
    image_path VARCHAR(255) NOT NULL,          -- Шлях до файлу
    image_name VARCHAR(255),                   -- Ім'я файлу
    is_primary BOOLEAN DEFAULT FALSE,          -- Головне зображення
    sort_order INT DEFAULT 0,                  -- Порядок сортування
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);
```

#### **`post_views` - Перегляди оголошень**
```sql
CREATE TABLE post_views (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,                      -- FK до posts
    user_id INT,                               -- FK до users (може бути NULL для анонімів)
    ip_address VARCHAR(45),                    -- IP адреса
    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
```

#### **`post_likes` - Лайки оголошень**
```sql
CREATE TABLE post_likes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,                      -- FK до posts
    user_id INT NOT NULL,                      -- FK до users
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_like (post_id, user_id)
);
```

---

### **3️⃣ КАТЕГОРІЇ ТА КЛАСИФІКАЦІЯ**

#### **`categories` - Категорії оголошень**
```sql
CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    parent_id INT,                             -- FK до categories (для підкategorій)
    name VARCHAR(100) NOT NULL,                -- Назва категорії
    slug VARCHAR(100) UNIQUE NOT NULL,         -- URL слаг
    description TEXT,                          -- Опис категорії
    icon VARCHAR(50),                          -- CSS клас іконки
    color VARCHAR(7),                          -- Колір (HEX)
    image VARCHAR(255),                        -- Зображення категорії
    sort_order INT DEFAULT 0,                  -- Порядок сортування
    is_active BOOLEAN DEFAULT TRUE,            -- Активність
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
);
```

#### **`services` - Послуги**
```sql
CREATE TABLE services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,                           -- FK до categories
    name VARCHAR(100) NOT NULL,                -- Назва послуги
    slug VARCHAR(100) UNIQUE NOT NULL,         -- URL слаг
    description TEXT,                          -- Опис послуги
    icon VARCHAR(50),                          -- CSS клас іконки
    sort_order INT DEFAULT 0,                  -- Порядок сортування
    is_active BOOLEAN DEFAULT TRUE,            -- Активність
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);
```

---

### **4️⃣ ГЕОГРАФІЯ**

#### **`cities` - Міста**
```sql
CREATE TABLE cities (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,                -- Назва міста
    slug VARCHAR(100) UNIQUE NOT NULL,         -- URL слаг
    region VARCHAR(100),                       -- Область/регіон
    country VARCHAR(100) DEFAULT 'Ukraine',    -- Країна
    latitude DECIMAL(10, 8),                   -- Широта
    longitude DECIMAL(11, 8),                  -- Довгота
    population INT,                            -- Населення
    sort_order INT DEFAULT 0,                  -- Порядок сортування
    is_active BOOLEAN DEFAULT TRUE,            -- Активність
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

### **5️⃣ СИСТЕМНІ НАЛАШТУВАННЯ**

#### **`site_settings` - Налаштування сайту**
```sql
CREATE TABLE site_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_name VARCHAR(100) UNIQUE NOT NULL, -- Ключ налаштування
    setting_value TEXT,                        -- Значення
    setting_type ENUM('text','number','boolean','json') DEFAULT 'text',
    description TEXT,                          -- Опис налаштування
    group_name VARCHAR(50),                    -- Група налаштувань
    is_public BOOLEAN DEFAULT FALSE,           -- Публічні налаштування
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### **`translations` - Переклади**
```sql
CREATE TABLE translations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    lang_code VARCHAR(5) NOT NULL,             -- Код мови (uk, en, ru)
    translation_key VARCHAR(255) NOT NULL,     -- Ключ перекладу
    translation_value TEXT NOT NULL,           -- Значення перекладу
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_translation (lang_code, translation_key)
);
```

---

### **6️⃣ ПОВІДОМЛЕННЯ ТА КОМУНІКАЦІЇ**

#### **`messages` - Повідомлення між користувачами**
```sql
CREATE TABLE messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT NOT NULL,                    -- FK до users (відправник)
    receiver_id INT NOT NULL,                  -- FK до users (отримувач)
    post_id INT,                               -- FK до posts (якщо стосується оголошення)
    subject VARCHAR(255),                      -- Тема повідомлення
    content TEXT NOT NULL,                     -- Текст повідомлення
    is_read BOOLEAN DEFAULT FALSE,             -- Прочитане
    is_deleted_by_sender BOOLEAN DEFAULT FALSE,
    is_deleted_by_receiver BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE SET NULL
);
```

#### **`notifications` - Сповіщення**
```sql
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,                      -- FK до users
    type VARCHAR(50) NOT NULL,                 -- Тип сповіщення
    title VARCHAR(255) NOT NULL,               -- Заголовок
    content TEXT,                              -- Текст сповіщення
    data JSON,                                 -- Додаткові дані
    is_read BOOLEAN DEFAULT FALSE,             -- Прочитане
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

---

### **7️⃣ МОДЕРАЦІЯ ТА АДМІНІСТРУВАННЯ**

#### **`admin_logs` - Логи адміністратора**
```sql
CREATE TABLE admin_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT NOT NULL,                     -- FK до users
    action VARCHAR(100) NOT NULL,              -- Дія
    table_name VARCHAR(50),                    -- Таблиця
    record_id INT,                             -- ID запису
    old_data JSON,                             -- Старі дані
    new_data JSON,                             -- Нові дані
    ip_address VARCHAR(45),                    -- IP адреса
    user_agent TEXT,                           -- User Agent
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
);
```

#### **`reports` - Скарги на оголошення**
```sql
CREATE TABLE reports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,                      -- FK до posts
    reporter_id INT,                           -- FK до users (може бути NULL для анонімів)
    reason VARCHAR(100) NOT NULL,              -- Причина скарги
    description TEXT,                          -- Детальний опис
    status ENUM('pending','reviewed','resolved','rejected') DEFAULT 'pending',
    admin_id INT,                              -- FK до users (адмін який розглядав)
    admin_comment TEXT,                        -- Коментар адміна
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP,
    
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (reporter_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE SET NULL
);
```

---

## 🌐 **СТРУКТУРА РОЗДІЛІВ САЙТУ**

### **1️⃣ ПУБЛІЧНА ЧАСТИНА**

#### **Головна сторінка (`/index.php`)**
- 🏠 Пошук оголошень
- 📊 Статистика сайту  
- 🔥 Рекомендовані оголошення
- 📰 Останні оголошення
- 🏙️ Популярні міста
- 📂 Популярні категорії

#### **Каталог оголошень (`/pages/posts/`)**
- `/pages/posts/index.php` - Список всіх оголошень
- `/pages/posts/view.php?id=123` - Перегляд оголошення
- `/pages/posts/search.php` - Розширений пошук
- `/pages/posts/category.php?slug=electronics` - Оголошення по категоріях
- `/pages/posts/city.php?slug=kyiv` - Оголошення по містах

#### **Категорії (`/pages/categories/`)**
- `/pages/categories/index.php` - Всі категорії
- `/pages/categories/view.php?slug=electronics` - Перегляд категорії

#### **Авторизація (`/pages/auth/`)**
- `/pages/user/login.php` - Вхід користувача
- `/pages/user/register.php` - Реєстрація користувача
- `/pages/partner/login.php` - Вхід партнера
- `/pages/partner/register.php` - Реєстрація партнера
- `/pages/auth/forgot-password.php` - Відновлення паролю
- `/pages/auth/reset-password.php` - Скидання паролю
- `/pages/auth/verify-email.php` - Верифікація email

### **2️⃣ ОСОБИСТИЙ КАБІНЕТ КОРИСТУВАЧА**

#### **Основне (`/pages/user/`)**
- `/pages/user/dashboard.php` - Головна сторінка кабінету
- `/pages/user/profile.php` - Редагування профілю
- `/pages/user/settings.php` - Налаштування

#### **Оголошення (`/pages/user/posts/`)**
- `/pages/user/posts/index.php` - Мої оголошення
- `/pages/user/posts/create.php` - Створити оголошення
- `/pages/user/posts/edit.php?id=123` - Редагувати оголошення
- `/pages/user/posts/statistics.php` - Статистика оголошень

#### **Повідомлення (`/pages/user/messages/`)**
- `/pages/user/messages/index.php` - Всі повідомлення
- `/pages/user/messages/view.php?id=123` - Перегляд повідомлення
- `/pages/user/messages/compose.php` - Написати повідомлення

### **3️⃣ ОСОБИСТИЙ КАБІНЕТ ПАРТНЕРА**

#### **Основне (`/pages/partner/`)**
- `/pages/partner/dashboard.php` - Головна сторінка партнера
- `/pages/partner/profile.php` - Профіль партнера
- `/pages/partner/advertising.php` - Рекламні кампанії
- `/pages/partner/statistics.php` - Статистика і аналітика
- `/pages/partner/billing.php` - Фінанси і оплата

### **4️⃣ АДМІНІСТРАТИВНА ПАНЕЛЬ**

#### **Основне (`/admin/`)**
- `/admin/index.php` - Головна адмін панелі
- `/admin/dashboard.php` - Статистика і графіки
- `/admin/settings.php` - Налаштування сайту

#### **Користувачі (`/admin/users/`)**
- `/admin/users/index.php` - Список користувачів
- `/admin/users/view.php?id=123` - Перегляд користувача
- `/admin/users/edit.php?id=123` - Редагування користувача
- `/admin/users/roles.php` - Управління ролями

#### **Оголошення (`/admin/posts/`)**
- `/admin/posts/index.php` - Всі оголошення
- `/admin/posts/pending.php` - На модерації
- `/admin/posts/reported.php` - Зі скаргами
- `/admin/posts/featured.php` - Рекомендовані

#### **Контент (`/admin/content/`)**
- `/admin/categories/index.php` - Категорії
- `/admin/services/index.php` - Послуги
- `/admin/cities/index.php` - Міста
- `/admin/translations/index.php` - Переклади

#### **Звіти (`/admin/reports/`)**
- `/admin/reports/users.php` - Звіт по користувачах
- `/admin/reports/posts.php` - Звіт по оголошеннях
- `/admin/reports/traffic.php` - Звіт по трафіку
- `/admin/reports/financial.php` - Фінансовий звіт

---

## 🔗 **ЗВ'ЯЗКИ МІЖ ТАБЛИЦЯМИ**

### **Основні зв'язки:**

#### **Users (Користувачі):**
- `users.city_id` → `cities.id` (місто користувача)
- `users.id` ← `posts.user_id` (оголошення користувача)
- `users.id` ← `messages.sender_id` (відправлені повідомлення)
- `users.id` ← `messages.receiver_id` (отримані повідомлення)

#### **Posts (Оголошення):**
- `posts.user_id` → `users.id` (автор оголошення)
- `posts.category_id` → `categories.id` (категорія)
- `posts.service_id` → `services.id` (послуга)
- `posts.city_id` → `cities.id` (місто)
- `posts.id` ← `post_images.post_id` (зображення)
- `posts.id` ← `post_views.post_id` (перегляди)
- `posts.id` ← `post_likes.post_id` (лайки)

#### **Categories (Категорії):**
- `categories.parent_id` → `categories.id` (батьківська категорія)
- `categories.id` ← `posts.category_id` (оголошення категорії)
- `categories.id` ← `services.category_id` (послуги категорії)

---

## 📁 **ФАЙЛОВА СТРУКТУРА CORE**

### **`/core/` - Основна логіка**
- `database.php` - Клас роботи з БД (singleton)
- `functions.php` - Загальні функції
- `auth.php` - Функції авторизації
- `session.php` - Управління сесіями
- `upload.php` - Завантаження файлів
- `email.php` - Відправка email
- `translation.php` - Система перекладів
- `cache.php` - Кешування
- `validation.php` - Валідація даних
- `pagination.php` - Пагінація
- `search.php` - Пошук

### **`/api/` - API endpoints**
- `posts.php` - API оголошень
- `users.php` - API користувачів
- `categories.php` - API категорій
- `cities.php` - API міст
- `upload.php` - API завантаження
- `search.php` - API пошуку

---

## 🎨 **СИСТЕМА ТЕМ**

### **`/themes/` - Теми оформлення**
- `header.php` - Верхня частина сайту
- `footer.php` - Нижня частина сайту
- `sidebar.php` - Бічна панель
- `navigation.php` - Навігація
- `breadcrumbs.php` - Хлібні крихти

### **CSS класи та градієнти:**
- 30 градієнтів: `gradient-1` до `gradient-30`
- Темна/світла тема: `theme-dark`, `theme-light`
- Адаптивність: Bootstrap 5
- Іконки: Font Awesome 6
- Анімації: CSS transitions та transforms

---

## 🔒 **БЕЗПЕКА ТА ПРАВА ДОСТУПУ**

### **Ролі користувачів:**
- `user` - Звичайний користувач
- `admin` - Адміністратор
- `super_admin` - Головний адміністратор

### **Типи користувачів:**
- `user` - Розміщує оголошення
- `partner` - Рекламодавець
- `admin` - Адміністратор системи

### **Статуси:**
- `active` - Активний
- `inactive` - Неактивний  
- `banned` - Заблокований

---

## 📊 **СТАТИСТИКА ТА АНАЛІТИКА**

### **Метрики що відслідковуються:**
- Кількість користувачів
- Кількість оголошень
- Перегляди оголошень
- Популярні категорії
- Популярні міста
- Активність користувачів
- Конверсія реєстрацій

### **Звіти що генеруються:**
- Щоденна активність
- Тижневі звіти
- Місячна статистика
- Річні звіти
- Фінансові звіти (для партнерів)

---

## 🌍 **МУЛЬТИМОВНІСТЬ**

### **Підтримувані мови:**
- `uk` - Українська (за замовчуванням)
- `en` - Англійська
- `ru` - Російська

### **Система перекладів:**
- Ключі перекладу зберігаються в `translations`
- Функція `__()` для виводу перекладів
- Автоматичне визначення мови браузера
- Можливість перемикання мови користувачем

---

Ця карта дає повне розуміння структури проекту для роботи з ним! 🚀