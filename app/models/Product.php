<?php

namespace App\Models;

use PDO;

class Product
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function findAll(int $page = 1, int $perPage = 20, array $filters = []): array
    {
        $offset = ($page - 1) * $perPage;
        $where = ["p.status = 'active'"];
        $params = [];

        // Применяем фильтры
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
            $where[] = "(p.title LIKE ? OR p.description LIKE ? OR p.short_description LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        // Сортировка
        $orderBy = "p.created_at DESC";
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'price_asc':
                    $orderBy = "p.price ASC";
                    break;
                case 'price_desc':
                    $orderBy = "p.price DESC";
                    break;
                case 'rating':
                    $orderBy = "p.rating DESC, p.total_reviews DESC";
                    break;
                case 'popular':
                    $orderBy = "p.views DESC";
                    break;
                case 'newest':
                    $orderBy = "p.created_at DESC";
                    break;
                case 'oldest':
                    $orderBy = "p.created_at ASC";
                    break;
            }
        }

        $params[] = $perPage;
        $params[] = $offset;

        $sql = "
            SELECT p.*, u.username as seller_name, u.rating as seller_rating, 
                   u.total_sales, u.id as seller_id,
                   (SELECT COUNT(*) FROM favorites f WHERE f.product_id = p.id) as favorites_count
            FROM products p 
            JOIN users u ON p.user_id = u.id 
            WHERE " . implode(' AND ', $where) . "
            ORDER BY {$orderBy}
            LIMIT ? OFFSET ?
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT p.*, u.username as seller_name, u.rating as seller_rating, 
                   u.total_sales, u.id as seller_id,
                   (SELECT COUNT(*) FROM favorites f WHERE f.product_id = p.id) as favorites_count
            FROM products p 
            JOIN users u ON p.user_id = u.id 
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        
        return $product ?: null;
    }

    public function findByUser(int $userId, int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        
        $stmt = $this->db->prepare("
            SELECT p.*, 
                   (SELECT COUNT(*) FROM favorites f WHERE f.product_id = p.id) as favorites_count
            FROM products p
            WHERE p.user_id = ? 
            ORDER BY p.created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$userId, $perPage, $offset]);
        return $stmt->fetchAll();
    }

    public function getCount(array $filters = []): int
    {
        $where = ["status = 'active'"];
        $params = [];

        if (!empty($filters['game'])) {
            $where[] = "game = ?";
            $params[] = $filters['game'];
        }

        if (!empty($filters['type'])) {
            $where[] = "type = ?";
            $params[] = $filters['type'];
        }

        if (!empty($filters['min_price'])) {
            $where[] = "price >= ?";
            $params[] = $filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $where[] = "price <= ?";
            $params[] = $filters['max_price'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(title LIKE ? OR description LIKE ? OR short_description LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $sql = "SELECT COUNT(*) FROM products WHERE " . implode(' AND ', $where);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        
        return (int) $stmt->fetchColumn();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO products (
                user_id, type, game, title, description, short_description, 
                price, currency, original_price, images, specifications, 
                delivery_info, delivery_time, auto_delivery, instant_delivery, 
                warranty_days, stock_quantity, tags, status, visibility, created_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()
            )
        ");
        
        $stmt->execute([
            $data['user_id'],
            $data['type'],
            $data['game'],
            $data['title'],
            $data['description'],
            $data['short_description'] ?? substr($data['description'], 0, 200),
            $data['price'],
            $data['currency'] ?? 'RUB',
            $data['original_price'] ?? null,
            isset($data['images']) ? json_encode($data['images']) : null,
            isset($data['specifications']) ? json_encode($data['specifications']) : null,
            $data['delivery_info'] ?? null,
            $data['delivery_time'] ?? null,
            $data['auto_delivery'] ?? false,
            $data['instant_delivery'] ?? false,
            $data['warranty_days'] ?? 0,
            $data['stock_quantity'] ?? 1,
            isset($data['tags']) ? json_encode($data['tags']) : null,
            $data['status'] ?? 'pending',
            $data['visibility'] ?? 'public'
        ]);
        
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $values = [];
        
        $allowedFields = [
            'type', 'game', 'title', 'description', 'short_description', 
            'price', 'currency', 'original_price', 'images', 'specifications',
            'delivery_info', 'delivery_time', 'auto_delivery', 'instant_delivery',
            'warranty_days', 'stock_quantity', 'tags', 'status', 'visibility'
        ];
        
        foreach ($data as $field => $value) {
            if (in_array($field, $allowedFields)) {
                $fields[] = "{$field} = ?";
                if (in_array($field, ['images', 'specifications', 'tags']) && is_array($value)) {
                    $values[] = json_encode($value);
                } else {
                    $values[] = $value;
                }
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $id;
        $sql = "UPDATE products SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function incrementViews(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE products SET views = views + 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function updateRating(int $id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE products p
            SET 
                rating = (
                    SELECT COALESCE(AVG(r.rating), 0)
                    FROM reviews r
                    WHERE r.product_id = p.id AND r.status = 'published'
                ),
                total_reviews = (
                    SELECT COUNT(*)
                    FROM reviews r
                    WHERE r.product_id = p.id AND r.status = 'published'
                ),
                updated_at = NOW()
            WHERE p.id = ?
        ");
        return $stmt->execute([$id]);
    }

    public function getGames(): array
    {
        $stmt = $this->db->prepare("
            SELECT DISTINCT game, COUNT(*) as count 
            FROM products 
            WHERE status = 'active' 
            GROUP BY game 
            ORDER BY count DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getTypes(): array
    {
        $stmt = $this->db->prepare("
            SELECT DISTINCT type, COUNT(*) as count 
            FROM products 
            WHERE status = 'active' 
            GROUP BY type 
            ORDER BY count DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getFeatured(int $limit = 6): array
    {
        $stmt = $this->db->prepare("
            SELECT p.*, u.username as seller_name, u.rating as seller_rating
            FROM products p 
            JOIN users u ON p.user_id = u.id 
            WHERE p.status = 'active' 
              AND (p.visibility = 'featured' OR p.featured_until > NOW())
            ORDER BY p.created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getRecommended(int $userId = null, int $limit = 6): array
    {
        if ($userId) {
            // Рекомендации на основе предпочтений пользователя
            $stmt = $this->db->prepare("
                SELECT p.*, u.username as seller_name, u.rating as seller_rating
                FROM products p 
                JOIN users u ON p.user_id = u.id 
                WHERE p.status = 'active' 
                  AND p.user_id != ?
                  AND p.game IN (
                      SELECT DISTINCT game 
                      FROM favorites f 
                      JOIN products fp ON f.product_id = fp.id 
                      WHERE f.user_id = ?
                  )
                ORDER BY p.rating DESC, p.created_at DESC 
                LIMIT ?
            ");
            $stmt->execute([$userId, $userId, $limit]);
        } else {
            // Общие рекомендации
            $stmt = $this->db->prepare("
                SELECT p.*, u.username as seller_name, u.rating as seller_rating
                FROM products p 
                JOIN users u ON p.user_id = u.id 
                WHERE p.status = 'active' 
                ORDER BY p.rating DESC, p.views DESC, p.created_at DESC 
                LIMIT ?
            ");
            $stmt->execute([$limit]);
        }
        
        return $stmt->fetchAll();
    }

    public function getStats(): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_products,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_products,
                SUM(CASE WHEN status = 'sold' THEN 1 ELSE 0 END) as sold_products,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_products,
                AVG(price) as avg_price,
                MIN(price) as min_price,
                MAX(price) as max_price
            FROM products
        ");
        $stmt->execute();
        
        return $stmt->fetch();
    }
}