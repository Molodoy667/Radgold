<?php
// Система дебагінгу AdBoard Pro
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html lang='uk'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>AdBoard Pro - System Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .test { margin: 15px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .success { background: #d4edda; border-color: #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .warning { background: #fff3cd; border-color: #ffeaa7; color: #856404; }
        .info { background: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
        h1, h2 { color: #333; }
        code { background: #f8f9fa; padding: 2px 5px; border-radius: 3px; }
        .gradient-test { width: 50px; height: 50px; border-radius: 50%; display: inline-block; margin: 5px; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔧 AdBoard Pro - Системна діагностика</h1>";

// Тест 1: Структура файлів
echo "<div class='test'>";
echo "<h2>📁 Тест 1: Структура файлів</h2>";

$requiredFiles = [
    'core/config.php',
    'core/functions.php',
    'install/index.php',
    'install/database.sql',
    'languages/uk.php',
    'languages/ru.php',
    'languages/en.php',
    'pages/user/profile.php',
    'themes/header.php',
    'themes/footer.php'
];

$allFilesOk = true;
foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "✅ <code>$file</code> - існує<br>";
    } else {
        echo "❌ <code>$file</code> - відсутній<br>";
        $allFilesOk = false;
    }
}

if ($allFilesOk) {
    echo "</div><div class='test success'><strong>✅ Всі основні файли присутні!</strong>";
} else {
    echo "</div><div class='test error'><strong>❌ Деякі файли відсутні!</strong>";
}
echo "</div>";

// Тест 2: Підключення до config.php
echo "<div class='test'>";
echo "<h2>⚙️ Тест 2: Підключення конфігурації</h2>";

try {
    require_once 'core/config.php';
    echo "✅ config.php підключено успішно<br>";
    echo "✅ DB_HOST: " . DB_HOST . "<br>";
    echo "✅ DB_NAME: " . DB_NAME . "<br>";
    echo "✅ SITE_NAME: " . SITE_NAME . "<br>";
    
    if (isset($db)) {
        echo "✅ Об'єкт бази даних створено<br>";
        echo "</div><div class='test success'><strong>✅ Конфігурація працює!</strong>";
    } else {
        echo "❌ Об'єкт бази даних не створено<br>";
        echo "</div><div class='test error'><strong>❌ Проблема з БД!</strong>";
    }
} catch (Exception $e) {
    echo "❌ Помилка: " . $e->getMessage() . "<br>";
    echo "</div><div class='test error'><strong>❌ Помилка конфігурації!</strong>";
}
echo "</div>";

// Тест 3: Функції
echo "<div class='test'>";
echo "<h2>🔧 Тест 3: Основні функції</h2>";

require_once 'core/functions.php';

$functions = [
    'isLoggedIn',
    'getUserId', 
    'getUserById',
    'getSiteSetting',
    'setSiteSetting',
    'getAllGradients',
    '__'
];

$functionsOk = true;
foreach ($functions as $func) {
    if (function_exists($func)) {
        echo "✅ <code>$func()</code> - існує<br>";
    } else {
        echo "❌ <code>$func()</code> - відсутня<br>";
        $functionsOk = false;
    }
}

if ($functionsOk) {
    echo "</div><div class='test success'><strong>✅ Всі функції доступні!</strong>";
} else {
    echo "</div><div class='test error'><strong>❌ Деякі функції відсутні!</strong>";
}
echo "</div>";

// Тест 4: Градієнти
echo "<div class='test'>";
echo "<h2>🎨 Тест 4: Система градієнтів</h2>";

if (function_exists('getAllGradients')) {
    $gradients = getAllGradients();
    echo "✅ Завантажено " . count($gradients) . " градієнтів<br><br>";
    
    echo "<div style='max-width: 600px;'>";
    foreach ($gradients as $key => $css) {
        echo "<div class='gradient-test' style='background: $css' title='$key'></div>";
    }
    echo "</div><br>";
    
    if (count($gradients) >= 30) {
        echo "</div><div class='test success'><strong>✅ Система градієнтів працює! (30+ градієнтів)</strong>";
    } else {
        echo "</div><div class='test warning'><strong>⚠️ Градієнтів менше 30!</strong>";
    }
} else {
    echo "❌ Функція getAllGradients() недоступна<br>";
    echo "</div><div class='test error'><strong>❌ Система градієнтів не працює!</strong>";
}
echo "</div>";

// Тест 5: Перевірка SQL файлів
echo "<div class='test'>";
echo "<h2>🗄️ Тест 5: SQL файли</h2>";

$sqlFiles = [
    'install/database.sql',
    'install/ads_database.sql',
    'install/admin_tables.sql'
];

$sqlOk = true;
foreach ($sqlFiles as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $lines = count(explode("\n", $content));
        $size = round(filesize($file) / 1024, 2);
        echo "✅ <code>$file</code> - $lines рядків, $size KB<br>";
        
        // Перевірка на дублювання avatar
        if (strpos($file, 'database.sql') !== false) {
            $avatarCount = substr_count($content, 'avatar VARCHAR');
            if ($avatarCount > 1) {
                echo "⚠️ Знайдено $avatarCount входжень 'avatar VARCHAR' - можливе дублювання<br>";
                $sqlOk = false;
            }
        }
    } else {
        echo "❌ <code>$file</code> - відсутній<br>";
        $sqlOk = false;
    }
}

if ($sqlOk) {
    echo "</div><div class='test success'><strong>✅ SQL файли в порядку!</strong>";
} else {
    echo "</div><div class='test warning'><strong>⚠️ Виявлені проблеми в SQL файлах!</strong>";
}
echo "</div>";

// Тест 6: Мовні файли
echo "<div class='test'>";
echo "<h2>🌍 Тест 6: Мультимовність</h2>";

$languages = ['uk', 'ru', 'en'];
$langOk = true;

foreach ($languages as $lang) {
    $file = "languages/$lang.php";
    if (file_exists($file)) {
        $translations = include $file;
        if (is_array($translations)) {
            $keys = count($translations, COUNT_RECURSIVE);
            echo "✅ <code>$lang.php</code> - $keys перекладів<br>";
            
            // Перевірка наявності profile секції
            if (isset($translations['profile'])) {
                echo "✅ Секція 'profile' присутня в $lang<br>";
            } else {
                echo "⚠️ Секція 'profile' відсутня в $lang<br>";
                $langOk = false;
            }
        } else {
            echo "❌ <code>$lang.php</code> - некоректний формат<br>";
            $langOk = false;
        }
    } else {
        echo "❌ <code>$lang.php</code> - відсутній<br>";
        $langOk = false;
    }
}

if ($langOk) {
    echo "</div><div class='test success'><strong>✅ Мультимовність налаштована!</strong>";
} else {
    echo "</div><div class='test warning'><strong>⚠️ Проблеми з мовними файлами!</strong>";
}
echo "</div>";

// Тест 7: Інсталятор
echo "<div class='test'>";
echo "<h2>💾 Тест 7: Інсталятор</h2>";

$isInstalled = file_exists('.installed');
echo "Статус установки: " . ($isInstalled ? "✅ Встановлено" : "⚠️ Не встановлено") . "<br>";

$installSteps = [];
for ($i = 1; $i <= 9; $i++) {
    $stepFile = "install/steps/step_$i.php";
    if (file_exists($stepFile)) {
        $installSteps[] = $i;
        echo "✅ Крок $i - існує<br>";
    } else {
        echo "❌ Крок $i - відсутній<br>";
    }
}

if (count($installSteps) === 9) {
    echo "</div><div class='test success'><strong>✅ Інсталятор готовий! (9 кроків)</strong>";
} else {
    echo "</div><div class='test error'><strong>❌ Інсталятор неповний!</strong>";
}
echo "</div>";

// Тест 8: Директорії
echo "<div class='test'>";
echo "<h2>📂 Тест 8: Директорії</h2>";

$requiredDirs = [
    'uploads',
    'uploads/avatars',
    'cache',
    'logs',
    'admin',
    'ajax',
    'core',
    'themes',
    'pages',
    'languages',
    'install'
];

$dirsOk = true;
foreach ($requiredDirs as $dir) {
    if (is_dir($dir)) {
        $writable = is_writable($dir) ? "(записуваний)" : "(тільки читання)";
        echo "✅ <code>$dir/</code> - існує $writable<br>";
    } else {
        echo "❌ <code>$dir/</code> - відсутній<br>";
        $dirsOk = false;
    }
}

if ($dirsOk) {
    echo "</div><div class='test success'><strong>✅ Всі директорії на місці!</strong>";
} else {
    echo "</div><div class='test error'><strong>❌ Деякі директорії відсутні!</strong>";
}
echo "</div>";

// Тест 9: Тестування перекладів
echo "<div class='test'>";
echo "<h2>🔤 Тест 9: Функція перекладів</h2>";

if (function_exists('__')) {
    $testKey = 'profile.my_profile';
    $translation = __($testKey);
    
    echo "Тест ключа: <code>$testKey</code><br>";
    echo "Результат: <strong>$translation</strong><br>";
    
    if ($translation !== $testKey) {
        echo "</div><div class='test success'><strong>✅ Функція перекладу працює!</strong>";
    } else {
        echo "</div><div class='test warning'><strong>⚠️ Переклад не знайдено, але функція працює!</strong>";
    }
} else {
    echo "❌ Функція __() недоступна<br>";
    echo "</div><div class='test error'><strong>❌ Функція перекладу не працює!</strong>";
}
echo "</div>";

// Загальний підсумок
echo "<div class='test info'>";
echo "<h2>📊 Загальний підсумок</h2>";
echo "<p>Діагностика завершена. Перевірте результати вище для виявлення та усунення проблем.</p>";
echo "<p><strong>Рекомендації:</strong></p>";
echo "<ul>";
echo "<li>Якщо система не встановлена, перейдіть на <a href='install/'>install/</a></li>";
echo "<li>Перевірте налаштування бази даних у <code>core/config.php</code></li>";
echo "<li>Переконайтесь, що директорії uploads та cache мають права на запис</li>";
echo "<li>Після установки протестуйте профіль користувача</li>";
echo "</ul>";
echo "</div>";

echo "    </div>
</body>
</html>";
?>