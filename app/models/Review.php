<?php
namespace App\Models;

class Review {
    public $id;
    public $product_id;
    public $user_id;
    public $purchase_id;
    public $rating;
    public $title;
    public $comment;
    public $status;
    public $created_at;
    public $updated_at;

    public static function create($data, $db) {
        $stmt = $db->prepare("INSERT INTO reviews (product_id, user_id, purchase_id, rating, title, comment, status) 
                             VALUES (?, ?, ?, ?, ?, ?, 'pending')");
        return $stmt->execute([
            $data['product_id'],
            $data['user_id'],
            $data['purchase_id'],
            $data['rating'],
            $data['title'] ?? null,
            $data['comment'] ?? null
        ]);
    }

    public static function findById($id, $db) {
        $stmt = $db->prepare("SELECT r.*, u.login as user_login, u.avatar as user_avatar,
                             p.title as product_title, p.game
                             FROM reviews r
                             JOIN users u ON r.user_id = u.id
                             JOIN products p ON r.product_id = p.id
                             WHERE r.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function findByProduct($productId, $db, $limit = 10, $offset = 0) {
        $stmt = $db->prepare("SELECT r.*, u.login as user_login, u.avatar as user_avatar
                             FROM reviews r
                             JOIN users u ON r.user_id = u.id
                             WHERE r.product_id = ? AND r.status = 'approved'
                             ORDER BY r.created_at DESC
                             LIMIT ? OFFSET ?");
        $stmt->execute([$productId, $limit, $offset]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function findByUser($userId, $db, $limit = 10, $offset = 0) {
        $stmt = $db->prepare("SELECT r.*, p.title as product_title, p.game, p.images
                             FROM reviews r
                             JOIN products p ON r.product_id = p.id
                             WHERE r.user_id = ?
                             ORDER BY r.created_at DESC
                             LIMIT ? OFFSET ?");
        $stmt->execute([$userId, $limit, $offset]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function updateStatus($id, $status, $db) {
        $stmt = $db->prepare("UPDATE reviews SET status = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public static function getProductRating($productId, $db) {
        $stmt = $db->prepare("SELECT 
                                AVG(rating) as average_rating,
                                COUNT(*) as total_reviews,
                                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as five_star,
                                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as four_star,
                                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as three_star,
                                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as two_star,
                                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as one_star
                             FROM reviews 
                             WHERE product_id = ? AND status = 'approved'");
        $stmt->execute([$productId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function canReview($userId, $purchaseId, $db) {
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM reviews WHERE user_id = ? AND purchase_id = ?");
        $stmt->execute([$userId, $purchaseId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['count'] == 0;
    }

    public static function getPendingReviews($db, $limit = 20, $offset = 0) {
        $stmt = $db->prepare("SELECT r.*, u.login as user_login, p.title as product_title
                             FROM reviews r
                             JOIN users u ON r.user_id = u.id
                             JOIN products p ON r.product_id = p.id
                             WHERE r.status = 'pending'
                             ORDER BY r.created_at ASC
                             LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}