<?php
namespace App\Core;

class API {
    private static $db;
    
    public static function init() {
        if (!self::$db) {
            self::$db = Router::getDb();
        }
    }
    
    public static function response($data, $status = 200, $message = '') {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        
        $response = [
            'success' => $status < 400,
            'data' => $data,
            'message' => $message,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
    
    public static function error($message, $status = 400, $data = []) {
        self::response($data, $status, $message);
    }
    
    public static function success($data, $message = 'Success') {
        self::response($data, 200, $message);
    }
    
    public static function requireAuth() {
        if (!Session::has('user_id')) {
            self::error('Unauthorized', 401);
        }
    }
    
    public static function requireRole($role) {
        self::requireAuth();
        
        $user = self::getCurrentUser();
        if ($user['role'] !== $role && $user['role'] !== 'admin') {
            self::error('Forbidden', 403);
        }
    }
    
    public static function getCurrentUser() {
        if (!Session::has('user_id')) {
            return null;
        }
        
        $sql = "SELECT id, login, email, role, status FROM users WHERE id = ?";
        $stmt = self::$db->prepare($sql);
        $stmt->execute([Session::get('user_id')]);
        
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    public static function validateInput($data, $rules) {
        $validator = new Validator($data);
        
        foreach ($rules as $field => $rule) {
            $ruleParts = explode('|', $rule);
            
            foreach ($ruleParts as $part) {
                if (strpos($part, ':') !== false) {
                    list($ruleName, $param) = explode(':', $part, 2);
                } else {
                    $ruleName = $part;
                    $param = null;
                }
                
                switch ($ruleName) {
                    case 'required':
                        $validator->required($field);
                        break;
                    case 'email':
                        $validator->email($field);
                        break;
                    case 'min':
                        $validator->minLength($field, $param);
                        break;
                    case 'max':
                        $validator->maxLength($field, $param);
                        break;
                    case 'numeric':
                        $validator->numeric($field);
                        break;
                }
            }
        }
        
        if ($validator->fails()) {
            self::error('Validation failed', 422, $validator->getErrors());
        }
        
        return $validator->getData();
    }
    
    // API endpoints для продуктов
    public static function getProducts($page = 1, $perPage = 20, $filters = []) {
        self::init();
        
        $search = new Search();
        $result = $search->searchProducts('', $filters, $page, $perPage);
        
        self::success($result, 'Products retrieved successfully');
    }
    
    public static function getProduct($id) {
        self::init();
        
        $sql = "SELECT p.*, u.login as seller_name, 
                       COUNT(r.id) as review_count, 
                       AVG(r.rating) as avg_rating
                FROM products p
                LEFT JOIN users u ON p.user_id = u.id
                LEFT JOIN reviews r ON p.id = r.product_id
                WHERE p.id = ? AND p.status = 'active'
                GROUP BY p.id";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute([$id]);
        $product = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$product) {
            self::error('Product not found', 404);
        }
        
        // Получаем отзывы
        $sql = "SELECT r.*, u.login as user_name 
                FROM reviews r
                LEFT JOIN users u ON r.user_id = u.id
                WHERE r.product_id = ? AND r.status = 'approved'
                ORDER BY r.created_at DESC
                LIMIT 10";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute([$id]);
        $product['reviews'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        self::success($product, 'Product retrieved successfully');
    }
    
    public static function createProduct($data) {
        self::requireAuth();
        
        $validated = self::validateInput($data, [
            'title' => 'required|max:255',
            'description' => 'required|max:1000',
            'game' => 'required|max:100',
            'type' => 'required',
            'price' => 'required|numeric|min:0'
        ]);
        
        $user = self::getCurrentUser();
        
        $sql = "INSERT INTO products (user_id, title, description, game, type, price, currency, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, 'RUB', 'pending', NOW())";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute([
            $user['id'],
            $validated['title'],
            $validated['description'],
            $validated['game'],
            $validated['type'],
            $validated['price']
        ]);
        
        $productId = self::$db->lastInsertId();
        
        self::success(['id' => $productId], 'Product created successfully');
    }
    
    public static function updateProduct($id, $data) {
        self::requireAuth();
        
        $user = self::getCurrentUser();
        
        // Проверяем, что продукт принадлежит пользователю
        $sql = "SELECT user_id FROM products WHERE id = ?";
        $stmt = self::$db->prepare($sql);
        $stmt->execute([$id]);
        $product = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$product || $product['user_id'] != $user['id']) {
            self::error('Product not found or access denied', 404);
        }
        
        $validated = self::validateInput($data, [
            'title' => 'max:255',
            'description' => 'max:1000',
            'game' => 'max:100',
            'price' => 'numeric|min:0'
        ]);
        
        $sql = "UPDATE products SET ";
        $params = [];
        $updates = [];
        
        foreach ($validated as $field => $value) {
            $updates[] = "{$field} = ?";
            $params[] = $value;
        }
        
        $updates[] = "updated_at = NOW()";
        $params[] = $id;
        
        $sql .= implode(', ', $updates) . " WHERE id = ?";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute($params);
        
        self::success(['id' => $id], 'Product updated successfully');
    }
    
    public static function deleteProduct($id) {
        self::requireAuth();
        
        $user = self::getCurrentUser();
        
        // Проверяем, что продукт принадлежит пользователю
        $sql = "SELECT user_id FROM products WHERE id = ?";
        $stmt = self::$db->prepare($sql);
        $stmt->execute([$id]);
        $product = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$product || $product['user_id'] != $user['id']) {
            self::error('Product not found or access denied', 404);
        }
        
        $sql = "DELETE FROM products WHERE id = ?";
        $stmt = self::$db->prepare($sql);
        $stmt->execute([$id]);
        
        self::success(['id' => $id], 'Product deleted successfully');
    }
    
    // API endpoints для пользователей
    public static function getProfile() {
        self::requireAuth();
        
        $user = self::getCurrentUser();
        
        // Получаем статистику
        $sql = "SELECT 
                    COUNT(DISTINCT p.id) as total_products,
                    COUNT(DISTINCT pur.id) as total_purchases,
                    COUNT(DISTINCT f.id) as total_favorites,
                    SUM(pur.price) as total_spent
                FROM users u
                LEFT JOIN products p ON u.id = p.user_id
                LEFT JOIN purchases pur ON u.id = pur.buyer_id
                LEFT JOIN favorites f ON u.id = f.user_id
                WHERE u.id = ?";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute([$user['id']]);
        $stats = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        $user['stats'] = $stats;
        
        self::success($user, 'Profile retrieved successfully');
    }
    
    public static function updateProfile($data) {
        self::requireAuth();
        
        $validated = self::validateInput($data, [
            'login' => 'max:50',
            'email' => 'email|max:100'
        ]);
        
        $user = self::getCurrentUser();
        
        $sql = "UPDATE users SET ";
        $params = [];
        $updates = [];
        
        foreach ($validated as $field => $value) {
            $updates[] = "{$field} = ?";
            $params[] = $value;
        }
        
        $updates[] = "updated_at = NOW()";
        $params[] = $user['id'];
        
        $sql .= implode(', ', $updates) . " WHERE id = ?";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute($params);
        
        self::success(['id' => $user['id']], 'Profile updated successfully');
    }
    
    // API endpoints для поиска
    public static function search($query, $type = 'products', $page = 1) {
        self::init();
        
        $search = new Search();
        
        switch ($type) {
            case 'products':
                $result = $search->searchProducts($query, [], $page, 20);
                break;
            case 'autocomplete':
                $result = $search->autocomplete($query);
                break;
            default:
                self::error('Invalid search type', 400);
        }
        
        // Логируем поиск
        if (Session::has('user_id')) {
            $search->logSearch(Session::get('user_id'), $query, count($result['data'] ?? $result));
        }
        
        self::success($result, 'Search completed successfully');
    }
    
    // API endpoints для уведомлений
    public static function getNotifications($page = 1, $perPage = 20) {
        self::requireAuth();
        
        $user = self::getCurrentUser();
        $offset = ($page - 1) * $perPage;
        
        $notifications = Notification::getAll($user['id'], $perPage, $offset);
        $unreadCount = Notification::getUnreadCount($user['id']);
        
        self::success([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
            'page' => $page,
            'per_page' => $perPage
        ], 'Notifications retrieved successfully');
    }
    
    public static function markNotificationAsRead($id) {
        self::requireAuth();
        
        $user = self::getCurrentUser();
        
        if (Notification::markAsRead($id, $user['id'])) {
            self::success(['id' => $id], 'Notification marked as read');
        } else {
            self::error('Notification not found', 404);
        }
    }
    
    public static function markAllNotificationsAsRead() {
        self::requireAuth();
        
        $user = self::getCurrentUser();
        
        if (Notification::markAllAsRead($user['id'])) {
            self::success([], 'All notifications marked as read');
        } else {
            self::error('Failed to mark notifications as read', 500);
        }
    }
    
    // API endpoints для избранного
    public static function addToFavorites($productId) {
        self::requireAuth();
        
        $user = self::getCurrentUser();
        
        // Проверяем, что продукт существует
        $sql = "SELECT id FROM products WHERE id = ? AND status = 'active'";
        $stmt = self::$db->prepare($sql);
        $stmt->execute([$productId]);
        
        if (!$stmt->fetch()) {
            self::error('Product not found', 404);
        }
        
        // Добавляем в избранное
        $sql = "INSERT IGNORE INTO favorites (user_id, product_id, created_at) VALUES (?, ?, NOW())";
        $stmt = self::$db->prepare($sql);
        $stmt->execute([$user['id'], $productId]);
        
        self::success(['product_id' => $productId], 'Added to favorites');
    }
    
    public static function removeFromFavorites($productId) {
        self::requireAuth();
        
        $user = self::getCurrentUser();
        
        $sql = "DELETE FROM favorites WHERE user_id = ? AND product_id = ?";
        $stmt = self::$db->prepare($sql);
        $stmt->execute([$user['id'], $productId]);
        
        self::success(['product_id' => $productId], 'Removed from favorites');
    }
    
    public static function getFavorites($page = 1, $perPage = 20) {
        self::requireAuth();
        
        $user = self::getCurrentUser();
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT p.*, u.login as seller_name 
                FROM favorites f
                JOIN products p ON f.product_id = p.id
                LEFT JOIN users u ON p.user_id = u.id
                WHERE f.user_id = ? AND p.status = 'active'
                ORDER BY f.created_at DESC
                LIMIT ? OFFSET ?";
        
        $stmt = self::$db->prepare($sql);
        $stmt->execute([$user['id'], $perPage, $offset]);
        $favorites = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Получаем общее количество
        $sql = "SELECT COUNT(*) as count FROM favorites f
                JOIN products p ON f.product_id = p.id
                WHERE f.user_id = ? AND p.status = 'active'";
        $stmt = self::$db->prepare($sql);
        $stmt->execute([$user['id']]);
        $total = $stmt->fetch(\PDO::FETCH_ASSOC)['count'];
        
        self::success([
            'favorites' => $favorites,
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage
        ], 'Favorites retrieved successfully');
    }
}