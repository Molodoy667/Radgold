<?php
// Перевіряємо, чи система встановлена
if (!file_exists(__DIR__ . '/installed.lock')) {
    // Якщо ми не в папці install, перенаправляємо туди
    if (!strpos($_SERVER['REQUEST_URI'], '/install/')) {
        header('Location: install/index.php');
        exit();
    }
}

// Підключаємо класи
require_once __DIR__ . '/../includes/settings.php';
require_once __DIR__ . '/../includes/theme.php';

// Базові константи (використовуються якщо БД недоступна)
define('UPLOAD_DIR', 'assets/uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', array('jpg', 'jpeg', 'png', 'gif', 'webp'));

// Ініціалізуємо налаштування після підключення до БД
if (file_exists(__DIR__ . '/installed.lock')) {
    try {
        require_once __DIR__ . '/database.php';
        $database = new Database();
        $db = $database->getConnection();
        
        // Ініціалізуємо налаштування та теми
        Settings::init($db);
        Theme::init($db);
        
        // Перевіряємо режим обслуговування
        if (Settings::isMaintenanceMode() && !isset($_SESSION['admin_logged_in'])) {
            // Показуємо сторінку обслуговування
            showMaintenancePage();
        }
        
    } catch (Exception $e) {
        // Якщо проблема з БД, продовжуємо з стандартними налаштуваннями
        error_log("Config error: " . $e->getMessage());
    }
}

// Стандартні категорії (для сумісності зі старим кодом)
$categories = array(
    1 => 'Транспорт',
    2 => 'Нерухомість',
    3 => 'Електроніка',
    4 => 'Меблі та інтер\'єр',
    5 => 'Одяг та взуття',
    6 => 'Спорт та відпочинок',
    7 => 'Робота',
    8 => 'Послуги',
    9 => 'Дитячі товари',
    10 => 'Інше'
);

/**
 * Функція очищення вводу користувача
 */
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Функція для показу сторінки обслуговування
 */
function showMaintenancePage() {
    $message = Settings::get('maintenance_message', 'Сайт тимчасово недоступний через технічні роботи. Вибачте за незручності.');
    $site_name = Settings::get('site_name', 'Дошка Оголошень');
    
    http_response_code(503);
    header('Retry-After: 3600'); // Повторити через годину
    
    echo "<!DOCTYPE html>
<html lang=\"uk\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>Технічні роботи - {$site_name}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: white;
            text-align: center;
        }
        .maintenance-container {
            max-width: 600px;
            padding: 2rem;
            background: rgba(255,255,255,0.1);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }
        h1 { font-size: 2.5rem; margin-bottom: 1rem; }
        p { font-size: 1.2rem; margin-bottom: 2rem; opacity: 0.9; }
        .icon { font-size: 4rem; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class=\"maintenance-container\">
        <div class=\"icon\">🔧</div>
        <h1>Технічні роботи</h1>
        <p>" . htmlspecialchars($message) . "</p>
        <small>Дякуємо за розуміння!</small>
    </div>
</body>
</html>";
    exit();
}

/**
 * Отримання поточної URL сторінки
 */
function getCurrentUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['REQUEST_URI'];
    return $protocol . $host . $uri;
}

/**
 * Функція для отримання базового URL сайту
 */
function getBaseUrl() {
    if (class_exists('Settings')) {
        return Settings::getSiteUrl();
    }
    
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    return $protocol . $host;
}

// Запускаємо сесію якщо ще не запущена
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>