<?php
namespace App\Core;

class Router {
    public function run() {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        if ($uri === '/login') {
            require_once __DIR__ . '/../views/auth/login.php';
            return;
        }
        if ($uri === '/auth/login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../controllers/AuthController.php';
            $db = $this->getDb();
            $controller = new \App\Controllers\AuthController();
            $controller->login($db);
            return;
        }
        // ... другие роуты ...
        echo '404 Not Found';
    }
    private function getDb() {
        $config = require __DIR__ . '/../config/database.php';
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        return new \PDO($dsn, $config['user'], $config['password']);
    }
}