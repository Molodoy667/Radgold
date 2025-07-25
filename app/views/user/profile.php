<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="windows-1251">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет - Game Marketplace</title>
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
                <a href="/products">Каталог</a>
                <a href="/profile" class="active">Личный кабинет</a>
                <a href="/chat">Сообщения</a>
                <a href="/logout">Выйти</a>
            </nav>
            <button onclick="toggleTheme()" class="btn-theme">🌙</button>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="profile-header">
                <div class="profile-avatar">
                    <img src="<?= $user['avatar'] ?? '/assets/images/default-avatar.png' ?>" alt="Аватар">
                </div>
                <div class="profile-info">
                    <h2><?= htmlspecialchars($user['login']) ?></h2>
                    <p class="profile-email"><?= htmlspecialchars($user['email']) ?></p>
                    <div class="profile-stats">
                        <span class="stat">
                            <strong>Рейтинг:</strong> <?= number_format($user['rating'], 1) ?> ⭐
                        </span>
                        <span class="stat">
                            <strong>Продажи:</strong> <?= $user['total_sales'] ?>
                        </span>
                        <span class="stat">
                            <strong>Баланс:</strong> <?= number_format($user['balance'], 2) ?> ₽
                        </span>
                    </div>
                </div>
            </div>

            <div class="profile-stats-grid">
                <div class="stat-card">
                    <h3>Покупки</h3>
                    <div class="stat-numbers">
                        <div class="stat-number"><?= $purchaseStats['total_purchases'] ?? 0 ?></div>
                        <div class="stat-label">Всего покупок</div>
                    </div>
                    <div class="stat-details">
                        <span>Выполнено: <?= $purchaseStats['completed_purchases'] ?? 0 ?></span>
                        <span>В ожидании: <?= $purchaseStats['pending_purchases'] ?? 0 ?></span>
                    </div>
                </div>

                <div class="stat-card">
                    <h3>Избранное</h3>
                    <div class="stat-numbers">
                        <div class="stat-number"><?= $favoritesCount ?></div>
                        <div class="stat-label">Товаров в избранном</div>
                    </div>
                </div>

                <div class="stat-card">
                    <h3>Сообщения</h3>
                    <div class="stat-numbers">
                        <div class="stat-number"><?= $unreadMessages ?></div>
                        <div class="stat-label">Непрочитанных</div>
                    </div>
                </div>
            </div>

            <div class="profile-actions">
                <h3>Быстрые действия</h3>
                <div class="action-buttons">
                    <a href="/my-products" class="btn-secondary">Мои товары</a>
                    <a href="/my-purchases" class="btn-secondary">Мои покупки</a>
                    <?php if ($user['role'] === 'seller'): ?>
                        <a href="/my-sales" class="btn-secondary">Мои продажи</a>
                    <?php endif; ?>
                    <a href="/favorites" class="btn-secondary">Избранное</a>
                    <a href="/chat" class="btn-secondary">Сообщения</a>
                    <a href="/settings" class="btn-secondary">Настройки</a>
                </div>
            </div>

            <?php if ($user['role'] === 'admin'): ?>
                <div class="admin-section">
                    <h3>Админ панель</h3>
                    <div class="action-buttons">
                        <a href="/admin" class="btn-admin">Дашборд</a>
                        <a href="/admin/users" class="btn-admin">Пользователи</a>
                        <a href="/admin/products" class="btn-admin">Товары</a>
                        <a href="/admin/disputes" class="btn-admin">Диспуты</a>
                        <a href="/admin/settings" class="btn-admin">Настройки</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>