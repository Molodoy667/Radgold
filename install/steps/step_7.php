<?php
// Крок 7: Реєстрація адміністратора
$adminData = $_SESSION['install_data']['admin'] ?? [];
?>

<div class="step-content animate__animated animate__fadeIn">
    <div class="step-header">
        <h3><i class="fas fa-user-shield me-3"></i>Створення адміністратора</h3>
        <p class="text-muted">Створіть обліковий запис головного адміністратора сайту</p>
    </div>

    <form method="POST" id="adminForm" novalidate>
        <input type="hidden" name="step" value="7">
        
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="admin-form-container">
                    <div class="admin-avatar mb-4 text-center">
                        <div class="avatar-placeholder">
                            <i class="fas fa-user-crown"></i>
                        </div>
                        <h5 class="mt-3 mb-0">Головний адміністратор</h5>
                        <small class="text-muted">Матиме повний доступ до всіх функцій</small>
                    </div>

                    <div class="mb-4">
                        <label for="admin_login" class="form-label required">
                            <i class="fas fa-user me-2"></i>Логін адміністратора
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-at"></i></span>
                            <input type="text" 
                                   class="form-control" 
                                   id="admin_login" 
                                   name="admin_login" 
                                   value="<?php echo htmlspecialchars($adminData['admin_login'] ?? ''); ?>" 
                                   required 
                                   pattern="^[a-zA-Z0-9_]{3,20}$"
                                   placeholder="admin">
                            <div class="invalid-feedback">
                                Логін має містити 3-20 символів (букви, цифри, підкреслення)
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            Використовується для входу в адмін-панель. Тільки букви, цифри та підкреслення.
                        </small>
                    </div>

                    <div class="mb-4">
                        <label for="admin_email" class="form-label required">
                            <i class="fas fa-envelope me-2"></i>Email адміністратора
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" 
                                   class="form-control" 
                                   id="admin_email" 
                                   name="admin_email" 
                                   value="<?php echo htmlspecialchars($adminData['admin_email'] ?? ''); ?>" 
                                   required 
                                   placeholder="admin@example.com">
                            <div class="invalid-feedback">Введіть коректний email адрес</div>
                        </div>
                        <small class="form-text text-muted">
                            Використовується для відновлення паролю та важливих повідомлень.
                        </small>
                    </div>

                    <div class="mb-4">
                        <label for="admin_password" class="form-label required">
                            <i class="fas fa-lock me-2"></i>Пароль
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                            <input type="password" 
                                   class="form-control" 
                                   id="admin_password" 
                                   name="admin_password" 
                                   required 
                                   minlength="6"
                                   placeholder="Введіть надійний пароль">
                            <button class="btn btn-outline-secondary" 
                                    type="button" 
                                    id="togglePassword"
                                    title="Показати/сховати пароль">
                                <i class="fas fa-eye"></i>
                            </button>
                            <div class="invalid-feedback">Пароль має містити мінімум 6 символів</div>
                        </div>
                        <div class="password-strength mt-2">
                            <div class="strength-indicator">
                                <div class="strength-bar" id="strengthBar"></div>
                            </div>
                            <small class="strength-text" id="strengthText">Введіть пароль</small>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="admin_password_confirm" class="form-label required">
                            <i class="fas fa-lock me-2"></i>Підтвердження паролю
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-check"></i></span>
                            <input type="password" 
                                   class="form-control" 
                                   id="admin_password_confirm" 
                                   name="admin_password_confirm" 
                                   required 
                                   placeholder="Повторіть пароль">
                            <div class="invalid-feedback">Паролі не співпадають</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label for="admin_first_name" class="form-label">
                                    <i class="fas fa-user me-2"></i>Ім'я
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="admin_first_name" 
                                       name="admin_first_name" 
                                       value="<?php echo htmlspecialchars($adminData['admin_first_name'] ?? ''); ?>" 
                                       placeholder="Ваше ім'я">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-4">
                                <label for="admin_last_name" class="form-label">
                                    <i class="fas fa-user me-2"></i>Прізвище
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="admin_last_name" 
                                       name="admin_last_name" 
                                       value="<?php echo htmlspecialchars($adminData['admin_last_name'] ?? ''); ?>" 
                                       placeholder="Ваше прізвище">
                            </div>
                        </div>
                    </div>

                    <div class="security-info">
                        <h6><i class="fas fa-shield-alt me-2"></i>Рекомендації безпеки:</h6>
                        <ul class="security-list">
                            <li><i class="fas fa-check text-success me-2"></i>Використовуйте надійний пароль (мінімум 8 символів)</li>
                            <li><i class="fas fa-check text-success me-2"></i>Включіть цифри, букви різного регістру та спеціальні символи</li>
                            <li><i class="fas fa-check text-success me-2"></i>Не використовуйте особисту інформацію в паролі</li>
                            <li><i class="fas fa-check text-success me-2"></i>Запам'ятайте або збережіть дані в надійному місці</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="info-box mt-4">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Важливо:</strong> Дані адміністратора можна буде змінити в особистому кабінеті після установки.
            Обов'язково запам'ятайте або запишіть ваші дані для входу.
        </div>

        <div class="step-navigation">
            <a href="?step=6" class="btn btn-outline-secondary btn-lg">
                <i class="fas fa-chevron-left me-2"></i>Назад
            </a>
            <button type="submit" class="btn btn-primary btn-lg">
                Далі<i class="fas fa-chevron-right ms-2"></i>
            </button>
        </div>
    </form>
</div>

<style>
.admin-form-container {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
    padding: 30px;
    border-radius: 15px;
    border: 1px solid rgba(102, 126, 234, 0.1);
}

.admin-avatar {
    position: relative;
}

.avatar-placeholder {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 32px;
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.password-strength {
    width: 100%;
}

.strength-indicator {
    width: 100%;
    height: 6px;
    background: #e9ecef;
    border-radius: 3px;
    overflow: hidden;
}

.strength-bar {
    height: 100%;
    width: 0%;
    transition: all 0.3s ease;
    border-radius: 3px;
}

.strength-text {
    display: block;
    margin-top: 5px;
    font-weight: 500;
}

.security-info {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    border-left: 4px solid var(--primary-color, #667eea);
    margin-top: 20px;
}

.security-list {
    list-style: none;
    padding: 0;
    margin: 10px 0 0 0;
}

.security-list li {
    padding: 5px 0;
    font-size: 0.9em;
}

.input-group-text {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    border-color: rgba(102, 126, 234, 0.2);
    color: var(--primary-color, #667eea);
}

.form-control:focus + .input-group-text,
.form-control:focus {
    border-color: var(--primary-color, #667eea);
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

#togglePassword {
    border-left: none;
}

.was-validated .form-control:valid {
    border-color: #28a745;
}

.was-validated .form-control:invalid {
    border-color: #dc3545;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('adminForm');
    const passwordInput = document.getElementById('admin_password');
    const confirmPasswordInput = document.getElementById('admin_password_confirm');
    const toggleButton = document.getElementById('togglePassword');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    const loginInput = document.getElementById('admin_login');
    
    // Переключення видимості паролю
    toggleButton.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        const icon = this.querySelector('i');
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    });
    
    // Перевірка сили паролю
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const strength = checkPasswordStrength(password);
        updatePasswordStrength(strength);
        validatePasswordMatch();
    });
    
    // Перевірка співпадіння паролів
    confirmPasswordInput.addEventListener('input', validatePasswordMatch);
    
    function checkPasswordStrength(password) {
        let score = 0;
        let feedback = [];
        
        // Довжина
        if (password.length >= 8) score += 1;
        else feedback.push('мінімум 8 символів');
        
        // Маленькі букви
        if (/[a-z]/.test(password)) score += 1;
        else feedback.push('маленькі букви');
        
        // Великі букви
        if (/[A-Z]/.test(password)) score += 1;
        else feedback.push('великі букви');
        
        // Цифри
        if (/\d/.test(password)) score += 1;
        else feedback.push('цифри');
        
        // Спеціальні символи
        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) score += 1;
        else feedback.push('спеціальні символи');
        
        return { score, feedback };
    }
    
    function updatePasswordStrength(strength) {
        const { score, feedback } = strength;
        const percentage = (score / 5) * 100;
        
        strengthBar.style.width = percentage + '%';
        
        if (score <= 1) {
            strengthBar.style.background = '#dc3545';
            strengthText.textContent = 'Слабкий пароль';
            strengthText.style.color = '#dc3545';
        } else if (score <= 2) {
            strengthBar.style.background = '#fd7e14';
            strengthText.textContent = 'Середній пароль';
            strengthText.style.color = '#fd7e14';
        } else if (score <= 3) {
            strengthBar.style.background = '#ffc107';
            strengthText.textContent = 'Хороший пароль';
            strengthText.style.color = '#ffc107';
        } else if (score <= 4) {
            strengthBar.style.background = '#20c997';
            strengthText.textContent = 'Сильний пароль';
            strengthText.style.color = '#20c997';
        } else {
            strengthBar.style.background = '#28a745';
            strengthText.textContent = 'Дуже сильний пароль';
            strengthText.style.color = '#28a745';
        }
        
        if (feedback.length > 0 && passwordInput.value.length > 0) {
            strengthText.textContent += ' (додайте: ' + feedback.join(', ') + ')';
        }
    }
    
    function validatePasswordMatch() {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        
        if (confirmPassword && password !== confirmPassword) {
            confirmPasswordInput.setCustomValidity('Паролі не співпадають');
        } else {
            confirmPasswordInput.setCustomValidity('');
        }
    }
    
    // Валідація логіну в реальному часі
    loginInput.addEventListener('input', function() {
        const login = this.value;
        const pattern = /^[a-zA-Z0-9_]{3,20}$/;
        
        if (login && !pattern.test(login)) {
            this.setCustomValidity('Логін має містити 3-20 символів (тільки букви, цифри, підкреслення)');
        } else {
            this.setCustomValidity('');
        }
    });
    
    // Валідація форми
    form.addEventListener('submit', function(e) {
        const submitBtn = form.querySelector('button[type="submit"]');
        
        if (!form.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            
            // Скидаємо стан кнопки при помилці валідації
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Далі<i class="fas fa-chevron-right ms-2"></i>';
            }
        } else {
            // Додаткова перевірка сили паролю
            const passwordStrength = checkPasswordStrength(passwordInput.value);
            if (passwordStrength.score < 2 && passwordInput.value.length > 0) {
                e.preventDefault();
                alert('Будь ласка, оберіть більш надійний пароль для безпеки адміністративного доступу.');
                passwordInput.focus();
                
                // Скидаємо стан кнопки
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = 'Далі<i class="fas fa-chevron-right ms-2"></i>';
                }
                return;
            }
            
            // Показуємо стан завантаження при успішній валідації
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Обробка...';
            }
        }
        form.classList.add('was-validated');
    });
    
    // Автофокус на першому полі
    loginInput.focus();
});
</script>