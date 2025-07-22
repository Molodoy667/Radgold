# 🎯 AdBoard Pro - Професійна дошка оголошень та рекламна платформа

[![Version](https://img.shields.io/badge/версія-2.1.0-blue.svg)](https://github.com/adboard-pro)
[![PHP](https://img.shields.io/badge/PHP-7.4+-green.svg)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange.svg)](https://mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple.svg)](https://getbootstrap.com)
[![License](https://img.shields.io/badge/ліцензія-MIT-red.svg)](LICENSE)

**AdBoard Pro** - це сучасна, повнофункціональна система для створення дошки оголошень з інтегрованою рекламною платформою. Ідеально підходить для створення сайтів типу OLX, Prom.ua, або локальних дощок оголошень.

---

## 🚀 **Останні оновлення (v2.1.0)**

### ✨ **Новий функціонал:**
- 🎨 **Детальна сторінка оголошення** з галереєю зображень
- 📱 **Адаптивний дизайн** для всіх пристроїв
- 🖼️ **Система завантаження зображень** з drag & drop
- 💬 **Система улюблених** з AJAX
- 📊 **Розумна пагінація** та фільтрація
- 🔍 **Повнотекстовий пошук** з автодоповненням
- 📈 **Аналітика переглядів** та статистика
- 🎯 **Система категорій** з атрибутами
- 🌍 **Геолокація** з підтримкою карт
- 🔐 **Розширена безпека** з CSRF захистом

### 🛠️ **Технічні поліпшення:**
- ✅ **Виправлений інсталятор** з тестом БД
- 🔧 **Нові AJAX обробники** для динамічного контенту
- 📱 **PWA готовність** для мобільних додатків
- 🎭 **Система тем** з градієнтами та анімаціями
- 📝 **Розширена валідація** форм
- 💾 **Автозбереження** чернеток

---

## 📋 **Основні функції**

### 🏪 **Дошка оголошень:**
- 📝 **Створення оголошень** з багатьма зображеннями
- 🏷️ **Система категорій** з підкатегоріями та атрибутами
- 🔍 **Розширений пошук** з фільтрами
- 📍 **Геолокація** з картами
- ❤️ **Улюблені оголошення**
- 💬 **Система чату** між користувачами
- 📊 **Статистика** переглядів та активності
- 🏆 **Платні послуги** (виділення, закріплення)
- 🔔 **Сповіщення** про нові повідомлення
- 📱 **Мобільна версія**

### 🏢 **Рекламна компанія:**
- 📈 **SMM послуги** (Instagram, TikTok, Facebook)
- 🔍 **SEO просування** сайтів
- 💻 **Створення сайтів** (лендінги, магазини)
- 🎨 **Дизайн та брендинг**
- 💼 **CRM система** для клієнтів
- 📋 **Брифи** та калькулятор вартості
- 📊 **Аналітика** проектів

### 👥 **Система користувачів:**
- 🏠 **Користувацькі кабінети** (users/partners)
- 🔐 **Google авторизація**
- 💰 **Система балансу** та платежів
- ⭐ **Рейтинги** та відгуки
- 📧 **Email сповіщення**
- 🔄 **Автоматичне продовження** оголошень

### 🛡️ **Адміністрування:**
- 📊 **Детальна аналітика** та статистика
- 🔧 **Модерація** оголошень
- 👤 **Управління користувачами**
- 💳 **Фінансові звіти**
- 🔍 **Лог помилок** в реальному часі
- 🔄 **Система оновлень** через адмін панель
- 🎨 **Управління темами** та дизайном

---

## 💻 **Системні вимоги**

- **PHP:** 7.4+ (рекомендуємо 8.0+)
- **MySQL:** 5.7+ або MariaDB 10.3+
- **Apache/Nginx** з mod_rewrite
- **Розширення PHP:**
  - mysqli
  - gd або imagick
  - json
  - mbstring
  - curl
  - fileinfo
- **Диск:** мінімум 500MB вільного місця
- **Пам'ять:** мінімум 256MB PHP memory_limit

---

## 🔧 **Встановлення**

### 1️⃣ **Швидке встановлення:**

```bash
# Завантаження проекту
git clone https://github.com/your-repo/adboard-pro.git
cd adboard-pro

# Налаштування прав доступу
chmod -R 755 .
chmod -R 777 images/uploads images/thumbs images/avatars
chmod -R 777 themes/cache logs

# Створення віртуального хоста (приклад для Apache)
# Вкажіть DocumentRoot на папку проекту
```

### 2️⃣ **Веб-інсталятор:**

1. Відкрийте `http://yoursite.com/install/` в браузері
2. Пройдіть всі кроки майстра встановлення:
   - **Крок 1:** Прийняття ліцензії
   - **Крок 2:** Перевірка системних вимог
   - **Крок 3:** Налаштування бази даних
   - **Крок 4:** Налаштування сайту та адмін акаунта
   - **Крок 5:** Завершення встановлення

3. Видаліть папку `/install/` після успішного встановлення

### 3️⃣ **Ручне налаштування (опціонально):**

```php
// core/config.php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');  
define('DB_NAME', 'your_database');
define('SITE_URL', 'https://yoursite.com');
```

---

## 🎨 **Система тем**

### **Доступні теми:**
- 🌙 **Dark Mode** - темна тема
- ☀️ **Light Mode** - світла тема
- 🌈 **30+ градієнтів** для акцентів

### **Налаштування тем:**
```php
// Зміна теми через адмін панель
// Налаштування → Тема та дизайн → Вибір теми

// Або програматично:
updateSetting('current_theme', 'dark');
updateSetting('current_gradient', 'gradient-blue-purple');
```

### **Створення власної теми:**
```css
/* themes/custom-theme.css */
:root {
    --primary-color: #your-color;
    --theme-bg: #your-bg;
    --theme-text: #your-text;
}
```

---

## 🔗 **API та розширення**

### **AJAX ендпойнти:**
- `ajax/check_auth.php` - перевірка авторизації
- `ajax/toggle_favorite.php` - управління улюбленими
- `ajax/get_category_attributes.php` - атрибути категорій
- `ajax/search_suggestions.php` - автодоповнення пошуку

### **Хуки та фільтри:**
```php
// Додавання власного функціоналу
add_action('after_ad_created', 'your_custom_function');
add_filter('ad_search_results', 'modify_search_results');
```

---

## 📱 **Мобільна версія та PWA**

### **Особливості:**
- 📱 **Responsive дизайн** для всіх пристроїв
- 🚀 **Швидке завантаження** (<3 секунди)
- 📲 **PWA функції** (можна встановити як додаток)
- 🔄 **Offline підтримка** базового функціоналу
- 📍 **Геолокація** для мобільних пристроїв

### **Налаштування PWA:**
```json
// manifest.json
{
  "name": "AdBoard Pro",
  "short_name": "AdBoard",
  "start_url": "/",
  "display": "standalone",
  "theme_color": "#007bff"
}
```

---

## 🔒 **Безпека**

### **Реалізовані заходи:**
- 🛡️ **CSRF токени** для всіх форм
- 🔐 **Хешування паролів** bcrypt
- 🚫 **SQL Injection** захист через prepared statements  
- 🔍 **XSS фільтрація** всіх даних
- 📝 **Валідація** файлів та зображень
- 🚪 **Обмеження доступу** до системних файлів
- 📊 **Логування** підозрілих дій

### **Рекомендації:**
```apache
# .htaccess security headers
Header always set X-Frame-Options DENY
Header always set X-Content-Type-Options nosniff
Header always set X-XSS-Protection "1; mode=block"
Header always set Strict-Transport-Security "max-age=31536000"
```

---

## 📊 **Статистика та аналітика**

### **Доступні метрики:**
- 👁️ **Перегляди оголошень** з деталізацією
- 🔍 **Популярні пошукові запити**
- 📈 **Трафік** по категоріях
- 💰 **Фінансова статистика**
- 👥 **Активність користувачів**
- 📱 **Мобільний/Десктоп** трафік

### **Інтеграція з Google Analytics:**
```javascript
// Google Analytics 4
gtag('config', 'GA_MEASUREMENT_ID', {
    page_title: 'AdBoard Pro',
    page_location: window.location.href
});
```

---

## 🔧 **Налаштування та оптимізація**

### **Продуктивність:**
```php
// Кешування
define('CACHE_ENABLED', true);
define('CACHE_TIME', 3600); // 1 година

// Оптимізація зображень
define('IMAGE_QUALITY', 85);
define('THUMBNAIL_SIZE', 300);
define('MAX_IMAGE_SIZE', 1920);
```

### **SEO налаштування:**
```php
// Мета теги
$metaTags = [
    'title' => 'Дошка оголошень - AdBoard Pro',
    'description' => 'Найкраща дошка оголошень України',
    'keywords' => 'оголошення, купити, продати, послуги',
    'og:image' => '/images/og-image.jpg'
];
```

---

## 🌐 **Багатомовність**

### **Підтримувані мови:**
- 🇺🇦 **Українська** (за замовчуванням)
- 🇷🇺 **Російська**
- 🇬🇧 **Англійська**

### **Додавання нової мови:**
```php
// languages/fr.php
return [
    'hello' => 'Bonjour',
    'welcome' => 'Bienvenue',
    // ... інші переклади
];
```

---

## 📂 **Структура проекту**

```
adboard-pro/
├── 📁 admin/              # Адмін панель
│   ├── dashboard.php      # Головна сторінка
│   ├── ads.php           # Управління оголошеннями
│   ├── users.php         # Користувачі
│   ├── updates.php       # Система оновлень
│   └── error-logs.php    # Лог помилок
├── 📁 ajax/              # AJAX обробники
│   ├── check_auth.php    # Авторизація
│   ├── toggle_favorite.php # Улюблені
│   └── get_category_attributes.php
├── 📁 core/              # Ядро системи
│   ├── config.php        # Конфігурація
│   ├── functions.php     # Основні функції
│   └── database.php      # База даних
├── 📁 themes/            # Теми дизайну
│   ├── header.php        # Шапка сайту
│   ├── footer.php        # Підвал
│   ├── styles.css        # Стилі
│   └── script.js         # JavaScript
├── 📁 pages/             # Сторінки сайту
│   ├── ads.php           # Дошка оголошень
│   ├── ad-detail.php     # Детальна сторінка
│   ├── create-ad.php     # Створення оголошення
│   └── user/             # Користувацькі сторінки
├── 📁 images/            # Зображення
│   ├── uploads/          # Завантажені файли
│   ├── thumbs/          # Мініатюри
│   └── avatars/         # Аватари
├── 📁 install/           # Інсталятор
│   ├── index.php         # Головний файл
│   ├── database.sql      # SQL структура
│   └── steps/           # Кроки встановлення
└── 📁 languages/         # Переклади
    ├── ua.php           # Українська
    ├── ru.php           # Російська
    └── en.php           # Англійська
```

---

## 🔄 **Система оновлень**

### **Автоматичні оновлення:**
1. Завантажте архів оновлення в адмін панелі
2. Система автоматично розпакує файли
3. Застосує зміни до бази даних
4. Покаже звіт про встановлення

### **Ручні оновлення:**
```bash
# Бекап бази даних
mysqldump -u username -p database_name > backup.sql

# Завантаження нових файлів
git pull origin main

# Застосування міграцій
php scripts/migrate.php
```

---

## 🐛 **Виправлення помилок**

### **Загальні проблеми:**

**1. Помилка підключення до БД:**
```php
// Перевірте налаштування в core/config.php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_user');
define('DB_PASS', 'your_password');
define('DB_NAME', 'your_database');
```

**2. Проблеми з завантаженням зображень:**
```bash
# Перевірте права доступу
chmod -R 777 images/uploads
chmod -R 777 images/thumbs
```

**3. Помилки JavaScript:**
- Перевірте підключення jQuery та Bootstrap
- Відкрийте Developer Tools для деталей

### **Лог помилок:**
- Перегляньте `/admin/error-logs.php` в адмін панелі
- Або файл `logs/error.log`

### **Дебагінг системи:**
1. Відкрийте `http://yoursite.com/debug.php` для загальної діагностики
2. Перевірте всі компоненти системи
3. Видаліть `debug.php` на production сервері!

---

## 🤝 **Підтримка та розробка**

### **Отримання допомоги:**
- 📧 **Email:** support@adboard-pro.com
- 💬 **Telegram:** @adboard_support
- 🐛 **GitHub Issues:** [створити тікет](https://github.com/adboard-pro/issues)
- 📚 **Документація:** [docs.adboard-pro.com](https://docs.adboard-pro.com)

### **Участь в розробці:**
```bash
# Форк репозиторію
git fork https://github.com/adboard-pro/adboard-pro

# Створення гілки
git checkout -b feature/new-feature

# Комміт змін
git commit -m "Add new feature"

# Створення Pull Request
```

---

## 🏆 **Ліцензія та використання**

**AdBoard Pro** розповсюджується під ліцензією MIT. Ви можете:
- ✅ Використовувати в комерційних проектах
- ✅ Модифікувати код під свої потреби  
- ✅ Розповсюджувати власні версії
- ✅ Продавати готові рішення

**Умови:**
- 📄 Збережіть копірайт та ліцензію
- 🚫 Ми не несемо відповідальності за збитки
- 💡 Будемо вдячні за зворотний зв'язок

---

## 📈 **Дорожня карта (Roadmap)**

### **v2.2 (Q2 2024):**
- 🗺️ **Інтеграція карт** (Google Maps, OpenStreetMap)
- 💳 **Платіжні системи** (LiqPay, Fondy, WayForPay)
- 📱 **Мобільний додаток** (React Native)
- 🔄 **Real-time чат** (WebSocket)

### **v2.3 (Q3 2024):**
- 🤖 **Штучний інтелект** для модерації
- 📊 **Розширена аналітика**
- 🌍 **Мультирегіональність**
- 📱 **PWA оновлення**

### **v3.0 (Q4 2024):**
- 🏗️ **Мікросервісна архітектура**
- ☁️ **Cloud ready** рішення  
- 🔗 **API для мобільних додатків**
- 🛡️ **Поліпшена безпека**

---

## 🎯 **Висновок**

**AdBoard Pro** - це не просто дошка оголошень, а комплексна платформа для створення успішного онлайн бізнесу. З сучасним дизайном, потужним функціоналом та простотою використання, вона ідеально підходить як для невеликих локальних проектів, так і для масштабних комерційних платформ.

**Почніть вже сьогодні!** 🚀

---

**© 2024 AdBoard Pro. Всі права захищені.**

[![Зірки GitHub](https://img.shields.io/github/stars/adboard-pro/adboard-pro?style=social)](https://github.com/adboard-pro/adboard-pro)
[![Форки](https://img.shields.io/github/forks/adboard-pro/adboard-pro?style=social)](https://github.com/adboard-pro/adboard-pro/fork)
[![Спостерігачі](https://img.shields.io/github/watchers/adboard-pro/adboard-pro?style=social)](https://github.com/adboard-pro/adboard-pro/watchers)
