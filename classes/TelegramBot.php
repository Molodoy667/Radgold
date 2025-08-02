<?php

require_once 'Database.php';
require_once 'User.php';
require_once 'Deal.php';
require_once 'PaymentSystem.php';

class TelegramBot {
    private $token;
    private $db;
    private $config;
    private $paymentSystem;
    
    public function __construct($config) {
        $this->config = $config;
        $this->token = $config['telegram']['bot_token'];
        $this->db = new Database($config['database']);
        $this->paymentSystem = new PaymentSystem($this->db, $config);
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
        // Получаем текст помощи из базы данных
        $helpText = $this->db->fetch(
            'SELECT setting_value FROM bot_settings WHERE setting_key = ?',
            ['help_text']
        );
        
        if ($helpText && !empty($helpText['setting_value'])) {
            $text = $helpText['setting_value'];
        } else {
            $text = $this->config['messages']['help'] ?? "📋 Помощь временно недоступна";
        }
        
        $this->sendMessage($chatId, $text);
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
        // Получаем текст поддержки из базы данных
        $supportText = $this->db->fetch(
            'SELECT setting_value FROM bot_settings WHERE setting_key = ?',
            ['support_text']
        );
        
        if ($supportText && !empty($supportText['setting_value'])) {
            $text = $supportText['setting_value'];
        } else {
            $text = "📞 <b>Техническая поддержка</b>\n\n";
            $text .= "Если у вас возникли вопросы или проблемы, обратитесь к администратору:\n\n";
            $text .= "📧 Email: support@gamegarant.com\n";
            $text .= "💬 Telegram: @gamegarant_support\n\n";
            $text .= "⏰ Время работы: 24/7";
        }
        
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
                
            case 'add_balance':
                $this->showPaymentMethods($chatId, $messageId, $userId);
                break;
                
            case 'verification':
                $this->showVerificationInfo($chatId, $messageId, $userId);
                break;
                
            case 'edit_profile':
                $this->showProfileEditor($chatId, $messageId, $userId);
                break;
                
            case 'cancel':
                $this->deleteMessage($chatId, $messageId);
                break;
                
            case 'cancel_deal_creation':
                // Очищаем временные данные
                $this->db->query('UPDATE users SET temp_data = NULL WHERE telegram_id = ?', [$userId]);
                $this->editMessage($chatId, $messageId, "❌ Создание сделки отменено", null);
                break;
                
            case 'main_menu':
                $this->deleteMessage($chatId, $messageId);
                $this->handleStart($chatId, $userId);
                break;
                
            case 'my_deals':
                $this->showMyDeals($chatId, $messageId, $userId);
                break;
                
            case 'show_balance':
                $this->showBalance($chatId, $messageId, $userId);
                break;
                
            case 'withdraw':
                $this->showWithdrawForm($chatId, $messageId, $userId);
                break;
                
            case 'create_deal_menu':
                $this->handleCreateDealCallback($chatId, $messageId, $userId);
                break;
                
            default:
                if (strpos($data, 'payment_') === 0) {
                    $this->processPaymentCallback($chatId, $messageId, $userId, $data);
                } elseif (strpos($data, 'amount_') === 0) {
                    $this->processAmountCallback($chatId, $messageId, $userId, $data);
                } elseif (strpos($data, 'deal_amount_') === 0) {
                    $this->processDealAmountCallback($chatId, $messageId, $userId, $data);
                } elseif (strpos($data, 'withdraw_') === 0) {
                    $this->processWithdrawCallback($chatId, $messageId, $userId, $data);
                } elseif (strpos($data, 'deal_') === 0) {
                    $this->processDealCallback($chatId, $messageId, $userId, $data);
                }
        }
    }
    
    private function startDealCreation($chatId, $messageId, $userId, $role) {
        $text = "💼 <b>Создание сделки</b>\n\n";
        $text .= "Ваша роль: " . ($role === 'buyer' ? '🛒 Покупатель' : '💰 Продавец') . "\n\n";
        $text .= "📝 <b>Введите название сделки:</b>\n";
        $text .= "Например: \"Продажа iPhone 13\", \"Разработка сайта\", \"Консультация по бизнесу\"\n\n";
        $text .= "✏️ Отправьте сообщение с названием сделки";
        
        // Сохраняем этап создания сделки
        $this->db->query(
            'UPDATE users SET temp_data = ? WHERE telegram_id = ?',
            [json_encode(['step' => 'create_deal_title', 'role' => $role]), $userId]
        );
        
        $keyboard = [
            'inline_keyboard' => [
                [['text' => '❌ Отменить', 'callback_data' => 'cancel_deal_creation']]
            ]
        ];
        
        $this->editMessage($chatId, $messageId, $text, $keyboard);
    }
    
    private function showPaymentMethods($chatId, $messageId, $userId) {
        $methods = $this->paymentSystem->getPaymentMethods();
        
        $text = "💳 <b>Выберите способ пополнения:</b>\n\n";
        
        $keyboard = ['inline_keyboard' => []];
        
        foreach ($methods as $key => $method) {
            if ($method['enabled']) {
                $text .= "{$method['icon']} <b>{$method['name']}</b>\n";
                $text .= "   {$method['description']}\n\n";
                
                $keyboard['inline_keyboard'][] = [
                    ['text' => "{$method['icon']} {$method['name']}", 'callback_data' => "payment_{$key}"]
                ];
            }
        }
        
        $keyboard['inline_keyboard'][] = [['text' => '🔙 Назад', 'callback_data' => 'cancel']];
        
        $this->editMessage($chatId, $messageId, $text, $keyboard);
    }
    
    private function processPaymentCallback($chatId, $messageId, $userId, $data) {
        $method = str_replace('payment_', '', $data);
        
        // Показываем форму для ввода суммы
        $text = "💳 <b>Пополнение баланса</b>\n\n";
        $text .= "Выбранный способ: " . $this->getPaymentMethodName($method) . "\n\n";
        $text .= "💰 <b>Введите сумму пополнения:</b>\n";
        $text .= "• Минимум: 100 ₽\n";
        $text .= "• Максимум: 50,000 ₽\n\n";
        $text .= "📝 Отправьте сообщение с суммой (только число)";
        
        // Сохраняем выбранный метод в сессии пользователя
        $this->db->query(
            'UPDATE users SET temp_data = ? WHERE telegram_id = ?',
            [json_encode(['payment_method' => $method, 'step' => 'enter_amount']), $userId]
        );
        
        $keyboard = [
            'inline_keyboard' => [
                [['text' => '💰 500 ₽', 'callback_data' => "amount_500_{$method}"]],
                [['text' => '💰 1000 ₽', 'callback_data' => "amount_1000_{$method}"]],
                [['text' => '💰 2000 ₽', 'callback_data' => "amount_2000_{$method}"]],
                [['text' => '🔙 Назад', 'callback_data' => 'add_balance']]
            ]
        ];
        
        $this->editMessage($chatId, $messageId, $text, $keyboard);
    }
    
    private function processAmountCallback($chatId, $messageId, $userId, $data) {
        // Парсим данные: amount_1000_sberbank
        $parts = explode('_', $data);
        $amount = intval($parts[1]);
        $method = $parts[2];
        
        $this->createPaymentWithAmount($chatId, $messageId, $userId, $amount, $method);
    }
    
    private function createPaymentWithAmount($chatId, $messageId, $userId, $amount, $method) {
        // Создаем платеж на пополнение баланса (deal_id = null)
        try {
            $paymentInfo = $this->paymentSystem->createPayment(null, $userId, $amount, $method);
            
            $text = $paymentInfo['instructions']['title'] . "\n\n";
            
            foreach ($paymentInfo['instructions']['steps'] as $step) {
                $text .= $step . "\n";
            }
            
            $text .= "\n📋 <b>Реквизиты:</b>\n";
            if (isset($paymentInfo['instructions']['card_number'])) {
                $text .= "💳 Номер карты: <code>{$paymentInfo['instructions']['card_number']}</code>\n";
            }
            if (isset($paymentInfo['instructions']['phone'])) {
                $text .= "📱 Телефон: <code>{$paymentInfo['instructions']['phone']}</code>\n";
            }
            if (isset($paymentInfo['instructions']['holder_name'])) {
                $text .= "👤 Получатель: {$paymentInfo['instructions']['holder_name']}\n";
            }
            
            $text .= "\n⚠️ <b>Важно:</b>\n";
            $text .= "• Обязательно укажите комментарий #{$paymentInfo['payment_id']}\n";
            $text .= "• После перевода администратор подтвердит платеж\n";
            $text .= "• Средства поступят на баланс в течение 24 часов";
            
            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '✅ Я оплатил', 'callback_data' => "paid_{$paymentInfo['payment_id']}"]],
                    [['text' => '🔙 Назад', 'callback_data' => 'add_balance']]
                ]
            ];
            
            $this->editMessage($chatId, $messageId, $text, $keyboard);
            
        } catch (Exception $e) {
            $this->editMessage($chatId, $messageId, "❌ Ошибка создания платежа: " . $e->getMessage());
        }
    }
    
    private function showVerificationInfo($chatId, $messageId, $userId) {
        $user = new User($this->db);
        $userData = $user->getUser($userId);
        
        if ($userData['is_verified']) {
            $text = "✅ <b>Ваш аккаунт верифицирован!</b>\n\n";
            $text .= "🎉 Поздравляем! У вас есть галочка верификации.\n";
            $text .= "Это повышает доверие других пользователей к вам.";
        } else {
            $text = "🔐 <b>Верификация аккаунта</b>\n\n";
            $text .= "📋 <b>Для верификации нужно:</b>\n";
            $text .= "• Заполнить профиль (имя, телефон)\n";
            $text .= "• Провести минимум 3 успешные сделки\n";
            $text .= "• Иметь рейтинг не менее 4.0\n\n";
            $text .= "💡 После выполнения условий обратитесь в поддержку";
        }
        
        $keyboard = [
            'inline_keyboard' => [
                [['text' => '🔙 Назад', 'callback_data' => 'cancel']]
            ]
        ];
        
        $this->editMessage($chatId, $messageId, $text, $keyboard);
    }
    
    private function showProfileEditor($chatId, $messageId, $userId) {
        $text = "✏️ <b>Редактирование профиля</b>\n\n";
        $text .= "📝 Что можно изменить:\n";
        $text .= "• Телефон\n";
        $text .= "• Email\n";
        $text .= "• Имя и фамилия\n\n";
        $text .= "💡 <i>Функция редактирования будет доступна в ближайшем обновлении</i>";
        
        $keyboard = [
            'inline_keyboard' => [
                [['text' => '🔙 Назад', 'callback_data' => 'cancel']]
            ]
        ];
        
        $this->editMessage($chatId, $messageId, $text, $keyboard);
    }
    
    private function getStatusEmoji($status) {
        $statuses = [
            'created' => '🆕 Создана',
            'paid' => '💰 Оплачена',
            'confirmed' => '✅ Подтверждена',
            'completed' => '✅ Завершена',
            'disputed' => '⚠️ Спор',
            'cancelled' => '❌ Отменена'
        ];
        
        return $statuses[$status] ?? $status;
    }
    
    private function processText($chatId, $userId, $text) {
        // Проверяем, не ожидает ли пользователь ввода суммы
        $user = new User($this->db);
        $userData = $user->getUser($userId);
        
        if ($userData['temp_data']) {
            $tempData = json_decode($userData['temp_data'], true);
            if ($tempData['step'] === 'enter_amount') {
                $this->handleAmountInput($chatId, $userId, $text, $tempData['payment_method']);
                return;
            } elseif ($tempData['step'] === 'withdraw_amount') {
                $this->handleWithdrawAmountInput($chatId, $userId, $text);
                return;
            } elseif (strpos($tempData['step'], 'create_deal_') === 0) {
                $this->handleDealCreationStep($chatId, $userId, $text, $tempData);
                return;
            }
        }
        
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
    
    private function handleAmountInput($chatId, $userId, $text, $method) {
        // Проверяем, что введена корректная сумма
        $amount = intval($text);
        
        if ($amount < 100) {
            $this->sendMessage($chatId, "❌ Минимальная сумма пополнения: 100 ₽");
            return;
        }
        
        if ($amount > 50000) {
            $this->sendMessage($chatId, "❌ Максимальная сумма пополнения: 50,000 ₽");
            return;
        }
        
        // Очищаем временные данные
        $this->db->query(
            'UPDATE users SET temp_data = NULL WHERE telegram_id = ?',
            [$userId]
        );
        
        // Создаем платеж
        try {
            $paymentInfo = $this->paymentSystem->createPayment(null, $userId, $amount, $method);
            
            $text = $paymentInfo['instructions']['title'] . "\n\n";
            
            foreach ($paymentInfo['instructions']['steps'] as $step) {
                $text .= $step . "\n";
            }
            
            $text .= "\n📋 <b>Реквизиты:</b>\n";
            if (isset($paymentInfo['instructions']['card_number'])) {
                $text .= "💳 Номер карты: <code>{$paymentInfo['instructions']['card_number']}</code>\n";
            }
            if (isset($paymentInfo['instructions']['phone'])) {
                $text .= "📱 Телефон: <code>{$paymentInfo['instructions']['phone']}</code>\n";
            }
            if (isset($paymentInfo['instructions']['holder_name'])) {
                $text .= "👤 Получатель: {$paymentInfo['instructions']['holder_name']}\n";
            }
            
            $text .= "\n⚠️ <b>Важно:</b>\n";
            $text .= "• Обязательно укажите комментарий #{$paymentInfo['payment_id']}\n";
            $text .= "• После перевода администратор подтвердит платеж\n";
            $text .= "• Средства поступят на баланс в течение 24 часов";
            
            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '✅ Я оплатил', 'callback_data' => "paid_{$paymentInfo['payment_id']}"]],
                    [['text' => '💰 Баланс', 'callback_data' => 'show_balance']]
                ]
            ];
            
            $this->sendMessage($chatId, $text, $keyboard);
            
        } catch (Exception $e) {
            $this->sendMessage($chatId, "❌ Ошибка создания платежа: " . $e->getMessage());
        }
    }
    
    private function getPaymentMethodName($method) {
        $methods = [
            'sberbank' => '🟢 Сбербанк',
            'mir_card' => '💳 Карта МИР',
            'manual_card' => '💰 Банковская карта'
        ];
        
        return $methods[$method] ?? $method;
    }
    
    private function processDealAmountCallback($chatId, $messageId, $userId, $data) {
        // Парсим данные: deal_amount_1000
        $amount = intval(str_replace('deal_amount_', '', $data));
        
        // Получаем временные данные пользователя
        $user = new User($this->db);
        $userData = $user->getUser($userId);
        
        if (!$userData['temp_data']) {
            $this->editMessage($chatId, $messageId, "❌ Сессия создания сделки истекла. Начните заново.");
            return;
        }
        
        $tempData = json_decode($userData['temp_data'], true);
        if ($tempData['step'] !== 'create_deal_amount') {
            $this->editMessage($chatId, $messageId, "❌ Неверный этап создания сделки");
            return;
        }
        
        $this->handleDealAmount($chatId, $userId, strval($amount), $tempData);
    }
    
    private function handleDealCreationStep($chatId, $userId, $text, $tempData) {
        $step = $tempData['step'];
        $role = $tempData['role'];
        
        switch ($step) {
            case 'create_deal_title':
                $this->handleDealTitle($chatId, $userId, $text, $role);
                break;
                
            case 'create_deal_description':
                $this->handleDealDescription($chatId, $userId, $text, $tempData);
                break;
                
            case 'create_deal_amount':
                $this->handleDealAmount($chatId, $userId, $text, $tempData);
                break;
        }
    }
    
    private function handleDealTitle($chatId, $userId, $text, $role) {
        if (strlen($text) < 5) {
            $this->sendMessage($chatId, "❌ Название сделки должно содержать минимум 5 символов");
            return;
        }
        
        if (strlen($text) > 100) {
            $this->sendMessage($chatId, "❌ Название сделки не должно превышать 100 символов");
            return;
        }
        
        // Сохраняем название и переходим к описанию
        $this->db->query(
            'UPDATE users SET temp_data = ? WHERE telegram_id = ?',
            [json_encode([
                'step' => 'create_deal_description',
                'role' => $role,
                'title' => $text
            ]), $userId]
        );
        
        $message = "📝 <b>Описание сделки</b>\n\n";
        $message .= "Название: <b>" . htmlspecialchars($text) . "</b>\n\n";
        $message .= "📋 <b>Опишите условия сделки:</b>\n";
        $message .= "• Что именно продается/покупается\n";
        $message .= "• Условия выполнения\n";
        $message .= "• Сроки\n";
        $message .= "• Другие важные детали\n\n";
        $message .= "✏️ Отправьте сообщение с описанием";
        
        $keyboard = [
            'inline_keyboard' => [
                [['text' => '❌ Отменить', 'callback_data' => 'cancel_deal_creation']]
            ]
        ];
        
        $this->sendMessage($chatId, $message, $keyboard);
    }
    
    private function handleDealDescription($chatId, $userId, $text, $tempData) {
        if (strlen($text) < 10) {
            $this->sendMessage($chatId, "❌ Описание должно содержать минимум 10 символов");
            return;
        }
        
        if (strlen($text) > 1000) {
            $this->sendMessage($chatId, "❌ Описание не должно превышать 1000 символов");
            return;
        }
        
        // Сохраняем описание и переходим к сумме
        $tempData['step'] = 'create_deal_amount';
        $tempData['description'] = $text;
        
        $this->db->query(
            'UPDATE users SET temp_data = ? WHERE telegram_id = ?',
            [json_encode($tempData), $userId]
        );
        
        $minAmount = $this->config['escrow']['min_deal_amount'];
        $maxAmount = $this->config['escrow']['max_deal_amount'];
        $commission = $this->config['escrow']['commission_percent'];
        
        $text = "💰 <b>Сумма сделки</b>\n\n";
        $text .= "Название: <b>" . htmlspecialchars($tempData['title']) . "</b>\n";
        $text .= "Описание: " . htmlspecialchars(mb_substr($tempData['description'], 0, 50)) . "...\n\n";
        $text .= "💵 <b>Укажите сумму сделки в рублях:</b>\n";
        $text .= "• Минимум: " . number_format($minAmount) . " ₽\n";
        $text .= "• Максимум: " . number_format($maxAmount) . " ₽\n";
        $text .= "• Комиссия сервиса: {$commission}%\n\n";
        $text .= "✏️ Отправьте сообщение с суммой (только число)";
        
        $keyboard = [
            'inline_keyboard' => [
                [['text' => '💰 1000 ₽', 'callback_data' => 'deal_amount_1000']],
                [['text' => '💰 5000 ₽', 'callback_data' => 'deal_amount_5000']],
                [['text' => '💰 10000 ₽', 'callback_data' => 'deal_amount_10000']],
                [['text' => '❌ Отменить', 'callback_data' => 'cancel_deal_creation']]
            ]
        ];
        
        $this->sendMessage($chatId, $text, $keyboard);
    }
    
    private function handleDealAmount($chatId, $userId, $text, $tempData) {
        $amount = floatval(str_replace([' ', ','], ['', '.'], $text));
        
        if ($amount < $this->config['escrow']['min_deal_amount']) {
            $this->sendMessage($chatId, "❌ Минимальная сумма сделки: " . number_format($this->config['escrow']['min_deal_amount']) . " ₽");
            return;
        }
        
        if ($amount > $this->config['escrow']['max_deal_amount']) {
            $this->sendMessage($chatId, "❌ Максимальная сумма сделки: " . number_format($this->config['escrow']['max_deal_amount']) . " ₽");
            return;
        }
        
        // Создаем сделку
        try {
            $deal = new Deal($this->db);
            $dealId = $deal->createSimpleDeal(
                $userId,
                $tempData['title'],
                $tempData['description'],
                $amount,
                $tempData['role']
            );
            
            // Очищаем временные данные
            $this->db->query(
                'UPDATE users SET temp_data = NULL WHERE telegram_id = ?',
                [$userId]
            );
            
            $commission = ($amount * $this->config['escrow']['commission_percent']) / 100;
            $dealData = $deal->getDeal($dealId);
            
            $text = "✅ <b>Сделка успешно создана!</b>\n\n";
            $text .= "🔢 Номер: <b>#{$dealData['deal_number']}</b>\n";
            $text .= "📝 Название: {$tempData['title']}\n";
            $text .= "💰 Сумма: " . number_format($amount, 2) . " ₽\n";
            $text .= "💸 Комиссия: " . number_format($commission, 2) . " ₽\n";
            $text .= "👤 Ваша роль: " . ($tempData['role'] === 'seller' ? 'Продавец' : 'Покупатель') . "\n\n";
            $text .= "📋 <b>Что дальше:</b>\n";
            $text .= "• Найдите второго участника сделки\n";
            $text .= "• Сообщите ему номер сделки: <code>#{$dealData['deal_number']}</code>\n";
            $text .= "• После присоединения второго участника сделка активируется\n\n";
            $text .= "⏰ Сделка действительна 72 часа";
            
            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '📋 Мои сделки', 'callback_data' => 'my_deals']],
                    [['text' => '🏠 Главное меню', 'callback_data' => 'main_menu']]
                ]
            ];
            
            $this->sendMessage($chatId, $text, $keyboard);
            
        } catch (Exception $e) {
            $this->sendMessage($chatId, "❌ Ошибка создания сделки: " . $e->getMessage());
        }
    }
    
    private function showMyDeals($chatId, $messageId, $userId) {
        $deal = new Deal($this->db);
        $deals = $deal->getUserDeals($userId);
        
        if (empty($deals)) {
            $text = "📋 <b>Ваши сделки</b>\n\n";
            $text .= "У вас пока нет сделок.\n";
            $text .= "Создайте первую сделку, чтобы начать работу с гарантом!";
            
            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '💼 Создать сделку', 'callback_data' => 'create_deal_menu']],
                    [['text' => '🏠 Главное меню', 'callback_data' => 'main_menu']]
                ]
            ];
        } else {
            $text = "📋 <b>Ваши сделки</b>\n\n";
            
            foreach (array_slice($deals, 0, 5) as $dealData) {
                $status = $this->getStatusEmoji($dealData['status']);
                $role = '';
                if ($dealData['seller_id'] == $userId) $role = '💰 Продавец';
                if ($dealData['buyer_id'] == $userId) $role = '🛒 Покупатель';
                
                $text .= "🔹 <b>#{$dealData['deal_number']}</b> {$role}\n";
                $text .= "📝 {$dealData['title']}\n";
                $text .= "💰 " . number_format($dealData['amount'], 2) . " ₽\n";
                $text .= "📊 {$status}\n";
                $text .= "📅 " . date('d.m.Y H:i', strtotime($dealData['created_at'])) . "\n\n";
            }
            
            if (count($deals) > 5) {
                $text .= "... и еще " . (count($deals) - 5) . " сделок\n\n";
            }
            
            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '💼 Создать сделку', 'callback_data' => 'create_deal_menu']],
                    [['text' => '🔄 Обновить', 'callback_data' => 'my_deals']],
                    [['text' => '🏠 Главное меню', 'callback_data' => 'main_menu']]
                ]
            ];
        }
        
        $this->editMessage($chatId, $messageId, $text, $keyboard);
    }
    
    private function showBalance($chatId, $messageId, $userId) {
        $user = new User($this->db);
        $userData = $user->getUser($userId);
        
        $text = "💰 <b>Ваш баланс:</b> " . number_format($userData['balance'], 2) . " ₽\n\n";
        $text .= "📊 <b>Статистика:</b>\n";
        $text .= "⭐ Рейтинг: " . number_format($userData['rating'], 1) . "/5.0\n";
        $text .= "📈 Сделок завершено: {$userData['deals_count']}\n";
        $text .= "✅ Верификация: " . ($userData['is_verified'] ? 'Пройдена' : 'Не пройдена');
        
        $keyboard = [
            'inline_keyboard' => [
                [['text' => '💳 Пополнить баланс', 'callback_data' => 'add_balance']],
                [['text' => '💸 Вывести средства', 'callback_data' => 'withdraw']],
                [['text' => '🏠 Главное меню', 'callback_data' => 'main_menu']]
            ]
        ];
        
        $this->editMessage($chatId, $messageId, $text, $keyboard);
    }
    
    private function showWithdrawForm($chatId, $messageId, $userId) {
        $user = new User($this->db);
        $userData = $user->getUser($userId);
        
        if ($userData['balance'] < 100) {
            $text = "💸 <b>Вывод средств</b>\n\n";
            $text .= "❌ Недостаточно средств для вывода\n";
            $text .= "Минимальная сумма вывода: 100 ₽\n";
            $text .= "Ваш баланс: " . number_format($userData['balance'], 2) . " ₽";
            
            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '💳 Пополнить баланс', 'callback_data' => 'add_balance']],
                    [['text' => '🔙 Назад', 'callback_data' => 'show_balance']]
                ]
            ];
        } else {
            $text = "💸 <b>Вывод средств</b>\n\n";
            $text .= "💰 Доступно для вывода: " . number_format($userData['balance'], 2) . " ₽\n\n";
            $text .= "💵 <b>Выберите сумму для вывода:</b>\n";
            $text .= "• Минимум: 100 ₽\n";
            $text .= "• Максимум: " . number_format($userData['balance'], 2) . " ₽\n\n";
            $text .= "✏️ Отправьте сообщение с суммой для вывода";
            
            // Сохраняем состояние вывода средств
            $this->db->query(
                'UPDATE users SET temp_data = ? WHERE telegram_id = ?',
                [json_encode(['step' => 'withdraw_amount']), $userId]
            );
            
            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '💰 100 ₽', 'callback_data' => 'withdraw_100']],
                    [['text' => '💰 500 ₽', 'callback_data' => 'withdraw_500']],
                    [['text' => '💰 1000 ₽', 'callback_data' => 'withdraw_1000']],
                    [['text' => '💸 Весь баланс', 'callback_data' => 'withdraw_all']],
                    [['text' => '🔙 Назад', 'callback_data' => 'show_balance']]
                ]
            ];
        }
        
        $this->editMessage($chatId, $messageId, $text, $keyboard);
    }
    
    private function processWithdrawCallback($chatId, $messageId, $userId, $data) {
        $user = new User($this->db);
        $userData = $user->getUser($userId);
        
        $amount = 0;
        if ($data === 'withdraw_100') $amount = 100;
        elseif ($data === 'withdraw_500') $amount = 500;
        elseif ($data === 'withdraw_1000') $amount = 1000;
        elseif ($data === 'withdraw_all') $amount = $userData['balance'];
        
        if ($amount > 0) {
            $this->processWithdrawRequest($chatId, $messageId, $userId, $amount);
        }
    }
    
    private function processWithdrawRequest($chatId, $messageId, $userId, $amount) {
        $user = new User($this->db);
        $userData = $user->getUser($userId);
        
        if ($amount > $userData['balance']) {
            $this->editMessage($chatId, $messageId, "❌ Недостаточно средств на балансе");
            return;
        }
        
        if ($amount < 100) {
            $this->editMessage($chatId, $messageId, "❌ Минимальная сумма вывода: 100 ₽");
            return;
        }
        
        // Создаем заявку на вывод
        $requestId = $this->db->insert('withdrawal_requests', [
            'user_id' => $userId,
            'amount' => $amount,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        // Очищаем временные данные
        $this->db->query('UPDATE users SET temp_data = NULL WHERE telegram_id = ?', [$userId]);
        
        $text = "✅ <b>Заявка на вывод создана!</b>\n\n";
        $text .= "📋 Номер заявки: <b>#{$requestId}</b>\n";
        $text .= "💰 Сумма: " . number_format($amount, 2) . " ₽\n";
        $text .= "📅 Дата: " . date('d.m.Y H:i') . "\n\n";
        $text .= "⏳ <b>Статус:</b> Ожидает обработки\n\n";
        $text .= "📞 Администратор свяжется с вами для уточнения реквизитов вывода.\n";
        $text .= "⏰ Обработка заявок: в течение 24 часов";
        
        $keyboard = [
            'inline_keyboard' => [
                [['text' => '💰 Баланс', 'callback_data' => 'show_balance']],
                [['text' => '🏠 Главное меню', 'callback_data' => 'main_menu']]
            ]
        ];
        
        if ($messageId) {
            $this->editMessage($chatId, $messageId, $text, $keyboard);
        } else {
            $this->sendMessage($chatId, $text, $keyboard);
        }
    }
    
    private function handleWithdrawAmountInput($chatId, $userId, $text) {
        $amount = floatval(str_replace([' ', ','], ['', '.'], $text));
        
        if ($amount < 100) {
            $this->sendMessage($chatId, "❌ Минимальная сумма вывода: 100 ₽");
            return;
        }
        
        $user = new User($this->db);
        $userData = $user->getUser($userId);
        
        if ($amount > $userData['balance']) {
            $this->sendMessage($chatId, "❌ Недостаточно средств на балансе: " . number_format($userData['balance'], 2) . " ₽");
            return;
        }
        
        $this->processWithdrawRequest($chatId, null, $userId, $amount);
    }
    
    private function handleCreateDealCallback($chatId, $messageId, $userId) {
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
        
        $this->editMessage($chatId, $messageId, $text, $keyboard);
    }
}