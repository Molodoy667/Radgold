<?php
require_once '../core/config.php';
require_once '../core/functions.php';

// Перевірка авторизації
if (!isLoggedIn()) {
    http_response_code(403);
    echo json_encode(['error' => 'Доступ заборонено']);
    exit();
}

header('Content-Type: application/json');

try {
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    $userId = $_SESSION['user_id'];
    
    switch ($action) {
        case 'get_chats':
            echo json_encode(getUserChats($userId));
            break;
            
        case 'get_messages':
            echo json_encode(getChatMessages($userId));
            break;
            
        case 'send_message':
            echo json_encode(sendMessage($userId));
            break;
            
        case 'start_chat':
            echo json_encode(startChat($userId));
            break;
            
        case 'mark_read':
            echo json_encode(markMessagesAsRead($userId));
            break;
            
        case 'delete_chat':
            echo json_encode(deleteChat($userId));
            break;
            
        case 'block_user':
            echo json_encode(blockUser($userId));
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Невідома дія']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function getUserChats($userId) {
    try {
        $db = Database::getInstance();
        
        // Отримуємо всі чати користувача
        $stmt = $db->prepare("
            SELECT 
                c.id,
                c.ad_id,
                c.buyer_id,
                c.seller_id,
                c.created_at,
                c.updated_at,
                a.title as ad_title,
                a.status as ad_status,
                (SELECT filename FROM ad_images WHERE ad_id = a.id AND is_main = 1 LIMIT 1) as ad_image,
                
                -- Інформація про співрозмовника
                CASE 
                    WHEN c.buyer_id = ? THEN s.username
                    ELSE b.username
                END as other_user_name,
                
                CASE 
                    WHEN c.buyer_id = ? THEN s.first_name
                    ELSE b.first_name
                END as other_user_first_name,
                
                CASE 
                    WHEN c.buyer_id = ? THEN s.last_name
                    ELSE b.last_name
                END as other_user_last_name,
                
                CASE 
                    WHEN c.buyer_id = ? THEN s.avatar
                    ELSE b.avatar
                END as other_user_avatar,
                
                CASE 
                    WHEN c.buyer_id = ? THEN s.id
                    ELSE b.id
                END as other_user_id,
                
                -- Останнє повідомлення
                (SELECT message FROM chat_messages WHERE chat_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message,
                (SELECT created_at FROM chat_messages WHERE chat_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message_date,
                (SELECT sender_id FROM chat_messages WHERE chat_id = c.id ORDER BY created_at DESC LIMIT 1) as last_message_sender,
                
                -- Кількість непрочитаних повідомлень
                (SELECT COUNT(*) FROM chat_messages WHERE chat_id = c.id AND receiver_id = ? AND is_read = FALSE) as unread_count
                
            FROM chats c
            JOIN ads a ON c.ad_id = a.id
            JOIN users b ON c.buyer_id = b.id
            JOIN users s ON c.seller_id = s.id
            WHERE c.buyer_id = ? OR c.seller_id = ?
            ORDER BY 
                CASE 
                    WHEN (SELECT COUNT(*) FROM chat_messages WHERE chat_id = c.id AND receiver_id = ? AND is_read = FALSE) > 0 THEN 0
                    ELSE 1
                END,
                c.updated_at DESC
        ");
        
        $stmt->bind_param("iiiiiiiiii", $userId, $userId, $userId, $userId, $userId, $userId, $userId, $userId, $userId, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $chats = [];
        while ($row = $result->fetch_assoc()) {
            // Форматуємо дати
            $row['created_at_formatted'] = timeAgo($row['created_at']);
            $row['last_message_formatted'] = $row['last_message_date'] ? timeAgo($row['last_message_date']) : null;
            
            // URL аватара
            if ($row['other_user_avatar']) {
                $row['other_user_avatar_url'] = '/images/avatars/' . $row['other_user_avatar'];
            } else {
                $row['other_user_avatar_url'] = '/images/default-avatar.svg';
            }
            
            // URL зображення оголошення
            if ($row['ad_image']) {
                $row['ad_image_url'] = '/images/thumbs/' . $row['ad_image'];
            } else {
                $row['ad_image_url'] = '/images/no-image.svg';
            }
            
            // Повне ім'я співрозмовника
            $row['other_user_full_name'] = trim($row['other_user_first_name'] . ' ' . $row['other_user_last_name']) ?: $row['other_user_name'];
            
            // Скорочене останнє повідомлення
            if ($row['last_message']) {
                $row['last_message_short'] = mb_strlen($row['last_message']) > 50 
                    ? mb_substr($row['last_message'], 0, 50) . '...' 
                    : $row['last_message'];
            }
            
            // Визначаємо роль користувача в чаті
            $row['user_role'] = ($row['buyer_id'] == $userId) ? 'buyer' : 'seller';
            
            $chats[] = $row;
        }
        
        return [
            'success' => true,
            'data' => $chats
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function getChatMessages($userId) {
    try {
        $db = Database::getInstance();
        
        $chatId = (int)($_GET['chat_id'] ?? 0);
        $page = (int)($_GET['page'] ?? 1);
        $limit = (int)($_GET['limit'] ?? 20);
        
        if (!$chatId) {
            throw new Exception('Невірний ID чату');
        }
        
        // Перевіряємо доступ до чату
        $stmt = $db->prepare("
            SELECT id FROM chats 
            WHERE id = ? AND (buyer_id = ? OR seller_id = ?)
        ");
        $stmt->bind_param("iii", $chatId, $userId, $userId);
        $stmt->execute();
        
        if (!$stmt->get_result()->fetch_assoc()) {
            throw new Exception('Чат не знайдено');
        }
        
        $offset = ($page - 1) * $limit;
        
        // Отримуємо повідомлення
        $stmt = $db->prepare("
            SELECT 
                m.id,
                m.message,
                m.sender_id,
                m.receiver_id,
                m.is_read,
                m.created_at,
                u.username as sender_name,
                u.first_name as sender_first_name,
                u.avatar as sender_avatar
            FROM chat_messages m
            JOIN users u ON m.sender_id = u.id
            WHERE m.chat_id = ?
            ORDER BY m.created_at DESC
            LIMIT ? OFFSET ?
        ");
        
        $stmt->bind_param("iii", $chatId, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $messages = [];
        while ($row = $result->fetch_assoc()) {
            // Форматуємо дату
            $row['created_at_formatted'] = date('d.m.Y H:i', strtotime($row['created_at']));
            $row['time_ago'] = timeAgo($row['created_at']);
            
            // URL аватара
            if ($row['sender_avatar']) {
                $row['sender_avatar_url'] = '/images/avatars/' . $row['sender_avatar'];
            } else {
                $row['sender_avatar_url'] = '/images/default-avatar.svg';
            }
            
            // Чи це повідомлення від поточного користувача
            $row['is_own'] = ($row['sender_id'] == $userId);
            
            $messages[] = $row;
        }
        
        // Повертаємо в хронологічному порядку
        $messages = array_reverse($messages);
        
        // Позначаємо непрочитані повідомлення як прочитані
        $stmt = $db->prepare("
            UPDATE chat_messages 
            SET is_read = TRUE 
            WHERE chat_id = ? AND receiver_id = ? AND is_read = FALSE
        ");
        $stmt->bind_param("ii", $chatId, $userId);
        $stmt->execute();
        
        return [
            'success' => true,
            'data' => $messages
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function sendMessage($userId) {
    try {
        $db = Database::getInstance();
        
        $chatId = (int)($_POST['chat_id'] ?? 0);
        $message = trim($_POST['message'] ?? '');
        
        if (!$chatId || empty($message)) {
            throw new Exception('Заповніть всі поля');
        }
        
        if (mb_strlen($message) > 1000) {
            throw new Exception('Повідомлення занадто довге (максимум 1000 символів)');
        }
        
        // Перевіряємо доступ до чату та отримуємо інформацію
        $stmt = $db->prepare("
            SELECT buyer_id, seller_id, ad_id
            FROM chats 
            WHERE id = ? AND (buyer_id = ? OR seller_id = ?)
        ");
        $stmt->bind_param("iii", $chatId, $userId, $userId);
        $stmt->execute();
        $chat = $stmt->get_result()->fetch_assoc();
        
        if (!$chat) {
            throw new Exception('Чат не знайдено');
        }
        
        // Визначаємо отримувача
        $receiverId = ($chat['buyer_id'] == $userId) ? $chat['seller_id'] : $chat['buyer_id'];
        
        // Додаємо повідомлення
        $stmt = $db->prepare("
            INSERT INTO chat_messages (chat_id, sender_id, receiver_id, message, ad_id, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param("iiiis", $chatId, $userId, $receiverId, $message, $chat['ad_id']);
        
        if (!$stmt->execute()) {
            throw new Exception('Помилка відправки повідомлення');
        }
        
        $messageId = $db->insert_id;
        
        // Оновлюємо час останньої активності чату
        $stmt = $db->prepare("UPDATE chats SET updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("i", $chatId);
        $stmt->execute();
        
        // Отримуємо відправлене повідомлення з деталями
        $stmt = $db->prepare("
            SELECT 
                m.id,
                m.message,
                m.sender_id,
                m.receiver_id,
                m.is_read,
                m.created_at,
                u.username as sender_name,
                u.first_name as sender_first_name,
                u.avatar as sender_avatar
            FROM chat_messages m
            JOIN users u ON m.sender_id = u.id
            WHERE m.id = ?
        ");
        $stmt->bind_param("i", $messageId);
        $stmt->execute();
        $messageData = $stmt->get_result()->fetch_assoc();
        
        // Форматуємо дату
        $messageData['created_at_formatted'] = date('d.m.Y H:i', strtotime($messageData['created_at']));
        $messageData['time_ago'] = timeAgo($messageData['created_at']);
        $messageData['is_own'] = true;
        
        // URL аватара
        if ($messageData['sender_avatar']) {
            $messageData['sender_avatar_url'] = '/images/avatars/' . $messageData['sender_avatar'];
        } else {
            $messageData['sender_avatar_url'] = '/images/default-avatar.svg';
        }
        
        // TODO: Відправити push-сповіщення отримувачу
        // TODO: Відправити email-сповіщення (якщо налаштовано)
        
        return [
            'success' => true,
            'message' => 'Повідомлення відправлено',
            'data' => $messageData
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function startChat($userId) {
    try {
        $db = Database::getInstance();
        
        $adId = (int)($_POST['ad_id'] ?? 0);
        $sellerId = (int)($_POST['seller_id'] ?? 0);
        
        if (!$adId) {
            throw new Exception('Невірний ID оголошення');
        }
        
        // Отримуємо інформацію про оголошення
        $stmt = $db->prepare("
            SELECT user_id, title, status 
            FROM ads 
            WHERE id = ? AND status = 'active'
        ");
        $stmt->bind_param("i", $adId);
        $stmt->execute();
        $ad = $stmt->get_result()->fetch_assoc();
        
        if (!$ad) {
            throw new Exception('Оголошення не знайдено або неактивне');
        }
        
        $sellerId = $ad['user_id'];
        
        // Перевіряємо, що користувач не намагається написати сам собі
        if ($sellerId == $userId) {
            throw new Exception('Не можна написати самому собі');
        }
        
        // Перевіряємо чи вже існує чат
        $stmt = $db->prepare("
            SELECT id FROM chats 
            WHERE ad_id = ? AND buyer_id = ? AND seller_id = ?
        ");
        $stmt->bind_param("iii", $adId, $userId, $sellerId);
        $stmt->execute();
        $existingChat = $stmt->get_result()->fetch_assoc();
        
        if ($existingChat) {
            return [
                'success' => true,
                'chat_id' => $existingChat['id'],
                'message' => 'Чат вже існує'
            ];
        }
        
        // Створюємо новий чат
        $stmt = $db->prepare("
            INSERT INTO chats (ad_id, buyer_id, seller_id, created_at, updated_at) 
            VALUES (?, ?, ?, NOW(), NOW())
        ");
        $stmt->bind_param("iii", $adId, $userId, $sellerId);
        
        if (!$stmt->execute()) {
            throw new Exception('Помилка створення чату');
        }
        
        $chatId = $db->insert_id;
        
        // Надсилаємо привітальне повідомлення
        $welcomeMessage = "Вітаю! Мене цікавить ваше оголошення: " . $ad['title'];
        
        $stmt = $db->prepare("
            INSERT INTO chat_messages (chat_id, sender_id, receiver_id, message, ad_id, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param("iiiis", $chatId, $userId, $sellerId, $welcomeMessage, $adId);
        $stmt->execute();
        
        return [
            'success' => true,
            'chat_id' => $chatId,
            'message' => 'Чат створено успішно'
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function markMessagesAsRead($userId) {
    try {
        $db = Database::getInstance();
        
        $chatId = (int)($_POST['chat_id'] ?? 0);
        
        if (!$chatId) {
            throw new Exception('Невірний ID чату');
        }
        
        // Позначаємо повідомлення як прочитані
        $stmt = $db->prepare("
            UPDATE chat_messages 
            SET is_read = TRUE 
            WHERE chat_id = ? AND receiver_id = ? AND is_read = FALSE
        ");
        $stmt->bind_param("ii", $chatId, $userId);
        
        if (!$stmt->execute()) {
            throw new Exception('Помилка оновлення повідомлень');
        }
        
        $affectedRows = $stmt->affected_rows;
        
        return [
            'success' => true,
            'message' => "Позначено як прочитані: $affectedRows повідомлень"
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function deleteChat($userId) {
    try {
        $db = Database::getInstance();
        
        $chatId = (int)($_POST['chat_id'] ?? 0);
        
        if (!$chatId) {
            throw new Exception('Невірний ID чату');
        }
        
        // Перевіряємо доступ
        $stmt = $db->prepare("
            SELECT id FROM chats 
            WHERE id = ? AND (buyer_id = ? OR seller_id = ?)
        ");
        $stmt->bind_param("iii", $chatId, $userId, $userId);
        $stmt->execute();
        
        if (!$stmt->get_result()->fetch_assoc()) {
            throw new Exception('Чат не знайдено');
        }
        
        // Видаляємо чат (CASCADE видалить повідомлення)
        $stmt = $db->prepare("DELETE FROM chats WHERE id = ?");
        $stmt->bind_param("i", $chatId);
        
        if (!$stmt->execute()) {
            throw new Exception('Помилка видалення чату');
        }
        
        return [
            'success' => true,
            'message' => 'Чат видалено'
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

function blockUser($userId) {
    try {
        $db = Database::getInstance();
        
        $targetUserId = (int)($_POST['user_id'] ?? 0);
        
        if (!$targetUserId || $targetUserId == $userId) {
            throw new Exception('Невірний ID користувача');
        }
        
        // Додаємо до чорного списку
        $stmt = $db->prepare("
            INSERT IGNORE INTO user_blocks (user_id, blocked_user_id, created_at) 
            VALUES (?, ?, NOW())
        ");
        $stmt->bind_param("ii", $userId, $targetUserId);
        
        if (!$stmt->execute()) {
            throw new Exception('Помилка блокування користувача');
        }
        
        // Видаляємо всі чати з цим користувачем
        $stmt = $db->prepare("
            DELETE FROM chats 
            WHERE (buyer_id = ? AND seller_id = ?) OR (buyer_id = ? AND seller_id = ?)
        ");
        $stmt->bind_param("iiii", $userId, $targetUserId, $targetUserId, $userId);
        $stmt->execute();
        
        return [
            'success' => true,
            'message' => 'Користувача заблоковано'
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

// Helper function для форматування часу
function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) {
        return 'щойно';
    } elseif ($time < 3600) {
        $minutes = floor($time / 60);
        return $minutes . ' хв тому';
    } elseif ($time < 86400) {
        $hours = floor($time / 3600);
        return $hours . ' год тому';
    } elseif ($time < 2592000) {
        $days = floor($time / 86400);
        return $days . ' дн тому';
    } else {
        return date('d.m.Y', strtotime($datetime));
    }
}
?>