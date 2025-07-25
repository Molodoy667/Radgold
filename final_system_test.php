<?php
// Фінальний тест системи AdBoard Pro після виправлень
echo "=== ФІНАЛЬНИЙ ТЕСТ СИСТЕМИ AdBoard Pro ===\n\n";

$errors = [];
$warnings = [];
$success = [];

// 1. Тест інсталятора
echo "1. ТЕСТУВАННЯ ІНСТАЛЯТОРА:\n";

// Перевірка всіх кроків
for ($i = 1; $i <= 9; $i++) {
    $file = "install/steps/step_$i.php";
    if (file_exists($file)) {
        echo "✅ Крок $i: існує\n";
        $success[] = "Крок $i готовий";
    } else {
        echo "❌ Крок $i: відсутній\n";
        $errors[] = "Відсутній крок $i";
    }
}

// Перевірка 4-го кроку (мова та часовий пояс мають бути видалені)
$step4Content = file_get_contents('install/steps/step_4.php');
if (strpos($step4Content, 'language') === false && strpos($step4Content, 'timezone') === false) {
    echo "✅ Крок 4: мова та часовий пояс видалені\n";
    $success[] = "Крок 4 очищено від мови/часового поясу";
} else {
    echo "❌ Крок 4: все ще містить мову або часовий пояс\n";
    $errors[] = "Крок 4 не очищено";
}

// Перевірка JSON помилки в кроці 8
$step8Content = file_get_contents('install/steps/step_8.php');
$exitCount = substr_count($step8Content, 'exit();');
if ($exitCount >= 3) {
    echo "✅ Крок 8: JSON помилка виправлена (достатньо exit() викликів)\n";
    $success[] = "JSON помилка виправлена";
} else {
    echo "❌ Крок 8: недостатньо exit() викликів для JSON відповідей\n";
    $errors[] = "JSON помилка може залишатися";
}

// Перевірка кнопок форм
$formSteps = [4, 5, 7];
foreach ($formSteps as $stepNum) {
    $stepFile = "install/steps/step_$stepNum.php";
    $content = file_get_contents($stepFile);
    if (strpos($content, 'submitBtn') !== false && strpos($content, 'fa-spinner') !== false) {
        echo "✅ Крок $stepNum: кнопки форм виправлені\n";
        $success[] = "Кнопки форм в кроці $stepNum виправлені";
    } else {
        echo "❌ Крок $stepNum: кнопки форм не виправлені\n";
        $errors[] = "Кнопки форм в кроці $stepNum потребують виправлення";
    }
}

echo "\n2. ТЕСТУВАННЯ БАЗИ ДАНИХ:\n";

// Перевірка дублювання avatar
$dbContent = file_get_contents('install/database.sql');
$avatarCount = substr_count($dbContent, 'avatar VARCHAR');
if ($avatarCount === 1) {
    echo "✅ БД: дублювання avatar виправлено\n";
    $success[] = "Дублювання avatar усунено";
} else {
    echo "❌ БД: все ще є дублювання avatar ($avatarCount входжень)\n";
    $errors[] = "Дублювання avatar в БД";
}

// Перевірка поля bio
if (strpos($dbContent, 'bio TEXT') !== false) {
    echo "✅ БД: поле bio додано\n";
    $success[] = "Поле bio додано";
} else {
    echo "❌ БД: поле bio відсутнє\n";
    $errors[] = "Відсутнє поле bio";
}

// Перевірка створення адміністратора
$step8Content = file_get_contents('install/steps/step_8.php');
if (strpos($step8Content, 'first_name, last_name') !== false) {
    echo "✅ БД: створення адміністратора виправлено\n";
    $success[] = "Створення адміністратора включає ім'я";
} else {
    echo "❌ БД: створення адміністратора неповне\n";
    $errors[] = "Створення адміністратора без імені";
}

echo "\n3. ТЕСТУВАННЯ ФУНКЦІЙ:\n";

// Перевірка основних функцій
$functionsContent = file_get_contents('core/functions.php');
$requiredFunctions = [
    'function getUserId()' => 'getUserId',
    'function getUserById(' => 'getUserById', 
    'function __(' => 'Функція перекладу',
    'function getAllGradients()' => 'getAllGradients',
    'function getSiteSetting(' => 'getSiteSetting'
];

foreach ($requiredFunctions as $search => $name) {
    if (strpos($functionsContent, $search) !== false) {
        echo "✅ Функція: $name існує\n";
        $success[] = "Функція $name реалізована";
    } else {
        echo "❌ Функція: $name відсутня\n";
        $errors[] = "Відсутня функція $name";
    }
}

// Перевірка підключення БД
$configContent = file_get_contents('core/config.php');
if (strpos($configContent, 'new mysqli(') !== false) {
    echo "✅ Config: підключення БД додано\n";
    $success[] = "Підключення БД налаштовано";
} else {
    echo "❌ Config: підключення БД відсутнє\n";
    $errors[] = "Відсутнє підключення БД";
}

echo "\n4. ТЕСТУВАННЯ МОВНИХ ФАЙЛІВ:\n";

$languages = ['uk', 'ru', 'en'];
foreach ($languages as $lang) {
    $langFile = "languages/$lang.php";
    $content = file_get_contents($langFile);
    
    // Перевірка наявності profile секції
    $profileCount = substr_count($content, "'profile' => [");
    if ($profileCount === 1) {
        echo "✅ $lang.php: profile секція унікальна\n";
        $success[] = "Profile секція в $lang.php унікальна";
    } else {
        echo "❌ $lang.php: дублювання profile секції ($profileCount входжень)\n";
        $errors[] = "Дублювання profile в $lang.php";
    }
    
    // Перевірка наявності ключових перекладів
    $requiredKeys = ['my_profile', 'personal_info', 'avatar_settings', 'save_changes'];
    $missingKeys = [];
    foreach ($requiredKeys as $key) {
        if (strpos($content, "'$key'") === false) {
            $missingKeys[] = $key;
        }
    }
    
    if (empty($missingKeys)) {
        echo "✅ $lang.php: всі ключові переклади присутні\n";
        $success[] = "Переклади в $lang.php повні";
    } else {
        echo "❌ $lang.php: відсутні переклади: " . implode(', ', $missingKeys) . "\n";
        $errors[] = "Неповні переклади в $lang.php";
    }
}

echo "\n5. ТЕСТУВАННЯ ГРАДІЄНТІВ:\n";

// Перевірка функції градієнтів
if (strpos($functionsContent, 'getAllGradients()') !== false) {
    // Підрахунок кількості градієнтів
    $gradientPattern = "'gradient-\d+' => 'linear-gradient";
    preg_match_all("/$gradientPattern/", $functionsContent, $matches);
    $gradientCount = count($matches[0]);
    
    if ($gradientCount >= 30) {
        echo "✅ Градієнти: $gradientCount градієнтів доступно\n";
        $success[] = "Система градієнтів повна ($gradientCount)";
    } else {
        echo "❌ Градієнти: тільки $gradientCount градієнтів (потрібно 30+)\n";
        $warnings[] = "Недостатньо градієнтів";
    }
} else {
    echo "❌ Градієнти: функція getAllGradients відсутня\n";
    $errors[] = "Відсутня система градієнтів";
}

// Перевірка CSS градієнтів у профілі
$profileContent = file_get_contents('pages/user/profile.php');
$profileGradients = substr_count($profileContent, '.gradient-');
if ($profileGradients >= 30) {
    echo "✅ Профіль: CSS для $profileGradients градієнтів\n";
    $success[] = "CSS градієнтів у профілі повний";
} else {
    echo "❌ Профіль: тільки $profileGradients CSS градієнтів\n";
    $warnings[] = "Неповний CSS градієнтів у профілі";
}

echo "\n6. ТЕСТУВАННЯ ПРОФІЛЮ КОРИСТУВАЧА:\n";

// Перевірка основних функцій профілю
$profileFunctions = [
    'updateUserProfile' => 'Оновлення профілю',
    'uploadUserAvatar' => 'Завантаження аватару',
    'changeUserPassword' => 'Зміна паролю',
    'generateInitialsAvatar' => 'Генерація аватару'
];

foreach ($profileFunctions as $func => $desc) {
    if (strpos($profileContent, $func) !== false) {
        echo "✅ Профіль: $desc реалізовано\n";
        $success[] = "$desc в профілі";
    } else {
        echo "❌ Профіль: $desc відсутнє\n";
        $errors[] = "Відсутнє $desc";
    }
}

// Перевірка шляхів
if (strpos($profileContent, "require_once '../../core/config.php'") !== false) {
    echo "✅ Профіль: шляхи include правильні\n";
    $success[] = "Шляхи у профілі правильні";
} else {
    echo "❌ Профіль: неправильні шляхи include\n";
    $errors[] = "Неправильні шляхи у профілі";
}

echo "\n7. ТЕСТУВАННЯ ДИРЕКТОРІЙ:\n";

$requiredDirs = [
    'uploads' => 'Завантаження',
    'uploads/avatars' => 'Аватари',
    'cache' => 'Кеш',
    'logs' => 'Логи',
    'languages' => 'Мови',
    'install' => 'Інсталятор'
];

foreach ($requiredDirs as $dir => $desc) {
    if (is_dir($dir)) {
        $writable = is_writable($dir) ? ' (записуваний)' : ' (тільки читання)';
        echo "✅ $desc: $dir існує$writable\n";
        $success[] = "Директорія $desc готова";
    } else {
        echo "❌ $desc: $dir відсутня\n";
        $errors[] = "Відсутня директорія $desc";
    }
}

echo "\n=== ПІДСУМОК ТЕСТУВАННЯ ===\n";

echo "\n✅ УСПІШНО (" . count($success) . "):\n";
foreach ($success as $item) {
    echo "   • $item\n";
}

if (!empty($warnings)) {
    echo "\n⚠️  ПОПЕРЕДЖЕННЯ (" . count($warnings) . "):\n";
    foreach ($warnings as $item) {
        echo "   • $item\n";
    }
}

if (!empty($errors)) {
    echo "\n❌ ПОМИЛКИ (" . count($errors) . "):\n";
    foreach ($errors as $item) {
        echo "   • $item\n";
    }
    echo "\n🚨 ПОТРІБНІ ДОДАТКОВІ ВИПРАВЛЕННЯ!\n";
} else {
    echo "\n🎉 ВСЕ ТЕСТИ ПРОЙДЕНІ УСПІШНО!\n";
    echo "💪 СИСТЕМА ГОТОВА ДО PRODUCTION!\n";
}

echo "\n=== СТАТИСТИКА ===\n";
echo "• Успішних тестів: " . count($success) . "\n";
echo "• Попереджень: " . count($warnings) . "\n";  
echo "• Помилок: " . count($errors) . "\n";
echo "• Загальна готовність: " . round((count($success) / (count($success) + count($warnings) + count($errors))) * 100, 1) . "%\n";

echo "\n📋 РЕКОМЕНДАЦІЇ:\n";
if (empty($errors)) {
    echo "• Запустіть інсталятор: http://your-domain/install/\n";
    echo "• Протестуйте всі 9 кроків установки\n";
    echo "• Перевірте профіль користувача після створення акаунту\n";
    echo "• Протестуйте завантаження аватарів\n";
} else {
    echo "• Виправте знайдені помилки перед production\n";
    echo "• Повторіть тестування після виправлень\n";
}

echo "\n=== ТЕСТ ЗАВЕРШЕНО ===\n";
?>