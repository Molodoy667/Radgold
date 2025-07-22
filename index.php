<?php
// Головний файл сайту
require_once 'core/config.php';
require_once 'core/database.php';
require_once 'core/functions.php';

// Отримуємо маршрут
$route = getRoute();

// Обробка маршрутів
switch ($route) {
    case '':
    case 'home':
        $page = 'pages/home.php';
        break;
        
    case 'ads':
        $page = 'pages/ads.php';
        break;
        
    case 'services':
        $page = 'pages/services.php';
        break;
        
    case 'about':
        $page = 'pages/about.php';
        break;
        
    case 'contact':
        $page = 'pages/contact.php';
        break;
        
    case 'login':
        $page = 'pages/login.php';
        break;
        
    case 'register':
        $page = 'pages/register.php';
        break;
        
    case 'logout':
        session_destroy();
        redirect(SITE_URL);
        break;
        
    case 'admin':
        if (!isLoggedIn()) {
            redirect(SITE_URL . '/admin/login.php');
        }
        if (!isAdmin()) {
            redirect(SITE_URL . '/login');
        }
        // Підключаємо адмін header замість звичайного
        require_once 'admin/dashboard.php';
        exit;
        break;
        
    default:
        // Перевіряємо чи це динамічна сторінка
        if (preg_match('/^ad\/(\d+)$/', $route, $matches)) {
            $adId = $matches[1];
            $page = 'pages/ad_detail.php';
        } elseif (preg_match('/^page\/(.+)$/', $route, $matches)) {
            $slug = $matches[1];
            $page = 'pages/page.php';
        } else {
            $page = 'pages/404.php';
        }
        break;
}

// Перевіряємо чи існує файл сторінки
if (!file_exists($page)) {
    $page = 'pages/404.php';
}

// Генеруємо CSRF токен
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = generateToken();
}

// Підключаємо header
require_once 'themes/header.php';

// Підключаємо сторінку
require_once $page;

// Підключаємо footer
require_once 'themes/footer.php';
?>
