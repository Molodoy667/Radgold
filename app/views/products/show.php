<?php
ob_start();
?>

<div class="min-h-screen bg-background">
    <!-- Хлебные крошки -->
    <div class="bg-card border-b border-border">
        <div class="container mx-auto px-4 py-4">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="/" class="text-muted-foreground hover:text-primary transition-colors">Главная</a>
                <span class="text-muted-foreground">•</span>
                <a href="/catalog" class="text-muted-foreground hover:text-primary transition-colors">Каталог</a>
                <span class="text-muted-foreground">•</span>
                <a href="/catalog?game=<?= urlencode($product['game']) ?>" class="text-muted-foreground hover:text-primary transition-colors">
                    <?= htmlspecialchars($product['game']) ?>
                </a>
                <span class="text-muted-foreground">•</span>
                <span class="text-foreground font-medium"><?= htmlspecialchars($product['title']) ?></span>
            </nav>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Галерея изображений -->
            <div class="lg:col-span-1">
                <div class="sticky top-20">
                    <div class="space-y-4">
                        <!-- Основное изображение -->
                        <div class="aspect-square bg-gradient-to-br from-primary/20 to-secondary/20 rounded-lg overflow-hidden">
                            <?php 
                            $images = json_decode($product['images'] ?? '[]', true);
                            if (!empty($images)): 
                            ?>
                                <img 
                                    id="main-image"
                                    src="/storage/uploads/<?= htmlspecialchars($images[0]) ?>" 
                                    alt="<?= htmlspecialchars($product['title']) ?>"
                                    class="w-full h-full object-cover"
                                >
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center">
                                    <span class="text-6xl">
                                        <?php 
                                        $gameIcons = [
                                            'valorant' => '🎯',
                                            'csgo' => '🔫', 
                                            'dota2' => '⚔️',
                                            'wow' => '🏰',
                                            'genshin' => '🌸',
                                            'lol' => '🌟',
                                            'default' => '🎮'
                                        ];
                                        echo $gameIcons[$product['game']] ?? $gameIcons['default'];
                                        ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Миниатюры изображений -->
                        <?php if (count($images) > 1): ?>
                            <div class="grid grid-cols-4 gap-2">
                                <?php foreach ($images as $index => $image): ?>
                                    <button 
                                        class="aspect-square bg-card border-2 border-border rounded-lg overflow-hidden hover:border-primary transition-colors thumbnail-btn <?= $index === 0 ? 'border-primary' : '' ?>"
                                        data-image="/storage/uploads/<?= htmlspecialchars($image) ?>"
                                    >
                                        <img 
                                            src="/storage/uploads/<?= htmlspecialchars($image) ?>" 
                                            alt="Изображение <?= $index + 1 ?>"
                                            class="w-full h-full object-cover"
                                        >
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Действия -->
                        <div class="space-y-3">
                            <?php if (isset($_SESSION['user'])): ?>
                                <button 
                                    id="favorite-btn"
                                    class="btn-secondary w-full flex items-center justify-center <?= $isFavorite ? 'favorited text-red-500' : '' ?>"
                                    data-product-id="<?= $product['id'] ?>"
                                >
                                    <i class="icon-heart mr-2"></i>
                                    <span><?= $isFavorite ? 'В избранном' : 'Добавить в избранное' ?></span>
                                </button>
                                
                                <button class="btn-secondary w-full flex items-center justify-center">
                                    <i class="icon-share mr-2"></i>
                                    Поделиться
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Основная информация -->
            <div class="lg:col-span-2">
                <div class="space-y-6">
                    <!-- Заголовок и базовая информация -->
                    <div>
                        <div class="flex items-start justify-between mb-4">
                            <div class="space-y-2">
                                <div class="flex items-center space-x-2">
                                    <span class="badge badge-type"><?= ucfirst($product['type']) ?></span>
                                    <span class="badge badge-game"><?= htmlspecialchars($product['game']) ?></span>
                                    <?php if ($product['instant_delivery']): ?>
                                        <span class="badge badge-instant">⚡ Мгновенная доставка</span>
                                    <?php endif; ?>
                                </div>
                                <h1 class="text-2xl lg:text-3xl font-bold"><?= htmlspecialchars($product['title']) ?></h1>
                            </div>
                            
                            <div class="text-right">
                                <div class="text-sm text-muted-foreground">ID: <?= $product['id'] ?></div>
                                <div class="text-sm text-muted-foreground"><?= $product['views'] ?> просмотров</div>
                            </div>
                        </div>

                        <!-- Рейтинг и отзывы -->
                        <?php if ($product['total_reviews'] > 0): ?>
                            <div class="flex items-center space-x-4 mb-4">
                                <div class="flex items-center space-x-1">
                                    <div class="flex text-yellow-500">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="<?= $i <= round($product['rating']) ? 'text-yellow-500' : 'text-gray-300' ?>">★</span>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="text-sm font-medium"><?= number_format($product['rating'], 1) ?></span>
                                </div>
                                <a href="#reviews" class="text-sm text-primary hover:underline">
                                    <?= $product['total_reviews'] ?> отзывов
                                </a>
                            </div>
                        <?php endif; ?>

                        <!-- Цена -->
                        <div class="flex items-center space-x-4 mb-6">
                            <div class="text-3xl font-bold text-primary">
                                <?= number_format($product['price'], 0) ?> ₽
                            </div>
                            <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
                                <div class="text-lg text-muted-foreground line-through">
                                    <?= number_format($product['original_price'], 0) ?> ₽
                                </div>
                                <div class="badge bg-red-500 text-white">
                                    -<?= round((($product['original_price'] - $product['price']) / $product['original_price']) * 100) ?>%
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Описание -->
                    <div class="card p-6">
                        <h3 class="text-lg font-semibold mb-4">Описание</h3>
                        <div class="prose prose-sm max-w-none">
                            <?= nl2br(htmlspecialchars($product['description'])) ?>
                        </div>
                    </div>

                    <!-- Характеристики -->
                    <?php 
                    $specifications = json_decode($product['specifications'] ?? '{}', true);
                    if (!empty($specifications)): 
                    ?>
                        <div class="card p-6">
                            <h3 class="text-lg font-semibold mb-4">Характеристики</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <?php foreach ($specifications as $key => $value): ?>
                                    <div class="flex justify-between py-2 border-b border-border">
                                        <span class="text-muted-foreground"><?= htmlspecialchars($key) ?>:</span>
                                        <span class="font-medium"><?= htmlspecialchars($value) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Информация о доставке -->
                    <?php if ($product['delivery_info'] || $product['delivery_time']): ?>
                        <div class="card p-6">
                            <h3 class="text-lg font-semibold mb-4 flex items-center">
                                <i class="icon-truck mr-2"></i>
                                Доставка
                            </h3>
                            <div class="space-y-2">
                                <?php if ($product['instant_delivery']): ?>
                                    <div class="flex items-center text-green-600">
                                        <i class="icon-zap mr-2"></i>
                                        Мгновенная доставка
                                    </div>
                                <?php elseif ($product['delivery_time']): ?>
                                    <div class="flex items-center">
                                        <i class="icon-clock mr-2"></i>
                                        Время доставки: <?= htmlspecialchars($product['delivery_time']) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($product['delivery_info']): ?>
                                    <p class="text-sm text-muted-foreground">
                                        <?= nl2br(htmlspecialchars($product['delivery_info'])) ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Продавец -->
                    <div class="card p-6">
                        <h3 class="text-lg font-semibold mb-4">Продавец</h3>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center text-white text-lg font-bold">
                                    <?= strtoupper(substr($product['seller_name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="font-semibold">
                                        <a href="/user/<?= $product['seller_id'] ?>" class="hover:text-primary transition-colors">
                                            <?= htmlspecialchars($product['seller_name']) ?>
                                        </a>
                                    </div>
                                    <div class="flex items-center text-sm text-muted-foreground">
                                        <span class="text-yellow-500 mr-1">⭐</span>
                                        <?= number_format($product['seller_rating'], 1) ?>
                                        <span class="mx-2">•</span>
                                        <?= $product['total_sales'] ?> продаж
                                    </div>
                                </div>
                            </div>
                            
                            <?php if (isset($_SESSION['user']) && $_SESSION['user']['id'] != $product['user_id']): ?>
                                <button class="btn-secondary">
                                    <i class="icon-message-circle mr-2"></i>
                                    Написать
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Кнопки действий -->
                    <div class="card p-6">
                        <?php if (isset($_SESSION['user'])): ?>
                            <?php if ($_SESSION['user']['id'] == $product['user_id']): ?>
                                <!-- Владелец товара -->
                                <div class="flex space-x-4">
                                    <a href="/products/<?= $product['id'] ?>/edit" class="btn-secondary flex-1">
                                        <i class="icon-edit mr-2"></i>
                                        Редактировать
                                    </a>
                                    <button class="btn-outline text-red-600 border-red-600 hover:bg-red-600 hover:text-white">
                                        <i class="icon-trash mr-2"></i>
                                        Удалить
                                    </button>
                                </div>
                            <?php else: ?>
                                <!-- Покупатель -->
                                <div class="space-y-4">
                                    <div class="flex space-x-4">
                                        <button class="btn-primary flex-1" id="buy-btn">
                                            <i class="icon-shopping-cart mr-2"></i>
                                            Купить за <?= number_format($product['price'], 0) ?> ₽
                                        </button>
                                        
                                        <?php if ($product['type'] === 'account'): ?>
                                            <button class="btn-secondary" id="rent-btn">
                                                <i class="icon-calendar mr-2"></i>
                                                Арендовать
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="text-xs text-muted-foreground text-center">
                                        Нажимая "Купить", вы соглашаетесь с 
                                        <a href="/terms" class="text-primary hover:underline">условиями использования</a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <!-- Неавторизованный пользователь -->
                            <div class="text-center">
                                <p class="text-muted-foreground mb-4">
                                    Войдите в аккаунт, чтобы приобрести товар
                                </p>
                                <div class="flex space-x-4">
                                    <a href="/login" class="btn-primary flex-1">Войти</a>
                                    <a href="/register" class="btn-secondary flex-1">Регистрация</a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Отзывы -->
        <?php if (!empty($reviews)): ?>
            <div class="mt-12" id="reviews">
                <div class="card p-6">
                    <h3 class="text-xl font-semibold mb-6">Отзывы покупателей</h3>
                    
                    <div class="space-y-6">
                        <?php foreach ($reviews as $review): ?>
                            <div class="border-b border-border pb-6 last:border-b-0">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center text-white text-sm font-bold">
                                            <?= strtoupper(substr($review['username'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="font-medium"><?= htmlspecialchars($review['username']) ?></div>
                                            <div class="flex items-center space-x-2 text-xs text-muted-foreground">
                                                <div class="flex text-yellow-500">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <span class="<?= $i <= $review['rating'] ? 'text-yellow-500' : 'text-gray-300' ?>">★</span>
                                                    <?php endfor; ?>
                                                </div>
                                                <span><?= date('d.m.Y', strtotime($review['created_at'])) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php if ($review['comment']): ?>
                                    <p class="text-sm"><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                                <?php endif; ?>
                                
                                <?php if ($review['reply']): ?>
                                    <div class="mt-3 pl-4 border-l-2 border-primary">
                                        <div class="text-xs text-muted-foreground mb-1">Ответ продавца:</div>
                                        <p class="text-sm"><?= nl2br(htmlspecialchars($review['reply'])) ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if ($canReview): ?>
                        <div class="mt-6 pt-6 border-t border-border">
                            <a href="/reviews/create/<?= $product['id'] ?>" class="btn-primary">
                                <i class="icon-star mr-2"></i>
                                Оставить отзыв
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Похожие товары -->
        <?php if (!empty($similarProducts)): ?>
            <div class="mt-12">
                <h3 class="text-xl font-semibold mb-6">Похожие товары</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <?php foreach ($similarProducts as $similar): ?>
                        <div class="card card-product">
                            <div class="relative mb-4">
                                <div class="aspect-square bg-gradient-to-br from-primary/20 to-secondary/20 rounded-lg overflow-hidden">
                                    <?php 
                                    $similarImages = json_decode($similar['images'] ?? '[]', true);
                                    if (!empty($similarImages)): 
                                    ?>
                                        <img 
                                            src="/storage/uploads/<?= htmlspecialchars($similarImages[0]) ?>" 
                                            alt="<?= htmlspecialchars($similar['title']) ?>"
                                            class="w-full h-full object-cover"
                                        >
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center">
                                            <span class="text-4xl">🎮</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <h4 class="font-semibold line-clamp-2">
                                    <a href="/product/<?= $similar['id'] ?>" class="hover:text-primary transition-colors">
                                        <?= htmlspecialchars($similar['title']) ?>
                                    </a>
                                </h4>
                                <div class="text-lg font-bold text-primary">
                                    <?= number_format($similar['price'], 0) ?> ₽
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Переключение изображений в галерее
    document.querySelectorAll('.thumbnail-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const newImageSrc = this.dataset.image;
            const mainImage = document.getElementById('main-image');
            
            if (mainImage && newImageSrc) {
                mainImage.src = newImageSrc;
                
                // Обновляем активную миниатюру
                document.querySelectorAll('.thumbnail-btn').forEach(b => {
                    b.classList.remove('border-primary');
                    b.classList.add('border-border');
                });
                this.classList.remove('border-border');
                this.classList.add('border-primary');
            }
        });
    });
    
    // Избранное
    const favoriteBtn = document.getElementById('favorite-btn');
    if (favoriteBtn) {
        favoriteBtn.addEventListener('click', async function() {
            const productId = this.dataset.productId;
            
            try {
                const response = await fetch('/favorites/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ product_id: productId })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    this.classList.toggle('favorited');
                    this.classList.toggle('text-red-500');
                    const span = this.querySelector('span');
                    span.textContent = data.in_favorites ? 'В избранном' : 'Добавить в избранное';
                    
                    App.notification.show(data.message, 'success');
                } else {
                    App.notification.show(data.message, 'error');
                }
                
            } catch (error) {
                App.notification.show('Ошибка при работе с избранным', 'error');
            }
        });
    }
    
    // Покупка товара
    const buyBtn = document.getElementById('buy-btn');
    if (buyBtn) {
        buyBtn.addEventListener('click', function() {
            // Здесь будет модальное окно покупки
            App.notification.show('Функция покупки будет добавлена позже', 'info');
        });
    }
    
    // Аренда товара
    const rentBtn = document.getElementById('rent-btn');
    if (rentBtn) {
        rentBtn.addEventListener('click', function() {
            // Здесь будет модальное окно аренды
            App.notification.show('Функция аренды будет добавлена позже', 'info');
        });
    }
});
</script>

<style>
.badge {
    @apply px-2 py-1 text-xs font-medium rounded-full;
}

.badge-type {
    @apply bg-primary text-white;
}

.badge-game {
    @apply bg-secondary text-white;
}

.badge-instant {
    @apply bg-green-500 text-white;
}

.favorited {
    @apply text-red-500;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.prose {
    color: rgb(var(--foreground));
}

.prose p {
    margin-bottom: 1rem;
}
</style>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
?>