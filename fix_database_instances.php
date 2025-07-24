<?php
// Скрипт для виправлення new Database() на Database::getInstance()

echo "🔧 Починаю виправлення Database instances...\n";

// Список файлів для виправлення (найкритичніші)
$criticalFiles = [
    'core/functions.php',
    'ajax/user_ads.php',
    'ajax/toggle_favorite.php',
    'ajax/consultation.php',
    'ajax/admin_users.php',
    'ajax/google_auth.php',
    'ajax/admin_ads.php',
    'ajax/chat.php',
    'ajax/admin_stats.php',
    'ajax/maps.php',
    'ajax/notifications.php'
];

$totalFixed = 0;

foreach ($criticalFiles as $file) {
    if (file_exists($file)) {
        echo "📝 Обробляю файл: $file\n";
        
        $content = file_get_contents($file);
        $originalContent = $content;
        
        // Виправляємо new Database() на Database::getInstance()
        $content = str_replace('$db = new Database();', '$db = Database::getInstance();', $content);
        $content = str_replace('$database = new Database();', '$database = Database::getInstance();', $content);
        $content = str_replace('new Database()', 'Database::getInstance()', $content);
        
        if ($content !== $originalContent) {
            file_put_contents($file, $content);
            $count = substr_count($originalContent, 'new Database()');
            echo "   ✅ Виправлено $count входжень в $file\n";
            $totalFixed += $count;
        } else {
            echo "   ℹ️ Немає змін в $file\n";
        }
    } else {
        echo "   ❌ Файл не знайдено: $file\n";
    }
}

echo "\n🎉 Загалом виправлено $totalFixed входжень!\n";
echo "✅ Тепер Database використовується як Singleton pattern\n";
?>