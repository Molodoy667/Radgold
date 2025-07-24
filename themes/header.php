<?php 
// Безпечне отримання налаштувань з fallback значеннями
try {
    $metaTags = function_exists('getMetaTags') ? getMetaTags() : [
        'title' => 'AdBoard Pro',
        'description' => 'Рекламна компанія та дошка оголошень',
        'keywords' => 'реклама, оголошення',
        'author' => 'AdBoard Pro',
        'favicon' => 'images/favicon.svg',
        'logo' => 'images/default_logo.svg'
    ];
    
    // Отримуємо тему з сесії або використовуємо дефолтні
    $currentTheme = $_SESSION['current_theme'] ?? 'light';
    $currentGradient = $_SESSION['current_gradient'] ?? 'gradient-1';
} catch (Exception $e) {
    // Fallback values if database is not available
    $metaTags = [
        'title' => 'AdBoard Pro',
        'description' => 'Рекламна компанія та дошка оголошень',
        'keywords' => 'реклама, оголошення',
        'author' => 'AdBoard Pro',
        'favicon' => 'images/favicon.svg',
        'logo' => 'images/default_logo.svg'
    ];
    $currentTheme = 'light';
    $currentGradient = 'gradient-1';
}

// Отримуємо всі градієнти
$gradients = [
    'gradient-1' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
    'gradient-2' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
    'gradient-3' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
    'gradient-4' => 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
    'gradient-5' => 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
    'gradient-6' => 'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
    'gradient-7' => 'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)',
    'gradient-8' => 'linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%)',
    'gradient-9' => 'linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%)',
    'gradient-10' => 'linear-gradient(135deg, #fad0c4 0%, #ffd1ff 100%)'
];

// Helper functions
function getSiteUrl($path = '') {
    $baseUrl = defined('SITE_URL') ? SITE_URL : 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
    return $baseUrl . ($path ? '/' . ltrim($path, '/') : '');
}

function getSiteName() {
    return defined('SITE_NAME') ? SITE_NAME : 'AdBoard Pro';
}

// Функція перекладу з fallback
function safeTranslate($key, $fallback = '') {
    if (function_exists('__')) {
        $translation = __($key);
        return $translation !== $key ? $translation : $fallback;
    }
    return $fallback;
}

// Поточна мова
$currentLang = $_SESSION['current_language'] ?? 'uk';
?>
<!DOCTYPE html>
<html lang="<?php echo $currentLang; ?>" data-theme="<?php echo $currentTheme; ?>" data-gradient="<?php echo $currentGradient; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- SEO Meta Tags -->
    <title><?php echo htmlspecialchars($metaTags['title']); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($metaTags['description']); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($metaTags['keywords']); ?>">
    <meta name="author" content="<?php echo htmlspecialchars($metaTags['author']); ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo getSiteUrl(); ?>">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo htmlspecialchars($metaTags['title']); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($metaTags['description']); ?>">
    <meta property="og:image" content="<?php echo getSiteUrl($metaTags['logo']); ?>">
    <meta property="og:url" content="<?php echo getSiteUrl(); ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?php echo getSiteName(); ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo $metaTags['favicon']; ?>">
    <link rel="apple-touch-icon" href="<?php echo $metaTags['logo']; ?>">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <!-- AOS Animation Library -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="themes/style.css">
    
    <!-- Dynamic Styles -->
    <style>
        :root {
            --current-gradient: <?php echo $gradients[$currentGradient]; ?>;
            --theme-bg: <?php echo $currentTheme === 'dark' ? '#1a1a1a' : '#ffffff'; ?>;
            --theme-text: <?php echo $currentTheme === 'dark' ? '#ffffff' : '#333333'; ?>;
            --theme-bg-secondary: <?php echo $currentTheme === 'dark' ? '#2d2d2d' : '#f8f9fa'; ?>;
            --theme-border: <?php echo $currentTheme === 'dark' ? '#404040' : '#dee2e6'; ?>;
            --theme-accent: <?php echo $currentTheme === 'dark' ? '#007bff' : '#0056b3'; ?>;
        }
        
        body {
            background-color: var(--theme-bg);
            color: var(--theme-text);
            transition: all 0.3s ease;
        }
        
        /* Красивий Navbar з покращеними анімаціями */
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(20px) saturate(180%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            position: relative;
            overflow: hidden;
        }
        
        .dark-theme .navbar {
            background: rgba(26, 26, 26, 0.95) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* Анімація появи navbar при скролі */
        .navbar::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--current-gradient);
            opacity: 0;
            transition: all 0.6s ease;
            z-index: -1;
        }
        
        .navbar:hover::before {
            left: 0;
            opacity: 0.05;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.6rem;
            color: var(--theme-text) !important;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            position: relative;
            z-index: 10;
        }
        
        .navbar-brand:hover {
            transform: scale(1.08) rotate(2deg);
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        
        .navbar-brand img {
            border-radius: 50%;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            filter: brightness(1);
        }
        
        .navbar-brand:hover img {
            transform: rotate(-5deg) scale(1.1);
            filter: brightness(1.2) drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3));
        }
        
        .navbar-nav .nav-link {
            color: var(--theme-text) !important;
            font-weight: 500;
            padding: 0.6rem 1.2rem !important;
            border-radius: 12px;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            position: relative;
            margin: 0 2px;
            overflow: hidden;
        }
        
        /* Красивий hover ефект з градієнтом */
        .navbar-nav .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--current-gradient);
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            z-index: -1;
            border-radius: 12px;
        }
        
        .navbar-nav .nav-link:hover::before {
            left: 0;
        }
        
        .navbar-nav .nav-link:hover {
            color: white !important;
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .navbar-nav .nav-link.active {
            background: var(--current-gradient);
            color: white !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            transform: scale(1.02);
        }
        
        .navbar-nav .nav-link i {
            margin-right: 8px;
            transition: all 0.3s ease;
        }
        
        .navbar-nav .nav-link:hover i {
            transform: rotate(10deg) scale(1.2);
        }
        
        /* Dropdown стилі з покращеними анімаціями */
        .dropdown-menu {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(255, 255, 255, 0.1);
            animation: dropdownSlideIn 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            overflow: hidden;
            min-width: 200px;
        }
        
        .dark-theme .dropdown-menu {
            background: rgba(26, 26, 26, 0.95) !important;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .dropdown-item {
            color: var(--theme-text);
            padding: 0.75rem 1.25rem;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            position: relative;
            overflow: hidden;
        }
        
        .dropdown-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--current-gradient);
            transition: all 0.3s ease;
            z-index: -1;
        }
        
        .dropdown-item:hover::before {
            left: 0;
        }
        
        .dropdown-item:hover {
            color: white;
            transform: translateX(5px);
        }
        
        .dropdown-item i {
            transition: all 0.3s ease;
        }
        
        .dropdown-item:hover i {
            transform: scale(1.2) rotate(5deg);
        }
        
        /* Кнопка теми */
        .theme-toggle-btn {
            background: var(--current-gradient);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: all 0.3s ease;
        }
        
        .theme-toggle-btn:hover {
            transform: rotate(180deg) scale(1.1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        /* Sidebar стилі */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .sidebar-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        
        .sidebar {
            position: fixed;
            top: 0;
            right: -400px;
            width: 350px;
            height: 100%;
            background: var(--theme-bg);
            border-left: 1px solid var(--theme-border);
            z-index: 1050;
            transition: all 0.3s ease;
            overflow-y: auto;
        }
        
        .sidebar.show {
            right: 0;
        }
        
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--theme-border);
            display: flex;
            justify-content: between;
            align-items: center;
        }
        
        .sidebar-header h5 {
            margin: 0;
            color: var(--theme-text);
        }
        
        .sidebar-content {
            padding: 1.5rem;
        }
        
        .theme-section {
            margin-bottom: 2rem;
        }
        
        .theme-section h6 {
            margin-bottom: 1rem;
            color: var(--theme-text);
        }
        
        .theme-toggle {
            display: flex;
            gap: 1rem;
        }
        
        .theme-toggle input[type="radio"] {
            display: none;
        }
        
        .theme-toggle label {
            flex: 1;
            padding: 1rem;
            text-align: center;
            border: 2px solid var(--theme-border);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: var(--theme-bg-secondary);
        }
        
        .theme-toggle input[type="radio"]:checked + label {
            border-color: var(--theme-accent);
            background: var(--current-gradient);
            color: white;
        }
        
        .gradient-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
        }
        
        .gradient-option {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
            border: 3px solid transparent;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        
        .gradient-option:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        
        .gradient-option.active {
            border-color: var(--theme-text);
            transform: scale(1.2);
        }
        
        /* Адаптивні стилі для всіх екранів */
        .main-content {
            min-height: calc(100vh - 80px);
            padding: 0;
        }
        
        .container-fluid, .container {
            padding-left: 15px;
            padding-right: 15px;
        }
        
        /* Responsive для tablet */
        @media (max-width: 992px) {
            .navbar-nav .nav-link {
                padding: 0.5rem 0.8rem !important;
                margin: 2px 0;
            }
            
            .navbar-brand {
                font-size: 1.4rem;
            }
        }
        
        /* Responsive для mobile */
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.2rem;
            }
            
            .navbar-nav {
                background: var(--theme-bg-secondary);
                border-radius: 15px;
                margin-top: 10px;
                padding: 10px;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            }
            
            .navbar-nav .nav-link {
                margin: 5px 0;
                text-align: center;
            }
            
            .sidebar {
                width: 100%;
                right: -100%;
            }
            
            .container-fluid, .container {
                padding-left: 10px;
                padding-right: 10px;
            }
            
            /* Адаптивність для контенту */
            .row {
                margin-left: -10px;
                margin-right: -10px;
            }
            
            .col, .col-1, .col-2, .col-3, .col-4, .col-5, .col-6,
            .col-7, .col-8, .col-9, .col-10, .col-11, .col-12,
            .col-auto, .col-lg, .col-lg-1, .col-lg-2, .col-lg-3,
            .col-lg-4, .col-lg-5, .col-lg-6, .col-lg-7, .col-lg-8,
            .col-lg-9, .col-lg-10, .col-lg-11, .col-lg-12, .col-lg-auto,
            .col-md, .col-md-1, .col-md-2, .col-md-3, .col-md-4,
            .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9,
            .col-md-10, .col-md-11, .col-md-12, .col-md-auto,
            .col-sm, .col-sm-1, .col-sm-2, .col-sm-3, .col-sm-4,
            .col-sm-5, .col-sm-6, .col-sm-7, .col-sm-8, .col-sm-9,
            .col-sm-10, .col-sm-11, .col-sm-12, .col-sm-auto,
            .col-xl, .col-xl-1, .col-xl-2, .col-xl-3, .col-xl-4,
            .col-xl-5, .col-xl-6, .col-xl-7, .col-xl-8, .col-xl-9,
            .col-xl-10, .col-xl-11, .col-xl-12, .col-xl-auto {
                padding-left: 10px;
                padding-right: 10px;
            }
        }
        
        /* Анімації */
        @keyframes dropdownSlideIn {
            from {
                opacity: 0;
                transform: translateY(-10px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideInFromLeft {
            from {
                opacity: 0;
                transform: translateX(-100%);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }
            50% {
                opacity: 1;
                transform: scale(1.05);
            }
            70% {
                transform: scale(0.9);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        /* Гарний скролбар */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: var(--theme-bg-secondary);
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: var(--current-gradient);
            border-radius: 3px;
        }
    </style>
</head>
<body class="<?php echo $currentTheme; ?>-theme">
    
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <!-- Brand -->
            <a class="navbar-brand d-flex align-items-center" href="<?php echo getSiteUrl(); ?>">
                <img src="<?php echo $metaTags['logo']; ?>" alt="<?php echo getSiteName(); ?>" height="40" class="me-2">
                <span><?php echo getSiteName(); ?></span>
            </a>
            
            <!-- Mobile Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Navigation Links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo getSiteUrl(); ?>">
                            <i class="fas fa-home"></i><?php echo safeTranslate('home', 'Головна'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo getSiteUrl('ads'); ?>">
                            <i class="fas fa-bullhorn"></i><?php echo safeTranslate('ads', 'Оголошення'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo getSiteUrl('categories'); ?>">
                            <i class="fas fa-th-large"></i><?php echo safeTranslate('categories', 'Категорії'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo getSiteUrl('services'); ?>">
                            <i class="fas fa-cogs"></i><?php echo safeTranslate('services', 'Послуги'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo getSiteUrl('about'); ?>">
                            <i class="fas fa-info-circle"></i><?php echo safeTranslate('about', 'Про нас'); ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo getSiteUrl('contact'); ?>">
                            <i class="fas fa-envelope"></i><?php echo safeTranslate('contact', 'Контакти'); ?>
                        </a>
                    </li>
                </ul>
                
                <!-- User Menu -->
                <ul class="navbar-nav align-items-center">
                    <?php if (function_exists('isLoggedIn') && isLoggedIn()): ?>
                        <!-- User Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user"></i>
                                <?php echo htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['username'] ?? 'User'); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="<?php echo getSiteUrl('profile'); ?>">
                                        <i class="fas fa-user me-2"></i><?php echo safeTranslate('profile', 'Профіль'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo getSiteUrl('my-ads'); ?>">
                                        <i class="fas fa-list me-2"></i><?php echo safeTranslate('my_ads', 'Мої оголошення'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo getSiteUrl('messages'); ?>">
                                        <i class="fas fa-comments me-2"></i><?php echo safeTranslate('messages', 'Повідомлення'); ?>
                                    </a>
                                </li>
                                <?php if (function_exists('isAdmin') && isAdmin()): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="<?php echo getSiteUrl('admin'); ?>">
                                            <i class="fas fa-cog me-2"></i><?php echo safeTranslate('admin_panel', 'Адміністрування'); ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo getSiteUrl('logout'); ?>">
                                        <i class="fas fa-sign-out-alt me-2"></i><?php echo safeTranslate('logout', 'Вихід'); ?>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- Login/Register -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo getSiteUrl('pages/login.php'); ?>">
                                <i class="fas fa-sign-in-alt"></i><?php echo safeTranslate('login', 'Вхід'); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo getSiteUrl('pages/register.php'); ?>">
                                <i class="fas fa-user-plus"></i><?php echo safeTranslate('register', 'Реєстрація'); ?>
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- Language Selector -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-globe"></i>
                            <?php 
                            $langFlags = ['uk' => '🇺🇦', 'ru' => '🇷🇺', 'en' => '🇺🇸'];
                            $langNames = ['uk' => 'UA', 'ru' => 'RU', 'en' => 'EN'];
                            echo ($langFlags[$currentLang] ?? '🌐') . ' ' . ($langNames[$currentLang] ?? 'Language');
                            ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item <?php echo $currentLang === 'uk' ? 'active' : ''; ?>" href="#" onclick="changeLanguage('uk')">
                                    🇺🇦 Українська
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo $currentLang === 'ru' ? 'active' : ''; ?>" href="#" onclick="changeLanguage('ru')">
                                    🇷🇺 Русский
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo $currentLang === 'en' ? 'active' : ''; ?>" href="#" onclick="changeLanguage('en')">
                                    🇺🇸 English
                                </a>
                            </li>
                        </ul>
                    </li>
                    
                    <!-- Theme Toggle -->
                    <li class="nav-item">
                        <button class="btn theme-toggle-btn ms-2" id="themeToggle" title="<?php echo safeTranslate('theme_settings', 'Налаштування теми'); ?>">
                            <i class="fas fa-palette"></i>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Theme Settings Sidebar -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <div class="sidebar" id="themeSidebar">
        <div class="sidebar-header">
            <h5><i class="fas fa-palette me-2"></i><?php echo safeTranslate('theme_settings', 'Налаштування теми'); ?></h5>
            <button class="btn-close" id="closeSidebar" aria-label="Close"></button>
        </div>
        
        <div class="sidebar-content">
            <!-- Theme Toggle -->
            <div class="theme-section">
                <h6><i class="fas fa-moon me-2"></i><?php echo safeTranslate('theme', 'Тема оформлення'); ?></h6>
                <div class="theme-toggle">
                    <input type="radio" name="theme" id="lightTheme" value="light" <?php echo $currentTheme === 'light' ? 'checked' : ''; ?>>
                    <label for="lightTheme">
                        <i class="fas fa-sun mb-2"></i>
                        <div><?php echo safeTranslate('light_theme', 'Світла'); ?></div>
                    </label>
                    
                    <input type="radio" name="theme" id="darkTheme" value="dark" <?php echo $currentTheme === 'dark' ? 'checked' : ''; ?>>
                    <label for="darkTheme">
                        <i class="fas fa-moon mb-2"></i>
                        <div><?php echo safeTranslate('dark_theme', 'Темна'); ?></div>
                    </label>
                </div>
            </div>
            
            <!-- Gradient Selection -->
            <div class="theme-section">
                <h6><i class="fas fa-brush me-2"></i><?php echo safeTranslate('gradient', 'Градієнт оформлення'); ?></h6>
                <div class="gradient-grid">
                    <?php foreach ($gradients as $key => $gradient): ?>
                        <div class="gradient-option <?php echo $currentGradient === $key ? 'active' : ''; ?>" 
                             data-gradient="<?php echo $key; ?>" 
                             style="background: <?php echo $gradient; ?>"
                             title="<?php echo ucfirst(str_replace('-', ' ', $key)); ?>">
                            <?php if ($currentGradient === $key): ?>
                                <i class="fas fa-check"></i>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="main-content">

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript for theme and language -->
<script>
// Language change function
function changeLanguage(lang) {
    const loading = showLoadingIndicator();
    
    fetch('<?php echo getSiteUrl('change_language.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'language=' + lang
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Smooth transition before reload
            document.body.style.opacity = '0.7';
            setTimeout(() => {
                location.reload();
            }, 300);
        } else {
            loading.remove();
            console.error('Language change error:', data.message);
        }
    })
    .catch(error => {
        loading.remove();
        console.error('Error changing language:', error);
    });
}

// Theme functionality
document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('themeToggle');
    const sidebar = document.getElementById('themeSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const closeSidebar = document.getElementById('closeSidebar');
    
    // Open sidebar
    themeToggle.addEventListener('click', function() {
        sidebar.classList.add('show');
        overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
    });
    
    // Close sidebar
    function closeSidebarFunc() {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
        document.body.style.overflow = '';
    }
    
    closeSidebar.addEventListener('click', closeSidebarFunc);
    overlay.addEventListener('click', closeSidebarFunc);
    
    // Theme toggle
    const themeInputs = document.querySelectorAll('input[name="theme"]');
    themeInputs.forEach(input => {
        input.addEventListener('change', function() {
            changeTheme(this.value);
        });
    });
    
    // Gradient selection
    const gradientOptions = document.querySelectorAll('.gradient-option');
    gradientOptions.forEach(option => {
        option.addEventListener('click', function() {
            const gradient = this.dataset.gradient;
            changeGradient(gradient);
        });
    });
});

// Change theme
function changeTheme(theme) {
    const loading = showLoadingIndicator();
    
    fetch('<?php echo getSiteUrl('ajax/change_theme.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'theme=' + theme
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Smooth transition before reload
            document.body.style.opacity = '0.7';
            setTimeout(() => {
                location.reload();
            }, 300);
        } else {
            loading.remove();
            console.error('Theme change failed:', data.message);
        }
    })
    .catch(error => {
        loading.remove();
        console.error('Error changing theme:', error);
    });
}

// Change gradient
function changeGradient(gradient) {
    const loading = showLoadingIndicator();
    
    fetch('<?php echo getSiteUrl('ajax/change_theme.php'); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'gradient=' + gradient
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Smooth transition before reload
            document.body.style.opacity = '0.7';
            setTimeout(() => {
                location.reload();
            }, 300);
        } else {
            loading.remove();
            console.error('Gradient change failed:', data.message);
        }
    })
    .catch(error => {
        loading.remove();
        console.error('Error changing gradient:', error);
    });
}

// Set active nav link and add scroll effects
document.addEventListener('DOMContentLoaded', function() {
    const currentPage = window.location.pathname;
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    const navbar = document.querySelector('.navbar');
    
    // Set active link
    navLinks.forEach(link => {
        if (link.getAttribute('href') === currentPage || 
            (currentPage === '/' && link.getAttribute('href') === '<?php echo getSiteUrl(); ?>')) {
            link.classList.add('active');
        }
    });
    
    // Add scroll effect to navbar
    let lastScrollTop = 0;
    window.addEventListener('scroll', function() {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        if (scrollTop > lastScrollTop && scrollTop > 100) {
            // Scrolling down
            navbar.style.transform = 'translateY(-100%)';
        } else {
            // Scrolling up
            navbar.style.transform = 'translateY(0)';
        }
        
        // Add glassmorphism effect when scrolled
        if (scrollTop > 50) {
            navbar.style.backdropFilter = 'blur(25px) saturate(200%)';
            navbar.style.boxShadow = '0 12px 40px rgba(0, 0, 0, 0.15)';
        } else {
            navbar.style.backdropFilter = 'blur(20px) saturate(180%)';
            navbar.style.boxShadow = '0 8px 32px rgba(0, 0, 0, 0.1)';
        }
        
        lastScrollTop = scrollTop;
    });
    
    // Add stagger animation to nav links
    navLinks.forEach((link, index) => {
        link.style.animationDelay = `${index * 0.1}s`;
        link.style.animation = 'slideInFromLeft 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards';
    });
    
    // Add loading animation to dropdowns
    const dropdowns = document.querySelectorAll('.dropdown-menu');
    dropdowns.forEach(dropdown => {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    if (dropdown.classList.contains('show')) {
                        const items = dropdown.querySelectorAll('.dropdown-item');
                        items.forEach((item, index) => {
                            item.style.animationDelay = `${index * 0.05}s`;
                            item.style.animation = 'slideInFromLeft 0.3s ease forwards';
                        });
                    }
                }
            });
        });
        observer.observe(dropdown, { attributes: true });
    });
});

// Add loading indicator for theme changes
function showLoadingIndicator() {
    const loading = document.createElement('div');
    loading.className = 'loading-indicator';
    loading.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    loading.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 20px;
        border-radius: 10px;
        z-index: 9999;
        font-size: 24px;
    `;
    document.body.appendChild(loading);
    return loading;
}
</script>
