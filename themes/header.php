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
    
    $themeSettings = function_exists('getThemeSettings') ? getThemeSettings() : [];
    $currentTheme = $themeSettings['current_theme'] ?? 'light';
    $currentGradient = $themeSettings['current_gradient'] ?? 'gradient-1';
    $gradients = function_exists('generateGradients') ? generateGradients() : [
        'gradient-1' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
    ];
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
    $gradients = ['gradient-1' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'];
}

// Helper function for safe URL generation
function getSiteUrl($path = '') {
    $baseUrl = defined('SITE_URL') ? SITE_URL : 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
    return $baseUrl . ($path ? '/' . ltrim($path, '/') : '');
}

function getSiteName() {
    return defined('SITE_NAME') ? SITE_NAME : 'AdBoard Pro';
}
?>
<!DOCTYPE html>
<html lang="uk" data-theme="<?php echo $currentTheme; ?>" data-gradient="<?php echo $currentGradient; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- SEO Meta Tags -->
    <title><?php echo function_exists('sanitize') ? sanitize($metaTags['title']) : htmlspecialchars($metaTags['title']); ?></title>
    <meta name="description" content="<?php echo function_exists('sanitize') ? sanitize($metaTags['description']) : htmlspecialchars($metaTags['description']); ?>">
    <meta name="keywords" content="<?php echo function_exists('sanitize') ? sanitize($metaTags['keywords']) : htmlspecialchars($metaTags['keywords']); ?>">
    <meta name="author" content="<?php echo function_exists('sanitize') ? sanitize($metaTags['author']) : htmlspecialchars($metaTags['author']); ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo defined('SITE_URL') ? SITE_URL : 'http://localhost'; ?>">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo function_exists('sanitize') ? sanitize($metaTags['title']) : htmlspecialchars($metaTags['title']); ?>">
    <meta property="og:description" content="<?php echo function_exists('sanitize') ? sanitize($metaTags['description']) : htmlspecialchars($metaTags['description']); ?>">
    <meta property="og:image" content="<?php echo (defined('SITE_URL') ? SITE_URL : 'http://localhost') . '/' . $metaTags['logo']; ?>">
    <meta property="og:url" content="<?php echo defined('SITE_URL') ? SITE_URL : 'http://localhost'; ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?php echo defined('SITE_NAME') ? SITE_NAME : 'AdBoard Pro'; ?>">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo function_exists('sanitize') ? sanitize($metaTags['title']) : htmlspecialchars($metaTags['title']); ?>">
    <meta name="twitter:description" content="<?php echo function_exists('sanitize') ? sanitize($metaTags['description']) : htmlspecialchars($metaTags['description']); ?>">
    <meta name="twitter:image" content="<?php echo (defined('SITE_URL') ? SITE_URL : 'http://localhost') . '/' . $metaTags['logo']; ?>">
    
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
    
    <!-- Dynamic Gradient Styles -->
    <style>
        :root {
            --current-gradient: <?php echo $gradients[$currentGradient]; ?>;
            --theme-bg: <?php echo $currentTheme === 'dark' ? '#1a1a1a' : '#ffffff'; ?>;
            --theme-text: <?php echo $currentTheme === 'dark' ? '#ffffff' : '#333333'; ?>;
            --theme-bg-secondary: <?php echo $currentTheme === 'dark' ? '#2d2d2d' : '#f8f9fa'; ?>;
            --theme-border: <?php echo $currentTheme === 'dark' ? '#404040' : '#dee2e6'; ?>;
        }
        
        body {
            background-color: var(--theme-bg);
            color: var(--theme-text);
            transition: all 0.3s ease;
        }
        
        .gradient-bg {
            background: var(--current-gradient);
        }
        
        .card {
            background-color: var(--theme-bg-secondary);
            border-color: var(--theme-border);
        }
        
        .navbar {
            background-color: var(--theme-bg-secondary) !important;
            border-bottom: 1px solid var(--theme-border);
        }
        
        .footer {
            background-color: var(--theme-bg-secondary);
            border-top: 1px solid var(--theme-border);
        }
    </style>
</head>
<body class="<?php echo $currentTheme; ?>-theme"
      data-animations="<?php echo getSiteSetting('enable_animations', true) ? 'true' : 'false'; ?>"
      data-particles="<?php echo getSiteSetting('enable_particles', false) ? 'true' : 'false'; ?>"
      data-smooth-scroll="<?php echo getSiteSetting('smooth_scroll', true) ? 'true' : 'false'; ?>"
      data-tooltips="<?php echo getSiteSetting('enable_tooltips', true) ? 'true' : 'false'; ?>">
    
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?php echo getSiteUrl(); ?>">
                <img src="<?php echo $metaTags['logo']; ?>" alt="<?php echo getSiteName(); ?>" height="40" class="me-2">
                <span class="fw-bold"><?php echo getSiteName(); ?></span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo getSiteUrl(); ?>"><i class="fas fa-home me-1"></i>Головна</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo getSiteUrl('pages/ads.php'); ?>"><i class="fas fa-bullhorn me-1"></i>Оголошення</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo getSiteUrl('services'); ?>"><i class="fas fa-cogs me-1"></i>Послуги</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo getSiteUrl('about'); ?>"><i class="fas fa-info-circle me-1"></i>Про нас</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo getSiteUrl('contact'); ?>"><i class="fas fa-envelope me-1"></i>Контакти</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (function_exists('isLoggedIn') && isLoggedIn()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i><?php echo function_exists('sanitize') ? sanitize($_SESSION['username'] ?? 'User') : htmlspecialchars($_SESSION['username'] ?? 'User'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?php echo getSiteUrl('profile'); ?>"><i class="fas fa-user me-2"></i>Профіль</a></li>
                                <li><a class="dropdown-item" href="<?php echo getSiteUrl('my-ads'); ?>"><i class="fas fa-list me-2"></i>Мої оголошення</a></li>
                                <?php if (function_exists('isAdmin') && isAdmin()): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo getSiteUrl('admin'); ?>"><i class="fas fa-cog me-2"></i>Адміністрування</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo getSiteUrl('logout'); ?>"><i class="fas fa-sign-out-alt me-2"></i>Вихід</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo getSiteUrl('login'); ?>"><i class="fas fa-sign-in-alt me-1"></i>Вхід</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo getSiteUrl('register'); ?>"><i class="fas fa-user-plus me-1"></i>Реєстрація</a>
                        </li>
                    <?php endif; ?>
                    
                    <!-- Theme Toggle Button -->
                    <li class="nav-item">
                        <button class="btn btn-outline-secondary btn-sm ms-2" id="themeToggle">
                            <i class="fas fa-palette"></i>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar for Theme Settings -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <div class="sidebar" id="themeSidebar">
        <div class="sidebar-header">
            <h5><i class="fas fa-palette me-2"></i>Налаштування теми</h5>
            <button class="btn-close" id="closeSidebar"></button>
        </div>
        
        <div class="sidebar-content">
            <!-- Theme Toggle -->
            <div class="theme-section">
                <h6><i class="fas fa-moon me-2"></i>Тема оформлення</h6>
                <div class="theme-toggle">
                    <input type="radio" name="theme" id="lightTheme" value="light" <?php echo $currentTheme === 'light' ? 'checked' : ''; ?>>
                    <label for="lightTheme">
                        <i class="fas fa-sun"></i>
                        <span>Світла</span>
                    </label>
                    
                    <input type="radio" name="theme" id="darkTheme" value="dark" <?php echo $currentTheme === 'dark' ? 'checked' : ''; ?>>
                    <label for="darkTheme">
                        <i class="fas fa-moon"></i>
                        <span>Темна</span>
                    </label>
                </div>
            </div>
            
            <!-- Gradient Selection -->
            <div class="gradient-section">
                <h6><i class="fas fa-brush me-2"></i>Градієнт оформлення</h6>
                <div class="gradient-grid">
                    <?php foreach ($gradients as $key => $gradient): ?>
                        <div class="gradient-option <?php echo $currentGradient === $key ? 'active' : ''; ?>" 
                             data-gradient="<?php echo $key; ?>" 
                             style="background: <?php echo $gradient; ?>">
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
