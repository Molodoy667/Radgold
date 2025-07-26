<?php
/**
 * Bootstrap файл для GameMarket Pro
 * Инициализация приложения и подключение всех необходимых файлов
 */

// Установка кодировки UTF-8
mb_internal_encoding('UTF-8');
header('Content-Type: text/html; charset=UTF-8');

// Обработка ошибок
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Определяем константы путей
define('APP_ROOT', __DIR__ . '/..');
define('APP_PATH', __DIR__);
define('PUBLIC_PATH', APP_ROOT . '/public');
define('CONFIG_PATH', APP_PATH . '/config');

// Подключаем конфигурацию
$appConfig = require CONFIG_PATH . '/app.php';
$dbConfig = require CONFIG_PATH . '/database.php';

// Устанавливаем временную зону
date_default_timezone_set($appConfig['timezone'] ?? 'Europe/Moscow');

// Запуск сессии
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Подключаем основные классы вручную
require_once APP_PATH . '/core/Database.php';
require_once APP_PATH . '/core/Controller.php';
require_once APP_PATH . '/core/Router.php';

// Подключаем модели
require_once APP_PATH . '/models/User.php';
require_once APP_PATH . '/models/Product.php';
require_once APP_PATH . '/models/Favorite.php';
require_once APP_PATH . '/models/Review.php';

// Подключаем контроллеры
require_once APP_PATH . '/controllers/AuthController.php';
require_once APP_PATH . '/controllers/ProductController.php';
require_once APP_PATH . '/controllers/UserController.php';
require_once APP_PATH . '/controllers/FavoriteController.php';

// Глобальные helper функции
if (!function_exists('config')) {
    function config($key = null, $default = null) {
        global $appConfig;
        if ($key === null) {
            return $appConfig;
        }
        return $appConfig[$key] ?? $default;
    }
}

if (!function_exists('dbConfig')) {
    function dbConfig($key = null) {
        global $dbConfig;
        if ($key === null) {
            return $dbConfig;
        }
        return $dbConfig[$key] ?? null;
    }
}

if (!function_exists('url')) {
    function url($path = '') {
        $baseUrl = config('app_url', 'http://localhost:8000');
        return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('redirect')) {
    function redirect($url) {
        if (!headers_sent()) {
            header('Location: ' . $url);
            exit;
        }
        echo "<script>window.location.href = '$url';</script>";
        exit;
    }
}

if (!function_exists('view')) {
    function view($template, $data = []) {
        extract($data);
        $templatePath = APP_PATH . '/views/' . $template . '.php';
        
        if (file_exists($templatePath)) {
            // Если это не layout, то рендерим с layout
            if (strpos($template, 'layouts/') !== 0) {
                // Получаем контент шаблона
                ob_start();
                include $templatePath;
                $content = ob_get_clean();
                
                // Рендерим с main layout
                $layoutPath = APP_PATH . '/views/layouts/main.php';
                if (file_exists($layoutPath)) {
                    include $layoutPath;
                } else {
                    echo $content;
                }
            } else {
                // Это layout, рендерим напрямую
                include $templatePath;
            }
        } else {
            throw new Exception("View template not found: $template");
        }
    }
}

if (!function_exists('sanitize')) {
    function sanitize($input) {
        if (is_array($input)) {
            return array_map('sanitize', $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('verify_csrf')) {
    function verify_csrf($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}

// Проверим подключение к базе данных при инициализации
try {
    $database = new App\Core\Database();
    $db = $database->getConnection();
    
    // Сохраняем соединение для глобального доступа
    $GLOBALS['db'] = $db;
    
} catch (Exception $e) {
    // Если база данных недоступна, показываем страницу установки
    error_log('Database connection failed: ' . $e->getMessage());
    
    if (config('debug', false)) {
        die('<h1>Ошибка подключения к базе данных</h1><p>' . htmlspecialchars($e->getMessage()) . '</p>');
    } else {
        // Перенаправляем на страницу установки или показываем общую ошибку
        if (file_exists(PUBLIC_PATH . '/install.php')) {
            redirect('/install.php');
        } else {
            die('<h1>Сайт временно недоступен</h1><p>Попробуйте позже</p>');
        }
    }
}

return [
    'app' => $appConfig,
    'db' => $dbConfig,
    'database' => $database ?? null
];
?>