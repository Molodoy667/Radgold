<?php
// Убеждаемся что сессия запущена
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Подключаем необходимые файлы если они еще не подключены
if (!class_exists('Settings')) {
    require_once __DIR__ . '/settings.php';
}
if (!class_exists('Theme')) {
    require_once __DIR__ . '/theme.php';
}

// Инициализируем тему
if (!isset($db)) {
    require_once __DIR__ . '/../config/database.php';
    $database = new Database();
    $db = $database->getConnection();
}
Theme::init($db);

// Определяем текущую страницу
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Головна'; ?> - <?php echo Settings::get('site_name', 'Дошка Оголошень'); ?></title>
    <meta name="description" content="<?php echo $page_description ?? Settings::get('site_description', 'Безкоштовна дошка оголошень'); ?>">
    
    <!-- Favicon -->
    <?php 
    $favicon = Settings::get('site_favicon', '');
    if (!empty($favicon) && file_exists($favicon)): 
    ?>
        <link rel="icon" type="image/x-icon" href="<?php echo $favicon; ?>">
    <?php endif; ?>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Theme Styles -->
    <style>
        <?php echo Theme::generateCSS(); ?>
        
        /* Основные стили */
        body {
            background: var(--surface-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-color);
            margin: 0;
            padding: 0;
        }
        
        .main-navbar {
            background: var(--card-bg) !important;
            border-bottom: 1px solid var(--border-color);
            box-shadow: var(--shadow);
            backdrop-filter: blur(10px);
        }
        
        .main-navbar .navbar-brand {
            color: var(--text-color) !important;
            font-weight: 600;
            font-size: 1.3rem;
        }
        
        .main-navbar .nav-link {
            color: var(--text-color) !important;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .main-navbar .nav-link:hover,
        .main-navbar .nav-link.active {
            color: var(--theme-primary) !important;
        }
        
        .btn-primary {
            background: var(--theme-gradient);
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(var(--theme-primary-rgb), 0.4);
        }
        
        .btn-outline-primary {
            color: var(--theme-primary);
            border-color: var(--theme-primary);
        }
        
        .btn-outline-primary:hover {
            background: var(--theme-primary);
            border-color: var(--theme-primary);
        }
        
        /* Основной контент */
        .main-content {
            padding-top: 80px;
            min-height: 100vh;
        }
        
        /* Карточки */
        .card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            border-radius: 15px;
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow);
        }
        
        .card-header {
            background: var(--surface-color);
            border-bottom: 1px solid var(--border-color);
            border-radius: 15px 15px 0 0 !important;
        }
        
        /* Формы */
        .form-control {
            background: var(--surface-color);
            border: 2px solid var(--border-color);
            color: var(--text-color);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:hover {
            border-color: transparent;
            background: var(--card-bg);
            background-image: var(--theme-gradient);
            background-size: 100% 2px;
            background-position: 0 100%;
            background-repeat: no-repeat;
            box-shadow: 0 4px 15px rgba(var(--theme-primary-rgb), 0.1);
            transform: translateY(-1px);
        }
        
        .form-control:focus {
            background: var(--card-bg);
            border-color: transparent;
            color: var(--text-color);
            box-shadow: 
                0 0 0 3px rgba(var(--theme-primary-rgb), 0.15),
                0 8px 25px rgba(var(--theme-primary-rgb), 0.2);
            transform: translateY(-2px);
            background-image: var(--theme-gradient);
            background-size: 100% 2px;
            background-position: 0 100%;
            background-repeat: no-repeat;
        }
        
        .form-control::placeholder {
            color: var(--text-muted);
            opacity: 0.7;
            transition: all 0.3s ease;
        }
        
        .form-control:focus::placeholder {
            opacity: 0.5;
            transform: translateY(-2px);
        }
        
        .form-label {
            color: var(--text-color);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .text-muted {
            color: var(--text-muted) !important;
        }
        
        /* Градиентный текст */
        .text-gradient {
            background: var(--theme-gradient);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 600;
        }
        
        /* Тема переключатель */
        .theme-toggle {
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .theme-toggle:hover {
            background: var(--theme-primary);
            border-color: var(--theme-primary);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg main-navbar fixed-top">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="index.php">
                <?php 
                $logo_path = Settings::get('site_logo', '');
                if (!empty($logo_path) && file_exists($logo_path)): 
                ?>
                    <img src="<?php echo Settings::getLogoUrl(); ?>" alt="<?php echo htmlspecialchars(Settings::get('site_name')); ?>" style="max-height: 40px;" class="me-2">
                <?php endif; ?>
                <?php echo htmlspecialchars(Settings::get('site_name', 'Дошка Оголошень')); ?>
            </a>
            
            <!-- Toggle button for mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Navigation items -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page === 'index' ? 'active' : ''; ?>" href="index.php">
                            <i class="fas fa-home me-1"></i>Головна
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page === 'categories' ? 'active' : ''; ?>" href="pages/categories.php">
                            <i class="fas fa-list me-1"></i>Категорії
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $current_page === 'ads' ? 'active' : ''; ?>" href="pages/ads.php">
                            <i class="fas fa-bullhorn me-1"></i>Оголошення
                        </a>
                    </li>
                </ul>
                
                <!-- Right side navigation -->
                <ul class="navbar-nav">
                    <!-- Theme toggle -->
                    <li class="nav-item me-2">
                        <button class="btn theme-toggle btn-sm" onclick="toggleTheme()" title="Змінити тему">
                            <i class="fas fa-palette"></i>
                        </button>
                    </li>
                    
                    <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in']): ?>
                        <!-- Logged in user -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['username']); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="pages/profile.php">
                                    <i class="fas fa-user me-2"></i>Профіль
                                </a></li>
                                <li><a class="dropdown-item" href="pages/my_ads.php">
                                    <i class="fas fa-bullhorn me-2"></i>Мої оголошення
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="pages/logout.php">
                                    <i class="fas fa-sign-out-alt me-2"></i>Вихід
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- Not logged in -->
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_page === 'login' ? 'active' : ''; ?>" href="pages/login.php">
                                <i class="fas fa-sign-in-alt me-1"></i>Вхід
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary ms-2" href="pages/register.php">
                                <i class="fas fa-user-plus me-1"></i>Реєстрація
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Main content starts here -->
    <div class="main-content">