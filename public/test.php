<?php
// Тестовая страница для проверки работы системы
try {
    // Подключаем bootstrap
    $bootstrap = require_once __DIR__ . '/../app/bootstrap.php';
    
    echo "<h1>🎮 GameMarket Pro - Тест системы</h1>";
    echo "<div style='font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; border-radius: 10px; margin: 20px;'>";
    
    echo "<h2>✅ Bootstrap загружен успешно</h2>";
    
    // Проверяем классы
    echo "<h3>Проверка классов:</h3>";
    
    $classes = [
        'App\Core\Database',
        'App\Core\Router', 
        'App\Core\Controller',
        'App\Models\User',
        'App\Models\Product'
    ];
    
    foreach ($classes as $class) {
        if (class_exists($class)) {
            echo "✅ $class - загружен<br>";
        } else {
            echo "❌ $class - НЕ загружен<br>";
        }
    }
    
    // Проверяем подключение к БД
    echo "<h3>Проверка базы данных:</h3>";
    if (isset($GLOBALS['db'])) {
        echo "✅ Подключение к БД установлено<br>";
        
        // Простой тест запроса
        $stmt = $GLOBALS['db']->query("SELECT 1 as test");
        $result = $stmt->fetch();
        if ($result && $result['test'] == 1) {
            echo "✅ Тестовый запрос выполнен успешно<br>";
        } else {
            echo "❌ Ошибка выполнения тестового запроса<br>";
        }
        
    } else {
        echo "❌ Подключение к БД не установлено<br>";
    }
    
    // Проверяем helper функции
    echo "<h3>Проверка helper функций:</h3>";
    $functions = ['config', 'url', 'sanitize', 'csrf_token'];
    
    foreach ($functions as $func) {
        if (function_exists($func)) {
            echo "✅ $func() - доступна<br>";
        } else {
            echo "❌ $func() - НЕ доступна<br>";
        }
    }
    
    // Показываем конфигурацию
    echo "<h3>Конфигурация приложения:</h3>";
    echo "Название: " . config('app_name') . "<br>";
    echo "URL: " . config('app_url') . "<br>";
    echo "Debug режим: " . (config('debug') ? 'включен' : 'выключен') . "<br>";
    echo "Временная зона: " . config('timezone') . "<br>";
    
    echo "<h3>Информация о PHP:</h3>";
    echo "Версия PHP: " . PHP_VERSION . "<br>";
    echo "Кодировка: " . mb_internal_encoding() . "<br>";
    
    echo "<h3>Тест роутера:</h3>";
    echo "URI: " . ($_SERVER['REQUEST_URI'] ?? 'не определен') . "<br>";
    echo "Метод: " . ($_SERVER['REQUEST_METHOD'] ?? 'не определен') . "<br>";
    
    echo "</div>";
    
    echo "<div style='padding: 20px; text-align: center;'>";
    echo "<a href='/' style='background: #667eea; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>На главную</a> ";
    echo "<a href='/install.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Установка</a>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<h1>❌ Ошибка!</h1>";
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px;'>";
    echo "<strong>Сообщение:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "<strong>Файл:</strong> " . $e->getFile() . "<br>";
    echo "<strong>Строка:</strong> " . $e->getLine() . "<br>";
    echo "<details style='margin-top: 10px;'>";
    echo "<summary>Трассировка стека</summary>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</details>";
    echo "</div>";
    
    echo "<div style='padding: 20px; text-align: center;'>";
    echo "<a href='/install.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Запустить установку</a>";
    echo "</div>";
}
?>