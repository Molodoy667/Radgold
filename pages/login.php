<?php
// Якщо користувач вже авторизований, перенаправляємо
if (function_exists('isLoggedIn') && isLoggedIn()) {
    $redirectUrl = defined('SITE_URL') ? SITE_URL : '/';
    if (function_exists('redirect')) {
        redirect($redirectUrl);
    } else {
        header('Location: ' . $redirectUrl);
        exit;
    }
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
                "SELECT id, username, email, password, role, status FROM users WHERE email = ?",
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
                    
                    $redirectUrl = defined('SITE_URL') ? SITE_URL : '/';
                    if (function_exists('redirect')) {
                        redirect($redirectUrl);
                    } else {
                        header('Location: ' . $redirectUrl);
                        exit;
                    }
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

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-7">
            <div class="card border-0 shadow-lg my-5">
                <div class="card-header gradient-bg text-white text-center py-4">
                    <h3 class="mb-0"><i class="fas fa-sign-in-alt me-2"></i>Вхід в систему</h3>
                </div>
                <div class="card-body p-5">
                    <?php if ($error): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" class="ajax-form">
                        <div class="mb-4">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2"></i>Email адреса
                            </label>
                            <input type="email" class="form-control form-control-lg" id="email" name="email" 
                                   value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>Пароль
                            </label>
                            <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                            <div class="invalid-feedback"></div>
                        </div>
                        
                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Запам'ятати мене
                            </label>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn gradient-bg text-white btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Увійти
                            </button>
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <p class="mb-3">Ще не маєте акаунт?</p>
                        <a href="<?php echo function_exists('getSiteUrl') ? getSiteUrl('register') : '/register'; ?>" class="btn btn-outline-primary">
                            <i class="fas fa-user-plus me-2"></i>Створити акаунт
                        </a>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="<?php echo function_exists('getSiteUrl') ? getSiteUrl('forgot-password') : '/forgot-password'; ?>" class="text-decoration-none">
                            <i class="fas fa-key me-2"></i>Забули пароль?
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 15px;
    overflow: hidden;
}

.form-control-lg {
    border-radius: 10px;
    padding: 12px 16px;
}

.btn-lg {
    border-radius: 10px;
    padding: 12px 24px;
}
</style>
