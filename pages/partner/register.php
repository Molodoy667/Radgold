<?php
require_once '../../core/config.php';
require_once '../../core/functions.php';

$error = '';
$success = '';

// Обробка форми реєстрації
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register'])) {
        $firstName = sanitize($_POST['first_name'] ?? '');
        $lastName = sanitize($_POST['last_name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $company = sanitize($_POST['company'] ?? '');
        $website = sanitize($_POST['website'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $agreeTerms = isset($_POST['agree_terms']);
        $agreeNewsletter = isset($_POST['agree_newsletter']);
        
        // Валідація
        if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($company) || empty($password)) {
            $error = 'Заповніть всі обов\'язкові поля';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Невірний формат email';
        } elseif (strlen($password) < 6) {
            $error = 'Пароль повинен містити мінімум 6 символів';
        } elseif ($password !== $confirmPassword) {
            $error = 'Паролі не співпадають';
        } elseif (!$agreeTerms) {
            $error = 'Необхідно прийняти умови використання';
        } elseif (userExists($email)) {
            $error = 'Користувач з таким email вже існує';
        } else {
            $userData = [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone' => $phone,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role' => 'partner',
                'user_type' => 'partner',
                'status' => 'active',
                'newsletter' => $agreeNewsletter,
                'company' => $company,
                'website' => $website,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            if (registerUser($userData)) {
                $partnerId = getLastInsertId();
                
                $success = 'Реєстрація успішна! Перенаправляємо в особистий кабінет...';
                // Відправка вітального email
                sendWelcomeEmail($email, $firstName, 'partner');
                // Повідомлення адмінам
                notifyAdminsNewPartner($email, $company);
                
                // Автоматичний вхід та перенаправлення
                session_start();
                $_SESSION['user_id'] = $partnerId;
                $_SESSION['user_type'] = 'partner';
                $_SESSION['user_role'] = 'partner';
                
                // Перенаправлення через JavaScript
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'dashboard.php';
                    }, 2000);
                </script>";
            } else {
                $error = 'Помилка при реєстрації. Спробуйте ще раз';
            }
        }
    }
}

include '../../themes/header.php';
?>

<div class="modern-auth-container partner-register-container">
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
            <div class="col-12 col-md-10 col-lg-8 col-xl-6">
                <div class="modern-auth-card partner-register-card">
                    <!-- Header -->
                    <div class="auth-header">
                        <div class="header-icon partner-register-icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h1>Партнерська реєстрація</h1>
                        <p>Приєднуйтесь до нашої партнерської програми</p>
                    </div>
                    
                    <!-- Features Preview -->
                    <div class="features-preview">
                        <div class="feature-badge partner-badge">
                            <i class="fas fa-crown"></i>
                            <span>VIP партнер</span>
                        </div>
                        <div class="feature-badge partner-badge">
                            <i class="fas fa-chart-bar"></i>
                            <span>Аналітика</span>
                        </div>
                        <div class="feature-badge partner-badge">
                            <i class="fas fa-handshake"></i>
                            <span>Партнерство</span>
                        </div>
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
                        <button type="button" class="social-btn google-btn" onclick="googleRegister()">
                            <i class="fab fa-google"></i>
                            <span>Реєстрація через Google Business</span>
                        </button>
                        
                        <div class="divider">
                            <span>або заповніть бізнес-форму</span>
                        </div>
                    </div>
                    
                    <!-- Registration Form -->
                    <form method="POST" id="modernPartnerRegisterForm" class="modern-form" novalidate>
                        <div class="form-row">
                            <div class="form-group">
                                <div class="input-wrapper">
                                    <input type="text" 
                                           id="first_name" 
                                           name="first_name" 
                                           placeholder="Ім'я *"
                                           value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>"
                                           required>
                                    <div class="input-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="input-line"></div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <div class="input-wrapper">
                                    <input type="text" 
                                           id="last_name" 
                                           name="last_name" 
                                           placeholder="Прізвище *"
                                           value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>"
                                           required>
                                    <div class="input-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="input-line"></div>
                                </div>
                            </div>
                        </div>
                        
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
                                <input type="tel" 
                                       id="phone" 
                                       name="phone" 
                                       placeholder="Номер телефону *"
                                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                                       required>
                                <div class="input-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="input-line"></div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="input-wrapper">
                                <input type="text" 
                                       id="company" 
                                       name="company" 
                                       placeholder="Назва компанії *"
                                       value="<?php echo htmlspecialchars($_POST['company'] ?? ''); ?>"
                                       required>
                                <div class="input-icon">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div class="input-line"></div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="input-wrapper">
                                <input type="url" 
                                       id="website" 
                                       name="website" 
                                       placeholder="Веб-сайт компанії"
                                       value="<?php echo htmlspecialchars($_POST['website'] ?? ''); ?>">
                                <div class="input-icon">
                                    <i class="fas fa-globe"></i>
                                </div>
                                <div class="input-line"></div>
                            </div>
                        </div>
                        
                        <div class="form-row">
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
                            
                            <div class="form-group">
                                <div class="input-wrapper">
                                    <input type="password" 
                                           id="confirm_password" 
                                           name="confirm_password" 
                                           placeholder="Підтвердити пароль *"
                                           required>
                                    <div class="input-icon">
                                        <i class="fas fa-lock"></i>
                                    </div>
                                    <div class="input-line"></div>
                                    <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="checkbox-group">
                            <label class="modern-checkbox">
                                <input type="checkbox" name="agree_terms" required>
                                <span class="checkmark"></span>
                                <span class="checkbox-text">Я погоджуюсь з <a href="#" class="terms-link">умовами партнерства</a></span>
                            </label>
                            
                            <label class="modern-checkbox">
                                <input type="checkbox" name="agree_newsletter">
                                <span class="checkmark"></span>
                                <span class="checkbox-text">Отримувати партнерські новини та пропозиції</span>
                            </label>
                        </div>
                        
                        <button type="submit" name="register" class="modern-submit-btn partner-register-btn">
                            <span class="btn-text">Стати партнером</span>
                            <div class="btn-icon">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <div class="btn-ripple"></div>
                        </button>
                    </form>
                    
                    <!-- Footer Links -->
                    <div class="auth-footer-links">
                        <p>Вже маєте партнерський акаунт?</p>
                        <a href="login.php" class="login-link">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Увійти як партнер</span>
                        </a>
                    </div>
                    
                    <!-- User Link -->
                    <div class="user-suggestion">
                        <p>Звичайний користувач?</p>
                        <a href="../user/register.php" class="user-link">
                            <i class="fas fa-user-plus"></i>
                            <span>Реєстрація користувача</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle password visibility
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

// Google registration (placeholder)
function googleRegister() {
    alert('Google Business реєстрація буде доступна незабаром!');
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('modernPartnerRegisterForm');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    
    // Real-time password validation
    confirmPasswordInput.addEventListener('input', function() {
        if (passwordInput.value !== confirmPasswordInput.value) {
            confirmPasswordInput.setCustomValidity('Паролі не співпадають');
        } else {
            confirmPasswordInput.setCustomValidity('');
        }
    });
    
    passwordInput.addEventListener('input', function() {
        if (confirmPasswordInput.value && passwordInput.value !== confirmPasswordInput.value) {
            confirmPasswordInput.setCustomValidity('Паролі не співпадають');
        } else {
            confirmPasswordInput.setCustomValidity('');
        }
    });
});
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

.features-preview {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.feature-badge {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: var(--theme-bg-secondary);
    border-radius: 20px;
    border: 1px solid var(--theme-border);
    color: var(--theme-text);
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.partner-badge {
    background: linear-gradient(135deg, rgba(255, 152, 0, 0.1), rgba(245, 124, 0, 0.1));
    border-color: rgba(255, 152, 0, 0.3);
    color: #ff9800;
}

.feature-badge:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
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

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
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
    color: var(--current-gradient);
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
    color: var(--current-gradient);
}

.checkbox-group {
    display: flex;
    flex-direction: column;
    gap: 15px;
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

.terms-link {
    color: var(--current-gradient);
    text-decoration: none;
}

.terms-link:hover {
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

.login-link {
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

.login-link:hover {
    border-color: var(--current-gradient);
    background: var(--theme-bg);
    color: var(--current-gradient);
    text-decoration: none;
    transform: translateY(-2px);
}

.user-suggestion {
    text-align: center;
    margin-top: 20px;
    padding: 20px;
    background: var(--theme-bg-secondary);
    border-radius: 12px;
    border: 1px solid var(--theme-border);
}

.user-suggestion p {
    color: var(--theme-muted);
    margin-bottom: 12px;
    font-size: 0.95rem;
}

.user-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: linear-gradient(135deg, #4caf50, #2e7d32);
    border-radius: 8px;
    color: white;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.user-link:hover {
    background: linear-gradient(135deg, #2e7d32, #1b5e20);
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
}

/* Partner Register specific styles */
.partner-register-icon {
    background: var(--current-gradient) !important;
}

.partner-register-card .modern-submit-btn {
    background: var(--current-gradient) !important;
}

.partner-register-card .modern-submit-btn:hover {
    background: var(--current-gradient) !important;
    opacity: 0.9;
}

.partner-register-card .input-wrapper input:focus + .input-icon {
    color: var(--current-gradient);
}

.partner-register-card .input-line {
    background: var(--current-gradient);
}

.partner-register-card .checkmark {
    border-color: var(--current-gradient);
}

.partner-register-card .modern-checkbox input:checked + .checkmark {
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
@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .modern-auth-card {
        padding: 25px;
        margin: 10px;
    }
    
    .features-preview {
        flex-direction: column;
        align-items: center;
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

<?php
// Функції для партнерів (якщо не існують)
if (!function_exists('savePartnerInfo')) {
    function savePartnerInfo($partnerId, $company, $website) {
        // Зберігаємо додаткову інформацію про партнера
        try {
            $db = new Database();
            $db->query("UPDATE users SET company = ?, website = ? WHERE id = ?", 
                      [$company, $website, $partnerId]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

if (!function_exists('notifyAdminsNewPartner')) {
    function notifyAdminsNewPartner($email, $company) {
        // Повідомляємо адмінів про нового партнера
        try {
            $subject = "Новий партнер: " . $company;
            $message = "
            <h2>Новий партнер зареєстрований</h2>
            <p><strong>Email:</strong> " . $email . "</p>
            <p><strong>Компанія:</strong> " . $company . "</p>
            <p><a href='" . SITE_URL . "/admin/partners'>Перейти до управління партнерами</a></p>
            ";
            
            // Відправка всім адмінам
            $db = new Database();
            $result = $db->query("SELECT email FROM users WHERE role = 'admin'");
            while ($admin = $result->fetch_assoc()) {
                sendEmail($admin['email'], $subject, $message);
            }
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
?>