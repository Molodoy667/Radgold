<?php
/**
 * Улучшенный debug файл для AdBoard Pro
 * Специально для диагностики проблем с БД и JSON
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>";
echo "<html lang='uk'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Системная диагностика AdBoard Pro</title>";
echo "<link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css' rel='stylesheet'>";
echo "<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>";
echo "</head>";
echo "<body class='bg-light'>";

echo "<div class='container my-5'>";
echo "<div class='card shadow'>";
echo "<div class='card-header bg-danger text-white'>";
echo "<h1 class='mb-0'><i class='fas fa-bug me-2'></i>Системная диагностика AdBoard Pro</h1>";
echo "</div>";
echo "<div class='card-body'>";

// Тест 1: Общая информация
echo "<div class='row mb-4'>";
echo "<div class='col-12'>";
echo "<h3><i class='fas fa-info-circle me-2'></i>Общая информация</h3>";
echo "<div class='table-responsive'>";
echo "<table class='table table-striped'>";

echo "<tr><td><strong>PHP версия</strong></td><td>" . PHP_VERSION . "</td></tr>";
echo "<tr><td><strong>Сервер</strong></td><td>" . ($_SERVER['SERVER_SOFTWARE'] ?? 'Неизвестно') . "</td></tr>";
echo "<tr><td><strong>Документ рут</strong></td><td>" . ($_SERVER['DOCUMENT_ROOT'] ?? 'Неизвестно') . "</td></tr>";
echo "<tr><td><strong>Текущая папка</strong></td><td>" . __DIR__ . "</td></tr>";
echo "<tr><td><strong>Memory limit</strong></td><td>" . ini_get('memory_limit') . "</td></tr>";

echo "</table>";
echo "</div>";
echo "</div>";
echo "</div>";

// Тест 2: Файлы конфигурации
echo "<div class='row mb-4'>";
echo "<div class='col-12'>";
echo "<h3><i class='fas fa-cog me-2'></i>Конфигурация</h3>";
echo "<div class='table-responsive'>";
echo "<table class='table table-striped'>";

$configFile = 'core/config.php';
echo "<tr>";
echo "<td><strong>Файл config.php</strong></td>";
if (file_exists($configFile)) {
    echo "<td class='text-success'><i class='fas fa-check'></i> Существует</td>";
    
    // Пробуем подключить конфигурацию
    try {
        require_once $configFile;
        echo "<td class='text-success'><i class='fas fa-check'></i> Успешно подключен</td>";
        
        // Проверяем константы
        $constants = ['DB_HOST', 'DB_USER', 'DB_NAME', 'SITE_NAME'];
        foreach ($constants as $const) {
            echo "<tr><td><strong>$const</strong></td>";
            if (defined($const)) {
                echo "<td class='text-success'>" . constant($const) . "</td>";
                echo "<td><i class='fas fa-check text-success'></i> OK</td>";
            } else {
                echo "<td class='text-danger'>Не определено</td>";
                echo "<td><i class='fas fa-times text-danger'></i> Ошибка</td>";
            }
            echo "</tr>";
        }
        
    } catch (Exception $e) {
        echo "<td class='text-danger'><i class='fas fa-times'></i> Ошибка: " . $e->getMessage() . "</td>";
    }
} else {
    echo "<td class='text-danger'><i class='fas fa-times'></i> Не существует</td>";
    echo "<td class='text-warning'>Файл конфигурации отсутствует</td>";
}
echo "</tr>";

echo "</table>";
echo "</div>";
echo "</div>";
echo "</div>";

// Тест 3: База данных
echo "<div class='row mb-4'>";
echo "<div class='col-12'>";
echo "<h3><i class='fas fa-database me-2'></i>База данных</h3>";
echo "<div class='table-responsive'>";
echo "<table class='table table-striped'>";

// Проверяем глобальную переменную $db
echo "<tr>";
echo "<td><strong>Глобальная переменная \$db</strong></td>";
if (isset($db) && $db instanceof mysqli) {
    echo "<td class='text-success'><i class='fas fa-check'></i> Создана (mysqli)</td>";
    
    if ($db->connect_error) {
        echo "<td class='text-danger'><i class='fas fa-times'></i> Ошибка подключения: " . $db->connect_error . "</td>";
    } else {
        echo "<td class='text-success'><i class='fas fa-check'></i> Подключено успешно</td>";
        
        // Тестируем запрос
        echo "<tr><td><strong>Тест запроса</strong></td>";
        try {
            $result = $db->query("SELECT 1 as test");
            if ($result) {
                $row = $result->fetch_assoc();
                echo "<td class='text-success'><i class='fas fa-check'></i> Запросы работают</td>";
                echo "<td>Результат: " . $row['test'] . "</td>";
            } else {
                echo "<td class='text-danger'><i class='fas fa-times'></i> Запрос не выполнился</td>";
                echo "<td>Ошибка: " . $db->error . "</td>";
            }
        } catch (Exception $e) {
            echo "<td class='text-danger'><i class='fas fa-times'></i> Исключение</td>";
            echo "<td>Ошибка: " . $e->getMessage() . "</td>";
        }
        echo "</tr>";
        
        // Проверяем таблицы
        $tables = ['users', 'site_settings', 'categories', 'ads'];
        foreach ($tables as $table) {
            echo "<tr><td><strong>Таблица $table</strong></td>";
            try {
                $result = $db->query("SELECT COUNT(*) as count FROM $table");
                if ($result) {
                    $row = $result->fetch_assoc();
                    echo "<td class='text-success'><i class='fas fa-check'></i> " . $row['count'] . " записей</td>";
                    echo "<td>OK</td>";
                } else {
                    echo "<td class='text-warning'><i class='fas fa-exclamation-triangle'></i> Не найдена</td>";
                    echo "<td>Ошибка: " . $db->error . "</td>";
                }
            } catch (Exception $e) {
                echo "<td class='text-danger'><i class='fas fa-times'></i> Ошибка</td>";
                echo "<td>" . $e->getMessage() . "</td>";
            }
            echo "</tr>";
        }
    }
} else {
    echo "<td class='text-danger'><i class='fas fa-times'></i> НЕ создана</td>";
    echo "<td class='text-danger'>Объект базы данных не существует!</td>";
}
echo "</tr>";

echo "</table>";
echo "</div>";
echo "</div>";
echo "</div>";

// Тест 4: Функции
echo "<div class='row mb-4'>";
echo "<div class='col-12'>";
echo "<h3><i class='fas fa-code me-2'></i>Критические функции</h3>";
echo "<div class='table-responsive'>";
echo "<table class='table table-striped'>";

// Проверяем функции
$functionsFile = 'core/functions.php';
if (file_exists($functionsFile)) {
    try {
        require_once $functionsFile;
        echo "<tr><td><strong>Файл functions.php</strong></td><td class='text-success'>Подключен</td><td><i class='fas fa-check text-success'></i></td></tr>";
        
        // Тестируем функции
        $functions = [
            'getSiteSetting' => 'Получение настроек сайта',
            '__' => 'Функция перевода'
        ];
        
        foreach ($functions as $func => $desc) {
            echo "<tr><td><strong>$desc ($func)</strong></td>";
            if (function_exists($func)) {
                echo "<td class='text-success'>Существует</td>";
                
                // Тестируем функцию
                try {
                    if ($func === 'getSiteSetting') {
                        $result = getSiteSetting('language', 'uk');
                        echo "<td class='text-success'>Результат: $result</td>";
                    } elseif ($func === '__') {
                        $result = __('test');
                        echo "<td class='text-success'>Результат: $result</td>";
                    }
                } catch (Exception $e) {
                    echo "<td class='text-danger'>Ошибка: " . $e->getMessage() . "</td>";
                }
            } else {
                echo "<td class='text-danger'>НЕ существует</td>";
                echo "<td><i class='fas fa-times text-danger'></i></td>";
            }
            echo "</tr>";
        }
        
    } catch (Exception $e) {
        echo "<tr><td><strong>Файл functions.php</strong></td><td class='text-danger'>Ошибка подключения</td><td>" . $e->getMessage() . "</td></tr>";
    }
} else {
    echo "<tr><td><strong>Файл functions.php</strong></td><td class='text-danger'>НЕ найден</td><td><i class='fas fa-times text-danger'></i></td></tr>";
}

echo "</table>";
echo "</div>";
echo "</div>";
echo "</div>";

// Тест 5: JSON
echo "<div class='row mb-4'>";
echo "<div class='col-12'>";
echo "<h3><i class='fas fa-code me-2'></i>Тест JSON</h3>";
echo "<div class='alert alert-info'>";

$testData = [
    'success' => true,
    'message' => 'Тест JSON работает',
    'timestamp' => date('Y-m-d H:i:s'),
    'db_status' => isset($db) && !$db->connect_error,
    'ukrainian_text' => 'Тест українського тексту'
];

$jsonString = json_encode($testData, JSON_UNESCAPED_UNICODE);
$decoded = json_decode($jsonString, true);

if ($jsonString && $decoded && $decoded['success'] === true) {
    echo "<i class='fas fa-check text-success me-2'></i><strong>JSON обработка работает!</strong><br>";
    echo "<small>JSON строка: <code>" . htmlspecialchars($jsonString) . "</code></small>";
} else {
    echo "<i class='fas fa-times text-danger me-2'></i><strong>Проблемы с JSON!</strong><br>";
    echo "Ошибка: " . json_last_error_msg();
}

echo "</div>";
echo "</div>";
echo "</div>";

// Тест 6: Рекомендации
echo "<div class='row mb-4'>";
echo "<div class='col-12'>";
echo "<h3><i class='fas fa-lightbulb me-2'></i>Рекомендации по исправлению</h3>";

if (!isset($db) || $db->connect_error) {
    echo "<div class='alert alert-danger'>";
    echo "<h5>🚨 Критическая проблема с базой данных!</h5>";
    echo "<ol>";
    echo "<li><strong>Проверьте данные подключения к БД</strong> в файле <code>core/config.php</code></li>";
    echo "<li><strong>Убедитесь что MySQL сервер запущен</strong></li>";
    echo "<li><strong>Проверьте права пользователя БД</strong></li>";
    echo "<li><strong>Убедитесь что база данных существует</strong></li>";
    echo "</ol>";
    echo "</div>";
}

echo "<div class='alert alert-warning'>";
echo "<h5>📋 Для исправления JSON ошибок:</h5>";
echo "<ol>";
echo "<li><strong>Запустите переустановку</strong> - удалите файл <code>.installed</code> и перейдите в <code>/install/</code></li>";
echo "<li><strong>Используйте правильные данные БД</strong>: хост=localhost, пользователь=iteiyzke_project, БД=iteiyzke_project</li>";
echo "<li><strong>Убедитесь что у пользователя БД есть все права</strong></li>";
echo "<li><strong>Проверьте логи ошибок сервера</strong> для дополнительной информации</li>";
echo "</ol>";
echo "</div>";

echo "</div>";
echo "</div>";

echo "</div>"; // card-body
echo "</div>"; // card
echo "</div>"; // container

echo "<script>";
echo "console.log('Debug system loaded successfully');";
echo "console.log('DB status:', " . (isset($db) && !$db->connect_error ? 'true' : 'false') . ");";
echo "</script>";

echo "</body>";
echo "</html>";
?>