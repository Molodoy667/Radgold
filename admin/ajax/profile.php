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

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_id = $_SESSION['admin_id'];
    $username = clean_input($_POST['username'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    
    // Валідація
    if (empty($username) || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Заповніть всі обов\'язкові поля']);
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Невірний формат email']);
        exit();
    }
    
    try {
        // Перевіряємо унікальність username та email (крім поточного користувача)
        $check_query = "SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->execute([$username, $email, $admin_id]);
        
        if ($check_stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Ім\'я користувача або email вже використовуються']);
            exit();
        }
        
        // Обробка завантаження аватара
        $avatar_path = null;
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../../assets/uploads/avatars/';
            
            // Створюємо папку якщо не існує
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_info = pathinfo($_FILES['avatar']['name']);
            $extension = strtolower($file_info['extension']);
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (!in_array($extension, $allowed_extensions)) {
                echo json_encode(['success' => false, 'message' => 'Дозволені тільки JPG, PNG, GIF файли']);
                exit();
            }
            
            if ($_FILES['avatar']['size'] > 2097152) { // 2MB
                echo json_encode(['success' => false, 'message' => 'Розмір файлу не повинен перевищувати 2MB']);
                exit();
            }
            
            // Генеруємо унікальне ім'я файлу
            $filename = 'admin_' . $admin_id . '_' . time() . '.' . $extension;
            $target_path = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_path)) {
                $avatar_path = 'assets/uploads/avatars/' . $filename;
                
                // Видаляємо старий аватар
                $old_avatar_query = "SELECT avatar FROM users WHERE id = ?";
                $old_avatar_stmt = $db->prepare($old_avatar_query);
                $old_avatar_stmt->execute([$admin_id]);
                $old_avatar = $old_avatar_stmt->fetchColumn();
                
                if ($old_avatar && file_exists('../../' . $old_avatar)) {
                    unlink('../../' . $old_avatar);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Помилка завантаження файлу']);
                exit();
            }
        }
        
        // Оновлюємо дані користувача
        $update_fields = ['username = ?', 'email = ?'];
        $update_params = [$username, $email];
        
        if ($avatar_path) {
            $update_fields[] = 'avatar = ?';
            $update_params[] = $avatar_path;
        }
        
        if (!empty($new_password)) {
            if (strlen($new_password) < 6) {
                echo json_encode(['success' => false, 'message' => 'Пароль повинен містити мінімум 6 символів']);
                exit();
            }
            $update_fields[] = 'password = ?';
            $update_params[] = password_hash($new_password, PASSWORD_DEFAULT);
        }
        
        $update_params[] = $admin_id;
        
        $update_query = "UPDATE users SET " . implode(', ', $update_fields) . ", updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $update_stmt = $db->prepare($update_query);
        $result = $update_stmt->execute($update_params);
        
        if ($result) {
            // Оновлюємо дані в сесії
            $_SESSION['admin_username'] = $username;
            $_SESSION['admin_email'] = $email;
            
            // Логування
            $log_query = "INSERT INTO admin_logs (admin_id, action, description, ip_address, user_agent) 
                         VALUES (?, 'profile_update', 'Оновлення профілю адміністратора', ?, ?)";
            $log_stmt = $db->prepare($log_query);
            $log_stmt->execute([
                $admin_id,
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Профіль успішно оновлено',
                'avatar_url' => $avatar_path ? ('../../' . $avatar_path) : null
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Помилка оновлення профілю']);
        }
        
    } catch (Exception $e) {
        error_log("Profile update error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Помилка сервера']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Невірний метод запиту']);
}
?>