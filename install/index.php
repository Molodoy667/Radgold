<?php
session_start();

// Перевіряємо, чи не встановлено вже систему
if (file_exists('../config/installed.lock')) {
    header('Location: ../index.php');
    exit();
}

// Етапи встановлення
$steps = [
    1 => 'Ліцензійна угода',
    2 => 'Перевірка системи',
    3 => 'Налаштування бази даних',
    4 => 'Створення адміністратора'
];

$current_step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
if ($current_step < 1) $current_step = 1;
if ($current_step > 4) $current_step = 4;

// Обробка POST запитів
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($current_step) {
        case 1:
            if (isset($_POST['accept_license'])) {
                $_SESSION['license_accepted'] = true;
                header('Location: ?step=2');
                exit();
            }
            break;
            
        case 2:
            if (isset($_POST['requirements_ok'])) {
                $_SESSION['requirements_checked'] = true;
                header('Location: ?step=3');
                exit();
            }
            break;
            
        case 3:
            if (isset($_POST['test_db'])) {
                header('Content-Type: application/json; charset=utf-8');
                $result = testDatabaseConnection();
                if ($result['success']) {
                    $_SESSION['db_config'] = $_POST;
                    $_SESSION['db_tested'] = true;
                }
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
                exit();
            }
            if (isset($_POST['save_db']) && $_SESSION['db_tested']) {
                header('Location: ?step=4');
                exit();
            }
            break;
            
        case 4:
            if (isset($_POST['install'])) {
                header('Content-Type: application/json; charset=utf-8');
                $result = completeInstallation();
                echo json_encode($result, JSON_UNESCAPED_UNICODE);
                exit();
            }
            break;
    }
}

function testDatabaseConnection() {
    $host = $_POST['db_host'] ?? '';
    $database = $_POST['db_name'] ?? '';
    $username = $_POST['db_username'] ?? '';
    $password = $_POST['db_password'] ?? '';
    
    try {
        $pdo = new PDO("mysql:host=$host", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Перевіряємо чи існує база даних
        $stmt = $pdo->prepare("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?");
        $stmt->execute([$database]);
        
        if (!$stmt->fetch()) {
            // Створюємо базу даних якщо не існує
            $pdo->exec("CREATE DATABASE `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        }
        
        // Підключаємося до бази
        $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8mb4", $username, $password, array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ));
        
        return ['success' => true, 'message' => 'Підключення до бази даних успішне!'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Помилка підключення: ' . $e->getMessage()];
    }
}

function completeInstallation() {
    try {
        error_log("Installation started");
        
        // 1. Створюємо конфігурацію бази даних
        error_log("Creating database config");
        if (!createDatabaseConfig()) {
            error_log("Failed to create database config");
            throw new Exception('Не вдалося створити конфігурацію бази даних');
        }
        error_log("Database config created successfully");
        
        // 2. Імпортуємо SQL дамп
        error_log("Importing database");
        if (!importDatabase()) {
            error_log("Failed to import database");
            throw new Exception('Не вдалося імпортувати базу даних');
        }
        error_log("Database imported successfully");
        
        // 3. Оновлюємо налаштування сайту
        error_log("Updating site settings");
        if (!updateSiteSettings()) {
            error_log("Failed to update site settings");
            throw new Exception('Не вдалося оновити налаштування сайту');
        }
        error_log("Site settings updated successfully");
        
        // 4. Створюємо адміністратора
        error_log("Creating admin user");
        if (!createAdmin()) {
            error_log("Failed to create admin");
            throw new Exception('Не вдалося створити адміністратора');
        }
        error_log("Admin user created successfully");
        
        // 5. Створюємо файл блокування
        error_log("Creating lock file");
        if (!file_put_contents('../config/installed.lock', date('Y-m-d H:i:s'))) {
            error_log("Failed to create lock file");
            throw new Exception('Не вдалося створити файл блокування');
        }
        error_log("Lock file created successfully");
        
        error_log("Installation completed successfully");
        return ['success' => true, 'message' => 'Встановлення завершено успішно!', 'redirect' => '../admin/index.php'];
        
    } catch (Exception $e) {
        error_log("Installation failed: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function createDatabaseConfig() {
    $config = $_SESSION['db_config'];
    
    // Створюємо папку config якщо не існує
    if (!file_exists('../config')) {
        mkdir('../config', 0755, true);
    }
    
    $content = "<?php
// Конфігурація бази даних
class Database {
    private \$host = '{$config['db_host']}';
    private \$db_name = '{$config['db_name']}';
    private \$username = '{$config['db_username']}';
    private \$password = '{$config['db_password']}';
    private \$conn;

    // Підключення до бази даних
    public function getConnection() {
        \$this->conn = null;
        
        try {
            \$this->conn = new PDO(
                \"mysql:host=\" . \$this->host . \";dbname=\" . \$this->db_name . \";charset=utf8mb4\",
                \$this->username,
                \$this->password,
                array(
                    PDO::MYSQL_ATTR_INIT_COMMAND => \"SET NAMES utf8mb4\",
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                )
            );
        } catch(PDOException \$exception) {
            echo \"Помилка підключення: \" . \$exception->getMessage();
        }
        
        return \$this->conn;
    }
}
?>";
    
    return file_put_contents('../config/database.php', $content) !== false;
}

function updateSiteSettings() {
    $config = $_SESSION['db_config'];
    
    try {
        $pdo = new PDO(
            "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4", 
            $config['db_username'], 
            $config['db_password'],
            array(
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            )
        );
        
        // Отримуємо поточну URL сайту
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'];
        $current_url = $protocol . $host;
        
        // Оновлюємо URL сайту в налаштуваннях
        $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'site_url'");
        $stmt->execute([$current_url]);
        
        return true;
    } catch (Exception $e) {
        error_log("Settings update error: " . $e->getMessage());
        return false;
    }
}

function clearDatabase($pdo) {
    try {
        error_log("Starting database cleanup");
        
        // Отключаем проверку внешних ключей
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        
        // Получаем список всех таблиц в базе данных (только обычные таблицы, не системные)
        $stmt = $pdo->query("
            SELECT TABLE_NAME 
            FROM INFORMATION_SCHEMA.TABLES 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_TYPE = 'BASE TABLE'
        ");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (!empty($tables)) {
            error_log("Found " . count($tables) . " tables to drop: " . implode(', ', $tables));
            
            // Создаем резервную копию списка таблиц (на случай восстановления)
            $backup_info = [
                'timestamp' => date('Y-m-d H:i:s'),
                'tables' => $tables,
                'database' => $pdo->query("SELECT DATABASE()")->fetchColumn()
            ];
            error_log("Backup info: " . json_encode($backup_info));
            
            // Удаляем все таблицы
            foreach ($tables as $table) {
                try {
                    $pdo->exec("DROP TABLE IF EXISTS `$table`");
                    error_log("Successfully dropped table: $table");
                } catch (Exception $e) {
                    error_log("Failed to drop table $table: " . $e->getMessage());
                    // Продолжаем с другими таблицами
                }
            }
            
            error_log("Database cleanup completed");
        } else {
            error_log("No user tables found in database");
        }
        
        // Включаем обратно проверку внешних ключей
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        
        // Дополнительная проверка - убеждаемся что таблицы действительно удалены
        $stmt = $pdo->query("SHOW TABLES");
        $remaining_tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (!empty($remaining_tables)) {
            error_log("Warning: Some tables still exist: " . implode(', ', $remaining_tables));
        } else {
            error_log("Database completely cleaned");
        }
        
        return true;
    } catch (Exception $e) {
        error_log("Database cleanup error: " . $e->getMessage());
        error_log("Error details: " . $e->getTraceAsString());
        
        // Включаем обратно проверку внешних ключей даже в случае ошибки
        try {
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        } catch (Exception $ignored) {
            error_log("Failed to restore FOREIGN_KEY_CHECKS");
        }
        
        return false;
    }
}

function importDatabase() {
    $config = $_SESSION['db_config'];
    
    try {
        $pdo = new PDO(
            "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4", 
            $config['db_username'], 
            $config['db_password'],
            array(
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            )
        );
        
        // Проверяем есть ли таблицы в базе данных
        $stmt = $pdo->query("SHOW TABLES");
        $existing_tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (!empty($existing_tables)) {
            error_log("Found existing tables in database: " . implode(', ', $existing_tables));
            error_log("Clearing database before importing new structure");
            
            if (!clearDatabase($pdo)) {
                throw new Exception('Не вдалося очистити існуючі таблиці');
            }
            
            error_log("Database cleared successfully");
        } else {
            error_log("Database is empty, proceeding with import");
        }
        
        $sql = file_get_contents('baza.sql');
        if ($sql === false) {
            throw new Exception('Не вдалося прочитати файл baza.sql');
        }
        
        error_log("Starting SQL import from baza.sql");
        
        // Розбиваємо SQL на окремі запити
        $queries = explode(';', $sql);
        $executed_queries = 0;
        
        foreach ($queries as $query) {
            $query = trim($query);
            if (!empty($query)) {
                $pdo->exec($query);
                $executed_queries++;
            }
        }
        
        error_log("Successfully executed $executed_queries SQL queries");
        
        return true;
    } catch (Exception $e) {
        error_log("Database import error: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        return false;
    }
}

function createAdmin() {
    $admin_username = $_POST['admin_username'] ?? '';
    $admin_email = $_POST['admin_email'] ?? '';
    $admin_password = $_POST['admin_password'] ?? '';
    
    if (empty($admin_username) || empty($admin_email) || empty($admin_password)) {
        throw new Exception('Всі поля адміністратора обов\'язкові');
    }
    
    $config = $_SESSION['db_config'];
    
    try {
        $pdo = new PDO(
            "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8mb4", 
            $config['db_username'], 
            $config['db_password'],
            array(
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            )
        );
        
        $hashed_password = password_hash($admin_password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, is_admin, is_active) VALUES (?, ?, ?, 1, 1)");
        $stmt->execute([$admin_username, $admin_email, $hashed_password]);
        
        return true;
    } catch (Exception $e) {
        error_log("Admin creation error: " . $e->getMessage());
        return false;
    }
}

?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Встановлення - Дошка Оголошень</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .install-container {
            max-width: 800px;
            margin: 2rem auto;
        }
        .install-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .install-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
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
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 10px;
            background: #e9ecef;
            color: #6c757d;
            font-weight: bold;
            position: relative;
        }
        .step.active {
            background: #0d6efd;
            color: white;
        }
        .step.completed {
            background: #198754;
            color: white;
        }
        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 100%;
            width: 20px;
            height: 2px;
            background: #e9ecef;
            transform: translateY(-50%);
        }
        .step.completed:not(:last-child)::after {
            background: #198754;
        }
        .requirement {
            padding: 0.5rem;
            margin: 0.25rem 0;
            border-radius: 5px;
        }
        .requirement.success {
            background: #d1e7dd;
            color: #0f5132;
        }
        .requirement.error {
            background: #f8d7da;
            color: #842029;
        }
        .requirement.warning {
            background: #fff3cd;
            color: #664d03;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="install-container">
            <div class="install-card">
                <div class="install-header">
                    <h1><i class="fas fa-bullhorn me-2"></i>Дошка Оголошень</h1>
                    <p class="mb-0">Майстер встановлення</p>
                </div>
                
                <div class="step-indicator">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <div class="step <?php 
                            if ($i < $current_step) echo 'completed';
                            elseif ($i == $current_step) echo 'active';
                        ?>">
                            <?php if ($i < $current_step): ?>
                                <i class="fas fa-check"></i>
                            <?php else: ?>
                                <?php echo $i; ?>
                            <?php endif; ?>
                        </div>
                    <?php endfor; ?>
                </div>
                
                <div class="p-4">
                    <h3 class="mb-4">Крок <?php echo $current_step; ?>: <?php echo $steps[$current_step]; ?></h3>
                    
                    <?php include "step{$current_step}.php"; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="install.js"></script>
</body>
</html>