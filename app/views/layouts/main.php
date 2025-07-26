<!DOCTYPE html>
<html lang="ru" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'GameMarket Pro' ?> - Игровой маркетплейс</title>
    
    <!-- Preload критичных ресурсов -->
    <link rel="preload" href="/assets/css/theme.css" as="style">
    <link rel="preload" href="/assets/js/app.js" as="script">
    
    <!-- CSS стили -->
    <link rel="stylesheet" href="/assets/css/theme.css">
    <link rel="stylesheet" href="/assets/css/components.css">
    <?php if (isset($additionalCSS)): ?>
        <?php foreach ($additionalCSS as $css): ?>
            <link rel="stylesheet" href="<?= $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Мета теги -->
    <meta name="description" content="Современный маркетплейс для покупки и продажи игровых аккаунтов, услуг бустинга, фарма и аренды">
    <meta name="keywords" content="игровые аккаунты, бустинг, фарм, аренда аккаунтов, игровые услуги">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">
    <link rel="icon" type="image/png" href="/assets/images/favicon.png">
    
    <!-- PWA Support -->
    <meta name="theme-color" content="#3b82f6">
    <link rel="manifest" href="/manifest.json">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="GameMarket">
    <link rel="apple-touch-icon" href="/assets/images/icon-192.png">
</head>
<body class="bg-background text-foreground">
    <!-- Лоадер страницы -->
    <div id="page-loader" class="fixed inset-0 z-50 flex items-center justify-center bg-background">
        <div class="loader-spinner"></div>
    </div>

    <!-- Навигация -->
    <header class="header sticky top-0 z-40 backdrop-blur-md bg-background/80 border-b border-border">
        <nav class="container mx-auto px-4 h-16 flex items-center justify-between">
            <!-- Логотип -->
            <div class="flex items-center space-x-4">
                <a href="/" class="flex items-center space-x-2 group">
                    <div class="w-8 h-8 bg-gradient-to-br from-primary to-secondary rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                        <span class="text-white font-bold text-sm">GM</span>
                    </div>
                    <span class="font-bold text-xl bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                        GameMarket Pro
                    </span>
                </a>
            </div>

            <!-- Основное меню -->
            <div class="hidden md:flex items-center space-x-6">
                <a href="/" class="nav-link">
                    <i class="icon-home"></i>
                    Главная
                </a>
                <a href="/catalog" class="nav-link">
                    <i class="icon-grid"></i>
                    Каталог
                </a>
                <?php if (isset($user) && $user): ?>
                    <a href="/profile" class="nav-link">
                        <i class="icon-user"></i>
                        Профиль
                    </a>
                    <a href="/messages" class="nav-link relative">
                        <i class="icon-message"></i>
                        Сообщения
                        <span class="unread-badge hidden absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Правая часть -->
            <div class="flex items-center space-x-4">
                <!-- Переключатель темы -->
                <button id="theme-toggle" class="btn-icon" title="Переключить тему">
                    <i class="icon-sun dark:hidden"></i>
                    <i class="icon-moon hidden dark:block"></i>
                </button>

                <?php if (isset($user) && $user): ?>
                    <!-- Баланс пользователя -->
                    <div class="hidden sm:flex items-center space-x-2 px-3 py-1 bg-card rounded-lg border">
                        <i class="icon-wallet text-primary"></i>
                        <span class="font-medium"><?= number_format($user['balance'] ?? 0, 2) ?> ₽</span>
                    </div>

                    <!-- Меню пользователя -->
                    <div class="relative">
                        <button id="user-menu-button" class="flex items-center space-x-2 p-2 rounded-lg hover:bg-card transition-colors">
                            <img src="<?= ($user['avatar'] ?? '') ?: '/assets/images/default-avatar.svg' ?>" 
                                 alt="Аватар" 
                                 class="w-8 h-8 rounded-full object-cover">
                            <i class="icon-chevron-down w-4 h-4"></i>
                        </button>
                        
                        <div id="user-menu" class="hidden absolute right-0 mt-2 w-48 bg-card border border-border rounded-lg shadow-lg py-1 z-50">
                            <a href="/profile" class="block px-4 py-2 hover:bg-accent transition-colors">
                                <i class="icon-user w-4 h-4 mr-2"></i>
                                Мой профиль
                            </a>
                            <a href="/my-products" class="block px-4 py-2 hover:bg-accent transition-colors">
                                <i class="icon-package w-4 h-4 mr-2"></i>
                                Мои товары
                            </a>
                            <a href="/favorites" class="block px-4 py-2 hover:bg-accent transition-colors">
                                <i class="icon-heart w-4 h-4 mr-2"></i>
                                Избранное
                            </a>
                            <hr class="my-1 border-border">
                            <a href="/settings" class="block px-4 py-2 hover:bg-accent transition-colors">
                                <i class="icon-settings w-4 h-4 mr-2"></i>
                                Настройки
                            </a>
                            <button onclick="logout()" class="block w-full text-left px-4 py-2 hover:bg-accent transition-colors text-red-500">
                                <i class="icon-logout w-4 h-4 mr-2"></i>
                                Выйти
                            </button>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Кнопки авторизации -->
                    <a href="/login" class="btn-secondary">Вход</a>
                    <a href="/register" class="btn-primary">Регистрация</a>
                <?php endif; ?>

                <!-- Мобильное меню -->
                <button id="mobile-menu-button" class="md:hidden btn-icon">
                    <i class="icon-menu"></i>
                </button>
            </div>
        </nav>

        <!-- Мобильное меню -->
        <div id="mobile-menu" class="hidden md:hidden border-t border-border bg-background">
            <div class="container mx-auto px-4 py-4 space-y-2">
                <a href="/" class="block py-2 px-4 rounded-lg hover:bg-accent transition-colors">Главная</a>
                <a href="/catalog" class="block py-2 px-4 rounded-lg hover:bg-accent transition-colors">Каталог</a>
                <?php if (isset($user) && $user): ?>
                    <a href="/profile" class="block py-2 px-4 rounded-lg hover:bg-accent transition-colors">Профиль</a>
                    <a href="/messages" class="block py-2 px-4 rounded-lg hover:bg-accent transition-colors">Сообщения</a>
                    <hr class="my-2 border-border">
                    <div class="px-4 py-2 text-sm text-muted-foreground">
                        Баланс: <?= number_format($user['balance'] ?? 0, 2) ?> ₽
                    </div>
                <?php else: ?>
                    <a href="/login" class="block py-2 px-4 rounded-lg hover:bg-accent transition-colors">Вход</a>
                    <a href="/register" class="block py-2 px-4 rounded-lg hover:bg-accent transition-colors">Регистрация</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Основной контент -->
    <main class="min-h-screen">
        <?= $content ?>
    </main>

    <!-- Футер -->
    <footer class="bg-card border-t border-border mt-auto">
        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="font-bold text-lg mb-4">GameMarket Pro</h3>
                    <p class="text-muted-foreground">
                        Современный маркетплейс для покупки и продажи игровых аккаунтов и услуг.
                    </p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Для покупателей</h4>
                    <ul class="space-y-2 text-muted-foreground">
                        <li><a href="/catalog" class="hover:text-primary transition-colors">Каталог товаров</a></li>
                        <li><a href="/help/buy" class="hover:text-primary transition-colors">Как купить</a></li>
                        <li><a href="/help/safety" class="hover:text-primary transition-colors">Безопасность</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Для продавцов</h4>
                    <ul class="space-y-2 text-muted-foreground">
                        <li><a href="/products/create" class="hover:text-primary transition-colors">Добавить товар</a></li>
                        <li><a href="/help/sell" class="hover:text-primary transition-colors">Как продавать</a></li>
                        <li><a href="/help/fees" class="hover:text-primary transition-colors">Комиссии</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Поддержка</h4>
                    <ul class="space-y-2 text-muted-foreground">
                        <li><a href="/help" class="hover:text-primary transition-colors">Справка</a></li>
                        <li><a href="/contacts" class="hover:text-primary transition-colors">Контакты</a></li>
                        <li><a href="/disputes" class="hover:text-primary transition-colors">Споры</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-8 border-border">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-muted-foreground">
                    © 2024 GameMarket Pro. Все права защищены.
                </p>
                <div class="flex space-x-4 mt-4 md:mt-0">
                    <a href="/privacy" class="text-muted-foreground hover:text-primary transition-colors">Конфиденциальность</a>
                    <a href="/terms" class="text-muted-foreground hover:text-primary transition-colors">Условия</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Уведомления -->
    <div id="notifications" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <!-- JavaScript -->
    <script src="/assets/js/app.js"></script>
    <?php if (isset($additionalJS)): ?>
        <?php foreach ($additionalJS as $js): ?>
            <script src="<?= $js ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <script>
        // Инициализация после загрузки страницы
        document.addEventListener('DOMContentLoaded', function() {
            // Скрыть лоадер
            document.getElementById('page-loader').style.display = 'none';
            
            // Инициализация компонентов
            App.init();
        });
    </script>
</body>
</html>