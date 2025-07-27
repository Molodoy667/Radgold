<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo h(SITE_DESCRIPTION); ?>">
    <meta name="keywords" content="<?php echo h(SITE_KEYWORDS); ?>">
    <meta name="author" content="<?php echo h(SITE_NAME); ?>">
    
    <title><?php echo isset($page_title) ? h($page_title) . ' - ' . h(SITE_NAME) : h(SITE_NAME); ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo themeUrl('images/favicon.ico'); ?>">
    
    <!-- Стили -->
    <link rel="stylesheet" href="<?php echo themeUrl('css/bootstrap.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo themeUrl('css/style.css'); ?>">
    
    <!-- CSRF Token для AJAX -->
    <meta name="csrf-token" content="<?php echo generateCSRFToken(); ?>">
</head>
<body>
    <!-- Навигация -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?php echo SITE_URL; ?>">
                <img src="<?php echo themeUrl('images/logo.png'); ?>" alt="<?php echo h(SITE_NAME); ?>" height="40">
                <?php echo h(SITE_NAME); ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>">Главная</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/catalog">Каталог</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/about">О нас</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/contact">Контакты</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/cart">
                            <i class="fas fa-shopping-cart"></i> Корзина
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/login">Войти</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo SITE_URL; ?>/register">Регистрация</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Основной контент -->
    <main class="main-content"><?php // Здесь будет контент страниц ?>