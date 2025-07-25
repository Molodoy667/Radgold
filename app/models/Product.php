<?php
namespace App\Models;

class Product {
    public $id;
    public $user_id;
    public $type;
    public $game;
    public $description;
    public $price;
    public $currency;
    public $images;
    public $status;
    public $views;
    public $created_at;

    public static function findAll($db, $limit = 20, $offset = 0) {
        $stmt = $db->prepare("SELECT p.*, u.login as seller_name FROM products p 
                             JOIN users u ON p.user_id = u.id 
                             WHERE p.status = 'active' 
                             ORDER BY p.created_at DESC 
                             LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function findById($id, $db) {
        $stmt = $db->prepare("SELECT p.*, u.login as seller_name FROM products p 
                             JOIN users u ON p.user_id = u.id 
                             WHERE p.id = ? AND p.status = 'active'");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function findByGame($game, $db) {
        $stmt = $db->prepare("SELECT * FROM products WHERE game = ? AND status = 'active'");
        $stmt->execute([$game]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function findByType($type, $db) {
        $stmt = $db->prepare("SELECT * FROM products WHERE type = ? AND status = 'active'");
        $stmt->execute([$type]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}