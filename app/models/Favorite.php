<?php

namespace App\Models;

use PDO;

class Favorite
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function isFavorite(int $userId, int $productId): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM favorites 
            WHERE user_id = ? AND product_id = ?
        ");
        $stmt->execute([$userId, $productId]);
        return $stmt->fetchColumn() > 0;
    }

    public function add(int $userId, int $productId): bool
    {
        // Проверяем, что товар существует и активен
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM products 
            WHERE id = ? AND status = 'active'
        ");
        $stmt->execute([$productId]);
        
        if ($stmt->fetchColumn() == 0) {
            return false;
        }

        // Проверяем, что не добавляем дубликат
        if ($this->isFavorite($userId, $productId)) {
            return true; // Уже в избранном
        }

        $stmt = $this->db->prepare("
            INSERT INTO favorites (user_id, product_id, created_at) 
            VALUES (?, ?, NOW())
        ");
        
        $result = $stmt->execute([$userId, $productId]);
        
        if ($result) {
            // Обновляем счетчик в таблице products
            $this->updateProductFavoritesCount($productId);
        }
        
        return $result;
    }

    public function remove(int $userId, int $productId): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM favorites 
            WHERE user_id = ? AND product_id = ?
        ");
        
        $result = $stmt->execute([$userId, $productId]);
        
        if ($result) {
            // Обновляем счетчик в таблице products
            $this->updateProductFavoritesCount($productId);
        }
        
        return $result;
    }

    public function toggle(int $userId, int $productId): bool
    {
        if ($this->isFavorite($userId, $productId)) {
            return $this->remove($userId, $productId);
        } else {
            return $this->add($userId, $productId);
        }
    }

    public function getUserFavorites(int $userId, int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        
        $stmt = $this->db->prepare("
            SELECT p.*, u.username as seller_name, u.rating as seller_rating,
                   f.created_at as favorited_at
            FROM favorites f
            JOIN products p ON f.product_id = p.id
            JOIN users u ON p.user_id = u.id
            WHERE f.user_id = ? AND p.status = 'active'
            ORDER BY f.created_at DESC
            LIMIT ? OFFSET ?
        ");
        
        $stmt->execute([$userId, $perPage, $offset]);
        return $stmt->fetchAll();
    }

    public function getUserFavoritesCount(int $userId): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM favorites f
            JOIN products p ON f.product_id = p.id
            WHERE f.user_id = ? AND p.status = 'active'
        ");
        
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public function getProductFavorites(int $productId, int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        
        $stmt = $this->db->prepare("
            SELECT u.id, u.username, u.avatar, f.created_at as favorited_at
            FROM favorites f
            JOIN users u ON f.user_id = u.id
            WHERE f.product_id = ?
            ORDER BY f.created_at DESC
            LIMIT ? OFFSET ?
        ");
        
        $stmt->execute([$productId, $perPage, $offset]);
        return $stmt->fetchAll();
    }

    public function getProductFavoritesCount(int $productId): int
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM favorites 
            WHERE product_id = ?
        ");
        
        $stmt->execute([$productId]);
        return (int) $stmt->fetchColumn();
    }

    public function clearUserFavorites(int $userId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM favorites WHERE user_id = ?");
        return $stmt->execute([$userId]);
    }

    public function clearProductFavorites(int $productId): bool
    {
        $stmt = $this->db->prepare("DELETE FROM favorites WHERE product_id = ?");
        return $stmt->execute([$productId]);
    }

    public function getMostFavorited(int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT p.*, u.username as seller_name, 
                   COUNT(f.id) as favorites_count
            FROM products p
            JOIN users u ON p.user_id = u.id
            LEFT JOIN favorites f ON p.id = f.product_id
            WHERE p.status = 'active'
            GROUP BY p.id
            ORDER BY favorites_count DESC, p.created_at DESC
            LIMIT ?
        ");
        
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getRecentlyFavorited(int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT p.*, u.username as seller_name,
                   MAX(f.created_at) as last_favorited
            FROM favorites f
            JOIN products p ON f.product_id = p.id
            JOIN users u ON p.user_id = u.id
            WHERE p.status = 'active'
            GROUP BY p.id
            ORDER BY last_favorited DESC
            LIMIT ?
        ");
        
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    private function updateProductFavoritesCount(int $productId): void
    {
        $stmt = $this->db->prepare("
            UPDATE products 
            SET favorites_count = (
                SELECT COUNT(*) 
                FROM favorites 
                WHERE product_id = ?
            )
            WHERE id = ?
        ");
        
        $stmt->execute([$productId, $productId]);
    }
}