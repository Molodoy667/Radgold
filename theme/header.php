<?php
/**
 * Шапка сайта с современным SEO и мета-тегами
 */

// Установка значений по умолчанию
$page_title = $page_title ?? 'Главная страница';
$page_description = $page_description ?? SITE_DESCRIPTION;
$page_keywords = $page_keywords ?? SITE_KEYWORDS;
$page_image = $page_image ?? themeUrl('images/logo-og.jpg');
$canonical_url = $canonical_url ?? getCurrentUrl();
$schema_type = $schema_type ?? 'WebSite';
?>
<!DOCTYPE html>
<html lang="ru" prefix="og: http://ogp.me/ns#">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- SEO Meta Tags -->
    <title><?php echo h($page_title . ($page_title !== SITE_NAME ? ' - ' . SITE_NAME : '')); ?></title>
    <meta name="description" content="<?php echo h($page_description); ?>">
    <meta name="keywords" content="<?php echo h($page_keywords); ?>">
    <meta name="author" content="<?php echo h(SITE_NAME); ?>">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?php echo h($canonical_url); ?>">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo h($canonical_url); ?>">
    <meta property="og:title" content="<?php echo h($page_title); ?>">
    <meta property="og:description" content="<?php echo h($page_description); ?>">
    <meta property="og:image" content="<?php echo h($page_image); ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:site_name" content="<?php echo h(SITE_NAME); ?>">
    <meta property="og:locale" content="ru_RU">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="<?php echo h($canonical_url); ?>">
    <meta name="twitter:title" content="<?php echo h($page_title); ?>">
    <meta name="twitter:description" content="<?php echo h($page_description); ?>">
    <meta name="twitter:image" content="<?php echo h($page_image); ?>">
    
    <!-- Additional Meta Tags -->
    <meta name="theme-color" content="#2196F3">
    <meta name="msapplication-TileColor" content="#2196F3">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="<?php echo h(SITE_NAME); ?>">
    
    <!-- Favicons -->
    <link rel="icon" type="image/x-icon" href="<?php echo themeUrl('images/favicon.ico'); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo themeUrl('images/favicon-32x32.png'); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo themeUrl('images/favicon-16x16.png'); ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo themeUrl('images/apple-touch-icon.png'); ?>">
    <link rel="mask-icon" href="<?php echo themeUrl('images/safari-pinned-tab.svg'); ?>" color="#2196F3">
    
    <!-- Web App Manifest -->
    <link rel="manifest" href="/manifest.json">
    
    <!-- DNS Prefetch для внешних ресурсов -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    
    <!-- Preconnect для критических ресурсов -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS Frameworks -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUa6c3A3z3JqMGWE8qHHmT5W1i1pLbC5t1vKh1ixJfkV1xLQHB8A5VEzI0LR" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet" crossorigin="anonymous">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo themeUrl('css/style.css?v=' . filemtime(THEME_PATH . '/css/style.css')); ?>">
    
    <!-- CSRF Token для AJAX -->
    <meta name="csrf-token" content="<?php echo generateCSRFToken(); ?>">
    
    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "<?php echo h($schema_type); ?>",
        "name": "<?php echo h(SITE_NAME); ?>",
        "description": "<?php echo h(SITE_DESCRIPTION); ?>",
        "url": "<?php echo h(SITE_URL); ?>",
        "logo": "<?php echo themeUrl('images/logo.png'); ?>",
        "image": "<?php echo h($page_image); ?>",
        "sameAs": [
            "https://vk.com/marketplace",
            "https://t.me/marketplace",
            "https://instagram.com/marketplace"
        ],
        "potentialAction": {
            "@type": "SearchAction",
            "target": {
                "@type": "EntryPoint",
                "urlTemplate": "<?php echo SITE_URL; ?>/search?q={search_term_string}"
            },
            "query-input": "required name=search_term_string"
        }
    }
    </script>
    
    <!-- Analytics -->
    <?php if (defined('GOOGLE_ANALYTICS_ID') && GOOGLE_ANALYTICS_ID): ?>
    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo GOOGLE_ANALYTICS_ID; ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?php echo GOOGLE_ANALYTICS_ID; ?>', {
            anonymize_ip: true,
            allow_google_signals: false,
            allow_ad_personalization_signals: false
        });
    </script>
    <?php endif; ?>
    
    <?php if (defined('META_PIXEL_ID') && META_PIXEL_ID): ?>
    <!-- Meta Pixel -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '<?php echo META_PIXEL_ID; ?>');
        fbq('track', 'PageView');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=<?php echo META_PIXEL_ID; ?>&ev=PageView&noscript=1" />
    </noscript>
    <?php endif; ?>
    
    <!-- Critical CSS для быстрой загрузки -->
    <style>
        /* Предотвращение FOUC */
        .loading { opacity: 0; }
        .theme-transition * { transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease !important; }
        
        /* Критические стили для первого экрана */
        .navbar-3d {
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
        }
        
        /* Стили уведомлений */
        #notifications-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
        }
        
        .notification {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 20px;
            margin-bottom: 10px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            background: white;
            border-left: 4px solid;
            animation: slideInRight 0.3s ease-out;
        }
        
        .notification-success { border-left-color: #28a745; color: #155724; }
        .notification-error { border-left-color: #dc3545; color: #721c24; }
        .notification-warning { border-left-color: #ffc107; color: #856404; }
        .notification-info { border-left-color: #17a2b8; color: #0c5460; }
        
        .notification-close {
            background: none;
            border: none;
            color: inherit;
            opacity: 0.7;
            cursor: pointer;
            margin-left: auto;
            padding: 4px;
        }
        
        .notification-close:hover { opacity: 1; }
        
        /* Глобальный загрузчик */
        #global-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            backdrop-filter: blur(4px);
        }
        
        /* Кастомные тултипы */
        .custom-tooltip {
            position: absolute;
            background: #333;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 14px;
            z-index: 10000;
            pointer-events: none;
            white-space: nowrap;
        }
        
        .custom-tooltip::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 5px solid transparent;
            border-top-color: #333;
        }
        
        @media (max-width: 768px) {
            #notifications-container {
                left: 10px;
                right: 10px;
                max-width: none;
            }
        }
    </style>
</head>
<body class="loading" data-theme="light">
    <!-- Навигация -->
    <nav class="navbar navbar-3d navbar-expand-lg">
        <div class="container">
            <!-- Логотип -->
            <a class="navbar-brand" href="/">
                <img src="<?php echo themeUrl('images/logo.png'); ?>" 
                     alt="<?php echo h(SITE_NAME); ?> логотип" 
                     width="40" 
                     height="40" 
                     class="d-inline-block align-text-top me-2">
                <?php echo h(SITE_NAME); ?>
            </a>
            
            <!-- Кнопка мобильного меню -->
            <button class="navbar-toggler border-0" 
                    type="button" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#navbarNav" 
                    aria-controls="navbarNav" 
                    aria-expanded="false" 
                    aria-label="Переключить навигацию">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Меню навигации -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Основное меню -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo isCurrentPage('/') ? 'active' : ''; ?>" 
                           href="/">
                            <i class="fas fa-home icon-animated"></i>
                            Главная
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" 
                           href="#" 
                           role="button" 
                           data-bs-toggle="dropdown" 
                           aria-expanded="false">
                            <i class="fas fa-th-large icon-animated"></i>
                            Каталог
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/catalog/electronics">
                                <i class="fas fa-laptop"></i> Электроника
                            </a></li>
                            <li><a class="dropdown-item" href="/catalog/clothing">
                                <i class="fas fa-tshirt"></i> Одежда
                            </a></li>
                            <li><a class="dropdown-item" href="/catalog/home">
                                <i class="fas fa-home"></i> Дом и сад
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/catalog">
                                <i class="fas fa-list"></i> Все категории
                            </a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo isCurrentPage('/sellers') ? 'active' : ''; ?>" 
                           href="/sellers">
                            <i class="fas fa-store icon-animated"></i>
                            Продавцы
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo isCurrentPage('/about') ? 'active' : ''; ?>" 
                           href="/about">
                            <i class="fas fa-info-circle icon-animated"></i>
                            О нас
                        </a>
                    </li>
                </ul>
                
                <!-- Поиск -->
                <form class="d-flex mx-3 search-form" role="search">
                    <div class="search-container">
                        <input class="search-input" 
                               type="search" 
                               placeholder="Поиск товаров, продавцов..." 
                               aria-label="Поиск"
                               autocomplete="off">
                        <button class="search-btn" type="submit" aria-label="Найти">
                            <i class="fas fa-search"></i>
                        </button>
                        <div class="search-suggestions"></div>
                    </div>
                </form>
                
                <!-- Правое меню -->
                <ul class="navbar-nav">
                    <!-- Переключатель темы -->
                    <li class="nav-item">
                        <div class="theme-toggle" 
                             title="Переключить тему" 
                             role="button" 
                             tabindex="0" 
                             aria-label="Переключить между светлой и тёмной темой"></div>
                    </li>
                    
                    <!-- Избранное -->
                    <li class="nav-item">
                        <a class="nav-link position-relative" 
                           href="/favorites" 
                           title="Избранное">
                            <i class="fas fa-heart icon-animated"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger favorites-counter" style="display: none;">
                                0
                            </span>
                        </a>
                    </li>
                    
                    <!-- Корзина -->
                    <li class="nav-item">
                        <a class="nav-link position-relative" 
                           href="/cart" 
                           title="Корзина">
                            <i class="fas fa-shopping-cart icon-animated"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary cart-counter" style="display: none;">
                                0
                            </span>
                        </a>
                    </li>
                    
                    <!-- Пользователь -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" 
                           href="#" 
                           role="button" 
                           data-bs-toggle="dropdown" 
                           aria-expanded="false">
                            <i class="fas fa-user icon-animated"></i>
                            <?php echo h($_SESSION['user_name'] ?? 'Пользователь'); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/profile">
                                <i class="fas fa-user-circle"></i> Профиль
                            </a></li>
                            <li><a class="dropdown-item" href="/orders">
                                <i class="fas fa-box"></i> Мои заказы
                            </a></li>
                            <li><a class="dropdown-item" href="/seller/dashboard">
                                <i class="fas fa-chart-bar"></i> Продавец
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/logout">
                                <i class="fas fa-sign-out-alt"></i> Выйти
                            </a></li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/login">
                            <i class="fas fa-sign-in-alt icon-animated"></i>
                            Войти
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-3d btn-sm ms-2" href="/register">
                            Регистрация
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Breadcrumbs -->
    <?php if (isset($breadcrumbs) && !empty($breadcrumbs)): ?>
    <div class="container my-3">
        <nav class="breadcrumb-3d" aria-label="Навигация">
            <ol class="mb-0">
                <?php foreach ($breadcrumbs as $index => $breadcrumb): ?>
                    <?php if ($index === count($breadcrumbs) - 1): ?>
                        <li aria-current="page"><?php echo h($breadcrumb['name']); ?></li>
                    <?php else: ?>
                        <li>
                            <a href="<?php echo h($breadcrumb['url']); ?>">
                                <?php echo h($breadcrumb['name']); ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ol>
        </nav>
    </div>
    <?php endif; ?>
    
    <!-- Основной контент -->
    <main class="main-content"><?php echo "\n"; ?>