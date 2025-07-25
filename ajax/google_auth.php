<?php
require_once '../core/config.php';
require_once '../core/functions.php';

header('Content-Type: application/json');

// Перевірка методу запиту
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

// Отримання даних
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['credential']) || empty($input['credential'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Google credential is required']);
    exit();
}

$credential = $input['credential'];
$userType = $input['user_type'] ?? 'user';
$action = $input['action'] ?? 'login'; // login або register

try {
    // Декодування Google JWT токену
    $googleUser = decodeGoogleJWT($credential);
    
    if (!$googleUser) {
        throw new Exception('Invalid Google credential');
    }
    
    $email = $googleUser['email'];
    $firstName = $googleUser['given_name'] ?? '';
    $lastName = $googleUser['family_name'] ?? '';
    $googleId = $googleUser['sub'];
    
    // Перевірка існування користувача
    $existingUser = getUserByEmail($email);
    
    if ($action === 'login') {
        // Вхід
        if (!$existingUser) {
            echo json_encode([
                'success' => false,
                'message' => 'Користувач з таким email не знайдений. Спробуйте зареєструватися.'
            ]);
            exit();
        }
        
        if ($existingUser['user_type'] !== $userType) {
            $typeText = $userType === 'partner' ? 'партнера' : 'користувача';
            echo json_encode([
                'success' => false,
                'message' => "Цей email зареєстрований не як {$typeText}."
            ]);
            exit();
        }
        
        // Оновлення Google ID якщо його немає
        if (empty($existingUser['google_id'])) {
            updateUserGoogleId($existingUser['id'], $googleId);
        }
        
        // Створення сесії
        createUserSession($existingUser);
        
        echo json_encode([
            'success' => true,
            'message' => 'Успішний вхід через Google',
            'redirect' => "pages/{$userType}/dashboard.php"
        ]);
        
    } else {
        // Реєстрація
        if ($existingUser) {
            echo json_encode([
                'success' => false,
                'message' => 'Користувач з таким email вже існує. Спробуйте увійти.'
            ]);
            exit();
        }
        
        // Створення нового користувача
        $userData = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'phone' => '', // Буде заповнено пізніше
            'password' => password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT), // Випадковий пароль
            'user_type' => $userType,
            'status' => 'active',
            'newsletter' => false,
            'google_id' => $googleId,
            'email_verified' => true, // Google email вже підтверджений
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $userId = createGoogleUser($userData);
        
        if ($userId) {
            // Отримання створеного користувача
            $newUser = getUserById($userId);
            
            // Створення сесії
            createUserSession($newUser);
            
            // Відправка вітального email
            sendWelcomeEmail($email, $firstName, $userType);
            
            echo json_encode([
                'success' => true,
                'message' => 'Успішна реєстрація через Google',
                'redirect' => "pages/{$userType}/dashboard.php"
            ]);
        } else {
            throw new Exception('Failed to create user');
        }
    }
    
} catch (Exception $e) {
    error_log("Google auth error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Помилка авторизації через Google. Спробуйте ще раз.'
    ]);
}

// Функція декодування Google JWT (спрощена версія)
function decodeGoogleJWT($credential) {
    // В реальному проекті використовуйте бібліотеку firebase/php-jwt
    // Тут спрощена версія для демонстрації
    
    $parts = explode('.', $credential);
    if (count($parts) !== 3) {
        return false;
    }
    
    $payload = base64_decode($parts[1]);
    $data = json_decode($payload, true);
    
    // Перевірка основних полів
    if (!isset($data['email']) || !isset($data['sub'])) {
        return false;
    }
    
    // В продакшені тут має бути верифікація підпису
    // та перевірка iss, aud, exp тощо
    
    return $data;
}

// Отримання користувача по email
function getUserByEmail($email) {
    try {
        $db = new Database();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    } catch (Exception $e) {
        return null;
    }
}

// Отримання користувача по ID
function getUserById($id) {
    try {
        $db = new Database();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    } catch (Exception $e) {
        return null;
    }
}

// Оновлення Google ID користувача
function updateUserGoogleId($userId, $googleId) {
    try {
        $db = new Database();
        $stmt = $db->prepare("UPDATE users SET google_id = ? WHERE id = ?");
        $stmt->bind_param("si", $googleId, $userId);
        return $stmt->execute();
    } catch (Exception $e) {
        return false;
    }
}

// Створення користувача з Google даними
function createGoogleUser($userData) {
    try {
        $db = new Database();
        $stmt = $db->prepare("
            INSERT INTO users (first_name, last_name, email, phone, password, user_type, status, newsletter, google_id, email_verified, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $newsletter = $userData['newsletter'] ? 1 : 0;
        $emailVerified = $userData['email_verified'] ? 1 : 0;
        
        $stmt->bind_param("sssssssssss", 
            $userData['first_name'],
            $userData['last_name'],
            $userData['email'],
            $userData['phone'],
            $userData['password'],
            $userData['user_type'],
            $userData['status'],
            $newsletter,
            $userData['google_id'],
            $emailVerified,
            $userData['created_at']
        );
        
        if ($stmt->execute()) {
            return $db->insert_id;
        }
        
        return false;
    } catch (Exception $e) {
        error_log("Create Google user error: " . $e->getMessage());
        return false;
    }
}

// Створення сесії користувача
function createUserSession($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'] ?? $user['user_type'];
    $_SESSION['user_type'] = $user['user_type'];
    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
    
    // Оновлення останнього входу
    try {
        $db = new Database();
        $stmt = $db->prepare("UPDATE users SET last_login = NOW(), login_count = login_count + 1 WHERE id = ?");
        $stmt->bind_param("i", $user['id']);
        $stmt->execute();
    } catch (Exception $e) {
        // Ігноруємо помилки оновлення
    }
}
?>