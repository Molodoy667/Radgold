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
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    switch ($action) {
        case 'get_ads':
            echo json_encode(getAds());
            break;
            
        case 'update_status':
            echo json_encode(updateAdStatus());
            break;
            
        case 'delete_ad':
            echo json_encode(deleteAd());
            break;
            
        case 'bulk_action':
            echo json_encode(bulkAction());
            break;
            
        case 'get_ad_details':
            echo json_encode(getAdDetails());
            break;
            
        case 'moderate_ad':
            echo json_encode(moderateAd());
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Невідома дія']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function getAds() {
    try {
        $db = new Database();
        
        // Параметри фільтрації та пагінації
        $page = (int)($_GET['page'] ?? 1);
        $limit = (int)($_GET['limit'] ?? 20);
        $status = $_GET['status'] ?? '';
        $category = (int)($_GET['category'] ?? 0);
        $search = trim($_GET['search'] ?? '');
        $sort = $_GET['sort'] ?? 'created_at';
        $order = $_GET['order'] ?? 'DESC';
        
        $offset = ($page - 1) * $limit;
        
        // Будуємо WHERE умови
        $whereConditions = [];
        $params = [];
        $types = '';
        
        if (!empty($status)) {
            $whereConditions[] = "a.status = ?";
            $params[] = $status;
            $types .= 's';
        }
        
        if ($category > 0) {
            $whereConditions[] = "a.category_id = ?";
            $params[] = $category;
            $types .= 'i';
        }
        
        if (!empty($search)) {
            $whereConditions[] = "(a.title LIKE ? OR a.description LIKE ? OR u.username LIKE ?)";
            $searchParam = "%$search%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
            $types .= 'sss';
        }
        
        $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        // Дозволені поля для сортування
        $allowedSorts = ['id', 'title', 'created_at', 'updated_at', 'status', 'views_count', 'favorites_count'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'created_at';
        }
        
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';
        
        // Отримуємо загальну кількість
        $countSql = "
            SELECT COUNT(*) as total
            FROM ads a
            JOIN users u ON a.user_id = u.id
            JOIN categories c ON a.category_id = c.id
            $whereClause
        ";
        
        if (!empty($params)) {
            $stmt = $db->prepare($countSql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $totalCount = $stmt->get_result()->fetch_assoc()['total'];
        } else {
            $totalCount = $db->query($countSql)->fetch_assoc()['total'];
        }
        
        // Отримуємо дані
        $sql = "
            SELECT 
                a.id, a.title, a.status, a.price, a.currency, a.created_at, a.updated_at,
                a.views_count, a.favorites_count, a.is_featured, a.is_urgent,
                u.username as user_name, u.email as user_email,
                c.name as category_name,
                l.name as location_name,
                (SELECT filename FROM ad_images WHERE ad_id = a.id AND is_main = 1 LIMIT 1) as main_image
            FROM ads a
            JOIN users u ON a.user_id = u.id
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
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        
        $ads = [];
        while ($row = $result->fetch_assoc()) {
            // Форматуємо дати
            $row['created_at_formatted'] = date('d.m.Y H:i', strtotime($row['created_at']));
            $row['updated_at_formatted'] = date('d.m.Y H:i', strtotime($row['updated_at']));
            
            // Форматуємо ціну
            if ($row['price']) {
                $row['price_formatted'] = number_format($row['price'], 2) . ' ' . $row['currency'];
            } else {
                $row['price_formatted'] = 'Договірна';
            }
            
            // URL головного зображення
            if ($row['main_image']) {
                $row['image_url'] = '/images/uploads/' . $row['main_image'];
            } else {
                $row['image_url'] = '/images/no-image.svg';
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

function updateAdStatus() {
    try {
        $adId = (int)($_POST['ad_id'] ?? 0);
        $newStatus = $_POST['status'] ?? '';
        $comment = trim($_POST['comment'] ?? '');
        
        if (!$adId || !in_array($newStatus, ['pending', 'active', 'rejected', 'archived'])) {
            throw new Exception('Невірні параметри');
        }
        
        $db = new Database();
        
        // Оновлюємо статус
        $stmt = $db->prepare("
            UPDATE ads 
            SET status = ?, moderation_comment = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->bind_param("ssi", $newStatus, $comment, $adId);
        
        if (!$stmt->execute()) {
            throw new Exception('Помилка оновлення статусу');
        }
        
        // Логуємо дію
        $logMessage = "Статус оголошення ID:$adId змінено на '$newStatus'";
        if ($comment) {
            $logMessage .= " з коментарем: $comment";
        }
        
        logActivity('ad_status_change', $logMessage, ['ad_id' => $adId, 'status' => $newStatus]);
        
        // Відправляємо email користувачу (якщо потрібно)
        if ($newStatus === 'active') {
            // TODO: Відправити email про схвалення
        } elseif ($newStatus === 'rejected') {
            // TODO: Відправити email про відхилення
        }
        
        return [
            'success' => true,
            'message' => 'Статус успішно оновлено'
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function deleteAd() {
    try {
        $adId = (int)($_POST['ad_id'] ?? 0);
        
        if (!$adId) {
            throw new Exception('Невірний ID оголошення');
        }
        
        $db = new Database();
        
        // Отримуємо інформацію про оголошення
        $stmt = $db->prepare("SELECT title, user_id FROM ads WHERE id = ?");
        $stmt->bind_param("i", $adId);
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
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        // Видаляємо оголошення (CASCADE видалить пов'язані записи)
        $stmt = $db->prepare("DELETE FROM ads WHERE id = ?");
        $stmt->bind_param("i", $adId);
        
        if (!$stmt->execute()) {
            throw new Exception('Помилка видалення оголошення');
        }
        
        logActivity('ad_delete', "Оголошення '{$ad['title']}' (ID:$adId) видалено адміністратором", [
            'ad_id' => $adId,
            'user_id' => $ad['user_id']
        ]);
        
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

function bulkAction() {
    try {
        $action = $_POST['bulk_action'] ?? '';
        $adIds = $_POST['ad_ids'] ?? [];
        
        if (empty($action) || empty($adIds) || !is_array($adIds)) {
            throw new Exception('Невірні параметри масової операції');
        }
        
        $adIds = array_map('intval', $adIds);
        $adIds = array_filter($adIds, function($id) { return $id > 0; });
        
        if (empty($adIds)) {
            throw new Exception('Не вибрано жодного оголошення');
        }
        
        $db = new Database();
        $affected = 0;
        
        switch ($action) {
            case 'activate':
                $stmt = $db->prepare("
                    UPDATE ads 
                    SET status = 'active', updated_at = NOW() 
                    WHERE id IN (" . str_repeat('?,', count($adIds) - 1) . "?)
                ");
                $stmt->bind_param(str_repeat('i', count($adIds)), ...$adIds);
                $stmt->execute();
                $affected = $stmt->affected_rows;
                break;
                
            case 'reject':
                $stmt = $db->prepare("
                    UPDATE ads 
                    SET status = 'rejected', updated_at = NOW() 
                    WHERE id IN (" . str_repeat('?,', count($adIds) - 1) . "?)
                ");
                $stmt->bind_param(str_repeat('i', count($adIds)), ...$adIds);
                $stmt->execute();
                $affected = $stmt->affected_rows;
                break;
                
            case 'archive':
                $stmt = $db->prepare("
                    UPDATE ads 
                    SET status = 'archived', updated_at = NOW() 
                    WHERE id IN (" . str_repeat('?,', count($adIds) - 1) . "?)
                ");
                $stmt->bind_param(str_repeat('i', count($adIds)), ...$adIds);
                $stmt->execute();
                $affected = $stmt->affected_rows;
                break;
                
            case 'delete':
                // Видаляємо зображення для всіх оголошень
                $stmt = $db->prepare("
                    SELECT filename FROM ad_images 
                    WHERE ad_id IN (" . str_repeat('?,', count($adIds) - 1) . "?)
                ");
                $stmt->bind_param(str_repeat('i', count($adIds)), ...$adIds);
                $stmt->execute();
                $images = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                
                foreach ($images as $image) {
                    $imagePath = '../images/uploads/' . $image['filename'];
                    if (file_exists($imagePath)) {
                        unlink($imagePath);
                    }
                }
                
                // Видаляємо оголошення
                $stmt = $db->prepare("
                    DELETE FROM ads 
                    WHERE id IN (" . str_repeat('?,', count($adIds) - 1) . "?)
                ");
                $stmt->bind_param(str_repeat('i', count($adIds)), ...$adIds);
                $stmt->execute();
                $affected = $stmt->affected_rows;
                break;
                
            default:
                throw new Exception('Невідома масова операція');
        }
        
        logActivity('ads_bulk_action', "Масова операція '$action' виконана для $affected оголошень", [
            'action' => $action,
            'ad_ids' => $adIds,
            'affected' => $affected
        ]);
        
        return [
            'success' => true,
            'message' => "Операція виконана для $affected оголошень"
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function getAdDetails() {
    try {
        $adId = (int)($_GET['ad_id'] ?? 0);
        
        if (!$adId) {
            throw new Exception('Невірний ID оголошення');
        }
        
        $db = new Database();
        
        // Отримуємо детальну інформацію
        $stmt = $db->prepare("
            SELECT 
                a.*, 
                u.username, u.email, u.created_at as user_registered,
                c.name as category_name,
                l.name as location_name
            FROM ads a
            JOIN users u ON a.user_id = u.id
            JOIN categories c ON a.category_id = c.id
            LEFT JOIN locations l ON a.location_id = l.id
            WHERE a.id = ?
        ");
        $stmt->bind_param("i", $adId);
        $stmt->execute();
        $ad = $stmt->get_result()->fetch_assoc();
        
        if (!$ad) {
            throw new Exception('Оголошення не знайдено');
        }
        
        // Отримуємо зображення
        $stmt = $db->prepare("
            SELECT * FROM ad_images 
            WHERE ad_id = ? 
            ORDER BY is_main DESC, sort_order ASC
        ");
        $stmt->bind_param("i", $adId);
        $stmt->execute();
        $ad['images'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Отримуємо атрибути
        $stmt = $db->prepare("
            SELECT ca.name, aa.value 
            FROM ad_attributes aa
            JOIN category_attributes ca ON aa.attribute_id = ca.id
            WHERE aa.ad_id = ?
        ");
        $stmt->bind_param("i", $adId);
        $stmt->execute();
        $ad['attributes'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Статистика переглядів
        $stmt = $db->prepare("
            SELECT COUNT(*) as total_views,
                   COUNT(DISTINCT ip_address) as unique_views,
                   COUNT(DISTINCT user_id) as logged_views
            FROM ad_views 
            WHERE ad_id = ?
        ");
        $stmt->bind_param("i", $adId);
        $stmt->execute();
        $ad['view_stats'] = $stmt->get_result()->fetch_assoc();
        
        return [
            'success' => true,
            'data' => $ad
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function moderateAd() {
    try {
        $adId = (int)($_POST['ad_id'] ?? 0);
        $decision = $_POST['decision'] ?? ''; // 'approve' або 'reject'
        $comment = trim($_POST['comment'] ?? '');
        
        if (!$adId || !in_array($decision, ['approve', 'reject'])) {
            throw new Exception('Невірні параметри модерації');
        }
        
        $newStatus = $decision === 'approve' ? 'active' : 'rejected';
        
        $db = new Database();
        
        $stmt = $db->prepare("
            UPDATE ads 
            SET status = ?, moderation_comment = ?, published_at = ?, updated_at = NOW()
            WHERE id = ?
        ");
        
        $publishedAt = $decision === 'approve' ? date('Y-m-d H:i:s') : null;
        $stmt->bind_param("sssi", $newStatus, $comment, $publishedAt, $adId);
        
        if (!$stmt->execute()) {
            throw new Exception('Помилка модерації оголошення');
        }
        
        logActivity('ad_moderation', "Оголошення ID:$adId $decision з коментарем: $comment", [
            'ad_id' => $adId,
            'decision' => $decision,
            'comment' => $comment
        ]);
        
        return [
            'success' => true,
            'message' => $decision === 'approve' ? 'Оголошення схвалено' : 'Оголошення відхилено'
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function logActivity($action, $description, $data = []) {
    try {
        $db = new Database();
        $stmt = $db->prepare("
            INSERT INTO activity_logs (user_id, action, description, data, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        
        $userId = $_SESSION['user_id'];
        $dataJson = json_encode($data);
        $stmt->bind_param("isss", $userId, $action, $description, $dataJson);
        $stmt->execute();
    } catch (Exception $e) {
        // Ігноруємо помилки логування
    }
}
?>