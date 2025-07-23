<?php
// Повний тест інсталятора AdBoard Pro після виправлень
echo "=== ПОВНИЙ ТЕСТ ІНСТАЛЯТОРА AdBoard Pro ===\n\n";

$errors = [];
$warnings = [];
$success = [];

// Тест всіх кроків інсталятора
echo "🔄 ТЕСТУВАННЯ КРОКІВ ІНСТАЛЯТОРА:\n\n";

for ($step = 1; $step <= 9; $step++) {
    echo "--- КРОК $step ---\n";
    $stepFile = "install/steps/step_$step.php";
    
    if (!file_exists($stepFile)) {
        echo "❌ Файл $stepFile не знайдено\n";
        $errors[] = "Відсутній крок $step";
        continue;
    }
    
    $content = file_get_contents($stepFile);
    
    // Перевірка розміру файлу
    $size = filesize($stepFile);
    echo "📄 Розмір файлу: " . round($size / 1024, 2) . " KB\n";
    
    // Тестування специфічних функцій для кожного кроку
    switch ($step) {
        case 1:
            // Крок 1: Ліцензійна угода
            if (strpos($content, 'acceptLicense') !== false) {
                echo "✅ Checkbox для прийняття ліцензії є\n";
                $success[] = "Крок 1: Checkbox ліцензії";
            } else {
                echo "❌ Checkbox для прийняття ліцензії відсутній\n";
                $errors[] = "Крок 1: Відсутній checkbox";
            }
            
            if (strpos($content, 'fa-spinner fa-spin') !== false) {
                echo "✅ Обробка кнопки submit додана\n";
                $success[] = "Крок 1: Кнопка submit";
            } else {
                echo "❌ Обробка кнопки submit відсутня\n";
                $errors[] = "Крок 1: Кнопка не оброблена";
            }
            break;
            
        case 2:
            // Крок 2: Перевірка системи
            if (strpos($content, 'checkSystemRequirements') !== false) {
                echo "✅ Функція перевірки системи є\n";
                $success[] = "Крок 2: Перевірка системи";
            } else {
                echo "❌ Функція перевірки системи відсутня\n";
                $errors[] = "Крок 2: Відсутня перевірка";
            }
            
            if (strpos($content, 'fa-spinner fa-spin') !== false) {
                echo "✅ Обробка кнопки submit додана\n";
                $success[] = "Крок 2: Кнопка submit";
            } else {
                echo "❌ Обробка кнопки submit відсутня\n";
                $errors[] = "Крок 2: Кнопка не оброблена";
            }
            break;
            
        case 3:
            // Крок 3: База даних
            if (strpos($content, 'testConnection') !== false) {
                echo "✅ Тест з'єднання з БД є\n";
                $success[] = "Крок 3: Тест БД";
            } else {
                echo "❌ Тест з'єднання з БД відсутній\n";
                $errors[] = "Крок 3: Відсутній тест БД";
            }
            
            if (strpos($content, 'submitBtn') !== false && strpos($content, 'fa-spinner') !== false) {
                echo "✅ Обробка кнопки submit виправлена\n";
                $success[] = "Крок 3: Кнопка виправлена";
            } else {
                echo "❌ Обробка кнопки submit не виправлена\n";
                $errors[] = "Крок 3: Кнопка не виправлена";
            }
            break;
            
        case 4:
            // Крок 4: Налаштування сайту (без мови/часового поясу)
            if (strpos($content, 'language') === false && strpos($content, 'timezone') === false) {
                echo "✅ Мова та часовий пояс видалені\n";
                $success[] = "Крок 4: Очищено від мови/часового поясу";
            } else {
                echo "❌ Мова або часовий пояс все ще присутні\n";
                $errors[] = "Крок 4: Не очищено";
            }
            
            if (strpos($content, 'submitBtn') !== false && strpos($content, 'fa-spinner') !== false) {
                echo "✅ Обробка кнопки submit виправлена\n";
                $success[] = "Крок 4: Кнопка виправлена";
            } else {
                echo "❌ Обробка кнопки submit не виправлена\n";
                $errors[] = "Крок 4: Кнопка не виправлена";
            }
            break;
            
        case 5:
            // Крок 5: Додаткові налаштування
            if (strpos($content, 'default_language') !== false && strpos($content, 'timezone') !== false) {
                echo "✅ Мова та часовий пояс присутні\n";
                $success[] = "Крок 5: Мова/часовий пояс";
            } else {
                echo "❌ Мова або часовий пояс відсутні\n";
                $errors[] = "Крок 5: Відсутні налаштування";
            }
            
            if (strpos($content, 'will-change: transform') !== false) {
                echo "✅ Оптимізація анімацій додана\n";
                $success[] = "Крок 5: Анімації оптимізовані";
            } else {
                echo "❌ Оптимізація анімацій відсутня\n";
                $warnings[] = "Крок 5: Анімації не оптимізовані";
            }
            
            if (strpos($content, 'submitBtn') !== false && strpos($content, 'fa-spinner') !== false) {
                echo "✅ Обробка кнопки submit виправлена\n";
                $success[] = "Крок 5: Кнопка виправлена";
            } else {
                echo "❌ Обробка кнопки submit не виправлена\n";
                $errors[] = "Крок 5: Кнопка не виправлена";
            }
            break;
            
        case 6:
            // Крок 6: Налаштування теми
            $gradientCount = substr_count($content, "'gradient-");
            if ($gradientCount >= 30) {
                echo "✅ $gradientCount градієнтів доступно\n";
                $success[] = "Крок 6: $gradientCount градієнтів";
            } else {
                echo "❌ Тільки $gradientCount градієнтів (потрібно 30+)\n";
                $errors[] = "Крок 6: Недостатньо градієнтів";
            }
            
            if (strpos($content, 'updateTimeout') !== false) {
                echo "✅ Дебаунсинг анімацій додано\n";
                $success[] = "Крок 6: Дебаунсинг";
            } else {
                echo "❌ Дебаунсинг анімацій відсутній\n";
                $warnings[] = "Крок 6: Відсутній дебаунсинг";
            }
            
            if (strpos($content, 'themeForm') !== false && strpos($content, 'fa-spinner') !== false) {
                echo "✅ Обробка кнопки submit додана\n";
                $success[] = "Крок 6: Кнопка submit";
            } else {
                echo "❌ Обробка кнопки submit відсутня\n";
                $errors[] = "Крок 6: Кнопка не оброблена";
            }
            break;
            
        case 7:
            // Крок 7: Адміністратор
            if (strpos($content, 'checkPasswordStrength') !== false) {
                echo "✅ Перевірка сили паролю є\n";
                $success[] = "Крок 7: Перевірка паролю";
            } else {
                echo "❌ Перевірка сили паролю відсутня\n";
                $errors[] = "Крок 7: Відсутня перевірка паролю";
            }
            
            if (strpos($content, 'submitBtn') !== false && strpos($content, 'fa-spinner') !== false) {
                echo "✅ Обробка кнопки submit виправлена\n";
                $success[] = "Крок 7: Кнопка виправлена";
            } else {
                echo "❌ Обробка кнопки submit не виправлена\n";
                $errors[] = "Крок 7: Кнопка не виправлена";
            }
            break;
            
        case 8:
            // Крок 8: Встановлення
            $exitCount = substr_count($content, 'exit();');
            if ($exitCount >= 2 && $exitCount <= 3) {
                echo "✅ JSON exit() виклики правильні ($exitCount)\n";
                $success[] = "Крок 8: JSON exit() виправлені";
            } else {
                echo "❌ Неправильна кількість exit() викликів ($exitCount)\n";
                $errors[] = "Крок 8: Неправильні exit()";
            }
            
            if (strpos($content, '<div class="step-content') !== false) {
                echo "✅ HTML контент присутній\n";
                $success[] = "Крок 8: HTML контент";
            } else {
                echo "❌ HTML контент відсутній\n";
                $errors[] = "Крок 8: Відсутній HTML";
            }
            
            if (strpos($content, 'startInstallation') !== false) {
                echo "✅ JavaScript установки є\n";
                $success[] = "Крок 8: JavaScript установки";
            } else {
                echo "❌ JavaScript установки відсутній\n";
                $errors[] = "Крок 8: Відсутній JavaScript";
            }
            break;
            
        case 9:
            // Крок 9: Завершення
            if (strpos($content, 'Вітаємо') !== false || strpos($content, 'завершен') !== false) {
                echo "✅ Повідомлення про завершення є\n";
                $success[] = "Крок 9: Повідомлення завершення";
            } else {
                echo "❌ Повідомлення про завершення відсутнє\n";
                $errors[] = "Крок 9: Відсутнє повідомлення";
            }
            break;
    }
    
    // Загальні перевірки для всіх кроків
    if (strpos($content, '<?php') !== false || strpos($content, '<div') !== false) {
        echo "✅ Правильна структура файлу\n";
        $success[] = "Крок $step: Структура файлу";
    } else {
        echo "❌ Неправильна структура файлу\n";
        $errors[] = "Крок $step: Неправильна структура";
    }
    
    echo "\n";
}

// Тестування основного файлу інсталятора
echo "🔧 ТЕСТУВАННЯ ОСНОВНОГО ФАЙЛУ ІНСТАЛЯТОРА:\n\n";

$installIndexContent = file_get_contents('install/index.php');

// Перевірка кількості кроків
if (strpos($installIndexContent, '$maxSteps = 9') !== false) {
    echo "✅ Кількість кроків правильна (9)\n";
    $success[] = "Інсталятор: 9 кроків";
} else {
    echo "❌ Неправильна кількість кроків\n";
    $errors[] = "Інсталятор: Неправильна кількість кроків";
}

// Перевірка обробки кроків
$requiredCases = ['case 4:', 'case 5:', 'case 6:', 'case 7:', 'case 8:'];
$missingCases = [];
foreach ($requiredCases as $case) {
    if (strpos($installIndexContent, $case) === false) {
        $missingCases[] = $case;
    }
}

if (empty($missingCases)) {
    echo "✅ Всі case обробники присутні\n";
    $success[] = "Інсталятор: Case обробники";
} else {
    echo "❌ Відсутні case обробники: " . implode(', ', $missingCases) . "\n";
    $errors[] = "Інсталятор: Відсутні case обробники";
}

// Перевірка функції створення БД
if (strpos($installIndexContent, 'createDatabaseAndSchema') !== false) {
    echo "✅ Функція створення БД присутня\n";
    $success[] = "Інсталятор: Функція БД";
} else {
    echo "❌ Функція створення БД відсутня\n";
    $errors[] = "Інсталятор: Відсутня функція БД";
}

echo "\n";

// ПІДСУМОК
echo "=== ПІДСУМОК ТЕСТУВАННЯ ===\n\n";

echo "✅ УСПІШНО (" . count($success) . "):\n";
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
    echo "\n🎉 ВСІ ТЕСТИ ПРОЙДЕНІ УСПІШНО!\n";
    echo "💪 ІНСТАЛЯТОР ГОТОВИЙ ДО ВИКОРИСТАННЯ!\n";
}

echo "\n=== СТАТИСТИКА ===\n";
echo "• Успішних тестів: " . count($success) . "\n";
echo "• Попереджень: " . count($warnings) . "\n";  
echo "• Помилок: " . count($errors) . "\n";
echo "• Готовність інсталятора: " . round((count($success) / (count($success) + count($warnings) + count($errors))) * 100, 1) . "%\n";

echo "\n📋 РЕКОМЕНДАЦІЇ:\n";
if (empty($errors)) {
    echo "• Запустіть інсталятор в браузері: http://your-domain/install/\n";
    echo "• Протестуйте кожен крок послідовно\n";
    echo "• Перевірте роботу кнопок при неправильних даних\n";
    echo "• Перевірте плавність анімацій при виборі тем/мови\n";
} else {
    echo "• Виправте знайдені помилки\n";
    echo "• Повторіть тестування\n";
}

echo "\n=== ТЕСТ ЗАВЕРШЕНО ===\n";
?>