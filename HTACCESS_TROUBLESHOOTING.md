# 🔧 Устранение проблем с .htaccess

## ❌ **Если появляется ошибка 500:**

### **Шаг 1 - Проверьте основной .htaccess**
Основной файл `.htaccess` был исправлен и теперь включает:
- ✅ Проверки модулей `<IfModule>` для всех директив
- ✅ Современный синтаксис `Require all denied`
- ✅ Безопасные RewriteRule для блокировки PHP

### **Шаг 2 - Если ошибка 500 продолжается**
Замените основной `.htaccess` на минимальную версию:

```bash
# Переименуйте текущий .htaccess
mv .htaccess .htaccess_backup

# Используйте минимальную версию
mv .htaccess_minimal .htaccess
```

### **Шаг 3 - Постепенное восстановление**
Если минимальная версия работает, добавляйте директивы по одной из резервной копии.

---

## 🛡️ **Что было исправлено:**

### **1. Заголовки безопасности**
**Было:**
```apache
Header always set X-XSS-Protection "1; mode=block"
```

**Стало:**
```apache
<IfModule mod_headers.c>
    Header always set X-XSS-Protection "1; mode=block"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-Frame-Options "SAMEORIGIN"
</IfModule>
```

### **2. PHP настройки**
**Было:**
```apache
php_value upload_max_filesize 5M
php_value post_max_size 5M
```

**Стало:**
```apache
<IfModule mod_php7.c>
    php_value upload_max_filesize 5M
    php_value post_max_size 5M
    php_value date.timezone "Europe/Kiev"
</IfModule>

<IfModule mod_php8.c>
    php_value upload_max_filesize 5M
    php_value post_max_size 5M
    php_value date.timezone "Europe/Kiev"
</IfModule>
```

### **3. Блокировка PHP в uploads**
**Было (проблемное):**
```apache
<Directory "assets/uploads">
    php_flag engine off
    Options -ExecCGI
    AddHandler cgi-script .php .pl .py .jsp .asp .sh .cgi
</Directory>
```

**Стало:**
```apache
RewriteCond %{REQUEST_URI} ^/assets/uploads/.*\.(php|phtml|php3|php4|php5|pl|py|jsp|asp|sh|cgi)$ [NC]
RewriteRule ^.*$ - [F,L]
```

### **4. Современный синтаксис доступа**
**Было:**
```apache
<FilesMatch "\.(ini|log|sh|sql|conf)$">
    Order allow,deny
    Deny from all
</FilesMatch>
```

**Стало:**
```apache
<FilesMatch "\.(ini|log|sh|sql|conf|bak|backup)$">
    Require all denied
</FilesMatch>
```

---

## 🔒 **Дополнительная защита uploads/**

Создан отдельный файл `assets/uploads/.htaccess`:
- 🚫 Блокирует все PHP файлы
- ✅ Разрешает только безопасные типы файлов
- 🛡️ Устанавливает правильные MIME типы
- 📵 Отключает выполнение скриптов

---

## 🔍 **Диагностика проблем:**

### **Если ошибка 500 все еще появляется:**

1. **Проверьте логи Apache:**
   ```bash
   tail -f /var/log/apache2/error.log
   ```

2. **Временно отключите .htaccess:**
   ```bash
   mv .htaccess .htaccess_disabled
   ```

3. **Проверьте какие модули доступны:**
   - `mod_rewrite` - для красивых URL
   - `mod_headers` - для заголовков безопасности
   - `mod_expires` - для кеширования
   - `mod_deflate` - для сжатия

4. **Используйте минимальную версию:**
   Файл `.htaccess_minimal` содержит только самые необходимые директивы.

---

## 🎯 **Что точно работает:**

### **Минимальная версия включает:**
- ✅ RewriteEngine On
- ✅ Красивые URL для объявлений
- ✅ Блокировка доступа к config/ и includes/
- ✅ Блокировка PHP в uploads/
- ✅ Отключение индексов папок
- ✅ UTF-8 кодировка
- ✅ Кастомная страница 404

### **Эти директивы безопасны на 99% хостингов.**

---

## 📋 **Инструкция по замене:**

### **Если нужна минимальная версия:**
```bash
# 1. Создайте резервную копию
cp .htaccess .htaccess_full_backup

# 2. Замените на минимальную версию
cp .htaccess_minimal .htaccess

# 3. Проверьте сайт
# Если работает - отлично!
# Если нет - проблема не в .htaccess
```

### **Если хотите восстановить полную версию:**
```bash
# Верните полную версию
cp .htaccess_full_backup .htaccess
```

---

## ✅ **Результат:**

- 🛡️ **Безопасность сохранена** - все блокировки работают
- 🚀 **Совместимость улучшена** - добавлены проверки модулей
- 📱 **URL работают** - красивые ссылки функционируют
- 🔧 **Легкая диагностика** - есть минимальная версия для отладки

**Сайт должен работать без ошибок 500!** 🎉