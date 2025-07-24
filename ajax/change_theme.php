<?php
session_start();

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

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
            throw new Exception('Invalid theme: ' . $theme);
        }
        
        $_SESSION['current_theme'] = $theme;
        
        // Встановлюємо cookie на 30 днів
        setcookie('current_theme', $theme, time() + (30 * 24 * 60 * 60), '/');
        
        error_log("Theme changed to: " . $theme);
        echo json_encode([
            'success' => true, 
            'message' => 'Theme updated successfully',
            'theme' => $theme
        ]);
        exit;
    }
    
    // Якщо змінюємо градієнт
    if (!empty($gradient)) {
        // Список дозволених градієнтів (30 штук)
        $allowedGradients = [
            'gradient-1', 'gradient-2', 'gradient-3', 'gradient-4', 'gradient-5',
            'gradient-6', 'gradient-7', 'gradient-8', 'gradient-9', 'gradient-10',
            'gradient-11', 'gradient-12', 'gradient-13', 'gradient-14', 'gradient-15',
            'gradient-16', 'gradient-17', 'gradient-18', 'gradient-19', 'gradient-20',
            'gradient-21', 'gradient-22', 'gradient-23', 'gradient-24', 'gradient-25',
            'gradient-26', 'gradient-27', 'gradient-28', 'gradient-29', 'gradient-30'
        ];
        
        if (!in_array($gradient, $allowedGradients)) {
            throw new Exception('Invalid gradient: ' . $gradient);
        }
        
        $_SESSION['current_gradient'] = $gradient;
        
        // Встановлюємо cookie на 30 днів
        setcookie('current_gradient', $gradient, time() + (30 * 24 * 60 * 60), '/');
        
        error_log("Gradient changed to: " . $gradient);
        echo json_encode([
            'success' => true, 
            'message' => 'Gradient updated successfully',
            'gradient' => $gradient
        ]);
        exit;
    }
    
    throw new Exception('No theme or gradient specified');
    
} catch (Exception $e) {
    error_log("Theme change error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}
?>
