<?php

namespace App\Models;

use PDO;

class Review
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getByProduct(int $productId, int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        
        $stmt = $this->db->prepare("
            SELECT r.*, u.username, u.avatar,
                   p.buyer_id, p.created_at as purchase_date
            FROM reviews r
            JOIN purchases p ON r.purchase_id = p.id
            JOIN users u ON r.reviewer_id = u.id
            WHERE r.product_id = ? AND r.status = 'published'
            ORDER BY r.created_at DESC
            LIMIT ? OFFSET ?
        ");
        
        $stmt->execute([$productId, $perPage, $offset]);
        return $stmt->fetchAll();
    }

    public function getByUser(int $userId, int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        
        $stmt = $this->db->prepare("
            SELECT r.*, p.title as product_title, u.username as seller_name
            FROM reviews r
            JOIN products p ON r.product_id = p.id
            JOIN users u ON p.user_id = u.id
            WHERE r.reviewer_id = ?
            ORDER BY r.created_at DESC
            LIMIT ? OFFSET ?
        ");
        
        $stmt->execute([$userId, $perPage, $offset]);
        return $stmt->fetchAll();
    }

    public function getBySeller(int $sellerId, int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        
        $stmt = $this->db->prepare("
            SELECT r.*, p.title as product_title, u.username as reviewer_name
            FROM reviews r
            JOIN products p ON r.product_id = p.id
            JOIN users u ON r.reviewer_id = u.id
            WHERE r.seller_id = ? AND r.status = 'published'
            ORDER BY r.created_at DESC
            LIMIT ? OFFSET ?
        ");
        
        $stmt->execute([$sellerId, $perPage, $offset]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO reviews (
                purchase_id, product_id, reviewer_id, seller_id, 
                rating, comment, pros, cons, is_anonymous, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $result = $stmt->execute([
            $data['purchase_id'],
            $data['product_id'],
            $data['reviewer_id'],
            $data['seller_id'],
            $data['rating'],
            $data['comment'] ?? null,
            $data['pros'] ?? null,
            $data['cons'] ?? null,
            $data['is_anonymous'] ?? false
        ]);
        
        if ($result) {
            $reviewId = (int) $this->db->lastInsertId();
            
            // Обновляем рейтинг товара и продавца
            $this->updateProductRating($data['product_id']);
            $this->updateSellerRating($data['seller_id']);
            
            return $reviewId;
        }
        
        return 0;
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $values = [];
        
        $allowedFields = ['rating', 'comment', 'pros', 'cons', 'is_anonymous'];
        
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
        $sql = "UPDATE reviews SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute($values);
        
        if ($result) {
            // Получаем информацию о отзыве для обновления рейтингов
            $review = $this->findById($id);
            if ($review) {
                $this->updateProductRating($review['product_id']);
                $this->updateSellerRating($review['seller_id']);
            }
        }
        
        return $result;
    }

    public function delete(int $id): bool
    {
        // Получаем информацию о отзыве перед удалением
        $review = $this->findById($id);
        
        $stmt = $this->db->prepare("DELETE FROM reviews WHERE id = ?");
        $result = $stmt->execute([$id]);
        
        if ($result && $review) {
            // Обновляем рейтинги после удаления
            $this->updateProductRating($review['product_id']);
            $this->updateSellerRating($review['seller_id']);
        }
        
        return $result;
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT r.*, u.username, p.title as product_title
            FROM reviews r
            JOIN users u ON r.reviewer_id = u.id
            JOIN products p ON r.product_id = p.id
            WHERE r.id = ?
        ");
        
        $stmt->execute([$id]);
        $review = $stmt->fetch();
        
        return $review ?: null;
    }

    public function canUserReview(int $userId, int $productId): bool
    {
        // Проверяем, купил ли пользователь товар
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM purchases 
            WHERE buyer_id = ? AND product_id = ? AND status = 'completed'
        ");
        $stmt->execute([$userId, $productId]);
        $hasPurchased = $stmt->fetchColumn() > 0;
        
        if (!$hasPurchased) {
            return false;
        }
        
        // Проверяем, не оставил ли уже отзыв
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM reviews r
            JOIN purchases p ON r.purchase_id = p.id
            WHERE p.buyer_id = ? AND r.product_id = ?
        ");
        $stmt->execute([$userId, $productId]);
        $hasReviewed = $stmt->fetchColumn() > 0;
        
        return !$hasReviewed;
    }

    public function getUserPurchaseForReview(int $userId, int $productId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT p.*, pr.title as product_title, u.username as seller_name
            FROM purchases p
            JOIN products pr ON p.product_id = pr.id
            JOIN users u ON p.seller_id = u.id
            WHERE p.buyer_id = ? AND p.product_id = ? AND p.status = 'completed'
            AND NOT EXISTS (
                SELECT 1 FROM reviews r WHERE r.purchase_id = p.id
            )
            ORDER BY p.completed_at DESC
            LIMIT 1
        ");
        
        $stmt->execute([$userId, $productId]);
        $purchase = $stmt->fetch();
        
        return $purchase ?: null;
    }

    public function addReply(int $reviewId, string $reply): bool
    {
        $stmt = $this->db->prepare("
            UPDATE reviews 
            SET reply = ?, reply_at = NOW(), updated_at = NOW() 
            WHERE id = ?
        ");
        
        return $stmt->execute([$reply, $reviewId]);
    }

    public function markHelpful(int $reviewId): bool
    {
        $stmt = $this->db->prepare("
            UPDATE reviews 
            SET helpful_count = helpful_count + 1, updated_at = NOW() 
            WHERE id = ?
        ");
        
        return $stmt->execute([$reviewId]);
    }

    public function moderateReview(int $id, string $status, string $reason = ''): bool
    {
        $stmt = $this->db->prepare("
            UPDATE reviews 
            SET status = ?, moderation_reason = ?, updated_at = NOW() 
            WHERE id = ?
        ");
        
        $result = $stmt->execute([$status, $reason, $id]);
        
        if ($result && $status === 'published') {
            // Обновляем рейтинги при публикации
            $review = $this->findById($id);
            if ($review) {
                $this->updateProductRating($review['product_id']);
                $this->updateSellerRating($review['seller_id']);
            }
        }
        
        return $result;
    }

    public function getStats(int $productId = null, int $sellerId = null): array
    {
        $where = ["r.status = 'published'"];
        $params = [];
        
        if ($productId) {
            $where[] = "r.product_id = ?";
            $params[] = $productId;
        }
        
        if ($sellerId) {
            $where[] = "r.seller_id = ?";
            $params[] = $sellerId;
        }
        
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_reviews,
                AVG(rating) as avg_rating,
                SUM(CASE WHEN rating = 5 THEN 1 ELSE 0 END) as rating_5,
                SUM(CASE WHEN rating = 4 THEN 1 ELSE 0 END) as rating_4,
                SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END) as rating_3,
                SUM(CASE WHEN rating = 2 THEN 1 ELSE 0 END) as rating_2,
                SUM(CASE WHEN rating = 1 THEN 1 ELSE 0 END) as rating_1
            FROM reviews r
            WHERE " . implode(' AND ', $where)
        );
        
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function getRecentReviews(int $limit = 10): array
    {
        $stmt = $this->db->prepare("
            SELECT r.*, u.username, p.title as product_title,
                   s.username as seller_name
            FROM reviews r
            JOIN users u ON r.reviewer_id = u.id
            JOIN products p ON r.product_id = p.id
            JOIN users s ON r.seller_id = s.id
            WHERE r.status = 'published'
            ORDER BY r.created_at DESC
            LIMIT ?
        ");
        
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    private function updateProductRating(int $productId): void
    {
        $stmt = $this->db->prepare("
            UPDATE products 
            SET 
                rating = (
                    SELECT COALESCE(AVG(rating), 0)
                    FROM reviews 
                    WHERE product_id = ? AND status = 'published'
                ),
                total_reviews = (
                    SELECT COUNT(*)
                    FROM reviews 
                    WHERE product_id = ? AND status = 'published'
                ),
                updated_at = NOW()
            WHERE id = ?
        ");
        
        $stmt->execute([$productId, $productId, $productId]);
    }

    private function updateSellerRating(int $sellerId): void
    {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET 
                rating = (
                    SELECT COALESCE(AVG(r.rating), 0)
                    FROM reviews r
                    WHERE r.seller_id = ? AND r.status = 'published'
                ),
                updated_at = NOW()
            WHERE id = ?
        ");
        
        $stmt->execute([$sellerId, $sellerId]);
    }
}