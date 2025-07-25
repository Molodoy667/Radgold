<?php
namespace App\Core;

class Metrics {
    private static $db;
    private static $startTime;
    
    public static function init() {
        if (!self::$db) {
            self::$db = Router::getDb();
        }
        self::$startTime = microtime(true);
    }
    
    public static function startTimer() {
        self::$startTime = microtime(true);
    }
    
    public static function endTimer() {
        return microtime(true) - self::$startTime;
    }
    
    public static function logRequest($uri, $method, $responseTime, $statusCode, $userId = null) {
        self::init();
        
        $sql = "INSERT INTO request_logs (uri, method, response_time, status_code, user_id, ip_address, user_agent, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = self::$db->prepare($sql);
        return $stmt->execute([
            $uri,
            $method,
            $responseTime,
            $statusCode,
            $userId,
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
    }
    
    public static function getRequestStats($period = '24h') {
        self::init();
        
        $timeFilter = self::getTimeFilter($period);
        
        $sql = "SELECT 
                    COUNT(*) as total_requests,
                    AVG(response_time) as avg_response_time,
                    MAX(response_time) as max_response_time,
                    MIN(response_time) as min_response_time,
                    COUNT(CASE WHEN status_code >= 400 THEN 1 END) as error_count,
                    COUNT(CASE WHEN status_code = 200 THEN 1 END) as success_count
                FROM request_logs 
                WHERE created_at >= ?";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute([$timeFilter]);
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    public static function getTopPages($period = '24h', $limit = 10) {
        self::init();
        
        $timeFilter = self::getTimeFilter($period);
        
        $sql = "SELECT 
                    uri,
                    COUNT(*) as requests,
                    AVG(response_time) as avg_response_time,
                    COUNT(CASE WHEN status_code >= 400 THEN 1 END) as errors
                FROM request_logs 
                WHERE created_at >= ?
                GROUP BY uri
                ORDER BY requests DESC
                LIMIT ?";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute([$timeFilter, $limit]);
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public static function getErrorStats($period = '24h') {
        self::init();
        
        $timeFilter = self::getTimeFilter($period);
        
        $sql = "SELECT 
                    status_code,
                    COUNT(*) as count,
                    AVG(response_time) as avg_response_time
                FROM request_logs 
                WHERE created_at >= ? AND status_code >= 400
                GROUP BY status_code
                ORDER BY count DESC";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute([$timeFilter]);
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public static function getDatabaseStats() {
        self::init();
        
        $stats = [];
        
        // Размер базы данных
        $sql = "SELECT 
                    table_schema as db_name,
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'db_size_mb'
                FROM information_schema.tables 
                WHERE table_schema = DATABASE()
                GROUP BY table_schema";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute();
        $stats['database_size'] = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        // Статистика по таблицам
        $sql = "SELECT 
                    table_name,
                    table_rows,
                    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'size_mb'
                FROM information_schema.tables 
                WHERE table_schema = DATABASE()
                ORDER BY (data_length + index_length) DESC";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute();
        $stats['tables'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        return $stats;
    }
    
    public static function getSystemStats() {
        $stats = [];
        
        // Использование памяти
        $stats['memory'] = [
            'used' => memory_get_usage(true),
            'peak' => memory_get_peak_usage(true),
            'limit' => ini_get('memory_limit')
        ];
        
        // Время выполнения
        $stats['execution_time'] = self::endTimer();
        
        // Загрузка CPU
        if (function_exists('sys_getloadavg')) {
            $stats['load_average'] = sys_getloadavg();
        }
        
        // Дисковое пространство
        $stats['disk'] = [
            'free' => disk_free_space('/'),
            'total' => disk_total_space('/')
        ];
        
        return $stats;
    }
    
    public static function getBusinessMetrics($period = '24h') {
        self::init();
        
        $timeFilter = self::getTimeFilter($period);
        
        $sql = "SELECT 
                    COUNT(DISTINCT u.id) as new_users,
                    COUNT(DISTINCT p.id) as new_products,
                    COUNT(DISTINCT pur.id) as new_purchases,
                    SUM(pur.price) as total_revenue,
                    COUNT(DISTINCT r.id) as new_reviews
                FROM users u
                LEFT JOIN products p ON p.created_at >= ?
                LEFT JOIN purchases pur ON pur.created_at >= ?
                LEFT JOIN reviews r ON r.created_at >= ?
                WHERE u.created_at >= ?";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute([$timeFilter, $timeFilter, $timeFilter, $timeFilter]);
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    public static function getActiveUsers($period = '24h') {
        self::init();
        
        $timeFilter = self::getTimeFilter($period);
        
        $sql = "SELECT COUNT(DISTINCT user_id) as active_users 
                FROM request_logs 
                WHERE created_at >= ? AND user_id IS NOT NULL";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute([$timeFilter]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $result['active_users'] ?? 0;
    }
    
    public static function getCacheStats() {
        $stats = [];
        
        $cachePath = dirname(__DIR__, 2) . '/storage/cache/';
        
        if (is_dir($cachePath)) {
            $files = glob($cachePath . '*.cache');
            $stats['cache_files'] = count($files);
            
            $totalSize = 0;
            foreach ($files as $file) {
                $totalSize += filesize($file);
            }
            $stats['cache_size'] = $totalSize;
        }
        
        return $stats;
    }
    
    public static function cleanupOldLogs($days = 30) {
        self::init();
        
        $sql = "DELETE FROM request_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)";
        $stmt = self::$db->prepare($sql);
        
        return $stmt->execute([$days]);
    }
    
    public static function generateReport($period = '24h') {
        $report = [
            'period' => $period,
            'timestamp' => date('Y-m-d H:i:s'),
            'request_stats' => self::getRequestStats($period),
            'top_pages' => self::getTopPages($period),
            'error_stats' => self::getErrorStats($period),
            'database_stats' => self::getDatabaseStats(),
            'system_stats' => self::getSystemStats(),
            'business_metrics' => self::getBusinessMetrics($period),
            'active_users' => self::getActiveUsers($period),
            'cache_stats' => self::getCacheStats()
        ];
        
        return $report;
    }
    
    private static function getTimeFilter($period) {
        switch ($period) {
            case '1h':
                return date('Y-m-d H:i:s', strtotime('-1 hour'));
            case '6h':
                return date('Y-m-d H:i:s', strtotime('-6 hours'));
            case '24h':
                return date('Y-m-d H:i:s', strtotime('-24 hours'));
            case '7d':
                return date('Y-m-d H:i:s', strtotime('-7 days'));
            case '30d':
                return date('Y-m-d H:i:s', strtotime('-30 days'));
            default:
                return date('Y-m-d H:i:s', strtotime('-24 hours'));
        }
    }
    
    public static function logPerformance($operation, $duration, $details = []) {
        self::init();
        
        $sql = "INSERT INTO performance_logs (operation, duration, details, created_at) 
                VALUES (?, ?, ?, NOW())";
        
        $stmt = self::$db->prepare($sql);
        return $stmt->execute([
            $operation,
            $duration,
            json_encode($details, JSON_UNESCAPED_UNICODE)
        ]);
    }
    
    public static function getPerformanceStats($operation = null, $period = '24h') {
        self::init();
        
        $timeFilter = self::getTimeFilter($period);
        
        $sql = "SELECT 
                    operation,
                    COUNT(*) as count,
                    AVG(duration) as avg_duration,
                    MAX(duration) as max_duration,
                    MIN(duration) as min_duration
                FROM performance_logs 
                WHERE created_at >= ?";
        
        $params = [$timeFilter];
        
        if ($operation) {
            $sql .= " AND operation = ?";
            $params[] = $operation;
        }
        
        $sql .= " GROUP BY operation ORDER BY avg_duration DESC";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}