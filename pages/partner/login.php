<?php
require_once '../../core/config.php';
require_once '../../core/functions.php';

$error = '';
$success = '';

// Обробка форми входу
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']);
        
        if (empty($email) || empty($password)) {
            $error = 'Заповніть всі поля';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Невірний формат email';
        } else {
            $user = loginUser($email, $password, 'partner', $remember);
            if ($user) {
                header('Location: dashboard.php');
                exit();
            } else {
                $error = 'Невірний email або пароль';
            }
        }
    }
    
    // Обробка відновлення паролю
    if (isset($_POST['reset_password'])) {
        $email = sanitize($_POST['reset_email'] ?? '');
        
        if (empty($email)) {
            $error = 'Введіть email для відновлення паролю';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Невірний формат email';
        } else {
            if (sendPasswordReset($email, 'partner')) {
                $success = 'Інструкції для відновлення паролю надіслані на ваш email';
            } else {
                $error = 'Користувач з таким email не знайдений';
            }
        }
    }
}

include '../../themes/header.php';
?>

<div class="modern-auth-container user-login-container">
    <!-- Animated Background -->
    <div class="animated-background">
        <div class="floating-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
            <div class="shape shape-4"></div>
            <div class="shape shape-5"></div>
        </div>
        <div class="gradient-orbs">
            <div class="orb orb-1"></div>
            <div class="orb orb-2"></div>
            <div class="orb orb-3"></div>
        </div>
    </div>
    
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 py-5">
            <div class="col-12 col-md-10 col-lg-8 col-xl-5">
                <div class="modern-auth-card partner-login-card">
                    <!-- Header -->
                    <div class="auth-header">
                        <div class="header-icon partner-login-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <h1>Партнерський вхід</h1>
                        <p>Увійдіть до партнерського акаунту</p>
                    </div>
                    
                    <!-- Alerts -->
                    <?php if ($error): ?>
                        <div class="modern-alert error-alert">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span><?php echo $error; ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="modern-alert success-alert">
                            <i class="fas fa-check-circle"></i>
                            <span><?php echo $success; ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Social Login -->
                    <div class="social-login-section">
                        <button type="button" class="social-btn google-btn" onclick="googleLogin()">
                            <i class="fab fa-google"></i>
                            <span>Увійти через Google</span>
                        </button>
                        
                        <div class="divider">
                            <span>або</span>
                        </div>
                    </div>
                    
                    <!-- Login Form -->
                    <form method="POST" id="modernLoginForm" class="modern-form" novalidate>
                        <div class="form-group">
                            <div class="input-wrapper">
                                <input type="email" 
                                       id="email" 
                                       name="email" 
                                       placeholder="Email адреса *"
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                       required>
                                <div class="input-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="input-line"></div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="input-wrapper">
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       placeholder="Пароль *"
                                       required>
                                <div class="input-icon">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <div class="input-line"></div>
                                <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="login-options">
                            <label class="modern-checkbox">
                                <input type="checkbox" name="remember">
                                <span class="checkmark"></span>
                                <span class="checkbox-text">Запам'ятати мене</span>
                            </label>
                            
                            <a href="#" class="forgot-password-link" onclick="showPasswordReset()">
                                Забули пароль?
                            </a>
                        </div>
                        
                        <button type="submit" name="login" class="modern-submit-btn partner-login-btn">
                            <span class="btn-text">Увійти як партнер</span>
                            <div class="btn-icon">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <div class="btn-ripple"></div>
                        </button>
                    </form>
                    
                    <!-- Password Reset Form (Hidden) -->
                    <form method="POST" id="passwordResetForm" class="modern-form" style="display: none;" novalidate>
                        <div class="form-group">
                            <div class="input-wrapper">
                                <input type="email" 
                                       id="reset_email" 
                                       name="reset_email" 
                                       placeholder="Email для відновлення *"
                                       required>
                                <div class="input-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="input-line"></div>
                            </div>
                        </div>
                        
                        <div class="reset-buttons">
                            <button type="submit" name="reset_password" class="modern-submit-btn reset-btn">
                                <span class="btn-text">Відновити</span>
                                <div class="btn-icon">
                                    <i class="fas fa-key"></i>
                                </div>
                            </button>
                            
                            <button type="button" class="cancel-btn" onclick="hidePasswordReset()">
                                Скасувати
                            </button>
                        </div>
                    </form>
                    
                    <!-- Footer Links -->
                    <div class="auth-footer-links">
                        <p>Ще не маєте партнерського акаунту?</p>
                        <a href="register.php" class="register-link partner-register">
                            <i class="fas fa-handshake"></i>
                            <span>Зареєструватись як партнер</span>
                        </a>
                    </div>
                    
                    <!-- User Link -->
                    <div class="user-suggestion">
                        <p>Звичайний користувач?</p>
                        <a href="../user/login.php" class="user-link">
                            <i class="fas fa-user"></i>
                            <span>Вхід для користувачів</span>
                        </a>
                    </div>
                                
                                <div class="auth-features">
                                    <div class="feature-item">
                                        <i class="fas fa-plus-circle"></i>
                                        <div>
                                            <h5>Додавайте оголошення</h5>
                                            <p>Розміщуйте свої товари та послуги безкоштовно</p>
                                        </div>
                                    </div>
                                    
                                    <div class="feature-item">
                                        <i class="fas fa-chart-line"></i>
                                        <div>
                                            <h5>Статистика переглядів</h5>
                                            <p>Відстежуйте популярність ваших оголошень</p>
                                        </div>
                                    </div>
                                    
                                    <div class="feature-item">
                                        <i class="fas fa-shield-alt"></i>
                                        <div>
                                            <h5>Безпека</h5>
                                            <p>Захищені угоди та перевірка користувачів</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="auth-footer">
                                    <p>Партнер? <a href="../partner/login.php" class="text-warning">Увійти як рекламодавець</a></p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Права частина з формою -->
                        <div class="col-md-6 auth-form-section">
                            <div class="auth-form-container">
                                <div class="auth-header">
                                    <h3>Вхід для користувачів</h3>
                                    <p>Розміщуйте оголошення та знаходьте покупців</p>
                                </div>
                                
                                <?php if ($error): ?>
                                    <div class="alert alert-danger animate__animated animate__shakeX">
                                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($success): ?>
                                    <div class="alert alert-success animate__animated animate__bounceIn">
                                        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Форма входу -->
                                <form method="POST" id="loginForm" class="auth-form">
                                    <div class="form-group mb-4">
                                        <label for="email" class="form-label">Email адреса</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                            <input type="email" 
                                                   class="form-control" 
                                                   id="email" 
                                                   name="email" 
                                                   placeholder="your@email.com"
                                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                                   required>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group mb-4">
                                        <label for="password" class="form-label">Пароль</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-lock"></i>
                                            </span>
                                            <input type="password" 
                                                   class="form-control" 
                                                   id="password" 
                                                   name="password" 
                                                   placeholder="Введіть пароль"
                                                   required>
                                            <button type="button" class="btn btn-outline-secondary toggle-password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="form-check-remember mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                            <label class="form-check-label" for="remember">
                                                Запам'ятати мене
                                            </label>
                                        </div>
                                        <a href="#" class="forgot-password" onclick="showResetForm()">Забули пароль?</a>
                                    </div>
                                    
                                    <button type="submit" name="login" class="btn btn-primary btn-lg w-100 mb-3">
                                        <i class="fas fa-sign-in-alt me-2"></i>Увійти
                                    </button>
                                </form>
                                
                                <!-- Google вхід -->
                                <div class="social-divider">
                                    <span>або</span>
                                </div>
                                
                                <button type="button" class="btn btn-google w-100 mb-4" onclick="googleLogin()">
                                    <i class="fab fa-google me-2"></i>Увійти через Google
                                </button>
                                
                                <!-- Форма відновлення паролю (приховано) -->
                                <form method="POST" id="resetForm" class="auth-form" style="display: none;">
                                    <div class="reset-header mb-4">
                                        <h4>Відновлення паролю</h4>
                                        <p>Введіть ваш email для отримання інструкцій</p>
                                    </div>
                                    
                                    <div class="form-group mb-4">
                                        <label for="reset_email" class="form-label">Email адреса</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-envelope"></i>
                                            </span>
                                            <input type="email" 
                                                   class="form-control" 
                                                   id="reset_email" 
                                                   name="reset_email" 
                                                   placeholder="your@email.com"
                                                   required>
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <button type="submit" name="reset_password" class="btn btn-warning btn-lg">
                                            <i class="fas fa-paper-plane me-2"></i>Надіслати інструкції
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" onclick="showLoginForm()">
                                            <i class="fas fa-arrow-left me-2"></i>Назад до входу
                                        </button>
                                    </div>
                                </form>
                                
                                <div class="auth-register">
                                    <p>Ще не маєте акаунту? <a href="register.php">Зареєструватися</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.auth-container {
    position: relative;
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    overflow: hidden;
}

.auth-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
}

.auth-particles {
    position: absolute;
    width: 100%;
    height: 100%;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
    animation: float 20s ease-in-out infinite;
}

.auth-shapes {
    position: absolute;
    width: 100%;
    height: 100%;
}

.shape {
    position: absolute;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    animation: float 15s ease-in-out infinite;
}

.shape-1 {
    width: 300px;
    height: 300px;
    top: -150px;
    right: -150px;
    animation-delay: -2s;
}

.shape-2 {
    width: 200px;
    height: 200px;
    bottom: -100px;
    left: -100px;
    animation-delay: -8s;
}

.shape-3 {
    width: 150px;
    height: 150px;
    top: 50%;
    left: -75px;
    animation-delay: -15s;
}

.auth-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.user-auth .auth-info {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    position: relative;
}

.auth-info::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="80" cy="60" r="1.5" fill="rgba(255,255,255,0.1)"/><circle cx="40" cy="80" r="1" fill="rgba(255,255,255,0.1)"/></svg>');
    pointer-events: none;
}

.auth-info-content {
    padding: 3rem 2rem;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    position: relative;
    z-index: 1;
}

.auth-brand {
    text-align: center;
    margin-bottom: 3rem;
}

.auth-brand i {
    font-size: 3rem;
    margin-bottom: 1rem;
    display: block;
    opacity: 0.9;
}

.auth-brand h2 {
    font-size: 1.8rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.auth-brand p {
    opacity: 0.8;
    margin-bottom: 0;
}

.auth-features {
    margin-bottom: 2rem;
}

.feature-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1.5rem;
}

.feature-item i {
    font-size: 1.2rem;
    margin-right: 1rem;
    margin-top: 0.3rem;
    opacity: 0.9;
    min-width: 20px;
}

.feature-item h5 {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.feature-item p {
    font-size: 0.85rem;
    opacity: 0.8;
    margin-bottom: 0;
}

.auth-footer {
    text-align: center;
    margin-top: auto;
}

.auth-footer a {
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    font-weight: 500;
}

.auth-footer a:hover {
    color: white;
    text-decoration: underline;
}

.auth-form-section {
    background: white;
}

.auth-form-container {
    padding: 3rem 2rem;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.auth-header {
    text-align: center;
    margin-bottom: 2rem;
}

.auth-header h3 {
    color: #495057;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.auth-header p {
    color: #6c757d;
    margin-bottom: 0;
}

.form-group {
    position: relative;
}

.form-label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 0.5rem;
}

.input-group-text {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    color: #6c757d;
}

.form-control {
    border: 1px solid #dee2e6;
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.toggle-password {
    border-left: none;
}

.form-check-remember {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.forgot-password {
    color: #667eea;
    text-decoration: none;
    font-size: 0.9rem;
}

.forgot-password:hover {
    text-decoration: underline;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    font-weight: 500;
    padding: 0.75rem 1.5rem;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.social-divider {
    text-align: center;
    position: relative;
    margin: 1.5rem 0;
}

.social-divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: #dee2e6;
}

.social-divider span {
    background: white;
    padding: 0 1rem;
    color: #6c757d;
    font-size: 0.9rem;
}

.btn-google {
    background: #db4437;
    color: white;
    border: none;
    font-weight: 500;
    padding: 0.75rem 1.5rem;
    transition: all 0.3s ease;
}

.btn-google:hover {
    background: #c23321;
    transform: translateY(-1px);
    color: white;
}

.reset-header h4 {
    color: #495057;
    font-weight: 600;
}

.reset-header p {
    color: #6c757d;
    margin-bottom: 0;
}

.auth-register {
    text-align: center;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #dee2e6;
}

.auth-register a {
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
}

.auth-register a:hover {
    text-decoration: underline;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    33% { transform: translateY(-30px) rotate(120deg); }
    66% { transform: translateY(-60px) rotate(240deg); }
}

@media (max-width: 768px) {
    .auth-card {
        margin: 1rem;
    }
    
    .auth-info {
        display: none;
    }
    
    .auth-form-container {
        padding: 2rem 1.5rem;
    }
}
</style>

<script src="https://accounts.google.com/gsi/client" async defer></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Переключення видимості паролю
    document.querySelector('.toggle-password').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = this.querySelector('i');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
    
    // Валідація форми
    const loginForm = document.getElementById('loginForm');
    loginForm.addEventListener('submit', function(e) {
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        
        if (!email || !password) {
            e.preventDefault();
            showAlert('Заповніть всі поля', 'error');
            return;
        }
        
        if (!isValidEmail(email)) {
            e.preventDefault();
            showAlert('Введіть коректний email', 'error');
            return;
        }
    });
});

function showResetForm() {
    document.getElementById('loginForm').style.display = 'none';
    document.getElementById('resetForm').style.display = 'block';
    document.querySelector('.btn-google').style.display = 'none';
    document.querySelector('.social-divider').style.display = 'none';
}

function showLoginForm() {
    document.getElementById('loginForm').style.display = 'block';
    document.getElementById('resetForm').style.display = 'none';
    document.querySelector('.btn-google').style.display = 'block';
    document.querySelector('.social-divider').style.display = 'block';
}

function googleLogin() {
    // Ініціалізація Google Sign-In
    google.accounts.id.initialize({
        client_id: 'YOUR_GOOGLE_CLIENT_ID',
        callback: handleGoogleSignIn
    });
    
    google.accounts.id.prompt();
}

function handleGoogleSignIn(response) {
    // Відправка даних на сервер для обробки
    fetch('../../ajax/google_auth.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            credential: response.credential,
            user_type: 'user'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = 'dashboard.php';
        } else {
            showAlert(data.message, 'error');
        }
    })
    .catch(error => {
        showAlert('Помилка авторизації через Google', 'error');
    });
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function showAlert(message, type) {
    const alertClass = type === 'error' ? 'alert-danger' : 'alert-success';
    const icon = type === 'error' ? 'fa-exclamation-circle' : 'fa-check-circle';
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert ${alertClass} animate__animated animate__${type === 'error' ? 'shakeX' : 'bounceIn'}`;
    alertDiv.innerHTML = `<i class="fas ${icon} me-2"></i>${message}`;
    
    const container = document.querySelector('.auth-form-container');
    const firstChild = container.firstElementChild;
    container.insertBefore(alertDiv, firstChild.nextSibling);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// Login specific functions
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const toggle = input.parentElement.querySelector('.password-toggle i');
    
    if (input.type === 'password') {
        input.type = 'text';
        toggle.classList.remove('fa-eye');
        toggle.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        toggle.classList.remove('fa-eye-slash');
        toggle.classList.add('fa-eye');
    }
}

function showPasswordReset() {
    document.getElementById('modernLoginForm').style.display = 'none';
    document.getElementById('passwordResetForm').style.display = 'block';
    document.querySelector('.auth-header h1').textContent = 'Відновлення паролю';
    document.querySelector('.auth-header p').textContent = 'Введіть email для відновлення';
}

function hidePasswordReset() {
    document.getElementById('modernLoginForm').style.display = 'block';
    document.getElementById('passwordResetForm').style.display = 'none';
    document.querySelector('.auth-header h1').textContent = 'Вхід';
    document.querySelector('.auth-header p').textContent = 'Увійдіть до свого акаунту';
}

function googleLogin() {
    alert('Google вхід буде доступний незабаром!');
}
</script>

<style>
/* Partner Login specific styles */
.partner-login-container {
    background: var(--theme-bg);
}

.partner-login-icon {
    background: linear-gradient(135deg, #ff9800, #f57c00) !important;
}

.partner-login-card .modern-submit-btn {
    background: linear-gradient(135deg, #ff9800, #f57c00) !important;
}

.partner-login-card .modern-submit-btn:hover {
    background: linear-gradient(135deg, #f57c00, #e65100) !important;
}

.partner-register {
    border-color: #ff9800 !important;
    background: rgba(255, 152, 0, 0.1) !important;
    color: #e65100 !important;
}

.partner-register:hover {
    border-color: #f57c00 !important;
    background: rgba(245, 124, 0, 0.2) !important;
    color: #f57c00 !important;
}

.login-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 20px 0;
}

.forgot-password-link {
    color: var(--theme-accent);
    text-decoration: none;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.forgot-password-link:hover {
    color: var(--theme-text);
    text-decoration: underline;
}

.reset-buttons {
    display: flex;
    gap: 15px;
    margin-top: 20px;
}

.reset-btn {
    flex: 1;
    background: linear-gradient(135deg, #ff9800, #f57c00) !important;
}

.cancel-btn {
    flex: 1;
    padding: 15px;
    border: 2px solid var(--theme-border);
    border-radius: 12px;
    background: var(--theme-bg-secondary);
    color: var(--theme-text);
    cursor: pointer;
    transition: all 0.3s ease;
}

.cancel-btn:hover {
    border-color: var(--theme-accent);
    background: var(--theme-bg);
}

.register-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: var(--theme-bg-secondary);
    border: 2px solid var(--theme-border);
    border-radius: 10px;
    color: var(--theme-text);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.register-link:hover {
    border-color: #4caf50;
    background: rgba(76, 175, 80, 0.1);
    color: #4caf50;
    text-decoration: none;
    transform: translateY(-2px);
}

@media (max-width: 576px) {
    .login-options {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
    
    .reset-buttons {
        flex-direction: column;
    }
}
</style>

<?php include '../../themes/footer.php'; ?>