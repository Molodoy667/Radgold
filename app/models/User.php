<?php

namespace App\Models;

use PDO;

class User
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, email, username, avatar, status, role, balance, rating, 
                   total_sales, subscription_type, subscription_expires_at, 
                   created_at, updated_at 
            FROM users 
            WHERE id = ?
        ");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        
        return $user ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, email, username, password_hash, avatar, status, role, 
                   balance, rating, total_sales, subscription_type, 
                   subscription_expires_at, created_at, updated_at 
            FROM users 
            WHERE email = ?
        ");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        return $user ?: null;
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, email, username, password_hash, avatar, status, role, 
                   balance, rating, total_sales, subscription_type, 
                   subscription_expires_at, created_at, updated_at 
            FROM users 
            WHERE username = ?
        ");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        return $user ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO users (email, username, password_hash, status, role, created_at) 
            VALUES (?, ?, ?, 'active', 'user', NOW())
        ");
        $stmt->execute([
            $data['email'],
            $data['username'],
            password_hash($data['password'], PASSWORD_DEFAULT)
        ]);
        
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $values = [];
        
        $allowedFields = ['email', 'username', 'avatar', 'status', 'role', 'balance', 'rating'];
        
        foreach ($data as $field => $value) {
            if (in_array($field, $allowedFields)) {
                $fields[] = "{$field} = ?";
                $values[] = $value;
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        $sql = "UPDATE users SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function updatePassword(int $id, string $newPassword): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET password_hash = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        return $stmt->execute([
            password_hash($newPassword, PASSWORD_DEFAULT),
            $id
        ]);
    }

    public function updateBalance(int $id, float $amount): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET balance = balance + ?, updated_at = NOW() 
            WHERE id = ?
        ");
        return $stmt->execute([$amount, $id]);
    }

    public function ban(int $id, string $reason = ''): bool
    {
        $this->db->beginTransaction();
        
        try {
            // Обновляем статус пользователя
            $stmt = $this->db->prepare("
                UPDATE users 
                SET status = 'banned', updated_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$id]);
            
            // Записываем лог
            $this->logAction($id, 'user_banned', $reason);
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    public function unban(int $id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET status = 'active', updated_at = NOW() 
            WHERE id = ?
        ");
        return $stmt->execute([$id]);
    }

    public function updateRating(int $id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE users u
            SET rating = (
                SELECT COALESCE(AVG(r.rating), 0)
                FROM reviews r
                JOIN purchases p ON r.purchase_id = p.id
                WHERE p.seller_id = u.id
            ), updated_at = NOW()
            WHERE u.id = ?
        ");
        return $stmt->execute([$id]);
    }

    public function getAll(int $page = 1, int $perPage = 20, array $filters = []): array
    {
        $offset = ($page - 1) * $perPage;
        $where = ["1=1"];
        $params = [];
        
        if (!empty($filters['search'])) {
            $where[] = "(username LIKE ? OR email LIKE ?)";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }
        
        if (!empty($filters['status'])) {
            $where[] = "status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['role'])) {
            $where[] = "role = ?";
            $params[] = $filters['role'];
        }
        
        $params[] = $perPage;
        $params[] = $offset;
        
        $sql = "
            SELECT id, email, username, avatar, status, role, balance, rating, 
                   total_sales, subscription_type, created_at 
            FROM users 
            WHERE " . implode(' AND ', $where) . "
            ORDER BY created_at DESC 
            LIMIT ? OFFSET ?
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return $stmt->fetchAll();
    }

    public function getCount(array $filters = []): int
    {
        $where = ["1=1"];
        $params = [];
        
        if (!empty($filters['search'])) {
            $where[] = "(username LIKE ? OR email LIKE ?)";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }
        
        if (!empty($filters['status'])) {
            $where[] = "status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['role'])) {
            $where[] = "role = ?";
            $params[] = $filters['role'];
        }
        
        $sql = "SELECT COUNT(*) FROM users WHERE " . implode(' AND ', $where);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return (int) $stmt->fetchColumn();
    }

    public function getStats(): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_users,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_users,
                SUM(CASE WHEN status = 'banned' THEN 1 ELSE 0 END) as banned_users,
                SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admin_users,
                SUM(CASE WHEN role = 'seller' THEN 1 ELSE 0 END) as seller_users,
                AVG(rating) as avg_rating,
                SUM(balance) as total_balance
            FROM users
        ");
        $stmt->execute();
        
        return $stmt->fetch();
    }

    private function logAction(int $userId, string $action, string $details = ''): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO activity_logs (user_id, action, details, ip_address, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $userId,
            $action,
            $details,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
    }
}