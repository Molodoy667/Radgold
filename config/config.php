<?php
/**
 * Основная конфигурация сайта
 */

// Настройки сайта
define('SITE_NAME', 'Marketplace');
define('SITE_URL', 'http://localhost');
define('SITE_DESCRIPTION', 'Торговая площадка');
define('SITE_KEYWORDS', 'marketplace, торговля, интернет-магазин');

// Пути
define('ROOT_PATH', dirname(__DIR__));
define('THEME_PATH', ROOT_PATH . '/theme');
define('THEME_URL', SITE_URL . '/theme');
define('IMAGES_PATH', ROOT_PATH . '/images');
define('IMAGES_URL', SITE_URL . '/images');

// Настройки базы данных
$db_config = require_once 'database.php';

// Настройки безопасности
define('CSRF_TOKEN_NAME', 'csrf_token');
define('SESSION_LIFETIME', 3600); // 1 час

// Настройки отладки
define('DEBUG_MODE', true);
define('ERROR_REPORTING', true);

if (ERROR_REPORTING) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}