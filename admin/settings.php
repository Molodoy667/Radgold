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
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .settings-block:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transform: translateY(-3px);
        }
        
        .settings-block.expanded {
            cursor: default;
            transform: none;
        }
        
        .block-header {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .block-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
            background: var(--theme-gradient);
        }
        
        .block-info h5 {
            color: var(--text-color);
            margin: 0;
            font-weight: 600;
        }
        
        .block-info p {
            color: var(--text-muted);
            margin: 0;
            font-size: 0.9rem;
        }
        
        .block-chevron {
            margin-left: auto;
            color: var(--text-muted);
            transition: transform 0.3s ease;
        }
        
        .settings-block.expanded .block-chevron {
            transform: rotate(180deg);
        }
        
        .block-content {
            display: none;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }
        
        .settings-block.expanded .block-content {
            display: block;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--text-color);
            margin-bottom: 0.5rem;
        }
        
        .form-control, .form-select {
            border: 1px solid var(--border-color);
            background: var(--surface-color);
            color: var(--text-color);
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--theme-primary);
            box-shadow: 0 0 0 0.2rem rgba(var(--theme-primary-rgb), 0.25);
        }
        
        .btn-save {
            background: var(--theme-gradient);
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .btn-back {
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            padding: 0.75rem 2rem;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        
        .btn-back:hover {
            background: var(--theme-primary);
            color: white;
            transform: translateY(-2px);
        }
        
        .file-preview {
            max-width: 200px;
            max-height: 100px;
            border-radius: 8px;
            margin-top: 0.5rem;
        }
        
        /* Градієнтна сітка */
        .gradient-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .gradient-option {
            text-align: center;
        }
        
        .gradient-option input[type="radio"] {
            display: none;
        }
        
        .gradient-preview {
            display: block;
            width: 100%;
            height: 80px;
            border-radius: 12px;
            border: 3px solid transparent;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .gradient-preview:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        
        .gradient-option input[type="radio"]:checked + .gradient-preview {
            border-color: white;
            box-shadow: 0 0 0 3px var(--theme-primary);
            transform: scale(1.05);
        }
        
        .gradient-check {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 1.5rem;
            opacity: 0;
            transition: opacity 0.3s ease;
            text-shadow: 0 0 10px rgba(0,0,0,0.8);
        }
        
        .gradient-option input[type="radio"]:checked + .gradient-preview .gradient-check {
            opacity: 1;
        }
        
        .gradient-name {
            display: block;
            margin-top: 0.5rem;
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        /* Стилі технічного обслуговування */
        .maintenance-icon {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24) !important;
            width: 60px !important;
            height: 60px !important;
            border-radius: 15px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 1.8rem !important;
            color: white !important;
        }
        
        .maintenance-toggle:checked {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
        }
        
        .maintenance-label {
            color: var(--text-color);
            font-weight: 600;
        }
        
        .maintenance-settings {
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 1rem;
        }
        
        .maintenance-toggle:checked ~ .maintenance-label {
            color: #dc3545;
        }
        
        /* Большие градиентные переключатели */
        .form-check-input {
            width: 2.5rem !important;
            height: 1.25rem !important;
            border-radius: 1rem !important;
            border: 2px solid var(--border-color) !important;
            background: var(--surface-color) !important;
            transition: all 0.3s ease !important;
        }
        
        .form-check-input:checked {
            background: var(--theme-gradient) !important;
            border-color: transparent !important;
            box-shadow: 0 0 0 0.2rem rgba(var(--theme-primary-rgb), 0.25) !important;
        }
        
        .form-check-input:focus {
            border-color: var(--theme-primary) !important;
            box-shadow: 0 0 0 0.2rem rgba(var(--theme-primary-rgb), 0.25) !important;
        }
        
        .form-check-label {
            font-weight: 500 !important;
            color: var(--text-color) !important;
            margin-left: 0.5rem !important;
            cursor: pointer !important;
        }
        
        .form-check {
            margin-bottom: 1rem !important;
            display: flex !important;
            align-items: center !important;
        }
        
        /* Переключатель технического обслуживания */
        .maintenance-toggle {
            width: 3rem !important;
            height: 1.5rem !important;
        }
        
        .maintenance-toggle:checked {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24) !important;
        }
        
        /* Уникальные градиенты для каждого блока настроек */
        .general-icon {
            background: linear-gradient(135deg, #667eea, #764ba2) !important;
        }
        
        .seo-icon {
            background: linear-gradient(135deg, #4facfe, #00f2fe) !important;
        }
        
        .branding-icon {
            background: linear-gradient(135deg, #43e97b, #38f9d7) !important;
        }
        
        .theme-icon {
            background: linear-gradient(135deg, #fa709a, #fee140) !important;
        }
        
        .maintenance-icon {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24) !important;
        }
        
        .functionality-icon {
            background: linear-gradient(135deg, #a8edea, #fed6e3) !important;
        }
        
        /* Hover эффекты для иконок */
        .settings-block:hover .block-icon {
            transform: scale(1.1);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        
        /* Анімація завантаження */
        .loading {
            position: relative;
            pointer-events: none;
        }
        
        .loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 15px;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            right: -350px;
            width: 350px;
            height: 100vh;
            background: var(--card-bg);
            border-left: 1px solid var(--border-color);
            box-shadow: -5px 0 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            z-index: 1050;
            overflow-y: auto;
        }
        
        .sidebar.active {
            right: 0;
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            background: var(--theme-gradient);
            color: white;
            display: flex;
            justify-content: between;
            align-items: center;
        }
        
        .close-sidebar {
            background: none;
            border: none;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 5px;
            transition: background 0.3s ease;
        }
        
        .close-sidebar:hover {
            background: rgba(255,255,255,0.2);
        }
        
        .sidebar-menu {
            padding: 1rem 0;
        }
        
        .sidebar-menu .menu-item {
            display: block;
            padding: 1rem 1.5rem;
            color: var(--text-color);
            text-decoration: none;
            border-bottom: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }
        
        .sidebar-menu .menu-item:hover {
            background: var(--surface-color);
            color: var(--theme-primary);
            padding-left: 2rem;
        }
        
        .sidebar-menu .menu-item.active {
            background: var(--theme-primary);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg admin-navbar fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
                <i class="fas fa-tachometer-alt me-2"></i>
                Адмін панель
            </a>
            
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                        <?php if (!empty($admin['avatar'])): ?>
                            <img src="../<?php echo htmlspecialchars($admin['avatar']); ?>" alt="Avatar" 
                                 class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                        <?php else: ?>
                            <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                 style="width: 32px; height: 32px; font-size: 14px; color: white;">
                                <?php echo strtoupper(substr($admin['username'], 0, 2)); ?>
                            </div>
                        <?php endif; ?>
                        <?php echo htmlspecialchars($admin['username']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="../index.php"><i class="fas fa-home me-2"></i>На сайт</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Вийти</a></li>
                    </ul>
                </div>
                
                <button class="btn btn-outline-primary ms-2" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Sidebar Menu -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div>
                <h5 class="mb-0">
                    <i class="fas fa-cogs me-2"></i>Налаштування
                </h5>
                <small>Адміністрування системи</small>
            </div>
            <button class="close-sidebar" onclick="toggleSidebar()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="sidebar-menu">
            <a href="dashboard.php" class="menu-item">
                <i class="fas fa-tachometer-alt me-2"></i>Дашборд
            </a>
            <a href="settings.php" class="menu-item active">
                <i class="fas fa-cog me-2"></i>Генеральні налаштування
            </a>
            <a href="categories.php" class="menu-item">
                <i class="fas fa-list me-2"></i>Категорії
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="settings-container">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-xl-10">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="mb-1">
                                <i class="fas fa-cogs me-2 text-primary"></i>
                                Генеральні налаштування
                            </h2>
                            <p class="text-muted mb-0">Налаштування загальних параметрів сайту</p>
                        </div>
                        <a href="dashboard.php" class="btn-back">
                            <i class="fas fa-arrow-left me-2"></i>До дашборду
                        </a>
                    </div>

                    <!-- Основні налаштування -->
                    <div class="settings-block" onclick="toggleBlock(this)">
                        <div class="block-header">
                            <div class="block-icon general-icon">
                                <i class="fas fa-globe"></i>
                            </div>
                            <div class="block-info">
                                <h5>Основні налаштування</h5>
                                <p>Назва сайту, опис, мова та інші базові параметри</p>
                            </div>
                            <i class="fas fa-chevron-down block-chevron"></i>
                        </div>
                        <div class="block-content">
                            <form id="generalForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Назва сайту</label>
                                            <input type="text" class="form-control" name="site_name" 
                                                   value="<?php echo htmlspecialchars($all_settings['site_name'] ?? ''); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Мова сайту</label>
                                            <select class="form-select" name="site_language">
                                                <option value="uk" <?php echo ($all_settings['site_language'] ?? '') === 'uk' ? 'selected' : ''; ?>>Українська</option>
                                                <option value="ru" <?php echo ($all_settings['site_language'] ?? '') === 'ru' ? 'selected' : ''; ?>>Русский</option>
                                                <option value="en" <?php echo ($all_settings['site_language'] ?? '') === 'en' ? 'selected' : ''; ?>>English</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Опис сайту</label>
                                    <textarea class="form-control" name="site_description" rows="3"><?php echo htmlspecialchars($all_settings['site_description'] ?? ''); ?></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Адреса електронної пошти</label>
                                            <input type="email" class="form-control" name="site_email" 
                                                   value="<?php echo htmlspecialchars($all_settings['site_email'] ?? ''); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Телефон</label>
                                            <input type="text" class="form-control" name="site_phone" 
                                                   value="<?php echo htmlspecialchars($all_settings['site_phone'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn-save">
                                    <i class="fas fa-save me-2"></i>Зберегти зміни
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- SEO налаштування -->
                    <div class="settings-block" onclick="toggleBlock(this)">
                        <div class="block-header">
                            <div class="block-icon seo-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <div class="block-info">
                                <h5>SEO налаштування</h5>
                                <p>Мета-теги, ключові слова та налаштування для пошукових систем</p>
                            </div>
                            <i class="fas fa-chevron-down block-chevron"></i>
                        </div>
                        <div class="block-content">
                            <form id="seoForm">
                                <div class="form-group">
                                    <label class="form-label">Мета-заголовок</label>
                                    <input type="text" class="form-control" name="meta_title" 
                                           value="<?php echo htmlspecialchars($all_settings['meta_title'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Мета-опис</label>
                                    <textarea class="form-control" name="meta_description" rows="3"><?php echo htmlspecialchars($all_settings['meta_description'] ?? ''); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Ключові слова</label>
                                    <input type="text" class="form-control" name="meta_keywords" 
                                           value="<?php echo htmlspecialchars($all_settings['meta_keywords'] ?? ''); ?>"
                                           placeholder="ключове слово 1, ключове слово 2">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Google Analytics код</label>
                                    <textarea class="form-control" name="analytics_code" rows="4" placeholder="Вставте код Google Analytics"><?php echo htmlspecialchars($all_settings['analytics_code'] ?? ''); ?></textarea>
                                </div>
                                <button type="submit" class="btn-save">
                                    <i class="fas fa-save me-2"></i>Зберегти зміни
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Брендинг -->
                    <div class="settings-block" onclick="toggleBlock(this)">
                        <div class="block-header">
                            <div class="block-icon branding-icon">
                                <i class="fas fa-palette"></i>
                            </div>
                            <div class="block-info">
                                <h5>Брендинг</h5>
                                <p>Логотип, фавікон та візуальне оформлення</p>
                            </div>
                            <i class="fas fa-chevron-down block-chevron"></i>
                        </div>
                        <div class="block-content">
                            <form id="brandingForm" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Логотип сайту</label>
                                            <input type="file" class="form-control" name="site_logo" accept="image/*">
                                            <?php if (!empty($all_settings['site_logo'])): ?>
                                                <img src="../<?php echo htmlspecialchars($all_settings['site_logo']); ?>" 
                                                     alt="Logo" class="file-preview">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Фавікон</label>
                                            <input type="file" class="form-control" name="site_favicon" accept=".ico,.png,.jpg,.jpeg">
                                            <?php if (!empty($all_settings['site_favicon'])): ?>
                                                <img src="../<?php echo htmlspecialchars($all_settings['site_favicon']); ?>" 
                                                     alt="Favicon" class="file-preview" style="max-width: 32px; max-height: 32px;">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn-save">
                                    <i class="fas fa-save me-2"></i>Зберегти зміни
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Оформлення та теми -->
                    <div class="settings-block" onclick="toggleBlock(this)">
                        <div class="block-header">
                            <div class="block-icon theme-icon">
                                <i class="fas fa-paint-brush"></i>
                            </div>
                            <div class="block-info">
                                <h5>Оформлення та теми</h5>
                                <p>Вибір градієнтів, кольорової схеми та стилю сайту</p>
                            </div>
                            <i class="fas fa-chevron-down block-chevron"></i>
                        </div>
                        <div class="block-content">
                            <form id="themeForm">
                                <div class="form-group mb-4">
                                    <label class="form-label">
                                        <i class="fas fa-palette me-2"></i>Градієнт за замовчуванням
                                    </label>
                                    <div class="gradient-grid">
                                        <?php 
                                        $gradients = Theme::getGradients();
                                        $current_gradient = $all_settings['default_theme_gradient'] ?? 'gradient-2';
                                        foreach ($gradients as $gradient_id => $gradient_data): 
                                        ?>
                                            <div class="gradient-option" data-gradient="<?php echo $gradient_id; ?>">
                                                <input type="radio" 
                                                       name="default_theme_gradient" 
                                                       value="<?php echo $gradient_id; ?>" 
                                                       id="gradient_<?php echo $gradient_id; ?>"
                                                       <?php echo $current_gradient === $gradient_id ? 'checked' : ''; ?>>
                                                <label for="gradient_<?php echo $gradient_id; ?>" 
                                                       class="gradient-preview"
                                                       style="background: linear-gradient(135deg, <?php echo $gradient_data[0]; ?>, <?php echo $gradient_data[1]; ?>);"
                                                       title="<?php echo $gradient_data[2]; ?>">
                                                    <i class="fas fa-check gradient-check"></i>
                                                </label>
                                                <small class="gradient-name"><?php echo $gradient_data[2]; ?></small>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="enable_theme_switcher" 
                                                   <?php echo ($all_settings['enable_theme_switcher'] ?? '1') ? 'checked' : ''; ?>>
                                            <label class="form-check-label">Дозволити зміну теми користувачам</label>
                                        </div>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="default_dark_mode" 
                                                   <?php echo ($all_settings['default_dark_mode'] ?? '0') ? 'checked' : ''; ?>>
                                            <label class="form-check-label">Темна тема за замовчуванням</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Кастомний CSS</label>
                                            <textarea class="form-control" name="custom_css" rows="4" 
                                                      placeholder="/* Додайте свій CSS код тут */"><?php echo htmlspecialchars($all_settings['custom_css'] ?? ''); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn-save">
                                    <i class="fas fa-save me-2"></i>Зберегти налаштування теми
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Технічне обслуговування -->
                    <div class="settings-block" onclick="toggleBlock(this)">
                        <div class="block-header">
                            <div class="block-icon maintenance-icon">
                                <i class="fas fa-tools"></i>
                            </div>
                            <div class="block-info">
                                <h5>Технічне обслуговування</h5>
                                <p>Закриття сайту для обслуговування та налагодження</p>
                            </div>
                            <i class="fas fa-chevron-down block-chevron"></i>
                        </div>
                        <div class="block-content">
                            <form id="maintenanceForm">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Увага!</strong> При включенні режиму обслуговування сайт буде недоступний для звичайних користувачів. 
                                    Доступ матимуть тільки адміністратори.
                                </div>
                                
                                <div class="form-check form-switch mb-4">
                                    <input class="form-check-input maintenance-toggle" type="checkbox" name="maintenance_mode" 
                                           id="maintenanceMode" <?php echo ($all_settings['maintenance_mode'] ?? '0') ? 'checked' : ''; ?>>
                                    <label class="form-check-label maintenance-label" for="maintenanceMode">
                                        <strong>Увімкнути режим технічного обслуговування</strong>
                                    </label>
                                </div>
                                
                                <div class="maintenance-settings" style="<?php echo ($all_settings['maintenance_mode'] ?? '0') ? '' : 'display: none;'; ?>">
                                    <div class="form-group mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-heading me-2"></i>Заголовок сторінки обслуговування
                                        </label>
                                        <input type="text" class="form-control" name="maintenance_title" 
                                               value="<?php echo htmlspecialchars($all_settings['maintenance_title'] ?? 'Сайт на технічному обслуговуванні'); ?>"
                                               placeholder="Сайт на технічному обслуговуванні">
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-comment-alt me-2"></i>Повідомлення для користувачів
                                        </label>
                                        <textarea class="form-control" name="maintenance_message" rows="4" 
                                                  placeholder="Наразі ми проводимо технічні роботи для покращення роботи сайту..."><?php echo htmlspecialchars($all_settings['maintenance_message'] ?? 'Наразі ми проводимо технічні роботи для покращення роботи сайту. Вибачте за тимчасові незручності. Сайт буде доступний найближчим часом.'); ?></textarea>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="form-label">
                                                    <i class="fas fa-clock me-2"></i>Очікуваний час завершення
                                                </label>
                                                <input type="datetime-local" class="form-control" name="maintenance_end_time" 
                                                       value="<?php echo $all_settings['maintenance_end_time'] ?? ''; ?>">
                                                <small class="form-text text-muted">Необов'язково. Буде показано користувачам.</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="form-label">
                                                    <i class="fas fa-envelope me-2"></i>Контактний email
                                                </label>
                                                <input type="email" class="form-control" name="maintenance_contact_email" 
                                                       value="<?php echo htmlspecialchars($all_settings['maintenance_contact_email'] ?? $all_settings['site_email'] ?? ''); ?>"
                                                       placeholder="admin@example.com">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="maintenance_show_progress" 
                                               <?php echo ($all_settings['maintenance_show_progress'] ?? '1') ? 'checked' : ''; ?>>
                                        <label class="form-check-label">Показати анімацію прогресу</label>
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-code me-2"></i>Додатковий HTML/CSS для сторінки обслуговування
                                        </label>
                                        <textarea class="form-control" name="maintenance_custom_html" rows="3" 
                                                  placeholder="<!-- Додайте свій HTML код -->"><?php echo htmlspecialchars($all_settings['maintenance_custom_html'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn-save">
                                    <i class="fas fa-save me-2"></i>Зберегти налаштування обслуговування
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Функціональність -->
                    <div class="settings-block" onclick="toggleBlock(this)">
                        <div class="block-header">
                            <div class="block-icon functionality-icon">
                                <i class="fas fa-cogs"></i>
                            </div>
                            <div class="block-info">
                                <h5>Функціональність</h5>
                                <p>Увімкнення/вимкнення функцій сайту</p>
                            </div>
                            <i class="fas fa-chevron-down block-chevron"></i>
                        </div>
                        <div class="block-content">
                            <form id="functionalityForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="enable_registration" 
                                                   <?php echo ($all_settings['enable_registration'] ?? '1') ? 'checked' : ''; ?>>
                                            <label class="form-check-label">Дозволити реєстрацію</label>
                                        </div>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="enable_comments" 
                                                   <?php echo ($all_settings['enable_comments'] ?? '1') ? 'checked' : ''; ?>>
                                            <label class="form-check-label">Дозволити коментарі</label>
                                        </div>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="enable_search" 
                                                   <?php echo ($all_settings['enable_search'] ?? '1') ? 'checked' : ''; ?>>
                                            <label class="form-check-label">Пошук оголошень</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="enable_favorites" 
                                                   <?php echo ($all_settings['enable_favorites'] ?? '1') ? 'checked' : ''; ?>>
                                            <label class="form-check-label">Улюблені оголошення</label>
                                        </div>
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" name="moderation_required" 
                                                   <?php echo ($all_settings['moderation_required'] ?? '0') ? 'checked' : ''; ?>>
                                            <label class="form-check-label">Модерація оголошень</label>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn-save">
                                    <i class="fas fa-save me-2"></i>Зберегти зміни
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100;">
        <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-check-circle me-2"></i>
                    <span id="successMessage">Зміни успішно збережені!</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
        
        <div id="errorToast" class="toast align-items-center text-white bg-danger border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <span id="errorMessage">Помилка збереження!</span>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Функція для перемикання бокової панелі
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
        }
        
        // Функція для перемикання блоків налаштувань
        function toggleBlock(block) {
            // Не перемикати якщо клікнули на форму
            if (event.target.closest('.block-content')) {
                return;
            }
            
            block.classList.toggle('expanded');
        }
        
        // Обробка форм
        document.addEventListener('DOMContentLoaded', function() {
            const forms = ['generalForm', 'seoForm', 'brandingForm', 'functionalityForm'];
            
            forms.forEach(formId => {
                const form = document.getElementById(formId);
                if (form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        saveSettings(this, formId);
                    });
                }
            });
        });
        
        // Функція збереження налаштувань
        function saveSettings(form, formType) {
            const formData = new FormData(form);
            formData.append('form_type', formType);
            
            // Показати індикатор завантаження
            const submitBtn = form.querySelector('.btn-save');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Збереження...';
            submitBtn.disabled = true;
            
            fetch('ajax/save_settings.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', data.message || 'Зміни успішно збережені!');
                    
                    // Перенаправлення на дашборд через 2 секунди
                    setTimeout(() => {
                        window.location.href = 'dashboard.php';
                    }, 2000);
                } else {
                    showToast('error', data.message || 'Помилка збереження!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Помилка з\'єднання з сервером!');
            })
            .finally(() => {
                // Повернути кнопку в початковий стан
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }
        
        // Функція збереження налаштувань для нових форм
        function saveSettingsCustom(form, action, successMessage) {
            const formData = new FormData(form);
            formData.append('action', action);
            
            // Відладка - виводимо дані форми
            console.log('Form action:', action);
            console.log('Form data:');
            for (let [key, value] of formData.entries()) {
                console.log(key, value);
            }
            
            // Показати індикатор завантаження
            const submitBtn = form.querySelector('.btn-save');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Збереження...';
            submitBtn.disabled = true;
            
            fetch('ajax/save_settings.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('success', data.message || successMessage);
                    
                    // Для теми - перезавантажити сторінку щоб побачити зміни
                    if (action === 'save_theme') {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    }
                    // Для технічного обслуговування - перенаправити на дашборд
                    else if (action === 'save_maintenance') {
                        setTimeout(() => {
                            window.location.href = 'dashboard.php';
                        }, 2000);
                    }
                } else {
                    showToast('error', data.message || 'Помилка збереження!');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('error', 'Помилка з\'єднання з сервером!');
            })
            .finally(() => {
                // Повернути кнопку в початковий стан
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }
        
        // Функція показу повідомлень
        function showToast(type, message) {
            const toastId = type === 'success' ? 'successToast' : 'errorToast';
            const messageId = type === 'success' ? 'successMessage' : 'errorMessage';
            
            document.getElementById(messageId).textContent = message;
            
            const toast = new bootstrap.Toast(document.getElementById(toastId));
            toast.show();
        }
        
        // Обробка режиму технічного обслуговування
        const maintenanceToggle = document.getElementById('maintenanceMode');
        const maintenanceSettings = document.querySelector('.maintenance-settings');
        
        if (maintenanceToggle) {
            maintenanceToggle.addEventListener('change', function() {
                if (this.checked) {
                    maintenanceSettings.style.display = 'block';
                    maintenanceSettings.style.animation = 'slideIn 0.3s ease';
                } else {
                    maintenanceSettings.style.display = 'none';
                }
            });
        }
        
        // Обробка форми теми
        const themeForm = document.getElementById('themeForm');
        if (themeForm) {
            themeForm.addEventListener('submit', function(e) {
                e.preventDefault();
                saveSettingsCustom(this, 'save_theme', 'Налаштування теми збережено!');
            });
        }
        
        // Обробка форми технічного обслуговування
        const maintenanceForm = document.getElementById('maintenanceForm');
        if (maintenanceForm) {
            maintenanceForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Спеціальне повідомлення якщо включений режим обслуговування
                const isMaintenanceEnabled = document.getElementById('maintenanceMode').checked;
                const message = isMaintenanceEnabled ? 
                    'Режим технічного обслуговування увімкнено! Сайт тепер недоступний для користувачів.' :
                    'Налаштування технічного обслуговування збережено!';
                
                saveSettingsCustom(this, 'save_maintenance', message);
            });
        }
        
        // Анімація для CSS
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        `;
        document.head.appendChild(style);
        
        // Закриття бокової панелі при кліку поза нею
        document.addEventListener('click', function(e) {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.querySelector('[onclick="toggleSidebar()"]');
            
            if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target) && sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });
    </script>
</body>
</html>