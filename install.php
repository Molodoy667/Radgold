<?php
// Файл установки AdBoard Pro
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Якщо файл конфігурації існує і база вже налаштована, перенаправляємо
if (file_exists('core/config.php')) {
    require_once 'core/config.php';
    if (defined('DB_NAME')) {
        try {
            $test_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if ($test_connection->connect_error) {
                throw new Exception('Connection failed');
            }
            $test_connection->close();
            header('Location: index.php');
            exit();
        } catch (Exception $e) {
            // Продовжуємо установку якщо не можемо підключитися
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
            $db_host = $_POST['db_host'] ?? 'localhost';
            $db_user = $_POST['db_user'] ?? 'root';
            $db_pass = $_POST['db_pass'] ?? '';
            $db_name = $_POST['db_name'] ?? 'adboard_site';
            
            try {
                // Тестуємо підключення
                $connection = new mysqli($db_host, $db_user, $db_pass);
                if ($connection->connect_error) {
                    throw new Exception('Не вдалося підключитися до MySQL: ' . $connection->connect_error);
                }
                
                // Створюємо базу даних якщо не існує
                $connection->query("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $connection->select_db($db_name);
                
                // Імпортуємо SQL
                $sql_content = file_get_contents('database.sql');
                
                // Замінюємо назву бази даних
                $sql_content = str_replace('adboard_site', $db_name, $sql_content);
                
                // Виконуємо SQL запити
                $queries = explode(';', $sql_content);
                foreach ($queries as $query) {
                    $query = trim($query);
                    if (!empty($query)) {
                        if (!$connection->query($query)) {
                            throw new Exception('SQL Error: ' . $connection->error);
                        }
                    }
                }
                
                // Зберігаємо дані підключення в сесії
                session_start();
                $_SESSION['install'] = [
                    'db_host' => $db_host,
                    'db_user' => $db_user,
                    'db_pass' => $db_pass,
                    'db_name' => $db_name
                ];
                
                $connection->close();
                $step = 3;
                $success = 'База даних успішно створена!';
                
            } catch (Exception $e) {
                $error = $e->getMessage();
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
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .install-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            margin: 2rem auto;
            max-width: 800px;
        }
        
        .install-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 20px 20px 0 0;
            text-align: center;
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            margin: 2rem 0;
        }
        
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            color: #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            font-weight: bold;
        }
        
        .step.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .step.completed {
            background: #28a745;
            color: white;
        }
        
        .requirement-item {
            padding: 10px;
            margin: 5px 0;
            border-radius: 8px;
        }
        
        .requirement-ok {
            background: #d4edda;
            color: #155724;
        }
        
        .requirement-error {
            background: #f8d7da;
            color: #721c24;
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
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
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
</body>
</html>
