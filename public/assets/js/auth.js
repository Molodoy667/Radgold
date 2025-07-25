// AJAX авторизация
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const errorDiv = document.getElementById('loginError');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(loginForm);
            const login = formData.get('login');
            const password = formData.get('password');
            
            // Валидация
            if (!login || !password) {
                showError('Пожалуйста, заполните все поля');
                return;
            }
            
            // Отправка запроса
            fetch('/auth/login', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Успешная авторизация
                    showSuccess('Вход выполнен успешно!');
                    setTimeout(() => {
                        window.location.href = '/products';
                    }, 1000);
                } else {
                    // Ошибка авторизации
                    showError(data.error || 'Ошибка входа');
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                showError('Произошла ошибка при подключении к серверу');
            });
        });
    }
    
    // Функция показа ошибки
    function showError(message) {
        if (errorDiv) {
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            errorDiv.classList.add('error-message');
            errorDiv.classList.remove('success-message');
            
            // Автоматическое скрытие через 5 секунд
            setTimeout(() => {
                errorDiv.style.display = 'none';
            }, 5000);
        }
    }
    
    // Функция показа успеха
    function showSuccess(message) {
        if (errorDiv) {
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            errorDiv.classList.add('success-message');
            errorDiv.classList.remove('error-message');
        }
    }
});

// Регистрация (если есть форма регистрации)
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    const registerError = document.getElementById('registerError');
    
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(registerForm);
            const email = formData.get('email');
            const login = formData.get('login');
            const password = formData.get('password');
            const confirmPassword = formData.get('confirm_password');
            
            // Валидация
            if (!email || !login || !password || !confirmPassword) {
                showRegisterError('Пожалуйста, заполните все поля');
                return;
            }
            
            if (password !== confirmPassword) {
                showRegisterError('Пароли не совпадают');
                return;
            }
            
            if (password.length < 6) {
                showRegisterError('Пароль должен содержать минимум 6 символов');
                return;
            }
            
            // Отправка запроса
            fetch('/auth/register', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showRegisterSuccess('Регистрация выполнена успешно!');
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 2000);
                } else {
                    showRegisterError(data.error || 'Ошибка регистрации');
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                showRegisterError('Произошла ошибка при подключении к серверу');
            });
        });
    }
    
    function showRegisterError(message) {
        if (registerError) {
            registerError.textContent = message;
            registerError.style.display = 'block';
            registerError.classList.add('error-message');
            registerError.classList.remove('success-message');
        }
    }
    
    function showRegisterSuccess(message) {
        if (registerError) {
            registerError.textContent = message;
            registerError.style.display = 'block';
            registerError.classList.add('success-message');
            registerError.classList.remove('error-message');
        }
    }
});