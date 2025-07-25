<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - Game Marketplace</title>
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
                <p>Войдите в свой аккаунт</p>
            </div>
            
            <form id="loginForm" class="auth-form">
                <div class="form-group">
                    <input type="text" name="login" placeholder="Логин" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Пароль" required>
                </div>
                <button type="submit" class="btn-primary">Войти</button>
            </form>
            
            <div id="loginError" class="error-message" style="display:none;"></div>
            
            <div class="auth-footer">
                <p>Нет аккаунта? <a href="/register">Зарегистрироваться</a></p>
                <button onclick="toggleTheme()" class="btn-theme">🌙</button>
            </div>
        </div>
    </div>
</body>
</html>