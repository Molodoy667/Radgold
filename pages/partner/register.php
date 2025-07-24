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
                'user_type' => 'partner',
                'status' => 'pending', // Партнери потребують модерації
                'newsletter' => $agreeNewsletter,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            if (registerUser($userData)) {
                // Додаткова інформація про компанію
                $partnerId = getLastInsertId();
                savePartnerInfo($partnerId, $company, $website);
                
                $success = 'Реєстрація успішна! Ваш акаунт буде активовано після модерації';
                // Відправка вітального email
                sendWelcomeEmail($email, $firstName, 'partner');
                // Повідомлення адмінам
                notifyAdminsNewPartner($email, $company);
            } else {
                $error = 'Помилка при реєстрації. Спробуйте ще раз';
            }
        }
    }
}

include '../../themes/header.php';
?>

<div class="modern-auth-container partner-container">
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
                        <div class="header-icon partner-icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h1>Партнерський акаунт</h1>
                        <p>Приєднуйтесь до нашої партнерської програми</p>
                    </div>
                    
                    <!-- Features Preview -->
                    <div class="features-preview">
                        <div class="feature-badge partner-badge">
                            <i class="fas fa-crown"></i>
                            <span>VIP партнер</span>
                        </div>
                        <div class="feature-badge partner-badge">
                            <i class="fas fa-chart-line"></i>
                            <span>Аналітика</span>
                        </div>
                        <div class="feature-badge partner-badge">
                            <i class="fas fa-headset"></i>
                            <span>Підтримка 24/7</span>
                        </div>
                    </div>
                                    </div>
                                    
                                    <div class="feature-item">
                                        <i class="fas fa-bullseye"></i>
                                        <div>
                                            <h5>Точне таргетування</h5>
                                            <p>Досягайте саме вашої цільової аудиторії</p>
                                        </div>
                                    </div>
                                    
                                    <div class="feature-item">
                                        <i class="fas fa-analytics"></i>
                                        <div>
                                            <h5>Детальна аналітика</h5>
                                            <p>Відстежуйте ROI в реальному часі</p>
                                        </div>
                                    </div>
                                    
                                    <div class="feature-item">
                                        <i class="fas fa-headset"></i>
                                        <div>
                                            <h5>VIP підтримка</h5>
                                            <p>Персональний менеджер 24/7</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="auth-stats">
                                    <div class="stat-item">
                                        <span class="stat-number">500+</span>
                                        <span class="stat-label">Партнерів</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-number">50M+</span>
                                        <span class="stat-label">Показів</span>
                                    </div>
                                    <div class="stat-item">
                                        <span class="stat-number">98%</span>
                                        <span class="stat-label">Задоволених</span>
                                    </div>
                                </div>
                                
                                <div class="auth-footer">
                                    <p>Вже є акаунт? <a href="login.php" class="text-warning">Увійти</a></p>
                                    <p>Звичайний користувач? <a href="../user/register.php" class="text-light">Реєстрація користувача</a></p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Права частина з формою -->
                        <div class="col-lg-7 auth-form-section">
                            <div class="auth-form-container">
                                <div class="auth-header">
                                    <h3>Реєстрація партнера</h3>
                                    <p>Приєднуйтесь до провідної рекламної платформи</p>
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
                                    <i class="fab fa-google me-2"></i>Зареєструватися через Google Business
                                </button>
                                
                                <div class="social-divider">
                                    <span>або заповніть бізнес-форму</span>
                                </div>
                                
                                <!-- Форма реєстрації -->
                                <form method="POST" id="registerForm" class="auth-form" novalidate>
                                    <!-- Особисті дані -->
                                    <div class="form-section">
                                        <h6 class="section-title">
                                            <i class="fas fa-user me-2"></i>Особисті дані
                                        </h6>
                                        
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
                                        
                                        <div class="row">
                                            <div class="col-md-6">
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
                                                               placeholder="business@company.com"
                                                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                                               required>
                                                        <div class="invalid-feedback">Введіть коректний email</div>
                                                        <div class="valid-feedback">Email доступний</div>
                                                    </div>
                                                    <div class="email-check-status"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
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
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Бізнес інформація -->
                                    <div class="form-section">
                                        <h6 class="section-title">
                                            <i class="fas fa-building me-2"></i>Бізнес інформація
                                        </h6>
                                        
                                        <div class="form-group mb-3">
                                            <label for="company" class="form-label">Назва компанії *</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas fa-building"></i>
                                                </span>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="company" 
                                                       name="company" 
                                                       placeholder="ТОВ Приклад"
                                                       value="<?php echo htmlspecialchars($_POST['company'] ?? ''); ?>"
                                                       required>
                                                <div class="invalid-feedback">Введіть назву компанії</div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group mb-3">
                                            <label for="website" class="form-label">Веб-сайт</label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="fas fa-globe"></i>
                                                </span>
                                                <input type="url" 
                                                       class="form-control" 
                                                       id="website" 
                                                       name="website" 
                                                       placeholder="https://example.com"
                                                       value="<?php echo htmlspecialchars($_POST['website'] ?? ''); ?>">
                                                <div class="invalid-feedback">Введіть коректний URL</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Безпека -->
                                    <div class="form-section">
                                        <h6 class="section-title">
                                            <i class="fas fa-shield-alt me-2"></i>Безпека
                                        </h6>
                                        
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
                                    </div>
                                    
                                    <div class="form-checks mb-4">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="agree_terms" name="agree_terms" required>
                                            <label class="form-check-label" for="agree_terms">
                                                Я погоджуюся з <a href="#" target="_blank">партнерською угодою</a> та <a href="#" target="_blank">політикою конфіденційності</a> *
                                            </label>
                                            <div class="invalid-feedback">Необхідно прийняти умови</div>
                                        </div>
                                        
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="agree_newsletter" name="agree_newsletter">
                                            <label class="form-check-label" for="agree_newsletter">
                                                Отримувати маркетингові новини та спеціальні пропозиції
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" name="register" class="btn btn-partner btn-lg w-100 mb-3">
                                        <i class="fas fa-crown me-2"></i>Стати партнером
                                    </button>
                                </form>
                                
                                <div class="partner-note">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <small>Акаунт партнера буде активовано після перевірки модератором протягом 24 годин</small>
                                </div>
                                
                                <div class="security-note">
                                    <i class="fas fa-shield-alt me-2"></i>
                                    <small>Ваші бізнес-дані захищені банківським рівнем безпеки</small>
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
.partner-register-container {
    background: linear-gradient(135deg, #ff6f00 0%, #ff8f00 25%, #ffa000 50%, #ffb300 75%, #ffc107 100%);
    min-height: 100vh;
}

.partner-register-container .auth-background {
    background: linear-gradient(45deg, rgba(255, 193, 7, 0.1) 0%, rgba(255, 152, 0, 0.1) 100%);
}

.partner-auth .auth-info {
    background: linear-gradient(135deg, #e65100 0%, #ff6f00 50%, #ff8f00 100%);
    color: white;
    position: relative;
}

.partner-auth .auth-brand i {
    background: linear-gradient(45deg, #ffc107, #ffb300);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.3));
}

.form-section {
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: rgba(255, 152, 0, 0.05);
    border-radius: 8px;
    border-left: 4px solid #ff9800;
}

.section-title {
    color: #e65100;
    font-weight: 600;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid rgba(255, 152, 0, 0.2);
}

.btn-partner {
    background: linear-gradient(135deg, #ff6f00 0%, #ff8f00 50%, #ffa000 100%);
    border: none;
    color: white;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(255, 111, 0, 0.3);
}

.btn-partner:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(255, 111, 0, 0.4);
    color: white;
}

.partner-note {
    text-align: center;
    margin-top: 1rem;
    padding: 0.75rem;
    background: rgba(255, 193, 7, 0.1);
    border-radius: 8px;
    color: #e65100;
    border: 1px solid rgba(255, 152, 0, 0.2);
}

.auth-stats {
    display: flex;
    justify-content: space-around;
    margin: 2rem 0;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 10px;
    backdrop-filter: blur(10px);
}

.shape-5 {
    width: 80px;
    height: 80px;
    top: 30%;
    right: 5%;
    animation-delay: -12s;
    background: linear-gradient(45deg, rgba(255, 193, 7, 0.1), rgba(255, 111, 0, 0.1));
}

@keyframes goldShimmer {
    0%, 100% { transform: translateY(0px) rotate(0deg) scale(1); opacity: 0.7; }
    50% { transform: translateY(-15px) rotate(180deg) scale(1.1); opacity: 1; }
}

.partner-register-container .shape {
    animation: goldShimmer 15s ease-in-out infinite;
}

.partner-register-container .auth-particles {
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="15" cy="15" r="1" fill="rgba(255,193,7,0.4)"/><circle cx="85" cy="65" r="0.8" fill="rgba(255,152,0,0.5)"/><circle cx="45" cy="85" r="1.2" fill="rgba(255,111,0,0.3)"/><polygon points="70,10 75,5 80,10 75,15" fill="rgba(255,193,7,0.2)"/></svg>') repeat;
    animation: goldShimmer 30s linear infinite;
}

@media (max-width: 992px) {
    .form-section {
        padding: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .auth-stats {
        margin: 1rem 0;
    }
}

@media (max-width: 768px) {
    .partner-register-container {
        padding: 1rem 0;
    }
    
    .auth-info {
        display: none;
    }
    
    .auth-form-container {
        padding: 2rem 1.5rem;
    }
    
    .form-section {
        padding: 0.75rem;
    }
}
</style>

<script src="https://accounts.google.com/gsi/client" async defer></script>
<script>
// Реалізація аналогічна до user/register.php але з додатковими полями для бізнесу
document.addEventListener('DOMContentLoaded', function() {
    // Ініціалізація всіх функцій з user/register.php
    // + додаткова логіка для бізнес полів
    
    const websiteInput = document.getElementById('website');
    websiteInput.addEventListener('blur', function() {
        let value = this.value;
        if (value && !value.startsWith('http://') && !value.startsWith('https://')) {
            this.value = 'https://' + value;
        }
    });
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
            user_type: 'partner',
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

// Інші функції аналогічні до user/register.php
</script>

<?php 
include '../../themes/footer.php';

// Додаткові функції для партнерів
function getLastInsertId() {
    try {
        $db = new Database();
        return $db->insert_id;
    } catch (Exception $e) {
        return 0;
    }
}

function savePartnerInfo($partnerId, $company, $website) {
    try {
        $db = new Database();
        $stmt = $db->prepare("
            INSERT INTO partner_info (user_id, company_name, website, created_at) 
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->bind_param("iss", $partnerId, $company, $website);
        return $stmt->execute();
    } catch (Exception $e) {
        return false;
    }
}

function notifyAdminsNewPartner($email, $company) {
    try {
        $subject = "Новий партнер зареєструвався - " . SITE_NAME;
        $message = "
        <h3>Новий партнер</h3>
        <p><strong>Email:</strong> {$email}</p>
        <p><strong>Компанія:</strong> {$company}</p>
        <p><strong>Час реєстрації:</strong> " . date('d.m.Y H:i:s') . "</p>
        <p><a href='" . SITE_URL . "/admin/users'>Перейти до управління користувачами</a></p>
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
?>