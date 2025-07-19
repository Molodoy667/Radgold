<?php
session_start();
header('Content-Type: application/json');

// Перевірка авторизації адміна
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    echo json_encode(['success' => false, 'message' => 'Доступ заборонено']);
    exit();
}

require_once '../../config/config.php';
require_once '../../config/database.php';

// Підключення до бази даних
$database = new Database();
$db = $database->getConnection();

try {
    $db->beginTransaction();
    
    $form_type = $_POST['form_type'] ?? '';
    $response = ['success' => false, 'message' => ''];
    
    switch ($form_type) {
        case 'generalForm':
            // Основні налаштування
            $fields = ['site_name', 'site_language', 'site_description', 'site_email', 'site_phone'];
            
            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    $value = clean_input($_POST[$field]);
                    
                    // Валідація
                    if ($field === 'site_name' && empty($value)) {
                        throw new Exception('Назва сайту обов\'язкова');
                    }
                    if ($field === 'site_email' && !empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        throw new Exception('Невірний формат email');
                    }
                    
                    $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) 
                                         ON DUPLICATE KEY UPDATE setting_value = ?");
                    $stmt->execute([$field, $value, $value]);
                }
            }
            
            $response['message'] = 'Основні налаштування успішно збережені!';
            break;
            
        case 'seoForm':
            // SEO налаштування
            $fields = ['meta_title', 'meta_description', 'meta_keywords', 'analytics_code'];
            
            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    $value = clean_input($_POST[$field]);
                    
                    $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) 
                                         ON DUPLICATE KEY UPDATE setting_value = ?");
                    $stmt->execute([$field, $value, $value]);
                }
            }
            
            $response['message'] = 'SEO налаштування успішно збережені!';
            break;
            
        case 'brandingForm':
            // Брендинг - обробка файлів
            $upload_dir = '../../assets/uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Обробка логотипа
            if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === UPLOAD_ERR_OK) {
                $file_info = pathinfo($_FILES['site_logo']['name']);
                $extension = strtolower($file_info['extension']);
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
                
                if (!in_array($extension, $allowed_extensions)) {
                    throw new Exception('Неприпустимий формат логотипу. Дозволені: ' . implode(', ', $allowed_extensions));
                }
                
                if ($_FILES['site_logo']['size'] > 2097152) {
                    throw new Exception('Розмір логотипу не повинен перевищувати 2MB');
                }
                
                $filename = 'logo_' . time() . '.' . $extension;
                $target_path = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $target_path)) {
                    // Видаляємо старий логотип
                    $old_logo_stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = 'site_logo'");
                    $old_logo_stmt->execute();
                    $old_logo = $old_logo_stmt->fetchColumn();
                    
                    if ($old_logo && file_exists('../../' . $old_logo)) {
                        unlink('../../' . $old_logo);
                    }
                    
                    $logo_path = 'assets/uploads/' . $filename;
                    $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('site_logo', ?) 
                                         ON DUPLICATE KEY UPDATE setting_value = ?");
                    $stmt->execute([$logo_path, $logo_path]);
                } else {
                    throw new Exception('Помилка завантаження логотипу');
                }
            }
            
            // Обробка фавікону
            if (isset($_FILES['site_favicon']) && $_FILES['site_favicon']['error'] === UPLOAD_ERR_OK) {
                $file_info = pathinfo($_FILES['site_favicon']['name']);
                $extension = strtolower($file_info['extension']);
                $allowed_extensions = ['ico', 'png', 'jpg', 'jpeg'];
                
                if (!in_array($extension, $allowed_extensions)) {
                    throw new Exception('Неприпустимий формат фавікону. Дозволені: ' . implode(', ', $allowed_extensions));
                }
                
                if ($_FILES['site_favicon']['size'] > 1048576) {
                    throw new Exception('Розмір фавікону не повинен перевищувати 1MB');
                }
                
                $filename = 'favicon_' . time() . '.' . $extension;
                $target_path = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['site_favicon']['tmp_name'], $target_path)) {
                    // Видаляємо старий фавікон
                    $old_favicon_stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = 'site_favicon'");
                    $old_favicon_stmt->execute();
                    $old_favicon = $old_favicon_stmt->fetchColumn();
                    
                    if ($old_favicon && file_exists('../../' . $old_favicon)) {
                        unlink('../../' . $old_favicon);
                    }
                    
                    $favicon_path = 'assets/uploads/' . $filename;
                    $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES ('site_favicon', ?) 
                                         ON DUPLICATE KEY UPDATE setting_value = ?");
                    $stmt->execute([$favicon_path, $favicon_path]);
                } else {
                    throw new Exception('Помилка завантаження фавікону');
                }
            }
            
            $response['message'] = 'Брендинг успішно оновлено!';
            break;
            
        case 'functionalityForm':
            // Функціональні налаштування
            $checkboxes = [
                'enable_registration', 'enable_comments', 'enable_search', 
                'enable_favorites', 'moderation_required', 'maintenance_mode'
            ];
            
            foreach ($checkboxes as $checkbox) {
                $value = isset($_POST[$checkbox]) ? '1' : '0';
                
                $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) 
                                     ON DUPLICATE KEY UPDATE setting_value = ?");
                $stmt->execute([$checkbox, $value, $value]);
            }
            
            $response['message'] = 'Функціональні налаштування успішно збережені!';
            break;
            
        default:
            throw new Exception('Невідомий тип форми');
    }
    
    // Логування
    $log_stmt = $db->prepare("INSERT INTO admin_logs (admin_id, action, description, ip_address, user_agent) 
                             VALUES (?, 'settings_update', ?, ?, ?)");
    $log_stmt->execute([
        $_SESSION['admin_id'],
        'Оновлення налаштувань: ' . $form_type,
        $_SERVER['REMOTE_ADDR'] ?? '',
        $_SERVER['HTTP_USER_AGENT'] ?? ''
    ]);
    
    $db->commit();
    
    // Очищуємо кеш налаштувань
    if (class_exists('Settings')) {
        Settings::clearCache();
    }
    
    $response['success'] = true;
    
} catch (Exception $e) {
    $db->rollback();
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
    error_log("Settings save error: " . $e->getMessage());
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>