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
    
    // Отримуємо тему з сесії, cookies або використовуємо дефолтні
    $currentTheme = $_SESSION['current_theme'] ?? $_COOKIE['current_theme'] ?? 'light';
    $currentGradient = $_SESSION['current_gradient'] ?? $_COOKIE['current_gradient'] ?? 'gradient-1';
    
    // Оновлюємо сесію якщо є cookie
    if (!isset($_SESSION['current_theme']) && isset($_COOKIE['current_theme'])) {
        $_SESSION['current_theme'] = $_COOKIE['current_theme'];
    }
    if (!isset($_SESSION['current_gradient']) && isset($_COOKIE['current_gradient'])) {
        $_SESSION['current_gradient'] = $_COOKIE['current_gradient'];
    }
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

// Отримуємо всі градієнти (30 штук)
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
    'gradient-10' => 'linear-gradient(135deg, #fad0c4 0%, #ffd1ff 100%)',
    'gradient-11' => 'linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%)',
    'gradient-12' => 'linear-gradient(135deg, #0abde3 0%, #006ba6 100%)',
    'gradient-13' => 'linear-gradient(135deg, #ff9ff3 0%, #f368e0 100%)',
    'gradient-14' => 'linear-gradient(135deg, #54a0ff 0%, #2e86de 100%)',
    'gradient-15' => 'linear-gradient(135deg, #5f27cd 0%, #341f97 100%)',
    'gradient-16' => 'linear-gradient(135deg, #00d2d3 0%, #54a0ff 100%)',
    'gradient-17' => 'linear-gradient(135deg, #ff9ff3 0%, #ff6b9d 100%)',
    'gradient-18' => 'linear-gradient(135deg, #c44569 0%, #f8b500 100%)',
    'gradient-19' => 'linear-gradient(135deg, #40407a 0%, #706fd3 100%)',
    'gradient-20' => 'linear-gradient(135deg, #33d9b2 0%, #218c74 100%)',
    'gradient-21' => 'linear-gradient(135deg, #ff5722 0%, #ff9800 100%)',
    'gradient-22' => 'linear-gradient(135deg, #e91e63 0%, #9c27b0 100%)',
    'gradient-23' => 'linear-gradient(135deg, #2196f3 0%, #21cbf3 100%)',
    'gradient-24' => 'linear-gradient(135deg, #4caf50 0%, #8bc34a 100%)',
    'gradient-25' => 'linear-gradient(135deg, #ff4081 0%, #ff6ec7 100%)',
    'gradient-26' => 'linear-gradient(135deg, #673ab7 0%, #9c27b0 100%)',
    'gradient-27' => 'linear-gradient(135deg, #009688 0%, #4caf50 100%)',
    'gradient-28' => 'linear-gradient(135deg, #795548 0%, #8d6e63 100%)',
    'gradient-29' => 'linear-gradient(135deg, #607d8b 0%, #90a4ae 100%)',
    'gradient-30' => 'linear-gradient(135deg, #37474f 0%, #263238 100%)'
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
            --current-gradient: <?php echo $gradients[$currentGradient] ?? $gradients['gradient-1']; ?>;
            --theme-bg: <?php echo $currentTheme === 'dark' ? '#0d1117' : '#ffffff'; ?>;
            --theme-text: <?php echo $currentTheme === 'dark' ? '#f0f6fc' : '#24292f'; ?>;
            --theme-bg-secondary: <?php echo $currentTheme === 'dark' ? '#161b22' : '#f6f8fa'; ?>;
            --theme-bg-tertiary: <?php echo $currentTheme === 'dark' ? '#21262d' : '#ffffff'; ?>;
            --theme-border: <?php echo $currentTheme === 'dark' ? '#30363d' : '#d0d7de'; ?>;
            --theme-accent: <?php echo $currentTheme === 'dark' ? '#58a6ff' : '#0969da'; ?>;
            --theme-muted: <?php echo $currentTheme === 'dark' ? '#8b949e' : '#656d76'; ?>;
        }
        
        body {
            background-color: var(--theme-bg);
            color: var(--theme-text);
            transition: all 0.3s ease;
        }
        
        /* Touch Menu Button */
        .touch-menu-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            width: 50px;
            height: 50px;
            background: var(--current-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 1100;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        
        .touch-menu-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.25);
        }
        
        .touch-menu-btn .menu-icon {
            width: 20px;
            height: 15px;
            position: relative;
        }
        
        .touch-menu-btn .menu-icon span {
            display: block;
            position: absolute;
            height: 2px;
            width: 100%;
            background: white;
            border-radius: 1px;
            opacity: 1;
            left: 0;
            transform: rotate(0deg);
            transition: 0.25s ease-in-out;
        }
        
        .touch-menu-btn .menu-icon span:nth-child(1) {
            top: 0px;
        }
        
        .touch-menu-btn .menu-icon span:nth-child(2) {
            top: 6px;
        }
        
        .touch-menu-btn .menu-icon span:nth-child(3) {
            top: 12px;
        }
        
        .touch-menu-btn.open .menu-icon span:nth-child(1) {
            top: 6px;
            transform: rotate(135deg);
        }
        
        .touch-menu-btn.open .menu-icon span:nth-child(2) {
            opacity: 0;
            left: -60px;
        }
        
        .touch-menu-btn.open .menu-icon span:nth-child(3) {
            top: 6px;
            transform: rotate(-135deg);
        }
        
        /* Top Header */
        .top-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 70px;
            background: var(--current-gradient);
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.15);
            z-index: 1050;
            transition: all 0.3s ease;
        }
        
        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 100%;
            padding: 0 20px;
            margin-left: 70px; /* Space for touch menu button */
        }
        
        .gradient-title {
            font-weight: 800;
            font-size: 1.8rem;
            background: linear-gradient(45deg, rgba(255,255,255,0.9), rgba(255,255,255,0.6));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        
        .header-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 25px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            font-weight: 500;
            backdrop-filter: blur(10px);
        }
        
        /* Language Circles */
        .language-circles {
            display: flex;
            gap: 8px;
        }
        
        .lang-circle {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            border: 2px solid transparent;
            font-size: 1rem;
        }
        
        .lang-circle:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }
        
        .lang-circle.active {
            background: rgba(255, 255, 255, 0.4);
            border-color: rgba(255, 255, 255, 0.6);
            transform: scale(1.15);
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.3);
        }
        
        /* Touch Menu */
        .touch-menu-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
        }
        
        .touch-menu-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        
        .touch-menu {
            position: fixed;
            top: 0;
            left: -400px;
            width: 350px;
            height: 100%;
            background: var(--current-gradient);
            z-index: 1060;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            overflow-y: auto;
            box-shadow: 10px 0 30px rgba(0, 0, 0, 0.3);
        }
        
        .touch-menu.show {
            left: 0;
        }
        
        .touch-menu-header {
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }
        
        .menu-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            font-weight: 700;
        }
        
        .menu-close-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .menu-close-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: rotate(90deg);
        }
        
        /* Menu Sections */
        .menu-section {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 15px;
            opacity: 0.9;
        }
        
        /* Theme Switcher */
        .theme-switcher {
            display: flex;
            gap: 15px;
            justify-content: center;
        }
        
        .theme-option {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.2rem;
            color: white;
        }
        
        .theme-option:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }
        
        .theme-option.active {
            background: rgba(255, 255, 255, 0.4);
            transform: scale(1.15);
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.3);
        }
        
        /* Gradients Grid */
        .gradients-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 10px;
            max-height: 200px;
            overflow-y: auto;
            padding: 5px;
        }
        
        .gradient-circle {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.8rem;
        }
        
        .gradient-circle:hover {
            transform: scale(1.2);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }
        
        .gradient-circle.active {
            border-color: white;
            transform: scale(1.3);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
        }
        
        /* Language Selector */
        .language-selector {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .language-option {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 15px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            cursor: pointer;
            transition: all 0.3s ease;
            color: white;
        }
        
        .language-option:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(5px);
        }
        
        .language-option.active {
            background: rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.2);
        }
        
        .language-option .flag {
            font-size: 1.2rem;
        }
        
        .language-option .lang-name {
            font-weight: 500;
        }
        
        /* Navigation Menu */
        .nav-menu {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .nav-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 15px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .nav-item:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateX(8px);
            color: white;
            text-decoration: none;
        }
        
        .nav-item.active {
            background: rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.2);
        }
        
        .nav-item.logout:hover {
            background: rgba(255, 100, 100, 0.3);
        }
        
        .nav-item i {
            width: 20px;
            text-align: center;
            font-size: 1rem;
        }
        
        .nav-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.2);
            margin: 10px 0;
        }
        
        /* Адаптивні стилі */
        .main-content {
            margin-top: 70px;
            min-height: calc(100vh - 70px);
            padding: 0;
        }
        
        /* Dropdown стилі з покращеними анімаціями */
        .dropdown-menu {
            background: var(--theme-bg-tertiary) !important;
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid var(--theme-border);
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            animation: dropdownSlideIn 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            overflow: hidden;
            min-width: 200px;
        }
        
        .dark-theme .dropdown-menu {
            background: var(--theme-bg-tertiary) !important;
            border: 1px solid var(--theme-border);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
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
        
        /* Глобальні стилі для тем */
        .card {
            background-color: var(--theme-bg-tertiary) !important;
            border: 1px solid var(--theme-border) !important;
            color: var(--theme-text) !important;
        }
        
        .bg-light {
            background-color: var(--theme-bg-secondary) !important;
        }
        
        .text-muted {
            color: var(--theme-muted) !important;
        }
        
        .gradient-bg {
            background: var(--current-gradient) !important;
        }
        
        .text-gradient {
            background: var(--current-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .touch-menu {
                width: 100%;
                left: -100%;
            }
            
            .header-content {
                margin-left: 60px;
                padding: 0 15px;
            }
            
            .gradient-title {
                font-size: 1.4rem;
                letter-spacing: 0.5px;
            }
            
            .language-circles {
                gap: 6px;
            }
            
            .lang-circle {
                width: 30px;
                height: 30px;
                font-size: 0.9rem;
            }
            
            .user-info {
                padding: 6px 12px;
                font-size: 0.85rem;
            }
            
            .gradients-grid {
                grid-template-columns: repeat(5, 1fr);
                max-height: 150px;
            }
            
            .gradient-circle {
                width: 30px;
                height: 30px;
            }
        }
        
        @media (max-width: 480px) {
            .touch-menu-btn {
                top: 15px;
                left: 15px;
                width: 45px;
                height: 45px;
            }
            
            .top-header {
                height: 60px;
            }
            
            .main-content {
                margin-top: 60px;
            }
            
            .header-content {
                margin-left: 55px;
                padding: 0 10px;
            }
            
            .gradient-title {
                font-size: 1.2rem;
            }
            
            .language-circles {
                gap: 4px;
            }
            
            .lang-circle {
                width: 28px;
                height: 28px;
                font-size: 0.8rem;
            }
            
            .user-info {
                display: none; /* Приховуємо на дуже маленьких екранах */
            }
        }
        
        /* Scroll styles for touch menu */
        .touch-menu::-webkit-scrollbar {
            width: 6px;
        }
        
        .touch-menu::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .touch-menu::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }
        
        .touch-menu::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
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
    
    <!-- Touch Menu Button -->
    <div class="touch-menu-btn" id="touchMenuBtn">
        <div class="menu-icon">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>

    <!-- Top Header -->
    <header class="top-header">
        <div class="header-content">
            <div class="gradient-title">
                <?php echo getSiteName(); ?>
            </div>
            <div class="header-actions">
                <!-- Language Circles -->
                <div class="language-circles">
                    <div class="lang-circle <?php echo $currentLang === 'uk' ? 'active' : ''; ?>" data-lang="uk" title="Українська">
                        🇺🇦
                    </div>
                    <div class="lang-circle <?php echo $currentLang === 'ru' ? 'active' : ''; ?>" data-lang="ru" title="Русский">
                        🇷🇺
                    </div>
                    <div class="lang-circle <?php echo $currentLang === 'en' ? 'active' : ''; ?>" data-lang="en" title="English">
                        🇺🇸
                    </div>
                </div>
                
                <!-- User Info (if logged in) -->
                <?php if (function_exists('isLoggedIn') && isLoggedIn()): ?>
                    <div class="user-info">
                        <i class="fas fa-user-circle"></i>
                        <span><?php echo htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['username'] ?? 'User'); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Touch Menu Overlay -->
    <div class="touch-menu-overlay" id="touchMenuOverlay"></div>
    
    <!-- Touch Menu -->
    <div class="touch-menu" id="touchMenu">
        <!-- Menu Header -->
        <div class="touch-menu-header">
            <div class="menu-brand">
                <img src="<?php echo $metaTags['logo']; ?>" alt="<?php echo getSiteName(); ?>" height="30">
                <span><?php echo getSiteName(); ?></span>
            </div>
            <button class="menu-close-btn" id="menuCloseBtn">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <!-- Theme Toggle -->
        <div class="menu-section">
            <div class="section-title">
                <i class="fas fa-palette"></i>
                <span><?php echo safeTranslate('theme', 'Тема'); ?></span>
            </div>
            <div class="theme-switcher">
                <div class="theme-option <?php echo $currentTheme === 'light' ? 'active' : ''; ?>" data-theme="light">
                    <i class="fas fa-sun"></i>
                </div>
                <div class="theme-option <?php echo $currentTheme === 'dark' ? 'active' : ''; ?>" data-theme="dark">
                    <i class="fas fa-moon"></i>
                </div>
            </div>
        </div>
        
        <!-- Gradients -->
        <div class="menu-section">
            <div class="section-title">
                <i class="fas fa-brush"></i>
                <span><?php echo safeTranslate('gradient', 'Градієнти'); ?></span>
            </div>
            <div class="gradients-grid">
                <?php foreach ($gradients as $key => $gradient): ?>
                    <div class="gradient-circle <?php echo $currentGradient === $key ? 'active' : ''; ?>" 
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
        
        <!-- Language Selection -->
        <div class="menu-section">
            <div class="section-title">
                <i class="fas fa-globe"></i>
                <span><?php echo safeTranslate('language', 'Мова'); ?></span>
            </div>
            <div class="language-selector">
                <div class="language-option <?php echo $currentLang === 'uk' ? 'active' : ''; ?>" data-lang="uk">
                    <span class="flag">🇺🇦</span>
                    <span class="lang-name">Українська</span>
                </div>
                <div class="language-option <?php echo $currentLang === 'ru' ? 'active' : ''; ?>" data-lang="ru">
                    <span class="flag">🇷🇺</span>
                    <span class="lang-name">Русский</span>
                </div>
                <div class="language-option <?php echo $currentLang === 'en' ? 'active' : ''; ?>" data-lang="en">
                    <span class="flag">🇺🇸</span>
                    <span class="lang-name">English</span>
                </div>
            </div>
        </div>
        
        <!-- Navigation Menu -->
        <div class="menu-section">
            <div class="section-title">
                <i class="fas fa-bars"></i>
                <span><?php echo safeTranslate('menu', 'Меню'); ?></span>
            </div>
            <div class="nav-menu">
                <a href="<?php echo getSiteUrl(); ?>" class="nav-item">
                    <i class="fas fa-home"></i>
                    <span><?php echo safeTranslate('home', 'Головна'); ?></span>
                </a>
                <a href="<?php echo getSiteUrl('ads'); ?>" class="nav-item">
                    <i class="fas fa-bullhorn"></i>
                    <span><?php echo safeTranslate('ads', 'Оголошення'); ?></span>
                </a>
                <a href="<?php echo getSiteUrl('categories'); ?>" class="nav-item">
                    <i class="fas fa-th-large"></i>
                    <span><?php echo safeTranslate('categories', 'Категорії'); ?></span>
                </a>
                <a href="<?php echo getSiteUrl('services'); ?>" class="nav-item">
                    <i class="fas fa-cogs"></i>
                    <span><?php echo safeTranslate('services', 'Послуги'); ?></span>
                </a>
                <a href="<?php echo getSiteUrl('about'); ?>" class="nav-item">
                    <i class="fas fa-info-circle"></i>
                    <span><?php echo safeTranslate('about', 'Про нас'); ?></span>
                </a>
                <a href="<?php echo getSiteUrl('contact'); ?>" class="nav-item">
                    <i class="fas fa-envelope"></i>
                    <span><?php echo safeTranslate('contact', 'Контакти'); ?></span>
                </a>
                
                <?php if (function_exists('isLoggedIn') && isLoggedIn()): ?>
                    <div class="nav-divider"></div>
                    <a href="<?php echo getSiteUrl('profile'); ?>" class="nav-item">
                        <i class="fas fa-user"></i>
                        <span><?php echo safeTranslate('profile', 'Профіль'); ?></span>
                    </a>
                    <a href="<?php echo getSiteUrl('my-ads'); ?>" class="nav-item">
                        <i class="fas fa-list"></i>
                        <span><?php echo safeTranslate('my_ads', 'Мої оголошення'); ?></span>
                    </a>
                    <a href="<?php echo getSiteUrl('messages'); ?>" class="nav-item">
                        <i class="fas fa-comments"></i>
                        <span><?php echo safeTranslate('messages', 'Повідомлення'); ?></span>
                    </a>
                    <?php if (function_exists('isAdmin') && isAdmin()): ?>
                        <a href="<?php echo getSiteUrl('admin'); ?>" class="nav-item">
                            <i class="fas fa-cog"></i>
                            <span><?php echo safeTranslate('admin_panel', 'Адміністрування'); ?></span>
                        </a>
                    <?php endif; ?>
                    <a href="<?php echo getSiteUrl('logout'); ?>" class="nav-item logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span><?php echo safeTranslate('logout', 'Вихід'); ?></span>
                    </a>
                <?php else: ?>
                    <div class="nav-divider"></div>
                    <a href="<?php echo getSiteUrl('pages/login.php'); ?>" class="nav-item">
                        <i class="fas fa-sign-in-alt"></i>
                        <span><?php echo safeTranslate('login', 'Вхід'); ?></span>
                    </a>
                    <a href="<?php echo getSiteUrl('pages/register.php'); ?>" class="nav-item">
                        <i class="fas fa-user-plus"></i>
                        <span><?php echo safeTranslate('register', 'Реєстрація'); ?></span>
                    </a>
                <?php endif; ?>
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
    const loading = showLoadingIndicator('Зміна мови...');
    
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
            // Update loading text
            loading.querySelector('.loading-text').textContent = 'Оновлення...';
            
            // Smooth transition before reload
            document.body.style.opacity = '0.7';
            setTimeout(() => {
                location.reload();
            }, 800);
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

// Touch Menu Functionality
document.addEventListener('DOMContentLoaded', function() {
    const touchMenuBtn = document.getElementById('touchMenuBtn');
    const touchMenu = document.getElementById('touchMenu');
    const touchMenuOverlay = document.getElementById('touchMenuOverlay');
    const menuCloseBtn = document.getElementById('menuCloseBtn');
    
    // Застосовуємо поточну тему до body
    const currentTheme = '<?php echo $currentTheme; ?>';
    document.body.className = currentTheme + '-theme';
    document.documentElement.setAttribute('data-theme', currentTheme);
    document.documentElement.setAttribute('data-gradient', '<?php echo $currentGradient; ?>');
    
    // Touch/Swipe support
    let startX = 0;
    let isMenuOpen = false;
    
    // Open menu function
    function openMenu() {
        touchMenu.classList.add('show');
        touchMenuOverlay.classList.add('show');
        touchMenuBtn.classList.add('open');
        document.body.style.overflow = 'hidden';
        isMenuOpen = true;
    }
    
    // Close menu function
    function closeMenu() {
        touchMenu.classList.remove('show');
        touchMenuOverlay.classList.remove('show');
        touchMenuBtn.classList.remove('open');
        document.body.style.overflow = '';
        isMenuOpen = false;
    }
    
    // Menu button click
    if (touchMenuBtn) {
        touchMenuBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (isMenuOpen) {
                closeMenu();
            } else {
                openMenu();
            }
        });
    }
    
    // Close button click
    if (menuCloseBtn) {
        menuCloseBtn.addEventListener('click', closeMenu);
    }
    
    // Overlay click
    if (touchMenuOverlay) {
        touchMenuOverlay.addEventListener('click', closeMenu);
    }
    
    // ESC key to close menu
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isMenuOpen) {
            closeMenu();
        }
    });
    
    // Swipe to open/close
    document.addEventListener('touchstart', function(e) {
        startX = e.touches[0].clientX;
    });
    
    document.addEventListener('touchend', function(e) {
        const endX = e.changedTouches[0].clientX;
        const diffX = endX - startX;
        
        // Swipe right to open (from left edge)
        if (startX < 50 && diffX > 100 && !isMenuOpen) {
            openMenu();
        }
        // Swipe left to close
        else if (diffX < -100 && isMenuOpen) {
            closeMenu();
        }
    });
    
    // Theme options
    const themeOptions = document.querySelectorAll('.theme-option');
    themeOptions.forEach(option => {
        option.addEventListener('click', function() {
            const theme = this.dataset.theme;
            
            // Update active state immediately
            themeOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
            
            changeTheme(theme);
        });
    });
    
    // Gradient selection
    const gradientCircles = document.querySelectorAll('.gradient-circle');
    gradientCircles.forEach(circle => {
        circle.addEventListener('click', function() {
            const gradient = this.dataset.gradient;
            
            // Update active state immediately
            gradientCircles.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
            
            // Update menu background immediately for real-time preview
            touchMenu.style.background = this.style.background;
            touchMenuBtn.style.background = this.style.background;
            
            changeGradient(gradient);
        });
    });
    
    // Language options (in touch menu)
    const languageOptions = document.querySelectorAll('.language-option');
    languageOptions.forEach(option => {
        option.addEventListener('click', function() {
            const lang = this.dataset.lang;
            
            // Update active state immediately
            languageOptions.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
            
            changeLanguage(lang);
        });
    });
    
    // Language circles (in header)
    const langCircles = document.querySelectorAll('.lang-circle');
    langCircles.forEach(circle => {
        circle.addEventListener('click', function() {
            const lang = this.dataset.lang;
            
            // Update active state immediately
            langCircles.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
            
            changeLanguage(lang);
        });
    });
    
    // Set active nav item
    const currentPage = window.location.pathname;
    const navItems = document.querySelectorAll('.nav-item');
    
    navItems.forEach(item => {
        const href = item.getAttribute('href');
        if (href && (href === currentPage || (currentPage === '/' && href.includes('<?php echo getSiteUrl(); ?>')))) {
            item.classList.add('active');
        }
    });
});

// Change theme
function changeTheme(theme) {
    const loading = showLoadingIndicator('Зміна теми...');
    
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
            // Update loading text
            loading.querySelector('.loading-text').textContent = 'Застосування...';
            
            // Apply theme immediately after short delay for visual feedback
            setTimeout(() => {
                applyTheme(theme);
                loading.remove();
                console.log('Theme changed to:', theme);
            }, 400);
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

// Apply theme immediately
function applyTheme(theme) {
    document.body.className = theme + '-theme';
    document.documentElement.setAttribute('data-theme', theme);
    
    // Update CSS variables
    const root = document.documentElement;
    if (theme === 'dark') {
        root.style.setProperty('--theme-bg', '#0d1117');
        root.style.setProperty('--theme-text', '#f0f6fc');
        root.style.setProperty('--theme-bg-secondary', '#161b22');
        root.style.setProperty('--theme-bg-tertiary', '#21262d');
        root.style.setProperty('--theme-border', '#30363d');
        root.style.setProperty('--theme-accent', '#58a6ff');
        root.style.setProperty('--theme-muted', '#8b949e');
    } else {
        root.style.setProperty('--theme-bg', '#ffffff');
        root.style.setProperty('--theme-text', '#24292f');
        root.style.setProperty('--theme-bg-secondary', '#f6f8fa');
        root.style.setProperty('--theme-bg-tertiary', '#ffffff');
        root.style.setProperty('--theme-border', '#d0d7de');
        root.style.setProperty('--theme-accent', '#0969da');
        root.style.setProperty('--theme-muted', '#656d76');
    }
}

// Change gradient
function changeGradient(gradient) {
    const loading = showLoadingIndicator('Зміна градієнта...');
    
    // Градієнти (30 штук)
    const gradients = {
        'gradient-1': 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        'gradient-2': 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
        'gradient-3': 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
        'gradient-4': 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
        'gradient-5': 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
        'gradient-6': 'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
        'gradient-7': 'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)',
        'gradient-8': 'linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%)',
        'gradient-9': 'linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%)',
        'gradient-10': 'linear-gradient(135deg, #fad0c4 0%, #ffd1ff 100%)',
        'gradient-11': 'linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%)',
        'gradient-12': 'linear-gradient(135deg, #0abde3 0%, #006ba6 100%)',
        'gradient-13': 'linear-gradient(135deg, #ff9ff3 0%, #f368e0 100%)',
        'gradient-14': 'linear-gradient(135deg, #54a0ff 0%, #2e86de 100%)',
        'gradient-15': 'linear-gradient(135deg, #5f27cd 0%, #341f97 100%)',
        'gradient-16': 'linear-gradient(135deg, #00d2d3 0%, #54a0ff 100%)',
        'gradient-17': 'linear-gradient(135deg, #ff9ff3 0%, #ff6b9d 100%)',
        'gradient-18': 'linear-gradient(135deg, #c44569 0%, #f8b500 100%)',
        'gradient-19': 'linear-gradient(135deg, #40407a 0%, #706fd3 100%)',
        'gradient-20': 'linear-gradient(135deg, #33d9b2 0%, #218c74 100%)',
        'gradient-21': 'linear-gradient(135deg, #ff5722 0%, #ff9800 100%)',
        'gradient-22': 'linear-gradient(135deg, #e91e63 0%, #9c27b0 100%)',
        'gradient-23': 'linear-gradient(135deg, #2196f3 0%, #21cbf3 100%)',
        'gradient-24': 'linear-gradient(135deg, #4caf50 0%, #8bc34a 100%)',
        'gradient-25': 'linear-gradient(135deg, #ff4081 0%, #ff6ec7 100%)',
        'gradient-26': 'linear-gradient(135deg, #673ab7 0%, #9c27b0 100%)',
        'gradient-27': 'linear-gradient(135deg, #009688 0%, #4caf50 100%)',
        'gradient-28': 'linear-gradient(135deg, #795548 0%, #8d6e63 100%)',
        'gradient-29': 'linear-gradient(135deg, #607d8b 0%, #90a4ae 100%)',
        'gradient-30': 'linear-gradient(135deg, #37474f 0%, #263238 100%)'
    };
    
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
            // Update loading text
            loading.querySelector('.loading-text').textContent = 'Застосування...';
            
            // Apply gradient immediately after short delay for visual feedback
            setTimeout(() => {
                applyGradient(gradient, gradients[gradient]);
                loading.remove();
                console.log('Gradient changed to:', gradient);
            }, 400);
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

// Apply gradient immediately
function applyGradient(gradientKey, gradientValue) {
    document.documentElement.setAttribute('data-gradient', gradientKey);
    document.documentElement.style.setProperty('--current-gradient', gradientValue);
    
    // Update touch menu background
    const touchMenu = document.getElementById('touchMenu');
    const touchMenuBtn = document.getElementById('touchMenuBtn');
    const topHeader = document.querySelector('.top-header');
    
    if (touchMenu) {
        touchMenu.style.background = gradientValue;
    }
    if (touchMenuBtn) {
        touchMenuBtn.style.background = gradientValue;
    }
    if (topHeader) {
        topHeader.style.background = gradientValue;
    }
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
function showLoadingIndicator(message = '') {
    const loading = document.createElement('div');
    loading.className = 'loading-indicator';
    loading.innerHTML = `
        <div class="spinner"></div>
        <div class="loading-text">${message}</div>
    `;
    loading.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        z-index: 9999;
        text-align: center;
        min-width: 150px;
    `;
    
    // Add spinner styles if not already present
    if (!document.getElementById('spinner-styles')) {
        const style = document.createElement('style');
        style.id = 'spinner-styles';
        style.textContent = `
            .loading-indicator .spinner {
                width: 40px;
                height: 40px;
                border: 4px solid #e3e3e3;
                border-top: 4px solid var(--current-gradient, #007bff);
                border-radius: 50%;
                animation: spin 1s linear infinite;
                margin: 0 auto 15px;
            }
            
            .loading-indicator .loading-text {
                font-weight: 600;
                color: var(--theme-text, #333);
                font-size: 14px;
            }
            
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);
    }
    
    document.body.appendChild(loading);
    return loading;
}
</script>
