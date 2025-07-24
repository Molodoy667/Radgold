<?php
require_once '../core/config.php';
require_once '../core/functions.php';

header('Content-Type: application/json');

try {
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    
    switch ($action) {
        case 'geocode_address':
            echo json_encode(geocodeAddress());
            break;
            
        case 'reverse_geocode':
            echo json_encode(reverseGeocode());
            break;
            
        case 'get_nearby_ads':
            echo json_encode(getNearbyAds());
            break;
            
        case 'get_ad_location':
            echo json_encode(getAdLocation());
            break;
            
        case 'update_ad_location':
            echo json_encode(updateAdLocation());
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
 * Геокодування адреси в координати
 */
function geocodeAddress() {
    try {
        $address = trim($_GET['address'] ?? $_POST['address'] ?? '');
        
        if (empty($address)) {
            throw new Exception('Адреса не вказана');
        }
        
        // Використовуємо OpenStreetMap Nominatim API як безкоштовну альтернативу Google
        $encodedAddress = urlencode($address . ', Ukraine');
        $url = "https://nominatim.openstreetmap.org/search?q={$encodedAddress}&format=json&limit=5&countrycodes=ua";
        
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: AdBoard Pro/1.0\r\n",
                'timeout' => 10
            ]
        ]);
        
        $response = file_get_contents($url, false, $context);
        
        if ($response === false) {
            throw new Exception('Помилка запиту до сервісу геокодування');
        }
        
        $data = json_decode($response, true);
        
        if (empty($data)) {
            throw new Exception('Адресу не знайдено');
        }
        
        $results = [];
        foreach ($data as $item) {
            $results[] = [
                'address' => $item['display_name'],
                'latitude' => (float)$item['lat'],
                'longitude' => (float)$item['lon'],
                'type' => $item['type'] ?? 'unknown',
                'importance' => (float)($item['importance'] ?? 0)
            ];
        }
        
        return [
            'success' => true,
            'results' => $results,
            'count' => count($results)
        ];
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Зворотне геокодування (координати в адресу)
 */
function reverseGeocode() {
    try {
        $latitude = (float)($_GET['lat'] ?? $_POST['lat'] ?? 0);
        $longitude = (float)($_GET['lng'] ?? $_POST['lng'] ?? 0);
        
        if (!$latitude || !$longitude) {
            throw new Exception('Координати не вказані');
        }
        
        // Перевіряємо чи координати в межах України
        if ($latitude < 44.0 || $latitude > 52.5 || $longitude < 22.0 || $longitude > 40.5) {
            throw new Exception('Координати поза межами України');
        }
        
        $url = "https://nominatim.openstreetmap.org/reverse?lat={$latitude}&lon={$longitude}&format=json&countrycodes=ua";
        
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: AdBoard Pro/1.0\r\n",
                'timeout' => 10
            ]
        ]);
        
        $response = file_get_contents($url, false, $context);
        
        if ($response === false) {
            throw new Exception('Помилка запиту до сервісу геокодування');
        }
        
        $data = json_decode($response, true);
        
        if (empty($data)) {
            throw new Exception('Адресу за координатами не знайдено');
        }
        
        $address = [
            'full_address' => $data['display_name'],
            'city' => $data['address']['city'] ?? $data['address']['town'] ?? $data['address']['village'] ?? '',
            'region' => $data['address']['state'] ?? '',
            'country' => $data['address']['country'] ?? 'Ukraine',
            'postcode' => $data['address']['postcode'] ?? '',
            'latitude' => $latitude,
            'longitude' => $longitude
        ];
        
        return [
            'success' => true,
            'address' => $address
        ];
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Отримання оголошень поблизу
 */
function getNearbyAds() {
    try {
        $latitude = (float)($_GET['lat'] ?? $_POST['lat'] ?? 0);
        $longitude = (float)($_GET['lng'] ?? $_POST['lng'] ?? 0);
        $radius = (int)($_GET['radius'] ?? $_POST['radius'] ?? 10); // км
        $limit = (int)($_GET['limit'] ?? $_POST['limit'] ?? 20);
        
        if (!$latitude || !$longitude) {
            throw new Exception('Координати не вказані');
        }
        
        $db = Database::getInstance();
        
        // Використовуємо формулу гаверсинуса для пошуку в радіусі
        $stmt = $db->prepare("
            SELECT 
                a.id, a.title, a.price, a.location_id, a.latitude, a.longitude,
                a.created_at, a.status,
                c.name as category_name, c.icon as category_icon,
                l.name as location_name,
                ai.image_url,
                (6371 * acos(cos(radians(?)) * cos(radians(a.latitude)) * 
                cos(radians(a.longitude) - radians(?)) + sin(radians(?)) * 
                sin(radians(a.latitude)))) AS distance
            FROM ads a
            LEFT JOIN categories c ON a.category_id = c.id
            LEFT JOIN locations l ON a.location_id = l.id
            LEFT JOIN (
                SELECT ad_id, MIN(image_url) as image_url 
                FROM ad_images 
                GROUP BY ad_id
            ) ai ON a.id = ai.ad_id
            WHERE a.status = 'active'
            AND a.latitude IS NOT NULL 
            AND a.longitude IS NOT NULL
            HAVING distance < ?
            ORDER BY distance ASC
            LIMIT ?
        ");
        
        $stmt->bind_param("ddddi", $latitude, $longitude, $latitude, $radius, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $ads = [];
        while ($row = $result->fetch_assoc()) {
            $ads[] = [
                'id' => (int)$row['id'],
                'title' => $row['title'],
                'price' => (float)$row['price'],
                'latitude' => (float)$row['latitude'],
                'longitude' => (float)$row['longitude'],
                'distance' => round((float)$row['distance'], 2),
                'category' => [
                    'name' => $row['category_name'],
                    'icon' => $row['category_icon']
                ],
                'location' => $row['location_name'],
                'image' => $row['image_url'] ? '/images/ads/' . $row['image_url'] : null,
                'created_at' => $row['created_at'],
                'url' => '/ad/' . $row['id']
            ];
        }
        
        return [
            'success' => true,
            'ads' => $ads,
            'count' => count($ads),
            'radius' => $radius
        ];
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Отримання локації конкретного оголошення
 */
function getAdLocation() {
    try {
        $adId = (int)($_GET['ad_id'] ?? $_POST['ad_id'] ?? 0);
        
        if (!$adId) {
            throw new Exception('ID оголошення не вказано');
        }
        
        $db = Database::getInstance();
        
        $stmt = $db->prepare("
            SELECT 
                a.id, a.title, a.latitude, a.longitude, a.address,
                l.name as location_name, l.latitude as location_lat, l.longitude as location_lng
            FROM ads a
            LEFT JOIN locations l ON a.location_id = l.id
            WHERE a.id = ? AND a.status = 'active'
        ");
        
        $stmt->bind_param("i", $adId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('Оголошення не знайдено');
        }
        
        $ad = $result->fetch_assoc();
        
        // Використовуємо точні координати оголошення, якщо є, інакше координати міста
        $latitude = $ad['latitude'] ?: $ad['location_lat'];
        $longitude = $ad['longitude'] ?: $ad['location_lng'];
        
        return [
            'success' => true,
            'location' => [
                'latitude' => (float)$latitude,
                'longitude' => (float)$longitude,
                'address' => $ad['address'],
                'city' => $ad['location_name'],
                'title' => $ad['title']
            ]
        ];
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Оновлення локації оголошення
 */
function updateAdLocation() {
    try {
        if (!isLoggedIn()) {
            throw new Exception('Необхідна авторизація');
        }
        
        $adId = (int)($_POST['ad_id'] ?? 0);
        $latitude = (float)($_POST['latitude'] ?? 0);
        $longitude = (float)($_POST['longitude'] ?? 0);
        $address = trim($_POST['address'] ?? '');
        
        if (!$adId) {
            throw new Exception('ID оголошення не вказано');
        }
        
        if (!$latitude || !$longitude) {
            throw new Exception('Координати не вказані');
        }
        
        $userId = $_SESSION['user_id'];
        $db = Database::getInstance();
        
        // Перевіряємо що оголошення належить користувачу
        $stmt = $db->prepare("SELECT id, user_id FROM ads WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $adId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception('Оголошення не знайдено або не належить вам');
        }
        
        // Оновлюємо координати
        $stmt = $db->prepare("
            UPDATE ads 
            SET latitude = ?, longitude = ?, address = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        
        $stmt->bind_param("ddsi", $latitude, $longitude, $address, $adId);
        
        if (!$stmt->execute()) {
            throw new Exception('Помилка оновлення локації');
        }
        
        // Логування
        logActivity($userId, 'ad_location_updated', "Оновлено локацію оголошення", [
            'ad_id' => $adId,
            'latitude' => $latitude,
            'longitude' => $longitude
        ]);
        
        return [
            'success' => true,
            'message' => 'Локацію успішно оновлено',
            'location' => [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'address' => $address
            ]
        ];
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

/**
 * Логування активності
 */
function logActivity($userId, $action, $description, $data = []) {
    try {
        $db = Database::getInstance();
        $stmt = $db->prepare("
            INSERT INTO activity_logs (user_id, action, description, data, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        
        $dataJson = json_encode($data);
        $stmt->bind_param("isss", $userId, $action, $description, $dataJson);
        $stmt->execute();
        
    } catch (Exception $e) {
        // Ігноруємо помилки логування
    }
}
?>