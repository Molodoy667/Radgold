<?php
session_start();
require_once '../core/database.php';
require_once '../core/functions.php';

// Перевірка чи не зареєстрований вже головний адмін
$db = Database::getInstance();
$existingAdmin = $db->query("SELECT id FROM users WHERE role = 'super_admin' LIMIT 1");

if (!empty($existingAdmin)) {
    header('Location: ../admin/');
    exit('Головний адміністратор вже зареєстрований!');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $site_name = trim($_POST['site_name'] ?? '');
    $site_description = trim($_POST['site_description'] ?? '');
    
    // Валідація
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Всі поля обов\'язкові для заповнення!';
    } elseif ($password !== $confirm_password) {
        $error = 'Паролі не співпадають!';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль має містити мінімум 6 символів!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Невірний формат email!';
    } else {
        try {
            // Перевірка унікальності
            $existingUser = $db->query("SELECT id FROM users WHERE username = ? OR email = ?", [$username, $email]);
            if (!empty($existingUser)) {
                $error = 'Користувач з таким ім\'ям або email вже існує!';
            } else {
                // Створення головного адміністратора
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                $adminData = [
                    'username' => $username,
                    'email' => $email,
                    'password' => $hashedPassword,
                    'role' => 'super_admin',
                    'user_type' => 'admin',
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s')
                ];
                
                $adminId = $db->insert('users', $adminData);
                
                if ($adminId) {
                    // Оновлення налаштувань сайту
                    if (!empty($site_name)) {
                        $db->query("UPDATE site_settings SET setting_value = ? WHERE setting_name = 'site_name'", [$site_name]);
                    }
                    if (!empty($site_description)) {
                        $db->query("UPDATE site_settings SET setting_value = ? WHERE setting_name = 'site_description'", [$site_description]);
                    }
                    
                    $success = 'Головний адміністратор успішно створений!';
                    
                    // Автоматичний вхід
                    $_SESSION['user_id'] = $adminId;
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = 'super_admin';
                    
                    // Перенаправлення через 2 секунди
                    header("refresh:2;url=../admin/");
                } else {
                    $error = 'Помилка при створенні адміністратора!';
                }
            }
        } catch (Exception $e) {
            $error = 'Помилка бази даних: ' . $e->getMessage();
        }
    }
}

// Отримання поточних налаштувань
$currentSettings = [];
try {
    $settings = $db->query("SELECT setting_name, setting_value FROM site_settings WHERE setting_name IN ('site_name', 'site_description')");
    foreach ($settings as $setting) {
        $currentSettings[$setting['setting_name']] = $setting['setting_value'];
    }
} catch (Exception $e) {
    // Ігноруємо помилки при отриманні налаштувань
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Налаштування головного адміністратора</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --dark-bg: #0d1117;
            --dark-secondary: #161b22;
            --dark-tertiary: #21262d;
        }
        
        body {
            background: var(--dark-bg);
            color: white;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .setup-container {
            background: var(--dark-secondary);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
            border: 1px solid #30363d;
        }
        
        .setup-header {
            background: var(--primary-gradient);
            padding: 30px;
            text-align: center;
            position: relative;
        }
        
        .setup-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.1;
        }
        
        .setup-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            position: relative;
            z-index: 1;
        }
        
        .setup-header .icon {
            font-size: 3rem;
            margin-bottom: 15px;
            position: relative;
            z-index: 1;
        }
        
        .setup-body {
            padding: 40px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            color: #f0f6fc;
            font-weight: 600;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .form-control {
            background: var(--dark-tertiary);
            border: 1px solid #30363d;
            border-radius: 12px;
            color: white;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            background: var(--dark-tertiary);
            border-color: #58a6ff;
            box-shadow: 0 0 0 3px rgba(88, 166, 255, 0.2);
            color: white;
        }
        
        .form-control::placeholder {
            color: #7d8590;
        }
        
        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            border-radius: 12px;
            padding: 12px 30px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            filter: brightness(1.1);
        }
        
        .alert {
            border: none;
            border-radius: 12px;
            padding: 15px 20px;
            margin-bottom: 25px;
            font-weight: 500;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #ff4757 0%, #ff3838 100%);
            color: white;
        }
        
        .alert-success {
            background: var(--success-gradient);
            color: white;
        }
        
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
        
        .loading.show {
            display: block;
        }
        
        .spinner {
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top: 3px solid white;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .setup-footer {
            background: var(--dark-tertiary);
            padding: 20px;
            text-align: center;
            color: #7d8590;
            font-size: 0.9rem;
        }
        
        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #30363d, transparent);
            margin: 30px 0;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #7d8590;
            z-index: 10;
        }
        
        .form-control.with-icon {
            padding-left: 45px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="setup-container mx-auto">
                    <div class="setup-header">
                        <div class="icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h1>Налаштування адміністратора</h1>
                        <p class="mb-0">Створення головного адміністратора системи</p>
                    </div>
                    
                    <div class="setup-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <?php echo htmlspecialchars($success); ?>
                            </div>
                            <div class="loading show">
                                <div class="spinner"></div>
                                <p>Перенаправлення в адмін панель...</p>
                            </div>
                        <?php else: ?>
                            <form method="POST" id="setupForm">
                                <!-- Налаштування сайту -->
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-globe"></i>
                                        Назва сайту
                                    </label>
                                    <div class="input-group">
                                        <i class="input-icon fas fa-tag"></i>
                                        <input type="text" class="form-control with-icon" name="site_name" 
                                               value="<?php echo htmlspecialchars($currentSettings['site_name'] ?? ''); ?>"
                                               placeholder="Введіть назву сайту">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-info-circle"></i>
                                        Опис сайту
                                    </label>
                                    <div class="input-group">
                                        <i class="input-icon fas fa-align-left"></i>
                                        <textarea class="form-control with-icon" name="site_description" rows="3"
                                                  placeholder="Введіть опис сайту"><?php echo htmlspecialchars($currentSettings['site_description'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                                
                                <div class="divider"></div>
                                
                                <!-- Дані адміністратора -->
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-user"></i>
                                        Ім'я користувача
                                    </label>
                                    <div class="input-group">
                                        <i class="input-icon fas fa-user"></i>
                                        <input type="text" class="form-control with-icon" name="username" required
                                               placeholder="Введіть ім'я користувача">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-envelope"></i>
                                        Email адреса
                                    </label>
                                    <div class="input-group">
                                        <i class="input-icon fas fa-envelope"></i>
                                        <input type="email" class="form-control with-icon" name="email" required
                                               placeholder="Введіть email адресу">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-lock"></i>
                                        Пароль
                                    </label>
                                    <div class="input-group">
                                        <i class="input-icon fas fa-lock"></i>
                                        <input type="password" class="form-control with-icon" name="password" required
                                               placeholder="Введіть пароль (мін. 6 символів)">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="form-label">
                                        <i class="fas fa-check"></i>
                                        Підтвердження паролю
                                    </label>
                                    <div class="input-group">
                                        <i class="input-icon fas fa-check"></i>
                                        <input type="password" class="form-control with-icon" name="confirm_password" required
                                               placeholder="Повторіть пароль">
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-user-plus me-2"></i>
                                    Створити адміністратора
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                    
                    <div class="setup-footer">
                        <i class="fas fa-shield-alt me-1"></i>
                        Безпечне створення головного адміністратора
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Додаткова валідація на клієнті
        document.getElementById('setupForm')?.addEventListener('submit', function(e) {
            const password = document.querySelector('input[name="password"]').value;
            const confirmPassword = document.querySelector('input[name="confirm_password"]').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Паролі не співпадають!');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Пароль має містити мінімум 6 символів!');
                return false;
            }
        });
        
        // Анімація для input полів
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>