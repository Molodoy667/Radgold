<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="windows-1251">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Marketplace - Каталог</title>
    <link rel="stylesheet" href="/assets/css/theme.css">
    <link rel="stylesheet" href="/assets/css/products.css">
    <script src="/assets/js/theme.js"></script>
    <script src="/assets/js/filter.js"></script>
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
            <div class="filters">
                <h3>Фильтры</h3>
                <select id="gameFilter" onchange="applyFilters()">
                    <option value="">Все игры</option>
                    <option value="CS:GO">CS:GO</option>
                    <option value="Dota 2">Dota 2</option>
                    <option value="GTA V">GTA V</option>
                </select>
                <select id="typeFilter" onchange="applyFilters()">
                    <option value="">Все типы</option>
                    <option value="account">Аккаунты</option>
                    <option value="service">Услуги</option>
                    <option value="rent">Аренда</option>
                </select>
                <input type="number" id="minPrice" placeholder="Мин. цена" onchange="applyFilters()">
                <input type="number" id="maxPrice" placeholder="Макс. цена" onchange="applyFilters()">
            </div>

            <div id="productsList" class="products-grid">
                <?php foreach ($products as $product): ?>
                <div class="product-card" data-aos="fade-up">
                    <div class="product-image">
                        <img src="/assets/images/game-<?= strtolower(str_replace(' ', '-', $product['game'])) ?>.jpg" 
                             alt="<?= htmlspecialchars($product['game']) ?>" 
                             onerror="this.src='/assets/images/default.jpg'">
                        <div class="product-type"><?= ucfirst($product['type']) ?></div>
                    </div>
                    <div class="product-info">
                        <h3><?= htmlspecialchars($product['game']) ?></h3>
                        <p><?= htmlspecialchars(mb_substr($product['description'], 0, 100)) ?>...</p>
                        <div class="product-meta">
                            <span class="price"><?= number_format($product['price']) ?> ₽</span>
                            <span class="seller"><?= htmlspecialchars($product['seller_name']) ?></span>
                        </div>
                        <a href="/products/<?= $product['id'] ?>" class="btn-view">Подробнее</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </main>

    <script>
        function applyFilters() {
            const game = document.getElementById('gameFilter').value;
            const type = document.getElementById('typeFilter').value;
            const minPrice = document.getElementById('minPrice').value;
            const maxPrice = document.getElementById('maxPrice').value;

            const params = {};
            if (game) params.game = game;
            if (type) params.type = type;
            if (minPrice) params.min_price = minPrice;
            if (maxPrice) params.max_price = maxPrice;

            filterProducts(params);
        }
    </script>
</body>
</html>