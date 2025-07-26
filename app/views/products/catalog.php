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
                <span class="text-foreground font-medium">Каталог товаров</span>
            </nav>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Боковая панель с фильтрами -->
            <div class="lg:col-span-1">
                <div class="sticky top-20">
                    <div class="card p-6">
                        <h3 class="text-lg font-semibold mb-6 flex items-center">
                            <i class="icon-filter mr-2"></i>
                            Фильтры
                        </h3>
                        
                        <form id="filters-form" class="space-y-6">
                            <!-- Поиск -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium">Поиск</label>
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        name="search" 
                                        id="search-input"
                                        value="<?= htmlspecialchars($filters['search']) ?>"
                                        class="input-field pl-10" 
                                        placeholder="Поиск товаров..."
                                    >
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="icon-search w-4 h-4 text-muted-foreground"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Игра -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium">Игра</label>
                                <select name="game" class="input-field">
                                    <option value="">Все игры</option>
                                    <?php foreach ($games as $game): ?>
                                        <option value="<?= htmlspecialchars($game['game']) ?>" 
                                                <?= $filters['game'] === $game['game'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($game['game']) ?> (<?= $game['count'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Тип товара -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium">Тип товара</label>
                                <select name="type" class="input-field">
                                    <option value="">Все типы</option>
                                    <?php foreach ($types as $type): ?>
                                        <option value="<?= htmlspecialchars($type['type']) ?>" 
                                                <?= $filters['type'] === $type['type'] ? 'selected' : '' ?>>
                                            <?= ucfirst($type['type']) ?> (<?= $type['count'] ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Цена -->
                            <div class="space-y-4">
                                <label class="block text-sm font-medium">Цена (₽)</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <input 
                                            type="number" 
                                            name="min_price" 
                                            value="<?= htmlspecialchars($filters['min_price']) ?>"
                                            class="input-field" 
                                            placeholder="От"
                                            min="0"
                                        >
                                    </div>
                                    <div>
                                        <input 
                                            type="number" 
                                            name="max_price" 
                                            value="<?= htmlspecialchars($filters['max_price']) ?>"
                                            class="input-field" 
                                            placeholder="До"
                                            min="0"
                                        >
                                    </div>
                                </div>
                                
                                <!-- Быстрые фильтры по цене -->
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" class="price-filter-btn" data-min="0" data-max="1000">
                                        До 1К
                                    </button>
                                    <button type="button" class="price-filter-btn" data-min="1000" data-max="5000">
                                        1К - 5К
                                    </button>
                                    <button type="button" class="price-filter-btn" data-min="5000" data-max="15000">
                                        5К - 15К
                                    </button>
                                    <button type="button" class="price-filter-btn" data-min="15000" data-max="">
                                        15К+
                                    </button>
                                </div>
                            </div>

                            <!-- Кнопки действий -->
                            <div class="space-y-3">
                                <button type="submit" class="btn-primary w-full">
                                    <i class="icon-search mr-2"></i>
                                    Применить фильтры
                                </button>
                                <button type="button" id="clear-filters" class="btn-secondary w-full">
                                    <i class="icon-refresh mr-2"></i>
                                    Сбросить
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Популярные теги (если есть) -->
                    <div class="card p-6 mt-6">
                        <h4 class="text-md font-semibold mb-4">Популярные теги</h4>
                        <div class="flex flex-wrap gap-2">
                            <button class="tag-btn" data-search="топ аккаунт">топ аккаунт</button>
                            <button class="tag-btn" data-search="скины">скины</button>
                            <button class="tag-btn" data-search="быстро">быстро</button>
                            <button class="tag-btn" data-search="дешево">дешево</button>
                            <button class="tag-btn" data-search="качество">качество</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Основная область с товарами -->
            <div class="lg:col-span-3">
                <!-- Заголовок и сортировка -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <div>
                        <h1 class="text-2xl font-bold">Каталог товаров</h1>
                        <p class="text-muted-foreground mt-1">
                            Найдено товаров: <span id="total-count"><?= number_format($pagination['total_products']) ?></span>
                        </p>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- Вид отображения -->
                        <div class="flex bg-card border border-border rounded-lg p-1">
                            <button id="grid-view" class="view-btn active" data-view="grid" title="Сетка">
                                <i class="icon-grid w-4 h-4"></i>
                            </button>
                            <button id="list-view" class="view-btn" data-view="list" title="Список">
                                <i class="icon-list w-4 h-4"></i>
                            </button>
                        </div>
                        
                        <!-- Сортировка -->
                        <select name="sort" id="sort-select" class="input-field min-w-[200px]">
                            <option value="newest" <?= $filters['sort'] === 'newest' ? 'selected' : '' ?>>
                                Сначала новые
                            </option>
                            <option value="price_asc" <?= $filters['sort'] === 'price_asc' ? 'selected' : '' ?>>
                                Цена: по возрастанию
                            </option>
                            <option value="price_desc" <?= $filters['sort'] === 'price_desc' ? 'selected' : '' ?>>
                                Цена: по убыванию
                            </option>
                            <option value="rating" <?= $filters['sort'] === 'rating' ? 'selected' : '' ?>>
                                По рейтингу
                            </option>
                            <option value="popular" <?= $filters['sort'] === 'popular' ? 'selected' : '' ?>>
                                По популярности
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Активные фильтры -->
                <div id="active-filters" class="mb-6"></div>

                <!-- Загрузчик -->
                <div id="products-loader" class="hidden flex justify-center py-8">
                    <div class="loader-spinner"></div>
                </div>

                <!-- Товары -->
                <div id="products-container" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card card card-product" data-product-id="<?= $product['id'] ?>">
                            <!-- Изображение товара -->
                            <div class="relative mb-4 group">
                                <div class="aspect-square bg-gradient-to-br from-primary/20 to-secondary/20 rounded-lg overflow-hidden">
                                    <?php 
                                    $images = json_decode($product['images'] ?? '[]', true);
                                    if (!empty($images)): 
                                    ?>
                                        <img 
                                            src="/storage/uploads/<?= htmlspecialchars($images[0]) ?>" 
                                            alt="<?= htmlspecialchars($product['title']) ?>"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                            loading="lazy"
                                        >
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center">
                                            <span class="text-4xl">
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
                                
                                <!-- Бейджи -->
                                <div class="absolute top-2 left-2 space-y-1">
                                    <span class="badge badge-type">
                                        <?= ucfirst($product['type']) ?>
                                    </span>
                                    <?php if ($product['instant_delivery']): ?>
                                        <span class="badge badge-instant">⚡ Мгновенно</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="absolute top-2 right-2">
                                    <?php if (isset($_SESSION['user'])): ?>
                                        <button 
                                            class="favorite-btn btn-icon btn-icon-sm bg-white/90 hover:bg-white"
                                            data-product-id="<?= $product['id'] ?>"
                                            title="Добавить в избранное"
                                        >
                                            <i class="icon-heart w-4 h-4"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Информация о товаре -->
                            <div class="space-y-3">
                                <div>
                                    <h3 class="font-semibold text-lg line-clamp-2 mb-1">
                                        <a href="/product/<?= $product['id'] ?>" class="hover:text-primary transition-colors">
                                            <?= htmlspecialchars($product['title']) ?>
                                        </a>
                                    </h3>
                                    <p class="text-muted-foreground text-sm line-clamp-2">
                                        <?= htmlspecialchars($product['short_description'] ?? substr($product['description'], 0, 100) . '...') ?>
                                    </p>
                                </div>

                                <!-- Цена и рейтинг -->
                                <div class="flex items-center justify-between">
                                    <div class="space-y-1">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xl font-bold text-primary">
                                                <?= number_format($product['price'], 0) ?> ₽
                                            </span>
                                            <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
                                                <span class="text-sm text-muted-foreground line-through">
                                                    <?= number_format($product['original_price'], 0) ?> ₽
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <?php if ($product['total_reviews'] > 0): ?>
                                            <div class="flex items-center space-x-1">
                                                <div class="flex text-yellow-500">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <span class="<?= $i <= round($product['rating']) ? 'text-yellow-500' : 'text-gray-300' ?>">★</span>
                                                    <?php endfor; ?>
                                                </div>
                                                <span class="text-xs text-muted-foreground">
                                                    (<?= $product['total_reviews'] ?>)
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="text-right">
                                        <div class="text-xs text-muted-foreground">
                                            <?= $product['views'] ?> просмотров
                                        </div>
                                        <?php if ($product['favorites_count'] > 0): ?>
                                            <div class="text-xs text-muted-foreground">
                                                ♥ <?= $product['favorites_count'] ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Продавец -->
                                <div class="flex items-center justify-between pt-2 border-t border-border">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center text-white text-xs font-bold">
                                            <?= strtoupper(substr($product['seller_name'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium">
                                                <?= htmlspecialchars($product['seller_name']) ?>
                                            </div>
                                            <div class="flex items-center text-xs text-muted-foreground">
                                                <span class="text-yellow-500 mr-1">⭐</span>
                                                <?= number_format($product['seller_rating'], 1) ?>
                                                <span class="mx-1">•</span>
                                                <?= $product['total_sales'] ?> продаж
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <a 
                                        href="/product/<?= $product['id'] ?>" 
                                        class="btn-primary text-xs px-3 py-1"
                                    >
                                        Подробнее
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Сообщение если товары не найдены -->
                <?php if (empty($products)): ?>
                    <div class="text-center py-12">
                        <div class="text-6xl mb-4">🔍</div>
                        <h3 class="text-xl font-semibold mb-2">Товары не найдены</h3>
                        <p class="text-muted-foreground mb-6">
                            Попробуйте изменить параметры поиска или сбросить фильтры
                        </p>
                        <button id="clear-all-filters" class="btn-primary">
                            Сбросить все фильтры
                        </button>
                    </div>
                <?php endif; ?>

                <!-- Пагинация -->
                <?php if ($pagination['total_pages'] > 1): ?>
                    <div class="mt-12">
                        <nav class="flex justify-center">
                            <div class="flex items-center space-x-2">
                                <!-- Предыдущая страница -->
                                <?php if ($pagination['current_page'] > 1): ?>
                                    <a 
                                        href="?page=<?= $pagination['current_page'] - 1 ?>&<?= http_build_query(array_filter($filters)) ?>"
                                        class="pagination-btn"
                                        data-page="<?= $pagination['current_page'] - 1 ?>"
                                    >
                                        ← Назад
                                    </a>
                                <?php endif; ?>

                                <!-- Номера страниц -->
                                <?php 
                                $start = max(1, $pagination['current_page'] - 2);
                                $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
                                
                                for ($i = $start; $i <= $end; $i++): 
                                ?>
                                    <a 
                                        href="?page=<?= $i ?>&<?= http_build_query(array_filter($filters)) ?>"
                                        class="pagination-btn <?= $i === $pagination['current_page'] ? 'active' : '' ?>"
                                        data-page="<?= $i ?>"
                                    >
                                        <?= $i ?>
                                    </a>
                                <?php endfor; ?>

                                <!-- Следующая страница -->
                                <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                                    <a 
                                        href="?page=<?= $pagination['current_page'] + 1 ?>&<?= http_build_query(array_filter($filters)) ?>"
                                        class="pagination-btn"
                                        data-page="<?= $pagination['current_page'] + 1 ?>"
                                    >
                                        Вперед →
                                    </a>
                                <?php endif; ?>
                            </div>
                        </nav>
                        
                        <div class="text-center mt-4 text-sm text-muted-foreground">
                            Страница <?= $pagination['current_page'] ?> из <?= $pagination['total_pages'] ?>
                            (<?= number_format($pagination['total_products']) ?> товаров)
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filtersForm = document.getElementById('filters-form');
    const sortSelect = document.getElementById('sort-select');
    const productsContainer = document.getElementById('products-container');
    const productsLoader = document.getElementById('products-loader');
    const totalCount = document.getElementById('total-count');
    
    // Обработка фильтров
    filtersForm.addEventListener('submit', function(e) {
        e.preventDefault();
        applyFilters();
    });
    
    // Обработка сортировки
    sortSelect.addEventListener('change', function() {
        applyFilters();
    });
    
    // Быстрые фильтры по цене
    document.querySelectorAll('.price-filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const minPrice = this.dataset.min;
            const maxPrice = this.dataset.max;
            
            document.querySelector('input[name="min_price"]').value = minPrice;
            document.querySelector('input[name="max_price"]').value = maxPrice;
            
            applyFilters();
        });
    });
    
    // Популярные теги
    document.querySelectorAll('.tag-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('search-input').value = this.dataset.search;
            applyFilters();
        });
    });
    
    // Сброс фильтров
    document.getElementById('clear-filters').addEventListener('click', function() {
        filtersForm.reset();
        applyFilters();
    });
    
    // Избранное
    document.querySelectorAll('.favorite-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const productId = this.dataset.productId;
            toggleFavorite(productId, this);
        });
    });
    
    // Переключение вида
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.dataset.view;
            switchView(view);
        });
    });
    
    async function applyFilters() {
        showLoader();
        
        const formData = new FormData(filtersForm);
        formData.append('sort', sortSelect.value);
        formData.append('ajax', '1');
        
        const params = new URLSearchParams(formData);
        
        try {
            const response = await fetch('/catalog?' + params.toString());
            const data = await response.json();
            
            updateProducts(data.products);
            updatePagination(data.pagination);
            updateURL(params);
            
        } catch (error) {
            console.error('Ошибка загрузки товаров:', error);
            App.notification.show('Ошибка загрузки товаров', 'error');
        } finally {
            hideLoader();
        }
    }
    
    function updateProducts(products) {
        if (products.length === 0) {
            productsContainer.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <div class="text-6xl mb-4">🔍</div>
                    <h3 class="text-xl font-semibold mb-2">Товары не найдены</h3>
                    <p class="text-muted-foreground">Попробуйте изменить параметры поиска</p>
                </div>
            `;
            return;
        }
        
        // Обновляем HTML товаров (здесь должен быть код генерации карточек)
        // Для простоты перезагружаем страницу
        location.reload();
    }
    
    function updateURL(params) {
        const newURL = new URL(window.location);
        newURL.search = params.toString();
        window.history.pushState({}, '', newURL);
    }
    
    function showLoader() {
        productsLoader.classList.remove('hidden');
        productsContainer.style.opacity = '0.5';
    }
    
    function hideLoader() {
        productsLoader.classList.add('hidden');
        productsContainer.style.opacity = '1';
    }
    
    async function toggleFavorite(productId, button) {
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
                button.classList.toggle('favorited');
                App.notification.show(data.message, 'success');
            } else {
                App.notification.show(data.message, 'error');
            }
            
        } catch (error) {
            App.notification.show('Ошибка при добавлении в избранное', 'error');
        }
    }
    
    function switchView(view) {
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        document.querySelector(`[data-view="${view}"]`).classList.add('active');
        
        if (view === 'list') {
            productsContainer.classList.remove('grid-cols-1', 'md:grid-cols-2', 'xl:grid-cols-3');
            productsContainer.classList.add('space-y-4');
            // Добавить класс для списочного вида
        } else {
            productsContainer.classList.add('grid-cols-1', 'md:grid-cols-2', 'xl:grid-cols-3');
            productsContainer.classList.remove('space-y-4');
        }
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

.badge-instant {
    @apply bg-green-500 text-white;
}

.price-filter-btn {
    @apply px-3 py-1 text-xs border border-border rounded-full hover:bg-accent transition-colors;
}

.tag-btn {
    @apply px-3 py-1 text-xs bg-accent text-foreground rounded-full hover:bg-primary hover:text-white transition-colors cursor-pointer;
}

.view-btn {
    @apply p-2 rounded transition-colors;
}

.view-btn.active {
    @apply bg-primary text-white;
}

.view-btn:not(.active) {
    @apply text-muted-foreground hover:text-foreground;
}

.pagination-btn {
    @apply px-3 py-2 border border-border rounded hover:bg-accent transition-colors;
}

.pagination-btn.active {
    @apply bg-primary text-white border-primary;
}

.favorite-btn.favorited {
    @apply text-red-500;
}

.product-card {
    @apply transition-all duration-300 hover:shadow-lg;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<?php
$content = ob_get_clean();
$additionalCSS = ['/assets/css/catalog.css'];
$additionalJS = ['/assets/js/catalog.js'];
require_once __DIR__ . '/../layouts/main.php';
?>