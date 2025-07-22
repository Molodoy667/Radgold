<?php
require_once '../core/config.php';
require_once '../core/database.php';
require_once '../core/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
}

$input = json_decode(file_get_contents('php://input'), true);
$action = sanitize($input['action'] ?? '');

try {
    $db = Database::getInstance();
    
    switch ($action) {
        case 'change_theme':
            $theme = sanitize($input['theme'] ?? '');
            
            if (!in_array($theme, ['light', 'dark'])) {
                throw new Exception('Invalid theme');
            }
            
            $result = $db->update(
                "UPDATE theme_settings SET current_theme = ? WHERE id = 1",
                [$theme]
            );
            
            if ($result !== false) {
                $_SESSION['theme'] = $theme;
                jsonResponse(['success' => true, 'message' => 'Theme updated successfully']);
            } else {
                throw new Exception('Failed to update theme');
            }
            break;
            
        case 'change_gradient':
            $gradient = sanitize($input['gradient'] ?? '');
            
            // Перевіряємо чи градієнт існує
            $gradients = generateGradients();
            if (!array_key_exists($gradient, $gradients)) {
                throw new Exception('Invalid gradient');
            }
            
            $result = $db->update(
                "UPDATE theme_settings SET current_gradient = ? WHERE id = 1",
                [$gradient]
            );
            
            if ($result !== false) {
                $_SESSION['gradient'] = $gradient;
                jsonResponse(['success' => true, 'message' => 'Gradient updated successfully']);
            } else {
                throw new Exception('Failed to update gradient');
            }
            break;
            
        default:
            throw new Exception('Invalid action');
    }
    
} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => $e->getMessage()], 400);
}
?>
