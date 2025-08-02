<?php
require_once 'config.php';
require_once 'classes/TelegramBot.php';

$config = require 'config.php';

// Получаем данные от Telegram
$input = file_get_contents('php://input');
$update = json_decode($input, true);

// Логируем входящие данные (для отладки)
error_log('Telegram webhook: ' . $input);

if ($update) {
    try {
        $bot = new TelegramBot($config);
        $bot->processUpdate($update);
    } catch (Exception $e) {
        error_log('Bot error: ' . $e->getMessage());
    }
}

// Отвечаем Telegram, что запрос обработан
http_response_code(200);
echo 'OK';
?>