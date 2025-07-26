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

<div class="auth-container partner-auth-container">
    <div class="auth-background">
        <div class="auth-particles"></div>
        <div class="auth-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
        </div>
    </div>
    
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-lg-10 col-xl-8">
                <div class="auth-card partner-auth">
                    <div class="row g-0">
                        <!-- Ліва частина з інформацією -->
                        <div class="col-md-6 auth-info">
                            <div class="auth-info-content">
                                <div class="auth-brand">
                                    <i class="fas fa-chart-line"></i>
                                    <h2>AdBoard Pro</h2>
                                    <p>Для рекламодавців</p>
                                </div>
                                
                                <div class="auth-features">
                                    <div class="feature-item">
                                        <i class="fas fa-bullseye"></i>
                                        <div>
                                            <h5>Таргетована реклама</h5>
                                            <p>Досягайте саме тих клієнтів, які вам потрібні</p>
                                        </div>
                                    </div>
                                    
                                    <div class="feature-item">
                                        <i class="fas fa-analytics"></i>
                                        <div>
                                            <h5>Детальна аналітика</h5>
                                            <p>Відстежуйте ROI та ефективність кампаній</p>
                                        </div>
                                    </div>
                                    
                                    <div class="feature-item">
                                        <i class="fas fa-crown"></i>
                                        <div>
                                            <h5>Преміум розміщення</h5>
                                            <p>Ваша реклама на найкращих позиціях</p>
                                        </div>
                                    </div>
                                    
                                    <div class="feature-item">
                                        <i class="fas fa-headset"></i>
                                        <div>
                                            <h5>Персональний менеджер</h5>
                                            <p>Професійна підтримка 24/7</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="auth-footer">
                                    <p>Звичайний користувач? <a href="../user/login.php" class="text-light">Увійти як користувач</a></p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Права частина з формою -->
                        <div class="col-md-6 auth-form-section">
                            <div class="auth-form-container">
                                <div class="auth-header">
                                    <h3>Вхід для партнерів</h3>
                                    <p>Просувайте ваш бізнес ефективно</p>
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
                                                   placeholder="your@company.com"
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
                                    
                                    <button type="submit" name="login" class="btn btn-partner btn-lg w-100 mb-3">
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
                                                   placeholder="your@company.com"
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
                                
                                <div class="partner-benefits">
                                    <h6><i class="fas fa-gift me-2"></i>Бонуси для нових партнерів:</h6>
                                    <ul>
                                        <li>💰 Перша кампанія зі знижкою 30%</li>
                                        <li>📊 Безкоштовна консультація</li>
                                        <li>🎯 Налаштування таргетингу</li>
                                    </ul>
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
.partner-auth-container {
    background: linear-gradient(135deg, #ff9800 0%, #f57c00 25%, #e65100 50%, #bf360c 75%, #d84315 100%);
    position: relative;
    min-height: 100vh;
    overflow: hidden;
}

.partner-auth-container .auth-background {
    background: linear-gradient(45deg, rgba(255, 193, 7, 0.1) 0%, rgba(255, 152, 0, 0.1) 100%);
}

.partner-auth .auth-info {
    background: linear-gradient(135deg, #ff6f00 0%, #e65100 50%, #bf360c 100%);
    color: white;
    position: relative;
}

.partner-auth .auth-info::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="25" cy="25" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="50" r="1.5" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><polygon points="80,20 85,10 90,20 85,30" fill="rgba(255,255,255,0.05)"/></svg>');
    pointer-events: none;
}

.partner-auth .auth-brand i {
    background: linear-gradient(45deg, #ffc107, #ff9800);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.3));
}

.partner-auth .feature-item i {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    margin-top: 0;
    min-width: 35px;
}

.btn-partner {
    background: linear-gradient(135deg, #ff9800 0%, #f57c00 50%, #e65100 100%);
    border: none;
    color: white;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(255, 152, 0, 0.3);
}

.btn-partner:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 152, 0, 0.4);
    color: white;
}

.partner-auth .forgot-password {
    color: #ff9800;
}

.partner-auth .forgot-password:hover {
    color: #f57c00;
}

.partner-auth .auth-register a {
    color: #ff9800;
    font-weight: 600;
}

.partner-auth .auth-register a:hover {
    color: #f57c00;
}

.partner-auth .input-group-text {
    background: linear-gradient(135deg, rgba(255, 152, 0, 0.1) 0%, rgba(245, 124, 0, 0.1) 100%);
    border-color: rgba(255, 152, 0, 0.3);
    color: #e65100;
}

.partner-auth .form-control:focus {
    border-color: #ff9800;
    box-shadow: 0 0 0 0.2rem rgba(255, 152, 0, 0.25);
}

.partner-benefits {
    background: linear-gradient(135deg, rgba(255, 152, 0, 0.1) 0%, rgba(245, 124, 0, 0.1) 100%);
    border-radius: 10px;
    padding: 1rem;
    margin-top: 1.5rem;
    border: 1px solid rgba(255, 152, 0, 0.2);
}

.partner-benefits h6 {
    color: #e65100;
    font-weight: 600;
    margin-bottom: 0.75rem;
}

.partner-benefits ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.partner-benefits li {
    padding: 0.25rem 0;
    font-size: 0.9rem;
    color: #5d4037;
}

.partner-auth-container .shape {
    background: rgba(255, 193, 7, 0.1);
}

.partner-auth-container .shape-1 {
    background: linear-gradient(45deg, rgba(255, 152, 0, 0.1), rgba(245, 124, 0, 0.1));
}

.partner-auth-container .shape-2 {
    background: linear-gradient(45deg, rgba(230, 81, 0, 0.1), rgba(191, 54, 12, 0.1));
}

.partner-auth-container .shape-3 {
    background: linear-gradient(45deg, rgba(255, 193, 7, 0.1), rgba(255, 152, 0, 0.1));
}

/* Анімації для партнерської версії */
@keyframes goldFloat {
    0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.7; }
    33% { transform: translateY(-20px) rotate(90deg); opacity: 1; }
    66% { transform: translateY(-40px) rotate(180deg); opacity: 0.8; }
}

.partner-auth-container .shape {
    animation: goldFloat 12s ease-in-out infinite;
}

.partner-auth-container .shape-1 {
    animation-delay: -2s;
}

.partner-auth-container .shape-2 {
    animation-delay: -6s;
}

.partner-auth-container .shape-3 {
    animation-delay: -10s;
}

/* Додаткові елементи для партнерів */
.partner-auth-container .auth-particles {
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="1" fill="rgba(255,193,7,0.3)"/><circle cx="80" cy="60" r="0.5" fill="rgba(255,152,0,0.4)"/><circle cx="40" cy="80" r="0.8" fill="rgba(245,124,0,0.3)"/></svg>') repeat;
    animation: goldFloat 25s linear infinite;
}

@media (max-width: 768px) {
    .partner-benefits {
        margin-top: 1rem;
        padding: 0.75rem;
    }
    
    .partner-benefits li {
        font-size: 0.8rem;
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
    document.querySelector('.partner-benefits').style.display = 'none';
}

function showLoginForm() {
    document.getElementById('loginForm').style.display = 'block';
    document.getElementById('resetForm').style.display = 'none';
    document.querySelector('.btn-google').style.display = 'block';
    document.querySelector('.social-divider').style.display = 'block';
    document.querySelector('.partner-benefits').style.display = 'block';
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
            user_type: 'partner'
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
</script>

<?php include '../../themes/footer.php'; ?>