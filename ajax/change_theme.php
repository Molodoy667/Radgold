<?php
session_start();
require_once '../core/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Отримуємо дані з POST
$theme = $_POST['theme'] ?? '';
$gradient = $_POST['gradient'] ?? '';

try {
    // Якщо змінюємо тему
    if (!empty($theme)) {
        if (!in_array($theme, ['light', 'dark'])) {
            throw new Exception('Invalid theme');
        }
        
        $_SESSION['current_theme'] = $theme;
        
        // Також спробуємо зберегти в БД якщо можливо
        try {
            if (isset($GLOBALS['db'])) {
                $db = $GLOBALS['db'];
                $stmt = $db->prepare("UPDATE theme_settings SET current_theme = ? WHERE id = 1");
                if ($stmt) {
                    $stmt->bind_param("s", $theme);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        } catch (Exception $e) {
            // Ігноруємо помилки БД, головне що зберегли в сесії
            error_log("Theme DB update error: " . $e->getMessage());
        }
        
        echo json_encode(['success' => true, 'message' => 'Theme updated successfully']);
        exit;
    }
    
    // Якщо змінюємо градієнт
    if (!empty($gradient)) {
        // Список дозволених градієнтів
        $allowedGradients = [
            'gradient-1', 'gradient-2', 'gradient-3', 'gradient-4', 'gradient-5',
            'gradient-6', 'gradient-7', 'gradient-8', 'gradient-9', 'gradient-10'
        ];
        
        if (!in_array($gradient, $allowedGradients)) {
            throw new Exception('Invalid gradient');
        }
        
        $_SESSION['current_gradient'] = $gradient;
        
        // Також спробуємо зберегти в БД якщо можливо
        try {
            if (isset($GLOBALS['db'])) {
                $db = $GLOBALS['db'];
                $stmt = $db->prepare("UPDATE theme_settings SET current_gradient = ? WHERE id = 1");
                if ($stmt) {
                    $stmt->bind_param("s", $gradient);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        } catch (Exception $e) {
            // Ігноруємо помилки БД, головне що зберегли в сесії
            error_log("Gradient DB update error: " . $e->getMessage());
        }
        
        echo json_encode(['success' => true, 'message' => 'Gradient updated successfully']);
        exit;
    }
    
    throw new Exception('No action specified');
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}
?>
