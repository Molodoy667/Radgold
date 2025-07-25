<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="windows-1251">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Модерация товаров - Админ панель</title>
    <link rel="stylesheet" href="/assets/css/theme.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
    <script src="/assets/js/theme.js"></script>
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h2>🎮 Админ панель</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="/admin" class="nav-item">
                    <span class="nav-icon">📊</span>
                    <span class="nav-text">Дашборд</span>
                </a>
                <a href="/admin/users" class="nav-item">
                    <span class="nav-icon">👥</span>
                    <span class="nav-text">Пользователи</span>
                </a>
                <a href="/admin/products" class="nav-item active">
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
                <a href="/" class="nav-item">
                    <span class="nav-icon">🏠</span>
                    <span class="nav-text">На сайт</span>
                </a>
            </nav>
        </aside>
        
        <main class="admin-main">
            <header class="admin-header">
                <h1>Модерация товаров</h1>
                <div class="header-actions">
                    <span class="admin-info">Админ: <?= htmlspecialchars($_SESSION['user']['login']) ?></span>
                </div>
            </header>
            
            <div class="admin-content">
                <div class="filters-section">
                    <form method="GET" class="filter-form">
                        <div class="filter-row">
                            <div class="filter-group">
                                <input type="text" name="search" placeholder="Поиск по названию..." 
                                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                            </div>
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
                            <button type="submit" class="btn-primary">Фильтровать</button>
                            <a href="/admin/products" class="btn-secondary">Сбросить</a>
                        </div>
                    </form>
                </div>
                
                <div class="table-section">
                    <div class="table-header">
                        <h2>Товары (<?= $totalProducts ?> всего)</h2>
                        <div class="table-actions">
                            <span class="pagination-info">
                                Страница <?= $currentPage ?> из <?= $totalPages ?>
                            </span>
                        </div>
                    </div>
                    
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Изображение</th>
                                <th>Название</th>
                                <th>Продавец</th>
                                <th>Тип</th>
                                <th>Игра</th>
                                <th>Цена</th>
                                <th>Статус</th>
                                <th>Просмотры</th>
                                <th>Дата</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?= $product['id'] ?></td>
                                    <td>
                                        <?php if ($product['images']): ?>
                                            <?php $images = json_decode($product['images'], true); ?>
                                            <img src="<?= htmlspecialchars($images[0] ?? '') ?>" 
                                                 alt="Изображение" class="product-thumb">
                                        <?php else: ?>
                                            <div class="no-image">Нет фото</div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="product-info">
                                            <div class="product-title"><?= htmlspecialchars($product['title']) ?></div>
                                            <div class="product-desc"><?= htmlspecialchars(substr($product['description'], 0, 50)) ?>...</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="user-info">
                                            <span class="user-login"><?= htmlspecialchars($product['seller_login']) ?></span>
                                            <span class="user-email"><?= htmlspecialchars($product['seller_email']) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $product['type'] ?>">
                                            <?= $product['type'] === 'account' ? 'Аккаунт' : 
                                               ($product['type'] === 'service' ? 'Услуга' : 'Аренда') ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($product['game']) ?></td>
                                    <td>
                                        <div class="price-info">
                                            <span class="price"><?= number_format($product['price']) ?></span>
                                            <span class="currency"><?= htmlspecialchars($product['currency']) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $product['status'] ?>">
                                            <?= $product['status'] === 'pending' ? 'Ожидает' :
                                               ($product['status'] === 'active' ? 'Активен' :
                                               ($product['status'] === 'rejected' ? 'Отклонен' : 'Продан')) ?>
                                        </span>
                                    </td>
                                    <td><?= number_format($product['views']) ?></td>
                                    <td><?= date('d.m.Y H:i', strtotime($product['created_at'])) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <?php if ($product['status'] === 'pending'): ?>
                                                <button onclick="approveProduct(<?= $product['id'] ?>)" 
                                                        class="btn-action btn-success" title="Одобрить">
                                                    ✅
                                                </button>
                                                <button onclick="rejectProduct(<?= $product['id'] ?>)" 
                                                        class="btn-action btn-danger" title="Отклонить">
                                                    ❌
                                                </button>
                                            <?php endif; ?>
                                            <a href="/products/<?= $product['id'] ?>" 
                                               class="btn-action btn-info" title="Просмотреть">
                                                👁️
                                            </a>
                                            <button onclick="deleteProduct(<?= $product['id'] ?>)" 
                                                    class="btn-action btn-danger" title="Удалить">
                                                🗑️
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <?php if ($totalPages > 1): ?>
                        <div class="pagination">
                            <?php if ($currentPage > 1): ?>
                                <a href="?page=<?= $currentPage - 1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&status=<?= urlencode($_GET['status'] ?? '') ?>&type=<?= urlencode($_GET['type'] ?? '') ?>&game=<?= urlencode($_GET['game'] ?? '') ?>" 
                                   class="page-link">← Назад</a>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                                <a href="?page=<?= $i ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&status=<?= urlencode($_GET['status'] ?? '') ?>&type=<?= urlencode($_GET['type'] ?? '') ?>&game=<?= urlencode($_GET['game'] ?? '') ?>" 
                                   class="page-link <?= $i === $currentPage ? 'active' : '' ?>"><?= $i ?></a>
                            <?php endfor; ?>
                            
                            <?php if ($currentPage < $totalPages): ?>
                                <a href="?page=<?= $currentPage + 1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&status=<?= urlencode($_GET['status'] ?? '') ?>&type=<?= urlencode($_GET['type'] ?? '') ?>&game=<?= urlencode($_GET['game'] ?? '') ?>" 
                                   class="page-link">Вперед →</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        function approveProduct(productId) {
            if (confirm('Одобрить этот товар?')) {
                fetch('/admin/products/approve', {
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
        
        function rejectProduct(productId) {
            const reason = prompt('Причина отклонения:');
            if (reason !== null) {
                fetch('/admin/products/reject', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ 
                        product_id: productId,
                        reason: reason
                    })
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
                fetch('/admin/products/delete', {
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
        .product-thumb {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .no-image {
            width: 50px;
            height: 50px;
            background: var(--bg-secondary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            color: var(--text-secondary);
        }
        
        .product-info {
            max-width: 200px;
        }
        
        .product-title {
            font-weight: 600;
            margin-bottom: 4px;
        }
        
        .product-desc {
            font-size: 12px;
            color: var(--text-secondary);
        }
        
        .user-info {
            display: flex;
            flex-direction: column;
        }
        
        .user-login {
            font-weight: 600;
        }
        
        .user-email {
            font-size: 12px;
            color: var(--text-secondary);
        }
        
        .price-info {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }
        
        .price {
            font-weight: 600;
        }
        
        .currency {
            font-size: 12px;
            color: var(--text-secondary);
        }
        
        .badge-account { background: var(--primary-color); }
        .badge-service { background: var(--success-color); }
        .badge-rental { background: var(--warning-color); }
        .badge-pending { background: var(--warning-color); }
        .badge-active { background: var(--success-color); }
        .badge-rejected { background: var(--danger-color); }
        .badge-sold { background: var(--info-color); }
        
        .action-buttons {
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
        }
        
        .btn-action {
            padding: 6px 8px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .btn-success { background: var(--success-color); color: white; }
        .btn-danger { background: var(--danger-color); color: white; }
        .btn-info { background: var(--info-color); color: white; }
        
        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        @media (max-width: 768px) {
            .admin-table {
                font-size: 12px;
            }
            
            .product-info, .user-info {
                max-width: 120px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
</body>
</html>