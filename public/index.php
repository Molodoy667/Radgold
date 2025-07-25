<?php
// Устанавливаем кодировку
header('Content-Type: text/html; charset=windows-1251');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/core/Router.php';
require_once __DIR__ . '/../app/config/database.php';

use App\Core\Router;

session_start();

$router = new Router();
$router->run();