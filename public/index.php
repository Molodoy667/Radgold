<?php
// Устанавливаем кодировку
header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/../vendor/autoload.php';

// Загружаем переменные окружения
\App\Core\Environment::load();

// Регистрируем обработчик ошибок
\App\Core\ErrorHandler::register();

// Инициализируем сессию
\App\Core\Session::start();

require_once __DIR__ . '/../app/core/Router.php';

use App\Core\Router;

session_start();

$router = new Router();
$router->run();