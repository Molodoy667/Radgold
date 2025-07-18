<?php
require_once '../config/config.php';
require_once '../config/database.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Невірний метод запиту']);
    exit();
}

$action = $_POST['action'] ?? '';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    switch ($action) {
        case 'login':
            handleLogin($db);
            break;
            
        case 'register':
            handleRegister($db);
            break;
            
        case 'logout':
            handleLogout();
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Невідома дія']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Помилка сервера: ' . $e->getMessage()]);
}

function handleLogin($db) {
    $email = clean_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Заповніть всі поля!']);
        return;
    }
    
    // Пошук користувача
    $query = "SELECT id, username, email, password, is_active FROM users WHERE email = ?";
    $stmt = $db->prepare($query);
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() === 1) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user['is_active']) {
            echo json_encode(['success' => false, 'message' => 'Акаунт заблокований']);
            return;
        }
        
        if (password_verify($password, $user['password'])) {
            // Успішна авторизація
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            
            // Запам'ятати користувача
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/');
                
                // Зберігаємо токен в базі даних
                $update_query = "UPDATE users SET remember_token = ? WHERE id = ?";
                $update_stmt = $db->prepare($update_query);
                $update_stmt->execute([$token, $user['id']]);
            }
            
            echo json_encode([
                'success' => true, 
                'message' => 'Ласкаво просимо, ' . $user['username'] . '!',
                'redirect' => '/',
                'user' => [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email']
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Невірний email або пароль!']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Невірний email або пароль!']);
    }
}

function handleRegister($db) {
    $username = clean_input($_POST['username'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = clean_input($_POST['phone'] ?? '');
    
    // Валідація
    if (empty($username) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Заповніть всі обов\'язкові поля!']);
        return;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Невірний формат email!']);
        return;
    }
    
    if (strlen($password) < 6) {
        echo json_encode(['success' => false, 'message' => 'Пароль повинен містити мінімум 6 символів!']);
        return;
    }
    
    if ($password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'Паролі не співпадають!']);
        return;
    }
    
    // Перевіряємо унікальність
    $check_query = "SELECT id FROM users WHERE email = ? OR username = ?";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->execute([$email, $username]);
    
    if ($check_stmt->rowCount() > 0) {
        echo json_encode(['success' => false, 'message' => 'Користувач з таким email або ім\'ям вже існує!']);
        return;
    }
    
    // Хешуємо пароль
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Вставляємо нового користувача
    $insert_query = "INSERT INTO users (username, email, password, phone) VALUES (?, ?, ?, ?)";
    $insert_stmt = $db->prepare($insert_query);
    
    if ($insert_stmt->execute([$username, $email, $hashed_password, $phone])) {
        echo json_encode([
            'success' => true, 
            'message' => 'Реєстрація успішна! Тепер ви можете увійти в систему.',
            'redirect' => '/pages/login.php'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Помилка реєстрації. Спробуйте пізніше.']);
    }
}

function handleLogout() {
    // Видаляємо всі дані сесії
    session_unset();
    session_destroy();
    
    // Видаляємо cookie для запам'ятовування
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Ви успішно вийшли з системи',
        'redirect' => '/'
    ]);
}
?>