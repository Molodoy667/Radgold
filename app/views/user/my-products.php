<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="windows-1251">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои товары - Game Marketplace</title>
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
                <h1>Мои товары</h1>
                <a href="/products/create" class="btn-primary">Добавить товар</a>
            </div>
            
            <div class="filters-section">
                <form method="GET" class="filter-form">
                    <div class="filter-row">
                        <div class="filter-group">
                            <select name="status">
                                <option value="">Все статусы</option>
                                <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Ожидает модерации</option>
                                <option value="active" <?= ($_GET['status'] ?? '') === 'active' ? 'selected' : '' ?>>Активные</option>
                                <option value="rejected" <?= ($_GET['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Отклоненные</option>
                                <option value="sold" <?= ($_GET['status'] ?? '') === 'sold' ? 'selected' : '' ?>>Проданные</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <select name="type">
                                <option value="">Все типы</option>
                                <option value="account" <?= ($_GET['type'] ?? '') === 'account' ? 'selected' : '' ?>>Аккаунт</option>
                                <option value="service" <?= ($_GET['type'] ?? '') === 'service' ? 'selected' : '' ?>>Услуга</option>
                                <option value="rental" <?= ($_GET['type'] ?? '') === 'rental' ? 'selected' : '' ?>>Аренда</option>
                            </select>
                        </div>
                        <div class="filter-group">
                            <input type="text" name="search" placeholder="Поиск по названию..." 
                                   value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                        </div>
                        <button type="submit" class="btn-primary">Фильтровать</button>
                        <a href="/my-products" class="btn-secondary">Сбросить</a>
                    </div>
                </form>
            </div>
            
            <div class="products-section">
                <div class="products-header">
                    <h2>Товары (<?= $totalProducts ?> всего)</h2>
                    <div class="products-actions">
                        <span class="pagination-info">
                            Страница <?= $currentPage ?> из <?= $totalPages ?>
                        </span>
                    </div>
                </div>
                
                <?php if (empty($products)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">📦</div>
                        <h3>У вас пока нет товаров</h3>
                        <p>Создайте свой первый товар и начните продавать!</p>
                        <a href="/products/create" class="btn-primary">Добавить товар</a>
                    </div>
                <?php else: ?>
                    <div class="products-grid">
                        <?php foreach ($products as $product): ?>
                            <div class="product-card">
                                <div class="product-images">
                                    <?php if ($product['images']): ?>
                                        <?php $images = json_decode($product['images'], true); ?>
                                        <img src="<?= htmlspecialchars($images[0] ?? '') ?>" 
                                             alt="<?= htmlspecialchars($product['title']) ?>" 
                                             class="product-image">
                                    <?php else: ?>
                                        <div class="no-image">Нет фото</div>
                                    <?php endif; ?>
                                    <div class="product-status">
                                        <span class="badge badge-<?= $product['status'] ?>">
                                            <?= $product['status'] === 'pending' ? 'Ожидает' :
                                               ($product['status'] === 'active' ? 'Активен' :
                                               ($product['status'] === 'rejected' ? 'Отклонен' : 'Продан')) ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="product-info">
                                    <div class="product-type">
                                        <span class="badge badge-<?= $product['type'] ?>">
                                            <?= $product['type'] === 'account' ? 'Аккаунт' : 
                                               ($product['type'] === 'service' ? 'Услуга' : 'Аренда') ?>
                                        </span>
                                    </div>
                                    <h3 class="product-title"><?= htmlspecialchars($product['title']) ?></h3>
                                    <p class="product-game"><?= htmlspecialchars($product['game']) ?></p>
                                    <p class="product-description"><?= htmlspecialchars(substr($product['description'], 0, 100)) ?>...</p>
                                    
                                    <div class="product-stats">
                                        <span class="stat">
                                            <span class="stat-icon">👁️</span>
                                            <?= number_format($product['views']) ?>
                                        </span>
                                        <span class="stat">
                                            <span class="stat-icon">⭐</span>
                                            <?= number_format($product['rating'], 1) ?>
                                        </span>
                                        <span class="stat">
                                            <span class="stat-icon">💬</span>
                                            <?= number_format($product['total_reviews']) ?>
                                        </span>
                                    </div>
                                    
                                    <div class="product-price">
                                        <span class="price"><?= number_format($product['price']) ?></span>
                                        <span class="currency"><?= htmlspecialchars($product['currency']) ?></span>
                                    </div>
                                    
                                    <div class="product-actions">
                                        <a href="/products/<?= $product['id'] ?>" class="btn-secondary">Просмотреть</a>
                                        <?php if ($product['status'] === 'active'): ?>
                                            <button onclick="editProduct(<?= $product['id'] ?>)" class="btn-primary">Редактировать</button>
                                        <?php endif; ?>
                                        <?php if ($product['status'] === 'rejected'): ?>
                                            <button onclick="resubmitProduct(<?= $product['id'] ?>)" class="btn-primary">Отправить повторно</button>
                                        <?php endif; ?>
                                        <button onclick="deleteProduct(<?= $product['id'] ?>)" class="btn-danger">Удалить</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if ($totalPages > 1): ?>
                        <div class="pagination">
                            <?php if ($currentPage > 1): ?>
                                <a href="?page=<?= $currentPage - 1 ?>&status=<?= urlencode($_GET['status'] ?? '') ?>&type=<?= urlencode($_GET['type'] ?? '') ?>&search=<?= urlencode($_GET['search'] ?? '') ?>" 
                                   class="page-link">← Назад</a>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                                <a href="?page=<?= $i ?>&status=<?= urlencode($_GET['status'] ?? '') ?>&type=<?= urlencode($_GET['type'] ?? '') ?>&search=<?= urlencode($_GET['search'] ?? '') ?>" 
                                   class="page-link <?= $i === $currentPage ? 'active' : '' ?>"><?= $i ?></a>
                            <?php endfor; ?>
                            
                            <?php if ($currentPage < $totalPages): ?>
                                <a href="?page=<?= $currentPage + 1 ?>&status=<?= urlencode($_GET['status'] ?? '') ?>&type=<?= urlencode($_GET['type'] ?? '') ?>&search=<?= urlencode($_GET['search'] ?? '') ?>" 
                                   class="page-link">Вперед →</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <script>
        function editProduct(productId) {
            window.location.href = `/products/edit/${productId}`;
        }
        
        function resubmitProduct(productId) {
            if (confirm('Отправить товар на повторную модерацию?')) {
                fetch('/products/resubmit', {
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
        
        function deleteProduct(productId) {
            if (confirm('Удалить этот товар навсегда?')) {
                fetch('/products/delete', {
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
        
        .btn-danger {
            background: var(--danger-color);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .btn-danger:hover {
            background: var(--danger-dark);
            transform: translateY(-2px);
        }
        
        .badge-pending { background: var(--warning-color); }
        .badge-active { background: var(--success-color); }
        .badge-rejected { background: var(--danger-color); }
        .badge-sold { background: var(--info-color); }
        
        .badge-account { background: var(--primary-color); }
        .badge-service { background: var(--success-color); }
        .badge-rental { background: var(--warning-color); }
        
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
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