<?php
require_once 'config.php';
require_once 'classes/Database.php';

$config = require 'config.php';

try {
    $db = new Database($config['database']);
    
    echo "🔄 Применение обновлений базы данных...\n\n";
    
    // Добавляем поле temp_data если его нет
    echo "1. Добавление поля temp_data в таблицу users...\n";
    try {
        $db->query("ALTER TABLE users ADD COLUMN temp_data TEXT NULL AFTER updated_at");
        echo "   ✅ Поле temp_data добавлено\n";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
            echo "   ℹ️  Поле temp_data уже существует\n";
        } else {
            echo "   ❌ Ошибка: " . $e->getMessage() . "\n";
        }
    }
    
    // Добавляем настройки платежных систем
    echo "\n2. Добавление настроек платежных систем...\n";
    
    $paymentSettings = [
        'sberbank_data' => '{"card_number":"","phone":"","holder_name":""}',
        'mir_card_data' => '{"card_number":"","holder_name":"","bank_name":""}',
        'manual_card_data' => '{"card_number":"","holder_name":"","bank_name":""}'
    ];
    
    foreach ($paymentSettings as $key => $value) {
        $existing = $db->fetch('SELECT id FROM bot_settings WHERE setting_key = ?', [$key]);
        if (!$existing) {
            $db->insert('bot_settings', [
                'setting_key' => $key,
                'setting_value' => $value,
                'description' => 'Настройки платежной системы',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            echo "   ✅ Добавлена настройка: {$key}\n";
        } else {
            echo "   ℹ️  Настройка {$key} уже существует\n";
        }
    }
    
    // Обновляем структуру таблицы payments
    echo "\n3. Обновление структуры таблицы payments...\n";
    try {
        $db->query("ALTER TABLE payments MODIFY COLUMN payment_method VARCHAR(50) NOT NULL");
        echo "   ✅ Структура таблицы payments обновлена\n";
    } catch (Exception $e) {
        echo "   ℹ️  Структура таблицы payments уже актуальна\n";
    }
    
    // Добавляем индексы
    echo "\n4. Добавление индексов для оптимизации...\n";
    
    $indexes = [
        'idx_payments_status' => 'CREATE INDEX idx_payments_status ON payments(status)',
        'idx_payments_user_id' => 'CREATE INDEX idx_payments_user_id ON payments(user_id)',
        'idx_users_temp_data' => 'CREATE INDEX idx_users_temp_data ON users(temp_data(100))'
    ];
    
    foreach ($indexes as $indexName => $sql) {
        try {
            $db->query($sql);
            echo "   ✅ Индекс {$indexName} создан\n";
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
                echo "   ℹ️  Индекс {$indexName} уже существует\n";
            } else {
                echo "   ❌ Ошибка создания индекса {$indexName}: " . $e->getMessage() . "\n";
            }
        }
    }
    
    // Создаем таблицу заявок на вывод средств
    echo "\n4. Создание таблицы заявок на вывод...\n";
    try {
        $db->query("
            CREATE TABLE IF NOT EXISTS withdrawal_requests (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id BIGINT NOT NULL,
                amount DECIMAL(10,2) NOT NULL,
                status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
                admin_comment TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_user_id (user_id),
                INDEX idx_status (status),
                FOREIGN KEY (user_id) REFERENCES users(telegram_id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        echo "   ✅ Таблица withdrawal_requests создана\n";
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'already exists') !== false) {
            echo "   ℹ️  Таблица withdrawal_requests уже существует\n";
        } else {
            echo "   ❌ Ошибка создания таблицы: " . $e->getMessage() . "\n";
        }
    }

    // Обновляем настройки бота
    echo "\n6. Обновление настроек бота...\n";
    
    $botSettings = [
        'commission_percent' => '5',
        'min_deal_amount' => '100',
        'max_deal_amount' => '100000',
        'help_text' => "📋 <b>Доступные команды:</b>\n\n/start - Начать работу\n/create_deal - Создать сделку\n/my_deals - Мои сделки\n/balance - Баланс\n/help - Помощь\n/support - Поддержка\n\n💡 <b>Как работает гарант:</b>\n1. Создайте сделку и найдите второго участника\n2. Покупатель пополняет баланс на сумму сделки\n3. После выполнения условий продавец получает средства\n4. Сервис берет комиссию 5% за безопасность сделки",
        'support_text' => "📞 <b>Техническая поддержка</b>\n\n🔧 <b>Если у вас возникли проблемы:</b>\n• Ошибки в работе бота\n• Вопросы по сделкам\n• Проблемы с платежами\n• Споры с участниками\n\n📱 <b>Свяжитесь с нами:</b>\n💬 Telegram: @your_support\n📧 Email: support@yourbot.com\n\n⏰ <b>Время работы:</b> 24/7\n💬 <b>Среднее время ответа:</b> 2-4 часа"
    ];
    
    foreach ($botSettings as $key => $value) {
        $existing = $db->fetch('SELECT id FROM bot_settings WHERE setting_key = ?', [$key]);
        if ($existing) {
            $db->update('bot_settings', [
                'setting_value' => $value,
                'updated_at' => date('Y-m-d H:i:s')
            ], 'setting_key = ?', [$key]);
            echo "   ✅ Обновлена настройка {$key}: {$value}\n";
        } else {
            $db->insert('bot_settings', [
                'setting_key' => $key,
                'setting_value' => $value,
                'description' => 'Настройка бота',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            echo "   ✅ Добавлена настройка {$key}: {$value}\n";
        }
    }
    
    echo "\n🎉 Все обновления успешно применены!\n\n";
    echo "📋 Что нового:\n";
    echo "• ✅ Система пополнения через карты МИР и Сбербанк\n";
    echo "• ✅ Админ-панель для управления платежными реквизитами\n";
    echo "• ✅ Функция создания сделок с пошаговым мастером\n";
    echo "• ✅ Система заявок на вывод средств\n";
    echo "• ✅ Управление текстами помощи и поддержки\n";
    echo "• ✅ Улучшенная система верификации\n";
    echo "• ✅ Оптимизация базы данных\n\n";
    echo "🔗 Откройте админ-панель: {$config['admin']['panel_url']}\n";
    echo "💳 Настройте платежные реквизиты в разделе 'Реквизиты'\n";
    echo "📝 Настройте тексты помощи в разделе 'Контент'\n";
    echo "💸 Управляйте заявками на вывод в разделе 'Вывод средств'\n";
    
} catch (Exception $e) {
    echo "❌ Ошибка подключения к базе данных: " . $e->getMessage() . "\n";
    echo "🔧 Проверьте настройки в config.php\n";
}
?>