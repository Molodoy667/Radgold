<?php
require_once '../core/config.php';
require_once '../core/functions.php';

// Перевірка авторизації адміністратора
if (!isLoggedIn() || !isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Доступ заборонено']);
    exit();
}

header('Content-Type: application/json');

try {
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    
    switch ($action) {
        case 'dashboard_stats':
            echo json_encode(getDashboardStats());
            break;
            
        case 'recent_activity':
            $limit = (int)($_GET['limit'] ?? 10);
            echo json_encode(getRecentActivity($limit));
            break;
            
        case 'system_health':
            echo json_encode(getSystemHealth());
            break;
            
        case 'chart_data':
            $type = $_GET['type'] ?? 'users';
            $period = $_GET['period'] ?? '7days';
            echo json_encode(getChartData($type, $period));
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Невідома дія']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function getDashboardStats() {
    try {
        $db = Database::getInstance();
        
        // Загальна статистика
        $stats = [];
        
        // Користувачі
        $result = $db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR) THEN 1 ELSE 0 END) as today,
                SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as week
            FROM users
        ");
        $stats['users'] = $result->fetch_assoc();
        
        // Оголошення
        $result = $db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR) THEN 1 ELSE 0 END) as today
            FROM ads
        ");
        $stats['ads'] = $result->fetch_assoc();
        
        // Категорії
        $result = $db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active
            FROM categories
        ");
        $stats['categories'] = $result->fetch_assoc();
        
        // Перегляди (за сьогодні)
        $result = $db->query("
            SELECT COUNT(*) as total
            FROM ad_views 
            WHERE DATE(created_at) = CURDATE()
        ");
        $stats['views_today'] = $result->fetch_assoc()['total'];
        
        // Пошукові запити (за тиждень)
        $result = $db->query("
            SELECT COUNT(*) as total
            FROM search_queries 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        ");
        $stats['searches_week'] = $result->fetch_assoc()['total'];
        
        // Топ категорії
        $result = $db->query("
            SELECT c.name, COUNT(a.id) as ads_count
            FROM categories c
            LEFT JOIN ads a ON c.id = a.category_id AND a.status = 'active'
            GROUP BY c.id, c.name
            ORDER BY ads_count DESC
            LIMIT 5
        ");
        $stats['top_categories'] = $result->fetch_all(MYSQLI_ASSOC);
        
        // Топ міста
        $result = $db->query("
            SELECT l.name, COUNT(a.id) as ads_count
            FROM locations l
            LEFT JOIN ads a ON l.id = a.location_id AND a.status = 'active'
            GROUP BY l.id, l.name
            ORDER BY ads_count DESC
            LIMIT 5
        ");
        $stats['top_locations'] = $result->fetch_all(MYSQLI_ASSOC);
        
        return [
            'success' => true,
            'data' => $stats,
            'timestamp' => time()
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function getRecentActivity($limit = 10) {
    try {
        $db = Database::getInstance();
        
        $activities = [];
        
        // Нові користувачі
        $result = $db->query("
            SELECT 'user' as type, id, username as title, created_at
            FROM users 
            ORDER BY created_at DESC 
            LIMIT " . ($limit / 2)
        );
        while ($row = $result->fetch_assoc()) {
            $row['icon'] = 'fa-user';
            $row['color'] = 'primary';
            $row['description'] = 'Новий користувач зареєструвався';
            $activities[] = $row;
        }
        
        // Нові оголошення
        $result = $db->query("
            SELECT 'ad' as type, a.id, a.title, a.created_at, u.username as user
            FROM ads a
            JOIN users u ON a.user_id = u.id
            ORDER BY a.created_at DESC 
            LIMIT " . ($limit / 2)
        );
        while ($row = $result->fetch_assoc()) {
            $row['icon'] = 'fa-bullhorn';
            $row['color'] = 'success';
            $row['description'] = "Оголошення від {$row['user']}";
            $activities[] = $row;
        }
        
        // Сортуємо за датою
        usort($activities, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return [
            'success' => true,
            'data' => array_slice($activities, 0, $limit)
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function getSystemHealth() {
    try {
        $health = [];
        
        // Перевірка диску
        $diskTotal = disk_total_space('.');
        $diskFree = disk_free_space('.');
        $diskUsed = $diskTotal - $diskFree;
        $diskUsagePercent = round(($diskUsed / $diskTotal) * 100, 1);
        
        $health['disk'] = [
            'usage_percent' => $diskUsagePercent,
            'free_space' => formatBytes($diskFree),
            'total_space' => formatBytes($diskTotal),
            'status' => $diskUsagePercent > 90 ? 'danger' : ($diskUsagePercent > 80 ? 'warning' : 'success')
        ];
        
        // Перевірка пам'яті PHP
        $memoryUsage = memory_get_usage(true);
        $memoryLimit = ini_get('memory_limit');
        $memoryLimitBytes = parseBytes($memoryLimit);
        $memoryPercent = round(($memoryUsage / $memoryLimitBytes) * 100, 1);
        
        $health['memory'] = [
            'usage_percent' => $memoryPercent,
            'current_usage' => formatBytes($memoryUsage),
            'limit' => $memoryLimit,
            'status' => $memoryPercent > 90 ? 'danger' : ($memoryPercent > 80 ? 'warning' : 'success')
        ];
        
        // Перевірка версій
        $health['php'] = [
            'version' => PHP_VERSION,
            'status' => version_compare(PHP_VERSION, '7.4.0', '>=') ? 'success' : 'warning'
        ];
        
        // Перевірка бази даних
        try {
            $db = Database::getInstance();
            $result = $db->query("SELECT VERSION() as version");
            $mysqlVersion = $result->fetch_assoc()['version'];
            
            $health['mysql'] = [
                'version' => $mysqlVersion,
                'status' => 'success'
            ];
        } catch (Exception $e) {
            $health['mysql'] = [
                'version' => 'Помилка підключення',
                'status' => 'danger',
                'error' => $e->getMessage()
            ];
        }
        
        // Перевірка директорій
        $directories = ['images/uploads', 'images/thumbs', 'images/avatars', 'logs'];
        $health['directories'] = [];
        
        foreach ($directories as $dir) {
            $health['directories'][$dir] = [
                'writable' => is_writable($dir),
                'exists' => is_dir($dir),
                'status' => (is_dir($dir) && is_writable($dir)) ? 'success' : 'danger'
            ];
        }
        
        return [
            'success' => true,
            'data' => $health,
            'timestamp' => time()
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function getChartData($type, $period) {
    try {
        $db = Database::getInstance();
        
        // Визначаємо період
        switch ($period) {
            case '24hours':
                $dateFormat = '%H:00';
                $groupBy = 'HOUR(created_at)';
                $interval = '24 HOUR';
                break;
            case '7days':
                $dateFormat = '%Y-%m-%d';
                $groupBy = 'DATE(created_at)';
                $interval = '7 DAY';
                break;
            case '30days':
                $dateFormat = '%Y-%m-%d';
                $groupBy = 'DATE(created_at)';
                $interval = '30 DAY';
                break;
            default:
                $dateFormat = '%Y-%m-%d';
                $groupBy = 'DATE(created_at)';
                $interval = '7 DAY';
        }
        
        $data = [];
        
        switch ($type) {
            case 'users':
                $result = $db->query("
                    SELECT 
                        DATE_FORMAT(created_at, '$dateFormat') as label,
                        COUNT(*) as value
                    FROM users 
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL $interval)
                    GROUP BY $groupBy
                    ORDER BY created_at ASC
                ");
                break;
                
            case 'ads':
                $result = $db->query("
                    SELECT 
                        DATE_FORMAT(created_at, '$dateFormat') as label,
                        COUNT(*) as value
                    FROM ads 
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL $interval)
                    GROUP BY $groupBy
                    ORDER BY created_at ASC
                ");
                break;
                
            case 'views':
                $result = $db->query("
                    SELECT 
                        DATE_FORMAT(created_at, '$dateFormat') as label,
                        COUNT(*) as value
                    FROM ad_views 
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL $interval)
                    GROUP BY $groupBy
                    ORDER BY created_at ASC
                ");
                break;
                
            default:
                throw new Exception('Невідомий тип графіка');
        }
        
        $chartData = [
            'labels' => [],
            'datasets' => [[
                'label' => ucfirst($type),
                'data' => [],
                'borderColor' => 'rgb(75, 192, 192)',
                'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
            ]]
        ];
        
        while ($row = $result->fetch_assoc()) {
            $chartData['labels'][] = $row['label'];
            $chartData['datasets'][0]['data'][] = (int)$row['value'];
        }
        
        return [
            'success' => true,
            'data' => $chartData
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

function parseBytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    $val = (int)$val;
    
    switch($last) {
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }
    
    return $val;
}
?>