<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Review;
use App\Models\Dispute;
use App\Models\Setting;
use App\Models\ChatMessage;

class AdminController {
    
    public function __construct() {
        // Проверка прав администратора
        if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
            header('Location: /login');
            exit;
        }
    }
    
    public function dashboard($db) {
        // Статистика для дашборда
        $stats = [
            'users' => User::getStats($db),
            'products' => Product::getStats($db),
            'purchases' => $this->getPurchaseStats($db),
            'disputes' => Dispute::getStats($db),
            'recent_activity' => $this->getRecentActivity($db)
        ];
        
        require_once __DIR__ . '/../views/admin/dashboard.php';
    }
    
    public function users($db) {
        $page = $_GET['page'] ?? 1;
        $search = $_GET['search'] ?? '';
        $role = $_GET['role'] ?? '';
        $status = $_GET['status'] ?? '';
        
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        if ($search) {
            $users = User::search($search, $db, $limit, $offset);
        } else {
            $users = User::getAll($db, $limit, $offset);
        }
        
        $totalUsers = User::getCount($db);
        $totalPages = ceil($totalUsers / $limit);
        
        require_once __DIR__ . '/../views/admin/users.php';
    }
    
    public function ban($userId, $db) {
        if (User::ban($userId, $db)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Ошибка при блокировке']);
        }
    }
    
    public function unban($userId, $db) {
        if (User::unban($userId, $db)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Ошибка при разблокировке']);
        }
    }
    
    public function changeRole($userId, $db) {
        $role = $_POST['role'] ?? '';
        if (in_array($role, ['user', 'seller', 'admin'])) {
            if (User::changeRole($userId, $role, $db)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Ошибка при смене роли']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Неверная роль']);
        }
    }
    
    public function products($db) {
        $page = $_GET['page'] ?? 1;
        $status = $_GET['status'] ?? '';
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        if ($status === 'pending') {
            $products = Product::getPendingProducts($db, $limit, $offset);
        } else {
            $products = Product::getAll($db, $limit, $offset);
        }
        
        require_once __DIR__ . '/../views/admin/products.php';
    }
    
    public function approve($productId, $db) {
        if (Product::updateStatus($productId, 'active', $db)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Ошибка при одобрении']);
        }
    }
    
    public function reject($productId, $db) {
        if (Product::updateStatus($productId, 'banned', $db)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Ошибка при отклонении']);
        }
    }
    
    public function disputes($db) {
        $page = $_GET['page'] ?? 1;
        $status = $_GET['status'] ?? '';
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        if ($status) {
            $disputes = Dispute::getByStatus($status, $db, $limit, $offset);
        } else {
            $disputes = Dispute::getOpenDisputes($db, $limit, $offset);
        }
        
        require_once __DIR__ . '/../views/admin/disputes.php';
    }
    
    public function resolveDispute($db) {
        $disputeId = $_POST['dispute_id'] ?? 0;
        $resolution = $_POST['resolution'] ?? '';
        $adminId = $_SESSION['user']['id'];
        
        if (Dispute::resolve($disputeId, $resolution, $adminId, $db)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Ошибка при решении диспута']);
        }
    }
    
    public function reviews($db) {
        $page = $_GET['page'] ?? 1;
        $status = $_GET['status'] ?? 'pending';
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        if ($status === 'pending') {
            $reviews = Review::getPendingReviews($db, $limit, $offset);
        } else {
            $reviews = Review::getAll($db, $limit, $offset);
        }
        
        require_once __DIR__ . '/../views/admin/reviews.php';
    }
    
    public function approveReview($db) {
        $reviewId = $_POST['review_id'] ?? 0;
        
        if (Review::updateStatus($reviewId, 'approved', $db)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Ошибка при одобрении отзыва']);
        }
    }
    
    public function rejectReview($db) {
        $reviewId = $_POST['review_id'] ?? 0;
        
        if (Review::updateStatus($reviewId, 'rejected', $db)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Ошибка при отклонении отзыва']);
        }
    }
    
    public function settings($db) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $settings = [
                'site_title' => $_POST['site_title'] ?? '',
                'contact_email' => $_POST['contact_email'] ?? '',
                'commission_percent' => $_POST['commission_percent'] ?? 5,
                'registration_enabled' => isset($_POST['registration_enabled']),
                'maintenance_mode' => isset($_POST['maintenance_mode']),
                'auto_approve_products' => isset($_POST['auto_approve_products'])
            ];
            
            if (Setting::updateMultiple($settings, $db)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Ошибка при сохранении настроек']);
            }
        } else {
            $settings = Setting::getAll($db);
            require_once __DIR__ . '/../views/admin/settings.php';
        }
    }
    
    private function getPurchaseStats($db) {
        $stmt = $db->prepare("SELECT 
                                COUNT(*) as total_purchases,
                                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_purchases,
                                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_purchases,
                                SUM(CASE WHEN status = 'disputed' THEN 1 ELSE 0 END) as disputed_purchases,
                                SUM(price) as total_revenue,
                                SUM(commission) as total_commission
                             FROM purchases");
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    private function getRecentActivity($db) {
        $stmt = $db->prepare("SELECT 
                                'user' as type,
                                u.login as title,
                                'Новый пользователь зарегистрировался' as description,
                                u.created_at as date
                             FROM users u
                             WHERE u.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                             
                             UNION ALL
                             
                             SELECT 
                                'product' as type,
                                p.title,
                                'Новый товар добавлен' as description,
                                p.created_at as date
                             FROM products p
                             WHERE p.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                             
                             UNION ALL
                             
                             SELECT 
                                'purchase' as type,
                                CONCAT('Покупка #', p.id) as title,
                                'Новая покупка совершена' as description,
                                p.created_at as date
                             FROM purchases p
                             WHERE p.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                             
                             ORDER BY date DESC
                             LIMIT 10");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}