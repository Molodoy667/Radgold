<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="windows-1251">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['game']) ?> - Game Marketplace</title>
    <link rel="stylesheet" href="/assets/css/theme.css">
    <link rel="stylesheet" href="/assets/css/products.css">
    <script src="/assets/js/theme.js"></script>
</head>
<body>
    <header class="header">
        <div class="container">
            <h1>Game Marketplace</h1>
            <nav>
                <a href="/">Главная</a>
                <a href="/products">Каталог</a>
                <?php if (isset($_SESSION['user'])): ?>
                    <a href="/profile">Личный кабинет</a>
                    <a href="/auth/logout">Выход</a>
                <?php else: ?>
                    <a href="/login">Вход</a>
                <?php endif; ?>
            </nav>
            <button onclick="toggleTheme()" class="theme-toggle">🌙</button>
        </div>
    </header>

    <main class="main">
        <div class="container">
            <div class="product-detail">
                <div class="product-gallery">
                    <img src="/assets/images/game-<?= strtolower(str_replace(' ', '-', $product['game'])) ?>.jpg" 
                         alt="<?= htmlspecialchars($product['game']) ?>" 
                         onerror="this.src='/assets/images/default.jpg'">
                </div>
                
                <div class="product-content">
                    <div class="product-header">
                        <h1><?= htmlspecialchars($product['game']) ?></h1>
                        <span class="product-type-badge"><?= ucfirst($product['type']) ?></span>
                    </div>
                    
                    <div class="product-price">
                        <span class="price-large"><?= number_format($product['price']) ?> ₽</span>
                    </div>
                    
                    <div class="product-description">
                        <h3>Описание</h3>
                        <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                    </div>
                    
                    <div class="product-meta">
                        <div class="meta-item">
                            <strong>Продавец:</strong> <?= htmlspecialchars($product['seller_name']) ?>
                        </div>
                        <div class="meta-item">
                            <strong>Просмотры:</strong> <?= $product['views'] ?>
                        </div>
                        <div class="meta-item">
                            <strong>Дата добавления:</strong> <?= date('d.m.Y', strtotime($product['created_at'])) ?>
                        </div>
                    </div>
                    
                    <div class="product-actions">
                        <?php if (isset($_SESSION['user'])): ?>
                            <button class="btn-buy" onclick="buyProduct(<?= $product['id'] ?>)">Купить</button>
                            <button class="btn-favorite" onclick="addToFavorites(<?= $product['id'] ?>)">❤️ В избранное</button>
                        <?php else: ?>
                            <a href="/login" class="btn-buy">Войти для покупки</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function buyProduct(productId) {
            if (confirm('Подтвердить покупку?')) {
                // Здесь будет логика покупки
                alert('Покупка оформлена!');
            }
        }
        
        function addToFavorites(productId) {
            // Здесь будет логика добавления в избранное
            alert('Добавлено в избранное!');
        }
    </script>
</body>
</html>