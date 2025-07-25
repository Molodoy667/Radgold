<?php
namespace App\Core;

class Environment {
    private static $loaded = false;
    
    public static function load($path = null) {
        if (self::$loaded) {
            return;
        }
        
        if ($path === null) {
            $path = dirname(__DIR__, 2) . '/.env';
        }
        
        if (!file_exists($path)) {
            return;
        }
        
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            if (strpos($line, '#') === 0) {
                continue; // Пропускаем комментарии
            }
            
            if (strpos($line, '=') !== false) {
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);
                
                // Убираем кавычки
                if (preg_match('/^"(.*)"$/', $value, $matches)) {
                    $value = $matches[1];
                } elseif (preg_match("/^'(.*)'$/", $value, $matches)) {
                    $value = $matches[1];
                }
                
                if (!array_key_exists($name, $_ENV)) {
                    $_ENV[$name] = $value;
                }
                
                if (!array_key_exists($name, $_SERVER)) {
                    $_SERVER[$name] = $value;
                }
            }
        }
        
        self::$loaded = true;
    }
    
    public static function get($key, $default = null) {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }
    
    public static function has($key) {
        return isset($_ENV[$key]) || isset($_SERVER[$key]);
    }
}