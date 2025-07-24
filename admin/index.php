<?php
require_once '../core/config.php';
require_once '../core/database.php'; 
require_once '../core/functions.php';

// Якщо користувач вже авторизований, перенаправляємо в дашборд
if (isLoggedIn() && isAdmin()) {
    redirect(SITE_URL . '/admin/dashboard.php');
}

$action = $_GET['action'] ?? 'login';
$error = '';
$success = '';

// Обробка форми входу
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'login') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (empty($email) || empty($password)) {
        $error = 'Заповніть всі поля';
    } else {
        try {
            $db = Database::getInstance();
            $result = $db->query(
                "SELECT id, username, email, password, role, status, failed_login_attempts, blocked_until FROM users WHERE email = ? AND role = 'admin'",
                [$email]
            );
            
            if ($user = $result->fetch_assoc()) {
                // Перевірка блокування
                if ($user['blocked_until'] && strtotime($user['blocked_until']) > time()) {
                    $error = 'Обліковий запис тимчасово заблокований до ' . date('d.m.Y H:i', strtotime($user['blocked_until']));
                } elseif ($user['status'] !== 'active') {
                    $error = 'Обліковий запис заблокований';
                } elseif (password_verify($password, $user['password'])) {
                    // Успішна авторизація
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];
                    
                    // Скидаємо лічильник невдалих спроб
                    $db->query(
                        "UPDATE users SET last_login = NOW(), failed_login_attempts = 0, blocked_until = NULL WHERE id = ?",
                        [$user['id']]
                    );
                    
                    // Запам'ятати користувача якщо потрібно
                    if ($remember) {
                        $token = bin2hex(random_bytes(32));
                        $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
                        
                        $db->query(
                            "INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (?, ?, ?)",
                            [$user['id'], $token, $expires]
                        );
                        
                        setcookie('remember_token', $token, strtotime('+30 days'), '/', '', false, true);
                    }
                    
                    // Логування входу
                    logActivity($user['id'], 'admin_login', 'Вхід в адмін-панель', ['ip' => getClientIP()]);
                    
                    redirect(SITE_URL . '/admin/dashboard.php');
                } else {
                    // Невдала спроба входу
                    $attempts = $user['failed_login_attempts'] + 1;
                    $maxAttempts = 5;
                    
                    if ($attempts >= $maxAttempts) {
                        // Блокуємо на 15 хвилин
                        $blockedUntil = date('Y-m-d H:i:s', strtotime('+15 minutes'));
                        $db->query(
                            "UPDATE users SET failed_login_attempts = ?, blocked_until = ? WHERE id = ?",
                            [$attempts, $blockedUntil, $user['id']]
                        );
                        $error = 'Забагато невдалих спроб входу. Обліковий запис заблокований на 15 хвилин.';
                    } else {
                        $db->query(
                            "UPDATE users SET failed_login_attempts = ? WHERE id = ?",
                            [$attempts, $user['id']]
                        );
                        $remaining = $maxAttempts - $attempts;
                        $error = "Невірний email або пароль. Залишилось спроб: $remaining";
                    }
                }
            } else {
                $error = 'Невірний email або пароль';
            }
        } catch (Exception $e) {
            $error = 'Помилка підключення до бази даних';
            logError('Admin login error: ' . $e->getMessage());
        }
    }
}

// Обробка відновлення паролю
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'reset') {
    $email = sanitize($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = 'Введіть email адресу';
    } else {
        try {
            $db = Database::getInstance();
            $result = $db->query(
                "SELECT id, email, username FROM users WHERE email = ? AND role = 'admin' AND status = 'active'",
                [$email]
            );
            
            if ($user = $result->fetch_assoc()) {
                // Генеруємо токен для скидання паролю
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Видаляємо старі токени
                $db->query("DELETE FROM password_resets WHERE email = ?", [$email]);
                
                // Вставляємо новий токен
                $db->query(
                    "INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)",
                    [$email, $token, $expires]
                );
                
                // Відправляємо email
                $resetLink = SITE_URL . "/admin/index.php?action=new_password&token=$token";
                $subject = 'Відновлення паролю - ' . SITE_NAME;
                $message = "
                    <h2>Відновлення паролю</h2>
                    <p>Привіт, {$user['username']}!</p>
                    <p>Ви запросили відновлення паролю для адмін-панелі сайту " . SITE_NAME . "</p>
                    <p>Перейдіть за посиланням для створення нового паролю:</p>
                    <p><a href='$resetLink' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Створити новий пароль</a></p>
                    <p>Посилання дійсне протягом 1 години.</p>
                    <p>Якщо ви не запросували відновлення паролю, просто проігноруйте цей лист.</p>
                    <hr>
                    <small>IP адреса запиту: " . getClientIP() . "</small>
                ";
                
                if (sendEmail($email, $subject, $message)) {
                    $success = 'Посилання для відновлення паролю відправлено на ваш email';
                    logActivity(null, 'password_reset_request', 'Запит відновлення паролю', ['email' => $email, 'ip' => getClientIP()]);
                } else {
                    $error = 'Помилка відправки email. Спробуйте пізніше.';
                }
            } else {
                // Не розкриваємо чи існує такий email
                $success = 'Якщо такий email існує в системі, посилання для відновлення буде відправлено';
            }
        } catch (Exception $e) {
            $error = 'Помилка системи. Спробуйте пізніше.';
            logError('Password reset error: ' . $e->getMessage());
        }
    }
}

// Обробка встановлення нового паролю
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'new_password') {
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($password) || empty($confirmPassword)) {
        $error = 'Заповніть всі поля';
    } elseif ($password !== $confirmPassword) {
        $error = 'Паролі не співпадають';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль має містити мінімум 6 символів';
    } else {
        try {
            $db = Database::getInstance();
            $result = $db->query(
                "SELECT email, expires_at FROM password_resets WHERE token = ? AND used = 0",
                [$token]
            );
            
            if ($reset = $result->fetch_assoc()) {
                if (strtotime($reset['expires_at']) < time()) {
                    $error = 'Термін дії посилання закінчився. Запросіть нове відновлення.';
                } else {
                    // Оновлюємо пароль
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $db->query(
                        "UPDATE users SET password = ? WHERE email = ? AND role = 'admin'",
                        [$hashedPassword, $reset['email']]
                    );
                    
                    // Позначаємо токен як використаний
                    $db->query(
                        "UPDATE password_resets SET used = 1 WHERE token = ?",
                        [$token]
                    );
                    
                    // Видаляємо всі remember токени користувача
                    $db->query("DELETE FROM remember_tokens WHERE user_id = (SELECT id FROM users WHERE email = ?)", [$reset['email']]);
                    
                    $success = 'Пароль успішно змінено. Тепер ви можете увійти з новим паролем.';
                    logActivity(null, 'password_reset_complete', 'Пароль змінено', ['email' => $reset['email'], 'ip' => getClientIP()]);
                }
            } else {
                $error = 'Недійсне посилання для відновлення';
            }
        } catch (Exception $e) {
            $error = 'Помилка системи. Спробуйте пізніше.';
            logError('New password error: ' . $e->getMessage());
        }
    }
}

// Перевірка токену для нового паролю
$validToken = false;
if ($action === 'new_password' && isset($_GET['token'])) {
    $token = $_GET['token'];
    try {
        $db = Database::getInstance();
        $result = $db->query(
            "SELECT expires_at FROM password_resets WHERE token = ? AND used = 0",
            [$token]
        );
        
        if ($reset = $result->fetch_assoc()) {
            $validToken = strtotime($reset['expires_at']) > time();
        }
    } catch (Exception $e) {
        logError('Token validation error: ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $action === 'reset' ? 'Відновлення паролю' : ($action === 'new_password' ? 'Новий пароль' : 'Вхід в адмін-панель'); ?> - <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .admin-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }
        
        .admin-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .admin-header i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }
        
        .admin-body {
            padding: 2rem;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-admin {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        .form-check-input:checked {
            background-color: #667eea;
            border-color: #667eea;
        }
        
        .link-secondary {
            color: #6c757d !important;
            text-decoration: none;
        }
        
        .link-secondary:hover {
            color: #667eea !important;
        }
        
        .password-strength {
            margin-top: 8px;
            font-size: 0.875rem;
        }
        
        .strength-bar {
            height: 4px;
            border-radius: 2px;
            margin-top: 4px;
            background: #e9ecef;
            overflow: hidden;
        }
        
        .strength-fill {
            height: 100%;
            transition: all 0.3s ease;
            width: 0%;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="admin-card">
                    <div class="admin-header">
                        <i class="fas fa-shield-alt"></i>
                        <h2 class="mb-0">
                            <?php if ($action === 'reset'): ?>
                                Відновлення паролю
                            <?php elseif ($action === 'new_password'): ?>
                                Новий пароль
                            <?php else: ?>
                                Адмін-панель
                            <?php endif; ?>
                        </h2>
                        <p class="mb-0 opacity-75"><?php echo SITE_NAME; ?></p>
                    </div>
                    
                    <div class="admin-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($action === 'login'): ?>
                            <!-- Форма входу -->
                            <form method="POST" class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-2"></i>Email адреса
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                                           placeholder="admin@example.com" required>
                                    <div class="invalid-feedback">
                                        Введіть коректний email
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">
                                        <i class="fas fa-lock me-2"></i>Пароль
                                    </label>
                                    <div class="position-relative">
                                        <input type="password" class="form-control" id="password" name="password" 
                                               placeholder="Введіть пароль" required>
                                        <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y" 
                                                onclick="togglePassword()">
                                            <i class="fas fa-eye" id="passwordToggle"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback">
                                        Введіть пароль
                                    </div>
                                </div>
                                
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">
                                        Запам'ятати мене
                                    </label>
                                </div>
                                
                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-admin text-white">
                                        <i class="fas fa-sign-in-alt me-2"></i>Увійти
                                    </button>
                                </div>
                                
                                <div class="text-center">
                                    <a href="?action=reset" class="link-secondary">
                                        <i class="fas fa-key me-1"></i>Забули пароль?
                                    </a>
                                </div>
                            </form>
                            
                        <?php elseif ($action === 'reset'): ?>
                            <!-- Форма відновлення паролю -->
                            <form method="POST" class="needs-validation" novalidate>
                                <input type="hidden" name="action" value="reset">
                                
                                <p class="text-muted mb-4">
                                    Введіть ваш email адрес і ми відправимо посилання для відновлення паролю.
                                </p>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-2"></i>Email адреса
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                                           placeholder="admin@example.com" required>
                                    <div class="invalid-feedback">
                                        Введіть коректний email
                                    </div>
                                </div>
                                
                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-admin text-white">
                                        <i class="fas fa-paper-plane me-2"></i>Відправити посилання
                                    </button>
                                </div>
                                
                                <div class="text-center">
                                    <a href="?" class="link-secondary">
                                        <i class="fas fa-arrow-left me-1"></i>Повернутися до входу
                                    </a>
                                </div>
                            </form>
                            
                        <?php elseif ($action === 'new_password' && $validToken): ?>
                            <!-- Форма нового паролю -->
                            <form method="POST" class="needs-validation" novalidate>
                                <input type="hidden" name="action" value="new_password">
                                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
                                
                                <p class="text-muted mb-4">
                                    Створіть новий пароль для вашого облікового запису.
                                </p>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label">
                                        <i class="fas fa-lock me-2"></i>Новий пароль
                                    </label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           placeholder="Мінімум 6 символів" required minlength="6">
                                    <div class="password-strength">
                                        <div class="strength-bar">
                                            <div class="strength-fill" id="strengthFill"></div>
                                        </div>
                                        <small id="strengthText" class="text-muted"></small>
                                    </div>
                                    <div class="invalid-feedback">
                                        Пароль має містити мінімум 6 символів
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">
                                        <i class="fas fa-lock me-2"></i>Підтвердження паролю
                                    </label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                           placeholder="Повторіть пароль" required>
                                    <div class="invalid-feedback">
                                        Паролі не співпадають
                                    </div>
                                </div>
                                
                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-admin text-white">
                                        <i class="fas fa-save me-2"></i>Зберегти пароль
                                    </button>
                                </div>
                            </form>
                            
                        <?php elseif ($action === 'new_password'): ?>
                            <!-- Недійсний токен -->
                            <div class="text-center">
                                <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                                <h5 class="mt-3">Недійсне посилання</h5>
                                <p class="text-muted">Посилання для відновлення паролю недійсне або його термін дії закінчився.</p>
                                <a href="?action=reset" class="btn btn-admin text-white">
                                    <i class="fas fa-redo me-2"></i>Запросити нове посилання
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <small class="text-white-50">
                        © <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Всі права захищені.
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Валідація форм
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
        
        // Переключення видимості паролю
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const passwordToggle = document.getElementById('passwordToggle');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                passwordToggle.classList.remove('fa-eye');
                passwordToggle.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                passwordToggle.classList.remove('fa-eye-slash');
                passwordToggle.classList.add('fa-eye');
            }
        }
        
        // Перевірка сили паролю
        <?php if ($action === 'new_password'): ?>
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('confirm_password');
        const strengthFill = document.getElementById('strengthFill');
        const strengthText = document.getElementById('strengthText');
        
        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                const strength = calculatePasswordStrength(password);
                updateStrengthDisplay(strength);
            });
        }
        
        if (confirmInput) {
            confirmInput.addEventListener('input', function() {
                const password = passwordInput.value;
                const confirm = this.value;
                
                if (confirm && password !== confirm) {
                    this.setCustomValidity('Паролі не співпадають');
                } else {
                    this.setCustomValidity('');
                }
            });
        }
        
        function calculatePasswordStrength(password) {
            let score = 0;
            
            if (password.length >= 6) score += 20;
            if (password.length >= 8) score += 20;
            if (/[a-z]/.test(password)) score += 20;
            if (/[A-Z]/.test(password)) score += 20;
            if (/[0-9]/.test(password)) score += 10;
            if (/[^A-Za-z0-9]/.test(password)) score += 10;
            
            return Math.min(score, 100);
        }
        
        function updateStrengthDisplay(strength) {
            strengthFill.style.width = strength + '%';
            
            if (strength < 40) {
                strengthFill.style.background = '#dc3545';
                strengthText.textContent = 'Слабкий пароль';
                strengthText.className = 'text-danger';
            } else if (strength < 70) {
                strengthFill.style.background = '#ffc107';
                strengthText.textContent = 'Середній пароль';
                strengthText.className = 'text-warning';
            } else {
                strengthFill.style.background = '#28a745';
                strengthText.textContent = 'Сильний пароль';
                strengthText.className = 'text-success';
            }
        }
        <?php endif; ?>
        
        // Автоматичне приховування повідомлень
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                if (alert.classList.contains('alert-success')) {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.remove();
                    }, 500);
                }
            });
        }, 5000);
    </script>
</body>
</html>