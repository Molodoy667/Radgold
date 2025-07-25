<?php
namespace App\Core;

class Router {
    private $routes = [
        // Главная страница
        '/' => ['ProductController', 'index'],
        
        // Авторизация
        '/login' => ['AuthController', 'login'],
        '/logout' => ['AuthController', 'logout'],
        '/register' => ['AuthController', 'register'],
        '/auth/login' => ['AuthController', 'login'],
        '/auth/register' => ['AuthController', 'register'],
        
        // Товары
        '/products' => ['ProductController', 'index'],
        '/products/filter' => ['ProductController', 'filter'],
        '/products/create' => ['ProductController', 'create'],
        '/products/buy' => ['ProductController', 'buy'],
        
        // Личный кабинет
        '/profile' => ['UserController', 'profile'],
        '/settings' => ['UserController', 'settings'],
        '/my-products' => ['UserController', 'myProducts'],
        '/my-purchases' => ['UserController', 'myPurchases'],
        '/my-sales' => ['UserController', 'mySales'],
        '/favorites' => ['UserController', 'favorites'],
        '/toggle-favorite' => ['UserController', 'toggleFavorite'],
        
        // Чат
        '/chat' => ['UserController', 'chat'],
        '/conversation' => ['UserController', 'conversation'],
        '/get-messages' => ['UserController', 'getMessages'],
        
        // Диспуты и отзывы
        '/disputes' => ['UserController', 'disputes'],
        '/create-dispute' => ['UserController', 'createDispute'],
        '/reviews' => ['UserController', 'reviews'],
        '/create-review' => ['UserController', 'createReview'],
        
        // Админ панель
        '/admin' => ['AdminController', 'dashboard'],
        '/admin/users' => ['AdminController', 'users'],
        '/admin/products' => ['AdminController', 'products'],
        '/admin/disputes' => ['AdminController', 'disputes'],
        '/admin/reviews' => ['AdminController', 'reviews'],
        '/admin/settings' => ['AdminController', 'settings'],
    ];

    public function run() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];
        
        $db = $this->getDb();
        
        // Обработка статических маршрутов
        if (isset($this->routes[$uri])) {
            $this->executeRoute($this->routes[$uri], $db);
            return;
        }
        
        // Обработка динамических маршрутов
        if (preg_match('/^\/products\/(\d+)$/', $uri, $matches)) {
            $this->executeRoute(['ProductController', 'show'], $db, [$matches[1]]);
            return;
        }
        
        if (preg_match('/^\/conversation\/(\d+)$/', $uri, $matches)) {
            $this->executeRoute(['UserController', 'conversation'], $db, [$matches[1]]);
            return;
        }
        
        if (preg_match('/^\/admin\/users\/(\d+)\/(ban|unban|role)$/', $uri, $matches)) {
            $this->executeRoute(['AdminController', $matches[2]], $db, [$matches[1]]);
            return;
        }
        
        if (preg_match('/^\/admin\/products\/(\d+)\/(approve|reject|ban)$/', $uri, $matches)) {
            $this->executeRoute(['AdminController', $matches[2]], $db, [$matches[1]]);
            return;
        }
        
        // 404
        $this->show404();
    }
    
    private function executeRoute($route, $db, $params = []) {
        list($controllerName, $method) = $route;
        $controllerClass = "App\\Controllers\\{$controllerName}";
        
        if (!class_exists($controllerClass)) {
            $this->show404();
            return;
        }
        
        $controller = new $controllerClass();
        
        if (!method_exists($controller, $method)) {
            $this->show404();
            return;
        }
        
        // Вызываем метод контроллера с параметрами
        if (!empty($params)) {
            call_user_func_array([$controller, $method], array_merge([$db], $params));
        } else {
            $controller->$method($db);
        }
    }
    
    private function show404() {
        header('HTTP/1.0 404 Not Found');
        echo '<h1>404 - Страница не найдена</h1>';
        echo '<p><a href="/products">Вернуться к каталогу</a></p>';
    }
    
    private function getDb() {
        $config = require __DIR__ . '/../config/database.php';
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        return new \PDO($dsn, $config['user'], $config['password']);
    }
}