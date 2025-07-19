<?php
// Убеждаемся что сессия запущена
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Перевірка авторизації адміна
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: index.php');
    exit();
}

// Подключаем необходимые файлы если они еще не подключены
if (!class_exists('Database')) {
    require_once __DIR__ . '/../../config/database.php';
}
if (!class_exists('Settings')) {
    require_once __DIR__ . '/../../includes/settings.php';
}
if (!class_exists('Theme')) {
    require_once __DIR__ . '/../../includes/theme.php';
}

// Получаем данные админа
$database = new Database();
$db = $database->getConnection();
$admin_query = "SELECT * FROM users WHERE id = ? AND is_admin = 1";
$admin_stmt = $db->prepare($admin_query);
$admin_stmt->execute([$_SESSION['admin_id']]);
$admin = $admin_stmt->fetch(PDO::FETCH_ASSOC);

// Определяем текущую страницу для активного пункта меню
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Адмін панель'; ?> - <?php echo Settings::get('site_name', 'Дошка Оголошень'); ?></title>
    
    <!-- Favicon -->
    <?php 
    $favicon = Settings::get('site_favicon', '');
    if (!empty($favicon) && file_exists('../' . $favicon)): 
    ?>
        <link rel="icon" type="image/x-icon" href="../<?php echo $favicon; ?>">
    <?php endif; ?>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Theme Styles -->
    <style>
        <?php echo Theme::generateCSS(); ?>
        
        /* Обновленный стиль для кнопки сайдбара */
        .swipe-indicator {
            position: fixed;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 15px !important;
            height: 30px !important;
            background: var(--theme-gradient);
            border-radius: 0 8px 8px 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.6rem !important;
            z-index: 1030;
            opacity: 0.7;
            animation: swipePulse 2s ease-in-out infinite;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .swipe-indicator:hover {
            opacity: 1;
            transform: translateY(-50%) scale(1.1);
            width: 18px !important;
        }
        
        .swipe-indicator.hidden {
            opacity: 0;
            transform: translateY(-50%) translateX(-20px);
        }
        
        @keyframes swipePulse {
            0%, 100% { 
                opacity: 0.7; 
                transform: translateY(-50%) scale(1);
            }
            50% { 
                opacity: 1; 
                transform: translateY(-50%) scale(1.05);
            }
        }
        
        /* Адаптивность для мобильных */
        @media (max-width: 768px) {
            .swipe-indicator {
                display: none;
            }
        }
        
        /* Основные стили админки */
        body {
            background: var(--surface-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-color);
            margin: 0;
            padding: 0;
        }
        
        .admin-navbar {
            background: var(--card-bg) !important;
            border-bottom: 1px solid var(--border-color);
            box-shadow: var(--shadow);
            backdrop-filter: blur(10px);
        }
        
        .admin-navbar .navbar-brand {
            color: var(--text-color) !important;
            font-weight: 600;
            font-size: 1.2rem;
        }
        
        .admin-navbar .nav-link {
            color: var(--text-color) !important;
            transition: all 0.3s ease;
        }
        
        .admin-navbar .nav-link:hover {
            color: var(--theme-primary) !important;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            left: -350px;
            width: 350px;
            height: 100vh;
            background: var(--card-bg);
            border-right: 1px solid var(--border-color);
            box-shadow: 5px 0 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            z-index: 1050;
            overflow-y: auto;
        }
        
        .sidebar.active {
            left: 0;
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            background: var(--theme-gradient);
            color: white;
            display: flex;
            justify-content: space-between;
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
        
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        /* Основной контент */
        .main-content {
            padding-top: 80px;
            min-height: 100vh;
        }
        
        /* Профиль админа */
        .profile-placeholder {
            width: 32px;
            height: 32px;
            background: var(--theme-gradient);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .profile-placeholder:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <!-- Swipe Indicator -->
    <div class="swipe-indicator" onclick="toggleSidebar()" title="Меню навігації">
        <i class="fas fa-chevron-right"></i>
    </div>
    
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg admin-navbar fixed-top">
        <div class="container-fluid">
            <!-- Left side - Menu button on mobile -->
            <button class="navbar-toggler d-lg-none" type="button" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- Profile and greeting -->
            <div class="navbar-nav">
                <?php if (!empty($admin['avatar']) && file_exists('../' . $admin['avatar'])): ?>
                    <img src="../<?php echo htmlspecialchars($admin['avatar']); ?>" 
                         alt="Avatar" class="rounded-circle me-2" 
                         style="width: 32px; height: 32px; object-fit: cover; cursor: pointer;" 
                         onclick="openProfileModal()">
                <?php else: ?>
                    <div class="profile-placeholder me-2" onclick="openProfileModal()">
                        <?php echo strtoupper(substr($admin['username'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
                
                <span class="d-none d-md-inline">
                    Привіт, <?php echo htmlspecialchars($admin['username']); ?>!
                </span>
            </div>
            
            <!-- Logo -->
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
            
            <!-- Navigation Toggle -->
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
    
    <!-- Sidebar -->
    <div class="sidebar-overlay" onclick="closeSidebar()"></div>
    <div class="sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div>
                <h5 class="mb-0">
                    <i class="fas fa-cogs me-2"></i>Панель управління
                </h5>
                <small>Адміністрування сайту</small>
            </div>
            <button class="close-sidebar" onclick="closeSidebar()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="sidebar-menu">
            <a href="dashboard.php" class="menu-item <?php echo $current_page == 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt me-3"></i>Головна панель
            </a>
            <a href="settings.php" class="menu-item <?php echo $current_page == 'settings' ? 'active' : ''; ?>">
                <i class="fas fa-cog me-3"></i>Генеральні налаштування
            </a>
            <a href="categories.php" class="menu-item <?php echo $current_page == 'categories' ? 'active' : ''; ?>">
                <i class="fas fa-list me-3"></i>Категорії
            </a>
            <a href="users.php" class="menu-item <?php echo $current_page == 'users' ? 'active' : ''; ?>">
                <i class="fas fa-users me-3"></i>Користувачі
            </a>
            <a href="ads.php" class="menu-item <?php echo $current_page == 'ads' ? 'active' : ''; ?>">
                <i class="fas fa-bullhorn me-3"></i>Оголошення
            </a>
            <a href="logs.php" class="menu-item <?php echo $current_page == 'logs' ? 'active' : ''; ?>">
                <i class="fas fa-history me-3"></i>Логи системи
            </a>
            <a href="analytics.php" class="menu-item <?php echo $current_page == 'analytics' ? 'active' : ''; ?>">
                <i class="fas fa-chart-bar me-3"></i>Аналітика
            </a>
            <a href="themes.php" class="menu-item <?php echo $current_page == 'themes' ? 'active' : ''; ?>">
                <i class="fas fa-paint-brush me-3"></i>Теми та дизайн
            </a>
            <a href="backup.php" class="menu-item <?php echo $current_page == 'backup' ? 'active' : ''; ?>">
                <i class="fas fa-download me-3"></i>Резервні копії
            </a>
            <a href="../index.php" class="menu-item" target="_blank">
                <i class="fas fa-external-link-alt me-3"></i>Переглянути сайт
            </a>
        </div>
    </div>

    <!-- Main content starts here -->
    <div class="main-content">