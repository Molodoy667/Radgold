<?php
// Файл для дебагінгу та тестування системи
// ВИДАЛІТЬ цей файл на production сервері!

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'core/config.php';
require_once 'core/functions.php';

echo "<h1>🔍 AdBoard Pro - Система дебагінгу</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;background:#f5f5f5;} .test{background:white;padding:15px;margin:10px 0;border-radius:8px;border-left:4px solid #007bff;} .success{border-color:#28a745;} .error{border-color:#dc3545;} .warning{border-color:#ffc107;}</style>";

// Тест 1: Конфігурація
echo "<div class='test'>";
echo "<h3>📋 Конфігурація</h3>";
echo "<p><strong>Site URL:</strong> " . (defined('SITE_URL') ? SITE_URL : '❌ Не визначено') . "</p>";
echo "<p><strong>Site Name:</strong> " . (defined('SITE_NAME') ? SITE_NAME : '❌ Не визначено') . "</p>";
echo "<p><strong>DB Host:</strong> " . (defined('DB_HOST') ? DB_HOST : '❌ Не визначено') . "</p>";
echo "<p><strong>DB Name:</strong> " . (defined('DB_NAME') ? DB_NAME : '❌ Не визначено') . "</p>";
echo "<p><strong>PHP Version:</strong> " . PHP_VERSION . "</p>";
echo "</div>";

// Тест 2: Підключення до БД
echo "<div class='test'>";
echo "<h3>🗃️ База даних</h3>";
try {
    $db = new Database();
    echo "<p>✅ <strong>Підключення до БД:</strong> Успішне</p>";
    
    // Перевірка основних таблиць
    $tables = ['users', 'categories', 'locations', 'ads', 'settings'];
    foreach ($tables as $table) {
        try {
            $result = $db->query("SELECT COUNT(*) as count FROM $table");
            $row = $result->fetch_assoc();
            echo "<p>✅ <strong>Таблиця $table:</strong> {$row['count']} записів</p>";
        } catch (Exception $e) {
            echo "<p>❌ <strong>Таблиця $table:</strong> Помилка - " . $e->getMessage() . "</p>";
        }
    }
} catch (Exception $e) {
    echo "<p>❌ <strong>Підключення до БД:</strong> " . $e->getMessage() . "</p>";
}
echo "</div>";

// Тест 3: Файлова система
echo "<div class='test'>";
echo "<h3>📁 Файлова система</h3>";
$directories = [
    'images/uploads' => 'Завантажені зображення',
    'images/thumbs' => 'Мініатюри',
    'images/avatars' => 'Аватари',
    'logs' => 'Логи системи',
    'themes' => 'Теми'
];

foreach ($directories as $dir => $desc) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "<p>✅ <strong>$desc ($dir):</strong> Існує та доступний для запису</p>";
        } else {
            echo "<p>⚠️ <strong>$desc ($dir):</strong> Існує, але недоступний для запису</p>";
        }
    } else {
        echo "<p>❌ <strong>$desc ($dir):</strong> Не існує</p>";
    }
}
echo "</div>";

// Тест 4: Функції
echo "<div class='test'>";
echo "<h3>⚙️ Основні функції</h3>";
$functions = [
    'sanitize' => 'Санітизація данних',
    'isLoggedIn' => 'Перевірка авторизації',
    'getCategories' => 'Отримання категорій',
    'getLocations' => 'Отримання локацій',
    'getSiteUrl' => 'Генерація URL'
];

foreach ($functions as $func => $desc) {
    if (function_exists($func)) {
        echo "<p>✅ <strong>$desc ($func):</strong> Функція існує</p>";
        
        // Тестування деяких функцій
        if ($func === 'getSiteUrl') {
            try {
                $url = getSiteUrl('test');
                echo "<p>   📌 Тест: getSiteUrl('test') = $url</p>";
            } catch (Exception $e) {
                echo "<p>   ❌ Помилка тестування: " . $e->getMessage() . "</p>";
            }
        }
    } else {
        echo "<p>❌ <strong>$desc ($func):</strong> Функція не існує</p>";
    }
}
echo "</div>";

// Тест 5: Розширення PHP
echo "<div class='test'>";
echo "<h3>🔧 PHP Розширення</h3>";
$extensions = [
    'mysqli' => 'MySQL підключення',
    'gd' => 'Робота з зображеннями',
    'json' => 'JSON обробка',
    'mbstring' => 'Мультибайтові рядки',
    'curl' => 'HTTP запити',
    'fileinfo' => 'Інформація про файли'
];

foreach ($extensions as $ext => $desc) {
    if (extension_loaded($ext)) {
        echo "<p>✅ <strong>$desc ($ext):</strong> Встановлено</p>";
    } else {
        echo "<p>❌ <strong>$desc ($ext):</strong> Не встановлено</p>";
    }
}
echo "</div>";

// Тест 6: Налаштування PHP
echo "<div class='test'>";
echo "<h3>📊 Налаштування PHP</h3>";
$settings = [
    'memory_limit' => 'Ліміт пам\'яті',
    'upload_max_filesize' => 'Максимальний розмір файлу',
    'post_max_size' => 'Максимальний розмір POST',
    'max_execution_time' => 'Максимальний час виконання'
];

foreach ($settings as $setting => $desc) {
    $value = ini_get($setting);
    echo "<p>📌 <strong>$desc:</strong> $value</p>";
}
echo "</div>";

// Тест 7: URL та маршрутизація
echo "<div class='test'>";
echo "<h3>🌐 URL та маршрутизація</h3>";
echo "<p><strong>Поточний URL:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p><strong>HTTP Host:</strong> " . $_SERVER['HTTP_HOST'] . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";

// Тест генерації URL
$testUrls = ['', 'ads', 'create-ad', 'admin'];
foreach ($testUrls as $url) {
    try {
        $generated = getSiteUrl($url);
        echo "<p>📌 <strong>getSiteUrl('$url'):</strong> $generated</p>";
    } catch (Exception $e) {
        echo "<p>❌ <strong>Помилка для '$url':</strong> " . $e->getMessage() . "</p>";
    }
}
echo "</div>";

// Тест 8: Сесії
echo "<div class='test'>";
echo "<h3>🔐 Сесії</h3>";
if (session_status() === PHP_SESSION_ACTIVE) {
    echo "<p>✅ <strong>Сесії:</strong> Активні</p>";
    echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
    if (!empty($_SESSION)) {
        echo "<p><strong>Дані сесії:</strong></p>";
        echo "<pre>" . print_r($_SESSION, true) . "</pre>";
    } else {
        echo "<p>📌 <strong>Дані сесії:</strong> Порожні</p>";
    }
} else {
    echo "<p>❌ <strong>Сесії:</strong> Не активні</p>";
}
echo "</div>";

// Завершення
echo "<div class='test success'>";
echo "<h3>✅ Дебагінг завершено</h3>";
echo "<p>Дата та час: " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>⚠️ ВАЖЛИВО:</strong> Видаліть цей файл (debug.php) на production сервері!</p>";
echo "</div>";

// Лінки для тестування
echo "<div class='test'>";
echo "<h3>🔗 Посилання для тестування</h3>";
echo "<p><a href='" . getSiteUrl() . "'>🏠 Головна сторінка</a></p>";
echo "<p><a href='" . getSiteUrl('ads') . "'>📋 Дошка оголошень</a></p>";
echo "<p><a href='" . getSiteUrl('create-ad') . "'>➕ Створити оголошення</a></p>";
echo "<p><a href='" . getSiteUrl('admin') . "'>⚙️ Адмін панель</a></p>";
echo "<p><a href='" . getSiteUrl('install') . "'>🔧 Інсталятор</a></p>";
echo "</div>";
?>