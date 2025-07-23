# 🚨 СРОЧНОЕ ИСПРАВЛЕНИЕ ПРОБЛЕМ УСТАНОВКИ

## 🔍 **ДИАГНОСТИРОВАННЫЕ ПРОБЛЕМЫ:**

1. ❌ **Объект базы данных не создается** в `core/config.php`
2. ❌ **Функция `getSiteSetting()` вызывается до создания БД**
3. ❌ **JSON ошибки из-за фатальных ошибок PHP**

## ✅ **БЫСТРОЕ РЕШЕНИЕ:**

### 1. **Замените `core/config.php` правильными данными БД:**

```php
<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'iteiyzke_project');
define('DB_PASS', '');
define('DB_NAME', 'iteiyzke_project');

// Site Configuration  
define('SITE_URL', 'https://novado.shop');
define('SITE_NAME', 'AdBoard Pro');

// Остальной код как в исправленном файле...
```

### 2. **Удалите файл установки и начните заново:**

```bash
rm .installed
```

### 3. **Перейдите на диагностику:**

```
https://novado.shop/debug_system.php
```

### 4. **Если все ОК, начните установку:**

```
https://novado.shop/install/
```

## 🔧 **ФАЙЛЫ ИСПРАВЛЕНЫ:**

- ✅ `core/config.php` - исправлено создание объекта БД
- ✅ `core/functions.php` - исправлена функция `getSiteSetting()`  
- ✅ `install/steps/step_8.php` - исправлена JSON обработка
- ✅ `core/classes/Database.php` - создан надежный класс БД

## ⚡ **ВАШИ ДАННЫЕ БД:**

- **Хост:** localhost
- **Пользователь:** iteiyzke_project
- **Пароль:** (пустой)
- **База данных:** iteiyzke_project

## 🎯 **РЕЗУЛЬТАТ:**

После исправлений установка должна пройти без ошибок JSON на 8 этапе!

---

**Если проблемы продолжаются - запустите `debug_system.php` для детальной диагностики.**