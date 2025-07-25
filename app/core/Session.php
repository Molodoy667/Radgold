<?php
namespace App\Core;

class Session {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            // Настройки сессии для безопасности
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            ini_set('session.cookie_secure', $_ENV['SESSION_SECURE'] ?? false);
            
            // Настройки для UTF-8
            ini_set('session.cache_limiter', 'nocache');
            
            session_start();
            
            // Устанавливаем кодировку для сессии
            if (!isset($_SESSION['_charset'])) {
                $_SESSION['_charset'] = 'UTF-8';
            }
        }
    }
    
    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }
    
    public static function get($key, $default = null) {
        self::start();
        return $_SESSION[$key] ?? $default;
    }
    
    public static function has($key) {
        self::start();
        return isset($_SESSION[$key]);
    }
    
    public static function remove($key) {
        self::start();
        unset($_SESSION[$key]);
    }
    
    public static function destroy() {
        self::start();
        session_destroy();
        $_SESSION = [];
    }
    
    public static function regenerate() {
        self::start();
        session_regenerate_id(true);
    }
    
    public static function flash($key, $value = null) {
        self::start();
        
        if ($value !== null) {
            $_SESSION['flash'][$key] = $value;
            return;
        }
        
        $value = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        
        return $value;
    }
    
    public static function hasFlash($key) {
        self::start();
        return isset($_SESSION['flash'][$key]);
    }
}