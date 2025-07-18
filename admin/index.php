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

// Обробка форми входу
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                
                // Remember me функціонал
                if ($remember) {
                    $remember_token = bin2hex(random_bytes(16));
                    $token_query = "UPDATE users SET remember_token = ? WHERE id = ?";
                    $token_stmt = $db->prepare($token_query);
                    $token_stmt->execute([$remember_token, $admin['id']]);
                    
                    setcookie('admin_remember', $remember_token, time() + (86400 * 30), '/admin/'); // 30 днів
                }
                
                // Логування входу
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
    <title>Адмін панель - Дошка Оголошень</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        
        .login-body {
            padding: 2rem;
        }
        
        .form-floating {
            margin-bottom: 1rem;
        }
        
        .form-floating .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            height: 58px;
            transition: all 0.3s ease;
        }
        
        .form-floating .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
            color: white;
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .remember-check {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 1rem 0;
        }
        
        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }
        
        .login-footer {
            background: #f8f9fa;
            padding: 1rem 2rem;
            text-align: center;
            border-top: 1px solid #e9ecef;
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
        
        .password-toggle {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            z-index: 5;
        }
        
        .password-toggle:hover {
            color: #495057;
        }
        
        @media (max-width: 576px) {
            .login-header {
                padding: 1.5rem;
            }
            
            .login-body {
                padding: 1.5rem;
            }
            
            .login-header h1 {
                font-size: 1.5rem;
            }
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
                <h1><i class="fas fa-bullhorn me-2"></i>Адмін панель</h1>
                <p>Дошка Оголошень</p>
            </div>
            
            <div class="login-body">
                <?php if ($error_message): ?>
                    <div class="alert alert-danger d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" novalidate>
                    <div class="form-floating position-relative">
                        <input type="text" class="form-control" id="username" name="username" 
                               placeholder="Логін або email" required 
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                        <label for="username">
                            <i class="fas fa-user me-2"></i>Логін або email
                        </label>
                    </div>
                    
                    <div class="form-floating position-relative">
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Пароль" required>
                        <label for="password">
                            <i class="fas fa-lock me-2"></i>Пароль
                        </label>
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="passwordIcon"></i>
                        </button>
                    </div>
                    
                    <div class="remember-check">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Запам'ятати мене
                            </label>
                        </div>
                        <a href="#" class="text-decoration-none small" onclick="showForgotPassword()">
                            Забули пароль?
                        </a>
                    </div>
                    
                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>Увійти
                    </button>
                </form>
            </div>
            
            <div class="login-footer">
                <small class="text-muted">
                    <i class="fas fa-shield-alt me-1"></i>
                    Безпечний вхід в адмін панель
                </small>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                passwordIcon.className = 'fas fa-eye';
            }
        }
        
        function showForgotPassword() {
            alert('Для відновлення пароля зверніться до системного адміністратора.');
        }
        
        // Автофокус на поле логіну
        document.addEventListener('DOMContentLoaded', function() {
            const usernameField = document.getElementById('username');
            if (usernameField.value === '') {
                usernameField.focus();
            } else {
                document.getElementById('password').focus();
            }
        });
        
        // Анімація помилок
        const alert = document.querySelector('.alert');
        if (alert) {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                alert.style.transition = 'all 0.3s ease';
                alert.style.opacity = '1';
                alert.style.transform = 'translateY(0)';
            }, 100);
        }
    </script>
</body>
</html>