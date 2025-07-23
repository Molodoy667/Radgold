<?php
require_once '../../core/config.php';
require_once '../../core/functions.php';

// Перевірка авторизації
if (!isLoggedIn()) {
    header('Location: ../../pages/auth/login.php');
    exit();
}

$userId = getUserId();
$user = getUserById($userId);

if (!$user) {
    header('Location: ../../pages/auth/login.php');
    exit();
}

$title = __('profile.my_profile');
$pageClass = 'profile-page';

// Обробка форми оновлення профілю
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_profile':
                $result = updateUserProfile($_POST);
                break;
            case 'change_password':
                $result = changeUserPassword($_POST);
                break;
            case 'upload_avatar':
                $result = uploadUserAvatar($_FILES['avatar'] ?? null);
                break;
            case 'remove_avatar':
                $result = removeUserAvatar();
                break;
        }
        
        if (isset($result)) {
            if ($result['success']) {
                $success = $result['message'];
                // Оновлюємо дані користувача
                $user = getUserById($userId);
            } else {
                $error = $result['message'];
            }
        }
    }
}

function updateUserProfile($data) {
    global $db, $userId;
    
    $firstName = trim($data['first_name'] ?? '');
    $lastName = trim($data['last_name'] ?? '');
    $username = trim($data['username'] ?? '');
    $email = trim($data['email'] ?? '');
    $phone = trim($data['phone'] ?? '');
    $bio = trim($data['bio'] ?? '');
    
    // Валідація
    if (empty($firstName) || empty($lastName) || empty($email)) {
        return ['success' => false, 'message' => 'Заповніть всі обов\'язкові поля'];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'message' => 'Некоректний email адрес'];
    }
    
    // Перевірка унікальності email та username
    $stmt = $db->prepare("SELECT id FROM users WHERE (email = ? OR username = ?) AND id != ?");
    $stmt->bind_param("ssi", $email, $username, $userId);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        return ['success' => false, 'message' => 'Email або нікнейм вже використовуються'];
    }
    
    // Оновлення
    $stmt = $db->prepare("
        UPDATE users SET 
        first_name = ?, last_name = ?, username = ?, email = ?, phone = ?, bio = ?,
        updated_at = CURRENT_TIMESTAMP
        WHERE id = ?
    ");
    $stmt->bind_param("ssssssi", $firstName, $lastName, $username, $email, $phone, $bio, $userId);
    
    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Профіль успішно оновлено'];
    } else {
        return ['success' => false, 'message' => 'Помилка оновлення профілю'];
    }
}

function changeUserPassword($data) {
    global $db, $userId, $user;
    
    $currentPassword = $data['current_password'] ?? '';
    $newPassword = $data['new_password'] ?? '';
    $confirmPassword = $data['confirm_password'] ?? '';
    
    // Валідація
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        return ['success' => false, 'message' => 'Заповніть всі поля'];
    }
    
    if (!password_verify($currentPassword, $user['password'])) {
        return ['success' => false, 'message' => 'Неправильний поточний пароль'];
    }
    
    if ($newPassword !== $confirmPassword) {
        return ['success' => false, 'message' => 'Нові паролі не співпадають'];
    }
    
    if (strlen($newPassword) < 6) {
        return ['success' => false, 'message' => 'Пароль має містити мінімум 6 символів'];
    }
    
    // Оновлення паролю
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $db->prepare("UPDATE users SET password = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->bind_param("si", $hashedPassword, $userId);
    
    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Пароль успішно змінено'];
    } else {
        return ['success' => false, 'message' => 'Помилка зміни паролю'];
    }
}

function uploadUserAvatar($file) {
    global $db, $userId, $user;
    
    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Помилка завантаження файлу'];
    }
    
    // Перевірка типу файлу
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return ['success' => false, 'message' => 'Дозволені тільки зображення (JPEG, PNG, GIF, WebP)'];
    }
    
    // Перевірка розміру файлу (максимум 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'message' => 'Розмір файлу не може перевищувати 5MB'];
    }
    
    // Створюємо директорію якщо не існує
    $uploadDir = '../../uploads/avatars/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Генеруємо унікальне ім'я файлу
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName = 'avatar_' . $userId . '_' . time() . '.' . $extension;
    $filePath = $uploadDir . $fileName;
    
    // Переміщуємо файл
    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        // Видаляємо старий аватар якщо є
        if ($user['avatar'] && file_exists('../../' . $user['avatar'])) {
            unlink('../../' . $user['avatar']);
        }
        
        // Оновлюємо БД
        $avatarPath = 'uploads/avatars/' . $fileName;
        $stmt = $db->prepare("UPDATE users SET avatar = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("si", $avatarPath, $userId);
        
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Аватар успішно завантажено'];
        } else {
            unlink($filePath);
            return ['success' => false, 'message' => 'Помилка збереження в базі даних'];
        }
    } else {
        return ['success' => false, 'message' => 'Помилка переміщення файлу'];
    }
}

function removeUserAvatar() {
    global $db, $userId, $user;
    
    if ($user['avatar'] && file_exists('../../' . $user['avatar'])) {
        unlink('../../' . $user['avatar']);
    }
    
    $stmt = $db->prepare("UPDATE users SET avatar = NULL, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->bind_param("i", $userId);
    
    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Аватар видалено'];
    } else {
        return ['success' => false, 'message' => 'Помилка видалення аватару'];
    }
}

// Функція для генерації аватару з ініціалами
function generateInitialsAvatar($firstName, $lastName) {
    $initials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
    $gradient = getSiteSetting('current_gradient', 'gradient-1');
    return [
        'initials' => $initials,
        'gradient' => $gradient
    ];
}

include '../../themes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 sidebar-profile">
            <div class="profile-sidebar">
                <div class="profile-avatar-section text-center mb-4">
                    <?php if ($user['avatar']): ?>
                        <img src="<?php echo htmlspecialchars($user['avatar']); ?>" 
                             alt="Avatar" 
                             class="profile-avatar">
                    <?php else: 
                        $avatarData = generateInitialsAvatar($user['first_name'], $user['last_name']);
                    ?>
                        <div class="profile-avatar-placeholder <?php echo $avatarData['gradient']; ?>">
                            <?php echo $avatarData['initials']; ?>
                        </div>
                    <?php endif; ?>
                    
                    <h5 class="profile-name mt-3"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h5>
                    <?php if ($user['username']): ?>
                        <p class="profile-username text-muted">@<?php echo htmlspecialchars($user['username']); ?></p>
                    <?php endif; ?>
                </div>
                
                <nav class="profile-nav">
                    <a href="#profile-info" class="nav-link active" data-tab="profile-info">
                        <i class="fas fa-user me-2"></i>Особиста інформація
                    </a>
                    <a href="#avatar-settings" class="nav-link" data-tab="avatar-settings">
                        <i class="fas fa-camera me-2"></i>Аватар
                    </a>
                    <a href="#password-settings" class="nav-link" data-tab="password-settings">
                        <i class="fas fa-lock me-2"></i>Зміна паролю
                    </a>
                    <a href="#account-settings" class="nav-link" data-tab="account-settings">
                        <i class="fas fa-cog me-2"></i>Налаштування акаунту
                    </a>
                </nav>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 main-content">
            <div class="profile-header mb-4">
                <h1><i class="fas fa-user-circle me-3"></i><?php echo $title; ?></h1>
                <p class="text-muted">Керуйте своїм профілем та налаштуваннями</p>
            </div>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Profile Information Tab -->
            <div class="tab-content active" id="profile-info">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Особиста інформація</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="row g-3">
                            <input type="hidden" name="action" value="update_profile">
                            
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">Ім'я *</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Прізвище *</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="username" class="form-label">Нікнейм</label>
                                <div class="input-group">
                                    <span class="input-group-text">@</span>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>"
                                           placeholder="your_nickname">
                                </div>
                                <div class="form-text">Унікальне ім'я для профілю (необов'язково)</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Телефон</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                                       placeholder="+380991234567">
                            </div>
                            
                            <div class="col-12">
                                <label for="bio" class="form-label">Про себе</label>
                                <textarea class="form-control" id="bio" name="bio" rows="3" 
                                          placeholder="Розкажіть трохи про себе..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Зберегти зміни
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Avatar Settings Tab -->
            <div class="tab-content" id="avatar-settings">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-camera me-2"></i>Налаштування аватару</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="current-avatar text-center mb-4">
                                    <h6>Поточний аватар</h6>
                                    <?php if ($user['avatar']): ?>
                                        <img src="<?php echo htmlspecialchars($user['avatar']); ?>" 
                                             alt="Current Avatar" 
                                             class="current-avatar-preview">
                                    <?php else: 
                                        $avatarData = generateInitialsAvatar($user['first_name'], $user['last_name']);
                                    ?>
                                        <div class="current-avatar-placeholder <?php echo $avatarData['gradient']; ?>">
                                            <?php echo $avatarData['initials']; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($user['avatar']): ?>
                                    <form method="POST" class="text-center">
                                        <input type="hidden" name="action" value="remove_avatar">
                                        <button type="submit" class="btn btn-outline-danger" 
                                                onclick="return confirm('Видалити поточний аватар?')">
                                            <i class="fas fa-trash me-2"></i>Видалити аватар
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-6">
                                <h6>Завантажити новий аватар</h6>
                                <form method="POST" enctype="multipart/form-data" id="avatarForm">
                                    <input type="hidden" name="action" value="upload_avatar">
                                    
                                    <div class="mb-3">
                                        <label for="avatar" class="form-label">Виберіть файл</label>
                                        <input type="file" class="form-control" id="avatar" name="avatar" 
                                               accept="image/*" required>
                                        <div class="form-text">
                                            Максимальний розмір: 5MB<br>
                                            Підтримувані формати: JPEG, PNG, GIF, WebP
                                        </div>
                                    </div>
                                    
                                    <div class="avatar-preview mb-3" id="avatarPreview" style="display: none;">
                                        <h6>Попередній перегляд:</h6>
                                        <img id="previewImage" class="preview-avatar" alt="Preview">
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-upload me-2"></i>Завантажити аватар
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Password Settings Tab -->
            <div class="tab-content" id="password-settings">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Зміна паролю</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="row g-3" id="passwordForm">
                            <input type="hidden" name="action" value="change_password">
                            
                            <div class="col-12">
                                <label for="current_password" class="form-label">Поточний пароль *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="current_password" 
                                           name="current_password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="toggleCurrentPassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="new_password" class="form-label">Новий пароль *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="new_password" 
                                           name="new_password" required minlength="6">
                                    <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="password-strength">
                                    <div class="strength-bar"></div>
                                    <small class="strength-text">Мінімум 6 символів</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="confirm_password" class="form-label">Підтвердіть пароль *</label>
                                <input type="password" class="form-control" id="confirm_password" 
                                       name="confirm_password" required>
                            </div>
                            
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-key me-2"></i>Змінити пароль
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Account Settings Tab -->
            <div class="tab-content" id="account-settings">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Налаштування акаунту</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="setting-item">
                                    <h6>Дата реєстрації</h6>
                                    <p class="text-muted"><?php echo date('d.m.Y', strtotime($user['created_at'])); ?></p>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="setting-item">
                                    <h6>Остання активність</h6>
                                    <p class="text-muted"><?php echo $user['updated_at'] ? date('d.m.Y H:i', strtotime($user['updated_at'])) : 'Не оновлювалось'; ?></p>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="setting-item">
                                    <h6>Статус акаунту</h6>
                                    <span class="badge bg-success">Активний</span>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="setting-item">
                                    <h6>Тип користувача</h6>
                                    <p class="text-muted"><?php echo ucfirst($user['role']); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.sidebar-profile {
    background: var(--bs-light);
    min-height: calc(100vh - 76px);
    padding: 2rem 1rem;
}

.profile-sidebar {
    position: sticky;
    top: 2rem;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid var(--bs-white);
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.profile-avatar-placeholder {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    font-weight: bold;
    color: white;
    margin: 0 auto;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.profile-name {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.profile-username {
    font-size: 0.9rem;
}

.profile-nav .nav-link {
    display: block;
    padding: 0.75rem 1rem;
    color: var(--bs-dark);
    text-decoration: none;
    border-radius: 8px;
    margin-bottom: 0.5rem;
    transition: all 0.3s ease;
}

.profile-nav .nav-link:hover,
.profile-nav .nav-link.active {
    background: var(--bs-primary);
    color: white;
}

.main-content {
    padding: 2rem;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.current-avatar-preview,
.current-avatar-placeholder {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    margin: 0 auto 1rem;
}

.current-avatar-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    font-weight: bold;
    color: white;
}

.preview-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
}

.password-strength {
    margin-top: 0.5rem;
}

.strength-bar {
    height: 4px;
    background: #e9ecef;
    border-radius: 2px;
    margin-bottom: 0.25rem;
    overflow: hidden;
}

.strength-bar::after {
    content: '';
    display: block;
    height: 100%;
    width: 0%;
    transition: width 0.3s ease;
    border-radius: 2px;
}

.strength-bar.weak::after {
    width: 25%;
    background: #dc3545;
}

.strength-bar.fair::after {
    width: 50%;
    background: #ffc107;
}

.strength-bar.good::after {
    width: 75%;
    background: #20c997;
}

.strength-bar.strong::after {
    width: 100%;
    background: #28a745;
}

.setting-item {
    padding: 1rem;
    background: var(--bs-light);
    border-radius: 8px;
}

.setting-item h6 {
    margin-bottom: 0.5rem;
    color: var(--bs-dark);
}

/* Gradient classes for avatar placeholders */
.gradient-1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.gradient-2 { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
.gradient-3 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
.gradient-4 { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
.gradient-5 { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching
    const tabLinks = document.querySelectorAll('.profile-nav .nav-link');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetTab = this.dataset.tab;
            
            // Update active link
            tabLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
            
            // Show target content
            tabContents.forEach(content => {
                content.classList.remove('active');
                if (content.id === targetTab) {
                    content.classList.add('active');
                }
            });
        });
    });
    
    // Avatar preview
    const avatarInput = document.getElementById('avatar');
    const previewContainer = document.getElementById('avatarPreview');
    const previewImage = document.getElementById('previewImage');
    
    avatarInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            previewContainer.style.display = 'none';
        }
    });
    
    // Password visibility toggles
    const toggleButtons = document.querySelectorAll('[id^="toggle"]');
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.id.replace('toggle', '').toLowerCase().replace('password', '_password');
            const targetInput = document.getElementById(targetId);
            const icon = this.querySelector('i');
            
            if (targetInput.type === 'password') {
                targetInput.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                targetInput.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });
    
    // Password strength indicator
    const newPasswordInput = document.getElementById('new_password');
    const strengthBar = document.querySelector('.strength-bar');
    const strengthText = document.querySelector('.strength-text');
    
    newPasswordInput.addEventListener('input', function() {
        const password = this.value;
        const strength = calculatePasswordStrength(password);
        
        strengthBar.className = 'strength-bar ' + strength.level;
        strengthText.textContent = strength.text;
    });
    
    function calculatePasswordStrength(password) {
        let score = 0;
        
        if (password.length >= 6) score++;
        if (password.length >= 8) score++;
        if (/[a-z]/.test(password)) score++;
        if (/[A-Z]/.test(password)) score++;
        if (/[0-9]/.test(password)) score++;
        if (/[^A-Za-z0-9]/.test(password)) score++;
        
        if (score < 2) return { level: 'weak', text: 'Слабкий пароль' };
        if (score < 4) return { level: 'fair', text: 'Середній пароль' };
        if (score < 5) return { level: 'good', text: 'Хороший пароль' };
        return { level: 'strong', text: 'Сильний пароль' };
    }
    
    // Password confirmation validation
    const confirmPasswordInput = document.getElementById('confirm_password');
    confirmPasswordInput.addEventListener('input', function() {
        const newPassword = newPasswordInput.value;
        const confirmPassword = this.value;
        
        if (confirmPassword && newPassword !== confirmPassword) {
            this.setCustomValidity('Паролі не співпадають');
        } else {
            this.setCustomValidity('');
        }
    });
});
</script>

<?php include '../../themes/footer.php'; ?>