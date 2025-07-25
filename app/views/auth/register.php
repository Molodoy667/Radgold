<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="windows-1251">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - Game Marketplace</title>
    <link rel="stylesheet" href="/assets/css/theme.css">
    <link rel="stylesheet" href="/assets/css/auth.css">
    <script src="/assets/js/theme.js"></script>
    <script src="/assets/js/auth.js"></script>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>🎮 Game Marketplace</h1>
                <p>Создайте новый аккаунт</p>
            </div>

            <form id="registerForm" class="auth-form">
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="text" name="login" placeholder="Логин" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Пароль" required>
                </div>
                <div class="form-group">
                    <input type="password" name="confirm_password" placeholder="Подтвердите пароль" required>
                </div>
                <button type="submit" class="btn-primary">Зарегистрироваться</button>
            </form>

            <div id="registerError" class="error-message" style="display:none;"></div>
            <div id="registerSuccess" class="success-message" style="display:none;"></div>

            <div class="auth-footer">
                <p>Уже есть аккаунт? <a href="/login">Войти</a></p>
                <button onclick="toggleTheme()" class="btn-theme">🌙</button>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const password = formData.get('password');
            const confirmPassword = formData.get('confirm_password');
            
            // Проверка паролей
            if (password !== confirmPassword) {
                showError('Пароли не совпадают');
                return;
            }
            
            if (password.length < 6) {
                showError('Пароль должен содержать минимум 6 символов');
                return;
            }
            
            // Отправка формы
            fetch('/auth/register', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess('Регистрация успешна! Перенаправление...');
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 2000);
                } else {
                    showError(data.error || 'Ошибка при регистрации');
                }
            })
            .catch(error => {
                showError('Ошибка сети');
            });
        });
        
        function showError(message) {
            const errorDiv = document.getElementById('registerError');
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            document.getElementById('registerSuccess').style.display = 'none';
        }
        
        function showSuccess(message) {
            const successDiv = document.getElementById('registerSuccess');
            successDiv.textContent = message;
            successDiv.style.display = 'block';
            document.getElementById('registerError').style.display = 'none';
        }
    </script>
</body>
</html>