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
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
    <div id="page-loader" class="page-loader">
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
                    <i class="fas fa-home"></i>
                    Главная
                </a>
                <a href="/catalog" class="nav-link">
                    <i class="fas fa-th-large"></i>
                    Каталог
                </a>
                <?php if (isset($user) && $user): ?>
                    <a href="/profile" class="nav-link">
                        <i class="fas fa-user"></i>
                        Профиль
                    </a>
                    <a href="/messages" class="nav-link relative">
                        <i class="fas fa-envelope"></i>
                        Сообщения
                        <span class="unread-badge hidden absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Правая часть -->
            <div class="flex items-center space-x-4">
                <!-- Переключатель темы -->
                <button id="theme-toggle" class="btn-icon" title="Переключить тему">
                    <i class="fas fa-sun dark:hidden"></i>
                    <i class="fas fa-moon hidden dark:block"></i>
                </button>

                <?php if (isset($user) && $user): ?>
                    <!-- Баланс пользователя -->
                    <div class="hidden sm:flex items-center space-x-2 px-3 py-1 bg-card rounded-lg border">
                        <i class="fas fa-wallet text-primary"></i>
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
                                <i class="fas fa-user w-4 h-4 mr-2"></i>
                                Мой профиль
                            </a>
                            <a href="/my-products" class="block px-4 py-2 hover:bg-accent transition-colors">
                                <i class="fas fa-box w-4 h-4 mr-2"></i>
                                Мои товары
                            </a>
                            <a href="/my-favorites" class="block px-4 py-2 hover:bg-accent transition-colors">
                                <i class="fas fa-heart w-4 h-4 mr-2"></i>
                                Избранное
                            </a>
                            <hr class="my-1 border-border">
                            <a href="/user/settings" class="block px-4 py-2 hover:bg-accent transition-colors">
                                <i class="fas fa-cog w-4 h-4 mr-2"></i>
                                Настройки
                            </a>
                            <button onclick="logout()" class="block w-full text-left px-4 py-2 hover:bg-accent transition-colors text-red-500">
                                <i class="fas fa-sign-out-alt w-4 h-4 mr-2"></i>
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
                    <i class="fas fa-bars"></i>
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
    <footer class="bg-gradient-to-br from-card to-accent/5 border-t border-border mt-auto relative overflow-hidden">
        <!-- Фоновая анимация -->
        <div class="absolute inset-0 opacity-5">
            <div class="floating-shape bg-primary rounded-full w-32 h-32 absolute top-10 left-10"></div>
            <div class="floating-shape bg-secondary rounded-full w-24 h-24 absolute bottom-20 right-20"></div>
            <div class="floating-shape bg-accent rounded-full w-16 h-16 absolute top-1/2 left-1/2"></div>
        </div>
        
        <div class="container mx-auto px-4 py-12 relative">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-8">
                <!-- Лого и описание -->
                <div class="md:col-span-2">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-primary to-secondary rounded-xl flex items-center justify-center transform hover:rotate-12 transition-transform duration-300">
                            <span class="text-white font-bold text-lg">GM</span>
                        </div>
                        <h3 class="font-bold text-2xl bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                            GameMarket Pro
                        </h3>
                    </div>
                    <p class="text-muted-foreground mb-6 leading-relaxed">
                        Современный маркетплейс для покупки и продажи игровых аккаунтов, услуг бустинга и внутриигрового контента. Безопасно, быстро, надежно.
                    </p>
                    
                    <!-- Соцсети -->
                    <div class="flex space-x-4">
                        <a href="https://t.me/gamemarket_pro" class="social-link group" title="Telegram">
                            <i class="fab fa-telegram group-hover:scale-110 transition-transform"></i>
                        </a>
                        <a href="https://vk.com/gamemarket_pro" class="social-link group" title="VKontakte">
                            <i class="fab fa-vk group-hover:scale-110 transition-transform"></i>
                        </a>
                        <a href="https://discord.gg/gamemarket" class="social-link group" title="Discord">
                            <i class="fab fa-discord group-hover:scale-110 transition-transform"></i>
                        </a>
                        <a href="mailto:support@gamemarket.pro" class="social-link group" title="Email">
                            <i class="fas fa-envelope group-hover:scale-110 transition-transform"></i>
                        </a>
                        <a href="https://www.youtube.com/@gamemarket_pro" class="social-link group" title="YouTube">
                            <i class="fab fa-youtube group-hover:scale-110 transition-transform"></i>
                        </a>
                        <a href="https://twitter.com/gamemarket_pro" class="social-link group" title="Twitter">
                            <i class="fab fa-twitter group-hover:scale-110 transition-transform"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Для покупателей -->
                <div class="footer-column">
                    <h4 class="footer-title">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Для покупателей
                    </h4>
                    <ul class="footer-links">
                        <li><a href="/catalog" class="footer-link">
                            <i class="fas fa-th-large mr-2"></i>Каталог товаров
                        </a></li>
                        <li><a href="/help" class="footer-link">
                            <i class="fas fa-question-circle mr-2"></i>Как купить
                        </a></li>
                        <li><a href="/about" class="footer-link">
                            <i class="fas fa-shield-alt mr-2"></i>Безопасность
                        </a></li>
                        <li><a href="/disputes" class="footer-link">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Споры и жалобы
                        </a></li>
                    </ul>
                </div>
                
                <!-- Для продавцов -->
                <div class="footer-column">
                    <h4 class="footer-title">
                        <i class="fas fa-box mr-2"></i>
                        Для продавцов
                    </h4>
                    <ul class="footer-links">
                        <li><a href="/products/create" class="footer-link">
                            <i class="fas fa-plus mr-2"></i>Добавить товар
                        </a></li>
                        <li><a href="/help" class="footer-link">
                            <i class="fas fa-chart-line mr-2"></i>Как продавать
                        </a></li>
                        <li><a href="/help" class="footer-link">
                            <i class="fas fa-percentage mr-2"></i>Комиссии
                        </a></li>
                        <li><a href="/help" class="footer-link">
                            <i class="fas fa-star mr-2"></i>Рейтинг продавца
                        </a></li>
                    </ul>
                </div>
                
                <!-- Поддержка -->
                <div class="footer-column">
                    <h4 class="footer-title">
                        <i class="fas fa-headset mr-2"></i>
                        Поддержка
                    </h4>
                    <ul class="footer-links">
                        <li><a href="/help" class="footer-link">
                            <i class="fas fa-book mr-2"></i>База знаний
                        </a></li>
                        <li><a href="/contact" class="footer-link">
                            <i class="fas fa-comments mr-2"></i>Связаться с нами
                        </a></li>
                        <li><a href="https://t.me/gamemarket_support" class="footer-link">
                            <i class="fab fa-telegram mr-2"></i>Техподдержка
                        </a></li>
                        <li><a href="/status" class="footer-link">
                            <i class="fas fa-server mr-2"></i>Статус сервиса
                        </a></li>
                    </ul>
                </div>
            </div>
            
            <!-- Разделитель -->
            <div class="relative my-8">
                <hr class="border-border">
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="bg-card px-4">
                        <div class="flex space-x-2">
                            <div class="w-2 h-2 bg-primary rounded-full animate-pulse"></div>
                            <div class="w-2 h-2 bg-secondary rounded-full animate-pulse" style="animation-delay: 0.2s;"></div>
                            <div class="w-2 h-2 bg-accent rounded-full animate-pulse" style="animation-delay: 0.4s;"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Нижняя часть -->
            <div class="flex flex-col lg:flex-row justify-between items-center space-y-4 lg:space-y-0">
                <div class="flex flex-col sm:flex-row items-center space-y-2 sm:space-y-0 sm:space-x-6">
                    <p class="text-muted-foreground text-sm">
                        © 2024 GameMarket Pro. Все права защищены.
                    </p>
                    <div class="flex items-center space-x-2 text-sm text-muted-foreground">
                        <i class="fas fa-lock text-green-500"></i>
                        <span>SSL зашифровано</span>
                    </div>
                </div>
                
                <div class="flex flex-wrap items-center gap-4 text-sm">
                    <a href="/privacy" class="footer-bottom-link">
                        <i class="fas fa-user-shield mr-1"></i>Конфиденциальность
                    </a>
                    <a href="/terms" class="footer-bottom-link">
                        <i class="fas fa-file-contract mr-1"></i>Условия использования
                    </a>
                    <a href="/cookies" class="footer-bottom-link">
                        <i class="fas fa-cookie-bite mr-1"></i>Политика cookie
                    </a>
                </div>
            </div>
            
            <!-- Статистика -->
            <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                <div class="stat-item">
                    <div class="text-2xl font-bold text-primary">10K+</div>
                    <div class="text-sm text-muted-foreground">Пользователей</div>
                </div>
                <div class="stat-item">
                    <div class="text-2xl font-bold text-secondary">5K+</div>
                    <div class="text-sm text-muted-foreground">Товаров</div>
                </div>
                <div class="stat-item">
                    <div class="text-2xl font-bold text-green-500">99.9%</div>
                    <div class="text-sm text-muted-foreground">Uptime</div>
                </div>
                <div class="stat-item">
                    <div class="text-2xl font-bold text-yellow-500">4.9/5</div>
                    <div class="text-sm text-muted-foreground">Рейтинг</div>
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
        // Финальный fallback - принудительное скрытие лоадера
        window.addEventListener('load', function() {
            setTimeout(function() {
                const loader = document.getElementById('page-loader');
                if (loader && getComputedStyle(loader).visibility !== 'hidden') {
                    console.log('Финальный fallback - принудительное скрытие лоадера');
                    loader.style.display = 'none';
                }
            }, 4000);
        });
    </script>
</body>
</html>