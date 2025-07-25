<?php
namespace App\Models;

class Purchase {
    public $id;
    public $buyer_id;
    public $seller_id;
    public $product_id;
    public $price;
    public $currency;
    public $commission;
    public $status;
    public $payment_method;
    public $transaction_id;
    public $created_at;
    public $updated_at;

    public static function create($data, $db) {
        $stmt = $db->prepare("INSERT INTO purchases (buyer_id, seller_id, product_id, price, currency, commission, status, payment_method) 
                             VALUES (?, ?, ?, ?, ?, ?, 'pending', ?)");
        return $stmt->execute([
            $data['buyer_id'],
            $data['seller_id'],
            $data['product_id'],
            $data['price'],
            $data['currency'] ?? 'RUB',
            $data['commission'] ?? 0,
            $data['payment_method'] ?? null
        ]);
    }

    public static function findById($id, $db) {
        $stmt = $db->prepare("SELECT p.*, pr.title as product_title, pr.game, 
                             b.login as buyer_login, s.login as seller_login
                             FROM purchases p
                             JOIN products pr ON p.product_id = pr.id
                             JOIN users b ON p.buyer_id = b.id
                             JOIN users s ON p.seller_id = s.id
                             WHERE p.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function findByBuyer($buyerId, $db, $limit = 20, $offset = 0) {
        $stmt = $db->prepare("SELECT p.*, pr.title as product_title, pr.game, pr.images,
                             s.login as seller_login, s.rating as seller_rating
                             FROM purchases p
                             JOIN products pr ON p.product_id = pr.id
                             JOIN users s ON p.seller_id = s.id
                             WHERE p.buyer_id = ?
                             ORDER BY p.created_at DESC
                             LIMIT ? OFFSET ?");
        $stmt->execute([$buyerId, $limit, $offset]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function findBySeller($sellerId, $db, $limit = 20, $offset = 0) {
        $stmt = $db->prepare("SELECT p.*, pr.title as product_title, pr.game,
                             b.login as buyer_login, b.rating as buyer_rating
                             FROM purchases p
                             JOIN products pr ON p.product_id = pr.id
                             JOIN users b ON p.buyer_id = b.id
                             WHERE p.seller_id = ?
                             ORDER BY p.created_at DESC
                             LIMIT ? OFFSET ?");
        $stmt->execute([$sellerId, $limit, $offset]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function updateStatus($id, $status, $db) {
        $stmt = $db->prepare("UPDATE purchases SET status = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public static function getStats($userId, $db) {
        $stmt = $db->prepare("SELECT 
                                COUNT(*) as total_purchases,
                                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_purchases,
                                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_purchases,
                                SUM(CASE WHEN status = 'disputed' THEN 1 ELSE 0 END) as disputed_purchases,
                                SUM(price) as total_spent
                             FROM purchases 
                             WHERE buyer_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}