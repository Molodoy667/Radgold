<?php
require_once 'config.php';
require_once 'classes/Database.php';

$config = require 'config.php';

try {
    $db = new Database($config['database']);
    
    echo "🔧 Диагностика и исправление базы данных...\n\n";
    
    // 1. Проверяем и добавляем поле temp_data
    echo "1. Проверка поля temp_data в таблице users...\n";
    try {
        $result = $db->query("SELECT temp_data FROM users LIMIT 1");
        echo "   ✅ Поле temp_data существует\n";
    } catch (Exception $e) {
        echo "   ⚠️  Поле temp_data не найдено, добавляю...\n";
        try {
            $db->query("ALTER TABLE users ADD COLUMN temp_data TEXT NULL");
            echo "   ✅ Поле temp_data добавлено\n";
        } catch (Exception $e2) {
            echo "   ❌ Ошибка добавления поля: " . $e2->getMessage() . "\n";
        }
    }
    
    // 2. Проверяем таблицу withdrawal_requests
    echo "\n2. Проверка таблицы withdrawal_requests...\n";
    try {
        $result = $db->query("SELECT COUNT(*) as count FROM withdrawal_requests");
        echo "   ✅ Таблица withdrawal_requests существует\n";
    } catch (Exception $e) {
        echo "   ⚠️  Таблица withdrawal_requests не найдена, создаю...\n";
        try {
            $db->query("
                CREATE TABLE withdrawal_requests (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id BIGINT NOT NULL,
                    amount DECIMAL(10,2) NOT NULL,
                    status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending',
                    admin_comment TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");
            echo "   ✅ Таблица withdrawal_requests создана\n";
        } catch (Exception $e2) {
            echo "   ❌ Ошибка создания таблицы: " . $e2->getMessage() . "\n";
        }
    }
    
    // 3. Проверяем настройки в bot_settings
    echo "\n3. Проверка настроек бота...\n";
    
    $requiredSettings = [
        'help_text' => "📋 <b>Доступные команды:</b>\n\n/start - Начать работу\n/create_deal - Создать сделку\n/my_deals - Мои сделки\n/balance - Баланс\n/help - Помощь\n/support - Поддержка\n\n💡 <b>Как работает гарант:</b>\n1. Создайте сделку и найдите второго участника\n2. Покупатель пополняет баланс на сумму сделки\n3. После выполнения условий продавец получает средства\n4. Сервис берет комиссию 5% за безопасность сделки",
        'support_text' => "📞 <b>Техническая поддержка</b>\n\n🔧 <b>Если у вас возникли проблемы:</b>\n• Ошибки в работе бота\n• Вопросы по сделкам\n• Проблемы с платежами\n• Споры с участниками\n\n📱 <b>Свяжитесь с нами:</b>\n💬 Telegram: @your_support\n📧 Email: support@yourbot.com\n\n⏰ <b>Время работы:</b> 24/7\n💬 <b>Среднее время ответа:</b> 2-4 часа",
        'commission_percent' => '5',
        'min_deal_amount' => '100',
        'max_deal_amount' => '100000',
        'sberbank_data' => '{"card_number":"","phone":"","holder_name":""}',
        'mir_card_data' => '{"card_number":"","holder_name":"","bank_name":""}',
        'manual_card_data' => '{"card_number":"","holder_name":"","bank_name":""}'
    ];
    
    foreach ($requiredSettings as $key => $defaultValue) {
        $existing = $db->fetch('SELECT id FROM bot_settings WHERE setting_key = ?', [$key]);
        if (!$existing) {
            $db->insert('bot_settings', [
                'setting_key' => $key,
                'setting_value' => $defaultValue,
                'description' => 'Настройка бота',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            echo "   ✅ Добавлена настройка: {$key}\n";
        } else {
            echo "   ℹ️  Настройка {$key} уже существует\n";
        }
    }
    
    // 4. Проверяем структуру таблицы payments
    echo "\n4. Проверка структуры таблицы payments...\n";
    try {
        $columns = $db->fetchAll("DESCRIBE payments");
        $hasUserIdColumn = false;
        foreach ($columns as $column) {
            if ($column['Field'] === 'user_id') {
                $hasUserIdColumn = true;
                break;
            }
        }
        
        if (!$hasUserIdColumn) {
            echo "   ⚠️  Поле user_id не найдено в таблице payments, добавляю...\n";
            $db->query("ALTER TABLE payments ADD COLUMN user_id BIGINT NOT NULL AFTER id");
            echo "   ✅ Поле user_id добавлено\n";
        } else {
            echo "   ✅ Структура таблицы payments корректна\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Ошибка проверки структуры payments: " . $e->getMessage() . "\n";
    }
    
    // 5. Тестируем основные функции
    echo "\n5. Тестирование основных функций...\n";
    
    // Тест вставки пользователя
    try {
        $testUserId = 999999999;
        $existing = $db->fetch('SELECT id FROM users WHERE telegram_id = ?', [$testUserId]);
        if (!$existing) {
            $db->insert('users', [
                'telegram_id' => $testUserId,
                'first_name' => 'Test',
                'last_name' => 'User',
                'username' => 'testuser',
                'balance' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            echo "   ✅ Тест создания пользователя прошел\n";
            
            // Удаляем тестового пользователя
            $db->query('DELETE FROM users WHERE telegram_id = ?', [$testUserId]);
        } else {
            echo "   ✅ Таблица users работает корректно\n";
        }
    } catch (Exception $e) {
        echo "   ❌ Ошибка тестирования users: " . $e->getMessage() . "\n";
    }
    
    echo "\n🎉 Диагностика завершена!\n";
    echo "📊 Статистика базы данных:\n";
    
    try {
        $userCount = $db->fetch('SELECT COUNT(*) as count FROM users')['count'];
        $dealCount = $db->fetch('SELECT COUNT(*) as count FROM deals')['count'];
        $paymentCount = $db->fetch('SELECT COUNT(*) as count FROM payments')['count'];
        $withdrawalCount = $db->fetch('SELECT COUNT(*) as count FROM withdrawal_requests')['count'];
        
        echo "   👥 Пользователей: {$userCount}\n";
        echo "   💼 Сделок: {$dealCount}\n";
        echo "   💳 Платежей: {$paymentCount}\n";
        echo "   💸 Заявок на вывод: {$withdrawalCount}\n";
    } catch (Exception $e) {
        echo "   ❌ Ошибка получения статистики: " . $e->getMessage() . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Ошибка подключения к базе данных: " . $e->getMessage() . "\n";
    echo "🔧 Проверьте настройки в config.php\n";
}
?>