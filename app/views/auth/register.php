<?php
ob_start();
?>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-primary/20 to-secondary/20 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <!-- Логотип и заголовок -->
        <div class="text-center">
            <div class="mx-auto w-16 h-16 bg-gradient-to-br from-primary to-secondary rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                <span class="text-white font-bold text-2xl">GM</span>
            </div>
            <h2 class="text-3xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                Создать аккаунт
            </h2>
            <p class="mt-2 text-muted-foreground">
                Присоединяйтесь к GameMarket Pro уже сегодня
            </p>
        </div>

        <!-- Форма регистрации -->
        <div class="bg-card p-8 rounded-2xl shadow-xl border border-border backdrop-blur-sm">
            <form id="register-form" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                
                <!-- Имя пользователя -->
                <div class="space-y-2">
                    <label for="username" class="block text-sm font-medium">
                        Имя пользователя <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="icon-user w-5 h-5 text-muted-foreground"></i>
                        </div>
                        <input 
                            id="username" 
                            name="username" 
                            type="text" 
                            autocomplete="username"
                            required 
                            class="input-field pl-10"
                            placeholder="Введите имя пользователя"
                            minlength="3"
                            maxlength="50"
                        >
                    </div>
                    <div class="text-xs text-muted-foreground">От 3 до 50 символов, только буквы, цифры и _</div>
                    <div class="error-message hidden" data-field="username"></div>
                </div>

                <!-- Email -->
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="icon-mail w-5 h-5 text-muted-foreground"></i>
                        </div>
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            autocomplete="email"
                            required 
                            class="input-field pl-10"
                            placeholder="Введите email"
                        >
                    </div>
                    <div class="error-message hidden" data-field="email"></div>
                </div>

                <!-- Пароль -->
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium">
                        Пароль <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="icon-lock w-5 h-5 text-muted-foreground"></i>
                        </div>
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            autocomplete="new-password"
                            required 
                            class="input-field pl-10 pr-10"
                            placeholder="Создайте пароль"
                            minlength="6"
                        >
                        <button 
                            type="button" 
                            id="toggle-password"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center"
                        >
                            <i class="icon-eye w-5 h-5 text-muted-foreground hover:text-foreground transition-colors"></i>
                        </button>
                    </div>
                    <div class="text-xs text-muted-foreground">Минимум 6 символов</div>
                    <div class="error-message hidden" data-field="password"></div>
                </div>

                <!-- Подтверждение пароля -->
                <div class="space-y-2">
                    <label for="password_confirmation" class="block text-sm font-medium">
                        Подтверждение пароля <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="icon-lock w-5 h-5 text-muted-foreground"></i>
                        </div>
                        <input 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            type="password" 
                            autocomplete="new-password"
                            required 
                            class="input-field pl-10 pr-10"
                            placeholder="Повторите пароль"
                        >
                        <button 
                            type="button" 
                            id="toggle-password-confirm"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center"
                        >
                            <i class="icon-eye w-5 h-5 text-muted-foreground hover:text-foreground transition-colors"></i>
                        </button>
                    </div>
                    <div class="error-message hidden" data-field="password_confirmation"></div>
                </div>

                <!-- Соглашения -->
                <div class="space-y-3">
                    <div class="flex items-start">
                        <input 
                            id="terms" 
                            name="terms" 
                            type="checkbox" 
                            required
                            class="checkbox mt-1"
                        >
                        <label for="terms" class="ml-2 text-sm">
                            Я принимаю <a href="/terms" class="text-primary hover:text-primary/80 transition-colors">Условия использования</a> 
                            и <a href="/privacy" class="text-primary hover:text-primary/80 transition-colors">Политику конфиденциальности</a>
                        </label>
                    </div>
                    <div class="error-message hidden" data-field="terms"></div>
                    
                    <div class="flex items-start">
                        <input 
                            id="newsletter" 
                            name="newsletter" 
                            type="checkbox" 
                            class="checkbox mt-1"
                        >
                        <label for="newsletter" class="ml-2 text-sm">
                            Получать новости и специальные предложения на email
                        </label>
                    </div>
                </div>

                <!-- Кнопка регистрации -->
                <button 
                    type="submit" 
                    class="btn-primary w-full group relative overflow-hidden"
                    id="register-button"
                >
                    <span class="relative z-10 flex items-center justify-center">
                        <i class="icon-user-plus w-5 h-5 mr-2"></i>
                        Создать аккаунт
                    </span>
                    <div class="absolute inset-0 bg-gradient-to-r from-primary to-secondary opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </button>

                <!-- Общие ошибки -->
                <div id="form-errors" class="hidden">
                    <div class="bg-red-500/10 border border-red-500/20 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="icon-alert-circle w-5 h-5 text-red-500 mr-2"></i>
                            <span class="text-red-500 text-sm" id="error-text"></span>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Разделитель -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-border"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-card text-muted-foreground">или</span>
                </div>
            </div>

            <!-- Социальные сети (для будущего использования) -->
            <div class="grid grid-cols-2 gap-3">
                <button class="btn-secondary flex items-center justify-center py-2.5" disabled>
                    <i class="icon-brand-vk w-5 h-5 mr-2"></i>
                    VKontakte
                </button>
                <button class="btn-secondary flex items-center justify-center py-2.5" disabled>
                    <i class="icon-brand-steam w-5 h-5 mr-2"></i>
                    Steam
                </button>
            </div>

            <!-- Ссылка на вход -->
            <div class="text-center mt-6">
                <span class="text-muted-foreground">Уже есть аккаунт? </span>
                <a href="/login" class="text-primary hover:text-primary/80 transition-colors font-medium">
                    Войти
                </a>
            </div>
        </div>

        <!-- Дополнительная информация -->
        <div class="text-center">
            <div class="flex items-center justify-center space-x-4 text-sm text-muted-foreground">
                <a href="/help" class="hover:text-primary transition-colors">Помощь</a>
                <span>•</span>
                <a href="/privacy" class="hover:text-primary transition-colors">Конфиденциальность</a>
                <span>•</span>
                <a href="/terms" class="hover:text-primary transition-colors">Условия</a>
            </div>
        </div>
    </div>
</div>

<style>
.input-field {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid rgb(var(--color-border));
    border-radius: var(--border-radius);
    background: rgb(var(--color-background));
    color: rgb(var(--color-foreground));
    transition: var(--transition-fast);
}

.input-field:focus {
    outline: none;
    border-color: rgb(var(--color-primary));
    box-shadow: 0 0 0 2px rgb(var(--color-primary) / 0.2);
}

.checkbox {
    width: 1rem;
    height: 1rem;
    border: 1px solid rgb(var(--color-border));
    border-radius: 3px;
    background: rgb(var(--color-background));
    cursor: pointer;
}

.checkbox:checked {
    background: rgb(var(--color-primary));
    border-color: rgb(var(--color-primary));
}

.error-message {
    color: #ef4444;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.icon-mail::before { content: "📧"; }
.icon-user-plus::before { content: "👤"; }
.icon-eye::before { content: "👁️"; }
.icon-eye-off::before { content: "🙈"; }
.icon-alert-circle::before { content: "⚠️"; }
.icon-brand-vk::before { content: "🔵"; }
.icon-brand-steam::before { content: "🎮"; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('register-form');
    const button = document.getElementById('register-button');
    const togglePassword = document.getElementById('toggle-password');
    const togglePasswordConfirm = document.getElementById('toggle-password-confirm');
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    
    // Переключение видимости пароля
    togglePassword.addEventListener('click', function() {
        togglePasswordVisibility(passwordInput, this);
    });
    
    togglePasswordConfirm.addEventListener('click', function() {
        togglePasswordVisibility(passwordConfirmInput, this);
    });
    
    function togglePasswordVisibility(input, button) {
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
        
        const icon = button.querySelector('i');
        icon.className = type === 'password' ? 
            'icon-eye w-5 h-5 text-muted-foreground hover:text-foreground transition-colors' : 
            'icon-eye-off w-5 h-5 text-muted-foreground hover:text-foreground transition-colors';
    }
    
    // Валидация паролей в реальном времени
    passwordConfirmInput.addEventListener('input', function() {
        const password = passwordInput.value;
        const passwordConfirm = this.value;
        const errorEl = document.querySelector('[data-field="password_confirmation"]');
        
        if (passwordConfirm && password !== passwordConfirm) {
            errorEl.textContent = 'Пароли не совпадают';
            errorEl.classList.remove('hidden');
        } else {
            errorEl.textContent = '';
            errorEl.classList.add('hidden');
        }
    });
    
    // Обработка формы
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Очистка предыдущих ошибок
        clearErrors();
        
        // Клиентская валидация
        if (!validateForm()) {
            return;
        }
        
        // Показываем загрузку
        showLoading(button);
        
        try {
            const formData = new FormData(form);
            
            const response = await fetch('/register', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                showSuccess('Регистрация успешна! Перенаправляем...');
                setTimeout(() => {
                    window.location.href = data.redirect || '/login';
                }, 2000);
            } else {
                hideLoading(button);
                
                if (data.errors) {
                    showFieldErrors(data.errors);
                } else {
                    showError(data.message || 'Произошла ошибка при регистрации');
                }
            }
        } catch (error) {
            hideLoading(button);
            showError('Ошибка соединения');
        }
    });
    
    function validateForm() {
        let isValid = true;
        
        // Проверка паролей
        const password = passwordInput.value;
        const passwordConfirm = passwordConfirmInput.value;
        
        if (password !== passwordConfirm) {
            showFieldError('password_confirmation', 'Пароли не совпадают');
            isValid = false;
        }
        
        // Проверка согласия с условиями
        const termsCheckbox = document.getElementById('terms');
        if (!termsCheckbox.checked) {
            showFieldError('terms', 'Необходимо принять условия использования');
            isValid = false;
        }
        
        return isValid;
    }
    
    function showLoading(button) {
        button.disabled = true;
        button.innerHTML = `
            <span class="relative z-10 flex items-center justify-center">
                <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                Создание аккаунта...
            </span>
        `;
    }
    
    function hideLoading(button) {
        button.disabled = false;
        button.innerHTML = `
            <span class="relative z-10 flex items-center justify-center">
                <i class="icon-user-plus w-5 h-5 mr-2"></i>
                Создать аккаунт
            </span>
            <div class="absolute inset-0 bg-gradient-to-r from-primary to-secondary opacity-0 group-hover:opacity-100 transition-opacity"></div>
        `;
    }
    
    function clearErrors() {
        document.querySelectorAll('.error-message').forEach(el => {
            el.textContent = '';
            el.classList.add('hidden');
        });
        document.getElementById('form-errors').classList.add('hidden');
    }
    
    function showFieldErrors(errors) {
        for (const [field, message] of Object.entries(errors)) {
            showFieldError(field, message);
        }
    }
    
    function showFieldError(field, message) {
        const errorEl = document.querySelector(`[data-field="${field}"]`);
        if (errorEl) {
            errorEl.textContent = message;
            errorEl.classList.remove('hidden');
        }
    }
    
    function showError(message) {
        const errorContainer = document.getElementById('form-errors');
        const errorText = document.getElementById('error-text');
        errorText.textContent = message;
        errorContainer.classList.remove('hidden');
    }
    
    function showSuccess(message) {
        if (window.app && window.app.showNotification) {
            window.app.showNotification(message, 'success');
        } else {
            alert(message);
        }
    }
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
?>