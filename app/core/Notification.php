<?php
namespace App\Core;

class Notification {
    private static $db;
    
    public static function init() {
        if (!self::$db) {
            self::$db = Router::getDb();
        }
    }
    
    public static function send($userId, $type, $title, $message, $data = []) {
        self::init();
        
        $sql = "INSERT INTO notifications (user_id, type, title, message, data, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = self::$db->prepare($sql);
        
        return $stmt->execute([
            $userId,
            $type,
            $title,
            $message,
            json_encode($data, JSON_UNESCAPED_UNICODE)
        ]);
    }
    
    public static function sendToAll($type, $title, $message, $data = []) {
        self::init();
        
        $sql = "SELECT id FROM users WHERE status = 'active'";
        $stmt = self::$db->prepare($sql);
        $stmt->execute();
        $users = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        
        $count = 0;
        foreach ($users as $userId) {
            if (self::send($userId, $type, $title, $message, $data)) {
                $count++;
            }
        }
        
        return $count;
    }
    
    public static function getUnread($userId) {
        self::init();
        
        $sql = "SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC";
        $stmt = self::$db->prepare($sql);
        $stmt->execute([$userId]);
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public static function getAll($userId, $limit = 50, $offset = 0) {
        self::init();
        
        $sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = self::$db->prepare($sql);
        $stmt->execute([$userId, $limit, $offset]);
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public static function markAsRead($notificationId, $userId = null) {
        self::init();
        
        $sql = "UPDATE notifications SET is_read = 1, read_at = NOW() WHERE id = ?";
        $params = [$notificationId];
        
        if ($userId) {
            $sql .= " AND user_id = ?";
            $params[] = $userId;
        }
        
        $stmt = self::$db->prepare($sql);
        return $stmt->execute($params);
    }
    
    public static function markAllAsRead($userId) {
        self::init();
        
        $sql = "UPDATE notifications SET is_read = 1, read_at = NOW() WHERE user_id = ? AND is_read = 0";
        $stmt = self::$db->prepare($sql);
        
        return $stmt->execute([$userId]);
    }
    
    public static function getUnreadCount($userId) {
        self::init();
        
        $sql = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0";
        $stmt = self::$db->prepare($sql);
        $stmt->execute([$userId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return (int) $result['count'];
    }
    
    public static function delete($notificationId, $userId = null) {
        self::init();
        
        $sql = "DELETE FROM notifications WHERE id = ?";
        $params = [$notificationId];
        
        if ($userId) {
            $sql .= " AND user_id = ?";
            $params[] = $userId;
        }
        
        $stmt = self::$db->prepare($sql);
        return $stmt->execute($params);
    }
    
    public static function deleteOld($days = 30) {
        self::init();
        
        $sql = "DELETE FROM notifications WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)";
        $stmt = self::$db->prepare($sql);
        
        return $stmt->execute([$days]);
    }
    
    // Предустановленные типы уведомлений
    public static function productSold($userId, $productId, $productTitle) {
        return self::send($userId, 'product_sold', 'Товар продан', "Ваш товар '{$productTitle}' был успешно продан", [
            'product_id' => $productId,
            'product_title' => $productTitle
        ]);
    }
    
    public static function newPurchase($userId, $productId, $productTitle) {
        return self::send($userId, 'new_purchase', 'Новая покупка', "Вы приобрели товар '{$productTitle}'", [
            'product_id' => $productId,
            'product_title' => $productTitle
        ]);
    }
    
    public static function newReview($userId, $productId, $productTitle, $rating) {
        return self::send($userId, 'new_review', 'Новый отзыв', "Получен новый отзыв на товар '{$productTitle}' (оценка: {$rating}/5)", [
            'product_id' => $productId,
            'product_title' => $productTitle,
            'rating' => $rating
        ]);
    }
    
    public static function newMessage($userId, $senderId, $senderName) {
        return self::send($userId, 'new_message', 'Новое сообщение', "Новое сообщение от пользователя {$senderName}", [
            'sender_id' => $senderId,
            'sender_name' => $senderName
        ]);
    }
    
    public static function disputeOpened($userId, $disputeId, $subject) {
        return self::send($userId, 'dispute_opened', 'Открыт диспут', "Открыт диспут: {$subject}", [
            'dispute_id' => $disputeId,
            'subject' => $subject
        ]);
    }
    
    public static function disputeResolved($userId, $disputeId, $subject) {
        return self::send($userId, 'dispute_resolved', 'Диспут решен', "Диспут '{$subject}' был решен", [
            'dispute_id' => $disputeId,
            'subject' => $subject
        ]);
    }
    
    public static function accountBanned($userId, $reason = '') {
        return self::send($userId, 'account_banned', 'Аккаунт заблокирован', "Ваш аккаунт был заблокирован" . ($reason ? ": {$reason}" : ''), [
            'reason' => $reason
        ]);
    }
    
    public static function accountUnbanned($userId) {
        return self::send($userId, 'account_unbanned', 'Аккаунт разблокирован', "Ваш аккаунт был разблокирован");
    }
    
    public static function productApproved($userId, $productId, $productTitle) {
        return self::send($userId, 'product_approved', 'Товар одобрен', "Ваш товар '{$productTitle}' был одобрен модератором", [
            'product_id' => $productId,
            'product_title' => $productTitle
        ]);
    }
    
    public static function productRejected($userId, $productId, $productTitle, $reason = '') {
        return self::send($userId, 'product_rejected', 'Товар отклонен', "Ваш товар '{$productTitle}' был отклонен модератором" . ($reason ? ": {$reason}" : ''), [
            'product_id' => $productId,
            'product_title' => $productTitle,
            'reason' => $reason
        ]);
    }
}