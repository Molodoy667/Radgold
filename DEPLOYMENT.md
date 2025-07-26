# 🚀 Инструкция по развертыванию GameMarket Pro

## 📋 Требования к системе

### Минимальные требования:
- **PHP**: 8.0 или выше
- **MySQL**: 8.0 или выше (или MariaDB 10.3+)
- **Веб-сервер**: Apache 2.4+ или Nginx 1.18+
- **Память**: 512 MB RAM (рекомендуется 1 GB+)
- **Место на диске**: 100 MB минимум

### PHP расширения:
```bash
php -m | grep -E "(pdo|json|mbstring|openssl|tokenizer|xml|ctype|fileinfo)"
```

Должны быть установлены:
- `pdo_mysql` - для работы с MySQL
- `json` - для работы с JSON данными
- `mbstring` - для работы с UTF-8
- `openssl` - для шифрования
- `fileinfo` - для загрузки файлов

## 🛠️ Быстрая установка

### 1. Клонирование репозитория
```bash
git clone https://github.com/Molodoy667/Radgold.git gamemarket-pro
cd gamemarket-pro
git checkout playmarket-modern
```

### 2. Настройка веб-сервера

#### Apache (рекомендуется)
```apache
<VirtualHost *:80>
    ServerName gamemarket.local
    DocumentRoot /path/to/gamemarket-pro/public
    
    <Directory /path/to/gamemarket-pro/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/gamemarket_error.log
    CustomLog ${APACHE_LOG_DIR}/gamemarket_access.log combined
</VirtualHost>
```

#### Nginx
```nginx
server {
    listen 80;
    server_name gamemarket.local;
    root /path/to/gamemarket-pro/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 3. Создание базы данных
```bash
# Войти в MySQL
mysql -u root -p

# Создать базу данных
mysql> CREATE DATABASE gamemarket_pro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
mysql> exit

# Импортировать схему
mysql -u root -p gamemarket_pro < database/schema.sql
```

### 4. Настройка конфигурации
```bash
# Скопировать конфигурацию
cp .env.example .env

# Отредактировать настройки БД
nano app/config/database.php
```

Пример настроек:
```php
return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [
            'host' => 'localhost',
            'database' => 'gamemarket_pro',
            'username' => 'your_username',
            'password' => 'your_password',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ],
    ],
];
```

### 5. Настройка прав доступа
```bash
# Права на папки
chmod -R 755 storage/
chmod -R 755 public/assets/
chmod 644 public/.htaccess

# Для производства
chown -R www-data:www-data .
```

## 🏃‍♂️ Быстрый запуск для разработки

```bash
# Встроенный сервер PHP (только для разработки!)
php -S localhost:8000 -t public

# Открыть в браузере
open http://localhost:8000
```

## 🔐 Тестовые аккаунты

После установки доступны следующие аккаунты:

| Роль | Email | Логин | Пароль | Описание |
|------|-------|--------|---------|----------|
| Администратор | admin@gamemarket.pro | admin | password | Полный доступ к админ-панели |
| Продавец | seller1@test.com | pro_seller | password | Премиум продавец с рейтингом 4.8 |
| Покупатель | buyer1@test.com | gamer_buyer | password | Обычный пользователь |
| Продавец Pro | seller2@test.com | boost_master | password | Pro продавец с рейтингом 4.9 |

## 🗄️ Структура базы данных

Проект использует 13 основных таблиц:

### Основные таблицы:
- `users` - Пользователи системы
- `products` - Товары и услуги
- `purchases` - Покупки
- `rentals` - Аренда аккаунтов

### Коммуникации:
- `chat_messages` - Сообщения чата
- `notifications` - Уведомления
- `reviews` - Отзывы
- `disputes` - Споры

### Система:
- `favorites` - Избранное
- `settings` - Настройки системы
- `activity_logs` - Логи активности
- `balance_transactions` - Транзакции
- `coupons` - Промокоды

## 🔧 Конфигурация для продакшена

### 1. Обновить настройки
```php
// app/config/app.php
'debug' => false,
'app_url' => 'https://your-domain.com',
```

### 2. Настроить HTTPS
```bash
# Получить SSL сертификат (Let's Encrypt)
certbot --apache -d your-domain.com
```

### 3. Оптимизация
```bash
# Сжатие CSS/JS (можно добавить в build процесс)
# Настройка кеширования в .htaccess уже добавлена
# Настройка логирования ошибок
```

### 4. Безопасность
- Изменить все дефолтные пароли
- Настроить файрвол
- Регулярно обновлять PHP и MySQL
- Настроить бэкапы БД

## 📊 Мониторинг и логи

### Логи ошибок:
- Apache: `/var/log/apache2/gamemarket_error.log`
- PHP: проверить `php.ini` для `error_log`
- Приложение: `storage/logs/` (нужно создать)

### Мониторинг производительности:
```sql
-- Проверка медленных запросов
SHOW VARIABLES LIKE 'slow_query_log';
SHOW VARIABLES LIKE 'long_query_time';
```

## 🆘 Устранение неполадок

### Проблема: 500 ошибка
```bash
# Проверить логи ошибок
tail -f /var/log/apache2/gamemarket_error.log

# Проверить права доступа
ls -la public/
```

### Проблема: не работает роутинг
```bash
# Проверить mod_rewrite
apache2ctl -M | grep rewrite

# Проверить .htaccess
cat public/.htaccess
```

### Проблема: ошибка подключения к БД
```bash
# Проверить подключение
mysql -u username -p -h localhost gamemarket_pro

# Проверить настройки
cat app/config/database.php
```

## 🔄 Обновление проекта

```bash
# Получить последние изменения
git pull origin playmarket-modern

# Проверить изменения в БД
# (при необходимости выполнить миграции)

# Очистить кеш (если используется)
# rm -rf storage/cache/*
```

## 📈 Масштабирование

### Для высоких нагрузок:
1. **Кеширование**: Redis/Memcached
2. **CDN**: для статических файлов
3. **Балансировка**: Nginx + несколько PHP-FPM
4. **БД**: Master-Slave репликация
5. **Мониторинг**: Prometheus + Grafana

---

**Готово!** 🎉 GameMarket Pro должен работать по адресу `http://gamemarket.local` или `http://localhost:8000`