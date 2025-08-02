<?php

class User {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function registerOrUpdate($userData) {
        $existingUser = $this->getUser($userData['id']);
        
        if ($existingUser) {
            // Обновляем существующего пользователя
            $this->db->update('users', [
                'username' => $userData['username'] ?? null,
                'first_name' => $userData['first_name'] ?? null,
                'last_name' => $userData['last_name'] ?? null,
                'updated_at' => date('Y-m-d H:i:s')
            ], 'telegram_id = :telegram_id', ['telegram_id' => $userData['id']]);
            
            return $existingUser['id'];
        } else {
            // Создаем нового пользователя
            return $this->db->insert('users', [
                'telegram_id' => $userData['id'],
                'username' => $userData['username'] ?? null,
                'first_name' => $userData['first_name'] ?? null,
                'last_name' => $userData['last_name'] ?? null,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
    
    public function getUser($telegramId) {
        return $this->db->fetch(
            'SELECT * FROM users WHERE telegram_id = :telegram_id',
            ['telegram_id' => $telegramId]
        );
    }
    
    public function updateBalance($telegramId, $amount, $operation = 'add') {
        $user = $this->getUser($telegramId);
        if (!$user) {
            return false;
        }
        
        $newBalance = $operation === 'add' 
            ? $user['balance'] + $amount 
            : $user['balance'] - $amount;
            
        if ($newBalance < 0) {
            return false; // Недостаточно средств
        }
        
        $this->db->update('users', [
            'balance' => $newBalance
        ], 'telegram_id = :telegram_id', ['telegram_id' => $telegramId]);
        
        return true;
    }
    
    public function updateRating($telegramId, $rating) {
        $user = $this->getUser($telegramId);
        if (!$user) {
            return false;
        }
        
        // Пересчитываем средний рейтинг
        $reviews = $this->db->fetchAll(
            'SELECT rating FROM reviews WHERE reviewed_id = :user_id',
            ['user_id' => $telegramId]
        );
        
        if (empty($reviews)) {
            $averageRating = $rating;
        } else {
            $totalRating = array_sum(array_column($reviews, 'rating')) + $rating;
            $averageRating = $totalRating / (count($reviews) + 1);
        }
        
        $this->db->update('users', [
            'rating' => round($averageRating, 2)
        ], 'telegram_id = :telegram_id', ['telegram_id' => $telegramId]);
        
        return true;
    }
    
    public function incrementDealsCount($telegramId) {
        $this->db->query(
            'UPDATE users SET deals_count = deals_count + 1 WHERE telegram_id = :telegram_id',
            ['telegram_id' => $telegramId]
        );
    }
    
    public function setVerified($telegramId, $verified = true) {
        $this->db->update('users', [
            'is_verified' => $verified
        ], 'telegram_id = :telegram_id', ['telegram_id' => $telegramId]);
    }
    
    public function banUser($telegramId, $banned = true) {
        $this->db->update('users', [
            'is_banned' => $banned
        ], 'telegram_id = :telegram_id', ['telegram_id' => $telegramId]);
    }
    
    public function isBanned($telegramId) {
        $user = $this->getUser($telegramId);
        return $user ? $user['is_banned'] : false;
    }
    
    public function isVerified($telegramId) {
        $user = $this->getUser($telegramId);
        return $user ? $user['is_verified'] : false;
    }
    
    public function updateProfile($telegramId, $data) {
        $allowedFields = ['phone', 'email', 'first_name', 'last_name'];
        $updateData = [];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }
        
        if (!empty($updateData)) {
            $updateData['updated_at'] = date('Y-m-d H:i:s');
            $this->db->update('users', $updateData, 'telegram_id = :telegram_id', ['telegram_id' => $telegramId]);
            return true;
        }
        
        return false;
    }
    
    public function getAllUsers($limit = 50, $offset = 0) {
        return $this->db->fetchAll(
            'SELECT * FROM users ORDER BY created_at DESC LIMIT :limit OFFSET :offset',
            ['limit' => $limit, 'offset' => $offset]
        );
    }
    
    public function getUserStats($telegramId) {
        $user = $this->getUser($telegramId);
        if (!$user) {
            return null;
        }
        
        // Статистика сделок
        $dealStats = $this->db->fetch('
            SELECT 
                COUNT(*) as total_deals,
                COUNT(CASE WHEN status = "completed" THEN 1 END) as completed_deals,
                COUNT(CASE WHEN status = "disputed" THEN 1 END) as disputed_deals,
                SUM(CASE WHEN status = "completed" THEN amount ELSE 0 END) as total_amount
            FROM deals 
            WHERE seller_id = :user_id OR buyer_id = :user_id
        ', ['user_id' => $telegramId]);
        
        // Средний рейтинг
        $avgRating = $this->db->fetch('
            SELECT AVG(rating) as avg_rating, COUNT(*) as reviews_count
            FROM reviews 
            WHERE reviewed_id = :user_id
        ', ['user_id' => $telegramId]);
        
        return [
            'user' => $user,
            'deals' => $dealStats,
            'rating' => $avgRating
        ];
    }
}