<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class ProductController extends Controller
{
    private User $userModel;

    public function __construct($db)
    {
        parent::__construct($db);
        $this->userModel = new User($db);
    }

    public function index(): void
    {
        // Простая главная страница с информацией о платформе
        $this->view('products/index', [
            'title' => 'Главная страница',
            'totalUsers' => $this->getTotalUsers(),
            'featuredProducts' => $this->getFeaturedProducts()
        ]);
    }

    private function getTotalUsers(): int
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM users WHERE status = 'active'");
            $stmt->execute();
            $result = $stmt->fetch();
            return (int) $result['count'];
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getFeaturedProducts(): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, u.username as seller_name, u.rating as seller_rating 
                FROM products p 
                JOIN users u ON p.user_id = u.id 
                WHERE p.status = 'active' AND p.visibility IN ('public', 'featured')
                ORDER BY p.created_at DESC 
                LIMIT 6
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\Exception $e) {
            return [];
        }
    }
}