<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="windows-1251">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Модерация отзывов - Админ панель</title>
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
                <a href="/admin/products" class="nav-item">
                    <span class="nav-icon">📦</span>
                    <span class="nav-text">Товары</span>
                </a>
                <a href="/admin/disputes" class="nav-item">
                    <span class="nav-icon">⚠️</span>
                    <span class="nav-text">Диспуты</span>
                </a>
                <a href="/admin/reviews" class="nav-item active">
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
                <h1>Модерация отзывов</h1>
                <div class="header-actions">
                    <span class="admin-info">Админ: <?= htmlspecialchars($_SESSION['user']['login']) ?></span>
                </div>
            </header>
            
            <div class="admin-content">
                <div class="filters-section">
                    <form method="GET" class="filter-form">
                        <div class="filter-row">
                            <div class="filter-group">
                                <input type="text" name="search" placeholder="Поиск по комментарию..." 
                                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                            </div>
                            <div class="filter-group">
                                <select name="status">
                                    <option value="">Все статусы</option>
                                    <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Ожидает модерации</option>
                                    <option value="approved" <?= ($_GET['status'] ?? '') === 'approved' ? 'selected' : '' ?>>Одобренные</option>
                                    <option value="rejected" <?= ($_GET['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Отклоненные</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <select name="rating">
                                    <option value="">Все оценки</option>
                                    <option value="5" <?= ($_GET['rating'] ?? '') === '5' ? 'selected' : '' ?>>⭐⭐⭐⭐⭐ (5)</option>
                                    <option value="4" <?= ($_GET['rating'] ?? '') === '4' ? 'selected' : '' ?>>⭐⭐⭐⭐ (4)</option>
                                    <option value="3" <?= ($_GET['rating'] ?? '') === '3' ? 'selected' : '' ?>>⭐⭐⭐ (3)</option>
                                    <option value="2" <?= ($_GET['rating'] ?? '') === '2' ? 'selected' : '' ?>>⭐⭐ (2)</option>
                                    <option value="1" <?= ($_GET['rating'] ?? '') === '1' ? 'selected' : '' ?>>⭐ (1)</option>
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
                            <a href="/admin/reviews" class="btn-secondary">Сбросить</a>
                        </div>
                    </form>
                </div>
                
                <div class="table-section">
                    <div class="table-header">
                        <h2>Отзывы (<?= $totalReviews ?> всего)</h2>
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
                                <th>Товар</th>
                                <th>Пользователь</th>
                                <th>Оценка</th>
                                <th>Заголовок</th>
                                <th>Комментарий</th>
                                <th>Статус</th>
                                <th>Дата</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reviews as $review): ?>
                                <tr>
                                    <td><?= $review['id'] ?></td>
                                    <td>
                                        <div class="product-info">
                                            <div class="product-title"><?= htmlspecialchars($review['product_title']) ?></div>
                                            <div class="product-game"><?= htmlspecialchars($review['product_game']) ?></div>
                                            <div class="product-price"><?= number_format($review['product_price']) ?> <?= htmlspecialchars($review['product_currency']) ?></div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="user-info">
                                            <span class="user-login"><?= htmlspecialchars($review['user_login']) ?></span>
                                            <span class="user-email"><?= htmlspecialchars($review['user_email']) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="rating-display">
                                            <div class="stars">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <span class="star <?= $i <= $review['rating'] ? 'filled' : '' ?>">⭐</span>
                                                <?php endfor; ?>
                                            </div>
                                            <span class="rating-number"><?= $review['rating'] ?>/5</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="review-title">
                                            <?= htmlspecialchars($review['title']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="review-comment">
                                            <?= htmlspecialchars(substr($review['comment'], 0, 100)) ?>
                                            <?= strlen($review['comment']) > 100 ? '...' : '' ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $review['status'] ?>">
                                            <?= $review['status'] === 'pending' ? 'Ожидает' :
                                               ($review['status'] === 'approved' ? 'Одобрен' : 'Отклонен') ?>
                                        </span>
                                    </td>
                                    <td><?= date('d.m.Y H:i', strtotime($review['created_at'])) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button onclick="viewReview(<?= $review['id'] ?>)" 
                                                    class="btn-action btn-info" title="Просмотреть">
                                                👁️
                                            </button>
                                            <?php if ($review['status'] === 'pending'): ?>
                                                <button onclick="approveReview(<?= $review['id'] ?>)" 
                                                        class="btn-action btn-success" title="Одобрить">
                                                    ✅
                                                </button>
                                                <button onclick="rejectReview(<?= $review['id'] ?>)" 
                                                        class="btn-action btn-danger" title="Отклонить">
                                                    ❌
                                                </button>
                                            <?php endif; ?>
                                            <button onclick="deleteReview(<?= $review['id'] ?>)" 
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
                                <a href="?page=<?= $currentPage - 1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&status=<?= urlencode($_GET['status'] ?? '') ?>&rating=<?= urlencode($_GET['rating'] ?? '') ?>&game=<?= urlencode($_GET['game'] ?? '') ?>" 
                                   class="page-link">← Назад</a>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                                <a href="?page=<?= $i ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&status=<?= urlencode($_GET['status'] ?? '') ?>&rating=<?= urlencode($_GET['rating'] ?? '') ?>&game=<?= urlencode($_GET['game'] ?? '') ?>" 
                                   class="page-link <?= $i === $currentPage ? 'active' : '' ?>"><?= $i ?></a>
                            <?php endfor; ?>
                            
                            <?php if ($currentPage < $totalPages): ?>
                                <a href="?page=<?= $currentPage + 1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&status=<?= urlencode($_GET['status'] ?? '') ?>&rating=<?= urlencode($_GET['rating'] ?? '') ?>&game=<?= urlencode($_GET['game'] ?? '') ?>" 
                                   class="page-link">Вперед →</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Модальное окно для просмотра отзыва -->
    <div id="reviewModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Детали отзыва</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body" id="reviewDetails">
                <!-- Содержимое будет загружено через AJAX -->
            </div>
        </div>
    </div>
    
    <script>
        function viewReview(reviewId) {
            fetch(`/admin/reviews/${reviewId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('reviewDetails').innerHTML = data.html;
                        document.getElementById('reviewModal').style.display = 'block';
                    } else {
                        alert('Ошибка: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Произошла ошибка');
                });
        }
        
        function closeModal() {
            document.getElementById('reviewModal').style.display = 'none';
        }
        
        function approveReview(reviewId) {
            if (confirm('Одобрить этот отзыв?')) {
                fetch('/admin/reviews/approve', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ review_id: reviewId })
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
        
        function rejectReview(reviewId) {
            const reason = prompt('Причина отклонения:');
            if (reason !== null) {
                fetch('/admin/reviews/reject', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ 
                        review_id: reviewId,
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
        
        function deleteReview(reviewId) {
            if (confirm('Удалить этот отзыв навсегда?')) {
                fetch('/admin/reviews/delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ review_id: reviewId })
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
        
        // Закрытие модального окна при клике вне его
        window.onclick = function(event) {
            const modal = document.getElementById('reviewModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>
    
    <style>
        .product-info {
            max-width: 200px;
        }
        
        .product-title {
            font-weight: 600;
            margin-bottom: 4px;
        }
        
        .product-game {
            font-size: 12px;
            color: var(--primary-color);
            margin-bottom: 2px;
        }
        
        .product-price {
            font-size: 12px;
            color: var(--text-secondary);
        }
        
        .rating-display {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .stars {
            display: flex;
            gap: 2px;
            margin-bottom: 4px;
        }
        
        .star {
            font-size: 14px;
            opacity: 0.3;
        }
        
        .star.filled {
            opacity: 1;
        }
        
        .rating-number {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
        }
        
        .review-title {
            font-weight: 600;
            margin-bottom: 4px;
        }
        
        .review-comment {
            font-size: 12px;
            color: var(--text-secondary);
            line-height: 1.4;
        }
        
        .badge-pending { background: var(--warning-color); }
        .badge-approved { background: var(--success-color); }
        .badge-rejected { background: var(--danger-color); }
        
        /* Модальное окно */
        .modal {
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: var(--bg-primary);
            margin: 5% auto;
            padding: 0;
            border-radius: 12px;
            width: 80%;
            max-width: 800px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }
        
        .modal-header {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-body {
            padding: 20px;
            max-height: 70vh;
            overflow-y: auto;
        }
        
        .close {
            color: var(--text-secondary);
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: var(--text-primary);
        }
        
        @media (max-width: 768px) {
            .admin-table {
                font-size: 12px;
            }
            
            .product-info {
                max-width: 120px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .modal-content {
                width: 95%;
                margin: 10% auto;
            }
        }
    </style>
</body>
</html>