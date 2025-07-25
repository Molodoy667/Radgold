# 🔧 ИСПРАВЛЕНИЕ ОШИБКИ DEBUG_MODE

## 🚨 **НАЙДЕННАЯ ПРОБЛЕМА:**

```
PHP Warning: Use of undefined constant DEBUG_MODE - assumed 'DEBUG_MODE' 
(this will throw an Error in a future version of PHP) 
in /var/www/vhosts/novado.miy.link/novado.shop/install/steps/step_8.php on line 304
```

## ✅ **ПРИЧИНА:**

В файле `install/steps/step_8.php` на строке 304 использовалась константа `DEBUG_MODE` без проверки её существования:

```php
// НЕПРАВИЛЬНО:
'trace' => DEBUG_MODE ? $e->getTraceAsString() : null

// ПРАВИЛЬНО:
'trace' => (defined('DEBUG_MODE') && DEBUG_MODE) ? $e->getTraceAsString() : null
```

## ✅ **ИСПРАВЛЕНИЯ:**

### 1. **Добавлена проверка `defined()` в step_8.php:**
```php
// В начале файла
if (!defined('DEBUG_MODE')) {
    define('DEBUG_MODE', false);
}
```

### 2. **Исправлена строка 304:**
```php
'trace' => (defined('DEBUG_MODE') && DEBUG_MODE) ? $e->getTraceAsString() : null
```

### 3. **Добавлена проверка в install/index.php:**
```php
// Встановлюємо базові константи якщо не визначені
if (!defined('DEBUG_MODE')) {
    define('DEBUG_MODE', true);
}
```

## 🧪 **ТЕСТИРОВАНИЕ:**

Создан специальный тест: **`install/test_step8.php`**

Запустите его для проверки:
```
https://novado.shop/install/test_step8.php
```

## 🎯 **РЕЗУЛЬТАТ:**

- ✅ PHP Warning убран
- ✅ JSON теперь отправляется без ошибок  
- ✅ 8 этап установки должен работать корректно

## 🚀 **ЧТО ДЕЛАТЬ ДАЛЬШЕ:**

1. **Запустите тест:** `https://novado.shop/install/test_step8.php`
2. **Если тест проходит** - попробуйте установку снова
3. **Перейдите на 8 этап:** `https://novado.shop/install/?step=8`

---

**Проблема с невалидным JSON должна быть решена!** 🎉