<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../config/config.php';

// Підключення до бази даних
$database = new Database();
$db = $database->getConnection();

// Перевіряємо чи дозволена зміна теми
if (!Theme::isThemeSwitcherEnabled()) {
    echo json_encode(['success' => false, 'message' => 'Зміна тем вимкнена']);
    exit;
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'change_theme':
        $gradient = $_POST['gradient'] ?? 'gradient-2';
        $dark_mode = isset($_POST['dark_mode']) && $_POST['dark_mode'] === 'true';
        
        // Валідація градієнту
        $available_gradients = array_keys(Theme::getGradients());
        if (!in_array($gradient, $available_gradients)) {
            echo json_encode(['success' => false, 'message' => 'Невірний градієнт']);
            exit;
        }
        
        // Збереження теми
        $result = Theme::saveTheme($gradient, $dark_mode);
        
        if ($result) {
            // Генеруємо новий CSS
            $css = Theme::generateCSS();
            echo json_encode([
                'success' => true, 
                'message' => 'Тему змінено',
                'css' => $css,
                'theme' => [
                    'gradient' => $gradient,
                    'dark_mode' => $dark_mode
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Не вдалося зберегти тему']);
        }
        break;
        
    case 'reset_theme':
        // Скидаємо на стандартну тему
        $default_gradient = Settings::get('default_theme_gradient', 'gradient-2');
        $default_dark_mode = Settings::get('default_dark_mode', false);
        
        $result = Theme::saveTheme($default_gradient, $default_dark_mode);
        
        if ($result) {
            // Очищуємо кеш теми - повторно ініціалізуємо з поточним підключенням
            if (isset($db)) {
                Theme::init($db);
            }
            $css = Theme::generateCSS();
            
            echo json_encode([
                'success' => true, 
                'message' => 'Тему скинуто до стандартної',
                'css' => $css,
                'theme' => [
                    'gradient' => $default_gradient,
                    'dark_mode' => $default_dark_mode
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Не вдалося скинути тему']);
        }
        break;
        
    case 'get_theme':
        // Отримуємо поточну тему
        $theme = Theme::getCurrentTheme();
        echo json_encode([
            'success' => true,
            'theme' => $theme,
            'css' => Theme::generateCSS()
        ]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Невідома дія']);
        break;
}
?>