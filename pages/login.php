<?php
require_once '../config/config.php';
require_once '../config/database.php';

// Налаштування META тегів
$page_title = 'Вхід - ' . Settings::get('site_name', 'Дошка Оголошень');
$page_description = 'Увійдіть у свій обліковий запис на ' . Settings::get('site_name', 'Дошка Оголошень');
$page_keywords = 'вхід, авторизація, логін, ' . Settings::get('site_keywords', '');
$error_message = '';

// Перевіряємо, чи користувач вже авторизований
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean_input($_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);
    
    if (empty($email) || empty($password)) {
        $error_message = 'Заповніть всі поля!';
    } else {
        // Підключення до бази даних
        $database = new Database();
        $db = $database->getConnection();
        
        // Пошук користувача
        $query = "SELECT id, username, email, password, is_active FROM users WHERE email = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() === 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user['is_active']) {
                $error_message = 'Акаунт заблокований. Зверніться до адміністратора.';
            } elseif (password_verify($password, $user['password'])) {
                // Успішна авторизація
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                
                // Запам'ятати користувача
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/'); // 30 днів
                    
                    // Зберігаємо токен в базі даних
                    $update_query = "UPDATE users SET remember_token = ? WHERE id = ?";
                    $update_stmt = $db->prepare($update_query);
                    $update_stmt->execute([$token, $user['id']]);
                }
                
                // Перенаправлення
                $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '../index.php';
                header("Location: " . $redirect);
                exit();
            } else {
                $error_message = 'Невірний email або пароль!';
            }
        } else {
            $error_message = 'Невірний email або пароль!';
        }
    }
}

include '../includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-custom" data-aos="fade-up">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="text-gradient">Вхід</h2>
                        <p class="text-muted">Увійдіть у свій акаунт</p>
                    </div>
                    
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-1"></i>Email
                            </label>
                            <input type="email" class="form-control" id="email" name="email" 
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                   required>
                            <div class="invalid-feedback">
                                Будь ласка, введіть правильний email.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-1"></i>Пароль
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <button class="btn btn-outline-secondary" type="button" 
                                        onclick="togglePassword('password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">
                                Будь ласка, введіть пароль.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Запам'ятати мене
                                </label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-sign-in-alt me-2"></i>Увійти
                        </button>
                    </form>
                    
                    <div class="text-center">
                        <p class="mb-2">
                            <a href="forgot_password.php" class="text-decoration-none">
                                Забули пароль?
                            </a>
                        </p>
                        <p class="mb-0">Немає акаунта? 
                            <a href="register.php" class="text-decoration-none fw-bold">Зареєструватися</a>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Демо акаунт -->
            <div class="card mt-3 border-warning" data-aos="fade-up" data-aos-delay="200">
                <div class="card-body text-center">
                    <h6 class="card-title text-warning">
                        <i class="fas fa-info-circle me-1"></i>Демо акаунт
                    </h6>
                    <p class="card-text small text-muted mb-2">
                        Для тестування використовуйте:
                    </p>
                    <code class="small">admin@example.com / password</code>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>