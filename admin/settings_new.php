<?php
// Устанавливаем заголовок страницы
$page_title = 'Налаштування';

// Подключаем header
require_once 'includes/header.php';

// Отримуємо всі налаштування
$all_settings = [];
$settings_query = "SELECT setting_key, setting_value FROM settings";
$settings_stmt = $db->prepare($settings_query);
$settings_stmt->execute();
while ($row = $settings_stmt->fetch(PDO::FETCH_ASSOC)) {
    $all_settings[$row['setting_key']] = $row['setting_value'];
}
