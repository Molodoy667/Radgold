<?php

namespace App\Controllers;

use App\Core\Controller;

class AdminController extends Controller
{
    public function __construct($db)
    {
        parent::__construct($db);
    }

    public function dashboard(): void
    {
        $this->requireAdmin();
        
        $stats = $this->getStats();
        
        $this->view('admin/dashboard', [
            'title' => 'Админ панель',
            'stats' => $stats
        ]);
    }

    public function users(): void
    {
        $this->requireAdmin();
        
        $input = $this->getInput();
        $page = max(1, intval($input['page'] ?? 1));
        $perPage = 20;
        
        $users = $this->getUsers($page, $perPage);
        $totalUsers = $this->getTotalUsers();
        
        $this->view('admin/users', [
            'title' => 'Управление пользователями',
            'users' => $users,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($totalUsers / $perPage),
                'total_items' => $totalUsers
            ]
        ]);
    }

    public function products(): void
    {
        $this->requireAdmin();
        
        $input = $this->getInput();
        $page = max(1, intval($input['page'] ?? 1));
        $perPage = 20;
        
        $products = $this->getProducts($page, $perPage);
        $totalProducts = $this->getTotalProducts();
        
        $this->view('admin/products', [
            'title' => 'Управление товарами',
            'products' => $products,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($totalProducts / $perPage),
                'total_items' => $totalProducts
            ]
        ]);
    }

    public function reviews(): void
    {
        $this->requireAdmin();
        
        $input = $this->getInput();
        $page = max(1, intval($input['page'] ?? 1));
        $perPage = 20;
        
        $reviews = $this->getReviews($page, $perPage);
        $totalReviews = $this->getTotalReviews();
        
        $this->view('admin/reviews', [
            'title' => 'Управление отзывами',
            'reviews' => $reviews,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($totalReviews / $perPage),
                'total_items' => $totalReviews
            ]
        ]);
    }

    public function disputes(): void
    {
        $this->requireAdmin();
        
        $input = $this->getInput();
        $page = max(1, intval($input['page'] ?? 1));
        $perPage = 20;
        
        $disputes = $this->getDisputes($page, $perPage);
        $totalDisputes = $this->getTotalDisputes();
        
        $this->view('admin/disputes', [
            'title' => 'Управление спорами',
            'disputes' => $disputes,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($totalDisputes / $perPage),
                'total_items' => $totalDisputes
            ]
        ]);
    }

    public function settings(): void
    {
        $this->requireAdmin();
        
        $settings = $this->getSettings();
        
        $this->view('admin/settings', [
            'title' => 'Настройки системы',
            'settings' => $settings
        ]);
    }

    public function updateSettings(): void
    {
        $this->requireAdmin();
        
        $input = $this->getInput();
        
        if (!$this->validateCsrfToken($input['csrf_token'] ?? '')) {
            $this->json(['success' => false, 'message' => 'Неверный токен безопасности'], 400);
            return;
        }

        try {
            foreach ($input as $key => $value) {
                if ($key !== 'csrf_token') {
                    $this->updateSetting($key, $value);
                }
            }

            $this->json(['success' => true, 'message' => 'Настройки обновлены']);
        } catch (\Exception $e) {
            error_log("Settings update error: " . $e->getMessage());
            $this->json(['success' => false, 'message' => 'Ошибка обновления настроек'], 500);
        }
    }

    private function getStats(): array
    {
        $stats = [];
        
        // Общая статистика
        $sql = "SELECT 
                    (SELECT COUNT(*) FROM users WHERE status = 'active') as active_users,
                    (SELECT COUNT(*) FROM products WHERE status = 'active') as active_products,
                    (SELECT COUNT(*) FROM purchases WHERE status = 'completed') as completed_purchases,
                    (SELECT SUM(total_amount) FROM purchases WHERE status = 'completed') as total_revenue";
        
        $stmt = $this->db->query($sql);
        $stats = $stmt->fetch();
        
        // Статистика за последние 30 дней
        $sql = "SELECT 
                    (SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as new_users,
                    (SELECT COUNT(*) FROM products WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as new_products,
                    (SELECT COUNT(*) FROM purchases WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as new_purchases";
        
        $stmt = $this->db->query($sql);
        $monthlyStats = $stmt->fetch();
        
        return array_merge($stats, $monthlyStats);
    }

    private function getUsers(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT id, username, email, role, status, balance, created_at, last_activity_at
                FROM users 
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$perPage, $offset]);
        
        return $stmt->fetchAll();
    }

    private function getTotalUsers(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM users");
        return $stmt->fetchColumn();
    }

    private function getProducts(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT p.*, u.username as seller_username
                FROM products p
                JOIN users u ON p.user_id = u.id
                ORDER BY p.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$perPage, $offset]);
        
        return $stmt->fetchAll();
    }

    private function getTotalProducts(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM products");
        return $stmt->fetchColumn();
    }

    private function getReviews(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT r.*, p.title as product_title, u.username as reviewer_username
                FROM reviews r
                JOIN products p ON r.product_id = p.id
                JOIN users u ON r.reviewer_id = u.id
                ORDER BY r.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$perPage, $offset]);
        
        return $stmt->fetchAll();
    }

    private function getTotalReviews(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM reviews");
        return $stmt->fetchColumn();
    }

    private function getDisputes(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT d.*, 
                       initiator.username as initiator_username,
                       respondent.username as respondent_username
                FROM disputes d
                JOIN users initiator ON d.initiator_id = initiator.id
                JOIN users respondent ON d.respondent_id = respondent.id
                ORDER BY d.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$perPage, $offset]);
        
        return $stmt->fetchAll();
    }

    private function getTotalDisputes(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM disputes");
        return $stmt->fetchColumn();
    }

    private function getSettings(): array
    {
        $stmt = $this->db->query("SELECT setting_key, setting_value FROM settings WHERE is_public = 0");
        
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        
        return $settings;
    }

    private function updateSetting(string $key, string $value): void
    {
        $sql = "UPDATE settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$value, $key]);
    }
}