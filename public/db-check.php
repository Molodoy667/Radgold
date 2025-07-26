<?php
// Диагностика базы данных GameMarket Pro

require_once __DIR__ . '/../app/bootstrap.php';

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Диагностика БД - GameMarket Pro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .status {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 8px 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: bold;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 5px;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn-danger:hover {
            background: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Диагностика базы данных GameMarket Pro</h1>
        
        <?php
        $dbConnected = false;
        $dbError = '';
        $tables = [];
        $tableStatus = [];
        
        try {
            // Проверка подключения к БД
            $db = $GLOBALS['db'] ?? null;
            if ($db) {
                $dbConnected = true;
                echo '<div class="status success">✅ Подключение к базе данных успешно</div>';
                
                // Получение информации о БД
                $dbInfo = $db->query("SELECT DATABASE() as db_name")->fetch();
                echo '<div class="status info">📊 База данных: <strong>' . htmlspecialchars($dbInfo['db_name']) . '</strong></div>';
                
                // Список всех таблиц
                $stmt = $db->query("SHOW TABLES");
                $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                if (empty($tables)) {
                    echo '<div class="status warning">⚠️ В базе данных нет таблиц! Необходима установка схемы.</div>';
                } else {
                    echo '<div class="status success">✅ Найдено таблиц: ' . count($tables) . '</div>';
                }
                
                // Проверка каждой таблицы
                $requiredTables = [
                    'users', 'products', 'categories', 'purchases', 'reviews', 
                    'favorites', 'messages', 'disputes', 'notifications', 
                    'transactions', 'settings'
                ];
                
                foreach ($requiredTables as $table) {
                    if (in_array($table, $tables)) {
                        try {
                            $count = $db->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
                            $tableStatus[$table] = [
                                'exists' => true,
                                'count' => $count,
                                'error' => null
                            ];
                        } catch (Exception $e) {
                            $tableStatus[$table] = [
                                'exists' => true,
                                'count' => 0,
                                'error' => $e->getMessage()
                            ];
                        }
                    } else {
                        $tableStatus[$table] = [
                            'exists' => false,
                            'count' => 0,
                            'error' => 'Таблица не существует'
                        ];
                    }
                }
                
            } else {
                throw new Exception('База данных не подключена');
            }
            
        } catch (Exception $e) {
            $dbError = $e->getMessage();
            echo '<div class="status error">❌ Ошибка подключения к БД: ' . htmlspecialchars($dbError) . '</div>';
        }
        ?>
        
        <?php if ($dbConnected): ?>
            <h2>📋 Состояние таблиц</h2>
            <table>
                <thead>
                    <tr>
                        <th>Таблица</th>
                        <th>Статус</th>
                        <th>Записей</th>
                        <th>Ошибка</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tableStatus as $table => $status): ?>
                        <tr>
                            <td><code><?= htmlspecialchars($table) ?></code></td>
                            <td>
                                <?php if ($status['exists']): ?>
                                    <span style="color: green;">✅ Существует</span>
                                <?php else: ?>
                                    <span style="color: red;">❌ Отсутствует</span>
                                <?php endif; ?>
                            </td>
                            <td><?= number_format($status['count']) ?></td>
                            <td>
                                <?php if ($status['error']): ?>
                                    <span style="color: red;"><?= htmlspecialchars($status['error']) ?></span>
                                <?php else: ?>
                                    <span style="color: green;">OK</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php
            $missingTables = array_filter($tableStatus, function($status) {
                return !$status['exists'];
            });
            
            if (!empty($missingTables)):
            ?>
                <div class="status warning">
                    ⚠️ Отсутствуют таблицы: <?= implode(', ', array_keys($missingTables)) ?>
                </div>
            <?php endif; ?>
            
            <h2>🛠️ Действия</h2>
            <?php if (!empty($missingTables) || empty($tables)): ?>
                <a href="/install.php" class="btn">🚀 Запустить установку</a>
                <a href="/install.php?step=install&force=1" class="btn btn-danger">⚠️ Переустановить схему БД</a>
            <?php else: ?>
                <div class="status success">✅ Все таблицы на месте! База данных готова к работе.</div>
                <a href="/" class="btn">🏠 Перейти на главную</a>
            <?php endif; ?>
            
        <?php else: ?>
            <h2>❌ Проблемы с подключением</h2>
            <div class="status error">
                <strong>Ошибка:</strong> <?= htmlspecialchars($dbError) ?>
            </div>
            <p>Возможные причины:</p>
            <ul>
                <li>Неверные данные подключения в <code>app/config/database.php</code></li>
                <li>MySQL сервер не запущен</li>
                <li>База данных не создана</li>
                <li>Нет прав доступа для пользователя</li>
            </ul>
            <a href="/install.php" class="btn">🔧 Запустить мастер установки</a>
        <?php endif; ?>
        
        <hr style="margin: 30px 0;">
        
        <h2>ℹ️ Конфигурация</h2>
        <?php
        $dbConfig = require __DIR__ . '/../app/config/database.php';
        $mysqlConfig = $dbConfig['connections']['mysql'];
        ?>
        <table>
            <tr>
                <td><strong>Хост:</strong></td>
                <td><?= htmlspecialchars($mysqlConfig['host']) ?></td>
            </tr>
            <tr>
                <td><strong>Порт:</strong></td>
                <td><?= htmlspecialchars($mysqlConfig['port']) ?></td>
            </tr>
            <tr>
                <td><strong>База данных:</strong></td>
                <td><?= htmlspecialchars($mysqlConfig['database']) ?></td>
            </tr>
            <tr>
                <td><strong>Пользователь:</strong></td>
                <td><?= htmlspecialchars($mysqlConfig['username']) ?></td>
            </tr>
            <tr>
                <td><strong>Кодировка:</strong></td>
                <td><?= htmlspecialchars($mysqlConfig['charset']) ?></td>
            </tr>
        </table>
        
        <p><small>Для изменения настроек отредактируйте файл <code>app/config/database.php</code></small></p>
    </div>
</body>
</html>