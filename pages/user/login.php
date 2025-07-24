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
            $user = loginUser($email, $password, 'user', $remember);
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
            if (sendPasswordReset($email, 'user')) {
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
                <div class="modern-auth-card user-login-card">
                    <!-- Header -->
                    <div class="auth-header">
                        <div class="header-icon user-login-icon">
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <h1>Вхід користувача</h1>
                        <p>Увійдіть до свого акаунту</p>
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
                        
                        <button type="submit" name="login" class="modern-submit-btn user-login-btn">
                            <span class="btn-text">Увійти</span>
                            <div class="btn-icon">
                                <i class="fas fa-sign-in-alt"></i>
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
                        <p>Ще не маєте акаунту?</p>
                        <a href="register.php" class="register-link">
                            <i class="fas fa-user-plus"></i>
                            <span>Зареєструватись</span>
                        </a>
                    </div>
                    
                    <!-- Partner Link -->
                    <div class="partner-suggestion">
                        <p>Бізнес акаунт?</p>
                        <a href="../partner/login.php" class="partner-link">
                            <i class="fas fa-briefcase"></i>
                            <span>Вхід для партнерів</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
    document.querySelector('.auth-header h1').textContent = 'Вхід користувача';
    document.querySelector('.auth-header p').textContent = 'Увійдіть до свого акаунту';
}

function googleLogin() {
    alert('Google вхід буде доступний незабаром!');
}
</script>

<style>
/* Modern Auth Container */
.modern-auth-container {
    min-height: 100vh;
    background: var(--theme-bg);
    position: relative;
    overflow: hidden;
}

.animated-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: 1;
}

.floating-shapes {
    position: absolute;
    width: 100%;
    height: 100%;
}

.shape {
    position: absolute;
    background: var(--current-gradient);
    border-radius: 50%;
    opacity: 0.1;
    animation: float 20s infinite ease-in-out;
}

.shape-1 { width: 80px; height: 80px; top: 10%; left: 10%; animation-delay: 0s; }
.shape-2 { width: 120px; height: 120px; top: 20%; right: 15%; animation-delay: 5s; }
.shape-3 { width: 60px; height: 60px; bottom: 30%; left: 20%; animation-delay: 10s; }
.shape-4 { width: 100px; height: 100px; bottom: 10%; right: 10%; animation-delay: 15s; }
.shape-5 { width: 140px; height: 140px; top: 50%; left: 50%; animation-delay: 7s; }

.gradient-orbs {
    position: absolute;
    width: 100%;
    height: 100%;
}

.orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(60px);
    opacity: 0.3;
    animation: orbFloat 15s infinite ease-in-out;
}

.orb-1 {
    width: 200px;
    height: 200px;
    background: linear-gradient(45deg, #ff6b6b, #4ecdc4);
    top: 20%;
    left: 10%;
}

.orb-2 {
    width: 300px;
    height: 300px;
    background: linear-gradient(45deg, #a8e6cf, #ffd93d);
    bottom: 20%;
    right: 15%;
    animation-delay: 5s;
}

.orb-3 {
    width: 150px;
    height: 150px;
    background: linear-gradient(45deg, #ff8a80, #8c9eff);
    top: 60%;
    left: 60%;
    animation-delay: 10s;
}

.modern-auth-card {
    background: var(--theme-bg);
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    border: 1px solid var(--theme-border);
    backdrop-filter: blur(20px);
    position: relative;
    z-index: 2;
    padding: 40px;
    margin: 20px 0;
}

.auth-header {
    text-align: center;
    margin-bottom: 30px;
}

.header-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--current-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 2rem;
    color: white;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

.auth-header h1 {
    color: var(--theme-text);
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 10px;
}

.auth-header p {
    color: var(--theme-muted);
    font-size: 1.1rem;
}

.modern-alert {
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-weight: 500;
}

.error-alert {
    background: rgba(244, 67, 54, 0.1);
    border: 1px solid rgba(244, 67, 54, 0.3);
    color: #d32f2f;
}

.success-alert {
    background: rgba(76, 175, 80, 0.1);
    border: 1px solid rgba(76, 175, 80, 0.3);
    color: #388e3c;
}

.social-login-section {
    margin-bottom: 30px;
}

.social-btn {
    width: 100%;
    padding: 15px;
    border: 2px solid var(--theme-border);
    border-radius: 12px;
    background: var(--theme-bg-secondary);
    color: var(--theme-text);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
    cursor: pointer;
}

.social-btn:hover {
    border-color: #db4437;
    background: rgba(219, 68, 55, 0.1);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.divider {
    text-align: center;
    margin: 25px 0;
    position: relative;
}

.divider::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: var(--theme-border);
}

.divider span {
    background: var(--theme-bg);
    padding: 0 20px;
    color: var(--theme-muted);
    font-size: 0.9rem;
    position: relative;
}

.modern-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-group {
    position: relative;
}

.input-wrapper {
    position: relative;
}

.input-wrapper input {
    width: 100%;
    padding: 15px 50px 15px 20px;
    border: 2px solid var(--theme-border);
    border-radius: 12px;
    background: var(--theme-bg-secondary);
    color: var(--theme-text);
    font-size: 1rem;
    transition: all 0.3s ease;
}

.input-wrapper input:focus {
    outline: none;
    border-color: var(--theme-accent);
    background: var(--theme-bg);
    box-shadow: 0 0 0 3px rgba(88, 166, 255, 0.1);
}

.input-icon {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--theme-muted);
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.input-wrapper input:focus + .input-icon {
    color: var(--theme-accent);
}

.input-line {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background: var(--current-gradient);
    transition: all 0.3s ease;
}

.input-wrapper input:focus ~ .input-line {
    width: 100%;
}

.password-toggle {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--theme-muted);
    cursor: pointer;
    font-size: 1.1rem;
    transition: all 0.3s ease;
}

.password-toggle:hover {
    color: var(--theme-accent);
}

.login-options {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 20px 0;
}

.modern-checkbox {
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
    position: relative;
}

.modern-checkbox input {
    display: none;
}

.checkmark {
    width: 20px;
    height: 20px;
    border: 2px solid var(--theme-border);
    border-radius: 4px;
    background: var(--theme-bg-secondary);
    position: relative;
    transition: all 0.3s ease;
}

.modern-checkbox input:checked + .checkmark {
    background: var(--current-gradient);
    border-color: transparent;
}

.modern-checkbox input:checked + .checkmark::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 12px;
    font-weight: bold;
}

.checkbox-text {
    color: var(--theme-text);
    font-size: 0.95rem;
    line-height: 1.4;
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

.modern-submit-btn {
    background: var(--current-gradient);
    border: none;
    border-radius: 12px;
    padding: 18px 30px;
    color: white;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-top: 10px;
}

.modern-submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.btn-ripple {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    transform: scale(0);
    animation: ripple 0.6s linear;
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

.auth-footer-links {
    text-align: center;
    margin-top: 30px;
    padding-top: 25px;
    border-top: 1px solid var(--theme-border);
}

.auth-footer-links p {
    color: var(--theme-muted);
    margin-bottom: 15px;
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

.partner-suggestion {
    text-align: center;
    margin-top: 20px;
    padding: 20px;
    background: var(--theme-bg-secondary);
    border-radius: 12px;
    border: 1px solid var(--theme-border);
}

.partner-suggestion p {
    color: var(--theme-muted);
    margin-bottom: 12px;
    font-size: 0.95rem;
}

.partner-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: linear-gradient(135deg, #ff9800, #f57c00);
    border-radius: 8px;
    color: white;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.partner-link:hover {
    background: linear-gradient(135deg, #f57c00, #e65100);
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(245, 124, 0, 0.3);
}

/* User Login specific styles */
.user-login-icon {
    background: var(--current-gradient) !important;
}

.user-login-card .modern-submit-btn {
    background: var(--current-gradient) !important;
}

.user-login-card .modern-submit-btn:hover {
    background: var(--current-gradient) !important;
    opacity: 0.9;
}

.user-login-card .input-wrapper input:focus + .input-icon {
    color: var(--current-gradient);
}

.user-login-card .input-line {
    background: var(--current-gradient);
}

/* Animations */
@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    33% { transform: translateY(-20px) rotate(120deg); }
    66% { transform: translateY(10px) rotate(240deg); }
}

@keyframes orbFloat {
    0%, 100% { transform: translate(0, 0) scale(1); }
    33% { transform: translate(30px, -30px) scale(1.1); }
    66% { transform: translate(-20px, 20px) scale(0.9); }
}

@keyframes ripple {
    to { transform: scale(4); opacity: 0; }
}

/* Responsive */
@media (max-width: 576px) {
    .login-options {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
    
    .reset-buttons {
        flex-direction: column;
    }
    
    .modern-auth-card {
        padding: 25px;
        margin: 10px;
    }
    
    .header-icon {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
    
    .auth-header h1 {
        font-size: 1.6rem;
    }
}
</style>

<?php include '../../themes/footer.php'; ?>