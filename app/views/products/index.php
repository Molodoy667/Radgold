<?php
ob_start();
?>

<div class="main-content">
    <!-- Hero секция -->
    <section class="hero-section bg-gradient-to-br from-primary/20 to-secondary/20 py-20">
        <div class="container mx-auto px-4">
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-5xl md:text-7xl font-bold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent mb-6">
                    GameMarket Pro
                </h1>
                <p class="text-xl md:text-2xl text-muted-foreground mb-8 leading-relaxed">
                    Современный маркетплейс для игровых аккаунтов, услуг бустинга и внутриигрового контента
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-12">
                    <a href="/catalog" class="btn-primary group text-lg px-8 py-4">
                        <i class="icon-grid mr-2"></i>
                        Смотреть каталог
                        <div class="absolute inset-0 bg-gradient-to-r from-primary to-secondary opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </a>
                    <?php if (!$user): ?>
                        <a href="/register" class="btn-secondary text-lg px-8 py-4">
                            <i class="icon-user mr-2"></i>
                            Начать продавать
                        </a>
                    <?php endif; ?>
                </div>
                
                <!-- Статистика -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-16">
                    <div class="text-center">
                        <div class="text-3xl md:text-4xl font-bold text-primary mb-2"><?= number_format($totalUsers) ?>+</div>
                        <div class="text-muted-foreground">Активных пользователей</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl md:text-4xl font-bold text-primary mb-2">500+</div>
                        <div class="text-muted-foreground">Товаров в каталоге</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl md:text-4xl font-bold text-primary mb-2">24/7</div>
                        <div class="text-muted-foreground">Поддержка пользователей</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Популярные игры -->
    <section class="py-16 bg-card">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-12">Популярные игры</h2>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
                <?php 
                $games = [
                    ['name' => 'Valorant', 'image' => '🎯', 'count' => '150+'],
                    ['name' => 'CS:GO', 'image' => '🔫', 'count' => '200+'],
                    ['name' => 'Dota 2', 'image' => '⚔️', 'count' => '120+'],
                    ['name' => 'WoW', 'image' => '🏰', 'count' => '80+'],
                    ['name' => 'LoL', 'image' => '🌟', 'count' => '100+'],
                ];
                foreach ($games as $game): ?>
                    <a href="/catalog?game=<?= strtolower($game['name']) ?>" class="card card-product text-center p-6 hover:scale-105 transition-transform">
                        <div class="text-4xl mb-4"><?= $game['image'] ?></div>
                        <h3 class="font-semibold text-lg mb-2"><?= $game['name'] ?></h3>
                        <p class="text-muted-foreground text-sm"><?= $game['count'] ?> товаров</p>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Рекомендуемые товары -->
    <?php if (!empty($featuredProducts)): ?>
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold mb-4">Рекомендуемые товары</h2>
                <p class="text-muted-foreground text-lg">Лучшие предложения от проверенных продавцов</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($featuredProducts as $product): ?>
                    <div class="card card-product">
                        <div class="relative mb-4">
                            <div class="w-full h-48 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-lg flex items-center justify-center">
                                <span class="text-4xl">
                                    <?php 
                                    $gameIcons = [
                                        'valorant' => '🎯',
                                        'csgo' => '🔫',
                                        'dota2' => '⚔️',
                                        'wow' => '🏰',
                                        'genshin' => '🌸',
                                        'default' => '🎮'
                                    ];
                                    echo $gameIcons[$product['game']] ?? $gameIcons['default'];
                                    ?>
                                </span>
                            </div>
                            <div class="absolute top-2 left-2 px-2 py-1 bg-primary text-white text-xs rounded-full">
                                <?= ucfirst($product['type']) ?>
                            </div>
                            <?php if ($product['visibility'] === 'featured'): ?>
                                <div class="absolute top-2 right-2 px-2 py-1 bg-yellow-500 text-white text-xs rounded-full">
                                    ⭐ VIP
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <h3 class="font-semibold text-lg mb-2 line-clamp-2"><?= htmlspecialchars($product['title']) ?></h3>
                        <p class="text-muted-foreground text-sm mb-4 line-clamp-3"><?= htmlspecialchars($product['short_description'] ?? $product['description']) ?></p>
                        
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <span class="text-2xl font-bold text-primary"><?= number_format($product['price'], 0) ?> ₽</span>
                                <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
                                    <span class="text-sm text-muted-foreground line-through ml-2"><?= number_format($product['original_price'], 0) ?> ₽</span>
                                <?php endif; ?>
                            </div>
                            <div class="flex items-center">
                                <span class="text-yellow-500 mr-1">⭐</span>
                                <span class="text-sm"><?= number_format($product['seller_rating'], 1) ?></span>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-muted-foreground">
                                by <?= htmlspecialchars($product['seller_name']) ?>
                            </div>
                            <div class="flex space-x-2">
                                <?php if ($user): ?>
                                    <button onclick="toggleFavorite(<?= $product['id'] ?>)" class="btn-icon btn-icon-sm">
                                        <i class="icon-heart"></i>
                                    </button>
                                <?php endif; ?>
                                <a href="/product/<?= $product['id'] ?>" class="btn-primary px-4 py-2 text-sm">
                                    Подробнее
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-12">
                <a href="/catalog" class="btn-secondary">
                    Смотреть все товары
                    <i class="icon-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Преимущества платформы -->
    <section class="py-16 bg-card">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-12">Почему выбирают нас?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center text-2xl text-white mx-auto mb-4">
                        🛡️
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Безопасность</h3>
                    <p class="text-muted-foreground">Все сделки проходят через систему гарантий. Ваши средства в безопасности до получения товара.</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-2xl text-white mx-auto mb-4">
                        ⚡
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Быстрота</h3>
                    <p class="text-muted-foreground">Автоматическая доставка цифровых товаров. Получайте аккаунты мгновенно после оплаты.</p>
                </div>
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center text-2xl text-white mx-auto mb-4">
                        🎯
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Качество</h3>
                    <p class="text-muted-foreground">Только проверенные продавцы и товары. Система отзывов и рейтингов гарантирует качество.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Призыв к действию -->
    <section class="py-16 bg-gradient-to-r from-primary to-secondary">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Готовы начать?</h2>
            <p class="text-xl text-white/90 mb-8">Присоединяйтесь к тысячам игроков, которые уже используют нашу платформу</p>
            
            <?php if ($user): ?>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="/products/create" class="btn-secondary">
                        <i class="icon-plus mr-2"></i>
                        Добавить товар
                    </a>
                    <a href="/catalog" class="btn-secondary">
                        <i class="icon-search mr-2"></i>
                        Найти товар
                    </a>
                </div>
            <?php else: ?>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="/register" class="bg-white text-primary px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                        <i class="icon-user mr-2"></i>
                        Зарегистрироваться
                    </a>
                    <a href="/login" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-primary transition-colors">
                        <i class="icon-login mr-2"></i>
                        Войти
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<script>
// Добавляем недостающие иконки
document.addEventListener('DOMContentLoaded', function() {
    const style = document.createElement('style');
    style.textContent = `
        .icon-arrow-right::before { content: "→"; }
        .icon-plus::before { content: "+"; }
        .icon-search::before { content: "🔍"; }
        
        .line-clamp-2 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        
        .line-clamp-3 {
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }
        
        .btn-icon-sm {
            width: 2rem;
            height: 2rem;
            font-size: 0.875rem;
        }
    `;
    document.head.appendChild(style);
});

<?php if ($user): ?>
function toggleFavorite(productId) {
    // TODO: Реализовать добавление в избранное
    console.log('Toggle favorite for product:', productId);
    App.notification.show('Функция будет добавлена в следующих обновлениях', 'info');
}
<?php endif; ?>
</script>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
?>