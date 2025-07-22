<?php
require_once '../core/config.php';
require_once '../core/database.php';
require_once '../core/functions.php';

// Якщо користувач вже авторизований, перенаправляємо в адмінку
if (isLoggedIn() && isAdmin()) {
    redirect(SITE_URL . '/admin');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Заповніть всі поля';
    } else {
        try {
            $db = Database::getInstance();
            $result = $db->query(
                "SELECT id, username, email, password, role, status FROM users WHERE email = ? AND role = 'admin'",
                [$email]
            );
            
            if ($user = $result->fetch_assoc()) {
                if ($user['status'] !== 'active') {
                    $error = 'Обліковий запис заблокований';
                } elseif (password_verify($password, $user['password'])) {
                    // Успішна авторизація
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role'];
                    
                    // Оновлюємо час останнього входу
                    $db->update(
                        "UPDATE users SET last_login = NOW() WHERE id = ?",
                        [$user['id']]
                    );
                    
                    redirect(SITE_URL . '/admin');
                } else {
                    $error = 'Невірний email або пароль';
                }
            } else {
                $error = 'Невірний email або пароль';
            }
        } catch (Exception $e) {
            $error = 'Помилка входу в систему';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вхід в адмін-панель - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../themes/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            max-width: 400px;
            width: 100%;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header i {
            font-size: 4rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="login-card mx-auto">
                    <div class="login-header">
                        <i class="fas fa-shield-alt"></i>
                        <h3 class="mt-3 mb-0">Адмін-панель</h3>
                        <p class="text-muted"><?php echo SITE_NAME; ?></p>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2"></i>Email
                            </label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>Пароль
                            </label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Запам'ятати мене
                            </label>
                        </div>
                        
                        <button type="submit" class="btn gradient-bg text-white w-100 mb-3">
                            <i class="fas fa-sign-in-alt me-2"></i>Увійти
                        </button>
                        
                        <div class="text-center">
                            <a href="<?php echo SITE_URL; ?>" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-2"></i>Повернутися на сайт
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
