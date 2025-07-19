<?php
session_start();

// Перевірка авторизації адміна
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    die('Access denied');
}

require_once '../config/config.php';
require_once '../config/database.php';

echo "<h2>Тест сохранения настроек темы</h2>";

// Підключення до бази даних
$database = new Database();
$db = $database->getConnection();

// Показываем текущие настройки
echo "<h3>Текущие настройки:</h3>";
$settings_query = "SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE '%theme%' OR setting_key = 'default_theme_gradient'";
$settings_stmt = $db->prepare($settings_query);
$settings_stmt->execute();
$settings = $settings_stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1'>";
echo "<tr><th>Ключ</th><th>Значение</th></tr>";
foreach ($settings as $setting) {
    echo "<tr><td>{$setting['setting_key']}</td><td>{$setting['setting_value']}</td></tr>";
}
echo "</table>";

// Форма для тестирования
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>Получены данные:</h3>";
    echo "<pre>" . print_r($_POST, true) . "</pre>";
    
    // Пытаемся сохранить
    if (isset($_POST['default_theme_gradient'])) {
        $gradient = $_POST['default_theme_gradient'];
        
        try {
            $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) 
                                 ON DUPLICATE KEY UPDATE setting_value = ?");
            $result = $stmt->execute(['default_theme_gradient', $gradient, $gradient]);
            
            if ($result) {
                echo "<p style='color: green;'>✅ Градиент сохранен: $gradient</p>";
                
                // Проверяем что сохранилось
                $check_stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = 'default_theme_gradient'");
                $check_stmt->execute();
                $saved = $check_stmt->fetchColumn();
                echo "<p>Сохраненное значение: $saved</p>";
            } else {
                echo "<p style='color: red;'>❌ Ошибка сохранения</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Исключение: " . $e->getMessage() . "</p>";
        }
    }
    
    // Перезагружаем настройки
    echo "<script>setTimeout(() => location.reload(), 2000);</script>";
}

// Показываем доступные градиенты
$gradients = Theme::getGradients();
echo "<h3>Доступные градиенты:</h3>";
echo "<form method='POST'>";
echo "<select name='default_theme_gradient'>";
foreach ($gradients as $id => $data) {
    $current = Settings::get('default_theme_gradient', 'gradient-2');
    $selected = $current === $id ? 'selected' : '';
    echo "<option value='$id' $selected>$id - {$data[2]}</option>";
}
echo "</select><br><br>";
echo "<input type='submit' value='Сохранить'>";
echo "</form>";

echo "<br><br>";
echo "<a href='settings.php'>← Назад к настройкам</a>";
?>