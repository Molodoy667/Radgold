<?php
namespace App\Core;

class Router {
    public function run() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];
        
        $db = $this->getDb();
        
        // Главная страница
        if ($uri === '/') {
            header('Location: /products');
            return;
        }
        
        // Авторизация
        if ($uri === '/login') {
            require_once __DIR__ . '/../views/auth/login.php';
            return;
        }
        
        if ($uri === '/auth/login' && $method === 'POST') {
            require_once __DIR__ . '/../controllers/AuthController.php';
            $controller = new \App\Controllers\AuthController();
            $controller->login($db);
            return;
        }
        
        if ($uri === '/auth/logout') {
            require_once __DIR__ . '/../controllers/AuthController.php';
            $controller = new \App\Controllers\AuthController();
            $controller->logout();
            return;
        }
        
        // Каталог товаров
        if ($uri === '/products') {
            require_once __DIR__ . '/../controllers/ProductController.php';
            $controller = new \App\Controllers\ProductController();
            $controller->index($db);
            return;
        }
        
        // Фильтрация товаров
        if ($uri === '/products/filter') {
            require_once __DIR__ . '/../controllers/ProductController.php';
            $controller = new \App\Controllers\ProductController();
            $controller->filter($db);
            return;
        }
        
        // Просмотр товара
        if (preg_match('/^\/products\/(\d+)$/', $uri, $matches)) {
            require_once __DIR__ . '/../controllers/ProductController.php';
            $controller = new \App\Controllers\ProductController();
            $controller->show($matches[1], $db);
            return;
        }
        
        // 404
        header('HTTP/1.0 404 Not Found');
        echo '<h1>404 - Страница не найдена</h1>';
        echo '<p><a href="/products">Вернуться к каталогу</a></p>';
    }
    
    private function getDb() {
        $config = require __DIR__ . '/../config/database.php';
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        return new \PDO($dsn, $config['user'], $config['password'], $config['options'] ?? []);
    }
}