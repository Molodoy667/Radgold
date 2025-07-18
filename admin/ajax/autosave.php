<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Перевірка авторизації адміна
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    echo json_encode(['success' => false, 'message' => 'Доступ заборонено']);
    exit();
}

require_once '../../config/config.php';
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        $updated_count = 0;
        
        foreach ($_POST as $key => $value) {
            if ($key !== 'submit' && $key !== 'action') {
                $clean_value = clean_input($value);
                
                $update_query = "UPDATE settings SET setting_value = ? WHERE setting_key = ?";
                $update_stmt = $db->prepare($update_query);
                if ($update_stmt->execute([$clean_value, $key])) {
                    $updated_count++;
                }
            }
        }
        
        // Очищуємо кеш налаштувань
        Settings::clearCache();
        
        echo json_encode([
            'success' => true, 
            'message' => "Автозбереження: оновлено {$updated_count} налаштувань",
            'timestamp' => date('H:i:s')
        ]);
        
    } catch (Exception $e) {
        error_log("Autosave error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Помилка автозбереження']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Невірний метод запиту']);
}
?>