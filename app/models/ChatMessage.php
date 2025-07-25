<?php
namespace App\Models;

class ChatMessage {
    public $id;
    public $sender_id;
    public $receiver_id;
    public $message;
    public $message_type;
    public $file_url;
    public $is_read;
    public $read_at;
    public $created_at;

    public static function send($data, $db) {
        $stmt = $db->prepare("INSERT INTO chat_messages (sender_id, receiver_id, message, message_type, file_url) 
                             VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['sender_id'],
            $data['receiver_id'],
            $data['message'],
            $data['message_type'] ?? 'text',
            $data['file_url'] ?? null
        ]);
    }

    public static function getConversation($user1Id, $user2Id, $db, $limit = 50, $offset = 0) {
        $stmt = $db->prepare("SELECT cm.*, 
                             s.login as sender_login, s.avatar as sender_avatar,
                             r.login as receiver_login, r.avatar as receiver_avatar
                             FROM chat_messages cm
                             JOIN users s ON cm.sender_id = s.id
                             JOIN users r ON cm.receiver_id = r.id
                             WHERE (cm.sender_id = ? AND cm.receiver_id = ?) 
                                OR (cm.sender_id = ? AND cm.receiver_id = ?)
                             ORDER BY cm.created_at DESC
                             LIMIT ? OFFSET ?");
        $stmt->execute([$user1Id, $user2Id, $user2Id, $user1Id, $limit, $offset]);
        return array_reverse($stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public static function markAsRead($receiverId, $senderId, $db) {
        $stmt = $db->prepare("UPDATE chat_messages 
                             SET is_read = TRUE, read_at = NOW() 
                             WHERE receiver_id = ? AND sender_id = ? AND is_read = FALSE");
        return $stmt->execute([$receiverId, $senderId]);
    }

    public static function getUnreadCount($userId, $db) {
        $stmt = $db->prepare("SELECT COUNT(*) as count FROM chat_messages 
                             WHERE receiver_id = ? AND is_read = FALSE");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['count'];
    }

    public static function getConversations($userId, $db) {
        $stmt = $db->prepare("SELECT 
                                u.id, u.login, u.avatar, u.status,
                                (SELECT COUNT(*) FROM chat_messages 
                                 WHERE sender_id = u.id AND receiver_id = ? AND is_read = FALSE) as unread_count,
                                (SELECT message FROM chat_messages 
                                 WHERE (sender_id = u.id AND receiver_id = ?) 
                                    OR (sender_id = ? AND receiver_id = u.id)
                                 ORDER BY created_at DESC LIMIT 1) as last_message,
                                (SELECT created_at FROM chat_messages 
                                 WHERE (sender_id = u.id AND receiver_id = ?) 
                                    OR (sender_id = ? AND receiver_id = u.id)
                                 ORDER BY created_at DESC LIMIT 1) as last_message_time
                             FROM users u
                             WHERE u.id IN (
                                SELECT DISTINCT 
                                    CASE 
                                        WHEN sender_id = ? THEN receiver_id 
                                        ELSE sender_id 
                                    END
                                FROM chat_messages 
                                WHERE sender_id = ? OR receiver_id = ?
                             )
                             ORDER BY last_message_time DESC");
        $stmt->execute([$userId, $userId, $userId, $userId, $userId, $userId, $userId, $userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function deleteMessage($messageId, $userId, $db) {
        $stmt = $db->prepare("DELETE FROM chat_messages WHERE id = ? AND sender_id = ?");
        return $stmt->execute([$messageId, $userId]);
    }
}