<?php
require_once '../config/config.php';
require_once '../config/database.php';

$page_title = 'Реєстрація';
$error_message = '';
$success_message = '';

// Перевіряємо, чи користувач вже авторизований
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean_input($_POST['username']);
    $email = clean_input($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = clean_input($_POST['phone']);
    
    // Валідація
    if (empty($username) || empty($email) || empty($password)) {
        $error_message = 'Заповніть всі обов\'язкові поля!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Невірний формат email!';
    } elseif (strlen($password) < 6) {
        $error_message = 'Пароль повинен містити мінімум 6 символів!';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Паролі не співпадають!';
    } else {
        // Підключення до бази даних
        $database = new Database();
        $db = $database->getConnection();
        
        // Перевіряємо, чи користувач з таким email або username вже існує
        $check_query = "SELECT id FROM users WHERE email = ? OR username = ?";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->execute([$email, $username]);
        
        if ($check_stmt->rowCount() > 0) {
            $error_message = 'Користувач з таким email або ім\'ям вже існує!';
        } else {
            // Хешуємо пароль
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Вставляємо нового користувача
            $insert_query = "INSERT INTO users (username, email, password, phone) VALUES (?, ?, ?, ?)";
            $insert_stmt = $db->prepare($insert_query);
            
            if ($insert_stmt->execute([$username, $email, $hashed_password, $phone])) {
                $success_message = 'Реєстрація успішна! Тепер ви можете увійти в систему.';
            } else {
                $error_message = 'Помилка реєстрації. Спробуйте пізніше.';
            }
        }
    }
}

include '../includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-custom" data-aos="fade-up">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="text-gradient">Реєстрація</h2>
                        <p class="text-muted">Створіть свій обліковий запис</p>
                    </div>
                    
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success_message): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php echo $success_message; ?>
                        </div>
                        <div class="text-center">
                            <a href="login.php" class="btn btn-primary">
                                <i class="fas fa-sign-in-alt me-2"></i>Увійти
                            </a>
                        </div>
                    <?php else: ?>
                        <form method="POST" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user me-1"></i>Ім'я користувача *
                                </label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                                       required>
                                <div class="invalid-feedback">
                                    Будь ласка, введіть ім'я користувача.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i>Email *
                                </label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                       required>
                                <div class="invalid-feedback">
                                    Будь ласка, введіть правильний email.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone me-1"></i>Телефон
                                </label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                                       placeholder="+380501234567">
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-1"></i>Пароль *
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" 
                                           minlength="6" required>
                                    <button class="btn btn-outline-secondary" type="button" 
                                            onclick="togglePassword('password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">
                                    Пароль повинен містити мінімум 6 символів.
                                </div>
                                <div class="form-text">
                                    Мінімум 6 символів
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="confirm_password" class="form-label">
                                    <i class="fas fa-lock me-1"></i>Підтвердження пароля *
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirm_password" 
                                           name="confirm_password" minlength="6" required>
                                    <button class="btn btn-outline-secondary" type="button" 
                                            onclick="togglePassword('confirm_password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">
                                    Будь ласка, підтвердіть пароль.
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms" required>
                                    <label class="form-check-label" for="terms">
                                        Я погоджуюся з <a href="#" class="text-decoration-none">умовами користування</a> 
                                        та <a href="#" class="text-decoration-none">політикою конфіденційності</a> *
                                    </label>
                                    <div class="invalid-feedback">
                                        Необхідно погодитися з умовами.
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="fas fa-user-plus me-2"></i>Зареєструватися
                            </button>
                        </form>
                    <?php endif; ?>
                    
                    <div class="text-center">
                        <p class="mb-0">Вже маєте акаунт? 
                            <a href="login.php" class="text-decoration-none fw-bold">Увійти</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Валідація підтвердження пароля
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (password !== confirmPassword) {
        this.setCustomValidity('Паролі не співпадають');
    } else {
        this.setCustomValidity('');
    }
});
</script>

<?php include '../includes/footer.php'; ?>