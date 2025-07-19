<?php
require_once '../config/config.php';
require_once '../config/database.php';

// Налаштування META тегів
$page_title = 'Реєстрація - ' . Settings::get('site_name', 'Дошка Оголошень');
$page_description = 'Створіть новий обліковий запис на ' . Settings::get('site_name', 'Дошка Оголошень');
$page_keywords = 'реєстрація, новий акаунт, ' . Settings::get('site_keywords', '');
$error_message = '';
$success_message = '';

// Перевіряємо, чи реєстрація дозволена
if (!Settings::get('registration_enabled', true)) {
    header("Location: ../index.php");
    exit();
}

// Перевіряємо, чи користувач вже авторизований
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean_input($_POST['username']);
    $email = clean_input($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $agree_terms = isset($_POST['agree_terms']);
    
    // Валідація
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = 'Заповніть всі обов\'язкові поля!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Невірний формат email!';
    } elseif (strlen($username) < 3) {
        $error_message = 'Ім\'я користувача має містити мінімум 3 символи!';
    } elseif (strlen($password) < 6) {
        $error_message = 'Пароль має містити мінімум 6 символів!';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Паролі не співпадають!';
    } elseif (!$agree_terms) {
        $error_message = 'Необхідно погодитися з умовами використання!';
    } else {
        try {
            $database = new Database();
            $db = $database->getConnection();
            
            // Перевіряємо унікальність email та username
            $check_query = "SELECT id FROM users WHERE email = ? OR username = ?";
            $check_stmt = $db->prepare($check_query);
            $check_stmt->execute([$email, $username]);
            
            if ($check_stmt->rowCount() > 0) {
                $error_message = 'Користувач з таким email або ім\'ям вже існує!';
            } else {
                // Створюємо користувача
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $activation_token = bin2hex(random_bytes(32));
                
                $insert_query = "INSERT INTO users (username, email, password, activation_token, created_at) 
                                VALUES (?, ?, ?, ?, NOW())";
                $insert_stmt = $db->prepare($insert_query);
                
                if ($insert_stmt->execute([$username, $email, $hashed_password, $activation_token])) {
                    $user_id = $db->lastInsertId();
                    
                    // Автоматично активуємо акаунт (в реальному проекті слід відправити email)
                    $activate_query = "UPDATE users SET is_active = 1, activation_token = NULL WHERE id = ?";
                    $activate_stmt = $db->prepare($activate_query);
                    $activate_stmt->execute([$user_id]);
                    
                    // Автоматично входимо
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['username'] = $username;
                    $_SESSION['email'] = $email;
                    
                    $success_message = 'Реєстрація успішна! Перенаправляємо...';
                    
                    // Перенаправлення через 2 секунди
                    header("refresh:2;url=../index.php");
                } else {
                    $error_message = 'Помилка створення акаунту. Спробуйте пізніше.';
                }
            }
        } catch (Exception $e) {
            $error_message = 'Помилка: ' . $e->getMessage();
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
            max-width: 520px;
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
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
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
            position: relative;
        }
        
        .form-floating .form-control:hover {
            border-color: transparent;
            background: var(--card-bg);
            background-image: var(--theme-gradient);
            background-size: 100% 3px;
            background-position: 0 100%;
            background-repeat: no-repeat;
            box-shadow: 0 6px 20px rgba(var(--theme-primary-rgb), 0.15);
            transform: translateY(-1px);
        }
        
        .form-floating .form-control:focus {
            border-color: transparent;
            background: var(--card-bg);
            color: var(--text-color);
            box-shadow: 
                0 0 0 4px rgba(var(--theme-primary-rgb), 0.2),
                0 10px 30px rgba(var(--theme-primary-rgb), 0.25);
            transform: translateY(-3px);
            background-image: var(--theme-gradient);
            background-size: 100% 3px;
            background-position: 0 100%;
            background-repeat: no-repeat;
        }
        
        .form-floating .form-control::placeholder {
            color: var(--text-muted);
            opacity: 0.7;
            transition: all 0.3s ease;
        }
        
        .form-floating .form-control:focus::placeholder {
            opacity: 0.5;
            transform: translateY(-2px);
        }
        
        .form-floating .form-control.is-valid {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        
        .form-floating .form-control.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
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
        
        .password-strength {
            margin-top: 0.5rem;
            display: none;
        }
        
        .password-strength.show {
            display: block;
        }
        
        .strength-bar {
            height: 4px;
            border-radius: 2px;
            background: var(--border-color);
            overflow: hidden;
        }
        
        .strength-fill {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }
        
        .strength-fill.weak { background: #dc3545; width: 25%; }
        .strength-fill.fair { background: #ffc107; width: 50%; }
        .strength-fill.good { background: #fd7e14; width: 75%; }
        .strength-fill.strong { background: #28a745; width: 100%; }
        
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
        
        .requirements {
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
            font-size: 0.9rem;
        }
        
        .requirement {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .requirement:last-child {
            margin-bottom: 0;
        }
        
        .requirement i {
            margin-right: 0.5rem;
            width: 16px;
        }
        
        .requirement.valid {
            color: #28a745;
        }
        
        .requirement.invalid {
            color: var(--text-muted);
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
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h1>Приєднуйтесь!</h1>
                    <p>Створіть новий обліковий запис</p>
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
                
                <form method="POST" action="" id="registerForm" novalidate>
                    <div class="form-floating">
                        <input type="text" class="form-control" id="username" name="username" 
                               placeholder="Ім'я користувача" required minlength="3"
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                        <label for="username">Ім'я користувача</label>
                        <i class="fas fa-user input-icon"></i>
                    </div>
                    
                    <div class="form-floating">
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="Email" required
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        <label for="email">Email</label>
                        <i class="fas fa-envelope input-icon"></i>
                    </div>
                    
                    <div class="form-floating">
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Пароль" required minlength="6">
                        <label for="password">Пароль</label>
                        <i class="fas fa-lock input-icon"></i>
                        <button type="button" class="password-toggle" onclick="togglePassword('password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    
                    <div class="password-strength" id="passwordStrength">
                        <div class="strength-bar">
                            <div class="strength-fill" id="strengthFill"></div>
                        </div>
                        <small class="text-muted mt-1" id="strengthText">Введіть пароль для перевірки</small>
                    </div>
                    
                    <div class="form-floating">
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                               placeholder="Підтвердіть пароль" required>
                        <label for="confirm_password">Підтвердіть пароль</label>
                        <i class="fas fa-lock input-icon"></i>
                        <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    
                    <div class="requirements">
                        <div class="requirement" id="req-length">
                            <i class="fas fa-times"></i>
                            Мінімум 6 символів
                        </div>
                        <div class="requirement" id="req-letter">
                            <i class="fas fa-times"></i>
                            Містить букви
                        </div>
                        <div class="requirement" id="req-number">
                            <i class="fas fa-times"></i>
                            Містить цифри
                        </div>
                        <div class="requirement" id="req-match">
                            <i class="fas fa-times"></i>
                            Паролі співпадають
                        </div>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agree_terms" name="agree_terms" required>
                        <label class="form-check-label" for="agree_terms">
                            Я погоджуюсь з 
                            <a href="../pages/terms.php" target="_blank" class="text-decoration-none">
                                умовами використання
                            </a> та 
                            <a href="../pages/privacy.php" target="_blank" class="text-decoration-none">
                                політикою конфіденційності
                            </a>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-auth" id="submitBtn" disabled>
                        <span class="btn-text">
                            <i class="fas fa-user-plus me-2"></i>Зареєструватися
                        </span>
                        <span class="loading" style="display: none;">
                            <i class="fas fa-spinner fa-spin"></i>
                        </span>
                    </button>
                </form>
                
                <div class="auth-links">
                    <p class="mb-0">Вже маєте акаунт? 
                        <a href="login.php" data-spa data-page="login">Увійти</a>
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
        
        // Валідація форми та перевірка паролів
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const usernameInput = document.getElementById('username');
        const emailInput = document.getElementById('email');
        const agreeTerms = document.getElementById('agree_terms');
        const submitBtn = document.getElementById('submitBtn');
        
        const requirements = {
            length: document.getElementById('req-length'),
            letter: document.getElementById('req-letter'),
            number: document.getElementById('req-number'),
            match: document.getElementById('req-match')
        };
        
        function checkRequirement(element, condition) {
            const icon = element.querySelector('i');
            if (condition) {
                element.classList.add('valid');
                element.classList.remove('invalid');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-check');
            } else {
                element.classList.remove('valid');
                element.classList.add('invalid');
                icon.classList.remove('fa-check');
                icon.classList.add('fa-times');
            }
        }
        
        function checkPasswordStrength(password) {
            const strengthBar = document.getElementById('strengthFill');
            const strengthText = document.getElementById('strengthText');
            
            if (password.length === 0) {
                strengthBar.className = 'strength-fill';
                strengthText.textContent = 'Введіть пароль для перевірки';
                return;
            }
            
            let score = 0;
            if (password.length >= 6) score++;
            if (/[a-zA-Z]/.test(password)) score++;
            if (/\d/.test(password)) score++;
            if (/[^a-zA-Z\d]/.test(password)) score++;
            
            const levels = ['weak', 'fair', 'good', 'strong'];
            const texts = ['Слабкий', 'Задовільний', 'Хороший', 'Сильний'];
            
            strengthBar.className = `strength-fill ${levels[Math.min(score, 3)]}`;
            strengthText.textContent = texts[Math.min(score, 3)];
        }
        
        function validateForm() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            const username = usernameInput.value;
            const email = emailInput.value;
            
            // Перевірка вимог
            const hasLength = password.length >= 6;
            const hasLetter = /[a-zA-Z]/.test(password);
            const hasNumber = /\d/.test(password);
            const passwordsMatch = password === confirmPassword && password.length > 0;
            
            checkRequirement(requirements.length, hasLength);
            checkRequirement(requirements.letter, hasLetter);
            checkRequirement(requirements.number, hasNumber);
            checkRequirement(requirements.match, passwordsMatch);
            
            // Перевірка полів
            const isValid = hasLength && hasLetter && hasNumber && passwordsMatch && 
                           username.length >= 3 && email.includes('@') && agreeTerms.checked;
            
            submitBtn.disabled = !isValid;
            
            return isValid;
        }
        
        // Події для валідації
        passwordInput.addEventListener('input', function() {
            const strengthContainer = document.getElementById('passwordStrength');
            strengthContainer.classList.add('show');
            checkPasswordStrength(this.value);
            validateForm();
        });
        
        confirmPasswordInput.addEventListener('input', validateForm);
        usernameInput.addEventListener('input', validateForm);
        emailInput.addEventListener('input', validateForm);
        agreeTerms.addEventListener('change', validateForm);
        
        // Валідація email
        emailInput.addEventListener('blur', function() {
            if (this.value && !this.validity.valid) {
                this.classList.add('is-invalid');
            } else if (this.value) {
                this.classList.add('is-valid');
                this.classList.remove('is-invalid');
            }
        });
        
        // Валідація username
        usernameInput.addEventListener('blur', function() {
            if (this.value.length < 3) {
                this.classList.add('is-invalid');
            } else {
                this.classList.add('is-valid');
                this.classList.remove('is-invalid');
            }
        });
        
        // Анімація кнопки при відправці
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                return;
            }
            
            const btn = submitBtn;
            const btnText = btn.querySelector('.btn-text');
            const loading = btn.querySelector('.loading');
            
            btnText.style.display = 'none';
            loading.style.display = 'inline-block';
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
            const particles = 40;
            const body = document.body;
            
            for (let i = 0; i < particles; i++) {
                const particle = document.createElement('div');
                particle.style.cssText = `
                    position: fixed;
                    width: 6px;
                    height: 6px;
                    background: rgba(255,255,255,0.4);
                    border-radius: 50%;
                    pointer-events: none;
                    z-index: 1;
                    animation: float ${Math.random() * 4 + 3}s ease-in-out infinite;
                    top: ${Math.random() * 100}vh;
                    left: ${Math.random() * 100}vw;
                    animation-delay: ${Math.random() * 3}s;
                `;
                body.appendChild(particle);
            }
        }
        
        // Запускаємо частки
        createParticles();
    </script>
</body>
</html>