<?php
// Файл установки AdBoard Pro
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(300); // Збільшуємо час виконання для імпорту БД

session_start();

// Функція для логування процесу установки
function logInstallStep($step, $message, $status = 'info') {
    if (!isset($_SESSION['install_log'])) {
        $_SESSION['install_log'] = [];
    }
    $_SESSION['install_log'][] = [
        'time' => date('H:i:s'),
        'step' => $step,
        'message' => $message,
        'status' => $status
    ];
}

// Якщо файл конфігурації існує і база вже налаштована, перенаправляємо
if (file_exists('core/config.php')) {
    require_once 'core/config.php';
    if (defined('DB_NAME')) {
        try {
            $test_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if ($test_connection->connect_error) {
                throw new Exception('Connection failed');
            }
            // Перевіряємо чи існують таблиці
            $result = $test_connection->query("SHOW TABLES LIKE 'users'");
            if ($result && $result->num_rows > 0) {
                $test_connection->close();
                header('Location: index.php');
                exit();
            }
            $test_connection->close();
        } catch (Exception $e) {
            // Продовжуємо установку якщо не можемо підключитися
            logInstallStep('check', 'База даних потребує налаштування: ' . $e->getMessage(), 'warning');
        }
    }
}

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$error = '';
$success = '';

// Обробка кроків установки
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($step) {
        case 1:
            // Перевірка системних вимог
            $step = 2;
            break;
            
        case 2:
            // Налаштування бази даних
            $db_host = trim($_POST['db_host'] ?? 'localhost');
            $db_user = trim($_POST['db_user'] ?? 'root');
            $db_pass = $_POST['db_pass'] ?? '';
            $db_name = trim($_POST['db_name'] ?? 'adboard_site');
            
            // Валідація введених даних
            if (empty($db_host) || empty($db_user) || empty($db_name)) {
                $error = 'Заповніть всі обов\'язкові поля';
                break;
            }
            
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $db_name)) {
                $error = 'Назва бази даних може містити тільки літери, цифри та підкреслення';
                break;
            }
            
            try {
                logInstallStep('database', 'Починаємо налаштування бази даних...', 'info');
                
                // Тестуємо підключення
                logInstallStep('database', 'Перевіряємо підключення до MySQL...', 'info');
                $connection = new mysqli($db_host, $db_user, $db_pass);
                if ($connection->connect_error) {
                    throw new Exception('Не вдалося підключитися до MySQL: ' . $connection->connect_error);
                }
                logInstallStep('database', 'Підключення до MySQL успішне', 'success');
                
                // Перевіряємо версію MySQL
                $version = $connection->server_info;
                logInstallStep('database', "Версія MySQL: $version", 'info');
                
                // Створюємо базу даних якщо не існує
                logInstallStep('database', "Створюємо базу даних '$db_name'...", 'info');
                if (!$connection->query("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
                    throw new Exception('Помилка створення бази даних: ' . $connection->error);
                }
                
                $connection->select_db($db_name);
                logInstallStep('database', 'База даних створена/обрана', 'success');
                
                // Перевіряємо чи SQL файл існує
                if (!file_exists('database.sql')) {
                    throw new Exception('Файл database.sql не знайдено');
                }
                
                // Імпортуємо SQL
                logInstallStep('database', 'Читаємо SQL файл...', 'info');
                $sql_content = file_get_contents('database.sql');
                if ($sql_content === false) {
                    throw new Exception('Не вдалося прочитати файл database.sql');
                }
                
                // Замінюємо назву бази даних
                $sql_content = str_replace('adboard_site', $db_name, $sql_content);
                
                // Виконуємо SQL запити
                logInstallStep('database', 'Виконуємо SQL запити...', 'info');
                $queries = explode(';', $sql_content);
                $executed = 0;
                $total = count(array_filter($queries, function($q) { return !empty(trim($q)); }));
                
                foreach ($queries as $query) {
                    $query = trim($query);
                    if (!empty($query) && !preg_match('/^--|^\/\*/', $query)) {
                        if (!$connection->query($query)) {
                            logInstallStep('database', 'SQL Error in query: ' . substr($query, 0, 100) . '...', 'error');
                            throw new Exception('SQL Error: ' . $connection->error . ' in query: ' . substr($query, 0, 100));
                        }
                        $executed++;
                        if ($executed % 5 == 0) {
                            logInstallStep('database', "Виконано $executed з $total запитів...", 'info');
                        }
                    }
                }
                
                logInstallStep('database', "Всього виконано $executed SQL запитів", 'success');
                
                // Перевіряємо створені таблиці
                $tables_result = $connection->query("SHOW TABLES");
                $tables_count = $tables_result->num_rows;
                logInstallStep('database', "Створено $tables_count таблиць", 'success');
                
                // Зберігаємо дані підключення в сесії
                $_SESSION['install'] = [
                    'db_host' => $db_host,
                    'db_user' => $db_user,
                    'db_pass' => $db_pass,
                    'db_name' => $db_name
                ];
                
                $connection->close();
                $step = 3;
                $success = 'База даних успішно створена!';
                logInstallStep('database', 'База даних налаштована успішно!', 'success');
                
            } catch (Exception $e) {
                $error = $e->getMessage();
                logInstallStep('database', 'Помилка: ' . $error, 'error');
            }
            break;
            
        case 3:
            // Створення адміністратора
            session_start();
            if (!isset($_SESSION['install'])) {
                $error = 'Помилка сесії. Почніть спочатку.';
                $step = 1;
                break;
            }
            
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            if (empty($username) || empty($email) || empty($password)) {
                $error = 'Заповніть всі поля';
            } elseif ($password !== $confirm_password) {
                $error = 'Паролі не співпадають';
            } elseif (strlen($password) < 6) {
                $error = 'Пароль повинен містити мінімум 6 символів';
            } else {
                try {
                    $install_data = $_SESSION['install'];
                    $connection = new mysqli(
                        $install_data['db_host'],
                        $install_data['db_user'],
                        $install_data['db_pass'],
                        $install_data['db_name']
                    );
                    
                    if ($connection->connect_error) {
                        throw new Exception('Database connection failed');
                    }
                    
                    // Видаляємо стандартного адміна
                    $connection->query("DELETE FROM users WHERE email = 'admin@adboardpro.com'");
                    
                    // Створюємо нового адміна
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $connection->prepare(
                        "INSERT INTO users (username, email, password, role, status, email_verified) VALUES (?, ?, ?, 'admin', 'active', 1)"
                    );
                    $stmt->bind_param('sss', $username, $email, $hashed_password);
                    
                    if (!$stmt->execute()) {
                        throw new Exception('Failed to create admin user');
                    }
                    
                    $step = 4;
                    $success = 'Адміністратор успішно створений!';
                    
                } catch (Exception $e) {
                    $error = 'Помилка створення адміністратора: ' . $e->getMessage();
                }
            }
            break;
            
        case 4:
            // Завершення установки
            session_start();
            if (!isset($_SESSION['install'])) {
                $error = 'Помилка сесії. Почніть спочатку.';
                $step = 1;
                break;
            }
            
            $install_data = $_SESSION['install'];
            
            // Створюємо файл конфігурації
            $config_content = "<?php
// Конфігурація бази даних
define('DB_HOST', '{$install_data['db_host']}');
define('DB_USER', '{$install_data['db_user']}');
define('DB_PASS', '{$install_data['db_pass']}');
define('DB_NAME', '{$install_data['db_name']}');

// Налаштування сайту
define('SITE_URL', 'http://{$_SERVER['HTTP_HOST']}');
define('SITE_NAME', 'AdBoard Pro');
define('SITE_DESCRIPTION', 'Рекламна компанія та дошка оголошень');
define('SITE_KEYWORDS', 'реклама, оголошення, дошка оголошень, маркетинг');

// Налаштування безпеки
define('SECRET_KEY', '" . bin2hex(random_bytes(32)) . "');
define('SESSION_NAME', 'adboard_session');

// Налаштування файлів
define('UPLOAD_PATH', 'images/uploads/');
define('MAX_FILE_SIZE', 5242880); // 5MB

// Налаштування пагінації
define('ITEMS_PER_PAGE', 12);

// Режим розробки
define('DEBUG_MODE', false);

// Автозавантаження класів
spl_autoload_register(function (\$class) {
    \$file = __DIR__ . '/classes/' . \$class . '.php';
    if (file_exists(\$file)) {
        require_once \$file;
    }
});

// Старт сесії
if (session_status() == PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}
?>";
            
            if (file_put_contents('core/config.php', $config_content)) {
                // Створюємо необхідні директорії
                $directories = [
                    'images/uploads',
                    'images/thumbs',
                    'images/avatars'
                ];
                
                foreach ($directories as $dir) {
                    if (!is_dir($dir)) {
                        mkdir($dir, 0755, true);
                    }
                }
                
                // Очищаємо дані установки
                unset($_SESSION['install']);
                
                $step = 5;
                $success = 'Установка завершена успішно!';
            } else {
                $error = 'Не вдалося створити файл конфігурації. Перевірте права доступу.';
            }
            break;
    }
}

// Перевірка системних вимог
function checkRequirements() {
    $requirements = [
        'PHP версія >= 7.4' => version_compare(PHP_VERSION, '7.4.0', '>='),
        'MySQLi extension' => extension_loaded('mysqli'),
        'JSON extension' => extension_loaded('json'),
        'GD extension' => extension_loaded('gd'),
        'cURL extension' => extension_loaded('curl'),
        'Можливість запису в core/' => is_writable('core/'),
        'Можливість запису в images/' => is_writable('images/'),
    ];
    
    return $requirements;
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Установка AdBoard Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            animation: fadeIn 0.8s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .install-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            margin: 2rem auto;
            max-width: 900px;
            animation: slideInUp 0.6s ease-out;
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .install-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 20px 20px 0 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .install-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            animation: shimmer 2s infinite;
        }
        
        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            margin: 2rem 0;
            position: relative;
        }
        
        .step-indicator::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background: #e9ecef;
            z-index: 0;
        }
        
        .step {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 15px;
            font-weight: bold;
            font-size: 18px;
            transition: all 0.3s ease;
            z-index: 1;
            position: relative;
        }
        
        .step.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: scale(1.1);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
            animation: pulse 1.5s infinite;
        }
        
        .step.completed {
            background: #28a745;
            color: white;
            transform: scale(1.05);
        }
        
        @keyframes pulse {
            0%, 100% { box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4); }
            50% { box-shadow: 0 5px 30px rgba(102, 126, 234, 0.6); }
        }
        
        .progress-container {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1rem 0;
            display: none;
        }
        
        .progress-bar-custom {
            width: 100%;
            height: 8px;
            background: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            margin: 1rem 0;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            width: 0%;
            transition: width 0.3s ease;
            border-radius: 4px;
        }
        
        .log-container {
            max-height: 300px;
            overflow-y: auto;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            display: none;
        }
        
        .log-entry {
            margin: 0.25rem 0;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            animation: fadeInLeft 0.3s ease-out;
        }
        
        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .log-info { background: #cce7ff; color: #0066cc; }
        .log-success { background: #d4edda; color: #155724; }
        .log-warning { background: #fff3cd; color: #856404; }
        .log-error { background: #f8d7da; color: #721c24; }
        
        .requirement-item {
            padding: 15px;
            margin: 8px 0;
            border-radius: 10px;
            transition: all 0.3s ease;
            animation: slideInLeft 0.5s ease-out;
        }
        
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .requirement-item:hover {
            transform: translateX(5px);
        }
        
        .requirement-ok {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .requirement-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            transform: translateY(-2px);
        }
        
        .btn {
            border-radius: 10px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255,255,255,0.3);
            border-radius: 50%;
            transition: all 0.6s ease;
            transform: translate(-50%, -50%);
        }
        
        .btn:hover::before {
            width: 200px;
            height: 200px;
        }
        
        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .success-animation {
            animation: bounceIn 0.8s ease-out;
        }
        
        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(0.3); }
            50% { opacity: 1; transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { opacity: 1; transform: scale(1); }
        }
        
        .error-shake {
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="install-container">
            <div class="install-header">
                <h1><i class="fas fa-cog me-2"></i>Установка AdBoard Pro</h1>
                <p class="mb-0">Майстер установки рекламної платформи</p>
            </div>
            
            <div class="step-indicator">
                <div class="step <?php echo $step >= 1 ? ($step > 1 ? 'completed' : 'active') : ''; ?>">1</div>
                <div class="step <?php echo $step >= 2 ? ($step > 2 ? 'completed' : 'active') : ''; ?>">2</div>
                <div class="step <?php echo $step >= 3 ? ($step > 3 ? 'completed' : 'active') : ''; ?>">3</div>
                <div class="step <?php echo $step >= 4 ? ($step > 4 ? 'completed' : 'active') : ''; ?>">4</div>
                <div class="step <?php echo $step >= 5 ? 'active' : ''; ?>">5</div>
            </div>
            
            <div class="p-4">
                <?php if ($error): ?>
                    <div class="alert alert-danger error-shake">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success success-animation">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Progress Container -->
                <div class="progress-container" id="progressContainer">
                    <h6><i class="fas fa-cogs me-2"></i>Прогрес установки</h6>
                    <div class="progress-bar-custom">
                        <div class="progress-fill" id="progressFill"></div>
                    </div>
                    <small class="text-muted" id="progressText">Очікування...</small>
                </div>
                
                <!-- Installation Log -->
                <div class="log-container" id="logContainer">
                    <h6><i class="fas fa-list me-2"></i>Лог установки</h6>
                    <div id="logEntries">
                        <?php if (isset($_SESSION['install_log'])): ?>
                            <?php foreach ($_SESSION['install_log'] as $log): ?>
                                <div class="log-entry log-<?php echo $log['status']; ?>">
                                    <span class="text-muted"><?php echo $log['time']; ?></span> 
                                    [<?php echo strtoupper($log['step']); ?>] 
                                    <?php echo htmlspecialchars($log['message']); ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php switch ($step): 
                    case 1: ?>
                        <h3><i class="fas fa-clipboard-check me-2"></i>Перевірка системних вимог</h3>
                        <p class="text-muted">Перевіряємо чи ваш сервер відповідає мінімальним вимогам для роботи AdBoard Pro.</p>
                        
                        <?php 
                        $requirements = checkRequirements();
                        $all_ok = true;
                        foreach ($requirements as $requirement => $status):
                            if (!$status) $all_ok = false;
                        ?>
                            <div class="requirement-item <?php echo $status ? 'requirement-ok' : 'requirement-error'; ?>">
                                <i class="fas <?php echo $status ? 'fa-check' : 'fa-times'; ?> me-2"></i>
                                <?php echo $requirement; ?>
                            </div>
                        <?php endforeach; ?>
                        
                        <?php if ($all_ok): ?>
                            <form method="POST" class="mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-arrow-right me-2"></i>Продовжити
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-warning mt-4">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Будь ласка, виправте всі помилки перед продовженням установки.
                            </div>
                        <?php endif; ?>
                        
                    <?php break; case 2: ?>
                        <h3><i class="fas fa-database me-2"></i>Налаштування бази даних</h3>
                        <p class="text-muted">Введіть параметри підключення до MySQL бази даних.</p>
                        
                        <form method="POST" class="mt-4">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="db_host" class="form-label">Хост бази даних</label>
                                    <input type="text" class="form-control" id="db_host" name="db_host" 
                                           value="<?php echo $_POST['db_host'] ?? 'localhost'; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="db_name" class="form-label">Назва бази даних</label>
                                    <input type="text" class="form-control" id="db_name" name="db_name" 
                                           value="<?php echo $_POST['db_name'] ?? 'adboard_site'; ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="db_user" class="form-label">Користувач</label>
                                    <input type="text" class="form-control" id="db_user" name="db_user" 
                                           value="<?php echo $_POST['db_user'] ?? 'root'; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="db_pass" class="form-label">Пароль</label>
                                    <input type="password" class="form-control" id="db_pass" name="db_pass" 
                                           value="<?php echo $_POST['db_pass'] ?? ''; ?>">
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-database me-2"></i>Створити базу даних
                            </button>
                        </form>
                        
                    <?php break; case 3: ?>
                        <h3><i class="fas fa-user-shield me-2"></i>Створення адміністратора</h3>
                        <p class="text-muted">Створіть обліковий запис адміністратора для управління сайтом.</p>
                        
                        <form method="POST" class="mt-4">
                            <div class="mb-3">
                                <label for="username" class="form-label">Ім'я користувача</label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo $_POST['username'] ?? ''; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo $_POST['email'] ?? ''; ?>" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label">Пароль</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="confirm_password" class="form-label">Підтвердження пароля</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Створити адміністратора
                            </button>
                        </form>
                        
                    <?php break; case 4: ?>
                        <h3><i class="fas fa-cogs me-2"></i>Завершення установки</h3>
                        <p class="text-muted">Створюємо файли конфігурації та завершуємо установку.</p>
                        
                        <form method="POST" class="mt-4">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Натисніть кнопку нижче для завершення установки. Буде створено файл конфігурації 
                                та необхідні директорії.
                            </div>
                            
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-check me-2"></i>Завершити установку
                            </button>
                        </form>
                        
                    <?php break; case 5: ?>
                        <div class="text-center">
                            <h3 class="text-success"><i class="fas fa-check-circle me-2"></i>Установка завершена!</h3>
                            <p class="lead">AdBoard Pro успішно встановлено на вашому сервері.</p>
                            
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Важливо:</strong> Видаліть файл <code>install.php</code> з сервера з міркувань безпеки.
                            </div>
                            
                            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                <a href="index.php" class="btn btn-primary btn-lg">
                                    <i class="fas fa-home me-2"></i>Перейти на сайт
                                </a>
                                <a href="admin/login.php" class="btn btn-outline-primary btn-lg">
                                    <i class="fas fa-cog me-2"></i>Адмін-панель
                                </a>
                            </div>
                        </div>
                <?php endswitch; ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Show progress and log containers if there are logs
            <?php if (isset($_SESSION['install_log']) && !empty($_SESSION['install_log'])): ?>
                $('#progressContainer, #logContainer').show();
                updateProgress();
            <?php endif; ?>
            
            // Animate requirement items
            $('.requirement-item').each(function(i) {
                $(this).css('animation-delay', (i * 0.1) + 's');
            });
            
            // Form submission with progress
            $('form').on('submit', function(e) {
                const $form = $(this);
                const $btn = $form.find('button[type="submit"]');
                const originalText = $btn.html();
                
                // Show loading state
                $btn.prop('disabled', true).html('<span class="spinner"></span> Обробка...');
                
                // Show progress container
                $('#progressContainer').show();
                animateProgress(0, 30, 1000);
                
                // Allow form to submit normally after animation
                setTimeout(() => {
                    $btn.prop('disabled', false).html(originalText);
                }, 1000);
            });
            
            // Auto-scroll log to bottom
            function scrollLogToBottom() {
                const logContainer = document.getElementById('logEntries');
                if (logContainer) {
                    logContainer.scrollTop = logContainer.scrollHeight;
                }
            }
            
            // Update progress based on current step
            function updateProgress() {
                const currentStep = <?php echo $step; ?>;
                const progress = (currentStep - 1) * 20; // 20% per step
                animateProgress(0, progress, 1000);
                
                // Update progress text
                const progressTexts = {
                    1: 'Перевірка системних вимог...',
                    2: 'Налаштування бази даних...',
                    3: 'Створення адміністратора...',
                    4: 'Створення конфігурації...',
                    5: 'Установка завершена!'
                };
                
                $('#progressText').text(progressTexts[currentStep] || 'Обробка...');
                scrollLogToBottom();
            }
            
            // Animate progress bar
            function animateProgress(from, to, duration) {
                const $progressFill = $('#progressFill');
                let current = from;
                const increment = (to - from) / (duration / 50);
                
                const interval = setInterval(() => {
                    current += increment;
                    if (current >= to) {
                        current = to;
                        clearInterval(interval);
                    }
                    $progressFill.css('width', current + '%');
                }, 50);
            }
            
            // Add loading effect to buttons
            $('.btn').on('click', function() {
                const $btn = $(this);
                if (!$btn.hasClass('btn-outline-secondary')) {
                    setTimeout(() => {
                        $btn.addClass('loading');
                    }, 100);
                }
            });
            
            // Animate form elements on focus
            $('.form-control').on('focus', function() {
                $(this).closest('.mb-3').addClass('animate__animated animate__pulse');
            }).on('blur', function() {
                $(this).closest('.mb-3').removeClass('animate__animated animate__pulse');
            });
            
            // Add ripple effect to buttons
            $('.btn').on('click', function(e) {
                const $btn = $(this);
                const offset = $btn.offset();
                const xPos = e.pageX - offset.left;
                const yPos = e.pageY - offset.top;
                
                const $ripple = $('<span class="ripple"></span>');
                $ripple.css({
                    position: 'absolute',
                    top: yPos + 'px',
                    left: xPos + 'px',
                    width: '0',
                    height: '0',
                    borderRadius: '50%',
                    background: 'rgba(255,255,255,0.5)',
                    transform: 'translate(-50%, -50%)',
                    animation: 'ripple-animation 0.6s ease-out'
                });
                
                $btn.append($ripple);
                
                setTimeout(() => {
                    $ripple.remove();
                }, 600);
            });
            
            // Add CSS for ripple animation
            $('<style>')
                .prop('type', 'text/css')
                .html(`
                    @keyframes ripple-animation {
                        to {
                            width: 100px;
                            height: 100px;
                            opacity: 0;
                        }
                    }
                    .loading {
                        pointer-events: none;
                        opacity: 0.7;
                    }
                `)
                .appendTo('head');
            
            // Validate form inputs in real-time
            $('.form-control').on('input', function() {
                const $input = $(this);
                const value = $input.val().trim();
                
                // Database name validation
                if ($input.attr('name') === 'db_name') {
                    if (!/^[a-zA-Z0-9_]+$/.test(value) && value !== '') {
                        $input.addClass('is-invalid');
                        if (!$input.siblings('.invalid-feedback').length) {
                            $input.after('<div class="invalid-feedback">Тільки літери, цифри та підкреслення</div>');
                        }
                    } else {
                        $input.removeClass('is-invalid').siblings('.invalid-feedback').remove();
                    }
                }
                
                // Email validation
                if ($input.attr('type') === 'email') {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(value) && value !== '') {
                        $input.addClass('is-invalid');
                        if (!$input.siblings('.invalid-feedback').length) {
                            $input.after('<div class="invalid-feedback">Невірний формат email</div>');
                        }
                    } else {
                        $input.removeClass('is-invalid').siblings('.invalid-feedback').remove();
                    }
                }
                
                // Password validation
                if ($input.attr('name') === 'password') {
                    if (value.length < 6 && value !== '') {
                        $input.addClass('is-invalid');
                        if (!$input.siblings('.invalid-feedback').length) {
                            $input.after('<div class="invalid-feedback">Мінімум 6 символів</div>');
                        }
                    } else {
                        $input.removeClass('is-invalid').siblings('.invalid-feedback').remove();
                    }
                }
                
                // Password confirmation
                if ($input.attr('name') === 'confirm_password') {
                    const password = $('input[name="password"]').val();
                    if (value !== password && value !== '') {
                        $input.addClass('is-invalid');
                        if (!$input.siblings('.invalid-feedback').length) {
                            $input.after('<div class="invalid-feedback">Паролі не співпадають</div>');
                        }
                    } else {
                        $input.removeClass('is-invalid').siblings('.invalid-feedback').remove();
                    }
                }
            });
            
            // Show/hide password
            $('<button type="button" class="btn btn-outline-secondary password-toggle" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border: none; background: none;"><i class="fas fa-eye"></i></button>')
                .insertAfter('input[type="password"]')
                .on('click', function() {
                    const $input = $(this).siblings('input[type="password"], input[type="text"]');
                    const $icon = $(this).find('i');
                    
                    if ($input.attr('type') === 'password') {
                        $input.attr('type', 'text');
                        $icon.removeClass('fa-eye').addClass('fa-eye-slash');
                    } else {
                        $input.attr('type', 'password');
                        $icon.removeClass('fa-eye-slash').addClass('fa-eye');
                    }
                });
            
            // Position password toggle buttons
            $('input[type="password"]').closest('.mb-3').css('position', 'relative');
            
            // Auto-focus first input
            $('.form-control:first').focus();
            
            // Add tooltips to requirement items
            $('.requirement-item').each(function() {
                const $item = $(this);
                const isOk = $item.hasClass('requirement-ok');
                
                if (!isOk) {
                    $item.attr('title', 'Ця вимога не виконана. Будь ласка, налаштуйте ваш сервер відповідно.');
                } else {
                    $item.attr('title', 'Ця вимога виконана успішно.');
                }
            });
        });
        
        // Global error handler
        window.addEventListener('error', function(e) {
            console.error('Installation Error:', e.error);
        });
    </script>
</body>
</html>
