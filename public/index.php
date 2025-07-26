<?php
declare(strict_types=1);

// Подключаем bootstrap файл для инициализации приложения
$bootstrap = require_once __DIR__ . '/../app/bootstrap.php';

// Инициализация роутера
use App\Core\Router;

try {
    // Получаем подключение к базе данных из bootstrap
    $db = $GLOBALS['db'];
    
    // Запуск роутера
    $router = new Router($db);
    $router->run();
    
} catch (Exception $e) {
    // Обработка критических ошибок
    error_log($e->getMessage());
    
    if (config('debug', false)) {
        echo '<h1>Ошибка приложения</h1>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    } else {
        echo '<h1>Сайт временно недоступен</h1>';
        echo '<p>Попробуйте позже</p>';
    }
}
?>