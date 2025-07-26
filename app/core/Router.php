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
        // Главная страница
        $this->routes['GET']['/'] = ['ProductController', 'index'];
        $this->routes['GET']['/home'] = ['ProductController', 'index'];
        
        // Каталог товаров
        $this->routes['GET']['/catalog'] = ['ProductController', 'catalog'];
        $this->routes['GET']['/products'] = ['ProductController', 'catalog'];
        
        // Отдельный товар
        $this->routes['GET']['/product/{id}'] = ['ProductController', 'show'];
        $this->routes['GET']['/products/{id}'] = ['ProductController', 'show'];
        
        // Управление товарами
        $this->routes['GET']['/products/create'] = ['ProductController', 'createForm'];
        $this->routes['POST']['/products/create'] = ['ProductController', 'create'];
        $this->routes['GET']['/products/{id}/edit'] = ['ProductController', 'editForm'];
        $this->routes['POST']['/products/{id}/update'] = ['ProductController', 'update'];
        $this->routes['DELETE']['/products/{id}'] = ['ProductController', 'delete'];
        
        // Авторизация
        $this->routes['GET']['/login'] = ['AuthController', 'loginForm'];
        $this->routes['POST']['/login'] = ['AuthController', 'login'];
        $this->routes['GET']['/register'] = ['AuthController', 'registerForm'];
        $this->routes['POST']['/register'] = ['AuthController', 'register'];
        $this->routes['POST']['/logout'] = ['AuthController', 'logout'];
        $this->routes['GET']['/logout'] = ['AuthController', 'logout'];
        
        // Пользователи и профили
        $this->routes['GET']['/profile'] = ['UserController', 'profile'];
        $this->routes['GET']['/profile/edit'] = ['UserController', 'editProfile'];
        $this->routes['POST']['/profile/update'] = ['UserController', 'updateProfile'];
        $this->routes['GET']['/my-products'] = ['UserController', 'myProducts'];
        $this->routes['GET']['/my-purchases'] = ['UserController', 'myPurchases'];
        $this->routes['GET']['/my-favorites'] = ['UserController', 'myFavorites'];
        $this->routes['GET']['/user/{id}'] = ['UserController', 'show'];
        
        // Избранное
        $this->routes['POST']['/favorites/add'] = ['FavoriteController', 'add'];
        $this->routes['POST']['/favorites/remove'] = ['FavoriteController', 'remove'];
        $this->routes['POST']['/favorites/toggle'] = ['FavoriteController', 'toggle'];
        
        // Покупки и аренда
        $this->routes['POST']['/purchases/create'] = ['PurchaseController', 'create'];
        $this->routes['GET']['/purchases/{id}'] = ['PurchaseController', 'show'];
        $this->routes['POST']['/rentals/create'] = ['RentalController', 'create'];
        $this->routes['GET']['/rentals/{id}'] = ['RentalController', 'show'];
        
        // Отзывы
        $this->routes['GET']['/reviews/create/{product_id}'] = ['ReviewController', 'createForm'];
        $this->routes['POST']['/reviews/create'] = ['ReviewController', 'create'];
        $this->routes['GET']['/reviews/{id}/edit'] = ['ReviewController', 'editForm'];
        $this->routes['POST']['/reviews/{id}/update'] = ['ReviewController', 'update'];
        $this->routes['DELETE']['/reviews/{id}'] = ['ReviewController', 'delete'];
        
        // Сообщения и чат
        $this->routes['GET']['/messages'] = ['MessageController', 'index'];
        $this->routes['GET']['/messages/{user_id}'] = ['MessageController', 'chat'];
        $this->routes['POST']['/messages/send'] = ['MessageController', 'send'];
        $this->routes['GET']['/messages/unread'] = ['MessageController', 'unread'];
        
        // Споры и жалобы
        $this->routes['GET']['/disputes'] = ['DisputeController', 'index'];
        $this->routes['GET']['/disputes/create/{purchase_id}'] = ['DisputeController', 'createForm'];
        $this->routes['POST']['/disputes/create'] = ['DisputeController', 'create'];
        $this->routes['GET']['/disputes/{id}'] = ['DisputeController', 'show'];
        
        // Админ панель
        $this->routes['GET']['/admin'] = ['AdminController', 'dashboard'];
        $this->routes['GET']['/admin/users'] = ['AdminController', 'users'];
        $this->routes['GET']['/admin/products'] = ['AdminController', 'products'];
        $this->routes['GET']['/admin/reviews'] = ['AdminController', 'reviews'];
        $this->routes['GET']['/admin/disputes'] = ['AdminController', 'disputes'];
        $this->routes['GET']['/admin/settings'] = ['AdminController', 'settings'];
        $this->routes['POST']['/admin/settings'] = ['AdminController', 'updateSettings'];
        
        // API маршруты
        $this->routes['GET']['/api/stats'] = ['ApiController', 'stats'];
        $this->routes['GET']['/api/search'] = ['ApiController', 'search'];
        $this->routes['POST']['/api/upload'] = ['ApiController', 'upload'];
        
        // Статические страницы
        $this->routes['GET']['/about'] = ['PageController', 'about'];
        $this->routes['GET']['/contact'] = ['PageController', 'contact'];
        $this->routes['GET']['/terms'] = ['PageController', 'terms'];
        $this->routes['GET']['/privacy'] = ['PageController', 'privacy'];
        $this->routes['GET']['/help'] = ['PageController', 'help'];
    }
    
    public function run(): void
    {
        $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        
        // Убираем завершающий слэш, кроме корневого пути
        if ($requestUri !== '/' && str_ends_with($requestUri, '/')) {
            $requestUri = rtrim($requestUri, '/');
        }
        
        // Поиск точного совпадения
        if (isset($this->routes[$requestMethod][$requestUri])) {
            $this->executeRoute($this->routes[$requestMethod][$requestUri], []);
            return;
        }
        
        // Поиск динамических маршрутов
        foreach ($this->routes[$requestMethod] ?? [] as $pattern => $handler) {
            $matches = $this->matchRoute($pattern, $requestUri);
            if ($matches !== null) {
                $this->executeRoute($handler, $matches);
                return;
            }
        }
        
        // Маршрут не найден
        $this->show404();
    }
    
    private function matchRoute(string $pattern, string $uri): ?array
    {
        // Преобразуем паттерн в регулярное выражение
        $pattern = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '([^/]+)', $pattern);
        $pattern = str_replace('/', '\/', $pattern);
        $pattern = '/^' . $pattern . '$/';
        
        if (preg_match($pattern, $uri, $matches)) {
            // Убираем полное совпадение из результата
            array_shift($matches);
            return $matches;
        }
        
        return null;
    }
    
    private function executeRoute(array $handler, array $params): void
    {
        [$controllerName, $method] = $handler;
        $controllerClass = "App\\Controllers\\{$controllerName}";
        
        if (!class_exists($controllerClass)) {
            error_log("Controller not found: {$controllerClass}");
            $this->show404();
            return;
        }
        
        $controller = new $controllerClass($this->db);
        
        if (!method_exists($controller, $method)) {
            error_log("Method not found: {$controllerClass}::{$method}");
            $this->show404();
            return;
        }
        
        try {
            // Вызываем метод контроллера с параметрами
            call_user_func_array([$controller, $method], $params);
        } catch (\Exception $e) {
            error_log("Route execution error: " . $e->getMessage());
            $this->show500($e);
        }
    }
    
    private function show404(): void
    {
        http_response_code(404);
        
        // Проверяем, существует ли view для 404
        $view404Path = __DIR__ . '/../views/errors/404.php';
        if (file_exists($view404Path)) {
            $title = 'Страница не найдена';
            ob_start();
            include $view404Path;
            $content = ob_get_clean();
            include __DIR__ . '/../views/layouts/main.php';
        } else {
            // Простая 404 страница
            echo '<h1>404 - Страница не найдена</h1>';
            echo '<p>Запрашиваемая страница не существует.</p>';
            echo '<a href="/">Вернуться на главную</a>';
        }
    }
    
    private function show500(\Exception $e): void
    {
        http_response_code(500);
        
        // В режиме отладки показываем детали ошибки
        $config = require __DIR__ . '/../config/app.php';
        
        if ($config['debug']) {
            echo '<h1>500 - Внутренняя ошибка сервера</h1>';
            echo '<p><strong>Ошибка:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<p><strong>Файл:</strong> ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</p>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        } else {
            echo '<h1>500 - Внутренняя ошибка сервера</h1>';
            echo '<p>Произошла ошибка при обработке запроса.</p>';
            echo '<a href="/">Вернуться на главную</a>';
        }
    }
}