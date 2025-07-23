<?php
// Файл функцій для AdBoard Pro

// Безпечна функція виводу
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Функція перенаправлення
function redirect($url) {
    header("Location: $url");
    exit();
}

// Перевірка авторизації
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Перевірка ролі адміністратора
function isAdmin() {
    return isLoggedIn() && $_SESSION['user_role'] === 'admin';
}

// Отримання поточного користувача
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    try {
        $db = new Database();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    } catch (Exception $e) {
        return null;
    }
}

// Функція входу користувача
function loginUser($email, $password, $userType = 'user', $remember = false) {
    try {
        $db = new Database();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND user_type = ? AND status = 'active'");
        $stmt->bind_param("ss", $email, $userType);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user && password_verify($password, $user['password'])) {
            // Оновлення останнього входу
            $updateStmt = $db->prepare("UPDATE users SET last_login = NOW(), login_count = login_count + 1 WHERE id = ?");
            $updateStmt->bind_param("i", $user['id']);
            $updateStmt->execute();
            
            // Створення сесії
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'] ?? $userType;
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            
            // Запам'ятати користувача
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $expires = time() + (30 * 24 * 60 * 60); // 30 днів
                
                setcookie('remember_token', $token, $expires, '/', '', true, true);
                
                $tokenStmt = $db->prepare("INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
                $expiresDate = date('Y-m-d H:i:s', $expires);
                $tokenStmt->bind_param("iss", $user['id'], $token, $expiresDate);
                $tokenStmt->execute();
            }
            
            return $user;
        }
        
        return false;
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        return false;
    }
}

// Функція реєстрації користувача
function registerUser($userData) {
    try {
        $db = new Database();
        
        // Перевірка існування користувача
        if (userExists($userData['email'])) {
            return false;
        }
        
        $stmt = $db->prepare("
            INSERT INTO users (first_name, last_name, email, phone, password, user_type, status, newsletter, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $newsletter = $userData['newsletter'] ? 1 : 0;
        
        $stmt->bind_param("sssssssss", 
            $userData['first_name'],
            $userData['last_name'],
            $userData['email'],
            $userData['phone'],
            $userData['password'],
            $userData['user_type'],
            $userData['status'],
            $newsletter,
            $userData['created_at']
        );
        
        return $stmt->execute();
    } catch (Exception $e) {
        error_log("Registration error: " . $e->getMessage());
        return false;
    }
}

// Перевірка існування користувача
function userExists($email) {
    try {
        $db = new Database();
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    } catch (Exception $e) {
        return false;
    }
}

// Відправка листа для відновлення паролю
function sendPasswordReset($email, $userType = 'user') {
    try {
        $db = new Database();
        $stmt = $db->prepare("SELECT id, first_name FROM users WHERE email = ? AND user_type = ?");
        $stmt->bind_param("ss", $email, $userType);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if (!$user) {
            return false;
        }
        
        // Генерація токену
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 3600); // 1 година
        
        // Видалення старих токенів
        $deleteStmt = $db->prepare("DELETE FROM password_resets WHERE email = ?");
        $deleteStmt->bind_param("s", $email);
        $deleteStmt->execute();
        
        // Додавання нового токену
        $tokenStmt = $db->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $tokenStmt->bind_param("sss", $email, $token, $expires);
        $tokenStmt->execute();
        
        // Відправка email
        $resetLink = SITE_URL . "/pages/{$userType}/reset_password.php?token=" . $token;
        $subject = "Відновлення паролю - " . SITE_NAME;
        $message = "
        <html>
        <head>
            <title>Відновлення паролю</title>
        </head>
        <body>
            <h2>Відновлення паролю</h2>
            <p>Привіт, {$user['first_name']}!</p>
            <p>Ви запросили відновлення паролю для вашого акаунту на сайті " . SITE_NAME . ".</p>
            <p>Натисніть на посилання нижче, щоб створити новий пароль:</p>
            <a href='{$resetLink}' style='background: #667eea; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Відновити пароль</a>
            <p>Посилання дійсне протягом 1 години.</p>
            <p>Якщо ви не запрошували відновлення паролю, просто проігноруйте цей лист.</p>
        </body>
        </html>
        ";
        
        return sendEmail($email, $subject, $message);
    } catch (Exception $e) {
        error_log("Password reset error: " . $e->getMessage());
        return false;
    }
}

// Відправка вітального email
function sendWelcomeEmail($email, $firstName, $userType = 'user') {
    try {
        $userTypeText = $userType === 'partner' ? 'партнера' : 'користувача';
        $dashboardLink = SITE_URL . "/pages/{$userType}/dashboard.php";
        
        $subject = "Ласкаво просимо до " . SITE_NAME . "!";
        $message = "
        <html>
        <head>
            <title>Ласкаво просимо!</title>
        </head>
        <body style='font-family: Arial, sans-serif;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h2 style='color: #667eea;'>Ласкаво просимо до " . SITE_NAME . "!</h2>
                <p>Привіт, {$firstName}!</p>
                <p>Дякуємо за реєстрацію як {$userTypeText} на нашій платформі.</p>
                " . ($userType === 'user' ? "
                <p>Тепер ви можете:</p>
                <ul>
                    <li>Розміщувати безкоштовні оголошення</li>
                    <li>Переглядати статистику переглядів</li>
                    <li>Керувати своїми товарами та послугами</li>
                </ul>
                " : "
                <p>Як партнер ви отримуєте доступ до:</p>
                <ul>
                    <li>Професійних рекламних інструментів</li>
                    <li>Детальної аналітики кампаній</li>
                    <li>Персонального менеджера</li>
                </ul>
                ") . "
                <a href='{$dashboardLink}' style='background: #667eea; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 20px 0;'>Перейти в особистий кабінет</a>
                <p>Якщо у вас виникнуть питання, не соромтеся звертатися до нашої служби підтримки.</p>
                <p>З найкращими побажаннями,<br>Команда " . SITE_NAME . "</p>
            </div>
        </body>
        </html>
        ";
        
        return sendEmail($email, $subject, $message);
    } catch (Exception $e) {
        error_log("Welcome email error: " . $e->getMessage());
        return false;
    }
}

// Базова функція відправки email
function sendEmail($to, $subject, $message) {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: ' . SITE_NAME . ' <noreply@' . $_SERVER['HTTP_HOST'] . '>' . "\r\n";
    
    return mail($to, $subject, $message, $headers);
}

// Функція виходу
function logout() {
    // Видалення remember token
    if (isset($_COOKIE['remember_token'])) {
        try {
            $db = new Database();
            $stmt = $db->prepare("DELETE FROM remember_tokens WHERE token = ?");
            $stmt->bind_param("s", $_COOKIE['remember_token']);
            $stmt->execute();
        } catch (Exception $e) {
            // Ігноруємо помилки
        }
        
        setcookie('remember_token', '', time() - 3600, '/', '', true, true);
    }
    
    // Очищення сесії
    session_destroy();
    
    redirect('/pages/user/login.php');
}

// Функції для роботи з мета-тегами
function getMetaTags() {
    try {
        $db = new Database();
        $result = $db->query("SELECT * FROM site_settings WHERE id = 1");
        $settings = $result->fetch_assoc();
        
        return [
            'title' => $settings['site_title'] ?? SITE_NAME,
            'description' => $settings['site_description'] ?? SITE_DESCRIPTION,
            'keywords' => $settings['site_keywords'] ?? SITE_KEYWORDS,
            'author' => $settings['site_author'] ?? 'AdBoard Pro',
            'favicon' => $settings['favicon_url'] ?? 'images/favicon.svg',
            'logo' => $settings['logo_url'] ?? 'images/default_logo.svg'
        ];
    } catch (Exception $e) {
        return [
            'title' => SITE_NAME,
            'description' => SITE_DESCRIPTION,
            'keywords' => SITE_KEYWORDS ?? 'реклама, оголошення',
            'author' => 'AdBoard Pro',
            'favicon' => 'images/favicon.svg',
            'logo' => 'images/default_logo.svg'
        ];
    }
}

// Функції для роботи з темою
function getThemeSettings() {
    try {
        $db = new Database();
        $result = $db->query("SELECT * FROM theme_settings WHERE id = 1");
        return $result->fetch_assoc() ?? [];
    } catch (Exception $e) {
        return [];
    }
}

// Генерація градієнтів
function generateGradients() {
    return [
        'gradient-1' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        'gradient-2' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
        'gradient-3' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
        'gradient-4' => 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
        'gradient-5' => 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
        'gradient-6' => 'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
        'gradient-7' => 'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)',
        'gradient-8' => 'linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%)',
        'gradient-9' => 'linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%)',
        'gradient-10' => 'linear-gradient(135deg, #fad0c4 0%, #ffd1ff 100%)',
        'gradient-11' => 'linear-gradient(135deg, #ff8a80 0%, #ff80ab 100%)',
        'gradient-12' => 'linear-gradient(135deg, #81c784 0%, #aed581 100%)',
        'gradient-13' => 'linear-gradient(135deg, #64b5f6 0%, #42a5f5 100%)',
        'gradient-14' => 'linear-gradient(135deg, #ffb74d 0%, #ffa726 100%)',
        'gradient-15' => 'linear-gradient(135deg, #9575cd 0%, #7986cb 100%)',
        'gradient-16' => 'linear-gradient(135deg, #4db6ac 0%, #26a69a 100%)',
        'gradient-17' => 'linear-gradient(135deg, #f06292 0%, #ec407a 100%)',
        'gradient-18' => 'linear-gradient(135deg, #ab47bc 0%, #8e24aa 100%)',
        'gradient-19' => 'linear-gradient(135deg, #5c6bc0 0%, #3f51b5 100%)',
        'gradient-20' => 'linear-gradient(135deg, #26c6da 0%, #00acc1 100%)',
        'gradient-21' => 'linear-gradient(135deg, #66bb6a 0%, #4caf50 100%)',
        'gradient-22' => 'linear-gradient(135deg, #ffca28 0%, #ffc107 100%)',
        'gradient-23' => 'linear-gradient(135deg, #ff7043 0%, #ff5722 100%)',
        'gradient-24' => 'linear-gradient(135deg, #8d6e63 0%, #795548 100%)',
        'gradient-25' => 'linear-gradient(135deg, #78909c 0%, #607d8b 100%)',
        'gradient-26' => 'linear-gradient(135deg, #e91e63 0%, #ad1457 100%)',
        'gradient-27' => 'linear-gradient(135deg, #673ab7 0%, #512da8 100%)',
        'gradient-28' => 'linear-gradient(135deg, #3f51b5 0%, #303f9f 100%)',
        'gradient-29' => 'linear-gradient(135deg, #009688 0%, #00695c 100%)',
        'gradient-30' => 'linear-gradient(135deg, #ff9800 0%, #f57c00 100%)'
    ];
}

// Функція для автоматичної авторизації через remember token
function checkRememberToken() {
    if (!isLoggedIn() && isset($_COOKIE['remember_token'])) {
        try {
            $db = new Database();
            $stmt = $db->prepare("
                SELECT u.* FROM users u 
                JOIN remember_tokens rt ON u.id = rt.user_id 
                WHERE rt.token = ? AND rt.expires_at > NOW()
            ");
            $stmt->bind_param("s", $_COOKIE['remember_token']);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            
            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'] ?? $user['user_type'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                
                return true;
            } else {
                // Видалення недійсного токену
                setcookie('remember_token', '', time() - 3600, '/', '', true, true);
            }
        } catch (Exception $e) {
            error_log("Remember token error: " . $e->getMessage());
        }
    }
    
    return false;
}

// Виклик перевірки remember token
if (session_status() == PHP_SESSION_ACTIVE) {
    checkRememberToken();
}

// Допоміжні функції
function formatBytes($size, $precision = 2) {
    if ($size == 0) return '0 B';
    $base = log($size, 1024);
    $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');
    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
}

function getRoute() {
    return isset($_GET['route']) ? sanitize($_GET['route']) : '';
}

function generateToken() {
    return bin2hex(random_bytes(32));
}

function truncateText($text, $length = 100) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

function getSiteSetting($key, $default = null) {
    global $db;
    
    static $settings = null;
    
    if ($settings === null) {
        $settings = [];
        $result = $db->query("SELECT setting_key, value, type FROM site_settings");
        while ($row = $result->fetch_assoc()) {
            $value = $row['value'];
            
            // Конвертуємо значення відповідно до типу
            switch ($row['type']) {
                case 'bool':
                    $value = (bool)((int)$value);
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
    }
    
    return $settings[$key] ?? $default;
}

function setSiteSetting($key, $value, $type = null) {
    global $db;
    
    if ($type === null) {
        $type = guessSettingType($value);
    }
    
    // Конвертуємо значення для збереження в БД
    switch ($type) {
        case 'bool':
            $value = $value ? '1' : '0';
            break;
        case 'int':
            $value = (string)(int)$value;
            break;
        case 'json':
            $value = is_array($value) ? json_encode($value) : $value;
            break;
        default:
            $value = (string)$value;
    }
    
    $stmt = $db->prepare("
        INSERT INTO site_settings (setting_key, value, type) 
        VALUES (?, ?, ?) 
        ON DUPLICATE KEY UPDATE 
        value = VALUES(value), 
        updated_at = CURRENT_TIMESTAMP
    ");
    $stmt->bind_param("sss", $key, $value, $type);
    $stmt->execute();
    
    // Очищуємо кеш
    static $settings;
    $settings = null;
}

function guessSettingType($value) {
    if (is_bool($value)) {
        return 'bool';
    }
    
    if (is_int($value) || is_numeric($value)) {
        return 'int';
    }
    
    if (is_array($value)) {
        return 'json';
    }
    
    if (strlen($value) > 255) {
        return 'text';
    }
    
    return 'string';
}
?>
