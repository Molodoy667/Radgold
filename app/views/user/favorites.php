<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Избранное - Game Marketplace</title>
    <link rel="stylesheet" href="/assets/css/theme.css">
    <link rel="stylesheet" href="/assets/css/products.css">
    <link rel="stylesheet" href="/assets/css/profile.css">
    <script src="/assets/js/theme.js"></script>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <h1>🎮 Game Marketplace</h1>
            </div>
            <nav class="nav">
                <a href="/">Главная</a>
                <a href="/products">Каталог</a>
                <?php if (isset($_SESSION['user'])): ?>
                    <a href="/profile">Личный кабинет</a>
                    <a href="/logout">Выйти</a>
                <?php else: ?>
                    <a href="/login">Войти</a>
                <?php endif; ?>
            </nav>
            <div class="header-actions">
                <button onclick="toggleTheme()" class="btn-theme">🌙</button>
            </div>
        </div>
    </header>
    
    <main class="main-content">
        <div class="container">
            <div class="page-header">
                <h1>Избранное</h1>
                <div class="header-stats">
                    <div class="stat-card">
                        <span class="stat-icon">❤️</span>
                        <span class="stat-value"><?= $totalFavorites ?></span>
                        <span class="stat-label">Товаров в избранном</span>
                    </div>
                </div>
            </div>
            
            <div class="filters-section">
                <form method="GET" class="filter-form">
                    <div class="filter-row">
                        <div class="filter-group">
                            <select name="type">
                                <option value="">Все типы</option>
                                <option value="account" <?= ($_GET['type'] ?? '') === 'account' ? 'selected' : '' ?>>Аккаунт</option>
                                <option value="service" <?= ($_GET['type'] ?? '') === 'service' ? 'selected' : '' ?>>Услуга</option>
                                <option value="rental" <?= ($_GET['type'] ?? '') === 'rental' ? 'selected' : '' ?>>Аренда</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <select name="game">
                                <option value="">Все игры</option>
                                <?php foreach ($games as $game): ?>
                                    <option value="<?= htmlspecialchars($game) ?>" 
                                            <?= ($_GET['game'] ?? '') === $game ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($game) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <select name="sort">
                                <option value="date" <?= ($_GET['sort'] ?? 'date') === 'date' ? 'selected' : '' ?>>По дате добавления</option>
                                <option value="price_asc" <?= ($_GET['sort'] ?? 'date') === 'price_asc' ? 'selected' : '' ?>>По цене (возрастание)</option>
                                <option value="price_desc" <?= ($_GET['sort'] ?? 'date') === 'price_desc' ? 'selected' : '' ?>>По цене (убывание)</option>
                                <option value="rating" <?= ($_GET['sort'] ?? 'date') === 'rating' ? 'selected' : '' ?>>По рейтингу</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <input type="text" name="search" placeholder="Поиск по названию..." 
                                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        </div>
                        <button type="submit" class="btn-primary">Фильтровать</button>
                        <a href="/favorites" class="btn-secondary">Сбросить</a>
                    </div>
                </form>
            </div>
            
            <div class="favorites-section">
                <div class="favorites-header">
                    <h2>Избранные товары (<?= $totalFavorites ?> всего)</h2>
                    <div class="favorites-actions">
                        <button onclick="clearAllFavorites()" class="btn-danger">Очистить все</button>
                        <span class="pagination-info">
                            Страница <?= $currentPage ?> из <?= $totalPages ?>
                        </span>
                    </div>
                </div>
                
                <?php if (empty($favorites)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">❤️</div>
                        <h3>У вас пока нет избранных товаров</h3>
                        <p>Добавляйте товары в избранное, чтобы быстро находить их позже!</p>
                        <a href="/products" class="btn-primary">Перейти в каталог</a>
                    </div>
                <?php else: ?>
                    <div class="products-grid">
                        <?php foreach ($favorites as $favorite): ?>
                            <div class="product-card favorite-card">
                                <div class="product-images">
                                    <?php if ($favorite['images']): ?>
                                        <?php $images = json_decode($favorite['images'], true); ?>
                                        <img src="<?= htmlspecialchars($images[0] ?? '') ?>" 
                                             alt="<?= htmlspecialchars($favorite['title']) ?>" 
                                             class="product-image">
                                    <?php else: ?>
                                        <div class="no-image">Нет фото</div>
                                    <?php endif; ?>
                                    <div class="favorite-badge">
                                        <span class="heart-icon">❤️</span>
                                    </div>
                                    <div class="product-status">
                                        <span class="badge badge-<?= $favorite['status'] ?>">
                                            <?= $favorite['status'] === 'active' ? 'Активен' : 'Недоступен' ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="product-info">
                                    <div class="product-type">
                                        <span class="badge badge-<?= $favorite['type'] ?>">
                                            <?= $favorite['type'] === 'account' ? 'Аккаунт' : 
                                               ($favorite['type'] === 'service' ? 'Услуга' : 'Аренда') ?>
                                        </span>
                                    </div>
                                    <h3 class="product-title"><?= htmlspecialchars($favorite['title']) ?></h3>
                                    <p class="product-game"><?= htmlspecialchars($favorite['game']) ?></p>
                                    <p class="product-description"><?= htmlspecialchars(substr($favorite['description'], 0, 100)) ?>...</p>
                                    
                                    <div class="product-stats">
                                        <span class="stat">
                                            <span class="stat-icon">👁️</span>
                                            <?= number_format($favorite['views']) ?>
                                        </span>
                                        <span class="stat">
                                            <span class="stat-icon">⭐</span>
                                            <?= number_format($favorite['rating'], 1) ?>
                                        </span>
                                        <span class="stat">
                                            <span class="stat-icon">💬</span>
                                            <?= number_format($favorite['total_reviews']) ?>
                                        </span>
                                    </div>
                                    
                                    <div class="seller-info">
                                        <span class="seller-label">Продавец:</span>
                                        <span class="seller-name"><?= htmlspecialchars($favorite['seller_login']) ?></span>
                                    </div>
                                    
                                    <div class="product-price">
                                        <span class="price"><?= number_format($favorite['price']) ?></span>
                                        <span class="currency"><?= htmlspecialchars($favorite['currency']) ?></span>
                                    </div>
                                    
                                    <div class="product-actions">
                                        <a href="/products/<?= $favorite['id'] ?>" class="btn-primary">Просмотреть</a>
                                        <?php if ($favorite['status'] === 'active'): ?>
                                            <button onclick="buyProduct(<?= $favorite['id'] ?>)" class="btn-success">Купить</button>
                                        <?php endif; ?>
                                        <button onclick="removeFromFavorites(<?= $favorite['id'] ?>)" class="btn-danger">Удалить</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if ($totalPages > 1): ?>
                        <div class="pagination">
                            <?php if ($currentPage > 1): ?>
                                <a href="?page=<?= $currentPage - 1 ?>&type=<?= urlencode($_GET['type'] ?? '') ?>&game=<?= urlencode($_GET['game'] ?? '') ?>&sort=<?= urlencode($_GET['sort'] ?? 'date') ?>&search=<?= urlencode($_GET['search'] ?? '') ?>" 
                                   class="page-link">← Назад</a>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                                <a href="?page=<?= $i ?>&type=<?= urlencode($_GET['type'] ?? '') ?>&game=<?= urlencode($_GET['game'] ?? '') ?>&sort=<?= urlencode($_GET['sort'] ?? 'date') ?>&search=<?= urlencode($_GET['search'] ?? '') ?>" 
                                   class="page-link <?= $i === $currentPage ? 'active' : '' ?>"><?= $i ?></a>
                            <?php endfor; ?>
                            
                            <?php if ($currentPage < $totalPages): ?>
                                <a href="?page=<?= $currentPage + 1 ?>&type=<?= urlencode($_GET['type'] ?? '') ?>&game=<?= urlencode($_GET['game'] ?? '') ?>&sort=<?= urlencode($_GET['sort'] ?? 'date') ?>&search=<?= urlencode($_GET['search'] ?? '') ?>" 
                                   class="page-link">Вперед →</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <script>
        function removeFromFavorites(productId) {
            if (confirm('Удалить товар из избранного?')) {
                fetch('/toggle-favorite', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ product_id: productId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Ошибка: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Произошла ошибка');
                });
            }
        }
        
        function clearAllFavorites() {
            if (confirm('Очистить все избранное?')) {
                fetch('/favorites/clear-all', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Ошибка: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Произошла ошибка');
                });
            }
        }
        
        function buyProduct(productId) {
            if (confirm('Купить этот товар?')) {
                fetch('/products/buy', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ product_id: productId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Покупка успешно совершена!');
                        location.reload();
                    } else {
                        alert('Ошибка: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Произошла ошибка');
                });
            }
        }
    </script>
    
    <style>
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .page-header h1 {
            margin: 0;
            color: var(--text-primary);
        }
        
        .header-stats {
            display: flex;
            gap: 20px;
        }
        
        .stat-card {
            background: var(--bg-secondary);
            padding: 15px 20px;
            border-radius: 8px;
            text-align: center;
            min-width: 120px;
        }
        
        .stat-icon {
            font-size: 24px;
            display: block;
            margin-bottom: 5px;
        }
        
        .stat-value {
            display: block;
            font-size: 18px;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .stat-label {
            display: block;
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 2px;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: var(--bg-secondary);
            border-radius: 12px;
            margin: 40px 0;
        }
        
        .empty-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            margin: 0 0 10px 0;
            color: var(--text-primary);
        }
        
        .empty-state p {
            margin: 0 0 30px 0;
            color: var(--text-secondary);
        }
        
        .favorites-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .favorites-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .favorite-card {
            position: relative;
        }
        
        .favorite-badge {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        
        .heart-icon {
            font-size: 20px;
        }
        
        .product-card {
            background: var(--bg-secondary);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            position: relative;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }
        
        .product-images {
            position: relative;
            height: 200px;
            overflow: hidden;
        }
        
        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .no-image {
            width: 100%;
            height: 100%;
            background: var(--bg-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            font-size: 24px;
        }
        
        .product-status {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        
        .product-info {
            padding: 20px;
        }
        
        .product-type {
            margin-bottom: 10px;
        }
        
        .product-title {
            margin: 0 0 8px 0;
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .product-game {
            margin: 0 0 10px 0;
            color: var(--primary-color);
            font-weight: 500;
        }
        
        .product-description {
            margin: 0 0 15px 0;
            color: var(--text-secondary);
            line-height: 1.5;
        }
        
        .product-stats {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .stat {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            color: var(--text-secondary);
        }
        
        .stat-icon {
            font-size: 16px;
        }
        
        .seller-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
            margin-bottom: 15px;
        }
        
        .seller-label {
            font-size: 12px;
            color: var(--text-secondary);
        }
        
        .seller-name {
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .product-price {
            display: flex;
            align-items: baseline;
            gap: 5px;
            margin-bottom: 20px;
        }
        
        .price {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .currency {
            font-size: 16px;
            color: var(--text-secondary);
        }
        
        .product-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn-success {
            background: var(--success-color);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .btn-success:hover {
            background: var(--success-dark);
            transform: translateY(-2px);
        }
        
        .badge-active { background: var(--success-color); }
        .badge-inactive { background: var(--secondary-color); }
        
        .badge-account { background: var(--primary-color); }
        .badge-service { background: var(--success-color); }
        .badge-rental { background: var(--warning-color); }
        
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .header-stats {
                justify-content: center;
            }
            
            .favorites-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .favorites-actions {
                flex-direction: column;
            }
            
            .product-actions {
                flex-direction: column;
            }
            
            .product-stats {
                flex-wrap: wrap;
            }
        }
    </style>
</body>
</html>