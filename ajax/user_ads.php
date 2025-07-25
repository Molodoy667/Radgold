<?php
require_once '../core/config.php';
require_once '../core/functions.php';

// Перевірка авторизації
if (!isLoggedIn()) {
    http_response_code(403);
    echo json_encode(['error' => 'Доступ заборонено']);
    exit();
}

header('Content-Type: application/json');

try {
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    $userId = $_SESSION['user_id'];
    
    switch ($action) {
        case 'get_my_ads':
            echo json_encode(getMyAds($userId));
            break;
            
        case 'get_ad_stats':
            echo json_encode(getAdStats($userId));
            break;
            
        case 'promote_ad':
            echo json_encode(promoteAd($userId));
            break;
            
        case 'delete_ad':
            echo json_encode(deleteUserAd($userId));
            break;
            
        case 'toggle_ad_status':
            echo json_encode(toggleAdStatus($userId));
            break;
            
        case 'republish_ad':
            echo json_encode(republishAd($userId));
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Невідома дія']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function getMyAds($userId) {
    try {
        $db = new Database();
        
        // Параметри фільтрації та пагінації
        $page = (int)($_GET['page'] ?? 1);
        $limit = (int)($_GET['limit'] ?? 10);
        $status = $_GET['status'] ?? '';
        $search = trim($_GET['search'] ?? '');
        $sort = $_GET['sort'] ?? 'created_at';
        $order = $_GET['order'] ?? 'DESC';
        
        $offset = ($page - 1) * $limit;
        
        // Будуємо WHERE умови
        $whereConditions = ["a.user_id = ?"];
        $params = [$userId];
        $types = 'i';
        
        if (!empty($status)) {
            $whereConditions[] = "a.status = ?";
            $params[] = $status;
            $types .= 's';
        }
        
        if (!empty($search)) {
            $whereConditions[] = "(a.title LIKE ? OR a.description LIKE ?)";
            $searchParam = "%$search%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $types .= 'ss';
        }
        
        $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
        
        // Дозволені поля для сортування
        $allowedSorts = ['id', 'title', 'created_at', 'updated_at', 'status', 'views_count', 'favorites_count', 'expires_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }
        
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';
        
        // Отримуємо загальну кількість
        $countSql = "
            SELECT COUNT(*) as total
            FROM ads a
            $whereClause
        ";
        
        $stmt = $db->prepare($countSql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $totalCount = $stmt->get_result()->fetch_assoc()['total'];
        
        // Отримуємо дані оголошень
        $sql = "
            SELECT 
                a.id, a.title, a.status, a.price, a.currency, a.created_at, a.updated_at,
                a.views_count, a.favorites_count, a.is_featured, a.is_urgent, a.expires_at,
                a.featured_until, a.urgent_until, a.moderation_comment,
                c.name as category_name,
                l.name as location_name,
                (SELECT filename FROM ad_images WHERE ad_id = a.id AND is_main = 1 LIMIT 1) as main_image,
                (SELECT COUNT(*) FROM favorites WHERE ad_id = a.id) as total_favorites
            FROM ads a
            JOIN categories c ON a.category_id = c.id
            LEFT JOIN locations l ON a.location_id = l.id
            $whereClause
            ORDER BY a.$sort $order
            LIMIT ? OFFSET ?
        ";
        
        $params[] = $limit;
        $params[] = $offset;
        $types .= 'ii';
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $ads = [];
        while ($row = $result->fetch_assoc()) {
            // Форматуємо дати
            $row['created_at_formatted'] = date('d.m.Y H:i', strtotime($row['created_at']));
            $row['updated_at_formatted'] = date('d.m.Y H:i', strtotime($row['updated_at']));
            $row['expires_at_formatted'] = $row['expires_at'] ? date('d.m.Y H:i', strtotime($row['expires_at'])) : null;
            
            // Форматуємо ціну
            if ($row['price']) {
                $row['price_formatted'] = number_format($row['price'], 2) . ' ' . $row['currency'];
            } else {
                $row['price_formatted'] = 'Договірна';
            }
            
            // URL головного зображення
            if ($row['main_image']) {
                $row['image_url'] = '/images/uploads/' . $row['main_image'];
                $row['thumb_url'] = '/images/thumbs/' . $row['main_image'];
            } else {
                $row['image_url'] = '/images/no-image.svg';
                $row['thumb_url'] = '/images/no-image.svg';
            }
            
            // Статус бейджи
            $row['status_badge'] = getStatusBadge($row['status']);
            $row['status_text'] = getStatusText($row['status']);
            
            // Перевіряємо активність платних послуг
            $row['is_featured_active'] = $row['is_featured'] && (!$row['featured_until'] || strtotime($row['featured_until']) > time());
            $row['is_urgent_active'] = $row['is_urgent'] && (!$row['urgent_until'] || strtotime($row['urgent_until']) > time());
            
            // Дні до закінчення
            if ($row['expires_at']) {
                $daysLeft = ceil((strtotime($row['expires_at']) - time()) / (24 * 60 * 60));
                $row['days_left'] = max(0, $daysLeft);
                $row['is_expiring_soon'] = $daysLeft <= 3 && $daysLeft > 0;
                $row['is_expired'] = $daysLeft <= 0;
            } else {
                $row['days_left'] = null;
                $row['is_expiring_soon'] = false;
                $row['is_expired'] = false;
            }
            
            $ads[] = $row;
        }
        
        return [
            'success' => true,
            'data' => [
                'ads' => $ads,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => ceil($totalCount / $limit),
                    'total_items' => (int)$totalCount,
                    'per_page' => $limit
                ]
            ]
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function getAdStats($userId) {
    try {
        $db = new Database();
        $adId = (int)($_GET['ad_id'] ?? 0);
        
        if (!$adId) {
            throw new Exception('Невірний ID оголошення');
        }
        
        // Перевіряємо належність оголошення користувачу
        $stmt = $db->prepare("SELECT id FROM ads WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $adId, $userId);
        $stmt->execute();
        
        if (!$stmt->get_result()->fetch_assoc()) {
            throw new Exception('Оголошення не знайдено');
        }
        
        // Статистика переглядів за період
        $period = $_GET['period'] ?? '30days';
        
        switch ($period) {
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
            case '3months':
                $dateFormat = '%Y-%m';
                $groupBy = 'DATE_FORMAT(created_at, "%Y-%m")';
                $interval = '3 MONTH';
                break;
            default:
                $dateFormat = '%Y-%m-%d';
                $groupBy = 'DATE(created_at)';
                $interval = '30 DAY';
        }
        
        // Отримуємо статистику переглядів
        $stmt = $db->prepare("
            SELECT 
                DATE_FORMAT(created_at, '$dateFormat') as date,
                COUNT(*) as views,
                COUNT(DISTINCT ip_address) as unique_views
            FROM ad_views 
            WHERE ad_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL $interval)
            GROUP BY $groupBy
            ORDER BY created_at ASC
        ");
        $stmt->bind_param("i", $adId);
        $stmt->execute();
        $viewsData = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Загальна статистика
        $stmt = $db->prepare("
            SELECT 
                (SELECT COUNT(*) FROM ad_views WHERE ad_id = ?) as total_views,
                (SELECT COUNT(DISTINCT ip_address) FROM ad_views WHERE ad_id = ?) as unique_views,
                (SELECT COUNT(*) FROM ad_views WHERE ad_id = ? AND DATE(created_at) = CURDATE()) as today_views,
                (SELECT COUNT(*) FROM favorites WHERE ad_id = ?) as favorites_count,
                (SELECT COUNT(*) FROM chat_messages WHERE ad_id = ?) as messages_count
        ");
        $stmt->bind_param("iiiii", $adId, $adId, $adId, $adId, $adId);
        $stmt->execute();
        $generalStats = $stmt->get_result()->fetch_assoc();
        
        // Топ джерела трафіку (за реферерами)
        $stmt = $db->prepare("
            SELECT 
                CASE 
                    WHEN user_agent LIKE '%Google%' THEN 'Google'
                    WHEN user_agent LIKE '%Facebook%' THEN 'Facebook'
                    WHEN user_agent LIKE '%Telegram%' THEN 'Telegram'
                    ELSE 'Прямий перехід'
                END as source,
                COUNT(*) as count
            FROM ad_views 
            WHERE ad_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            GROUP BY source
            ORDER BY count DESC
            LIMIT 5
        ");
        $stmt->bind_param("i", $adId);
        $stmt->execute();
        $trafficSources = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        return [
            'success' => true,
            'data' => [
                'views_chart' => $viewsData,
                'general_stats' => $generalStats,
                'traffic_sources' => $trafficSources
            ]
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function promoteAd($userId) {
    try {
        $db = new Database();
        
        $adId = (int)($_POST['ad_id'] ?? 0);
        $serviceType = $_POST['service_type'] ?? '';
        $duration = (int)($_POST['duration'] ?? 7);
        
        if (!$adId || !in_array($serviceType, ['featured', 'urgent', 'top', 'highlight'])) {
            throw new Exception('Невірні параметри просування');
        }
        
        // Перевіряємо належність оголошення
        $stmt = $db->prepare("SELECT id, status FROM ads WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $adId, $userId);
        $stmt->execute();
        $ad = $stmt->get_result()->fetch_assoc();
        
        if (!$ad) {
            throw new Exception('Оголошення не знайдено');
        }
        
        if ($ad['status'] !== 'active') {
            throw new Exception('Можна просувати тільки активні оголошення');
        }
        
        // Отримуємо вартість послуги
        $stmt = $db->prepare("
            SELECT price FROM paid_services 
            WHERE service_type = ? AND is_active = TRUE
            ORDER BY price ASC 
            LIMIT 1
        ");
        $stmt->bind_param("s", $serviceType);
        $stmt->execute();
        $service = $stmt->get_result()->fetch_assoc();
        
        if (!$service) {
            throw new Exception('Послуга не доступна');
        }
        
        $cost = $service['price'];
        
        // Перевіряємо баланс користувача
        $stmt = $db->prepare("SELECT balance FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $userBalance = $stmt->get_result()->fetch_assoc()['balance'] ?? 0;
        
        if ($userBalance < $cost) {
            return [
                'success' => false,
                'error' => 'Недостатньо коштів на балансі',
                'required_amount' => $cost,
                'current_balance' => $userBalance
            ];
        }
        
        // Активуємо послугу
        $endDate = date('Y-m-d H:i:s', strtotime("+{$duration} days"));
        
        $updateFields = [];
        $updateValues = [];
        $updateTypes = '';
        
        switch ($serviceType) {
            case 'featured':
                $updateFields[] = "is_featured = TRUE, featured_until = ?";
                $updateValues[] = $endDate;
                $updateTypes .= 's';
                break;
            case 'urgent':
                $updateFields[] = "is_urgent = TRUE, urgent_until = ?";
                $updateValues[] = $endDate;
                $updateTypes .= 's';
                break;
        }
        
        if (!empty($updateFields)) {
            $sql = "UPDATE ads SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $updateValues[] = $adId;
            $updateTypes .= 'i';
            
            $stmt = $db->prepare($sql);
            $stmt->bind_param($updateTypes, ...$updateValues);
            $stmt->execute();
        }
        
        // Списуємо кошти
        $stmt = $db->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
        $stmt->bind_param("di", $cost, $userId);
        $stmt->execute();
        
        // Записуємо транзакцію
        $stmt = $db->prepare("
            INSERT INTO transactions (user_id, type, amount, description, created_at) 
            VALUES (?, 'expense', ?, ?, NOW())
        ");
        $description = "Просування оголошення: $serviceType";
        $stmt->bind_param("ids", $userId, $cost, $description);
        $stmt->execute();
        
        return [
            'success' => true,
            'message' => 'Оголошення успішно просунуто!',
            'service_type' => $serviceType,
            'duration' => $duration,
            'cost' => $cost,
            'new_balance' => $userBalance - $cost
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function deleteUserAd($userId) {
    try {
        $db = new Database();
        
        $adId = (int)($_POST['ad_id'] ?? 0);
        
        if (!$adId) {
            throw new Exception('Невірний ID оголошення');
        }
        
        // Перевіряємо належність
        $stmt = $db->prepare("SELECT title FROM ads WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $adId, $userId);
        $stmt->execute();
        $ad = $stmt->get_result()->fetch_assoc();
        
        if (!$ad) {
            throw new Exception('Оголошення не знайдено');
        }
        
        // Видаляємо зображення
        $stmt = $db->prepare("SELECT filename FROM ad_images WHERE ad_id = ?");
        $stmt->bind_param("i", $adId);
        $stmt->execute();
        $images = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        foreach ($images as $image) {
            $imagePath = '../images/uploads/' . $image['filename'];
            $thumbPath = '../images/thumbs/' . $image['filename'];
            
            if (file_exists($imagePath)) unlink($imagePath);
            if (file_exists($thumbPath)) unlink($thumbPath);
        }
        
        // Видаляємо оголошення
        $stmt = $db->prepare("DELETE FROM ads WHERE id = ?");
        $stmt->bind_param("i", $adId);
        
        if (!$stmt->execute()) {
            throw new Exception('Помилка видалення оголошення');
        }
        
        return [
            'success' => true,
            'message' => 'Оголошення успішно видалено'
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function toggleAdStatus($userId) {
    try {
        $db = new Database();
        
        $adId = (int)($_POST['ad_id'] ?? 0);
        $newStatus = $_POST['status'] ?? '';
        
        if (!$adId || !in_array($newStatus, ['active', 'inactive', 'sold'])) {
            throw new Exception('Невірні параметри');
        }
        
        // Перевіряємо належність
        $stmt = $db->prepare("SELECT status FROM ads WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $adId, $userId);
        $stmt->execute();
        $ad = $stmt->get_result()->fetch_assoc();
        
        if (!$ad) {
            throw new Exception('Оголошення не знайдено');
        }
        
        // Оновлюємо статус
        $stmt = $db->prepare("UPDATE ads SET status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("si", $newStatus, $adId);
        
        if (!$stmt->execute()) {
            throw new Exception('Помилка оновлення статусу');
        }
        
        $statusText = [
            'active' => 'активовано',
            'inactive' => 'деактивовано',
            'sold' => 'позначено як продано'
        ];
        
        return [
            'success' => true,
            'message' => 'Оголошення ' . ($statusText[$newStatus] ?? 'оновлено'),
            'new_status' => $newStatus
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function republishAd($userId) {
    try {
        $db = new Database();
        
        $adId = (int)($_POST['ad_id'] ?? 0);
        
        if (!$adId) {
            throw new Exception('Невірний ID оголошення');
        }
        
        // Перевіряємо належність
        $stmt = $db->prepare("SELECT status, expires_at FROM ads WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $adId, $userId);
        $stmt->execute();
        $ad = $stmt->get_result()->fetch_assoc();
        
        if (!$ad) {
            throw new Exception('Оголошення не знайдено');
        }
        
        // Оновлюємо дату закінчення та статус
        $newExpiry = date('Y-m-d H:i:s', strtotime('+30 days'));
        
        $stmt = $db->prepare("
            UPDATE ads 
            SET expires_at = ?, updated_at = NOW(), status = 'active' 
            WHERE id = ?
        ");
        $stmt->bind_param("si", $newExpiry, $adId);
        
        if (!$stmt->execute()) {
            throw new Exception('Помилка поновлення оголошення');
        }
        
        return [
            'success' => true,
            'message' => 'Оголошення успішно поновлено на 30 днів',
            'new_expiry' => $newExpiry
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

// Helper functions
function getStatusBadge($status) {
    $badges = [
        'active' => ['class' => 'success', 'text' => 'Активне'],
        'pending' => ['class' => 'warning', 'text' => 'На модерації'],
        'rejected' => ['class' => 'danger', 'text' => 'Відхилено'],
        'expired' => ['class' => 'secondary', 'text' => 'Прострочене'],
        'sold' => ['class' => 'info', 'text' => 'Продано'],
        'inactive' => ['class' => 'dark', 'text' => 'Неактивне'],
        'draft' => ['class' => 'light', 'text' => 'Чернетка']
    ];
    
    return $badges[$status] ?? $badges['inactive'];
}

function getStatusText($status) {
    $texts = [
        'active' => 'Активне',
        'pending' => 'На модерації',
        'rejected' => 'Відхилено',
        'expired' => 'Прострочене',
        'sold' => 'Продано',
        'inactive' => 'Неактивне',
        'draft' => 'Чернетка'
    ];
    
    return $texts[$status] ?? 'Невідомо';
}
?>