<?php
if (!isAdmin()) {
    redirect(SITE_URL . '/admin/login.php');
}

$metaTags = getMetaTags();
$themeSettings = getThemeSettings();
$currentTheme = $themeSettings['current_theme'] ?? 'light';
$currentGradient = $themeSettings['current_gradient'] ?? 'gradient-1';
$gradients = generateGradients();
?>
<!DOCTYPE html>
<html lang="uk" data-theme="<?php echo $currentTheme; ?>" data-gradient="<?php echo $currentGradient; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <title>Адмін-панель - <?php echo SITE_NAME; ?></title>
    <meta name="robots" content="noindex, nofollow">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../themes/style.css">
    
    <!-- Admin specific styles -->
    <style>
        :root {
            --current-gradient: <?php echo $gradients[$currentGradient]; ?>;
            --theme-bg: <?php echo $currentTheme === 'dark' ? '#1a1a1a' : '#ffffff'; ?>;
            --theme-text: <?php echo $currentTheme === 'dark' ? '#ffffff' : '#333333'; ?>;
            --theme-bg-secondary: <?php echo $currentTheme === 'dark' ? '#2d2d2d' : '#f8f9fa'; ?>;
            --theme-border: <?php echo $currentTheme === 'dark' ? '#404040' : '#dee2e6'; ?>;
            --sidebar-width: 280px;
        }
        
        body {
            background-color: var(--theme-bg-secondary);
            font-size: 0.875rem;
        }
        
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--theme-bg);
            border-right: 1px solid var(--theme-border);
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.3s ease;
        }
        
        .admin-sidebar.collapsed {
            width: 70px;
        }
        
        .admin-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s ease;
        }
        
        .admin-content.expanded {
            margin-left: 70px;
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--theme-border);
            text-align: center;
        }
        
        .sidebar-header .logo {
            font-size: 1.5rem;
            font-weight: bold;
            background: var(--current-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .sidebar-nav {
            padding: 1rem 0;
        }
        
        .nav-item {
            margin: 0.25rem 0;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: var(--theme-text);
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            background: none;
        }
        
        .nav-link:hover {
            background: var(--theme-bg-secondary);
            color: var(--theme-text);
            transform: translateX(5px);
        }
        
        .nav-link.active {
            background: var(--current-gradient);
            color: white;
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
            text-align: center;
        }
        
        .sidebar-toggle {
            position: fixed;
            top: 1rem;
            left: calc(var(--sidebar-width) - 35px);
            z-index: 1001;
            background: var(--current-gradient);
            border: none;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        
        .sidebar-toggle.collapsed {
            left: 35px;
        }
        
        .admin-header {
            background: var(--theme-bg);
            border-bottom: 1px solid var(--theme-border);
            padding: 1rem 2rem;
            display: flex;
            justify-content: between;
            align-items: center;
        }
        
        .breadcrumb {
            background: none;
            padding: 0;
            margin: 0;
        }
        
        .user-dropdown {
            margin-left: auto;
        }
        
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }
            
            .admin-sidebar.mobile-open {
                transform: translateX(0);
            }
            
            .admin-content {
                margin-left: 0;
            }
            
            .sidebar-toggle {
                left: 1rem;
            }
        }
    </style>
</head>
<body class="<?php echo $currentTheme; ?>-theme">
    
    <!-- Sidebar Toggle Button -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Admin Sidebar -->
    <nav class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div class="logo"><?php echo SITE_NAME; ?></div>
            <small class="text-muted">Адмін-панель</small>
        </div>
        
        <div class="sidebar-nav">
            <div class="nav-item">
                <a href="<?php echo SITE_URL; ?>/admin" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' || basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            
            <!-- Загальні налаштування -->
            <div class="nav-item">
                <div class="nav-link text-muted text-uppercase small fw-bold" style="cursor: default;">
                    <i class="fas fa-cog"></i>
                    <span>Налаштування</span>
                </div>
            </div>
            
            <div class="nav-item">
                <a href="<?php echo SITE_URL; ?>/admin/settings" class="nav-link">
                    <i class="fas fa-sliders-h"></i>
                    <span>Загальні</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="<?php echo SITE_URL; ?>/admin/theme" class="nav-link">
                    <i class="fas fa-palette"></i>
                    <span>Тема та дизайн</span>
                </a>
            </div>
            
            <!-- Контент -->
            <div class="nav-item">
                <div class="nav-link text-muted text-uppercase small fw-bold" style="cursor: default;">
                    <i class="fas fa-file-alt"></i>
                    <span>Контент</span>
                </div>
            </div>
            
            <div class="nav-item">
                <a href="<?php echo SITE_URL; ?>/admin/ads" class="nav-link">
                    <i class="fas fa-bullhorn"></i>
                    <span>Оголошення</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="<?php echo SITE_URL; ?>/admin/categories" class="nav-link">
                    <i class="fas fa-tags"></i>
                    <span>Категорії</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="<?php echo SITE_URL; ?>/admin/pages" class="nav-link">
                    <i class="fas fa-file"></i>
                    <span>Сторінки</span>
                </a>
            </div>
            
            <!-- Користувачі -->
            <div class="nav-item">
                <div class="nav-link text-muted text-uppercase small fw-bold" style="cursor: default;">
                    <i class="fas fa-users"></i>
                    <span>Користувачі</span>
                </div>
            </div>
            
            <div class="nav-item">
                <a href="<?php echo SITE_URL; ?>/admin/users" class="nav-link">
                    <i class="fas fa-user"></i>
                    <span>Всі користувачі</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="<?php echo SITE_URL; ?>/admin/messages" class="nav-link">
                    <i class="fas fa-envelope"></i>
                    <span>Повідомлення</span>
                </a>
            </div>
            
            <!-- Система -->
            <div class="nav-item">
                <div class="nav-link text-muted text-uppercase small fw-bold" style="cursor: default;">
                    <i class="fas fa-server"></i>
                    <span>Система</span>
                </div>
            </div>
            
            <div class="nav-item">
                <a href="<?php echo SITE_URL; ?>/admin/updates" class="nav-link">
                    <i class="fas fa-sync-alt"></i>
                    <span>Встановлення оновлень</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="<?php echo SITE_URL; ?>/admin/error-logs" class="nav-link">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Лог помилок</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="<?php echo SITE_URL; ?>/admin/logs" class="nav-link">
                    <i class="fas fa-list-alt"></i>
                    <span>Системні логи</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="<?php echo SITE_URL; ?>/admin/backups" class="nav-link">
                    <i class="fas fa-download"></i>
                    <span>Бекапи</span>
                </a>
            </div>
            
            <!-- Розділювач -->
            <hr class="my-3">
            
            <div class="nav-item">
                <a href="<?php echo SITE_URL; ?>" class="nav-link" target="_blank">
                    <i class="fas fa-external-link-alt"></i>
                    <span>Переглянути сайт</span>
                </a>
            </div>
            
            <div class="nav-item">
                <a href="<?php echo SITE_URL; ?>/logout" class="nav-link text-danger">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Вихід</span>
                </a>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <div class="admin-content" id="adminContent">
        <!-- Header -->
        <div class="admin-header">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="<?php echo SITE_URL; ?>/admin" class="text-decoration-none">
                            <i class="fas fa-home"></i> Головна
                        </a>
                    </li>
                    <?php
                    $currentPage = basename($_SERVER['PHP_SELF'], '.php');
                    if ($currentPage !== 'dashboard' && $currentPage !== 'index') {
                        $pageTitle = ucfirst(str_replace(['-', '_'], ' ', $currentPage));
                        echo '<li class="breadcrumb-item active">' . $pageTitle . '</li>';
                    }
                    ?>
                </ol>
            </nav>
            
            <div class="user-dropdown">
                <div class="dropdown">
                    <button class="btn btn-link dropdown-toggle text-decoration-none" type="button" 
                            id="userDropdown" data-bs-toggle="dropdown">
                        <i class="fas fa-user-circle me-2"></i><?php echo sanitize($_SESSION['user_name'] ?? 'Адмін'); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/profile">
                                <i class="fas fa-user me-2"></i>Профіль
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="<?php echo SITE_URL; ?>/admin/settings">
                                <i class="fas fa-cog me-2"></i>Налаштування
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="<?php echo SITE_URL; ?>/logout">
                                <i class="fas fa-sign-out-alt me-2"></i>Вихід
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Page Content -->
        <div class="p-4">
