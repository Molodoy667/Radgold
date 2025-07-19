<?php
session_start();
header('Content-Type: application/json');

// Проверка авторизации админа
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    echo json_encode(['success' => false, 'message' => 'Необхідна авторизація']);
    exit();
}

require_once '../../config/database.php';
require_once '../../includes/settings.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $section = $_POST['section'] ?? '';
    
    // Функция для сохранения настройки
    function saveSetting($db, $key, $value) {
        $query = "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) 
                  ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
        $stmt = $db->prepare($query);
        return $stmt->execute([$key, $value]);
    }
    
    switch ($section) {
        case 'general':
            // Основные настройки
            $settings = [
                'site_name' => $_POST['site_name'] ?? '',
                'site_description' => $_POST['site_description'] ?? '',
                'site_language' => $_POST['site_language'] ?? 'uk'
            ];
            
            foreach ($settings as $key => $value) {
                if (!saveSetting($db, $key, $value)) {
                    throw new Exception("Помилка збереження налаштування: $key");
                }
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Основні налаштування збережено'
            ]);
            break;
            
        case 'theme':
            // Настройки тем
            $settings = [
                'default_dark_mode' => isset($_POST['default_dark_mode']) ? 1 : 0,
                'enable_theme_switcher' => isset($_POST['enable_theme_switcher']) ? 1 : 0,
                'default_theme_gradient' => $_POST['default_theme_gradient'] ?? 'gradient-2'
            ];
            
            foreach ($settings as $key => $value) {
                if (!saveSetting($db, $key, $value)) {
                    throw new Exception("Помилка збереження налаштування: $key");
                }
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Налаштування тем збережено'
            ]);
            break;
            
        case 'security':
            // Настройки безопасности
            $settings = [
                'registration_enabled' => isset($_POST['registration_enabled']) ? 1 : 0,
                'ads_moderation' => isset($_POST['ads_moderation']) ? 1 : 0
            ];
            
            foreach ($settings as $key => $value) {
                if (!saveSetting($db, $key, $value)) {
                    throw new Exception("Помилка збереження налаштування: $key");
                }
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Налаштування безпеки збережено'
            ]);
            break;
            
        case 'email':
            // Настройки email
            $settings = [
                'smtp_enabled' => isset($_POST['smtp_enabled']) ? 1 : 0,
                'smtp_host' => $_POST['smtp_host'] ?? '',
                'smtp_port' => $_POST['smtp_port'] ?? '587',
                'smtp_username' => $_POST['smtp_username'] ?? '',
                'smtp_password' => $_POST['smtp_password'] ?? '',
                'smtp_secure' => $_POST['smtp_secure'] ?? 'tls',
                'email_from_address' => $_POST['email_from_address'] ?? '',
                'email_from_name' => $_POST['email_from_name'] ?? ''
            ];
            
            foreach ($settings as $key => $value) {
                if (!saveSetting($db, $key, $value)) {
                    throw new Exception("Помилка збереження налаштування: $key");
                }
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Налаштування email збережено'
            ]);
            break;
            
        case 'seo':
            // SEO настройки
            $settings = [
                'site_keywords' => $_POST['site_keywords'] ?? '',
                'google_analytics' => $_POST['google_analytics'] ?? '',
                'google_search_console' => $_POST['google_search_console'] ?? '',
                'robots_txt' => $_POST['robots_txt'] ?? '',
                'sitemap_enabled' => isset($_POST['sitemap_enabled']) ? 1 : 0
            ];
            
            foreach ($settings as $key => $value) {
                if (!saveSetting($db, $key, $value)) {
                    throw new Exception("Помилка збереження налаштування: $key");
                }
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'SEO налаштування збережено'
            ]);
            break;
            
        case 'social':
            // Социальные сети
            $settings = [
                'social_facebook' => $_POST['social_facebook'] ?? '',
                'social_instagram' => $_POST['social_instagram'] ?? '',
                'social_telegram' => $_POST['social_telegram'] ?? '',
                'social_twitter' => $_POST['social_twitter'] ?? '',
                'social_youtube' => $_POST['social_youtube'] ?? ''
            ];
            
            foreach ($settings as $key => $value) {
                if (!saveSetting($db, $key, $value)) {
                    throw new Exception("Помилка збереження налаштування: $key");
                }
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Налаштування соціальних мереж збережено'
            ]);
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Невірна секція налаштувань'
            ]);
            break;
    }
    
} catch (Exception $e) {
    error_log("Settings save error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}