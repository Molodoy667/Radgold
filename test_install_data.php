<?php
// Тестовий файл для перевірки збереження даних установки
session_start();

// Підключення до бази даних
require_once __DIR__ . '/core/config.php';
require_once __DIR__ . '/core/functions.php';

echo "<h2>Тест збереження даних установки</h2>";

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

echo "<hr>";
echo "<p><a href='admin/'>Перейти в адмін-панель</a> | <a href='./'>Перейти на головну</a></p>";
?>