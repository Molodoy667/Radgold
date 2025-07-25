<?php
namespace App\Models;

class Favorite {
    public $id;
    public $user_id;
    public $product_id;
    public $created_at;

    public static function add($userId, $productId, $db) {
        try {
            $stmt = $db->prepare("INSERT INTO favorites (user_id, product_id) VALUES (?, ?)");
            return $stmt->execute([$userId, $productId]);
        } catch (\PDOException $e) {
            // Если уже в избранном, игнорируем ошибку
            return false;
        }
    }

    public static function remove($userId, $productId, $db) {
        $stmt = $db->prepare("DELETE FROM favorites WHERE user_id = ? AND product_id = ?");
        return $stmt->execute([$userId, $productId]);
    }

    public static function isFavorite($userId, $productId, $db) {
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM favorites WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    public static function findByUser($userId, $db, $limit = 20, $offset = 0) {
        $stmt = $db->prepare("SELECT f.*, p.title, p.game, p.price, p.currency, p.images, p.rating, p.total_reviews,
                             u.login as seller_login, u.rating as seller_rating
                             FROM favorites f
                             JOIN products p ON f.product_id = p.id
                             JOIN users u ON p.user_id = u.id
                             WHERE f.user_id = ? AND p.status = 'active'
                             ORDER BY f.created_at DESC
                             LIMIT ? OFFSET ?");
        $stmt->execute([$userId, $limit, $offset]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getCount($userId, $db) {
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM favorites f
                             JOIN products p ON f.product_id = p.id
                             WHERE f.user_id = ? AND p.status = 'active'");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['count'];
    }
}