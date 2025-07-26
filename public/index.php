<?php
declare(strict_types=1);

// Установка кодировки UTF-8
mb_internal_encoding('UTF-8');
header('Content-Type: text/html; charset=UTF-8');

// Обработка ошибок
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Автозагрузка классов
require_once __DIR__ . '/../vendor/autoload.php';

// Запуск сессии
session_start();

// Загрузка конфигурации
$config = require_once __DIR__ . '/../app/config/app.php';

// Инициализация роутера
use App\Core\Router;
use App\Core\Database;

try {
    // Подключение к базе данных
    $database = new Database();
    $db = $database->getConnection();
    
    // Запуск роутера
    $router = new Router($db);
    $router->run();
    
} catch (Exception $e) {
    // Обработка критических ошибок
    error_log($e->getMessage());
    
    if ($config['debug']) {
        echo '<h1>Ошибка приложения</h1>';
        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    } else {
        echo '<h1>Сайт временно недоступен</h1>';
        echo '<p>Попробуйте позже</p>';
    }
}