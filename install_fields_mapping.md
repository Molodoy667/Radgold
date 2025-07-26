# Маппінг полів установки AdBoard Pro

## Крок 4: Налаштування сайту
| Поле форми | Назва в $_POST | Поле в site_settings | Константа в config.php |
|------------|----------------|---------------------|------------------------|
| Назва сайту | `site_name` | `site_title` | `SITE_NAME` |
| URL сайту | `site_url` | `site_url` | `SITE_URL` |
| Опис сайту | `site_description` | `site_description` | `SITE_DESCRIPTION` |
| Ключові слова | `site_keywords` | `site_keywords` | - |
| Контактний email | `contact_email` | `contact_email` | `SITE_EMAIL` |

## Крок 5: Додаткові налаштування
| Поле форми | Назва в $_POST | Поле в site_settings | Значення |
|------------|----------------|---------------------|----------|
| Мова за замовчуванням | `default_language` | `language` | uk/ru/en |
| Часовий пояс | `timezone` | `timezone` | Europe/Kiev |
| Анімації | `enable_animations` | `enable_animations` | 1/0 |
| Частинки | `enable_particles` | `enable_particles` | 1/0 |
| Плавна прокрутка | `smooth_scroll` | `smooth_scroll` | 1/0 |
| Підказки | `enable_tooltips` | `enable_tooltips` | 1/0 |

## Крок 6: Налаштування теми
| Поле форми | Назва в $_POST | Поле в site_settings |
|------------|----------------|---------------------|
| Тема за замовчуванням | `default_theme` | `current_theme` |
| Градієнт | `default_gradient` | `current_gradient` |

## Крок 7: Адміністратор
| Поле форми | Назва в $_POST | Таблиця | Поле |
|------------|----------------|---------|------|
| Логін | `admin_login` | `users` | `username` |
| Email | `admin_email` | `users` | `email` |
| Пароль | `admin_password` | `users` | `password` (хешований) |
| Ім'я | `admin_first_name` | `users` | `first_name` |
| Прізвище | `admin_last_name` | `users` | `last_name` |
| - | - | `users` | `role` = 'admin' |
| - | - | `users` | `user_type` = 'admin' |
| - | - | `users` | `group_id` = 1 |
| - | - | `users` | `status` = 'active' |
| - | - | `users` | `email_verified` = 1 |

## Системні налаштування (дефолтні)
| Ключ | Значення | Опис |
|------|----------|------|
| `max_ad_duration_days` | 30 | Максимальна тривалість оголошення |
| `ads_per_page` | 12 | Кількість оголошень на сторінку |
| `auto_approve_ads` | 0 | Автоматичне схвалення оголошень |
| `maintenance_mode` | 0 | Режим обслуговування |
| `available_languages` | ["uk","ru","en"] | Доступні мови |

## Константи config.php
```php
// Основні
define('SITE_URL', $siteConfig['site_url']);
define('SITE_NAME', $siteConfig['site_name']);
define('SITE_EMAIL', $siteConfig['contact_email']);
define('SITE_DESCRIPTION', $siteConfig['site_description']);

// База даних
define('DB_HOST', $dbConfig['host']);
define('DB_USER', $dbConfig['user']);
define('DB_PASS', $dbConfig['pass']);
define('DB_NAME', $dbConfig['name']);

// Безпека
define('SECRET_KEY', 'генерується автоматично');
define('JWT_SECRET', 'генерується автоматично');
```

## Поля що НЕ зберігаються в site_settings:
- ❌ `admin_email` - зберігається в `users.email`
- ❌ `admin_name` - формується з `first_name + last_name` в `users`
- ❌ `admin_password` - хешується та зберігається в `users.password`
- ❌ `admin_password_confirm` - використовується тільки для валідації