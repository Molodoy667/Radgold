<?php

namespace App\Core;

use PDO;

abstract class Controller
{
    protected PDO $db;
    protected array $config;
    
    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->config = require __DIR__ . '/../config/app.php';
    }

    protected function view(string $template, array $data = []): void
    {
        // Используем глобальную функцию view из bootstrap
        if (function_exists('view')) {
            view($template, $data);
        } else {
            // Fallback если bootstrap не загружен
            extract($data);
            
            // Добавляем общие переменные
            $appName = $this->config['app_name'];
            $user = $_SESSION['user'] ?? null;
            
            // Подключаем шаблон
            $templatePath = __DIR__ . "/../views/{$template}.php";
            
            if (!file_exists($templatePath)) {
                throw new \Exception("Template not found: {$template}");
            }
            
            require $templatePath;
        }
    }

    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    protected function requireAuth(): void
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }
    }

    protected function requireAdmin(): void
    {
        $this->requireAuth();
        
        if ($_SESSION['user']['role'] !== 'admin') {
            http_response_code(403);
            $this->view('errors/403');
            exit;
        }
    }

    protected function getInput(): array
    {
        $input = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = $_POST;
            
            // Если это JSON запрос
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (strpos($contentType, 'application/json') !== false) {
                $json = file_get_contents('php://input');
                $input = json_decode($json, true) ?: [];
            }
        } else {
            $input = $_GET;
        }
        
        return $this->sanitizeInput($input);
    }

    private function sanitizeInput(array $input): array
    {
        $sanitized = [];
        
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeInput($value);
            } else {
                $sanitized[$key] = trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
            }
        }
        
        return $sanitized;
    }

    protected function generateCsrfToken(): string
    {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }

    protected function validateCsrfToken(string $token): bool
    {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
}