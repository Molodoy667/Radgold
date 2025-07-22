<?php
require_once '../core/config.php';
require_once '../core/functions.php';

header('Content-Type: application/json');

try {
    if (!isLoggedIn()) {
        throw new Exception('Необхідна авторизація');
    }
    
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    $userId = $_SESSION['user_id'];
    
    switch ($action) {
        case 'get_notifications':
            echo json_encode(getNotifications($userId));
            break;
            
        case 'mark_as_read':
            echo json_encode(markAsRead($userId));
            break;
            
        case 'mark_all_as_read':
            echo json_encode(markAllAsRead($userId));
            break;
            
        case 'delete_notification':
            echo json_encode(deleteNotification($userId));
            break;
            
        case 'get_unread_count':
            echo json_encode(getUnreadCount($userId));
            break;
            
        case 'subscribe_push':
            echo json_encode(subscribePush($userId));
            break;
            
        case 'unsubscribe_push':
            echo json_encode(unsubscribePush($userId));
            break;
            
        case 'test_notification':
            echo json_encode(testNotification($userId));
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Невідома дія']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

/**
 * Отримання сповіщень користувача
 */
function getNotifications($userId) {
    try {
        $db = new Database();
        
        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $stmt = $db->prepare("
            SELECT 
                id, type, title, message, data, is_read, 
                created_at, action_url, icon, priority
            FROM notifications 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT ? OFFSET ?
        ");
        
        $stmt->bind_param("iii", $userId, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $notifications = [];
        while ($row = $result->fetch_assoc()) {
            $notifications[] = [
                'id' => (int)$row['id'],
                'type' => $row['type'],
                'title' => $row['title'],
                'message' => $row['message'],
                'data' => json_decode($row['data'], true),
                'is_read' => (bool)$row['is_read'],
                'created_at' => $row['created_at'],
                'time_ago' => timeAgo($row['created_at']),
                'action_url' => $row['action_url'],
                'icon' => $row['icon'] ?: getDefaultIcon($row['type']),
                'priority' => $row['priority'],
                'color' => getTypeColor($row['type'])
            ];
        }
        
        // Загальна кількість
        $countStmt = $db->prepare("SELECT COUNT(*) as total FROM notifications WHERE user_id = ?");
        $countStmt->bind_param("i", $userId);
        $countStmt->execute();
        $total = $countStmt->get_result()->fetch_assoc()['total'];
        
        return [
            'success' => true,
            'data' => [
                'notifications' => $notifications,
                'pagination' => [
                    'current_page' => $page,
                    'total' => (int)$total,
                    'per_page' => $limit,
                    'has_next' => ($offset + $limit) < $total
                ]
            ]
        ];
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Позначити сповіщення як прочитане
 */
function markAsRead($userId) {
    try {
        $notificationId = (int)($_POST['notification_id'] ?? 0);
        
        if (!$notificationId) {
            throw new Exception('ID сповіщення не вказано');
        }
        
        $db = new Database();
        
        $stmt = $db->prepare("
            UPDATE notifications 
            SET is_read = 1, read_at = NOW() 
            WHERE id = ? AND user_id = ?
        ");
        
        $stmt->bind_param("ii", $notificationId, $userId);
        
        if (!$stmt->execute()) {
            throw new Exception('Помилка оновлення сповіщення');
        }
        
        return [
            'success' => true,
            'message' => 'Сповіщення позначено як прочитане'
        ];
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Позначити всі сповіщення як прочитані
 */
function markAllAsRead($userId) {
    try {
        $db = new Database();
        
        $stmt = $db->prepare("
            UPDATE notifications 
            SET is_read = 1, read_at = NOW() 
            WHERE user_id = ? AND is_read = 0
        ");
        
        $stmt->bind_param("i", $userId);
        
        if (!$stmt->execute()) {
            throw new Exception('Помилка оновлення сповіщень');
        }
        
        $affectedRows = $stmt->affected_rows;
        
        return [
            'success' => true,
            'message' => "Позначено як прочитані: {$affectedRows} сповіщень"
        ];
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Видалення сповіщення
 */
function deleteNotification($userId) {
    try {
        $notificationId = (int)($_POST['notification_id'] ?? 0);
        
        if (!$notificationId) {
            throw new Exception('ID сповіщення не вказано');
        }
        
        $db = new Database();
        
        $stmt = $db->prepare("DELETE FROM notifications WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $notificationId, $userId);
        
        if (!$stmt->execute()) {
            throw new Exception('Помилка видалення сповіщення');
        }
        
        if ($stmt->affected_rows === 0) {
            throw new Exception('Сповіщення не знайдено');
        }
        
        return [
            'success' => true,
            'message' => 'Сповіщення видалено'
        ];
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Кількість непрочитаних сповіщень
 */
function getUnreadCount($userId) {
    try {
        $db = new Database();
        
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        return [
            'success' => true,
            'count' => (int)$result['count']
        ];
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Підписка на push сповіщення
 */
function subscribePush($userId) {
    try {
        $endpoint = $_POST['endpoint'] ?? '';
        $p256dh = $_POST['p256dh'] ?? '';
        $auth = $_POST['auth'] ?? '';
        
        if (empty($endpoint) || empty($p256dh) || empty($auth)) {
            throw new Exception('Неповні дані підписки');
        }
        
        $db = new Database();
        
        // Перевіряємо чи існує підписка
        $stmt = $db->prepare("SELECT id FROM push_subscriptions WHERE user_id = ? AND endpoint = ?");
        $stmt->bind_param("is", $userId, $endpoint);
        $stmt->execute();
        $existing = $stmt->get_result()->fetch_assoc();
        
        if ($existing) {
            return [
                'success' => true,
                'message' => 'Підписка вже існує'
            ];
        }
        
        // Створюємо нову підписку
        $stmt = $db->prepare("
            INSERT INTO push_subscriptions (user_id, endpoint, p256dh, auth, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        
        $stmt->bind_param("isss", $userId, $endpoint, $p256dh, $auth);
        
        if (!$stmt->execute()) {
            throw new Exception('Помилка збереження підписки');
        }
        
        return [
            'success' => true,
            'message' => 'Підписка на push сповіщення активована'
        ];
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Відписка від push сповіщень
 */
function unsubscribePush($userId) {
    try {
        $endpoint = $_POST['endpoint'] ?? '';
        
        if (empty($endpoint)) {
            throw new Exception('Endpoint не вказано');
        }
        
        $db = new Database();
        
        $stmt = $db->prepare("DELETE FROM push_subscriptions WHERE user_id = ? AND endpoint = ?");
        $stmt->bind_param("is", $userId, $endpoint);
        
        if (!$stmt->execute()) {
            throw new Exception('Помилка видалення підписки');
        }
        
        return [
            'success' => true,
            'message' => 'Підписка скасована'
        ];
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Тестове сповіщення
 */
function testNotification($userId) {
    try {
        createNotification(
            $userId,
            'test',
            'Тестове сповіщення',
            'Це тестове повідомлення для перевірки системи сповіщень.',
            null,
            'normal',
            'fas fa-bell'
        );
        
        return [
            'success' => true,
            'message' => 'Тестове сповіщення створено'
        ];
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Створення сповіщення
 */
function createNotification($userId, $type, $title, $message, $actionUrl = null, $priority = 'normal', $icon = null, $data = []) {
    try {
        $db = new Database();
        
        $stmt = $db->prepare("
            INSERT INTO notifications (
                user_id, type, title, message, action_url, 
                priority, icon, data, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $dataJson = json_encode($data);
        $stmt->bind_param("isssssss", $userId, $type, $title, $message, $actionUrl, $priority, $icon, $dataJson);
        
        if ($stmt->execute()) {
            $notificationId = $db->insert_id;
            
            // Відправляємо push сповіщення
            sendPushNotification($userId, $title, $message, $actionUrl, $icon);
            
            return $notificationId;
        }
        
        return false;
        
    } catch (Exception $e) {
        error_log("Error creating notification: " . $e->getMessage());
        return false;
    }
}

/**
 * Відправка push сповіщення
 */
function sendPushNotification($userId, $title, $message, $actionUrl = null, $icon = null) {
    try {
        $db = new Database();
        
        // Отримуємо підписки користувача
        $stmt = $db->prepare("SELECT endpoint, p256dh, auth FROM push_subscriptions WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $subscriptions = $stmt->get_result();
        
        while ($subscription = $subscriptions->fetch_assoc()) {
            // TODO: Реалізувати відправку Web Push
            // Потрібна бібліотека web-push-php
            sendWebPush($subscription, $title, $message, $actionUrl, $icon);
        }
        
    } catch (Exception $e) {
        error_log("Error sending push notification: " . $e->getMessage());
    }
}

/**
 * Фактична відправка Web Push (заглушка)
 */
function sendWebPush($subscription, $title, $message, $actionUrl = null, $icon = null) {
    // TODO: Реалізувати з web-push-php бібліотекою
    /*
    $payload = json_encode([
        'title' => $title,
        'body' => $message,
        'icon' => $icon ?: '/images/icon-192x192.png',
        'badge' => '/images/badge-72x72.png',
        'url' => $actionUrl ?: '/',
        'tag' => 'adboard-notification',
        'requireInteraction' => true
    ]);
    
    // Використати web-push-php для відправки
    */
}

/**
 * Отримання іконки за типом
 */
function getDefaultIcon($type) {
    $icons = [
        'ad_created' => 'fas fa-bullhorn',
        'ad_approved' => 'fas fa-check-circle',
        'ad_rejected' => 'fas fa-times-circle',
        'ad_expired' => 'fas fa-clock',
        'message' => 'fas fa-envelope',
        'payment' => 'fas fa-credit-card',
        'favorite' => 'fas fa-heart',
        'system' => 'fas fa-cog',
        'test' => 'fas fa-bell',
        'default' => 'fas fa-info-circle'
    ];
    
    return $icons[$type] ?? $icons['default'];
}

/**
 * Отримання кольору за типом
 */
function getTypeColor($type) {
    $colors = [
        'ad_created' => 'primary',
        'ad_approved' => 'success',
        'ad_rejected' => 'danger',
        'ad_expired' => 'warning',
        'message' => 'info',
        'payment' => 'success',
        'favorite' => 'danger',
        'system' => 'secondary',
        'test' => 'primary',
        'default' => 'secondary'
    ];
    
    return $colors[$type] ?? $colors['default'];
}

/**
 * Розрахунок часу "тому"
 */
function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'щойно';
    if ($time < 3600) return floor($time/60) . ' хв тому';
    if ($time < 86400) return floor($time/3600) . ' год тому';
    if ($time < 2592000) return floor($time/86400) . ' дн тому';
    if ($time < 31536000) return floor($time/2592000) . ' міс тому';
    
    return floor($time/31536000) . ' р тому';
}
?>