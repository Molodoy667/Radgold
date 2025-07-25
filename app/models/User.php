<?php
namespace App\Models;

class User {
    public $id;
    public $email;
    public $login;
    public $password;
    public $avatar;
    public $status;
    public $role;
    public $balance;
    public $rating;
    public $total_sales;
    public $created_at;
    public $updated_at;

    public static function findByLogin($login, $db) {
        $stmt = $db->prepare("SELECT * FROM users WHERE login = ? LIMIT 1");
        $stmt->execute([$login]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public static function create($data, $db) {
        $stmt = $db->prepare("INSERT INTO users (email, login, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
        return $stmt->execute([$data['email'], $data['login'], $data['password'], $data['role'] ?? 'user']);
    }

    public static function findById($id, $db) {
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
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
        $sql = "UPDATE users SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?";
        $stmt = $db->prepare($sql);
        return $stmt->execute($values);
    }

    public static function updateBalance($id, $amount, $db) {
        $stmt = $db->prepare("UPDATE users SET balance = balance + ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$amount, $id]);
    }

    public static function ban($id, $db) {
        $stmt = $db->prepare("UPDATE users SET status = 'banned', updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function unban($id, $db) {
        $stmt = $db->prepare("UPDATE users SET status = 'active', updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public static function changeRole($id, $role, $db) {
        $stmt = $db->prepare("UPDATE users SET role = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$role, $id]);
    }

    public static function getAll($db, $limit = 20, $offset = 0) {
        $stmt = $db->prepare("SELECT * FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getCount($db) {
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM users");
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['count'];
    }

    public static function search($query, $db, $limit = 20, $offset = 0) {
        $stmt = $db->prepare("SELECT * FROM users 
                             WHERE login LIKE ? OR email LIKE ? 
                             ORDER BY created_at DESC 
                             LIMIT ? OFFSET ?");
        $searchTerm = "%$query%";
        $stmt->execute([$searchTerm, $searchTerm, $limit, $offset]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getStats($db) {
        $stmt = $db->prepare("SELECT 
                                COUNT(*) as total_users,
                                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_users,
                                SUM(CASE WHEN status = 'banned' THEN 1 ELSE 0 END) as banned_users,
                                SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admin_users,
                                SUM(CASE WHEN role = 'seller' THEN 1 ELSE 0 END) as seller_users,
                                AVG(rating) as avg_rating
                             FROM users");
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
