<?php
namespace App\Core;

class Cache {
    private static $cachePath;
    private static $config;
    
    public static function init() {
        self::$cachePath = dirname(__DIR__, 2) . '/storage/cache/';
        self::$config = require __DIR__ . '/../config/app.php';
        
        if (!is_dir(self::$cachePath)) {
            mkdir(self::$cachePath, 0755, true);
        }
    }
    
    public static function set($key, $value, $ttl = null) {
        self::init();
        
        $ttl = $ttl ?: (self::$config['cache']['ttl'] ?? 3600);
        $expires = time() + $ttl;
        
        $data = [
            'value' => $value,
            'expires' => $expires
        ];
        
        $cacheFile = self::getCacheFile($key);
        return file_put_contents($cacheFile, serialize($data), LOCK_EX);
    }
    
    public static function get($key, $default = null) {
        self::init();
        
        $cacheFile = self::getCacheFile($key);
        
        if (!file_exists($cacheFile)) {
            return $default;
        }
        
        $data = unserialize(file_get_contents($cacheFile));
        
        if (!$data || !isset($data['expires']) || time() > $data['expires']) {
            self::delete($key);
            return $default;
        }
        
        return $data['value'];
    }
    
    public static function has($key) {
        return self::get($key) !== null;
    }
    
    public static function delete($key) {
        self::init();
        
        $cacheFile = self::getCacheFile($key);
        if (file_exists($cacheFile)) {
            return unlink($cacheFile);
        }
        return true;
    }
    
    public static function clear() {
        self::init();
        
        $files = glob(self::$cachePath . '*.cache');
        foreach ($files as $file) {
            unlink($file);
        }
        return true;
    }
    
    public static function remember($key, $callback, $ttl = null) {
        $value = self::get($key);
        
        if ($value !== null) {
            return $value;
        }
        
        $value = $callback();
        self::set($key, $value, $ttl);
        
        return $value;
    }
    
    public static function increment($key, $value = 1) {
        $current = self::get($key, 0);
        $new = $current + $value;
        self::set($key, $new);
        return $new;
    }
    
    public static function decrement($key, $value = 1) {
        return self::increment($key, -$value);
    }
    
    public static function tags($tags) {
        return new CacheTagged($tags);
    }
    
    private static function getCacheFile($key) {
        return self::$cachePath . md5($key) . '.cache';
    }
}

class CacheTagged {
    private $tags;
    
    public function __construct($tags) {
        $this->tags = is_array($tags) ? $tags : [$tags];
    }
    
    public function set($key, $value, $ttl = null) {
        $taggedKey = $this->getTaggedKey($key);
        return Cache::set($taggedKey, $value, $ttl);
    }
    
    public function get($key, $default = null) {
        $taggedKey = $this->getTaggedKey($key);
        return Cache::get($taggedKey, $default);
    }
    
    public function flush() {
        foreach ($this->tags as $tag) {
            $tagFile = Cache::getCachePath() . 'tag_' . md5($tag) . '.json';
            if (file_exists($tagFile)) {
                $keys = json_decode(file_get_contents($tagFile), true) ?: [];
                foreach ($keys as $key) {
                    Cache::delete($key);
                }
                unlink($tagFile);
            }
        }
    }
    
    private function getTaggedKey($key) {
        return implode(':', $this->tags) . ':' . $key;
    }
}