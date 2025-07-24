<?php
/**
 * Файл для отладки установки AdBoard Pro
 * Запустите этот файл перед установкой для проверки готовности системы
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html lang='uk'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Диагностика установки AdBoard Pro</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>";
echo "</head>";
echo "<body class='bg-light'>";

echo "<div class='container my-5'>";
echo "<div class='row justify-content-center'>";
echo "<div class='col-lg-10'>";

echo "<div class='card shadow'>";
echo "<div class='card-header bg-primary text-white'>";
echo "<h1 class='mb-0'><i class='fas fa-tools me-2'></i>Диагностика установки AdBoard Pro</h1>";
echo "</div>";
echo "<div class='card-body'>";

// Перевірка PHP версії
echo "<div class='row mb-4'>";
echo "<div class='col-12'>";
echo "<h3><i class='fab fa-php me-2'></i>PHP Конфігурація</h3>";
echo "<div class='table-responsive'>";
echo "<table class='table table-striped'>";

$phpVersion = PHP_VERSION;
$requiredVersion = '7.4.0';
$phpOk = version_compare($phpVersion, $requiredVersion, '>=');

echo "<tr>";
echo "<td><strong>PHP версія</strong></td>";
echo "<td>" . $phpVersion . "</td>";
echo "<td>";
if ($phpOk) {
    echo "<span class='badge bg-success'><i class='fas fa-check'></i> OK</span>";
} else {
    echo "<span class='badge bg-danger'><i class='fas fa-times'></i> Потрібно $requiredVersion+</span>";
}
echo "</td>";
echo "</tr>";

// Перевірка розширень PHP
$requiredExtensions = [
    'mysqli' => 'MySQL підключення',
    'pdo' => 'PDO бази даних',
    'json' => 'JSON обробка',
    'mbstring' => 'Багатобайтові рядки',
    'fileinfo' => 'Інформація про файли',
    'gd' => 'Обробка зображень',
    'curl' => 'HTTP запити',
    'openssl' => 'SSL/TLS',
    'session' => 'Сесії'
];

foreach ($requiredExtensions as $ext => $desc) {
    echo "<tr>";
    echo "<td><strong>$desc ($ext)</strong></td>";
    $loaded = extension_loaded($ext);
    echo "<td>" . ($loaded ? 'Завантажено' : 'Відсутнє') . "</td>";
    echo "<td>";
    if ($loaded) {
        echo "<span class='badge bg-success'><i class='fas fa-check'></i> OK</span>";
    } else {
        echo "<span class='badge bg-danger'><i class='fas fa-times'></i> Відсутнє</span>";
    }
    echo "</td>";
    echo "</tr>";
}

echo "</table>";
echo "</div>";
echo "</div>";
echo "</div>";

// Перевірка параметрів PHP
echo "<div class='row mb-4'>";
echo "<div class='col-12'>";
echo "<h3><i class='fas fa-cogs me-2'></i>PHP Налаштування</h3>";
echo "<div class='table-responsive'>";
echo "<table class='table table-striped'>";

$phpSettings = [
    'memory_limit' => ['required' => '128M', 'current' => ini_get('memory_limit')],
    'max_execution_time' => ['required' => '300', 'current' => ini_get('max_execution_time')],
    'upload_max_filesize' => ['required' => '10M', 'current' => ini_get('upload_max_filesize')],
    'post_max_size' => ['required' => '10M', 'current' => ini_get('post_max_size')],
    'max_input_vars' => ['required' => '3000', 'current' => ini_get('max_input_vars')]
];

foreach ($phpSettings as $setting => $info) {
    echo "<tr>";
    echo "<td><strong>$setting</strong></td>";
    echo "<td>{$info['current']}</td>";
    echo "<td>Рекомендовано: {$info['required']}</td>";
    echo "</tr>";
}

echo "</table>";
echo "</div>";
echo "</div>";
echo "</div>";

// Перевірка файлової системи
echo "<div class='row mb-4'>";
echo "<div class='col-12'>";
echo "<h3><i class='fas fa-folder me-2'></i>Файлова система</h3>";
echo "<div class='table-responsive'>";
echo "<table class='table table-striped'>";

$directories = [
    'install/' => 'Директорія установки',
    'core/' => 'Основні файли',
    'pages/' => 'Сторінки',
    'themes/' => 'Теми',
    'languages/' => 'Мови',
    'admin/' => 'Адмін панель',
    'ajax/' => 'AJAX обробники'
];

foreach ($directories as $dir => $desc) {
    echo "<tr>";
    echo "<td><strong>$desc</strong></td>";
    echo "<td>$dir</td>";
    $exists = is_dir($dir);
    echo "<td>";
    if ($exists) {
        $readable = is_readable($dir);
        if ($readable) {
            echo "<span class='badge bg-success'><i class='fas fa-check'></i> Доступна</span>";
        } else {
            echo "<span class='badge bg-warning'><i class='fas fa-exclamation-triangle'></i> Недоступна для читання</span>";
        }
    } else {
        echo "<span class='badge bg-danger'><i class='fas fa-times'></i> Не існує</span>";
    }
    echo "</td>";
    echo "</tr>";
}

echo "</table>";
echo "</div>";
echo "</div>";
echo "</div>";

// Перевірка SQL файлів
echo "<div class='row mb-4'>";
echo "<div class='col-12'>";
echo "<h3><i class='fas fa-database me-2'></i>SQL файли</h3>";
echo "<div class='table-responsive'>";
echo "<table class='table table-striped'>";

$sqlFiles = [
    'install/database.sql' => 'Основна схема БД',
            'install/initial_data.sql' => 'Початкові дані'
];

foreach ($sqlFiles as $file => $desc) {
    echo "<tr>";
    echo "<td><strong>$desc</strong></td>";
    echo "<td>$file</td>";
    $exists = file_exists($file);
    echo "<td>";
    if ($exists) {
        $size = filesize($file);
        $readable = is_readable($file);
        if ($readable && $size > 0) {
            echo "<span class='badge bg-success'><i class='fas fa-check'></i> OK (" . number_format($size) . " байт)</span>";
        } else {
            echo "<span class='badge bg-warning'><i class='fas fa-exclamation-triangle'></i> Порожній або недоступний</span>";
        }
    } else {
        echo "<span class='badge bg-danger'><i class='fas fa-times'></i> Не існує</span>";
    }
    echo "</td>";
    echo "</tr>";
}

echo "</table>";
echo "</div>";
echo "</div>";
echo "</div>";

// Тест JSON
echo "<div class='row mb-4'>";
echo "<div class='col-12'>";
echo "<h3><i class='fas fa-code me-2'></i>Тест JSON обробки</h3>";
echo "<div class='alert alert-info'>";

$testData = ['success' => true, 'message' => 'Тест JSON', 'data' => ['key' => 'value']];
$jsonString = json_encode($testData, JSON_UNESCAPED_UNICODE);
$decoded = json_decode($jsonString, true);

if ($jsonString && $decoded && $decoded['success'] === true) {
    echo "<i class='fas fa-check text-success me-2'></i><strong>JSON обробка працює коректно</strong><br>";
    echo "Тестовий JSON: <code>$jsonString</code>";
} else {
    echo "<i class='fas fa-times text-danger me-2'></i><strong>Проблеми з JSON обробкою</strong><br>";
    echo "Помилка JSON: " . json_last_error_msg();
}

echo "</div>";
echo "</div>";
echo "</div>";

// Тест сесій
echo "<div class='row mb-4'>";
echo "<div class='col-12'>";
echo "<h3><i class='fas fa-user-clock me-2'></i>Тест сесій</h3>";
echo "<div class='alert alert-info'>";

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

$sessionTest = 'test_' . time();
$_SESSION['install_test'] = $sessionTest;

if (isset($_SESSION['install_test']) && $_SESSION['install_test'] === $sessionTest) {
    echo "<i class='fas fa-check text-success me-2'></i><strong>Сесії працюють коректно</strong>";
    unset($_SESSION['install_test']);
} else {
    echo "<i class='fas fa-times text-danger me-2'></i><strong>Проблеми з сесіями</strong>";
}

echo "</div>";
echo "</div>";
echo "</div>";

// Рекомендації
echo "<div class='row mb-4'>";
echo "<div class='col-12'>";
echo "<h3><i class='fas fa-lightbulb me-2'></i>Рекомендації</h3>";
echo "<div class='alert alert-warning'>";
echo "<ul class='mb-0'>";
echo "<li>Переконайтеся, що база даних MySQL працює</li>";
echo "<li>Підготуйте дані для підключення до БД (хост, користувач, пароль, назва БД)</li>";
echo "<li>Переконайтеся, що у користувача БД є права на створення таблиць</li>";
echo "<li>Мініхайте створити резервну копію існуючих даних</li>";
echo "<li>Після установки видаліть папку /install/ для безпеки</li>";
echo "</ul>";
echo "</div>";
echo "</div>";
echo "</div>";

// Кнопка запуску установки
if ($phpOk && extension_loaded('mysqli') && extension_loaded('json')) {
    echo "<div class='row'>";
    echo "<div class='col-12 text-center'>";
    echo "<a href='install/' class='btn btn-primary btn-lg'>";
    echo "<i class='fas fa-play me-2'></i>Запустити установку";
    echo "</a>";
    echo "</div>";
    echo "</div>";
} else {
    echo "<div class='row'>";
    echo "<div class='col-12'>";
    echo "<div class='alert alert-danger text-center'>";
    echo "<i class='fas fa-exclamation-triangle me-2'></i>";
    echo "<strong>Система не готова до установки!</strong><br>";
    echo "Виправте вказані проблеми перед запуском установки.";
    echo "</div>";
    echo "</div>";
    echo "</div>";
}

echo "</div>"; // card-body
echo "</div>"; // card
echo "</div>"; // col
echo "</div>"; // row
echo "</div>"; // container

echo "</body>";
echo "</html>";
?>