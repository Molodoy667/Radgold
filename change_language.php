<?php
session_start();

// Підключення до бази даних
require_once __DIR__ . '/core/config.php';
require_once __DIR__ . '/core/functions.php';

// Перевіряємо чи це AJAX запит
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
    header('Content-Type: application/json; charset=utf-8');
    
    $language = trim($_POST['language'] ?? '');
    
    // Валідація мови
    $allowed_languages = ['uk', 'ru', 'en'];
    if (!in_array($language, $allowed_languages)) {
        echo json_encode([
            'success' => false, 
            'message' => 'Недопустима мова. Дозволені: ' . implode(', ', $allowed_languages)
        ]);
        exit;
    }
    
    try {
        // Зберігаємо мову в сесії
        $_SESSION['current_language'] = $language;
        
        // Якщо користувач авторизований, зберігаємо в базі
        if (isLoggedIn() && isset($_SESSION['user_id'])) {
            $user_id = intval($_SESSION['user_id']);
            
            // Перевіряємо чи існує стовпець language в таблиці users
            $result = $db->query("SHOW COLUMNS FROM users LIKE 'language'");
            if ($result->num_rows == 0) {
                // Додаємо стовпець language якщо не існує
                $db->query("ALTER TABLE users ADD COLUMN language VARCHAR(5) DEFAULT 'uk' AFTER email");
            }
            
            // Оновлюємо мову користувача
            $stmt = $db->prepare("UPDATE users SET language = ? WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("si", $language, $user_id);
                $stmt->execute();
                $stmt->close();
            }
        }
        
        // Також оновлюємо мову за замовчуванням в налаштуваннях сайту
        $stmt = $db->prepare("UPDATE site_settings SET value = ? WHERE setting_key = 'language'");
        if ($stmt) {
            $stmt->bind_param("s", $language);
            $stmt->execute();
            $stmt->close();
        } else {
            // Якщо настройка не існує, створюємо її
            $stmt = $db->prepare("INSERT INTO site_settings (setting_key, value) VALUES ('language', ?) ON DUPLICATE KEY UPDATE value = ?");
            if ($stmt) {
                $stmt->bind_param("ss", $language, $language);
                $stmt->execute();
                $stmt->close();
            }
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Мову змінено успішно',
            'language' => $language
        ]);
        
    } catch (Exception $e) {
        error_log("Language change error: " . $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => 'Помилка зміни мови: ' . $e->getMessage()
        ]);
    }
    
} else {
    // Якщо це не AJAX запит, перенаправляємо на головну
    $language = $_GET['lang'] ?? $_POST['language'] ?? 'uk';
    $allowed_languages = ['uk', 'ru', 'en'];
    
    if (in_array($language, $allowed_languages)) {
        $_SESSION['current_language'] = $language;
        
        // Зберігаємо в базі якщо користувач авторизований
        if (isLoggedIn() && isset($_SESSION['user_id'])) {
            try {
                $user_id = intval($_SESSION['user_id']);
                
                // Перевіряємо чи існує стовпець language
                $result = $db->query("SHOW COLUMNS FROM users LIKE 'language'");
                if ($result->num_rows == 0) {
                    $db->query("ALTER TABLE users ADD COLUMN language VARCHAR(5) DEFAULT 'uk' AFTER email");
                }
                
                $stmt = $db->prepare("UPDATE users SET language = ? WHERE id = ?");
                if ($stmt) {
                    $stmt->bind_param("si", $language, $user_id);
                    $stmt->execute();
                    $stmt->close();
                }
            } catch (Exception $e) {
                error_log("Language update error: " . $e->getMessage());
            }
        }
    }
    
    // Перенаправляємо назад або на головну
    $redirect_url = $_SERVER['HTTP_REFERER'] ?? (defined('SITE_URL') ? SITE_URL : '/');
    header('Location: ' . $redirect_url);
    exit;
}
?>