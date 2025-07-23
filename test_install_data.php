<?php
// Тестовий файл для перевірки збереження даних установки
session_start();

// Підключення до бази даних
require_once __DIR__ . '/core/config.php';
require_once __DIR__ . '/core/functions.php';

echo "<h2>Тест збереження даних установки</h2>";
echo "<p><strong>Важливо:</strong> Дані адміністратора зберігаються в таблиці <code>users</code>, а НЕ в <code>site_settings</code>!</p>";

// Перевіряємо що записано в site_settings
echo "<h3>Налаштування сайту (site_settings):</h3>";
try {
    $result = $db->query("SELECT * FROM site_settings ORDER BY setting_key");
    if ($result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Ключ</th><th>Значення</th><th>Опис</th><th>Тип</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['setting_key']) . "</td>";
            echo "<td>" . htmlspecialchars($row['value']) . "</td>";
            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
            echo "<td>" . htmlspecialchars($row['type']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>Таблиця site_settings порожня!</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Помилка: " . $e->getMessage() . "</p>";
}

// Перевіряємо адміністратора
echo "<h3>Користувачі (users):</h3>";
try {
    $result = $db->query("SELECT id, username, first_name, last_name, email, role, user_type, group_id, status, created_at FROM users ORDER BY id");
    if ($result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Username</th><th>Ім'я</th><th>Прізвище</th><th>Email</th><th>Role</th><th>Type</th><th>Group ID</th><th>Status</th><th>Створено</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['username']) . "</td>";
            echo "<td>" . htmlspecialchars($row['first_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['last_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . htmlspecialchars($row['role']) . "</td>";
            echo "<td>" . htmlspecialchars($row['user_type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['group_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>Таблиця users порожня!</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Помилка: " . $e->getMessage() . "</p>";
}

// Перевіряємо групи користувачів
echo "<h3>Групи користувачів (user_groups):</h3>";
try {
    $result = $db->query("SELECT id, name, slug, description, is_default, is_system, color, sort_order FROM user_groups ORDER BY sort_order");
    if ($result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Назва</th><th>Slug</th><th>Опис</th><th>За замовчуванням</th><th>Системна</th><th>Колір</th><th>Порядок</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['slug']) . "</td>";
            echo "<td>" . htmlspecialchars($row['description']) . "</td>";
            echo "<td>" . ($row['is_default'] ? 'Так' : 'Ні') . "</td>";
            echo "<td>" . ($row['is_system'] ? 'Так' : 'Ні') . "</td>";
            echo "<td style='background-color: " . htmlspecialchars($row['color']) . "; color: white;'>" . htmlspecialchars($row['color']) . "</td>";
            echo "<td>" . htmlspecialchars($row['sort_order']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>Таблиця user_groups порожня!</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Помилка: " . $e->getMessage() . "</p>";
}

// Перевіряємо переклади
echo "<h3>Переклади (translations) - перші 10:</h3>";
try {
    $result = $db->query("SELECT * FROM translations ORDER BY id LIMIT 10");
    if ($result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Ключ</th><th>UA</th><th>RU</th><th>EN</th><th>Категорія</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['translation_key']) . "</td>";
            echo "<td>" . htmlspecialchars($row['uk']) . "</td>";
            echo "<td>" . htmlspecialchars($row['ru']) . "</td>";
            echo "<td>" . htmlspecialchars($row['en']) . "</td>";
            echo "<td>" . htmlspecialchars($row['category']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        $total_result = $db->query("SELECT COUNT(*) as total FROM translations");
        $total = $total_result->fetch_assoc()['total'];
        echo "<p>Всього перекладів: <strong>$total</strong></p>";
    } else {
        echo "<p style='color: red;'>Таблиця translations порожня!</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Помилка: " . $e->getMessage() . "</p>";
}

// Перевіряємо дані сесії
echo "<h3>Дані сесії установки:</h3>";
if (isset($_SESSION['install_data'])) {
    echo "<pre>" . htmlspecialchars(print_r($_SESSION['install_data'], true)) . "</pre>";
} else {
    echo "<p style='color: orange;'>Дані сесії відсутні (установка завершена)</p>";
}

// Перевіряємо файл конфігурації
echo "<h3>Файл конфігурації:</h3>";
if (file_exists(__DIR__ . '/core/config.php')) {
    echo "<p style='color: green;'>Файл core/config.php існує</p>";
    
    // Показуємо константи
    echo "<h4>Константи:</h4>";
    echo "<ul>";
    if (defined('SITE_URL')) echo "<li>SITE_URL: " . SITE_URL . "</li>";
    if (defined('SITE_NAME')) echo "<li>SITE_NAME: " . SITE_NAME . "</li>";
    if (defined('SITE_EMAIL')) echo "<li>SITE_EMAIL: " . SITE_EMAIL . "</li>";
    if (defined('SITE_DESCRIPTION')) echo "<li>SITE_DESCRIPTION: " . SITE_DESCRIPTION . "</li>";
    if (defined('DB_HOST')) echo "<li>DB_HOST: " . DB_HOST . "</li>";
    if (defined('DB_USER')) echo "<li>DB_USER: " . DB_USER . "</li>";
    if (defined('DB_NAME')) echo "<li>DB_NAME: " . DB_NAME . "</li>";
    echo "</ul>";
} else {
    echo "<p style='color: red;'>Файл core/config.php не існує!</p>";
}

// Перевіряємо .installed файл
echo "<h3>Файл установки:</h3>";
if (file_exists(__DIR__ . '/.installed')) {
    $install_date = file_get_contents(__DIR__ . '/.installed');
    echo "<p style='color: green;'>Установка завершена: " . htmlspecialchars($install_date) . "</p>";
} else {
    echo "<p style='color: red;'>Файл .installed не існує!</p>";
}

// Показуємо маппінг ключових полів
echo "<h3>Маппінг ключових полів:</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Крок форми</th><th>Поле форми</th><th>Таблиця</th><th>Поле БД</th><th>Поточне значення</th></tr>";

// Основні поля сайту
$site_fields = [
    ['Крок 4', 'site_name', 'site_settings', 'site_title', getSiteSetting('site_title', 'не встановлено')],
    ['Крок 4', 'site_description', 'site_settings', 'site_description', getSiteSetting('site_description', 'не встановлено')],
    ['Крок 4', 'contact_email', 'site_settings', 'contact_email', getSiteSetting('contact_email', 'не встановлено')],
    ['Крок 5', 'default_language', 'site_settings', 'language', getSiteSetting('language', 'не встановлено')],
    ['Крок 5', 'enable_animations', 'site_settings', 'enable_animations', getSiteSetting('enable_animations', 'не встановлено')],
    ['Крок 6', 'default_theme', 'site_settings', 'current_theme', getSiteSetting('current_theme', 'не встановлено')]
];

foreach ($site_fields as $field) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($field[0]) . "</td>";
    echo "<td>" . htmlspecialchars($field[1]) . "</td>";
    echo "<td>" . htmlspecialchars($field[2]) . "</td>";
    echo "<td>" . htmlspecialchars($field[3]) . "</td>";
    echo "<td>" . htmlspecialchars($field[4]) . "</td>";
    echo "</tr>";
}

// Показуємо адміна з users
try {
    $admin_result = $db->query("SELECT username, email, first_name, last_name, role, group_id FROM users WHERE role = 'admin' LIMIT 1");
    if ($admin_result->num_rows > 0) {
        $admin = $admin_result->fetch_assoc();
        $admin_fields = [
            ['Крок 7', 'admin_login', 'users', 'username', $admin['username']],
            ['Крок 7', 'admin_email', 'users', 'email', $admin['email']],
            ['Крок 7', 'admin_first_name', 'users', 'first_name', $admin['first_name']],
            ['Крок 7', 'admin_last_name', 'users', 'last_name', $admin['last_name']],
            ['Система', 'auto', 'users', 'group_id', $admin['group_id'] . ' (Супер Адмін)']
        ];
        
        foreach ($admin_fields as $field) {
            echo "<tr style='background-color: #e8f5e8;'>";
            echo "<td>" . htmlspecialchars($field[0]) . "</td>";
            echo "<td>" . htmlspecialchars($field[1]) . "</td>";
            echo "<td>" . htmlspecialchars($field[2]) . "</td>";
            echo "<td>" . htmlspecialchars($field[3]) . "</td>";
            echo "<td>" . htmlspecialchars($field[4]) . "</td>";
            echo "</tr>";
        }
    }
} catch (Exception $e) {
    echo "<tr><td colspan='5' style='color: red;'>Помилка отримання адміна: " . $e->getMessage() . "</td></tr>";
}

echo "</table>";

echo "<hr>";
echo "<p><a href='admin/'>Перейти в адмін-панель</a> | <a href='./'>Перейти на головну</a> | <a href='install_fields_mapping.md'>Документація полів</a></p>";
?>