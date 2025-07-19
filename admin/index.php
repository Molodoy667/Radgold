<?php
session_start();

// Перевіряємо, чи система встановлена
if (!file_exists('../config/installed.lock')) {
    header('Location: ../install/index.php');
    exit();
}

require_once '../config/config.php';
require_once '../config/database.php';

// Якщо адмін вже увійшов - перенаправляємо в панель
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']) {
    header('Location: dashboard.php');
    exit();
}

$error_message = '';
$success_message = '';
$reset_mode = isset($_GET['reset']) && $_GET['reset'] === '1';

// Обробка відновлення пароля
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'reset_password') {
        $email = clean_input($_POST['email'] ?? '');
        
        if (empty($email)) {
            $error_message = 'Будь ласка, введіть email';
        } else {
            try {
                $database = new Database();
                $db = $database->getConnection();
                
                $query = "SELECT id, username, email FROM users WHERE email = ? AND is_admin = 1 AND is_active = 1";
                $stmt = $db->prepare($query);
                $stmt->execute([$email]);
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($admin) {
                    // Генеруємо токен для скидання
                    $reset_token = bin2hex(random_bytes(32));
                    $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
                    
                    // Зберігаємо токен в базі
                    $update_query = "UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE id = ?";
                    $update_stmt = $db->prepare($update_query);
                    $update_stmt->execute([$reset_token, $expires_at, $admin['id']]);
                    
                    // TODO: Відправка email (поки просто показуємо токен)
                    $success_message = "Посилання для відновлення надіслано на ваш email.<br>
                                      <small>Токен для тестування: <a href='?token={$reset_token}' class='fw-bold'>{$reset_token}</a></small>";
                } else {
                    $error_message = 'Адміністратора з таким email не знайдено';
                }
            } catch (Exception $e) {
                $error_message = 'Помилка: ' . $e->getMessage();
            }
        }
    } elseif ($_POST['action'] === 'confirm_reset') {
        $token = clean_input($_POST['token'] ?? '');
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        if (empty($token) || empty($new_password) || empty($confirm_password)) {
            $error_message = 'Будь ласка, заповніть всі поля';
        } elseif ($new_password !== $confirm_password) {
            $error_message = 'Паролі не співпадають';
        } elseif (strlen($new_password) < 6) {
            $error_message = 'Пароль повинен містити мінімум 6 символів';
        } else {
            try {
                $database = new Database();
                $db = $database->getConnection();
                
                $query = "SELECT id FROM users WHERE reset_token = ? AND reset_token_expires > NOW() AND is_admin = 1";
                $stmt = $db->prepare($query);
                $stmt->execute([$token]);
                $admin = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($admin) {
                    // Оновлюємо пароль та очищуємо токен
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $update_query = "UPDATE users SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?";
                    $update_stmt = $db->prepare($update_query);
                    $update_stmt->execute([$hashed_password, $admin['id']]);
                    
                    $success_message = 'Пароль успішно змінено. Тепер ви можете увійти з новим паролем.';
                    $reset_mode = false;
                } else {
                    $error_message = 'Недійсний або прострочений токен';
                }
            } catch (Exception $e) {
                $error_message = 'Помилка: ' . $e->getMessage();
            }
        }
    }
}

// Обробка форми входу
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['action']) || $_POST['action'] === 'login')) {
    $username = clean_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (empty($username) || empty($password)) {
        $error_message = 'Будь ласка, заповніть всі поля';
    } else {
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            $query = "SELECT id, username, email, password FROM users WHERE (username = ? OR email = ?) AND is_admin = 1 AND is_active = 1";
            $stmt = $db->prepare($query);
            $stmt->execute([$username, $username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($admin && password_verify($password, $admin['password'])) {
                // Успішний вхід
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_email'] = $admin['email'];
                
                // Оновлення останнього входу
                $update_query = "UPDATE users SET last_login = NOW() WHERE id = ?";
                $update_stmt = $db->prepare($update_query);
                $update_stmt->execute([$admin['id']]);
                
                // Remember me
                if ($remember) {
                    $remember_token = bin2hex(random_bytes(32));
                    setcookie('admin_remember', $remember_token, time() + (30 * 24 * 60 * 60), '/admin/');
                    
                    $token_query = "UPDATE users SET remember_token = ? WHERE id = ?";
                    $token_stmt = $db->prepare($token_query);
                    $token_stmt->execute([$remember_token, $admin['id']]);
                }
                
                // Логування успішного входу
                $log_query = "INSERT INTO admin_logs (admin_id, action, description, ip_address, user_agent) 
                             VALUES (?, 'login', 'Успішний вхід в адмін панель', ?, ?)";
                $log_stmt = $db->prepare($log_query);
                $log_stmt->execute([
                    $admin['id'], 
                    $_SERVER['REMOTE_ADDR'] ?? '', 
                    $_SERVER['HTTP_USER_AGENT'] ?? ''
                ]);
                
                header('Location: dashboard.php');
                exit();
            } else {
                $error_message = 'Невірний логін або пароль';
                
                // Логування невдалої спроби
                if ($admin) {
                    $log_query = "INSERT INTO admin_logs (admin_id, action, description, ip_address, user_agent) 
                                 VALUES (?, 'failed_login', 'Невдала спроба входу', ?, ?)";
                    $log_stmt = $db->prepare($log_query);
                    $log_stmt->execute([
                        $admin['id'], 
                        $_SERVER['REMOTE_ADDR'] ?? '', 
                        $_SERVER['HTTP_USER_AGENT'] ?? ''
                    ]);
                }
            }
        } catch (Exception $e) {
            $error_message = 'Помилка з\'єднання з базою даних';
        }
    }
}

// Перевірка remember me cookie
if (isset($_COOKIE['admin_remember']) && !isset($_SESSION['admin_logged_in'])) {
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "SELECT id, username, email FROM users WHERE remember_token = ? AND is_admin = 1 AND is_active = 1";
        $stmt = $db->prepare($query);
        $stmt->execute([$_COOKIE['admin_remember']]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($admin) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_email'] = $admin['email'];
            
            header('Location: dashboard.php');
            exit();
        }
    } catch (Exception $e) {
        // Видаляємо невалідний cookie
        setcookie('admin_remember', '', time() - 3600, '/admin/');
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Адмін панель - <?php echo Settings::get('site_name', 'Дошка Оголошень'); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        <?php echo Theme::generateCSS(); ?>
        
        body {
            background: var(--theme-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-container {
            max-width: 450px;
            width: 100%;
            padding: 0 20px;
        }
        
        .login-card {
            background: var(--card-bg);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
            border: 1px solid var(--border-color);
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-header {
            background: var(--theme-gradient);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .login-header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
        }
        
        .login-header p {
            margin: 0.5rem 0 0 0;
            opacity: 0.9;
        }
        
        .admin-icon {
            background: rgba(255,255,255,0.2);
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem auto;
            font-size: 2rem;
        }
        
        .login-body {
            padding: 2rem;
        }
        
        .form-floating {
            margin-bottom: 1rem;
        }
        
        .form-floating .form-control {
            border: 2px solid var(--border-color);
            border-radius: 10px;
            height: 58px;
            transition: all 0.3s ease;
            background: var(--surface-color);
            color: var(--text-color);
        }
        
        .form-floating .form-control:focus {
            border-color: var(--theme-primary);
            box-shadow: 0 0 0 0.2rem rgba(var(--theme-primary-rgb), 0.25);
            background: var(--surface-color);
        }
        
        .form-floating label {
            color: var(--text-muted);
        }
        
        .btn-login {
            background: var(--theme-gradient);
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            width: 100%;
            color: white;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(var(--theme-primary-rgb), 0.3);
            color: white;
        }
        
        .password-toggle {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            z-index: 10;
        }
        
        .password-toggle:hover {
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="admin-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h1><i class="fas fa-cog me-2"></i>Адмін панель</h1>
                <p><?php echo htmlspecialchars(Settings::get('site_name', 'Дошка Оголошень')); ?></p>
            </div>
            
            <div class="login-body">
                <?php if ($error_message): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo $error_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($success_message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo $success_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (!$reset_mode && !isset($_GET['token'])): ?>
                    <!-- Форма входу -->
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="login">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="username" name="username" 
                                   placeholder="Логін або Email" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                            <label for="username"><i class="fas fa-user me-2"></i>Логін або Email</label>
                        </div>
                        
                        <div class="form-floating mb-3 position-relative">
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Пароль" required>
                            <label for="password"><i class="fas fa-lock me-2"></i>Пароль</label>
                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                <i class="fas fa-eye" id="password-toggle"></i>
                            </button>
                        </div>
                        
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember" 
                                   <?php echo isset($_POST['remember']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="remember">
                                Запам'ятати мене
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-login btn-lg">
                            <i class="fas fa-sign-in-alt me-2"></i>Увійти
                        </button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <a href="?reset=1" class="text-decoration-none text-muted">
                            <i class="fas fa-key me-1"></i>Забули пароль?
                        </a>
                    </div>
                    
                <?php elseif ($reset_mode): ?>
                    <!-- Форма відновлення пароля -->
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="reset_password">
                        <div class="text-center mb-3">
                            <h5>Відновлення пароля</h5>
                            <p class="text-muted small">Введіть ваш email для отримання посилання</p>
                        </div>
                        
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" name="email" 
                                   placeholder="Email" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            <label for="email"><i class="fas fa-envelope me-2"></i>Email</label>
                        </div>
                        
                        <button type="submit" class="btn btn-login btn-lg">
                            <i class="fas fa-paper-plane me-2"></i>Надіслати
                        </button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <a href="index.php" class="text-decoration-none text-muted">
                            <i class="fas fa-arrow-left me-1"></i>Повернутися до входу
                        </a>
                    </div>
                    
                <?php elseif (isset($_GET['token'])): ?>
                    <!-- Форма встановлення нового пароля -->
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="confirm_reset">
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
                        
                        <div class="text-center mb-3">
                            <h5>Новий пароль</h5>
                            <p class="text-muted small">Введіть новий пароль</p>
                        </div>
                        
                        <div class="form-floating mb-3 position-relative">
                            <input type="password" class="form-control" id="new_password" name="new_password" 
                                   placeholder="Новий пароль" required minlength="6">
                            <label for="new_password"><i class="fas fa-lock me-2"></i>Новий пароль</label>
                            <button type="button" class="password-toggle" onclick="togglePassword('new_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        
                        <div class="form-floating mb-3 position-relative">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                   placeholder="Підтвердження пароля" required minlength="6">
                            <label for="confirm_password"><i class="fas fa-lock me-2"></i>Підтвердження пароля</label>
                            <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        
                        <button type="submit" class="btn btn-login btn-lg">
                            <i class="fas fa-save me-2"></i>Зберегти пароль
                        </button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <a href="index.php" class="text-decoration-none text-muted">
                            <i class="fas fa-arrow-left me-1"></i>Повернутися до входу
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function togglePassword(inputId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = passwordInput.parentNode.querySelector('.password-toggle i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
        
        // Перевірка співпадання паролів
        document.addEventListener('DOMContentLoaded', function() {
            const newPassword = document.getElementById('new_password');
            const confirmPassword = document.getElementById('confirm_password');
            
            if (newPassword && confirmPassword) {
                function checkPasswordMatch() {
                    if (newPassword.value !== confirmPassword.value) {
                        confirmPassword.setCustomValidity('Паролі не співпадають');
                    } else {
                        confirmPassword.setCustomValidity('');
                    }
                }
                
                newPassword.addEventListener('input', checkPasswordMatch);
                confirmPassword.addEventListener('input', checkPasswordMatch);
            }
        });
    </script>
</body>
</html>