<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="windows-1251">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог - Game Marketplace</title>
    <link rel="stylesheet" href="/assets/css/theme.css">
    <link rel="stylesheet" href="/assets/css/products.css">
    <script src="/assets/js/theme.js"></script>
    <script src="/assets/js/filter.js"></script>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <h1>🎮 Game Marketplace</h1>
            </div>
            <nav class="nav">
                <a href="/products" class="active">Каталог</a>
                <?php if (isset($_SESSION['user'])): ?>
                    <a href="/profile">Личный кабинет</a>
                    <a href="/chat">Сообщения</a>
                    <a href="/logout">Выйти</a>
                <?php else: ?>
                    <a href="/login">Вход</a>
                    <a href="/register">Регистрация</a>
                <?php endif; ?>
            </nav>
            <div class="header-actions">
                <?php if (isset($_SESSION['user'])): ?>
                    <a href="/products/create" class="btn-primary">Добавить товар</a>
                <?php endif; ?>
                <button onclick="toggleTheme()" class="btn-theme">🌙</button>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="filters-section">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label for="gameFilter">Игра</label>
                        <select id="gameFilter">
                            <option value="">Все игры</option>
                            <?php foreach ($games as $game): ?>
                                <option value="<?= htmlspecialchars($game) ?>"><?= htmlspecialchars($game) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="typeFilter">Тип</label>
                        <select id="typeFilter">
                            <option value="">Все типы</option>
                            <?php foreach ($types as $type): ?>
                                <option value="<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($type) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="minPrice">Мин. цена</label>
                        <input type="number" id="minPrice" placeholder="0" min="0">
                    </div>
                    
                    <div class="filter-group">
                        <label for="maxPrice">Макс. цена</label>
                        <input type="number" id="maxPrice" placeholder="∞" min="0">
                    </div>
                    
                    <div class="filter-group">
                        <label for="searchInput">Поиск</label>
                        <input type="text" id="searchInput" placeholder="Поиск товаров...">
                    </div>
                    
                    <div class="filter-group">
                        <button onclick="applyFilters()" class="btn-primary">Применить</button>
                        <button onclick="clearFilters()" class="btn-secondary">Сбросить</button>
                    </div>
                </div>
            </div>

            <div class="products-section">
                <div class="products-header">
                    <h2>Найдено товаров: <span id="productsCount"><?= count($products) ?></span></h2>
                    <div class="sort-controls">
                        <select id="sortSelect" onchange="applyFilters()">
                            <option value="newest">Сначала новые</option>
                            <option value="oldest">Сначала старые</option>
                            <option value="price_asc">Цена: по возрастанию</option>
                            <option value="price_desc">Цена: по убыванию</option>
                            <option value="rating">По рейтингу</option>
                        </select>
                    </div>
                </div>

                <div id="productsList" class="products-grid">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card" data-game="<?= htmlspecialchars($product['game']) ?>" data-type="<?= htmlspecialchars($product['type']) ?>" data-price="<?= $product['price'] ?>">
                            <div class="product-image">
                                <img src="<?= $product['images'] ? '/storage/products/' . $product['images'] : '/assets/images/default-product.jpg' ?>" alt="<?= htmlspecialchars($product['title']) ?>">
                                <div class="product-type-badge"><?= htmlspecialchars($product['type']) ?></div>
                            </div>
                            
                            <div class="product-info">
                                <h3 class="product-title"><?= htmlspecialchars($product['title']) ?></h3>
                                <p class="product-game"><?= htmlspecialchars($product['game']) ?></p>
                                <p class="product-description"><?= htmlspecialchars(substr($product['description'], 0, 100)) ?>...</p>
                                
                                <div class="product-meta">
                                    <div class="seller-info">
                                        <span class="seller-name"><?= htmlspecialchars($product['seller_name']) ?></span>
                                        <?php if ($product['seller_rating']): ?>
                                            <span class="seller-rating">⭐ <?= number_format($product['seller_rating'], 1) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="product-stats">
                                        <span class="views">👁 <?= $product['views'] ?></span>
                                        <?php if ($product['rating']): ?>
                                            <span class="rating">⭐ <?= number_format($product['rating'], 1) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="product-price">
                                    <span class="price"><?= number_format($product['price'], 0) ?> ₽</span>
                                    <?php if (isset($_SESSION['user'])): ?>
                                        <button onclick="toggleFavorite(<?= $product['id'] ?>)" class="btn-favorite <?= isset($_SESSION['user']) && \App\Models\Favorite::isFavorite($_SESSION['user']['id'], $product['id'], $GLOBALS['db']) ? 'active' : '' ?>">
                                            ❤
                                        </button>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="product-actions">
                                    <a href="/products/<?= $product['id'] ?>" class="btn-secondary">Подробнее</a>
                                    <?php if (isset($_SESSION['user']) && $_SESSION['user']['id'] != $product['user_id']): ?>
                                        <button onclick="buyProduct(<?= $product['id'] ?>)" class="btn-primary">Купить</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div id="loadingSpinner" class="loading-spinner" style="display: none;">
                    <div class="spinner"></div>
                    <p>Загрузка товаров...</p>
                </div>

                <div id="noProducts" class="no-products" style="display: none;">
                    <h3>Товары не найдены</h3>
                    <p>Попробуйте изменить фильтры поиска</p>
                </div>
            </div>
        </div>
    </main>

    <script>
        function buyProduct(productId) {
            if (!confirm('Вы уверены, что хотите купить этот товар?')) {
                return;
            }
            
            fetch('/products/buy', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Покупка успешно совершена!');
                    location.reload();
                } else {
                    alert(data.error || 'Ошибка при покупке');
                }
            })
            .catch(error => {
                alert('Ошибка сети');
            });
        }

        function toggleFavorite(productId) {
            fetch('/toggle-favorite', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const btn = event.target;
                    if (data.action === 'added') {
                        btn.classList.add('active');
                    } else {
                        btn.classList.remove('active');
                    }
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
            });
        }
    </script>
</body>
</html>
