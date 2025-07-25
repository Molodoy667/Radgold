<?php
namespace App\Models;

class Product {
    public $id;
    public $user_id;
    public $type;
    public $game;
    public $title;
    public $description;
    public $price;
    public $currency;
    public $images;
    public $status;
    public $views;
    public $rating;
    public $total_reviews;
    public $created_at;
    public $updated_at;

    public static function findAll($db) {
        $stmt = $db->prepare("SELECT p.*, u.login as seller_name, u.rating as seller_rating 
                             FROM products p 
                             JOIN users u ON p.user_id = u.id 
                             WHERE p.status = 'active' 
                             ORDER BY p.created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function findById($id, $db) {
        $stmt = $db->prepare("SELECT p.*, u.login as seller_name, u.rating as seller_rating, u.total_sales
                             FROM products p 
                             JOIN users u ON p.user_id = u.id 
                             WHERE p.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function findByUser($userId, $db, $limit = 20, $offset = 0) {
        $stmt = $db->prepare("SELECT * FROM products 
                             WHERE user_id = ? 
                             ORDER BY created_at DESC 
                             LIMIT ? OFFSET ?");
        $stmt->execute([$userId, $limit, $offset]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getCountByUser($userId, $db) {
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM products WHERE user_id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['count'];
    }

    public static function create($data, $db) {
        $stmt = $db->prepare("INSERT INTO products (user_id, type, game, title, description, price, currency, status, created_at)
                             VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
        return $stmt->execute([
            $data['user_id'],
            $data['type'],
            $data['game'],
            $data['title'],
            $data['description'],
            $data['price'],
            $data['currency'] ?? 'RUB'
        ]);
    }

    public static function update($id, $data, $db) {
        $fields = [];
        $values = [];
        
        foreach ($data as $key => $value) {
            if ($key !== 'id') {
                $fields[] = "$key = ?";
                $values[] = $value;
            }
        }
        
        $values[] = $id;
        $sql = "UPDATE products SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute($values);
    }

    public static function updateStatus($id, $status, $db) {
        $stmt = $db->prepare("UPDATE products SET status = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public static function incrementViews($id, $db) {
        $stmt = $db->prepare("UPDATE products SET views = views + 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function filter($filters, $db, $limit = 20, $offset = 0) {
        $where = ["p.status = 'active'"];
        $params = [];
        
        if (!empty($filters['game'])) {
            $where[] = "p.game = ?";
            $params[] = $filters['game'];
        }
        
        if (!empty($filters['type'])) {
            $where[] = "p.type = ?";
            $params[] = $filters['type'];
        }
        
        if (!empty($filters['min_price'])) {
            $where[] = "p.price >= ?";
            $params[] = $filters['min_price'];
        }
        
        if (!empty($filters['max_price'])) {
            $where[] = "p.price <= ?";
            $params[] = $filters['max_price'];
        }
        
        if (!empty($filters['search'])) {
            $where[] = "(p.title LIKE ? OR p.description LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $sql = "SELECT p.*, u.login as seller_name, u.rating as seller_rating 
                FROM products p 
                JOIN users u ON p.user_id = u.id 
                WHERE " . implode(' AND ', $where) . " 
                ORDER BY p.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getGames($db) {
        $stmt = $db->prepare("SELECT DISTINCT game FROM products WHERE status = 'active' ORDER BY game");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public static function getTypes($db) {
        $stmt = $db->prepare("SELECT DISTINCT type FROM products WHERE status = 'active' ORDER BY type");
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public static function getPendingProducts($db, $limit = 20, $offset = 0) {
        $stmt = $db->prepare("SELECT p.*, u.login as seller_name, u.email as seller_email
                             FROM products p 
                             JOIN users u ON p.user_id = u.id 
                             WHERE p.status = 'pending' 
                             ORDER BY p.created_at ASC 
                             LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getStats($db) {
        $stmt = $db->prepare("SELECT 
                                COUNT(*) as total_products,
                                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_products,
                                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_products,
                                SUM(CASE WHEN status = 'sold' THEN 1 ELSE 0 END) as sold_products,
                                SUM(CASE WHEN status = 'banned' THEN 1 ELSE 0 END) as banned_products,
                                AVG(price) as avg_price,
                                SUM(views) as total_views
                             FROM products");
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function delete($id, $userId, $db) {
        $stmt = $db->prepare("DELETE FROM products WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }
}
