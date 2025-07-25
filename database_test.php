<?php
// Тест подключения к базе данных
header('Content-Type: text/html; charset=UTF-8');

require_once __DIR__ . '/vendor/autoload.php';

// Загружаем переменные окружения
\App\Core\Environment::load();

echo '<!DOCTYPE html>';
echo '<html lang="ru">';
echo '<head>';
echo '<meta charset="UTF-8">';
echo '<title>Тест подключения к БД - Game Marketplace</title>';
echo '<style>';
echo 'body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }';
echo '.container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 800px; margin: 0 auto; }';
echo '.success { color: #27ae60; background: #d5f4e6; padding: 10px; border-radius: 4px; margin: 10px 0; }';
echo '.error { color: #e74c3c; background: #fadbd8; padding: 10px; border-radius: 4px; margin: 10px 0; }';
echo '.info { color: #3498db; background: #d6eaf8; padding: 10px; border-radius: 4px; margin: 10px 0; }';
echo 'pre { background: #f8f9fa; padding: 10px; border-radius: 4px; overflow-x: auto; }';
echo '</style>';
echo '</head>';
echo '<body>';

echo '<div class="container">';
echo '<h1>🔧 Тест подключения к базе данных</h1>';

// Проверяем переменные окружения
echo '<h2>📋 Переменные окружения</h2>';
echo '<div class="info">';
echo '<strong>DB_HOST:</strong> ' . ($_ENV['DB_HOST'] ?? 'не установлено') . '<br>';
echo '<strong>DB_NAME:</strong> ' . ($_ENV['DB_NAME'] ?? 'не установлено') . '<br>';
echo '<strong>DB_USER:</strong> ' . ($_ENV['DB_USER'] ?? 'не установлено') . '<br>';
echo '<strong>DB_CHARSET:</strong> ' . ($_ENV['DB_CHARSET'] ?? 'не установлено') . '<br>';
echo '<strong>DB_COLLATION:</strong> ' . ($_ENV['DB_COLLATION'] ?? 'не установлено') . '<br>';
echo '</div>';

// Тестируем подключение
echo '<h2>🔌 Тест подключения</h2>';

try {
    $config = require __DIR__ . '/app/config/database.php';
    
    echo '<div class="info">';
    echo '<strong>Конфигурация подключения:</strong><br>';
    echo 'Host: ' . $config['host'] . '<br>';
    echo 'Database: ' . $config['dbname'] . '<br>';
    echo 'User: ' . $config['user'] . '<br>';
    echo 'Charset: ' . $config['charset'] . '<br>';
    echo 'Collation: ' . $config['collation'] . '<br>';
    echo '</div>';
    
    $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['user'], $config['password'], $config['options'] ?? []);
    
    echo '<div class="success">✅ Подключение к базе данных успешно!</div>';
    
    // Проверяем кодировку
    $stmt = $pdo->query("SELECT @@character_set_database as charset, @@collation_database as collation");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo '<div class="info">';
    echo '<strong>Кодировка базы данных:</strong><br>';
    echo 'Character Set: ' . $result['charset'] . '<br>';
    echo 'Collation: ' . $result['collation'] . '<br>';
    echo '</div>';
    
    // Проверяем таблицы
    echo '<h2>📊 Проверка таблиц</h2>';
    
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo '<div class="error">❌ Таблицы не найдены. Возможно, нужно импортировать дамп базы данных.</div>';
    } else {
        echo '<div class="success">✅ Найдено таблиц: ' . count($tables) . '</div>';
        echo '<div class="info">';
        echo '<strong>Список таблиц:</strong><br>';
        foreach ($tables as $table) {
            echo '- ' . $table . '<br>';
        }
        echo '</div>';
    }
    
    // Проверяем пользователей
    if (in_array('users', $tables)) {
        echo '<h2>👥 Проверка пользователей</h2>';
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo '<div class="info">';
        echo '<strong>Количество пользователей:</strong> ' . $result['count'] . '<br>';
        echo '</div>';
        
        if ($result['count'] > 0) {
            $stmt = $pdo->query("SELECT id, login, email, role FROM users LIMIT 5");
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo '<div class="info">';
            echo '<strong>Первые 5 пользователей:</strong><br>';
            foreach ($users as $user) {
                echo '- ID: ' . $user['id'] . ', Login: ' . $user['login'] . ', Email: ' . $user['email'] . ', Role: ' . $user['role'] . '<br>';
            }
            echo '</div>';
        }
    }
    
    // Проверяем товары
    if (in_array('products', $tables)) {
        echo '<h2>🎮 Проверка товаров</h2>';
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM products");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo '<div class="info">';
        echo '<strong>Количество товаров:</strong> ' . $result['count'] . '<br>';
        echo '</div>';
    }
    
} catch (PDOException $e) {
    echo '<div class="error">❌ Ошибка подключения к базе данных: ' . $e->getMessage() . '</div>';
} catch (Exception $e) {
    echo '<div class="error">❌ Общая ошибка: ' . $e->getMessage() . '</div>';
}

echo '<h2>📝 Рекомендации</h2>';
echo '<div class="info">';
echo '1. Убедитесь, что база данных <strong>iteiyzke_market</strong> существует<br>';
echo '2. Проверьте, что пользователь <strong>novado</strong> имеет права доступа<br>';
echo '3. Импортируйте дамп базы данных из файла <strong>database/dump.sql</strong><br>';
echo '4. Убедитесь, что MySQL поддерживает кодировку <strong>utf8mb4</strong><br>';
echo '</div>';

echo '<p><a href="/" style="color: #3498db; text-decoration: none;">← Вернуться на главную</a></p>';

echo '</div>';
echo '</body>';
echo '</html>';