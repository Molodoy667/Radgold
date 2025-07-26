<?php

namespace App\Core;

use PDO;

class Router
{
    private PDO $db;
    private array $routes = [];
    
    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->defineRoutes();
    }

    private function defineRoutes(): void
    {
        // Главная страница и каталог
        $this->routes = [
            'GET /' => ['ProductController', 'index'],
            'GET /catalog' => ['ProductController', 'catalog'],
            'GET /product/{id}' => ['ProductController', 'show'],
            
            // Аутентификация
            'GET /login' => ['AuthController', 'loginForm'],
            'POST /login' => ['AuthController', 'login'],
            'GET /register' => ['AuthController', 'registerForm'],
            'POST /register' => ['AuthController', 'register'],
            'POST /logout' => ['AuthController', 'logout'],
            
            // Личный кабинет
            'GET /profile' => ['UserController', 'profile'],
            'GET /my-products' => ['UserController', 'myProducts'],
            'GET /my-purchases' => ['UserController', 'myPurchases'],
            'GET /favorites' => ['UserController', 'favorites'],
            'GET /settings' => ['UserController', 'settings'],
            
            // Управление товарами
            'GET /products/create' => ['ProductController', 'createForm'],
            'POST /products/create' => ['ProductController', 'create'],
            'GET /products/{id}/edit' => ['ProductController', 'editForm'],
            'POST /products/{id}/edit' => ['ProductController', 'update'],
            'POST /products/{id}/delete' => ['ProductController', 'delete'],
            
            // Покупки и аренда
            'POST /products/{id}/buy' => ['PurchaseController', 'buy'],
            'POST /products/{id}/rent' => ['RentalController', 'rent'],
            
            // Избранное
            'POST /favorites/toggle' => ['FavoriteController', 'toggle'],
            
            // Чат и сообщения
            'GET /messages' => ['MessageController', 'index'],
            'GET /messages/{userId}' => ['MessageController', 'conversation'],
            'POST /messages/send' => ['MessageController', 'send'],
            
            // Отзывы
            'POST /reviews/create' => ['ReviewController', 'create'],
            
            // Споры
            'GET /disputes' => ['DisputeController', 'index'],
            'POST /disputes/create' => ['DisputeController', 'create'],
            
            // API эндпоинты
            'GET /api/products/filter' => ['Api\\ProductController', 'filter'],
            'GET /api/messages/unread' => ['Api\\MessageController', 'unread'],
            
            // Админ панель
            'GET /admin' => ['AdminController', 'dashboard'],
            'GET /admin/users' => ['AdminController', 'users'],
            'GET /admin/products' => ['AdminController', 'products'],
            'GET /admin/disputes' => ['AdminController', 'disputes'],
            'POST /admin/users/{id}/ban' => ['AdminController', 'banUser'],
            'POST /admin/products/{id}/approve' => ['AdminController', 'approveProduct'],
        ];
    }

    public function run(): void
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];
        $routeKey = $method . ' ' . $uri;

        // Поиск прямого совпадения
        if (isset($this->routes[$routeKey])) {
            $this->executeRoute($this->routes[$routeKey], []);
            return;
        }

        // Поиск динамических маршрутов
        foreach ($this->routes as $route => $handler) {
            if ($this->matchRoute($route, $routeKey, $params)) {
                $this->executeRoute($handler, $params);
                return;
            }
        }

        // 404 страница
        $this->show404();
    }

    private function matchRoute(string $pattern, string $route, &$params): bool
    {
        // Конвертируем паттерн в regex
        $pattern = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $pattern);
        $pattern = str_replace('/', '\/', $pattern);
        $pattern = '/^' . $pattern . '$/';

        if (preg_match($pattern, $route, $matches)) {
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            return true;
        }

        return false;
    }

    private function executeRoute(array $handler, array $params): void
    {
        [$controllerName, $method] = $handler;
        $controllerClass = "App\\Controllers\\{$controllerName}";

        if (!class_exists($controllerClass)) {
            $this->show404();
            return;
        }

        $controller = new $controllerClass($this->db);

        if (!method_exists($controller, $method)) {
            $this->show404();
            return;
        }

        // Вызов метода контроллера с параметрами
        call_user_func_array([$controller, $method], $params);
    }

    private function show404(): void
    {
        http_response_code(404);
        require_once __DIR__ . '/../views/errors/404.php';
    }
}