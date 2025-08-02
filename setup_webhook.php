<?php
require_once 'config.php';

$config = require 'config.php';

// Получаем токен бота из конфигурации
$botToken = $config['telegram']['bot_token'];
$webhookUrl = $config['telegram']['webhook_url'];

if ($botToken === 'YOUR_BOT_TOKEN_HERE') {
    die('❌ Сначала укажите токен бота в config.php');
}

echo "<h2>🔧 Настройка Telegram Webhook</h2>";

// Устанавливаем webhook
$apiUrl = "https://api.telegram.org/bot{$botToken}/setWebhook";

$data = [
    'url' => $webhookUrl,
    'allowed_updates' => ['message', 'callback_query']
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$result = json_decode($response, true);

echo "<div style='font-family: Arial; padding: 20px;'>";

if ($httpCode === 200 && $result['ok']) {
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "✅ <strong>Webhook успешно установлен!</strong><br>";
    echo "📍 URL: {$webhookUrl}<br>";
    echo "🤖 Бот готов к работе!";
    echo "</div>";
    
    echo "<h3>🧪 Тестирование:</h3>";
    echo "<p>1. Найдите вашего бота в Telegram</p>";
    echo "<p>2. Отправьте команду <code>/start</code></p>";
    echo "<p>3. Бот должен ответить приветственным сообщением</p>";
    
} else {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "❌ <strong>Ошибка установки webhook!</strong><br>";
    echo "Ответ API: " . htmlspecialchars($response);
    echo "</div>";
    
    echo "<h3>🔍 Возможные причины:</h3>";
    echo "<ul>";
    echo "<li>Неверный токен бота</li>";
    echo "<li>URL недоступен для Telegram</li>";
    echo "<li>Отсутствует SSL сертификат</li>";
    echo "<li>Файл webhook.php не существует</li>";
    echo "</ul>";
}

echo "<hr>";

// Проверяем текущий статус webhook
echo "<h3>📊 Информация о webhook:</h3>";

$infoUrl = "https://api.telegram.org/bot{$botToken}/getWebhookInfo";
$infoResponse = file_get_contents($infoUrl);
$infoResult = json_decode($infoResponse, true);

if ($infoResult['ok']) {
    $info = $infoResult['result'];
    echo "<table style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><td style='border: 1px solid #ddd; padding: 8px;'><strong>URL:</strong></td><td style='border: 1px solid #ddd; padding: 8px;'>" . ($info['url'] ?: 'Не установлен') . "</td></tr>";
    echo "<tr><td style='border: 1px solid #ddd; padding: 8px;'><strong>Ожидающих обновлений:</strong></td><td style='border: 1px solid #ddd; padding: 8px;'>" . $info['pending_update_count'] . "</td></tr>";
    echo "<tr><td style='border: 1px solid #ddd; padding: 8px;'><strong>Последняя ошибка:</strong></td><td style='border: 1px solid #ddd; padding: 8px;'>" . ($info['last_error_message'] ?: 'Нет ошибок') . "</td></tr>";
    echo "<tr><td style='border: 1px solid #ddd; padding: 8px;'><strong>Максимум соединений:</strong></td><td style='border: 1px solid #ddd; padding: 8px;'>" . $info['max_connections'] . "</td></tr>";
    echo "</table>";
} else {
    echo "<p style='color: red;'>Не удалось получить информацию о webhook</p>";
}

echo "<hr>";
echo "<h3>🔗 Полезные ссылки:</h3>";
echo "<p><a href='admin/' target='_blank'>🎛️ Админ-панель</a></p>";
echo "<p><a href='webhook.php' target='_blank'>🔗 Проверить webhook.php</a></p>";

echo "<hr>";
echo "<p><strong>⚠️ Важно:</strong> После успешной настройки удалите этот файл для безопасности!</p>";
echo "<p><code>Удалите файл: setup_webhook.php</code></p>";

echo "</div>";
?>