<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="windows-1251">
    <title>Вход в маркетплейс</title>
    <link rel="stylesheet" href="/assets/css/theme.css">
    <script src="/assets/js/theme.js"></script>
    <script src="/assets/js/auth.js"></script>
</head>
<body>
<div class="login-container">
    <h2>Вход</h2>
    <form id="loginForm">
        <input type="text" name="login" placeholder="Логин" required><br>
        <input type="password" name="password" placeholder="Пароль" required><br>
        <button type="submit">Войти</button>
    </form>
    <div id="loginError" style="color:red;display:none;"></div>
    <button onclick="toggleTheme()">Сменить тему</button>
</div>
</body>
</html>