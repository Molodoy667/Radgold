<?php
namespace App\Controllers;

use App\Models\Product;

class ProductController {
    public function index($db) {
        $products = Product::findAll($db);
        require_once __DIR__ . '/../views/products/index.php';
    }

    public function show($id, $db) {
        $product = Product::findById($id, $db);
        if (!$product) {
            header('HTTP/1.0 404 Not Found');
            echo 'Товар не найден';
            return;
        }
        require_once __DIR__ . '/../views/products/show.php';
    }

    public function filter($db) {
        $game = $_GET['game'] ?? '';
        $type = $_GET['type'] ?? '';
        $minPrice = $_GET['min_price'] ?? '';
        $maxPrice = $_GET['max_price'] ?? '';

        $sql = "SELECT p.*, u.login as seller_name FROM products p 
                JOIN users u ON p.user_id = u.id 
                WHERE p.status = 'active'";
        $params = [];

        if ($game) {
            $sql .= " AND p.game = ?";
            $params[] = $game;
        }
        if ($type) {
            $sql .= " AND p.type = ?";
            $params[] = $type;
        }
        if ($minPrice) {
            $sql .= " AND p.price >= ?";
            $params[] = $minPrice;
        }
        if ($maxPrice) {
            $sql .= " AND p.price <= ?";
            $params[] = $maxPrice;
        }

        $sql .= " ORDER BY p.created_at DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            echo json_encode($products);
        } else {
            require_once __DIR__ . '/../views/products/index.php';
        }
    }
}