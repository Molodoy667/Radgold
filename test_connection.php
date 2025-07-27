<?php
/**
 * Тестирование подключения к базе данных
 */

require_once 'config/config.php';

echo "=== Тест подключения к базе данных ===\n";
echo "PHP версия: " . PHP_VERSION . "\n";
echo "База данных: " . $db_config['database'] . "\n";
echo "Пользователь: " . $db_config['username'] . "\n";
echo "Хост: " . $db_config['host'] . "\n\n";

try {
    $dsn = "mysql:host={$db_config['host']};dbname={$db_config['database']};charset={$db_config['charset']}";
    $pdo = new PDO($dsn, $db_config['username'], $db_config['password'], $db_config['options']);
    
    echo "✅ Успешное подключение к базе данных!\n";
    
    // Проверим версию MySQL
    $stmt = $pdo->query('SELECT VERSION() as version');
    $result = $stmt->fetch();
    echo "MySQL версия: " . $result['version'] . "\n";
    
    // Проверим доступные таблицы
    $stmt = $pdo->query('SHOW TABLES');
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Количество таблиц в базе: " . count($tables) . "\n";
    
    if (count($tables) > 0) {
        echo "Существующие таблицы:\n";
        foreach ($tables as $table) {
            echo "  - " . $table . "\n";
        }
    }
    
} catch (PDOException $e) {
    echo "❌ Ошибка подключения к базе данных:\n";
    echo "Сообщение: " . $e->getMessage() . "\n";
    echo "Код ошибки: " . $e->getCode() . "\n";
}

echo "\n=== Конец теста ===\n";
?>