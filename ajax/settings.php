<?php
require_once '../core/config.php';
require_once '../core/functions.php';

header('Content-Type: application/json');

// Перевірка авторизації адміністратора
if (!isLoggedIn() || !isAdmin()) {
    http_response_code(403);
    echo json_encode(['error' => 'Доступ заборонено']);
    exit();
}

try {
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    
    switch ($action) {
        case 'get_settings':
            echo json_encode(getSettings());
            break;
            
        case 'update_setting':
            echo json_encode(updateSetting());
            break;
            
        case 'update_multiple':
            echo json_encode(updateMultipleSettings());
            break;
            
        case 'get_theme_settings':
            echo json_encode(getThemeSettings());
            break;
            
        case 'update_theme_settings':
            echo json_encode(updateThemeSettings());
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Невідома дія']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

function getSettings() {
    global $db;
    
    $result = $db->query("SELECT setting_key, value, type FROM site_settings");
    $settings = [];
    
    while ($row = $result->fetch_assoc()) {
        $value = $row['value'];
        
        // Конвертуємо значення відповідно до типу
        switch ($row['type']) {
            case 'bool':
                $value = (bool)$value;
                break;
            case 'int':
                $value = (int)$value;
                break;
            case 'json':
                $value = json_decode($value, true);
                break;
        }
        
        $settings[$row['setting_key']] = $value;
    }
    
    return ['success' => true, 'settings' => $settings];
}

function updateSetting() {
    global $db;
    
    $key = trim($_POST['key'] ?? '');
    $value = $_POST['value'] ?? '';
    
    if (empty($key)) {
        throw new Exception('Ключ налаштування не може бути порожнім');
    }
    
    // Перевіряємо чи існує налаштування
    $stmt = $db->prepare("SELECT type FROM site_settings WHERE setting_key = ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Оновлюємо існуюче
        $type = $row['type'];
        
        // Валідуємо та конвертуємо значення
        $value = validateAndConvertValue($value, $type);
        
        $updateStmt = $db->prepare("UPDATE site_settings SET value = ?, updated_at = CURRENT_TIMESTAMP WHERE setting_key = ?");
        $updateStmt->bind_param("ss", $value, $key);
        $updateStmt->execute();
    } else {
        // Створюємо нове (автоматично визначаємо тип)
        $type = guessValueType($value);
        $value = validateAndConvertValue($value, $type);
        
        $insertStmt = $db->prepare("INSERT INTO site_settings (setting_key, value, type) VALUES (?, ?, ?)");
        $insertStmt->bind_param("sss", $key, $value, $type);
        $insertStmt->execute();
    }
    
    logActivity('settings_update', "Оновлено налаштування: {$key}", getUserId());
    
    return ['success' => true, 'message' => 'Налаштування оновлено'];
}

function updateMultipleSettings() {
    global $db;
    
    $settings = $_POST['settings'] ?? [];
    
    if (empty($settings) || !is_array($settings)) {
        throw new Exception('Немає налаштувань для оновлення');
    }
    
    $db->begin_transaction();
    
    try {
        foreach ($settings as $key => $value) {
            // Перевіряємо чи існує налаштування
            $stmt = $db->prepare("SELECT type FROM site_settings WHERE setting_key = ?");
            $stmt->bind_param("s", $key);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                // Оновлюємо існуюче
                $type = $row['type'];
                $value = validateAndConvertValue($value, $type);
                
                $updateStmt = $db->prepare("UPDATE site_settings SET value = ?, updated_at = CURRENT_TIMESTAMP WHERE setting_key = ?");
                $updateStmt->bind_param("ss", $value, $key);
                $updateStmt->execute();
            } else {
                // Створюємо нове
                $type = guessValueType($value);
                $value = validateAndConvertValue($value, $type);
                
                $insertStmt = $db->prepare("INSERT INTO site_settings (setting_key, value, type) VALUES (?, ?, ?)");
                $insertStmt->bind_param("sss", $key, $value, $type);
                $insertStmt->execute();
            }
        }
        
        $db->commit();
        
        logActivity('settings_bulk_update', "Оновлено " . count($settings) . " налаштувань", getUserId());
        
        return ['success' => true, 'message' => 'Всі налаштування оновлено', 'count' => count($settings)];
        
    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    }
}

function getThemeSettings() {
    global $db;
    
    $themeKeys = [
        'current_theme', 'current_gradient', 'enable_animations', 
        'enable_particles', 'smooth_scroll', 'enable_tooltips', 
        'custom_css', 'custom_js'
    ];
    
    $placeholders = str_repeat('?,', count($themeKeys) - 1) . '?';
    $stmt = $db->prepare("SELECT setting_key, value, type FROM site_settings WHERE setting_key IN ($placeholders)");
    $stmt->bind_param(str_repeat('s', count($themeKeys)), ...$themeKeys);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $settings = [];
    while ($row = $result->fetch_assoc()) {
        $value = $row['value'];
        
        switch ($row['type']) {
            case 'bool':
                $value = (bool)$value;
                break;
            case 'int':
                $value = (int)$value;
                break;
            case 'json':
                $value = json_decode($value, true);
                break;
        }
        
        $settings[$row['setting_key']] = $value;
    }
    
    // Додаємо значення за замовчуванням для відсутніх ключів
    $defaults = [
        'current_theme' => 'light',
        'current_gradient' => 'gradient-1',
        'enable_animations' => true,
        'enable_particles' => false,
        'smooth_scroll' => true,
        'enable_tooltips' => true,
        'custom_css' => '',
        'custom_js' => ''
    ];
    
    foreach ($defaults as $key => $default) {
        if (!isset($settings[$key])) {
            $settings[$key] = $default;
        }
    }
    
    return ['success' => true, 'settings' => $settings];
}

function updateThemeSettings() {
    global $db;
    
    $allowedSettings = [
        'current_theme' => 'string',
        'current_gradient' => 'string',
        'enable_animations' => 'bool',
        'enable_particles' => 'bool',
        'smooth_scroll' => 'bool',
        'enable_tooltips' => 'bool',
        'custom_css' => 'text',
        'custom_js' => 'text'
    ];
    
    $updates = [];
    foreach ($allowedSettings as $key => $type) {
        if (isset($_POST[$key])) {
            $value = $_POST[$key];
            $value = validateAndConvertValue($value, $type);
            $updates[$key] = $value;
        }
    }
    
    if (empty($updates)) {
        throw new Exception('Немає даних для оновлення');
    }
    
    $db->begin_transaction();
    
    try {
        foreach ($updates as $key => $value) {
            $type = $allowedSettings[$key];
            
            $stmt = $db->prepare("
                INSERT INTO site_settings (setting_key, value, type) 
                VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE 
                value = VALUES(value), 
                updated_at = CURRENT_TIMESTAMP
            ");
            $stmt->bind_param("sss", $key, $value, $type);
            $stmt->execute();
        }
        
        $db->commit();
        
        logActivity('theme_settings_update', "Оновлено налаштування теми: " . implode(', ', array_keys($updates)), getUserId());
        
        return ['success' => true, 'message' => 'Налаштування теми оновлено', 'updated' => array_keys($updates)];
        
    } catch (Exception $e) {
        $db->rollback();
        throw $e;
    }
}

function validateAndConvertValue($value, $type) {
    switch ($type) {
        case 'bool':
            return $value ? '1' : '0';
            
        case 'int':
            if (!is_numeric($value)) {
                throw new Exception("Значення має бути числовим для типу 'int'");
            }
            return (string)(int)$value;
            
        case 'json':
            if (is_array($value)) {
                return json_encode($value);
            } else if (is_string($value)) {
                $decoded = json_decode($value, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception("Некоректний JSON формат");
                }
                return $value;
            }
            throw new Exception("Значення має бути масивом або JSON строкою для типу 'json'");
            
        case 'text':
        case 'string':
        default:
            return (string)$value;
    }
}

function guessValueType($value) {
    if (is_bool($value) || in_array($value, ['0', '1', 'true', 'false'], true)) {
        return 'bool';
    }
    
    if (is_numeric($value)) {
        return 'int';
    }
    
    if (is_array($value) || (is_string($value) && json_decode($value, true) !== null)) {
        return 'json';
    }
    
    if (strlen($value) > 255) {
        return 'text';
    }
    
    return 'string';
}

function getSetting($key, $default = null) {
    global $db;
    
    $stmt = $db->prepare("SELECT value, type FROM site_settings WHERE setting_key = ?");
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $value = $row['value'];
        
        switch ($row['type']) {
            case 'bool':
                return (bool)$value;
            case 'int':
                return (int)$value;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }
    
    return $default;
}

function logActivity($action, $description, $user_id = null) {
    global $db;
    
    if (!$user_id) {
        $user_id = getUserId();
    }
    
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $stmt = $db->prepare("
        INSERT INTO activity_logs (user_id, action, description, ip_address, user_agent) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("issss", $user_id, $action, $description, $ip, $user_agent);
    $stmt->execute();
}
?>