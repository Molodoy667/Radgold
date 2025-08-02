<?php
// Конфигурация Telegram бота для услуг гаранта

return [
    // Настройки Telegram Bot API
    'telegram' => [
        'bot_token' => 'YOUR_BOT_TOKEN_HERE',
        'webhook_url' => 'https://yourdomain.com/webhook.php',
    ],
    
    // Настройки базы данных
    'database' => [
        'host' => 'localhost',
        'dbname' => 'escrow_bot',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4'
    ],
    
    // Настройки админ-панели
    'admin' => [
        'admin_ids' => [123456789], // ID администраторов
        'panel_url' => 'https://yourdomain.com/admin/',
        'panel_password' => 'admin123' // Смените пароль!
    ],
    
    // Настройки гарантийного сервиса
    'escrow' => [
        'commission_percent' => 5, // Комиссия в процентах
        'min_deal_amount' => 100, // Минимальная сумма сделки
        'max_deal_amount' => 100000, // Максимальная сумма сделки
        'deal_timeout_hours' => 72, // Время на выполнение сделки в часах
    ],
    
    // Настройки платежной системы
    'payments' => [
        'provider' => 'yoomoney', // yoomoney, qiwi, paypal
        'yoomoney_token' => 'YOUR_YOOMONEY_TOKEN',
        'qiwi_token' => 'YOUR_QIWI_TOKEN',
    ],
    
    // Текстовые сообщения
    'messages' => [
        'welcome' => "🤝 Добро пожаловать в сервис гарантийных сделок!\n\nЯ помогу вам безопасно провести сделку с гарантией возврата средств.",
        'help' => "📋 Доступные команды:\n\n/start - Начать работу\n/create_deal - Создать сделку\n/my_deals - Мои сделки\n/balance - Баланс\n/help - Помощь\n/support - Поддержка",
        'commission_info' => "💰 Комиссия сервиса составляет {percent}% от суммы сделки"
    ]
];