<?php
// Конфігурація бази даних
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'adboard_site');

// Налаштування сайту
define('SITE_URL', 'http://localhost');
define('SITE_NAME', 'AdBoard Pro');
define('SITE_DESCRIPTION', 'Рекламна компанія та дошка оголошень');
define('SITE_KEYWORDS', 'реклама, оголошення, дошка оголошень, маркетинг');

// Налаштування безпеки
define('SECRET_KEY', 'your_secret_key_here_' . md5(__FILE__));
define('SESSION_NAME', 'adboard_session');

// Налаштування файлів
define('UPLOAD_PATH', 'images/uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB

// Налаштування пагінації
define('ITEMS_PER_PAGE', 12);

// Режим розробки
define('DEBUG_MODE', true);

// Автозавантаження класів
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/classes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Старт сесії
if (session_status() == PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}
?>
