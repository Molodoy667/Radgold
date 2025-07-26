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
                Добро пожаловать!
            </h2>
            <p class="mt-2 text-muted-foreground">
                Войдите в свой аккаунт для продолжения
            </p>
        </div>

        <!-- Форма входа -->
        <div class="bg-card p-8 rounded-2xl shadow-xl border border-border backdrop-blur-sm">
            <form id="login-form" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                
                <!-- Email или логин -->
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium">
                        Email или логин
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="icon-user w-5 h-5 text-muted-foreground"></i>
                        </div>
                        <input 
                            id="email" 
                            name="email" 
                            type="text" 
                            autocomplete="username"
                            required 
                            class="input-field pl-10"
                            placeholder="Введите email или логин"
                        >
                    </div>
                    <div class="error-message" data-field="email"></div>
                </div>

                <!-- Пароль -->
                <div class="space-y-2">
                    <label for="password" class="block text-sm font-medium">
                        Пароль
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="icon-lock w-5 h-5 text-muted-foreground"></i>
                        </div>
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            autocomplete="current-password"
                            required 
                            class="input-field pl-10 pr-10"
                            placeholder="Введите пароль"
                        >
                        <button 
                            type="button" 
                            id="toggle-password"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center"
                        >
                            <i class="icon-eye w-5 h-5 text-muted-foreground hover:text-foreground transition-colors"></i>
                        </button>
                    </div>
                    <div class="error-message" data-field="password"></div>
                </div>

                <!-- Запомнить меня и забыл пароль -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input 
                            id="remember-me" 
                            name="remember" 
                            type="checkbox" 
                            class="checkbox"
                        >
                        <label for="remember-me" class="ml-2 text-sm">
                            Запомнить меня
                        </label>
                    </div>
                    <a href="/forgot-password" class="text-sm text-primary hover:text-primary/80 transition-colors">
                        Забыли пароль?
                    </a>
                </div>

                <!-- Кнопка входа -->
                <button 
                    type="submit" 
                    class="btn-primary w-full group relative overflow-hidden"
                    id="login-button"
                >
                    <span class="relative z-10 flex items-center justify-center">
                        <i class="icon-login w-5 h-5 mr-2"></i>
                        Войти
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

            <!-- Ссылка на регистрацию -->
            <div class="text-center mt-6">
                <span class="text-muted-foreground">Нет аккаунта? </span>
                <a href="/register" class="text-primary hover:text-primary/80 transition-colors font-medium">
                    Зарегистрироваться
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('login-form');
    const button = document.getElementById('login-button');
    const togglePassword = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');
    
    // Переключение видимости пароля
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        const icon = this.querySelector('i');
        icon.className = type === 'password' ? 'icon-eye w-5 h-5 text-muted-foreground hover:text-foreground transition-colors' : 
                                               'icon-eye-off w-5 h-5 text-muted-foreground hover:text-foreground transition-colors';
    });
    
    // Обработка формы
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Очистка предыдущих ошибок
        clearErrors();
        
        // Показываем загрузку
        showLoading(button);
        
        try {
            const formData = new FormData(form);
            
            const response = await fetch('/login', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                showSuccess('Успешный вход! Перенаправляем...');
                setTimeout(() => {
                    window.location.href = data.redirect || '/';
                }, 1000);
            } else {
                hideLoading(button);
                
                if (data.errors) {
                    showFieldErrors(data.errors);
                } else {
                    showError(data.message || 'Произошла ошибка');
                }
            }
        } catch (error) {
            hideLoading(button);
            showError('Ошибка соединения');
        }
    });
    
    function showLoading(button) {
        button.disabled = true;
        button.innerHTML = `
            <span class="relative z-10 flex items-center justify-center">
                <div class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin mr-2"></div>
                Вход...
            </span>
        `;
    }
    
    function hideLoading(button) {
        button.disabled = false;
        button.innerHTML = `
            <span class="relative z-10 flex items-center justify-center">
                <i class="icon-login w-5 h-5 mr-2"></i>
                Войти
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
            const errorEl = document.querySelector(`[data-field="${field}"]`);
            if (errorEl) {
                errorEl.textContent = message;
                errorEl.classList.remove('hidden');
            }
        }
    }
    
    function showError(message) {
        const errorContainer = document.getElementById('form-errors');
        const errorText = document.getElementById('error-text');
        errorText.textContent = message;
        errorContainer.classList.remove('hidden');
    }
    
    function showSuccess(message) {
        App.notification.show(message, 'success');
    }
});
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
?>