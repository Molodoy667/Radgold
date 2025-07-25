<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Review;
use App\Models\Favorite;
use App\Models\ChatMessage;
use App\Models\Dispute;
use App\Models\Setting;

class UserController {
    
    public function profile($db) {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            return;
        }

        $userId = $_SESSION['user']['id'];
        $user = User::findById($userId, $db);
        $purchaseStats = Purchase::getStats($userId, $db);
        $favoritesCount = Favorite::getCount($userId, $db);
        $unreadMessages = ChatMessage::getUnreadCount($userId, $db);

        require_once __DIR__ . '/../views/user/profile.php';
    }

    public function settings($db) {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user']['id'];
            $email = $_POST['email'] ?? '';
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';

            // Проверяем текущий пароль
            $user = User::findById($userId, $db);
            if (!password_verify($currentPassword, $user['password'])) {
                echo json_encode(['success' => false, 'error' => 'Неверный текущий пароль']);
                return;
            }

            // Обновляем данные
            $updateData = ['email' => $email];
            if (!empty($newPassword)) {
                $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
            }

            if (User::update($userId, $updateData, $db)) {
                $_SESSION['user']['email'] = $email;
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Ошибка при обновлении данных']);
            }
        } else {
            $userId = $_SESSION['user']['id'];
            $user = User::findById($userId, $db);
            require_once __DIR__ . '/../views/user/settings.php';
        }
    }

    public function myProducts($db) {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            return;
        }

        $userId = $_SESSION['user']['id'];
        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $products = Product::findByUser($userId, $db, $limit, $offset);
        $totalProducts = Product::getCountByUser($userId, $db);
        $totalPages = ceil($totalProducts / $limit);

        require_once __DIR__ . '/../views/user/my_products.php';
    }

    public function myPurchases($db) {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            return;
        }

        $userId = $_SESSION['user']['id'];
        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $purchases = Purchase::findByBuyer($userId, $db, $limit, $offset);
        $stats = Purchase::getStats($userId, $db);

        require_once __DIR__ . '/../views/user/my_purchases.php';
    }

    public function mySales($db) {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            return;
        }

        $userId = $_SESSION['user']['id'];
        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $sales = Purchase::findBySeller($userId, $db, $limit, $offset);

        require_once __DIR__ . '/../views/user/my_sales.php';
    }

    public function favorites($db) {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            return;
        }

        $userId = $_SESSION['user']['id'];
        $page = $_GET['page'] ?? 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;

        $favorites = Favorite::findByUser($userId, $db, $limit, $offset);
        $totalFavorites = Favorite::getCount($userId, $db);
        $totalPages = ceil($totalFavorites / $limit);

        require_once __DIR__ . '/../views/user/favorites.php';
    }

    public function toggleFavorite($db) {
        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'error' => 'Не авторизован']);
            return;
        }

        $userId = $_SESSION['user']['id'];
        $productId = $_POST['product_id'] ?? 0;

        if (Favorite::isFavorite($userId, $productId, $db)) {
            Favorite::remove($userId, $productId, $db);
            echo json_encode(['success' => true, 'action' => 'removed']);
        } else {
            Favorite::add($userId, $productId, $db);
            echo json_encode(['success' => true, 'action' => 'added']);
        }
    }

    public function chat($db) {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            return;
        }

        $userId = $_SESSION['user']['id'];
        $conversations = ChatMessage::getConversations($userId, $db);

        require_once __DIR__ . '/../views/user/chat.php';
    }

    public function conversation($db) {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            return;
        }

        $userId = $_SESSION['user']['id'];
        $otherUserId = $_GET['user_id'] ?? 0;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $message = $_POST['message'] ?? '';
            if (!empty($message)) {
                ChatMessage::send([
                    'sender_id' => $userId,
                    'receiver_id' => $otherUserId,
                    'message' => $message
                ], $db);
            }
            echo json_encode(['success' => true]);
        } else {
            $messages = ChatMessage::getConversation($userId, $otherUserId, $db);
            $otherUser = User::findById($otherUserId, $db);
            
            // Отмечаем сообщения как прочитанные
            ChatMessage::markAsRead($userId, $otherUserId, $db);

            require_once __DIR__ . '/../views/user/conversation.php';
        }
    }

    public function getMessages($db) {
        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'error' => 'Не авторизован']);
            return;
        }

        $userId = $_SESSION['user']['id'];
        $otherUserId = $_GET['user_id'] ?? 0;
        $lastMessageId = $_GET['last_id'] ?? 0;

        $messages = ChatMessage::getConversation($userId, $otherUserId, $db);
        
        // Фильтруем только новые сообщения
        $newMessages = array_filter($messages, function($msg) use ($lastMessageId) {
            return $msg['id'] > $lastMessageId;
        });

        echo json_encode(['success' => true, 'messages' => array_values($newMessages)]);
    }

    public function disputes($db) {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            return;
        }

        $userId = $_SESSION['user']['id'];
        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $disputes = Dispute::findByUser($userId, $db, $limit, $offset);

        require_once __DIR__ . '/../views/user/disputes.php';
    }

    public function createDispute($db) {
        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'error' => 'Не авторизован']);
            return;
        }

        $userId = $_SESSION['user']['id'];
        $purchaseId = $_POST['purchase_id'] ?? 0;
        $type = $_POST['type'] ?? '';
        $subject = $_POST['subject'] ?? '';
        $description = $_POST['description'] ?? '';

        if (empty($subject) || empty($description)) {
            echo json_encode(['success' => false, 'error' => 'Заполните все поля']);
            return;
        }

        if (!Dispute::canCreateDispute($userId, $purchaseId, $db)) {
            echo json_encode(['success' => false, 'error' => 'Диспут уже создан для этой покупки']);
            return;
        }

        if (Dispute::create([
            'purchase_id' => $purchaseId,
            'user_id' => $userId,
            'type' => $type,
            'subject' => $subject,
            'description' => $description
        ], $db)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Ошибка при создании диспута']);
        }
    }

    public function reviews($db) {
        if (!isset($_SESSION['user'])) {
            header('Location: /login');
            return;
        }

        $userId = $_SESSION['user']['id'];
        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $reviews = Review::findByUser($userId, $db, $limit, $offset);

        require_once __DIR__ . '/../views/user/reviews.php';
    }

    public function createReview($db) {
        if (!isset($_SESSION['user'])) {
            echo json_encode(['success' => false, 'error' => 'Не авторизован']);
            return;
        }

        $userId = $_SESSION['user']['id'];
        $purchaseId = $_POST['purchase_id'] ?? 0;
        $rating = $_POST['rating'] ?? 0;
        $title = $_POST['title'] ?? '';
        $comment = $_POST['comment'] ?? '';

        if ($rating < 1 || $rating > 5) {
            echo json_encode(['success' => false, 'error' => 'Неверная оценка']);
            return;
        }

        if (!Review::canReview($userId, $purchaseId, $db)) {
            echo json_encode(['success' => false, 'error' => 'Отзыв уже оставлен для этой покупки']);
            return;
        }

        // Получаем информацию о покупке
        $purchase = Purchase::findById($purchaseId, $db);
        if (!$purchase || $purchase['buyer_id'] != $userId) {
            echo json_encode(['success' => false, 'error' => 'Покупка не найдена']);
            return;
        }

        if (Review::create([
            'product_id' => $purchase['product_id'],
            'user_id' => $userId,
            'purchase_id' => $purchaseId,
            'rating' => $rating,
            'title' => $title,
            'comment' => $comment
        ], $db)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Ошибка при создании отзыва']);
        }
    }
}