<?php
namespace App\Core;

class CSRF {
    private static $tokenName = 'csrf_token';
    
    public static function generateToken() {
        if (!Session::has(self::$tokenName)) {
            $token = bin2hex(random_bytes(32));
            Session::set(self::$tokenName, $token);
        }
        return Session::get(self::$tokenName);
    }
    
    public static function verifyToken($token) {
        $storedToken = Session::get(self::$tokenName);
        return $token && $storedToken && hash_equals($storedToken, $token);
    }
    
    public static function getTokenField() {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }
    
    public static function validateRequest() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
            if (!self::verifyToken($token)) {
                http_response_code(403);
                die('CSRF token validation failed');
            }
        }
    }
    
    public static function refreshToken() {
        Session::remove(self::$tokenName);
        return self::generateToken();
    }
}