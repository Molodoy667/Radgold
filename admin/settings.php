<?php
session_start();

// Перевірка авторизації адміна
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: index.php');
    exit();
}

require_once '../config/config.php';
require_once '../config/database.php';

// Підключення до бази даних
$database = new Database();
$db = $database->getConnection();

// Отримуємо дані адміна
$admin_query = "SELECT * FROM users WHERE id = ? AND is_admin = 1";
$admin_stmt = $db->prepare($admin_query);
$admin_stmt->execute([$_SESSION['admin_id']]);
$admin = $admin_stmt->fetch(PDO::FETCH_ASSOC);

$success_message = '';
$error_message = '';

// Обробка збереження налаштувань
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db->beginTransaction();
        
        foreach ($_POST as $key => $value) {
            if ($key !== 'submit' && $key !== 'action') {
                $clean_value = clean_input($value);
                
                $update_query = "UPDATE settings SET setting_value = ? WHERE setting_key = ?";
                $update_stmt = $db->prepare($update_query);
                $update_stmt->execute([$clean_value, $key]);
            }
        }
        
        // Обробка завантаження лого
        if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../assets/uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_info = pathinfo($_FILES['site_logo']['name']);
            $extension = strtolower($file_info['extension']);
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
            
            if (in_array($extension, $allowed_extensions) && $_FILES['site_logo']['size'] <= 2097152) {
                $filename = 'logo_' . time() . '.' . $extension;
                $target_path = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $target_path)) {
                    // Видаляємо старе лого
                    $old_logo = Settings::get('site_logo');
                    if ($old_logo && file_exists('../' . $old_logo)) {
                        unlink('../' . $old_logo);
                    }
                    
                    $logo_path = 'assets/uploads/' . $filename;
                    $update_query = "UPDATE settings SET setting_value = ? WHERE setting_key = 'site_logo'";
                    $update_stmt = $db->prepare($update_query);
                    $update_stmt->execute([$logo_path]);
                }
            }
        }
        
        // Обробка завантаження favicon
        if (isset($_FILES['site_favicon']) && $_FILES['site_favicon']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../assets/uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_info = pathinfo($_FILES['site_favicon']['name']);
            $extension = strtolower($file_info['extension']);
            $allowed_extensions = ['ico', 'png', 'jpg', 'jpeg'];
            
            if (in_array($extension, $allowed_extensions) && $_FILES['site_favicon']['size'] <= 1048576) {
                $filename = 'favicon_' . time() . '.' . $extension;
                $target_path = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['site_favicon']['tmp_name'], $target_path)) {
                    // Видаляємо старий favicon
                    $old_favicon = Settings::get('site_favicon');
                    if ($old_favicon && file_exists('../' . $old_favicon)) {
                        unlink('../' . $old_favicon);
                    }
                    
                    $favicon_path = 'assets/uploads/' . $filename;
                    $update_query = "UPDATE settings SET setting_value = ? WHERE setting_key = 'site_favicon'";
                    $update_stmt = $db->prepare($update_query);
                    $update_stmt->execute([$favicon_path]);
                }
            }
        }
        
        $db->commit();
        
        // Логування
        $log_query = "INSERT INTO admin_logs (admin_id, action, description, ip_address, user_agent) 
                     VALUES (?, 'settings_update', 'Оновлення налаштувань сайту', ?, ?)";
        $log_stmt = $db->prepare($log_query);
        $log_stmt->execute([
            $_SESSION['admin_id'],
            $_SERVER['REMOTE_ADDR'] ?? '',
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
        
        $success_message = 'Налаштування успішно збережені!';
        
        // Очищуємо кеш налаштувань
        Settings::clearCache();
        
    } catch (Exception $e) {
        $db->rollback();
        $error_message = 'Помилка збереження: ' . $e->getMessage();
        error_log("Settings update error: " . $e->getMessage());
    }
}

// Отримуємо всі налаштування
$all_settings = [];
$settings_query = "SELECT setting_key, setting_value FROM settings";
$settings_stmt = $db->prepare($settings_query);
$settings_stmt->execute();
while ($row = $settings_stmt->fetch(PDO::FETCH_ASSOC)) {
    $all_settings[$row['setting_key']] = $row['setting_value'];
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Генеральні налаштування - <?php echo Settings::get('site_name', 'Дошка Оголошень'); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Color Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@simonwep/pickr/dist/themes/classic.min.css">
    
    <style>
        <?php echo Theme::generateCSS(); ?>
        
        .admin-navbar {
            background: var(--card-bg) !important;
            border-bottom: 1px solid var(--border-color);
            box-shadow: var(--shadow);
        }
        
        .settings-container {
            padding-top: 80px;
            min-height: 100vh;
            background: var(--surface-color);
        }
        
        .settings-block {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            transition: all 0.3s ease;
        }
        
        .settings-block:hover {
            box-shadow: var(--shadow);
            transform: translateY(-2px);
        }
        
        .block-header {
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .block-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }
        
        .block-title {
            color: var(--text-color);
            margin: 0;
            font-size: 1.4rem;
            font-weight: 600;
        }
        
        .block-description {
            color: var(--text-muted);
            margin: 0;
            font-size: 0.9rem;
        }
        
        .form-control, .form-select {
            border: 2px solid var(--border-color);
            border-radius: 10px;
            transition: all 0.3s ease;
            background: var(--card-bg);
            color: var(--text-color);
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--theme-primary);
            box-shadow: 0 0 0 0.2rem rgba(240, 147, 251, 0.25);
            background: var(--card-bg);
            color: var(--text-color);
        }
        
        .form-label {
            color: var(--text-color);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .btn-save {
            background: var(--theme-gradient);
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }
        
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(240, 147, 251, 0.3);
            color: white;
        }
        
        .color-picker-preview {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            border: 2px solid var(--border-color);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .color-picker-preview:hover {
            transform: scale(1.1);
            box-shadow: var(--shadow);
        }
        
        .file-upload-preview {
            max-width: 200px;
            max-height: 100px;
            border-radius: 8px;
            border: 2px solid var(--border-color);
            margin-top: 10px;
        }
        
        .gradient-option {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            border: 3px solid transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 5px;
            position: relative;
        }
        
        .gradient-option:hover {
            transform: scale(1.1);
            border-color: var(--theme-primary);
        }
        
        .gradient-option.active {
            border-color: var(--theme-primary);
            box-shadow: 0 0 0 3px rgba(240, 147, 251, 0.3);
        }
        
        .gradient-option::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: white;
            opacity: 0;
            transition: all 0.3s ease;
        }
        
        .gradient-option.active::after {
            opacity: 1;
            background: var(--theme-primary);
        }
        
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }
        
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        
        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        
        input:checked + .slider {
            background: var(--theme-gradient);
        }
        
        input:checked + .slider:before {
            transform: translateX(26px);
        }
        
        .navbar-brand {
            font-weight: 600;
            color: var(--text-color) !important;
        }
        
        .nav-link {
            color: var(--text-color) !important;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            color: var(--theme-primary) !important;
        }
    </style>
</head>
<body>
    <!-- Admin Navbar -->
    <nav class="navbar navbar-expand-lg admin-navbar fixed-top">
        <div class="container-fluid">
            <div class="navbar-brand d-flex align-items-center">
                <a href="dashboard.php" class="btn btn-outline-primary me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <span>Генеральні налаштування</span>
            </div>
            
            <div class="navbar-brand mx-auto">
                <?php 
                $logo_path = Settings::get('site_logo', '');
                if (!empty($logo_path) && file_exists('../' . $logo_path)): 
                ?>
                    <img src="../<?php echo Settings::getLogoUrl(); ?>" alt="<?php echo htmlspecialchars(Settings::get('site_name')); ?>" style="max-height: 40px;">
                <?php else: ?>
                    <i class="fas fa-cog me-2"></i><?php echo htmlspecialchars(Settings::get('site_name', 'Дошка Оголошень')); ?>
                <?php endif; ?>
            </div>
            
            <div class="navbar-nav">
                <a href="../index.php" class="nav-link" target="_blank" title="Відвідати сайт">
                    <i class="fas fa-external-link-alt"></i>
                    <span class="d-none d-md-inline ms-1">Сайт</span>
                </a>
                <a href="logout.php" class="nav-link text-danger" title="Вихід">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="d-none d-md-inline ms-1">Вихід</span>
                </a>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="settings-container">
        <div class="container-fluid">
            <!-- Alert Messages -->
            <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data" id="settingsForm">
                <!-- Основні налаштування -->
                <div class="settings-block">
                    <div class="block-header">
                        <div class="block-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <i class="fas fa-globe"></i>
                        </div>
                        <div>
                            <h3 class="block-title">Основні налаштування</h3>
                            <p class="block-description">Базова інформація про сайт</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="site_name" class="form-label">
                                <i class="fas fa-tag me-1"></i>Назва сайту
                            </label>
                            <input type="text" class="form-control" id="site_name" name="site_name" 
                                   value="<?php echo htmlspecialchars($all_settings['site_name'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="site_url" class="form-label">
                                <i class="fas fa-link me-1"></i>URL сайту
                            </label>
                            <input type="url" class="form-control" id="site_url" name="site_url" 
                                   value="<?php echo htmlspecialchars($all_settings['site_url'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="site_description" class="form-label">
                                <i class="fas fa-align-left me-1"></i>Опис сайту
                            </label>
                            <textarea class="form-control" id="site_description" name="site_description" 
                                      rows="3"><?php echo htmlspecialchars($all_settings['site_description'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="site_language" class="form-label">
                                <i class="fas fa-language me-1"></i>Мова сайту
                            </label>
                            <select class="form-select" id="site_language" name="site_language">
                                <option value="uk" <?php echo ($all_settings['site_language'] ?? '') === 'uk' ? 'selected' : ''; ?>>Українська</option>
                                <option value="ru" <?php echo ($all_settings['site_language'] ?? '') === 'ru' ? 'selected' : ''; ?>>Русский</option>
                                <option value="en" <?php echo ($all_settings['site_language'] ?? '') === 'en' ? 'selected' : ''; ?>>English</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="site_timezone" class="form-label">
                                <i class="fas fa-clock me-1"></i>Часовий пояс
                            </label>
                            <select class="form-select" id="site_timezone" name="site_timezone">
                                <option value="Europe/Kiev" <?php echo ($all_settings['site_timezone'] ?? '') === 'Europe/Kiev' ? 'selected' : ''; ?>>Київ (UTC+2)</option>
                                <option value="Europe/Moscow" <?php echo ($all_settings['site_timezone'] ?? '') === 'Europe/Moscow' ? 'selected' : ''; ?>>Москва (UTC+3)</option>
                                <option value="UTC" <?php echo ($all_settings['site_timezone'] ?? '') === 'UTC' ? 'selected' : ''; ?>>UTC</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- SEO налаштування -->
                <div class="settings-block">
                    <div class="block-header">
                        <div class="block-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <i class="fas fa-search"></i>
                        </div>
                        <div>
                            <h3 class="block-title">SEO налаштування</h3>
                            <p class="block-description">Оптимізація для пошукових систем</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="site_title" class="form-label">
                                <i class="fas fa-heading me-1"></i>Meta Title
                            </label>
                            <input type="text" class="form-control" id="site_title" name="site_title" 
                                   value="<?php echo htmlspecialchars($all_settings['site_title'] ?? ''); ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="site_author" class="form-label">
                                <i class="fas fa-user-edit me-1"></i>Автор
                            </label>
                            <input type="text" class="form-control" id="site_author" name="site_author" 
                                   value="<?php echo htmlspecialchars($all_settings['site_author'] ?? ''); ?>">
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="site_keywords" class="form-label">
                                <i class="fas fa-tags me-1"></i>Ключові слова (через кому)
                            </label>
                            <textarea class="form-control" id="site_keywords" name="site_keywords" 
                                      rows="2" placeholder="оголошення, дошка, купити, продати"><?php echo htmlspecialchars($all_settings['site_keywords'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="google_analytics" class="form-label">
                                <i class="fab fa-google me-1"></i>Google Analytics ID
                            </label>
                            <input type="text" class="form-control" id="google_analytics" name="google_analytics" 
                                   value="<?php echo htmlspecialchars($all_settings['google_analytics'] ?? ''); ?>" 
                                   placeholder="G-XXXXXXXXXX">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="analytics_yandex" class="form-label">
                                <i class="fab fa-yandex me-1"></i>Yandex Metrica ID
                            </label>
                            <input type="text" class="form-control" id="analytics_yandex" name="analytics_yandex" 
                                   value="<?php echo htmlspecialchars($all_settings['analytics_yandex'] ?? ''); ?>" 
                                   placeholder="12345678">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="google_site_verification" class="form-label">
                                <i class="fas fa-shield-alt me-1"></i>Google Site Verification
                            </label>
                            <input type="text" class="form-control" id="google_site_verification" name="google_site_verification" 
                                   value="<?php echo htmlspecialchars($all_settings['google_site_verification'] ?? ''); ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="yandex_verification" class="form-label">
                                <i class="fas fa-check-circle me-1"></i>Yandex Verification
                            </label>
                            <input type="text" class="form-control" id="yandex_verification" name="yandex_verification" 
                                   value="<?php echo htmlspecialchars($all_settings['yandex_verification'] ?? ''); ?>">
                        </div>
                    </div>
                </div>
                
                <!-- Логотип та Брендинг -->
                <div class="settings-block">
                    <div class="block-header">
                        <div class="block-icon" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                            <i class="fas fa-image"></i>
                        </div>
                        <div>
                            <h3 class="block-title">Логотип та Брендинг</h3>
                            <p class="block-description">Візуальна ідентифікація сайту</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="site_logo" class="form-label">
                                <i class="fas fa-star me-1"></i>Логотип сайту
                            </label>
                            <input type="file" class="form-control" id="site_logo" name="site_logo" 
                                   accept="image/*" onchange="previewImage(this, 'logoPreview')">
                            <small class="text-muted">SVG, PNG, JPG до 2MB</small>
                            <?php if (!empty($all_settings['site_logo'])): ?>
                                <img src="../<?php echo htmlspecialchars($all_settings['site_logo']); ?>" 
                                     alt="Логотип" class="file-upload-preview" id="logoPreview">
                            <?php else: ?>
                                <div id="logoPreview" class="file-upload-preview d-none"></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="site_favicon" class="form-label">
                                <i class="fas fa-bookmark me-1"></i>Favicon
                            </label>
                            <input type="file" class="form-control" id="site_favicon" name="site_favicon" 
                                   accept=".ico,.png,.jpg,.jpeg" onchange="previewImage(this, 'faviconPreview')">
                            <small class="text-muted">ICO, PNG до 1MB</small>
                            <?php if (!empty($all_settings['site_favicon'])): ?>
                                <img src="../<?php echo htmlspecialchars($all_settings['site_favicon']); ?>" 
                                     alt="Favicon" class="file-upload-preview" id="faviconPreview">
                            <?php else: ?>
                                <div id="faviconPreview" class="file-upload-preview d-none"></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Тема оформлення -->
                <div class="settings-block">
                    <div class="block-header">
                        <div class="block-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-palette"></i>
                        </div>
                        <div>
                            <h3 class="block-title">Тема оформлення</h3>
                            <p class="block-description">Кольори та стиль сайту</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-fill-drip me-1"></i>Градієнт за замовчуванням
                            </label>
                            <div class="d-flex flex-wrap">
                                <?php foreach (Theme::getGradients() as $gradient_key => $gradient_data): ?>
                                    <input type="radio" name="default_theme_gradient" value="<?php echo $gradient_key; ?>" 
                                           id="gradient_<?php echo $gradient_key; ?>" class="d-none"
                                           <?php echo ($all_settings['default_theme_gradient'] ?? 'gradient-2') === $gradient_key ? 'checked' : ''; ?>>
                                    <label for="gradient_<?php echo $gradient_key; ?>" 
                                           class="gradient-option <?php echo ($all_settings['default_theme_gradient'] ?? 'gradient-2') === $gradient_key ? 'active' : ''; ?>"
                                           style="background: linear-gradient(135deg, <?php echo $gradient_data[0]; ?> 0%, <?php echo $gradient_data[1]; ?> 100%);"
                                           title="<?php echo $gradient_data[2]; ?>"></label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-moon me-1"></i>Темний режим за замовчуванням
                            </label>
                            <div class="d-flex align-items-center">
                                <label class="switch">
                                    <input type="checkbox" name="default_dark_mode" value="1" 
                                           <?php echo ($all_settings['default_dark_mode'] ?? '0') === '1' ? 'checked' : ''; ?>>
                                    <span class="slider"></span>
                                </label>
                                <span class="ms-2">Увімкнути темний режим</span>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-toggle-on me-1"></i>Перемикач тем
                            </label>
                            <div class="d-flex align-items-center">
                                <label class="switch">
                                    <input type="checkbox" name="enable_theme_switcher" value="1" 
                                           <?php echo ($all_settings['enable_theme_switcher'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                    <span class="slider"></span>
                                </label>
                                <span class="ms-2">Дозволити користувачам змінювати тему</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Контактна інформація -->
                <div class="settings-block">
                    <div class="block-header">
                        <div class="block-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <i class="fas fa-address-book"></i>
                        </div>
                        <div>
                            <h3 class="block-title">Контактна інформація</h3>
                            <p class="block-description">Контакти та соціальні мережі</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="contact_email" class="form-label">
                                <i class="fas fa-envelope me-1"></i>Email для зв'язку
                            </label>
                            <input type="email" class="form-control" id="contact_email" name="contact_email" 
                                   value="<?php echo htmlspecialchars($all_settings['contact_email'] ?? ''); ?>">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="contact_phone" class="form-label">
                                <i class="fas fa-phone me-1"></i>Телефон
                            </label>
                            <input type="tel" class="form-control" id="contact_phone" name="contact_phone" 
                                   value="<?php echo htmlspecialchars($all_settings['contact_phone'] ?? ''); ?>">
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="contact_address" class="form-label">
                                <i class="fas fa-map-marker-alt me-1"></i>Адреса
                            </label>
                            <textarea class="form-control" id="contact_address" name="contact_address" 
                                      rows="2"><?php echo htmlspecialchars($all_settings['contact_address'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="social_facebook" class="form-label">
                                <i class="fab fa-facebook me-1"></i>Facebook
                            </label>
                            <input type="url" class="form-control" id="social_facebook" name="social_facebook" 
                                   value="<?php echo htmlspecialchars($all_settings['social_facebook'] ?? ''); ?>" 
                                   placeholder="https://facebook.com/yourpage">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="social_twitter" class="form-label">
                                <i class="fab fa-twitter me-1"></i>Twitter
                            </label>
                            <input type="url" class="form-control" id="social_twitter" name="social_twitter" 
                                   value="<?php echo htmlspecialchars($all_settings['social_twitter'] ?? ''); ?>" 
                                   placeholder="https://twitter.com/youraccount">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="social_instagram" class="form-label">
                                <i class="fab fa-instagram me-1"></i>Instagram
                            </label>
                            <input type="url" class="form-control" id="social_instagram" name="social_instagram" 
                                   value="<?php echo htmlspecialchars($all_settings['social_instagram'] ?? ''); ?>" 
                                   placeholder="https://instagram.com/youraccount">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="social_youtube" class="form-label">
                                <i class="fab fa-youtube me-1"></i>YouTube
                            </label>
                            <input type="url" class="form-control" id="social_youtube" name="social_youtube" 
                                   value="<?php echo htmlspecialchars($all_settings['social_youtube'] ?? ''); ?>" 
                                   placeholder="https://youtube.com/yourchannel">
                        </div>
                    </div>
                </div>
                
                <!-- Функціональні налаштування -->
                <div class="settings-block">
                    <div class="block-header">
                        <div class="block-icon" style="background: linear-gradient(135deg, #96fbc4 0%, #f9f047 100%);">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <div>
                            <h3 class="block-title">Функціональні налаштування</h3>
                            <p class="block-description">Включення/відключення функцій</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-comments me-1"></i>Коментарі
                            </label>
                            <div class="d-flex align-items-center">
                                <label class="switch">
                                    <input type="checkbox" name="enable_comments" value="1" 
                                           <?php echo ($all_settings['enable_comments'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                    <span class="slider"></span>
                                </label>
                                <span class="ms-2">Дозволити коментарі</span>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-star me-1"></i>Рейтинги
                            </label>
                            <div class="d-flex align-items-center">
                                <label class="switch">
                                    <input type="checkbox" name="enable_ratings" value="1" 
                                           <?php echo ($all_settings['enable_ratings'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                    <span class="slider"></span>
                                </label>
                                <span class="ms-2">Система рейтингів</span>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-heart me-1"></i>Обране
                            </label>
                            <div class="d-flex align-items-center">
                                <label class="switch">
                                    <input type="checkbox" name="enable_favorites" value="1" 
                                           <?php echo ($all_settings['enable_favorites'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                    <span class="slider"></span>
                                </label>
                                <span class="ms-2">Додавання в обране</span>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-share me-1"></i>Поширення
                            </label>
                            <div class="d-flex align-items-center">
                                <label class="switch">
                                    <input type="checkbox" name="enable_sharing" value="1" 
                                           <?php echo ($all_settings['enable_sharing'] ?? '1') === '1' ? 'checked' : ''; ?>>
                                    <span class="slider"></span>
                                </label>
                                <span class="ms-2">Кнопки поширення</span>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="max_login_attempts" class="form-label">
                                <i class="fas fa-shield-alt me-1"></i>Макс. спроб входу
                            </label>
                            <input type="number" class="form-control" id="max_login_attempts" name="max_login_attempts" 
                                   value="<?php echo htmlspecialchars($all_settings['max_login_attempts'] ?? '5'); ?>" 
                                   min="1" max="20">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="session_lifetime" class="form-label">
                                <i class="fas fa-clock me-1"></i>Час життя сесії (хвилин)
                            </label>
                            <input type="number" class="form-control" id="session_lifetime" name="session_lifetime" 
                                   value="<?php echo htmlspecialchars($all_settings['session_lifetime'] ?? '60'); ?>" 
                                   min="15" max="480">
                        </div>
                    </div>
                </div>
                
                <!-- Розширені налаштування -->
                <div class="settings-block">
                    <div class="block-header">
                        <div class="block-icon" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                            <i class="fas fa-tools"></i>
                        </div>
                        <div>
                            <h3 class="block-title">Розширені налаштування</h3>
                            <p class="block-description">Технічні та додаткові параметри</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="maintenance_message" class="form-label">
                                <i class="fas fa-wrench me-1"></i>Повідомлення про технічні роботи
                            </label>
                            <textarea class="form-control" id="maintenance_message" name="maintenance_message" 
                                      rows="3" placeholder="Сайт тимчасово недоступний через технічні роботи..."><?php echo htmlspecialchars($all_settings['maintenance_message'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-bug me-1"></i>Режим відлагодження
                            </label>
                            <div class="d-flex align-items-center">
                                <label class="switch">
                                    <input type="checkbox" name="debug_mode" value="1" 
                                           <?php echo ($all_settings['debug_mode'] ?? '0') === '1' ? 'checked' : ''; ?>>
                                    <span class="slider"></span>
                                </label>
                                <span class="ms-2">Показувати помилки</span>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="fas fa-save me-1"></i>Автоматичні резервні копії
                            </label>
                            <div class="d-flex align-items-center">
                                <label class="switch">
                                    <input type="checkbox" name="backup_enabled" value="1" 
                                           <?php echo ($all_settings['backup_enabled'] ?? '0') === '1' ? 'checked' : ''; ?>>
                                    <span class="slider"></span>
                                </label>
                                <span class="ms-2">Створювати backup бази даних</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Кнопка збереження -->
                <div class="text-center mb-4">
                    <button type="submit" class="btn btn-save btn-lg px-5">
                        <i class="fas fa-save me-2"></i>Зберегти всі налаштування
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Попередній перегляд зображень
        function previewImage(input, previewId) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById(previewId);
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        // Обробка вибору градієнтів
        document.querySelectorAll('input[name="default_theme_gradient"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.gradient-option').forEach(option => {
                    option.classList.remove('active');
                });
                document.querySelector(`label[for="${this.id}"]`).classList.add('active');
            });
        });
        
        // Валідація форми
        document.getElementById('settingsForm').addEventListener('submit', function(e) {
            const requiredFields = ['site_name', 'site_url'];
            let hasErrors = false;
            
            requiredFields.forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    hasErrors = true;
                } else {
                    field.classList.remove('is-invalid');
                }
            });
            
            if (hasErrors) {
                e.preventDefault();
                alert('Будь ласка, заповніть всі обов\'язкові поля');
            }
        });
        
        // Автозбереження (кожні 5 хвилин)
        setInterval(function() {
            const formData = new FormData(document.getElementById('settingsForm'));
            
            fetch('ajax/autosave.php', {
                method: 'POST',
                body: formData
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    console.log('Автозбереження виконано');
                }
            }).catch(error => {
                console.error('Помилка автозбереження:', error);
            });
        }, 300000); // 5 хвилин
        
        // Підсвічування змінених полів
        document.querySelectorAll('input, textarea, select').forEach(field => {
            const originalValue = field.value;
            field.addEventListener('input', function() {
                if (this.value !== originalValue) {
                    this.style.borderColor = '#f093fb';
                } else {
                    this.style.borderColor = '';
                }
            });
        });
    </script>
</body>
</html>