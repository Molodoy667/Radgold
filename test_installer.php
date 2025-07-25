<?php
// Тест логіки інсталятора без виконання PHP
echo "=== ТЕСТ ІНСТАЛЯТОРА AdBoard Pro ===\n\n";

// Перевірка всіх кроків інсталятора
echo "1. Перевірка кроків інсталятора:\n";
for ($i = 1; $i <= 9; $i++) {
    $file = "install/steps/step_$i.php";
    echo "Крок $i: " . (file_exists($file) ? "✅" : "❌") . " $file\n";
}

echo "\n2. Перевірка SQL файлів:\n";
$sqlFiles = ['database.sql', 'ads_database.sql', 'admin_tables.sql'];
foreach ($sqlFiles as $file) {
    $path = "install/$file";
    if (file_exists($path)) {
        $size = round(filesize($path) / 1024, 2);
        echo "✅ $file ($size KB)\n";
    } else {
        echo "❌ $file - відсутній\n";
    }
}

echo "\n3. Перевірка структури database.sql:\n";
$dbContent = file_get_contents('install/database.sql');
$checks = [
    'CREATE TABLE users' => 'Таблиця користувачів',
    'CREATE TABLE site_settings' => 'Налаштування сайту', 
    'avatar VARCHAR(255)' => 'Колонка аватару',
    'bio TEXT' => 'Колонка біографії',
    'username VARCHAR' => 'Колонка імені користувача',
    'first_name VARCHAR' => 'Ім\'я',
    'last_name VARCHAR' => 'Прізвище'
];

foreach ($checks as $search => $desc) {
    if (strpos($dbContent, $search) !== false) {
        echo "✅ $desc знайдено\n";
    } else {
        echo "❌ $desc відсутнє\n";
    }
}

// Перевірка на дублювання
$avatarCount = substr_count($dbContent, 'avatar VARCHAR');
echo "\nПеревірка дублювань:\n";
echo "Колонок avatar: $avatarCount " . ($avatarCount === 1 ? "✅" : "❌ (має бути 1)") . "\n";

echo "\n4. Перевірка інсталятора index.php:\n";
$installContent = file_get_contents('install/index.php');
$installChecks = [
    '$maxSteps = 9' => 'Кількість кроків',
    'createDatabaseAndSchema' => 'Функція створення БД',
    'case 5:' => 'Обробка кроку 5',
    'case 8:' => 'Обробка кроку 8',
    'additional' => 'Додаткові налаштування',
    'theme' => 'Налаштування теми'
];

foreach ($installChecks as $search => $desc) {
    if (strpos($installContent, $search) !== false) {
        echo "✅ $desc\n";
    } else {
        echo "❌ $desc відсутнє\n";
    }
}

echo "\n5. Перевірка профілю користувача:\n";
if (file_exists('pages/user/profile.php')) {
    $profileContent = file_get_contents('pages/user/profile.php');
    $profileChecks = [
        'updateUserProfile' => 'Функція оновлення профілю',
        'uploadUserAvatar' => 'Завантаження аватару',
        'changeUserPassword' => 'Зміна паролю',
        'generateInitialsAvatar' => 'Генерація аватару з ініціалами',
        'avatar_settings' => 'Налаштування аватару',
        'password_settings' => 'Налаштування паролю'
    ];

    foreach ($profileChecks as $search => $desc) {
        if (strpos($profileContent, $search) !== false) {
            echo "✅ $desc\n";
        } else {
            echo "❌ $desc відсутнє\n";
        }
    }
} else {
    echo "❌ Файл профілю не знайдено\n";
}

echo "\n6. Перевірка функцій у core/functions.php:\n";
if (file_exists('core/functions.php')) {
    $functionsContent = file_get_contents('core/functions.php');
    $functionChecks = [
        'function getUserId()' => 'getUserId',
        'function getUserById(' => 'getUserById',
        'function __(' => 'Функція перекладу',
        'function getAllGradients()' => 'getAllGradients',
        'function getSiteSetting(' => 'getSiteSetting',
        'function setSiteSetting(' => 'setSiteSetting'
    ];

    foreach ($functionChecks as $search => $desc) {
        if (strpos($functionsContent, $search) !== false) {
            echo "✅ $desc\n";
        } else {
            echo "❌ $desc відсутня\n";
        }
    }
} else {
    echo "❌ Файл functions.php не знайдено\n";
}

echo "\n7. Перевірка перекладів:\n";
$languages = ['uk', 'ru', 'en'];
foreach ($languages as $lang) {
    $langFile = "languages/$lang.php";
    if (file_exists($langFile)) {
        $content = file_get_contents($langFile);
        $hasProfile = strpos($content, "'profile' => [") !== false;
        $hasMyProfile = strpos($content, "'my_profile'") !== false;
        echo "✅ $lang.php: profile=" . ($hasProfile ? "✅" : "❌") . ", my_profile=" . ($hasMyProfile ? "✅" : "❌") . "\n";
    } else {
        echo "❌ $lang.php не знайдено\n";
    }
}

echo "\n8. Перевірка config.php:\n";
if (file_exists('core/config.php')) {
    $configContent = file_get_contents('core/config.php');
    $configChecks = [
        'new mysqli(' => 'Підключення до БД',
        'DB_HOST' => 'Константа хосту',
        'DB_NAME' => 'Константа БД',
        'session_start()' => 'Старт сесії'
    ];

    foreach ($configChecks as $search => $desc) {
        if (strpos($configContent, $search) !== false) {
            echo "✅ $desc\n";
        } else {
            echo "❌ $desc відсутнє\n";
        }
    }
} else {
    echo "❌ config.php не знайдено\n";
}

echo "\n9. Перевірка директорій:\n";
$dirs = ['uploads', 'uploads/avatars', 'cache', 'logs', 'admin', 'ajax', 'core', 'themes', 'pages', 'languages', 'install'];
foreach ($dirs as $dir) {
    $exists = is_dir($dir);
    $writable = $exists ? is_writable($dir) : false;
    echo ($exists ? "✅" : "❌") . " $dir" . ($writable ? " (записуваний)" : " (тільки читання)") . "\n";
}

echo "\n=== ПІДСУМОК ТЕСТУВАННЯ ===\n";
echo "Всі основні компоненти перевірені.\n";
echo "Для повного тестування запустіть інсталятор в браузері.\n";
echo "URL: http://your-domain/install/\n\n";

// Статистика файлів
echo "=== СТАТИСТИКА ПРОЕКТУ ===\n";
$phpFiles = glob('**/*.php', GLOB_BRACE);
$sqlFiles = glob('**/*.sql', GLOB_BRACE);
echo "PHP файлів: " . count($phpFiles) . "\n";
echo "SQL файлів: " . count($sqlFiles) . "\n";

$totalSize = 0;
foreach ($phpFiles as $file) {
    if (file_exists($file)) {
        $totalSize += filesize($file);
    }
}
echo "Загальний розмір PHP: " . round($totalSize / 1024, 2) . " KB\n";
?>