<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Установка GameMarket Pro</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #667eea;
            margin-bottom: 10px;
        }
        .status {
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .status.success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .status.error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .status.warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            margin: 5px;
        }
        .btn:hover {
            background: #5a6fd8;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            border: 1px solid #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎮 GameMarket Pro</h1>
            <p>Установка и настройка маркетплейса</p>
        </div>

        <?php
        $step = $_GET['step'] ?? 'check';
        $action = $_POST['action'] ?? '';

        // Проверка системных требований
        function checkSystemRequirements() {
            $requirements = [
                'PHP версия >= 7.4' => version_compare(PHP_VERSION, '7.4.0', '>='),
                'PDO расширение' => extension_loaded('pdo'),
                'PDO MySQL' => extension_loaded('pdo_mysql'),
                'mbstring расширение' => extension_loaded('mbstring'),
                'JSON расширение' => extension_loaded('json'),
                'Запись в папку public' => is_writable(__DIR__),
            ];
            
            return $requirements;
        }

        // Тест подключения к БД
        function testDatabaseConnection($config) {
            try {
                $dsn = sprintf(
                    'mysql:host=%s;port=%s;charset=%s',
                    $config['host'],
                    $config['port'],
                    $config['charset']
                );
                
                $pdo = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
                
                return ['success' => true, 'message' => 'Подключение к MySQL серверу успешно'];
            } catch (PDOException $e) {
                return ['success' => false, 'message' => 'Ошибка подключения: ' . $e->getMessage()];
            }
        }

        // Создание базы данных
        function createDatabase($config) {
            try {
                $dsn = sprintf(
                    'mysql:host=%s;port=%s;charset=%s',
                    $config['host'],
                    $config['port'],
                    $config['charset']
                );
                
                $pdo = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
                
                // Создаем базу данных
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                
                // Подключаемся к созданной базе
                $dsn = sprintf(
                    'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                    $config['host'],
                    $config['port'],
                    $config['database'],
                    $config['charset']
                );
                
                $pdo = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                );
                
                return ['success' => true, 'pdo' => $pdo, 'message' => 'База данных создана успешно'];
            } catch (PDOException $e) {
                return ['success' => false, 'message' => 'Ошибка создания БД: ' . $e->getMessage()];
            }
        }

        // Установка схемы БД
        function installSchema($pdo) {
            // Сначала пробуем исправленную схему
            $schemaFile = __DIR__ . '/../database/schema_fixed.sql';
            
            if (!file_exists($schemaFile)) {
                // Если нет исправленной, используем обычную
                $schemaFile = __DIR__ . '/../database/schema.sql';
            }
            
            if (!file_exists($schemaFile)) {
                return ['success' => false, 'message' => 'Файл схемы БД не найден: ' . $schemaFile];
            }
            
            try {
                $sql = file_get_contents($schemaFile);
                // Убираем команды USE и CREATE DATABASE из schema.sql
                $sql = preg_replace('/^(DROP DATABASE|CREATE DATABASE|USE)\s+.*;$/mi', '', $sql);
                
                // Разбиваем на отдельные запросы по точке с запятой
                $queries = array_filter(array_map('trim', preg_split('/;(\s*$)/m', $sql)));
                
                $executed = 0;
                $errors = [];
                
                foreach ($queries as $query) {
                    $query = trim($query);
                    if (!empty($query) && !preg_match('/^(--|\/\*)/i', $query)) {
                        try {
                            $pdo->exec($query);
                            $executed++;
                        } catch (PDOException $e) {
                            // Игнорируем ошибки "таблица уже существует"
                            if (strpos($e->getMessage(), 'already exists') === false) {
                                $errors[] = "Запрос: " . substr($query, 0, 100) . "... Ошибка: " . $e->getMessage();
                            }
                        }
                    }
                }
                
                if (empty($errors)) {
                    return ['success' => true, 'message' => "Схема БД установлена успешно. Выполнено запросов: $executed"];
                } else {
                    return ['success' => false, 'message' => 'Ошибки при установке схемы: ' . implode('; ', $errors)];
                }
                
            } catch (Exception $e) {
                return ['success' => false, 'message' => 'Общая ошибка установки схемы: ' . $e->getMessage()];
            }
        }

        if ($step === 'check') {
            echo '<h2>Шаг 1: Проверка системных требований</h2>';
            
            $requirements = checkSystemRequirements();
            $allOk = true;
            
            foreach ($requirements as $requirement => $status) {
                $class = $status ? 'success' : 'error';
                $icon = $status ? '✅' : '❌';
                $allOk = $allOk && $status;
                
                echo "<div class='status $class'>$icon $requirement</div>";
            }
            
            if ($allOk) {
                echo '<div class="status success">Все системные требования выполнены!</div>';
                echo '<a href="?step=database" class="btn">Далее: Настройка БД</a>';
            } else {
                echo '<div class="status error">Некоторые требования не выполнены. Установите необходимые расширения PHP.</div>';
            }
        }

        elseif ($step === 'database') {
            echo '<h2>Шаг 2: Настройка базы данных</h2>';
            
            if ($action === 'test_db') {
                            $config = [
                'host' => $_POST['db_host'] ?? 'localhost',
                'port' => $_POST['db_port'] ?? '3306',
                'database' => $_POST['db_name'] ?? 'iteiyzke_market',
                'username' => $_POST['db_user'] ?? 'novado',
                'password' => $_POST['db_pass'] ?? 'Q05021998@',
                    'charset' => 'utf8mb4'
                ];
                
                $testResult = testDatabaseConnection($config);
                $class = $testResult['success'] ? 'success' : 'error';
                echo "<div class='status $class'>{$testResult['message']}</div>";
                
                                 if ($testResult['success']) {
                     echo '<div style="margin-top: 15px;">';
                     echo '<a href="?step=install&' . http_build_query($config) . '" class="btn">Установить схему БД</a> ';
                     echo '<a href="?step=install&clean=1&' . http_build_query($config) . '" class="btn" style="background: #dc3545;">Чистая установка (очистить БД)</a>';
                     echo '</div>';
                     echo '<div style="margin-top: 10px; font-size: 0.9em; color: #666;">';
                     echo '<strong>Обычная установка:</strong> Попытается создать таблицы без удаления существующих<br>';
                     echo '<strong>Чистая установка:</strong> Удалит все существующие таблицы и создаст новые';
                     echo '</div>';
                 }
            }
            ?>
            
            <form method="post">
                <input type="hidden" name="action" value="test_db">
                
                <div class="form-group">
                    <label>Хост БД:</label>
                    <input type="text" name="db_host" value="<?= htmlspecialchars($_POST['db_host'] ?? 'localhost') ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Порт:</label>
                    <input type="text" name="db_port" value="<?= htmlspecialchars($_POST['db_port'] ?? '3306') ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Имя БД:</label>
                    <input type="text" name="db_name" value="<?= htmlspecialchars($_POST['db_name'] ?? 'iteiyzke_market') ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Пользователь:</label>
                    <input type="text" name="db_user" value="<?= htmlspecialchars($_POST['db_user'] ?? 'novado') ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Пароль:</label>
                    <input type="password" name="db_pass" value="<?= htmlspecialchars($_POST['db_pass'] ?? 'Q05021998@') ?>">
                </div>
                
                <button type="submit" class="btn">Проверить подключение</button>
            </form>
            
            <?php
        }

        elseif ($step === 'install') {
            echo '<h2>Шаг 3: Установка схемы базы данных</h2>';
            
            $config = [
                'host' => $_GET['host'] ?? 'localhost',
                'port' => $_GET['port'] ?? '3306',
                'database' => $_GET['database'] ?? 'gamemarket_pro',
                'username' => $_GET['username'] ?? '',
                'password' => $_GET['password'] ?? '',
                'charset' => 'utf8mb4'
            ];
            
            // Опция для очистки базы данных
            $cleanInstall = $_GET['clean'] ?? false;
            
            // Создаем БД
            $dbResult = createDatabase($config);
            $class = $dbResult['success'] ? 'success' : 'error';
            echo "<div class='status $class'>{$dbResult['message']}</div>";
            
            if ($dbResult['success']) {
                // Если выбрана чистая установка, очищаем БД
                if ($cleanInstall) {
                    try {
                        echo '<div class="status warning">Очистка базы данных...</div>';
                        
                        // Отключаем проверку внешних ключей
                        $dbResult['pdo']->exec('SET FOREIGN_KEY_CHECKS = 0');
                        
                        // Получаем список всех таблиц
                        $tables = $dbResult['pdo']->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                        
                        // Удаляем все таблицы
                        foreach ($tables as $table) {
                            $dbResult['pdo']->exec("DROP TABLE IF EXISTS `$table`");
                        }
                        
                        // Включаем проверку внешних ключей
                        $dbResult['pdo']->exec('SET FOREIGN_KEY_CHECKS = 1');
                        
                        echo '<div class="status success">База данных очищена</div>';
                    } catch (PDOException $e) {
                        echo '<div class="status warning">Предупреждение при очистке БД: ' . $e->getMessage() . '</div>';
                    }
                }
                
                // Устанавливаем схему
                $schemaResult = installSchema($dbResult['pdo']);
                $class = $schemaResult['success'] ? 'success' : 'error';
                echo "<div class='status $class'>{$schemaResult['message']}</div>";
                
                if ($schemaResult['success']) {
                    // Создаем файл конфигурации
                    $configContent = "<?php\n\nreturn [\n";
                    $configContent .= "    'default' => 'mysql',\n    'connections' => [\n        'mysql' => [\n";
                    $configContent .= "            'driver' => 'mysql',\n";
                    $configContent .= "            'host' => '{$config['host']}',\n";
                    $configContent .= "            'port' => '{$config['port']}',\n";
                    $configContent .= "            'database' => '{$config['database']}',\n";
                    $configContent .= "            'username' => '{$config['username']}',\n";
                    $configContent .= "            'password' => '{$config['password']}',\n";
                    $configContent .= "            'charset' => 'utf8mb4',\n";
                    $configContent .= "            'collation' => 'utf8mb4_unicode_ci',\n";
                    $configContent .= "            'options' => [\n";
                    $configContent .= "                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,\n";
                    $configContent .= "                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,\n";
                    $configContent .= "                PDO::ATTR_EMULATE_PREPARES => false,\n";
                    $configContent .= "                PDO::MYSQL_ATTR_INIT_COMMAND => \"SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci\"\n";
                    $configContent .= "            ],\n        ],\n    ],\n];\n";
                    
                    file_put_contents(__DIR__ . '/../app/config/database.php', $configContent);
                    
                    echo '<div class="status success">✅ Конфигурация БД сохранена</div>';
                    echo '<div class="status success">🎉 Установка завершена успешно!</div>';
                    echo '<a href="/" class="btn">Перейти на сайт</a>';
                }
            }
        }
        ?>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; text-align: center; color: #666;">
            <small>GameMarket Pro Installation Wizard</small>
        </div>
    </div>
</body>
</html>