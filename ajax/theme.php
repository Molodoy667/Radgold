<?php
session_start();
header('Content-Type: application/json');

require_once '../config/database.php';
require_once '../includes/theme.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    Theme::init($db);
    
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'change_theme':
            $gradient = $_POST['gradient'] ?? 'gradient-2';
            $dark_mode = isset($_POST['dark_mode']) ? (bool)$_POST['dark_mode'] : false;
            
            // Валидация градиента
            $available_gradients = array_keys(Theme::getGradients());
            if (!in_array($gradient, $available_gradients)) {
                $gradient = 'gradient-2';
            }
            
            // Сохраняем тему
            if (Theme::saveTheme($gradient, $dark_mode)) {
                // Генерируем новый CSS
                $css = Theme::generateCSS();
                $theme = Theme::getCurrentTheme();
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Тему змінено успішно',
                    'css' => $css,
                    'theme' => $theme
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Помилка збереження теми'
                ]);
            }
            break;
            
        case 'get_current_theme':
            $theme = Theme::getCurrentTheme();
            $css = Theme::generateCSS();
            
            echo json_encode([
                'success' => true,
                'theme' => $theme,
                'css' => $css
            ]);
            break;
            
        case 'reset_theme':
            // Сбрасываем на тему по умолчанию
            if (Theme::saveTheme('gradient-2', false)) {
                $css = Theme::generateCSS();
                $theme = Theme::getCurrentTheme();
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Тему скинуто до значень за замовчуванням',
                    'css' => $css,
                    'theme' => $theme
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Помилка скидання теми'
                ]);
            }
            break;
            
        case 'get_gradients':
            $gradients = Theme::getGradients();
            $current_theme = Theme::getCurrentTheme();
            
            echo json_encode([
                'success' => true,
                'gradients' => $gradients,
                'current' => $current_theme['gradient']
            ]);
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Невірна дія'
            ]);
            break;
    }
    
} catch (Exception $e) {
    error_log("Theme API error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Системна помилка'
    ]);
}
?>