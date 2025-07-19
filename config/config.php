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
        if (Settings::isMaintenanceMode()) {
            Settings::showMaintenancePage();
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