<?php
session_start();
header('Content-Type: application/json');

// Проверка авторизации админа
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    echo json_encode(['success' => false, 'message' => 'Необхідна авторизація']);
    exit();
}

require_once '../../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $admin_id = $_SESSION['admin_id'];
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Валидация
    if (empty($username) || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Ім\'я користувача та email обов\'язкові']);
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Невірний формат email']);
        exit();
    }
    
    if (!empty($new_password)) {
        if (strlen($new_password) < 6) {
            echo json_encode(['success' => false, 'message' => 'Пароль повинен містити не менше 6 символів']);
            exit();
        }
        
        if ($new_password !== $confirm_password) {
            echo json_encode(['success' => false, 'message' => 'Паролі не співпадають']);
            exit();
        }
    }
    
    // Проверка на уникальность username и email
    $check_query = "SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->execute([$username, $email, $admin_id]);
    
    if ($check_stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Користувач з таким ім\'ям або email вже існує']);
        exit();
    }
    
    // Обработка аватара
    $avatar_path = null;
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../assets/uploads/avatars/';
        
        // Создаем папку если её нет
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_info = pathinfo($_FILES['avatar']['name']);
        $extension = strtolower($file_info['extension']);
        
        // Проверка типа файла
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($extension, $allowed_extensions)) {
            echo json_encode(['success' => false, 'message' => 'Дозволені тільки зображення: JPG, PNG, GIF, WebP']);
            exit();
        }
        
        // Проверка размера файла (2MB)
        if ($_FILES['avatar']['size'] > 2 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'Розмір файлу не повинен перевищувати 2MB']);
            exit();
        }
        
        // Генерируем уникальное имя файла
        $filename = 'avatar_' . $admin_id . '_' . time() . '.' . $extension;
        $upload_path = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_path)) {
            $avatar_path = 'assets/uploads/avatars/' . $filename;
            
            // Удаляем старый аватар
            $old_avatar_query = "SELECT avatar FROM users WHERE id = ?";
            $old_avatar_stmt = $db->prepare($old_avatar_query);
            $old_avatar_stmt->execute([$admin_id]);
            $old_avatar = $old_avatar_stmt->fetchColumn();
            
            if ($old_avatar && file_exists('../../' . $old_avatar)) {
                unlink('../../' . $old_avatar);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Помилка завантаження аватара']);
            exit();
        }
    }
    
    // Подготавливаем запрос на обновление
    $update_fields = ['username = ?', 'email = ?'];
    $update_values = [$username, $email];
    
    if (!empty($new_password)) {
        $update_fields[] = 'password = ?';
        $update_values[] = password_hash($new_password, PASSWORD_DEFAULT);
    }
    
    if ($avatar_path) {
        $update_fields[] = 'avatar = ?';
        $update_values[] = $avatar_path;
    }
    
    $update_values[] = $admin_id;
    
    $update_query = "UPDATE users SET " . implode(', ', $update_fields) . " WHERE id = ?";
    $update_stmt = $db->prepare($update_query);
    
    if ($update_stmt->execute($update_values)) {
        // Обновляем session данные
        $_SESSION['admin_username'] = $username;
        $_SESSION['admin_email'] = $email;
        
        echo json_encode(['success' => true, 'message' => 'Профіль успішно оновлено']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Помилка оновлення профілю']);
    }
    
} catch (Exception $e) {
    error_log("Profile update error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Системна помилка']);
}