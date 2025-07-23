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
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $agreeTerms = isset($_POST['agree_terms']);
        $agreeNewsletter = isset($_POST['agree_newsletter']);
        
        // Валідація
        if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($password)) {
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
                'user_type' => 'user',
                'status' => 'active',
                'newsletter' => $agreeNewsletter,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            if (registerUser($userData)) {
                $success = 'Реєстрація успішна! Тепер ви можете увійти в систему';
                // Відправка вітального email
                sendWelcomeEmail($email, $firstName, 'user');
            } else {
                $error = 'Помилка при реєстрації. Спробуйте ще раз';
            }
        }
    }
}

include '../../themes/header.php';
?>

<div class="auth-container register-container">
    <div class="auth-background">
        <div class="auth-particles"></div>
        <div class="auth-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
            <div class="shape shape-4"></div>
        </div>
    </div>
    
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 py-5">
            <div class="col-lg-11 col-xl-10">
                <div class="auth-card user-auth register-card">
                    <div class="row g-0">
                        <!-- Ліва частина з інформацією -->
                        <div class="col-lg-5 auth-info">
                            <div class="auth-info-content">
                                <div class="auth-brand">
                                    <i class="fas fa-user-plus"></i>
                                    <h2>Приєднуйтесь до AdBoard Pro</h2>
                                    <p>Почніть продавати вже сьогодні</p>
                                </div>
                                
                                <div class="auth-features">
                                    <div class="feature-item">
                                        <i class="fas fa-rocket"></i>
                                        <div>
                                            <h5>Швидкий старт</h5>
                                            <p>Розмістіть перше оголошення за 2 хвилини</p>
                                        </div>
                                    </div>
                                    
                                    <div class="feature-item">
                                        <i class="fas fa-users"></i>
                                        <div>
                                            <h5>Мільйони покупців</h5>
                                            <p>Ваші товари побачать тисячі людей щодня</p>
                                        </div>
                                    </div>
                                    
                                    <div class="feature-item">
                                        <i class="fas fa-mobile-alt"></i>
                                        <div>
                                            <h5>Мобільний додаток</h5>
                                            <p>Керуйте оголошеннями з будь-якого місця</p>
                                        </div>
                                    </div>
                                    
                                    <div class="feature-item">
                                        <i class="fas fa-handshake"></i>
                                        <div>
                                            <h5>Безпечні угоди</h5>
                                            <p>Захист покупців та продавців</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="auth-stats">
                                    <div class="stat-item">
                                        <span class="stat-number">50K+</span>
                                        <span class="stat-label">Користувачів</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-number">100K+</span>
                                        <span class="stat-label">Оголошень</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-number">1M+</span>
                                        <span class="stat-label">Переглядів</span>
                                    </div>
                                </div>
                                
                                <div class="auth-footer">
                                    <p>Вже є акаунт? <a href="login.php">Увійти</a></p>
                                    <p>Партнер? <a href="../partner/register.php" class="text-warning">Реєстрація рекламодавця</a></p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Права частина з формою -->
                        <div class="col-lg-7 auth-form-section">
                            <div class="auth-form-container">
                                <div class="auth-header">
                                    <h3>Створити акаунт користувача</h3>
                                    <p>Заповніть форму для реєстрації</p>
                                </div>
                                
                                <?php if ($error): ?>
                                    <div class="alert alert-danger animate__animated animate__shakeX">
                                        <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($success): ?>
                                    <div class="alert alert-success animate__animated animate__bounceIn">
                                        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                                        <div class="mt-2">
                                            <a href="login.php" class="btn btn-sm btn-outline-success">Увійти зараз</a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Google реєстрація -->
                                <button type="button" class="btn btn-google w-100 mb-4" onclick="googleRegister()">
                                    <i class="fab fa-google me-2"></i>Зареєструватися через Google
                                </button>
                                
                                <div class="social-divider">
                                    <span>або заповніть форму</span>
                                </div>
                                
                                <!-- Форма реєстрації -->
                                <form method="POST" id="registerForm" class="auth-form" novalidate>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="first_name" class="form-label">Ім'я *</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-user"></i>
                                                    </span>
                                                    <input type="text" 
                                                           class="form-control" 
                                                           id="first_name" 
                                                           name="first_name" 
                                                           placeholder="Ваше ім'я"
                                                           value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>"
                                                           required>
                                                    <div class="invalid-feedback">Введіть ваше ім'я</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="last_name" class="form-label">Прізвище *</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-user"></i>
                                                    </span>
                                                    <input type="text" 
                                                           class="form-control" 
                                                           id="last_name" 
                                                           name="last_name" 
                                                           placeholder="Ваше прізвище"
                                                           value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>"
                                                           required>
                                                    <div class="invalid-feedback">Введіть ваше прізвище</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="email" class="form-label">Email адреса *</label>
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
                                            <div class="invalid-feedback">Введіть коректний email</div>
                                            <div class="valid-feedback">Email доступний</div>
                                        </div>
                                        <div class="email-check-status"></div>
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="phone" class="form-label">Телефон *</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-phone"></i>
                                            </span>
                                            <input type="tel" 
                                                   class="form-control" 
                                                   id="phone" 
                                                   name="phone" 
                                                   placeholder="+380 XX XXX XX XX"
                                                   value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                                                   required>
                                            <div class="invalid-feedback">Введіть коректний номер телефону</div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="password" class="form-label">Пароль *</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-lock"></i>
                                                    </span>
                                                    <input type="password" 
                                                           class="form-control" 
                                                           id="password" 
                                                           name="password" 
                                                           placeholder="Мінімум 6 символів"
                                                           required>
                                                    <button type="button" class="btn btn-outline-secondary toggle-password">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <div class="invalid-feedback">Пароль занадто слабкий</div>
                                                </div>
                                                <div class="password-strength mt-2">
                                                    <div class="strength-bar">
                                                        <div class="strength-fill"></div>
                                                    </div>
                                                    <small class="strength-text">Введіть пароль</small>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="confirm_password" class="form-label">Підтвердіть пароль *</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        <i class="fas fa-check"></i>
                                                    </span>
                                                    <input type="password" 
                                                           class="form-control" 
                                                           id="confirm_password" 
                                                           name="confirm_password" 
                                                           placeholder="Повторіть пароль"
                                                           required>
                                                    <div class="invalid-feedback">Паролі не співпадають</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-checks mb-4">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="agree_terms" name="agree_terms" required>
                                            <label class="form-check-label" for="agree_terms">
                                                Я погоджуюся з <a href="#" target="_blank">умовами використання</a> та <a href="#" target="_blank">політикою конфіденційності</a> *
                                            </label>
                                            <div class="invalid-feedback">Необхідно прийняти умови</div>
                                        </div>
                                        
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="agree_newsletter" name="agree_newsletter">
                                            <label class="form-check-label" for="agree_newsletter">
                                                Отримувати новини та спеціальні пропозиції на email
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" name="register" class="btn btn-primary btn-lg w-100 mb-3">
                                        <i class="fas fa-user-plus me-2"></i>Створити акаунт
                                    </button>
                                </form>
                                
                                <div class="security-note">
                                    <i class="fas fa-shield-alt me-2"></i>
                                    <small>Ваші дані захищені 256-бітним шифруванням SSL</small>
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
.register-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
}

.register-card {
    margin: 2rem 0;
}

.auth-stats {
    display: flex;
    justify-content: space-around;
    margin: 2rem 0;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    backdrop-filter: blur(10px);
}

.stat-item {
    text-align: center;
}

.stat-number {
    display: block;
    font-size: 1.5rem;
    font-weight: bold;
    color: white;
}

.stat-label {
    font-size: 0.8rem;
    opacity: 0.8;
}

.password-strength {
    margin-top: 0.5rem;
}

.strength-bar {
    height: 4px;
    background: #e9ecef;
    border-radius: 2px;
    overflow: hidden;
}

.strength-fill {
    height: 100%;
    width: 0%;
    transition: all 0.3s ease;
    border-radius: 2px;
}

.strength-text {
    margin-top: 0.25rem;
    font-size: 0.8rem;
    font-weight: 500;
}

.email-check-status {
    margin-top: 0.25rem;
    font-size: 0.8rem;
}

.form-checks .form-check {
    margin-bottom: 0.75rem;
}

.form-checks .form-check-label {
    font-size: 0.9rem;
    line-height: 1.4;
}

.form-checks a {
    color: #667eea;
    text-decoration: none;
}

.form-checks a:hover {
    text-decoration: underline;
}

.security-note {
    text-align: center;
    margin-top: 1rem;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 8px;
    color: #6c757d;
}

.invalid-feedback {
    display: block;
    margin-top: 0.25rem;
}

.was-validated .form-control:invalid {
    border-color: #dc3545;
}

.was-validated .form-control:valid {
    border-color: #28a745;
}

.shape-4 {
    width: 100px;
    height: 100px;
    top: 20%;
    right: 10%;
    animation-delay: -10s;
}

@media (max-width: 992px) {
    .auth-info {
        order: 2;
        padding: 2rem 1rem;
    }
    
    .auth-form-section {
        order: 1;
    }
    
    .auth-stats {
        margin: 1rem 0;
    }
    
    .stat-number {
        font-size: 1.2rem;
    }
}

@media (max-width: 768px) {
    .register-container {
        padding: 1rem 0;
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
    const form = document.getElementById('registerForm');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    
    // Переключення видимості паролю
    document.querySelector('.toggle-password').addEventListener('click', function() {
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
    
    // Перевірка сили паролю
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        updatePasswordStrength(password);
    });
    
    // Перевірка співпадіння паролів
    confirmPasswordInput.addEventListener('input', function() {
        checkPasswordMatch();
    });
    
    passwordInput.addEventListener('input', checkPasswordMatch);
    
    // Перевірка email на унікальність
    let emailCheckTimeout;
    emailInput.addEventListener('input', function() {
        clearTimeout(emailCheckTimeout);
        const email = this.value;
        
        if (email && isValidEmail(email)) {
            emailCheckTimeout = setTimeout(() => {
                checkEmailUnique(email);
            }, 1000);
        }
    });
    
    // Форматування номера телефону
    phoneInput.addEventListener('input', function() {
        formatPhoneNumber(this);
    });
    
    // Валідація форми
    form.addEventListener('submit', function(e) {
        if (!validateForm()) {
            e.preventDefault();
        }
        form.classList.add('was-validated');
    });
    
    function updatePasswordStrength(password) {
        const strengthBar = document.querySelector('.strength-fill');
        const strengthText = document.querySelector('.strength-text');
        let strength = 0;
        let feedback = [];
        
        if (password.length >= 6) strength++;
        else feedback.push('мінімум 6 символів');
        
        if (/[a-z]/.test(password)) strength++;
        else feedback.push('маленькі букви');
        
        if (/[A-Z]/.test(password)) strength++;
        else feedback.push('великі букви');
        
        if (/\d/.test(password)) strength++;
        else feedback.push('цифри');
        
        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength++;
        else feedback.push('спеціальні символи');
        
        const percentage = (strength / 5) * 100;
        strengthBar.style.width = percentage + '%';
        
        if (strength <= 1) {
            strengthBar.style.background = '#dc3545';
            strengthText.textContent = 'Слабкий';
            strengthText.style.color = '#dc3545';
        } else if (strength <= 2) {
            strengthBar.style.background = '#fd7e14';
            strengthText.textContent = 'Середній';
            strengthText.style.color = '#fd7e14';
        } else if (strength <= 3) {
            strengthBar.style.background = '#ffc107';
            strengthText.textContent = 'Хороший';
            strengthText.style.color = '#ffc107';
        } else if (strength <= 4) {
            strengthBar.style.background = '#20c997';
            strengthText.textContent = 'Сильний';
            strengthText.style.color = '#20c997';
        } else {
            strengthBar.style.background = '#28a745';
            strengthText.textContent = 'Дуже сильний';
            strengthText.style.color = '#28a745';
        }
    }
    
    function checkPasswordMatch() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        if (confirmPassword && password !== confirmPassword) {
            confirmPasswordInput.setCustomValidity('Паролі не співпадають');
        } else {
            confirmPasswordInput.setCustomValidity('');
        }
    }
    
    function checkEmailUnique(email) {
        const statusDiv = document.querySelector('.email-check-status');
        statusDiv.innerHTML = '<small class="text-info"><i class="fas fa-spinner fa-spin me-1"></i>Перевірка email...</small>';
        
        fetch('../../ajax/check_email.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                statusDiv.innerHTML = '<small class="text-danger"><i class="fas fa-times me-1"></i>Email вже використовується</small>';
                emailInput.setCustomValidity('Email вже зайнятий');
            } else {
                statusDiv.innerHTML = '<small class="text-success"><i class="fas fa-check me-1"></i>Email доступний</small>';
                emailInput.setCustomValidity('');
            }
        })
        .catch(() => {
            statusDiv.innerHTML = '';
        });
    }
    
    function formatPhoneNumber(input) {
        let value = input.value.replace(/\D/g, '');
        
        if (value.startsWith('380')) {
            value = value.substring(3);
        } else if (value.startsWith('0')) {
            value = value.substring(1);
        }
        
        if (value.length >= 9) {
            value = value.substring(0, 9);
            const formatted = `+380 ${value.substring(0, 2)} ${value.substring(2, 5)} ${value.substring(5, 7)} ${value.substring(7, 9)}`;
            input.value = formatted;
        }
    }
    
    function validateForm() {
        const firstName = document.getElementById('first_name').value.trim();
        const lastName = document.getElementById('last_name').value.trim();
        const email = document.getElementById('email').value.trim();
        const phone = document.getElementById('phone').value.trim();
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        const agreeTerms = document.getElementById('agree_terms').checked;
        
        if (!firstName || !lastName || !email || !phone || !password || !confirmPassword) {
            showAlert('Заповніть всі обов\'язкові поля', 'error');
            return false;
        }
        
        if (!isValidEmail(email)) {
            showAlert('Введіть коректний email', 'error');
            return false;
        }
        
        if (password.length < 6) {
            showAlert('Пароль повинен містити мінімум 6 символів', 'error');
            return false;
        }
        
        if (password !== confirmPassword) {
            showAlert('Паролі не співпадають', 'error');
            return false;
        }
        
        if (!agreeTerms) {
            showAlert('Необхідно прийняти умови використання', 'error');
            return false;
        }
        
        return true;
    }
});

function googleRegister() {
    google.accounts.id.initialize({
        client_id: 'YOUR_GOOGLE_CLIENT_ID',
        callback: handleGoogleRegister
    });
    
    google.accounts.id.prompt();
}

function handleGoogleRegister(response) {
    fetch('../../ajax/google_auth.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            credential: response.credential,
            user_type: 'user',
            action: 'register'
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
        showAlert('Помилка реєстрації через Google', 'error');
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
    const header = container.querySelector('.auth-header');
    container.insertBefore(alertDiv, header.nextSibling);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}
</script>

<?php include '../../themes/footer.php'; ?>