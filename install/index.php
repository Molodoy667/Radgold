<?php
// Покращений інсталятор AdBoard Pro
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(600);

session_start();

// Функція для логування
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

// Перевірка чи сайт вже встановлений
function isInstalled() {
    return file_exists('../core/config.php') && file_exists('../.installed');
}

if (isInstalled()) {
    header('Location: ../index.php');
    exit();
}

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$error = '';
$success = '';
$maxSteps = 8;

// Ініціалізація даних установки
if (!isset($_SESSION['install_data'])) {
    $_SESSION['install_data'] = [
        'license_accepted' => false,
        'system_check' => false,
        'db_config' => [],
        'admin_config' => [],
        'site_config' => [],
        'theme_config' => []
    ];
}

// Обробка POST запитів
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($step) {
        case 1:
            if (isset($_POST['accept_license']) && $_POST['accept_license'] === '1') {
                $_SESSION['install_data']['license_accepted'] = true;
                logInstallStep('license', 'Ліцензію прийнято', 'success');
                header('Location: ?step=2');
                exit();
            } else {
                $error = 'Необхідно прийняти ліцензійну угоду';
            }
            break;

        case 2:
            $_SESSION['install_data']['system_check'] = true;
            logInstallStep('system', 'Системні вимоги перевірено', 'success');
            header('Location: ?step=3');
            exit();
            break;

        case 3:
            // Тест підключення до БД
            if (isset($_POST['test_connection'])) {
                testDatabaseConnection();
            } else {
                // Збереження конфігурації БД
                $db_host = trim($_POST['db_host'] ?? 'localhost');
                $db_user = trim($_POST['db_user'] ?? 'root');
                $db_pass = $_POST['db_pass'] ?? '';
                $db_name = trim($_POST['db_name'] ?? 'adboard_site');
                
                if (empty($db_host) || empty($db_user) || empty($db_name)) {
                    $error = 'Заповніть всі обов\'язкові поля';
                    break;
                }
                
                $_SESSION['install_data']['db_config'] = [
                    'host' => $db_host,
                    'user' => $db_user,
                    'pass' => $db_pass,
                    'name' => $db_name
                ];
                
                logInstallStep('database', 'Конфігурація БД збережена', 'success');
                header('Location: ?step=4');
                exit();
            }
            break;

        case 4:
            $site_name = trim($_POST['site_name'] ?? 'AdBoard Pro');
            $site_description = trim($_POST['site_description'] ?? '');
            $site_keywords = trim($_POST['site_keywords'] ?? '');
            $site_url = trim($_POST['site_url'] ?? 'http://' . $_SERVER['HTTP_HOST']);
            
            $_SESSION['install_data']['site_config'] = [
                'name' => $site_name,
                'description' => $site_description,
                'keywords' => $site_keywords,
                'url' => rtrim($site_url, '/')
            ];
            
            logInstallStep('site_config', 'Налаштування сайту збережено', 'success');
            header('Location: ?step=5');
            exit();
            break;

        case 5:
            $theme = $_POST['theme'] ?? 'light';
            $gradient = $_POST['gradient'] ?? 'gradient-1';
            
            $_SESSION['install_data']['theme_config'] = [
                'theme' => $theme,
                'gradient' => $gradient
            ];
            
            logInstallStep('theme', 'Налаштування теми збережено', 'success');
            header('Location: ?step=6');
            exit();
            break;

        case 6:
            $admin_username = trim($_POST['admin_username'] ?? '');
            $admin_email = trim($_POST['admin_email'] ?? '');
            $admin_password = $_POST['admin_password'] ?? '';
            $admin_password_confirm = $_POST['admin_password_confirm'] ?? '';
            
            if (empty($admin_username) || empty($admin_email) || empty($admin_password)) {
                $error = 'Заповніть всі поля';
                break;
            }
            
            if (!filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Невірний формат email';
                break;
            }
            
            if (strlen($admin_password) < 6) {
                $error = 'Пароль повинен містити мінімум 6 символів';
                break;
            }
            
            if ($admin_password !== $admin_password_confirm) {
                $error = 'Паролі не співпадають';
                break;
            }
            
            $_SESSION['install_data']['admin_config'] = [
                'username' => $admin_username,
                'email' => $admin_email,
                'password' => $admin_password
            ];
            
            logInstallStep('admin', 'Дані адміністратора збережено', 'success');
            header('Location: ?step=7');
            exit();
            break;

        case 7:
            try {
                installSite();
                header('Location: ?step=8');
                exit();
            } catch (Exception $e) {
                $error = 'Помилка установки: ' . $e->getMessage();
                logInstallStep('install', 'Помилка: ' . $error, 'error');
            }
            break;
    }
}

function testDatabaseConnection() {
    global $success, $error;
    
    $host = $_POST['db_host'] ?? '';
    $user = $_POST['db_user'] ?? '';
    $pass = $_POST['db_pass'] ?? '';
    
    try {
        $connection = new mysqli($host, $user, $pass);
        if ($connection->connect_error) {
            throw new Exception('Помилка підключення: ' . $connection->connect_error);
        }
        
        $success = 'Підключення до бази даних успішне!';
        $connection->close();
        logInstallStep('db_test', 'Тест підключення до БД пройшов успішно', 'success');
    } catch (Exception $e) {
        $error = $e->getMessage();
        logInstallStep('db_test', 'Помилка тесту БД: ' . $error, 'error');
    }
}

function installSite() {
    $installData = $_SESSION['install_data'];
    
    logInstallStep('install', 'Початок установки сайту...', 'info');
    
    // Створення директорій
    $directories = [
        '../images/uploads',
        '../images/thumbs', 
        '../images/avatars',
        '../logs'
    ];
    
    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                throw new Exception("Не вдалося створити директорію: $dir");
            }
            logInstallStep('install', "Створено директорію: $dir", 'success');
        }
    }
    
    // Створення бази даних
    logInstallStep('install', 'Створюємо базу даних...', 'info');
    $dbConfig = $installData['db_config'];
    $connection = new mysqli($dbConfig['host'], $dbConfig['user'], $dbConfig['pass']);
    
    if ($connection->connect_error) {
        throw new Exception('Помилка підключення до БД');
    }
    
    $dbName = $dbConfig['name'];
    if (!$connection->query("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci")) {
        throw new Exception('Помилка створення БД');
    }
    
    $connection->select_db($dbName);
    logInstallStep('install', 'База даних створена', 'success');
    
    // Імпорт SQL
    logInstallStep('install', 'Імпортуємо структуру БД...', 'info');
    $sqlContent = file_get_contents('database.sql');
    $sqlContent = str_replace('adboard_site', $dbName, $sqlContent);
    
    $queries = explode(';', $sqlContent);
    $executed = 0;
    
    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query) && !preg_match('/^--|^\/\*/', $query)) {
            if (!$connection->query($query)) {
                throw new Exception('SQL помилка: ' . $connection->error);
            }
            $executed++;
        }
    }
    
    logInstallStep('install', "Виконано $executed SQL запитів", 'success');
    
    // Налаштування сайту
    $siteConfig = $installData['site_config'];
    $themeConfig = $installData['theme_config'];
    
    $stmt = $connection->prepare(
        "UPDATE site_settings SET site_title = ?, site_description = ?, site_keywords = ? WHERE id = 1"
    );
    $stmt->bind_param('sss', $siteConfig['name'], $siteConfig['description'], $siteConfig['keywords']);
    $stmt->execute();
    
    $stmt = $connection->prepare(
        "UPDATE theme_settings SET current_theme = ?, current_gradient = ? WHERE id = 1"
    );
    $stmt->bind_param('ss', $themeConfig['theme'], $themeConfig['gradient']);
    $stmt->execute();
    
    // Створення адміністратора
    logInstallStep('install', 'Створюємо адміністратора...', 'info');
    
    $adminConfig = $installData['admin_config'];
    $hashedPassword = password_hash($adminConfig['password'], PASSWORD_DEFAULT);
    
    $connection->query("DELETE FROM users WHERE email = 'admin@adboardpro.com'");
    
    $stmt = $connection->prepare(
        "INSERT INTO users (username, email, password, role, status, email_verified) VALUES (?, ?, ?, 'admin', 'active', 1)"
    );
    $stmt->bind_param('sss', $adminConfig['username'], $adminConfig['email'], $hashedPassword);
    
    if (!$stmt->execute()) {
        throw new Exception('Помилка створення адміністратора');
    }
    
    logInstallStep('install', 'Адміністратор створений', 'success');
    
    // Створення конфігурації
    logInstallStep('install', 'Створюємо конфігурацію...', 'info');
    
    $configContent = "<?php
define('DB_HOST', '{$dbConfig['host']}');
define('DB_USER', '{$dbConfig['user']}');
define('DB_PASS', '{$dbConfig['pass']}');
define('DB_NAME', '{$dbConfig['name']}');

define('SITE_URL', '{$siteConfig['url']}');
define('SITE_NAME', '{$siteConfig['name']}');
define('SITE_DESCRIPTION', '{$siteConfig['description']}');
define('SITE_KEYWORDS', '{$siteConfig['keywords']}');

define('SECRET_KEY', '" . bin2hex(random_bytes(32)) . "');
define('SESSION_NAME', 'adboard_session');
define('UPLOAD_PATH', 'images/uploads/');
define('MAX_FILE_SIZE', 5242880);
define('ITEMS_PER_PAGE', 12);
define('DEBUG_MODE', false);
define('LOG_ERRORS', true);
define('LOG_FILE', 'logs/error.log');

spl_autoload_register(function (\$class) {
    \$file = __DIR__ . '/classes/' . \$class . '.php';
    if (file_exists(\$file)) {
        require_once \$file;
    }
});

if (session_status() == PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

if (!file_exists(__DIR__ . '/.installed')) {
    if (basename(\$_SERVER['PHP_SELF']) !== 'index.php' || strpos(\$_SERVER['REQUEST_URI'], '/install/') === false) {
        header('Location: install/');
        exit();
    }
}
?>";
    
    if (!file_put_contents('../core/config.php', $configContent)) {
        throw new Exception('Не вдалося створити файл конфігурації');
    }
    
    file_put_contents('../.installed', date('Y-m-d H:i:s'));
    
    logInstallStep('install', 'Установка завершена успішно!', 'success');
    $connection->close();
}

// Допоміжні функції
function checkSystemRequirements() {
    return [
        'PHP версія >= 7.4' => version_compare(PHP_VERSION, '7.4.0', '>='),
        'MySQLi extension' => extension_loaded('mysqli'),
        'JSON extension' => extension_loaded('json'), 
        'GD extension' => extension_loaded('gd'),
        'cURL extension' => extension_loaded('curl'),
        'Session support' => function_exists('session_start'),
        'File uploads enabled' => ini_get('file_uploads'),
        'Можливість запису в core/' => is_writable('../core/') || @mkdir('../core/', 0755, true),
        'Можливість запису в images/' => is_writable('../images/') || @mkdir('../images/', 0755, true),
        'Можливість запису в корень' => is_writable('../'),
    ];
}

function checkFiles() {
    $requiredFiles = [
        '../index.php',
        '../.htaccess', 
        '../core/database.php',
        '../core/functions.php',
        '../themes/header.php',
        '../themes/footer.php',
        '../themes/style.css',
        '../themes/script.js',
        '../pages/home.php',
        '../pages/login.php',
        '../ajax/change_theme.php',
        '../admin/dashboard.php'
    ];
    
    $missingFiles = [];
    foreach ($requiredFiles as $file) {
        if (!file_exists($file)) {
            $missingFiles[] = $file;
        }
    }
    
    return $missingFiles;
}

function generateGradients() {
    return [
        'gradient-1' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        'gradient-2' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
        'gradient-3' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
        'gradient-4' => 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
        'gradient-5' => 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
        'gradient-6' => 'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
        'gradient-7' => 'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)',
        'gradient-8' => 'linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%)',
        'gradient-9' => 'linear-gradient(135deg, #ff8a80 0%, #ea80fc 100%)',
        'gradient-10' => 'linear-gradient(135deg, #8fd3f4 0%, #84fab0 100%)',
        'gradient-11' => 'linear-gradient(135deg, #d299c2 0%, #fef9d7 100%)',
        'gradient-12' => 'linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%)',
        'gradient-13' => 'linear-gradient(135deg, #fdbb2d 0%, #22c1c3 100%)',
        'gradient-14' => 'linear-gradient(135deg, #e0c3fc 0%, #9bb5ff 100%)',
        'gradient-15' => 'linear-gradient(135deg, #ffeef8 0%, #f8e1ff 100%)',
        'gradient-16' => 'linear-gradient(135deg, #ffd89b 0%, #19547b 100%)',
        'gradient-17' => 'linear-gradient(135deg, #a7c0cd 0%, #f7f0ac 100%)',
        'gradient-18' => 'linear-gradient(135deg, #96fbc4 0%, #f9f047 100%)',
        'gradient-19' => 'linear-gradient(135deg, #ffeaa7 0%, #fab1a0 100%)',
        'gradient-20' => 'linear-gradient(135deg, #74b9ff 0%, #0984e3 100%)',
        'gradient-21' => 'linear-gradient(135deg, #fd79a8 0%, #fdcb6e 100%)',
        'gradient-22' => 'linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%)',
        'gradient-23' => 'linear-gradient(135deg, #55efc4 0%, #81ecec 100%)',
        'gradient-24' => 'linear-gradient(135deg, #ff7675 0%, #fd79a8 100%)',
        'gradient-25' => 'linear-gradient(135deg, #fdcb6e 0%, #e17055 100%)',
        'gradient-26' => 'linear-gradient(135deg, #00b894 0%, #00cec9 100%)',
        'gradient-27' => 'linear-gradient(135deg, #6c5ce7 0%, #74b9ff 100%)',
        'gradient-28' => 'linear-gradient(135deg, #fd79a8 0%, #fdcb6e 100%)',
        'gradient-29' => 'linear-gradient(135deg, #e17055 0%, #fab1a0 100%)',
        'gradient-30' => 'linear-gradient(135deg, #00cec9 0%, #55efc4 100%)'
    ];
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="install.css">
</head>
<body>
    <div class="container">
        <div class="install-container">
            <div class="install-header">
                <h1><i class="fas fa-cog me-3"></i>Установка AdBoard Pro</h1>
                <p class="mb-0">Майстер установки рекламної платформи</p>
            </div>
            
            <!-- Navigation Steps -->
            <div class="step-nav">
                <?php for ($i = 1; $i <= $maxSteps; $i++): ?>
                    <div class="step-item">
                        <div class="step-circle <?php echo $i < $step ? 'completed' : ($i == $step ? 'active' : ''); ?>">
                            <?php if ($i < $step): ?>
                                <i class="fas fa-check"></i>
                            <?php else: ?>
                                <?php echo $i; ?>
                            <?php endif; ?>
                        </div>
                        <div class="step-label">
                            <?php
                            $labels = [
                                1 => 'Ліцензія',
                                2 => 'Перевірка',
                                3 => 'База даних',
                                4 => 'Сайт',
                                5 => 'Тема',
                                6 => 'Адмін',
                                7 => 'Установка',
                                8 => 'Завершення'
                            ];
                            echo $labels[$i] ?? '';
                            ?>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
            
            <div class="install-content">
                <?php if ($error): ?>
                    <div class="alert alert-danger animate__animated animate__shakeX">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success animate__animated animate__bounceIn">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <?php
                // Відображення кроків
                switch ($step) {
                    case 1:
                        include 'steps/step_1.php';
                        break;
                    case 2:
                        include 'steps/step_2.php';
                        break;
                    case 3:
                        include 'steps/step_3.php';
                        break;
                    case 4:
                        include 'steps/step_4.php';
                        break;
                    case 5:
                        include 'steps/step_5.php';
                        break;
                    case 6:
                        include 'steps/step_6.php';
                        break;
                    case 7:
                        include 'steps/step_7.php';
                        break;
                    case 8:
                        include 'steps/step_8.php';
                        break;
                    default:
                        echo '<p>Невірний крок установки</p>';
                }
                ?>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="install.js"></script>
</body>
</html>
