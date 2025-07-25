<?php
namespace App\Core;

class HealthCheck {
    private static $db;
    private static $results = [];
    
    public static function init() {
        if (!self::$db) {
            self::$db = Router::getDb();
        }
    }
    
    public static function runAll() {
        self::init();
        
        self::$results = [
            'database' => self::checkDatabase(),
            'file_system' => self::checkFileSystem(),
            'cache' => self::checkCache(),
            'memory' => self::checkMemory(),
            'disk_space' => self::checkDiskSpace(),
            'external_services' => self::checkExternalServices(),
            'security' => self::checkSecurity()
        ];
        
        return self::$results;
    }
    
    public static function checkDatabase() {
        try {
            // Проверяем подключение
            $stmt = self::$db->query("SELECT 1");
            $stmt->fetch();
            
            // Проверяем основные таблицы
            $tables = ['users', 'products', 'purchases', 'reviews', 'favorites'];
            $missingTables = [];
            
            foreach ($tables as $table) {
                $stmt = self::$db->query("SHOW TABLES LIKE '{$table}'");
                if (!$stmt->fetch()) {
                    $missingTables[] = $table;
                }
            }
            
            // Проверяем размер базы данных
            $stmt = self::$db->query("SELECT 
                ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'db_size_mb'
                FROM information_schema.tables 
                WHERE table_schema = DATABASE()");
            $dbSize = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            return [
                'status' => 'healthy',
                'connection' => true,
                'missing_tables' => $missingTables,
                'database_size_mb' => $dbSize['db_size_mb'] ?? 0,
                'message' => empty($missingTables) ? 'Database is healthy' : 'Missing tables: ' . implode(', ', $missingTables)
            ];
            
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'connection' => false,
                'error' => $e->getMessage(),
                'message' => 'Database connection failed'
            ];
        }
    }
    
    public static function checkFileSystem() {
        $checks = [];
        
        // Проверяем права на запись в storage
        $storagePath = dirname(__DIR__, 2) . '/storage/';
        $checks['storage_writable'] = is_writable($storagePath);
        
        // Проверяем поддиректории
        $subdirs = ['uploads', 'logs', 'cache'];
        foreach ($subdirs as $dir) {
            $path = $storagePath . $dir;
            $checks[$dir . '_exists'] = is_dir($path);
            $checks[$dir . '_writable'] = is_writable($path);
        }
        
        // Проверяем .env файл
        $envPath = dirname(__DIR__, 2) . '/.env';
        $checks['env_exists'] = file_exists($envPath);
        $checks['env_readable'] = is_readable($envPath);
        
        $allHealthy = !in_array(false, $checks, true);
        
        return [
            'status' => $allHealthy ? 'healthy' : 'unhealthy',
            'checks' => $checks,
            'message' => $allHealthy ? 'File system is healthy' : 'Some file system checks failed'
        ];
    }
    
    public static function checkCache() {
        $cachePath = dirname(__DIR__, 2) . '/storage/cache/';
        
        if (!is_dir($cachePath)) {
            return [
                'status' => 'unhealthy',
                'message' => 'Cache directory does not exist'
            ];
        }
        
        // Проверяем возможность записи в кэш
        $testFile = $cachePath . 'health_check_' . time() . '.tmp';
        $writeTest = file_put_contents($testFile, 'test');
        
        if ($writeTest !== false) {
            unlink($testFile);
            $writeSuccess = true;
        } else {
            $writeSuccess = false;
        }
        
        // Получаем статистику кэша
        $files = glob($cachePath . '*.cache');
        $cacheSize = 0;
        foreach ($files as $file) {
            $cacheSize += filesize($file);
        }
        
        return [
            'status' => $writeSuccess ? 'healthy' : 'unhealthy',
            'writable' => $writeSuccess,
            'cache_files' => count($files),
            'cache_size_bytes' => $cacheSize,
            'message' => $writeSuccess ? 'Cache is healthy' : 'Cache is not writable'
        ];
    }
    
    public static function checkMemory() {
        $memoryLimit = ini_get('memory_limit');
        $memoryUsage = memory_get_usage(true);
        $peakUsage = memory_get_peak_usage(true);
        
        // Конвертируем лимит в байты
        $limitBytes = self::convertToBytes($memoryLimit);
        $usagePercent = ($memoryUsage / $limitBytes) * 100;
        
        $isHealthy = $usagePercent < 80; // Здорово если используется менее 80%
        
        return [
            'status' => $isHealthy ? 'healthy' : 'warning',
            'memory_limit' => $memoryLimit,
            'current_usage' => $memoryUsage,
            'peak_usage' => $peakUsage,
            'usage_percent' => round($usagePercent, 2),
            'message' => $isHealthy ? 'Memory usage is normal' : 'Memory usage is high'
        ];
    }
    
    public static function checkDiskSpace() {
        $path = dirname(__DIR__, 2);
        $freeSpace = disk_free_space($path);
        $totalSpace = disk_total_space($path);
        $usedSpace = $totalSpace - $freeSpace;
        $usagePercent = ($usedSpace / $totalSpace) * 100;
        
        $isHealthy = $usagePercent < 90; // Здорово если используется менее 90%
        
        return [
            'status' => $isHealthy ? 'healthy' : 'warning',
            'free_space_bytes' => $freeSpace,
            'total_space_bytes' => $totalSpace,
            'used_space_bytes' => $usedSpace,
            'usage_percent' => round($usagePercent, 2),
            'message' => $isHealthy ? 'Disk space is sufficient' : 'Disk space is running low'
        ];
    }
    
    public static function checkExternalServices() {
        $checks = [];
        
        // Проверяем доступность внешних сервисов
        $services = [
            'google' => 'https://www.google.com',
            'github' => 'https://api.github.com'
        ];
        
        foreach ($services as $name => $url) {
            $startTime = microtime(true);
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'method' => 'HEAD'
                ]
            ]);
            
            $headers = @get_headers($url, 1, $context);
            $responseTime = microtime(true) - $startTime;
            
            $checks[$name] = [
                'available' => $headers !== false,
                'response_time' => round($responseTime, 3),
                'status_code' => $headers ? (int)substr($headers[0], 9, 3) : 0
            ];
        }
        
        $allAvailable = !in_array(false, array_column($checks, 'available'), true);
        
        return [
            'status' => $allAvailable ? 'healthy' : 'warning',
            'services' => $checks,
            'message' => $allAvailable ? 'All external services are available' : 'Some external services are unavailable'
        ];
    }
    
    public static function checkSecurity() {
        $checks = [];
        
        // Проверяем HTTPS
        $checks['https'] = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
        
        // Проверяем заголовки безопасности
        $headers = headers_list();
        $securityHeaders = [
            'X-Content-Type-Options' => false,
            'X-Frame-Options' => false,
            'X-XSS-Protection' => false,
            'Strict-Transport-Security' => false
        ];
        
        foreach ($headers as $header) {
            foreach ($securityHeaders as $name => &$found) {
                if (stripos($header, $name) !== false) {
                    $found = true;
                }
            }
        }
        
        $checks['security_headers'] = $securityHeaders;
        
        // Проверяем сессию
        $checks['session_secure'] = ini_get('session.cookie_secure') == 1;
        $checks['session_httponly'] = ini_get('session.cookie_httponly') == 1;
        
        // Проверяем права на файлы
        $envPath = dirname(__DIR__, 2) . '/.env';
        $checks['env_permissions'] = file_exists($envPath) ? (fileperms($envPath) & 0777) : 0;
        
        $allSecure = $checks['https'] && 
                    !in_array(false, $securityHeaders, true) && 
                    $checks['session_secure'] && 
                    $checks['session_httponly'];
        
        return [
            'status' => $allSecure ? 'healthy' : 'warning',
            'checks' => $checks,
            'message' => $allSecure ? 'Security checks passed' : 'Some security issues detected'
        ];
    }
    
    public static function getOverallStatus() {
        $results = self::runAll();
        
        $statuses = array_column($results, 'status');
        $criticalIssues = array_filter($statuses, function($status) {
            return $status === 'unhealthy';
        });
        
        $warnings = array_filter($statuses, function($status) {
            return $status === 'warning';
        });
        
        if (!empty($criticalIssues)) {
            $overallStatus = 'unhealthy';
        } elseif (!empty($warnings)) {
            $overallStatus = 'warning';
        } else {
            $overallStatus = 'healthy';
        }
        
        return [
            'status' => $overallStatus,
            'timestamp' => date('Y-m-d H:i:s'),
            'checks' => $results,
            'summary' => [
                'total_checks' => count($results),
                'healthy' => count(array_filter($statuses, fn($s) => $s === 'healthy')),
                'warnings' => count($warnings),
                'critical' => count($criticalIssues)
            ]
        ];
    }
    
    public static function generateHealthReport() {
        $overall = self::getOverallStatus();
        
        $report = [
            'health_check' => $overall,
            'system_info' => [
                'php_version' => PHP_VERSION,
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'server_time' => date('Y-m-d H:i:s'),
                'timezone' => date_default_timezone_get()
            ],
            'recommendations' => self::generateRecommendations($overall['checks'])
        ];
        
        return $report;
    }
    
    private static function generateRecommendations($checks) {
        $recommendations = [];
        
        if ($checks['database']['status'] === 'unhealthy') {
            $recommendations[] = 'Fix database connection issues';
        }
        
        if ($checks['file_system']['status'] === 'unhealthy') {
            $recommendations[] = 'Check file permissions and storage directory access';
        }
        
        if ($checks['cache']['status'] === 'unhealthy') {
            $recommendations[] = 'Ensure cache directory is writable';
        }
        
        if ($checks['memory']['status'] === 'warning') {
            $recommendations[] = 'Consider increasing memory limit or optimizing code';
        }
        
        if ($checks['disk_space']['status'] === 'warning') {
            $recommendations[] = 'Free up disk space or increase storage';
        }
        
        if ($checks['security']['status'] === 'warning') {
            $recommendations[] = 'Enable HTTPS and configure security headers';
        }
        
        return $recommendations;
    }
    
    private static function convertToBytes($size) {
        $unit = strtolower(substr($size, -1));
        $value = (int)substr($size, 0, -1);
        
        switch ($unit) {
            case 'k':
                return $value * 1024;
            case 'm':
                return $value * 1024 * 1024;
            case 'g':
                return $value * 1024 * 1024 * 1024;
            default:
                return $value;
        }
    }
}