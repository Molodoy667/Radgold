<?php

require_once 'Database.php';
require_once 'User.php';
require_once 'Deal.php';

class TelegramBot {
    private $token;
    private $db;
    private $config;
    
    public function __construct($config) {
        $this->config = $config;
        $this->token = $config['telegram']['bot_token'];
        $this->db = new Database($config['database']);
    }
    
    public function sendMessage($chatId, $text, $keyboard = null, $parseMode = 'HTML') {
        $data = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => $parseMode
        ];
        
        if ($keyboard) {
            $data['reply_markup'] = json_encode($keyboard);
        }
        
        return $this->apiRequest('sendMessage', $data);
    }
    
    public function editMessage($chatId, $messageId, $text, $keyboard = null) {
        $data = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
            'parse_mode' => 'HTML'
        ];
        
        if ($keyboard) {
            $data['reply_markup'] = json_encode($keyboard);
        }
        
        return $this->apiRequest('editMessageText', $data);
    }
    
    public function deleteMessage($chatId, $messageId) {
        return $this->apiRequest('deleteMessage', [
            'chat_id' => $chatId,
            'message_id' => $messageId
        ]);
    }
    
    private function apiRequest($method, $data) {
        $url = "https://api.telegram.org/bot{$this->token}/{$method}";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        
        $result = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($result, true);
    }
    
    public function processUpdate($update) {
        if (isset($update['message'])) {
            $this->processMessage($update['message']);
        } elseif (isset($update['callback_query'])) {
            $this->processCallbackQuery($update['callback_query']);
        }
    }
    
    private function processMessage($message) {
        $chatId = $message['chat']['id'];
        $userId = $message['from']['id'];
        $text = $message['text'] ?? '';
        
        // Регистрируем или обновляем пользователя
        $user = new User($this->db);
        $user->registerOrUpdate($message['from']);
        
        // Обработка команд
        if (strpos($text, '/') === 0) {
            $this->processCommand($chatId, $userId, $text);
        } else {
            $this->processText($chatId, $userId, $text);
        }
    }
    
    private function processCommand($chatId, $userId, $command) {
        $user = new User($this->db);
        
        // Проверяем, не забанен ли пользователь
        if ($user->isBanned($userId)) {
            $this->sendMessage($chatId, "❌ Ваш аккаунт заблокирован. Обратитесь в поддержку.");
            return;
        }
        
        switch ($command) {
            case '/start':
                $this->handleStart($chatId, $userId);
                break;
                
            case '/help':
                $this->handleHelp($chatId);
                break;
                
            case '/create_deal':
                $this->handleCreateDeal($chatId, $userId);
                break;
                
            case '/my_deals':
                $this->handleMyDeals($chatId, $userId);
                break;
                
            case '/balance':
                $this->handleBalance($chatId, $userId);
                break;
                
            case '/profile':
                $this->handleProfile($chatId, $userId);
                break;
                
            case '/support':
                $this->handleSupport($chatId);
                break;
                
            default:
                $this->sendMessage($chatId, "❓ Неизвестная команда. Используйте /help для просмотра доступных команд.");
        }
    }
    
    private function handleStart($chatId, $userId) {
        $keyboard = [
            'keyboard' => [
                [['text' => '💼 Создать сделку'], ['text' => '📋 Мои сделки']],
                [['text' => '💰 Баланс'], ['text' => '👤 Профиль']],
                [['text' => '❓ Помощь'], ['text' => '📞 Поддержка']]
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => false
        ];
        
        $message = $this->config['messages']['welcome'] . "\n\n";
        $message .= str_replace('{percent}', $this->config['escrow']['commission_percent'], 
                              $this->config['messages']['commission_info']);
        
        $this->sendMessage($chatId, $message, $keyboard);
    }
    
    private function handleHelp($chatId) {
        $this->sendMessage($chatId, $this->config['messages']['help']);
    }
    
    private function handleCreateDeal($chatId, $userId) {
        $keyboard = [
            'inline_keyboard' => [
                [['text' => '🛒 Я покупатель', 'callback_data' => 'create_deal_buyer']],
                [['text' => '💰 Я продавец', 'callback_data' => 'create_deal_seller']],
                [['text' => '❌ Отмена', 'callback_data' => 'cancel']]
            ]
        ];
        
        $text = "💼 <b>Создание новой сделки</b>\n\n";
        $text .= "Выберите вашу роль в сделке:\n\n";
        $text .= "🛒 <b>Покупатель</b> - вы покупаете товар/услугу\n";
        $text .= "💰 <b>Продавец</b> - вы продаете товар/услугу\n\n";
        $text .= "ℹ️ Комиссия сервиса: {$this->config['escrow']['commission_percent']}%";
        
        $this->sendMessage($chatId, $text, $keyboard);
    }
    
    private function handleMyDeals($chatId, $userId) {
        $deal = new Deal($this->db);
        $deals = $deal->getUserDeals($userId);
        
        if (empty($deals)) {
            $this->sendMessage($chatId, "📋 У вас пока нет активных сделок.");
            return;
        }
        
        $text = "📋 <b>Ваши сделки:</b>\n\n";
        
        foreach ($deals as $dealData) {
            $status = $this->getStatusEmoji($dealData['status']);
            $text .= "🔹 <b>#{$dealData['deal_number']}</b>\n";
            $text .= "📝 {$dealData['title']}\n";
            $text .= "💰 {$dealData['amount']} руб.\n";
            $text .= "📊 Статус: {$status}\n";
            $text .= "📅 " . date('d.m.Y H:i', strtotime($dealData['created_at'])) . "\n\n";
        }
        
        $this->sendMessage($chatId, $text);
    }
    
    private function handleBalance($chatId, $userId) {
        $user = new User($this->db);
        $userData = $user->getUser($userId);
        
        $text = "💰 <b>Ваш баланс:</b> {$userData['balance']} руб.\n\n";
        $text .= "📊 <b>Статистика:</b>\n";
        $text .= "⭐ Рейтинг: {$userData['rating']}/5.0\n";
        $text .= "📈 Сделок завершено: {$userData['deals_count']}\n";
        $text .= "✅ Верификация: " . ($userData['is_verified'] ? 'Пройдена' : 'Не пройдена');
        
        $keyboard = [
            'inline_keyboard' => [
                [['text' => '💳 Пополнить баланс', 'callback_data' => 'add_balance']],
                [['text' => '💸 Вывести средства', 'callback_data' => 'withdraw']]
            ]
        ];
        
        $this->sendMessage($chatId, $text, $keyboard);
    }
    
    private function handleProfile($chatId, $userId) {
        $user = new User($this->db);
        $userData = $user->getUser($userId);
        
        $text = "👤 <b>Ваш профиль:</b>\n\n";
        $text .= "🆔 ID: {$userData['telegram_id']}\n";
        $text .= "👤 Имя: {$userData['first_name']} {$userData['last_name']}\n";
        $text .= "📱 Username: @{$userData['username']}\n";
        $text .= "📞 Телефон: " . ($userData['phone'] ?: 'Не указан') . "\n";
        $text .= "📧 Email: " . ($userData['email'] ?: 'Не указан') . "\n";
        $text .= "📅 Регистрация: " . date('d.m.Y', strtotime($userData['created_at']));
        
        $keyboard = [
            'inline_keyboard' => [
                [['text' => '✏️ Редактировать', 'callback_data' => 'edit_profile']],
                [['text' => '🔐 Верификация', 'callback_data' => 'verification']]
            ]
        ];
        
        $this->sendMessage($chatId, $text, $keyboard);
    }
    
    private function handleSupport($chatId) {
        $text = "📞 <b>Техническая поддержка</b>\n\n";
        $text .= "Если у вас возникли вопросы или проблемы, обратитесь к администратору:\n\n";
        $text .= "📧 Email: support@escrowbot.com\n";
        $text .= "💬 Telegram: @support_bot\n\n";
        $text .= "⏰ Время работы: 24/7";
        
        $this->sendMessage($chatId, $text);
    }
    
    private function processCallbackQuery($callbackQuery) {
        $chatId = $callbackQuery['message']['chat']['id'];
        $messageId = $callbackQuery['message']['message_id'];
        $userId = $callbackQuery['from']['id'];
        $data = $callbackQuery['data'];
        
        // Обработка callback данных
        $this->processCallback($chatId, $messageId, $userId, $data);
        
        // Подтверждаем получение callback
        $this->apiRequest('answerCallbackQuery', ['callback_query_id' => $callbackQuery['id']]);
    }
    
    private function processCallback($chatId, $messageId, $userId, $data) {
        switch ($data) {
            case 'create_deal_buyer':
                $this->startDealCreation($chatId, $messageId, $userId, 'buyer');
                break;
                
            case 'create_deal_seller':
                $this->startDealCreation($chatId, $messageId, $userId, 'seller');
                break;
                
            case 'cancel':
                $this->deleteMessage($chatId, $messageId);
                break;
                
            default:
                if (strpos($data, 'deal_') === 0) {
                    $this->processDealCallback($chatId, $messageId, $userId, $data);
                }
        }
    }
    
    private function startDealCreation($chatId, $messageId, $userId, $role) {
        // Здесь будет логика создания сделки
        $text = "🔄 Функция создания сделки в разработке...";
        $this->editMessage($chatId, $messageId, $text);
    }
    
    private function getStatusEmoji($status) {
        $statuses = [
            'created' => '🆕 Создана',
            'paid' => '💰 Оплачена',
            'confirmed' => '✅ Подтверждена',
            'disputed' => '⚠️ Спор',
            'completed' => '✅ Завершена',
            'cancelled' => '❌ Отменена'
        ];
        
        return $statuses[$status] ?? $status;
    }
    
    private function processText($chatId, $userId, $text) {
        // Обработка текстовых сообщений (кнопки клавиатуры)
        switch ($text) {
            case '💼 Создать сделку':
                $this->handleCreateDeal($chatId, $userId);
                break;
                
            case '📋 Мои сделки':
                $this->handleMyDeals($chatId, $userId);
                break;
                
            case '💰 Баланс':
                $this->handleBalance($chatId, $userId);
                break;
                
            case '👤 Профиль':
                $this->handleProfile($chatId, $userId);
                break;
                
            case '❓ Помощь':
                $this->handleHelp($chatId);
                break;
                
            case '📞 Поддержка':
                $this->handleSupport($chatId);
                break;
                
            default:
                $this->sendMessage($chatId, "❓ Используйте кнопки меню или команды для навигации.");
        }
    }
}