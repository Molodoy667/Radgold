<?php
namespace App\Core;

class RateLimiter {
    private static $cachePath;
    
    public static function init() {
        self::$cachePath = dirname(__DIR__, 2) . '/storage/cache/rate_limit/';
        if (!is_dir(self::$cachePath)) {
            mkdir(self::$cachePath, 0755, true);
        }
    }
    
    public static function check($key, $maxRequests = 60, $window = 60) {
        self::init();
        
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $cacheKey = md5($key . '_' . $ip);
        $cacheFile = self::$cachePath . $cacheKey . '.json';
        
        $now = time();
        $windowStart = $now - $window;
        
        if (file_exists($cacheFile)) {
            $data = json_decode(file_get_contents($cacheFile), true);
            if ($data && isset($data['requests'])) {
                // Удаляем старые запросы
                $data['requests'] = array_filter($data['requests'], function($timestamp) use ($windowStart) {
                    return $timestamp > $windowStart;
                });
                
                if (count($data['requests']) >= $maxRequests) {
                    return false; // Превышен лимит
                }
            } else {
                $data = ['requests' => []];
            }
        } else {
            $data = ['requests' => []];
        }
        
        // Добавляем текущий запрос
        $data['requests'][] = $now;
        file_put_contents($cacheFile, json_encode($data));
        
        return true;
    }
    
    public static function getRemaining($key, $maxRequests = 60, $window = 60) {
        self::init();
        
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $cacheKey = md5($key . '_' . $ip);
        $cacheFile = self::$cachePath . $cacheKey . '.json';
        
        if (!file_exists($cacheFile)) {
            return $maxRequests;
        }
        
        $data = json_decode(file_get_contents($cacheFile), true);
        if (!$data || !isset($data['requests'])) {
            return $maxRequests;
        }
        
        $now = time();
        $windowStart = $now - $window;
        
        $validRequests = array_filter($data['requests'], function($timestamp) use ($windowStart) {
            return $timestamp > $windowStart;
        });
        
        return max(0, $maxRequests - count($validRequests));
    }
    
    public static function clear($key) {
        self::init();
        
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $cacheKey = md5($key . '_' . $ip);
        $cacheFile = self::$cachePath . $cacheKey . '.json';
        
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
    }
}