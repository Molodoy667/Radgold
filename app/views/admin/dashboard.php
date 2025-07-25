<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="windows-1251">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ панель - Game Marketplace</title>
    <link rel="stylesheet" href="/assets/css/theme.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
    <script src="/assets/js/theme.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h2>🎮 Админ панель</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="/admin" class="nav-item active">
                    <span class="nav-icon">📊</span>
                    <span class="nav-text">Дашборд</span>
                </a>
                <a href="/admin/users" class="nav-item">
                    <span class="nav-icon">👥</span>
                    <span class="nav-text">Пользователи</span>
                </a>
                <a href="/admin/products" class="nav-item">
                    <span class="nav-icon">📦</span>
                    <span class="nav-text">Товары</span>
                </a>
                <a href="/admin/disputes" class="nav-item">
                    <span class="nav-icon">⚠️</span>
                    <span class="nav-text">Диспуты</span>
                </a>
                <a href="/admin/reviews" class="nav-item">
                    <span class="nav-icon">⭐</span>
                    <span class="nav-text">Отзывы</span>
                </a>
                <a href="/admin/settings" class="nav-item">
                    <span class="nav-icon">⚙️</span>
                    <span class="nav-text">Настройки</span>
                </a>
                <a href="/profile" class="nav-item">
                    <span class="nav-icon">👤</span>
                    <span class="nav-text">Профиль</span>
                </a>
                <a href="/logout" class="nav-item">
                    <span class="nav-icon">🚪</span>
                    <span class="nav-text">Выйти</span>
                </a>
            </nav>
        </aside>

        <main class="admin-main">
            <header class="admin-header">
                <div class="header-content">
                    <h1>📊 Дашборд</h1>
                    <div class="header-actions">
                        <button onclick="toggleTheme()" class="btn-theme">🌙</button>
                    </div>
                </div>
            </header>

            <div class="admin-content">
                <!-- Статистика -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">👥</div>
                        <div class="stat-content">
                            <h3>Пользователи</h3>
                            <div class="stat-number"><?= $stats['users']['total_users'] ?? 0 ?></div>
                            <div class="stat-details">
                                <span>Активных: <?= $stats['users']['active_users'] ?? 0 ?></span>
                                <span>Заблокированных: <?= $stats['users']['banned_users'] ?? 0 ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">📦</div>
                        <div class="stat-content">
                            <h3>Товары</h3>
                            <div class="stat-number"><?= $stats['products']['total_products'] ?? 0 ?></div>
                            <div class="stat-details">
                                <span>Активных: <?= $stats['products']['active_products'] ?? 0 ?></span>
                                <span>На модерации: <?= $stats['products']['pending_products'] ?? 0 ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">💰</div>
                        <div class="stat-content">
                            <h3>Продажи</h3>
                            <div class="stat-number"><?= $stats['purchases']['total_purchases'] ?? 0 ?></div>
                            <div class="stat-details">
                                <span>Выполнено: <?= $stats['purchases']['completed_purchases'] ?? 0 ?></span>
                                <span>Доход: <?= number_format($stats['purchases']['total_commission'] ?? 0, 2) ?> ₽</span>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">⚠️</div>
                        <div class="stat-content">
                            <h3>Диспуты</h3>
                            <div class="stat-number"><?= $stats['disputes']['total_disputes'] ?? 0 ?></div>
                            <div class="stat-details">
                                <span>Открытых: <?= $stats['disputes']['open_disputes'] ?? 0 ?></span>
                                <span>Срочных: <?= $stats['disputes']['urgent_disputes'] ?? 0 ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Графики -->
                <div class="charts-grid">
                    <div class="chart-card">
                        <h3>Продажи за неделю</h3>
                        <canvas id="salesChart" width="400" height="200"></canvas>
                    </div>

                    <div class="chart-card">
                        <h3>Популярные игры</h3>
                        <canvas id="gamesChart" width="400" height="200"></canvas>
                    </div>
                </div>

                <!-- Последняя активность -->
                <div class="activity-section">
                    <h3>Последняя активность</h3>
                    <div class="activity-list">
                        <?php foreach ($stats['recent_activity'] as $activity): ?>
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <?php
                                    switch ($activity['type']) {
                                        case 'user': echo '👤'; break;
                                        case 'product': echo '📦'; break;
                                        case 'purchase': echo '💰'; break;
                                        default: echo '📝';
                                    }
                                    ?>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title"><?= htmlspecialchars($activity['title']) ?></div>
                                    <div class="activity-description"><?= htmlspecialchars($activity['description']) ?></div>
                                    <div class="activity-time"><?= date('d.m.Y H:i', strtotime($activity['date'])) ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Быстрые действия -->
                <div class="quick-actions">
                    <h3>Быстрые действия</h3>
                    <div class="actions-grid">
                        <a href="/admin/users" class="action-card">
                            <div class="action-icon">👥</div>
                            <div class="action-text">Управление пользователями</div>
                        </a>
                        <a href="/admin/products?status=pending" class="action-card">
                            <div class="action-icon">📦</div>
                            <div class="action-text">Модерация товаров</div>
                        </a>
                        <a href="/admin/disputes" class="action-card">
                            <div class="action-icon">⚠️</div>
                            <div class="action-text">Обработка диспутов</div>
                        </a>
                        <a href="/admin/reviews" class="action-card">
                            <div class="action-icon">⭐</div>
                            <div class="action-text">Модерация отзывов</div>
                        </a>
                        <a href="/admin/settings" class="action-card">
                            <div class="action-icon">⚙️</div>
                            <div class="action-text">Настройки сайта</div>
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // График продаж
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'],
                datasets: [{
                    label: 'Продажи',
                    data: [12, 19, 3, 5, 2, 3, 7],
                    borderColor: 'rgb(99, 102, 241)',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // График популярных игр
        const gamesCtx = document.getElementById('gamesChart').getContext('2d');
        new Chart(gamesCtx, {
            type: 'doughnut',
            data: {
                labels: ['CS:GO', 'Dota 2', 'GTA V', 'LoL', 'Valorant'],
                datasets: [{
                    data: [30, 25, 15, 20, 10],
                    backgroundColor: [
                        '#6366f1',
                        '#8b5cf6',
                        '#ec4899',
                        '#f59e0b',
                        '#10b981'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
</body>
</html>