<?php
require_once '../config/config.php';
require_once '../config/database.php';

// Налаштування META тегів
$page_title = 'Вхід - ' . Settings::get('site_name', 'Дошка Оголошень');
$page_description = 'Увійдіть у свій обліковий запис на ' . Settings::get('site_name', 'Дошка Оголошень');
$page_keywords = 'вхід, авторизація, логін, ' . Settings::get('site_keywords', '');
$error_message = '';
$success_message = '';

// Перевіряємо, чи користувач вже авторизований
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'login') {
        $email = clean_input($_POST['email']);
        $password = $_POST['password'];
        $remember = isset($_POST['remember']);
        
        if (empty($email) || empty($password)) {
            $error_message = 'Заповніть всі поля!';
        } else {
            try {
                $database = new Database();
                $db = $database->getConnection();
                
                // Пошук користувача
                $query = "SELECT id, username, email, password, is_active FROM users WHERE email = ? OR username = ?";
                $stmt = $db->prepare($query);
                $stmt->execute([$email, $email]);
                
                if ($stmt->rowCount() === 1) {
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if (!$user['is_active']) {
                        $error_message = 'Акаунт заблокований. Зверніться до адміністратора.';
                    } elseif (password_verify($password, $user['password'])) {
                        // Успішна авторизація
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['email'] = $user['email'];
                        
                        // Запам'ятати користувача
                        if ($remember) {
                            $token = bin2hex(random_bytes(32));
                            setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/'); // 30 днів
                            
                            // Зберігаємо токен в базі даних
                            $update_query = "UPDATE users SET remember_token = ? WHERE id = ?";
                            $update_stmt = $db->prepare($update_query);
                            $update_stmt->execute([$token, $user['id']]);
                        }
                        
                        // Перенаправлення
                        header("Location: ../index.php");
                        exit();
                    } else {
                        $error_message = 'Неправильний пароль!';
                    }
                } else {
                    $error_message = 'Користувача з таким email не знайдено!';
                }
            } catch (Exception $e) {
                $error_message = 'Помилка: ' . $e->getMessage();
            }
        }
    } elseif ($_POST['action'] === 'reset_password') {
        $email = clean_input($_POST['reset_email']);
        
        if (empty($email)) {
            $error_message = 'Введіть email для відновлення пароля';
        } else {
            try {
                $database = new Database();
                $db = $database->getConnection();
                
                $query = "SELECT id, username FROM users WHERE email = ? AND is_active = 1";
                $stmt = $db->prepare($query);
                $stmt->execute([$email]);
                
                if ($stmt->rowCount() === 1) {
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    // Генеруємо токен
                    $reset_token = bin2hex(random_bytes(32));
                    $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
                    
                    // Зберігаємо токен
                    $update_query = "UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE id = ?";
                    $update_stmt = $db->prepare($update_query);
                    $update_stmt->execute([$reset_token, $expires_at, $user['id']]);
                    
                    $success_message = "Інструкції з відновлення пароля надіслано на ваш email.<br>
                                      <small class='text-muted'>Посилання для тестування: 
                                      <a href='reset_password.php?token={$reset_token}' class='text-decoration-none'>Відновити пароль</a></small>";
                } else {
                    $error_message = 'Користувача з таким email не знайдено';
                }
            } catch (Exception $e) {
                $error_message = 'Помилка: ' . $e->getMessage();
            }
        }
    }
}

$meta_data = Settings::getMetaTags($page_title, $page_description, $page_keywords);
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    
    <?php echo $meta_data['meta_tags']; ?>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AOS Animation -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    
    <style>
        <?php echo Theme::generateCSS(); ?>
        
        body {
            background: var(--theme-gradient);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px 0;
        }
        
        .auth-container {
            max-width: 480px;
            width: 100%;
            padding: 0 20px;
        }
        
        .auth-card {
            background: var(--card-bg);
            border-radius: 25px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
            overflow: hidden;
            border: 1px solid var(--border-color);
            backdrop-filter: blur(10px);
        }
        
        .auth-header {
            background: var(--theme-gradient);
            color: white;
            padding: 2.5rem 2rem;
            text-align: center;
            position: relative;
        }
        
        .auth-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(5px);
        }
        
        .auth-header-content {
            position: relative;
            z-index: 2;
        }
        
        .auth-icon {
            background: rgba(255,255,255,0.2);
            width: 90px;
            height: 90px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem auto;
            font-size: 2.5rem;
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .auth-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .auth-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 1.1rem;
        }
        
        .auth-body {
            padding: 2.5rem 2rem;
        }
        
        .form-floating {
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .form-floating .form-control {
            border: 2px solid var(--border-color);
            border-radius: 15px;
            height: 60px;
            transition: all 0.3s ease;
            background: var(--surface-color);
            color: var(--text-color);
            font-size: 1.1rem;
            padding-left: 50px;
        }
        
        .form-floating .form-control:focus {
            border-color: var(--theme-primary);
            box-shadow: 0 0 0 0.2rem rgba(var(--theme-primary-rgb), 0.25);
            background: var(--surface-color);
            transform: translateY(-2px);
        }
        
        .form-floating label {
            color: var(--text-muted);
            font-weight: 500;
            padding-left: 50px;
        }
        
        .form-floating .form-control:focus ~ label,
        .form-floating .form-control:not(:placeholder-shown) ~ label {
            padding-left: 50px;
        }
        
        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 1.2rem;
            z-index: 5;
            transition: color 0.3s ease;
        }
        
        .form-floating .form-control:focus ~ .input-icon {
            color: var(--theme-primary);
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            z-index: 10;
            font-size: 1.1rem;
            padding: 5px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .password-toggle:hover {
            color: var(--theme-primary);
            background: rgba(var(--theme-primary-rgb), 0.1);
        }
        
        .btn-auth {
            background: var(--theme-gradient);
            border: none;
            padding: 15px 30px;
            border-radius: 15px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            width: 100%;
            font-size: 1.1rem;
            position: relative;
            overflow: hidden;
        }
        
        .btn-auth::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.2);
            transition: left 0.5s ease;
        }
        
        .btn-auth:hover::before {
            left: 100%;
        }
        
        .btn-auth:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(var(--theme-primary-rgb), 0.4);
            color: white;
        }
        
        .btn-auth:active {
            transform: translateY(-1px);
        }
        
        .form-check {
            margin-bottom: 1.5rem;
        }
        
        .form-check-input:checked {
            background-color: var(--theme-primary);
            border-color: var(--theme-primary);
        }
        
        .form-check-input:focus {
            border-color: var(--theme-primary);
            box-shadow: 0 0 0 0.2rem rgba(var(--theme-primary-rgb), 0.25);
        }
        
        .auth-links {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border-color);
        }
        
        .auth-links a {
            color: var(--theme-primary);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .auth-links a:hover {
            color: var(--theme-primary);
            transform: translateY(-1px);
        }
        
        .auth-links a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--theme-gradient);
            transition: width 0.3s ease;
        }
        
        .auth-links a:hover::after {
            width: 100%;
        }
        
        .divider {
            display: flex;
            align-items: center;
            margin: 2rem 0;
            color: var(--text-muted);
        }
        
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border-color);
        }
        
        .divider span {
            padding: 0 1rem;
            font-size: 0.9rem;
        }
        
        .alert {
            border: none;
            border-radius: 15px;
            margin-bottom: 1.5rem;
            animation: slideInDown 0.5s ease-out;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #00b894, #00cec9);
            color: white;
        }
        
        @keyframes slideInDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .btn-outline-secondary {
            border: 2px solid var(--border-color);
            color: var(--text-color);
            border-radius: 15px;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }
        
        .btn-outline-secondary:hover {
            background: var(--theme-primary);
            border-color: var(--theme-primary);
            color: white;
            transform: translateY(-2px);
        }
        
        .loading {
            display: none;
        }
        
        .loading.show {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Responsive */
        @media (max-width: 576px) {
            .auth-container {
                padding: 0 15px;
            }
            
            .auth-header {
                padding: 2rem 1.5rem;
            }
            
            .auth-body {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card" data-aos="fade-up" data-aos-duration="800">
            <div class="auth-header">
                <div class="auth-header-content">
                    <div class="auth-icon">
                        <i class="fas fa-sign-in-alt"></i>
                    </div>
                    <h1>Вітаємо знову!</h1>
                    <p>Увійдіть у свій обліковий запис</p>
                </div>
            </div>
            
            <div class="auth-body">
                <?php if ($error_message): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success_message): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Форма входу -->
                <form method="POST" action="" id="loginForm">
                    <input type="hidden" name="action" value="login">
                    
                    <div class="form-floating">
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="Email або логін" required 
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        <label for="email">Email або логін</label>
                        <i class="fas fa-envelope input-icon"></i>
                    </div>
                    
                    <div class="form-floating">
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Пароль" required>
                        <label for="password">Пароль</label>
                        <i class="fas fa-lock input-icon"></i>
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember" 
                               <?php echo isset($_POST['remember']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="remember">
                            Запам'ятати мене
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-auth">
                        <span class="btn-text">
                            <i class="fas fa-sign-in-alt me-2"></i>Увійти
                        </span>
                        <span class="loading">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                    </button>
                </form>
                
                <div class="divider">
                    <span>або</span>
                </div>
                
                <!-- Відновлення пароля -->
                <div class="text-center">
                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#resetModal">
                        <i class="fas fa-key me-2"></i>Забули пароль?
                    </button>
                </div>
                
                <div class="auth-links">
                    <p class="mb-0">Ще немає акаунту? 
                        <a href="register.php" data-spa data-page="register">Зареєструватися</a>
                    </p>
                    <p class="mt-2 mb-0">
                        <a href="../index.php" data-spa data-page="home">
                            <i class="fas fa-arrow-left me-1"></i>Повернутися на головну
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal відновлення пароля -->
    <div class="modal fade" id="resetModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 20px; border: none;">
                <div class="modal-header" style="background: var(--theme-gradient); color: white; border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title">
                        <i class="fas fa-key me-2"></i>Відновлення пароля
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="padding: 2rem;">
                    <form method="POST" action="" id="resetForm">
                        <input type="hidden" name="action" value="reset_password">
                        
                        <p class="text-muted mb-3">Введіть ваш email і ми надішлемо інструкції з відновлення пароля</p>
                        
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="reset_email" name="reset_email" 
                                   placeholder="Ваш email" required style="border-radius: 15px; height: 55px;">
                            <label for="reset_email">Ваш email</label>
                        </div>
                        
                        <button type="submit" class="btn btn-auth">
                            <i class="fas fa-paper-plane me-2"></i>Надіслати
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS Animation -->
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    
    <script>
        // Ініціалізація AOS
        AOS.init({
            duration: 800,
            easing: 'ease-out-cubic',
            once: true
        });
        
        // Функція перемикання видимості пароля
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling.nextElementSibling.querySelector('i');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        // Анімація кнопки при відправці форми
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = this.querySelector('.btn-auth');
            const btnText = btn.querySelector('.btn-text');
            const loading = btn.querySelector('.loading');
            
            btnText.style.display = 'none';
            loading.style.display = 'inline-block';
            btn.disabled = true;
        });
        
        document.getElementById('resetForm').addEventListener('submit', function() {
            const btn = this.querySelector('.btn-auth');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Надсилаємо...';
            btn.disabled = true;
        });
        
        // Плавна анімація для input полів
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
        
        // Частки що рухаються на фоні
        function createParticles() {
            const particles = 50;
            const body = document.body;
            
            for (let i = 0; i < particles; i++) {
                const particle = document.createElement('div');
                particle.style.cssText = `
                    position: fixed;
                    width: 4px;
                    height: 4px;
                    background: rgba(255,255,255,0.3);
                    border-radius: 50%;
                    pointer-events: none;
                    z-index: 1;
                    animation: float ${Math.random() * 3 + 2}s ease-in-out infinite;
                    top: ${Math.random() * 100}vh;
                    left: ${Math.random() * 100}vw;
                    animation-delay: ${Math.random() * 2}s;
                `;
                body.appendChild(particle);
            }
        }
        
        // Запускаємо частки
        createParticles();
    </script>
</body>
</html>