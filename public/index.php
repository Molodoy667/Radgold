<?php
// Устанавливаем кодировку
header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/core/Router.php';

use App\Core\Router;

session_start();

$router = new Router();
$router->run();