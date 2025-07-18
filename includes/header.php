<?php
// Завантажуємо конфігурацію якщо ще не завантажена
if (!class_exists('Settings')) {
    require_once __DIR__ . '/../config/config.php';
}

// Отримуємо налаштування для META тегів
$page_title = isset($page_title) ? $page_title : null;
$page_description = isset($page_description) ? $page_description : null;
$page_keywords = isset($page_keywords) ? $page_keywords : null;

$meta_data = Settings::getMetaTags($page_title, $page_description, $page_keywords);
$site_name = Settings::get('site_name', 'Дошка Оголошень');
$logo_url = Settings::getLogoUrl();
$theme_color = Settings::get('theme_color', '#007bff');
?>
<!DOCTYPE html>
<html lang="<?php echo Settings::get('site_language', 'uk'); ?>">
<head>
    <?php echo $meta_data['meta_tags']; ?>
    
    <title><?php echo htmlspecialchars($meta_data['title']); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AOS Animation -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo getBaseUrl(); ?>/assets/css/style.css">
    
    <!-- Динамічні CSS змінні для кольорів теми -->
    <style>
        :root {
            --theme-color: <?php echo Settings::get('theme_color', '#007bff'); ?>;
            --theme-secondary: <?php echo Settings::get('theme_secondary_color', '#6c757d'); ?>;
            --header-bg: <?php echo Settings::get('header_background', '#ffffff'); ?>;
            --footer-bg: <?php echo Settings::get('footer_background', '#343a40'); ?>;
        }
        
        .navbar-brand img {
            max-height: 40px;
            width: auto;
        }
        
        .navbar {
            background-color: var(--header-bg) !important;
        }
        
        .btn-primary {
            background-color: var(--theme-color);
            border-color: var(--theme-color);
        }
        
        .btn-primary:hover {
            background-color: var(--theme-color);
            border-color: var(--theme-color);
            opacity: 0.9;
        }
        
        .text-primary {
            color: var(--theme-color) !important;
        }
        
        .bg-primary {
            background-color: var(--theme-color) !important;
        }
    </style>
    
    <?php echo $meta_data['analytics']; ?>
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="<?php echo getBaseUrl(); ?>/index.php" data-spa data-page="home">
            <?php 
            $logo_path = Settings::get('site_logo', '');
            if (!empty($logo_path) && file_exists($logo_path)): 
            ?>
                <img src="<?php echo $logo_url; ?>" alt="<?php echo htmlspecialchars($site_name); ?>" class="me-2">
            <?php else: ?>
                <i class="fas fa-bullhorn me-2 text-primary"></i>
            <?php endif; ?>
            <?php echo htmlspecialchars($site_name); ?>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo getBaseUrl(); ?>/index.php" data-spa data-page="home">
                        <i class="fas fa-home me-1"></i>Головна
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo getBaseUrl(); ?>/pages/categories.php" data-spa data-page="categories">
                        <i class="fas fa-list me-1"></i>Категорії
                    </a>
                </li>
                <?php if (Settings::get('enable_search', true)): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo getBaseUrl(); ?>/pages/search.php" data-spa data-page="search">
                        <i class="fas fa-search me-1"></i>Пошук
                    </a>
                </li>
                <?php endif; ?>
            </ul>
            
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo getBaseUrl(); ?>/pages/profile.php" data-spa data-page="profile">
                                <i class="fas fa-user-circle me-1"></i>Мій профіль
                            </a></li>
                            <li><a class="dropdown-item" href="<?php echo getBaseUrl(); ?>/pages/my_ads.php" data-spa data-page="my_ads">
                                <i class="fas fa-list-ul me-1"></i>Мої оголошення
                            </a></li>
                            <?php if (Settings::get('enable_favorites', true)): ?>
                            <li><a class="dropdown-item" href="<?php echo getBaseUrl(); ?>/pages/favorites.php" data-spa data-page="favorites">
                                <i class="fas fa-heart me-1"></i>Вподобання
                            </a></li>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']): ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo getBaseUrl(); ?>/admin/dashboard.php">
                                <i class="fas fa-cogs me-1"></i>Адмін панель
                            </a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" onclick="handleLogout(); return false;">
                                <i class="fas fa-sign-out-alt me-1"></i>Вихід
                            </a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <?php if (Settings::get('registration_enabled', true)): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo getBaseUrl(); ?>/pages/login.php" data-spa data-page="login">
                            <i class="fas fa-sign-in-alt me-1"></i>Вхід
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo getBaseUrl(); ?>/pages/register.php" data-spa data-page="register">
                            <i class="fas fa-user-plus me-1"></i>Реєстрація
                        </a>
                    </li>
                    <?php endif; ?>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="btn btn-warning ms-2 text-dark fw-bold" href="<?php echo getBaseUrl(); ?>/pages/add_ad.php" data-spa data-page="add_ad">
                        <i class="fas fa-plus me-1"></i>Подати оголошення
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Alert Container for notifications -->
<div id="alert-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1055;"></div>

<!-- Loading Spinner -->
<div id="page-loader" class="d-none">
    <div class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center" 
         style="background: rgba(0,0,0,0.5); z-index: 9999;">
        <div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Завантаження...</span>
        </div>
    </div>
</div>