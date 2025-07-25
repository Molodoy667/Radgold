<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои покупки - Game Marketplace</title>
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
                <h1>Мои покупки</h1>
                <div class="header-stats">
                    <div class="stat-card">
                        <span class="stat-icon">💰</span>
                        <span class="stat-value"><?= number_format($totalSpent) ?></span>
                        <span class="stat-label">Потрачено всего</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-icon">📦</span>
                        <span class="stat-value"><?= $totalPurchases ?></span>
                        <span class="stat-label">Покупок</span>
                    </div>
                </div>
            </div>
            
            <div class="filters-section">
                <form method="GET" class="filter-form">
                    <div class="filter-row">
                        <div class="filter-group">
                            <select name="status">
                                <option value="">Все статусы</option>
                                <option value="completed" <?= ($_GET['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Завершенные</option>
                                <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>В обработке</option>
                                <option value="disputed" <?= ($_GET['status'] ?? '') === 'disputed' ? 'selected' : '' ?>>Диспут</option>
                                <option value="cancelled" <?= ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Отмененные</option>
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
                        <a href="/my-purchases" class="btn-secondary">Сбросить</a>
                    </div>
                </form>
            </div>
            
            <div class="purchases-section">
                <div class="purchases-header">
                    <h2>История покупок (<?= $totalPurchases ?> всего)</h2>
                    <div class="purchases-actions">
                        <span class="pagination-info">
                            Страница <?= $currentPage ?> из <?= $totalPages ?>
                        </span>
                    </div>
                </div>
                
                <?php if (empty($purchases)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">🛒</div>
                        <h3>У вас пока нет покупок</h3>
                        <p>Перейдите в каталог и найдите интересные товары!</p>
                        <a href="/products" class="btn-primary">Перейти в каталог</a>
                    </div>
                <?php else: ?>
                    <div class="purchases-list">
                        <?php foreach ($purchases as $purchase): ?>
                            <div class="purchase-card">
                                <div class="purchase-header">
                                    <div class="purchase-info">
                                        <span class="purchase-id">Покупка #<?= $purchase['id'] ?></span>
                                        <span class="purchase-date"><?= date('d.m.Y H:i', strtotime($purchase['created_at'])) ?></span>
                                    </div>
                                    <div class="purchase-status">
                                        <span class="badge badge-<?= $purchase['status'] ?>">
                                            <?= $purchase['status'] === 'completed' ? 'Завершена' :
                                               ($purchase['status'] === 'pending' ? 'В обработке' :
                                               ($purchase['status'] === 'disputed' ? 'Диспут' : 'Отменена')) ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="purchase-content">
                                    <div class="product-info">
                                        <?php if ($purchase['product_images']): ?>
                                            <?php $images = json_decode($purchase['product_images'], true); ?>
                                            <img src="<?= htmlspecialchars($images[0] ?? '') ?>" 
                                                 alt="<?= htmlspecialchars($purchase['product_title']) ?>" 
                                                 class="product-image">
                                        <?php else: ?>
                                            <div class="no-image">Нет фото</div>
                                        <?php endif; ?>
                                        
                                        <div class="product-details">
                                            <h3 class="product-title"><?= htmlspecialchars($purchase['product_title']) ?></h3>
                                            <p class="product-game"><?= htmlspecialchars($purchase['product_game']) ?></p>
                                            <div class="product-type">
                                                <span class="badge badge-<?= $purchase['product_type'] ?>">
                                                    <?= $purchase['product_type'] === 'account' ? 'Аккаунт' : 
                                                       ($purchase['product_type'] === 'service' ? 'Услуга' : 'Аренда') ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="purchase-details">
                                        <div class="price-info">
                                            <span class="price"><?= number_format($purchase['price']) ?></span>
                                            <span class="currency"><?= htmlspecialchars($purchase['currency']) ?></span>
                                        </div>
                                        
                                        <div class="seller-info">
                                            <span class="seller-label">Продавец:</span>
                                            <span class="seller-name"><?= htmlspecialchars($purchase['seller_login']) ?></span>
                                        </div>
                                        
                                        <div class="payment-info">
                                            <span class="payment-method"><?= htmlspecialchars($purchase['payment_method']) ?></span>
                                            <?php if ($purchase['transaction_id']): ?>
                                                <span class="transaction-id">ID: <?= htmlspecialchars($purchase['transaction_id']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="purchase-actions">
                                        <a href="/products/<?= $purchase['product_id'] ?>" class="btn-secondary">Просмотреть товар</a>
                                        
                                        <?php if ($purchase['status'] === 'completed' && !$purchase['has_review']): ?>
                                            <button onclick="showReviewForm(<?= $purchase['id'] ?>)" class="btn-primary">Оставить отзыв</button>
                                        <?php endif; ?>
                                        
                                        <?php if ($purchase['status'] === 'completed' && $purchase['has_review']): ?>
                                            <span class="review-status">✅ Отзыв оставлен</span>
                                        <?php endif; ?>
                                        
                                        <?php if ($purchase['status'] === 'completed'): ?>
                                            <button onclick="createDispute(<?= $purchase['id'] ?>)" class="btn-warning">Открыть диспут</button>
                                        <?php endif; ?>
                                        
                                        <?php if ($purchase['status'] === 'disputed'): ?>
                                            <a href="/disputes" class="btn-info">Просмотреть диспут</a>
                                        <?php endif; ?>
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
    
    <!-- Модальное окно для отзыва -->
    <div id="reviewModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Оставить отзыв</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="reviewForm">
                    <input type="hidden" id="purchaseId" name="purchase_id">
                    <div class="form-group">
                        <label for="reviewTitle">Заголовок отзыва</label>
                        <input type="text" id="reviewTitle" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="reviewRating">Оценка</label>
                        <div class="rating-input">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" id="star<?= $i ?>" name="rating" value="<?= $i ?>" <?= $i === 5 ? 'checked' : '' ?>>
                                <label for="star<?= $i ?>">⭐</label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="reviewComment">Комментарий</label>
                        <textarea id="reviewComment" name="comment" rows="4" required></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Отправить отзыв</button>
                        <button type="button" onclick="closeModal()" class="btn-secondary">Отмена</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function showReviewForm(purchaseId) {
            document.getElementById('purchaseId').value = purchaseId;
            document.getElementById('reviewModal').style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('reviewModal').style.display = 'none';
            document.getElementById('reviewForm').reset();
        }
        
        document.getElementById('reviewForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const reviewData = {};
            
            for (let [key, value] of formData.entries()) {
                reviewData[key] = value;
            }
            
            fetch('/create-review', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(reviewData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal();
                    location.reload();
                } else {
                    alert('Ошибка: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Произошла ошибка');
            });
        });
        
        function createDispute(purchaseId) {
            const reason = prompt('Причина диспута:');
            if (reason !== null) {
                fetch('/create-dispute', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ 
                        purchase_id: purchaseId,
                        subject: 'Диспут по покупке',
                        description: reason
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
        
        // Закрытие модального окна при клике вне его
        window.onclick = function(event) {
            const modal = document.getElementById('reviewModal');
            if (event.target === modal) {
                modal.style.display = 'none';
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
        
        .purchases-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .purchase-card {
            background: var(--bg-secondary);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .purchase-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }
        
        .purchase-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background: var(--bg-primary);
            border-bottom: 1px solid var(--border-color);
        }
        
        .purchase-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .purchase-id {
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .purchase-date {
            font-size: 12px;
            color: var(--text-secondary);
        }
        
        .purchase-content {
            padding: 20px;
        }
        
        .product-info {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .no-image {
            width: 80px;
            height: 80px;
            background: var(--bg-primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            font-size: 20px;
        }
        
        .product-details {
            flex: 1;
        }
        
        .product-title {
            margin: 0 0 5px 0;
            font-size: 16px;
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .product-game {
            margin: 0 0 8px 0;
            color: var(--primary-color);
            font-size: 14px;
        }
        
        .purchase-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
            padding: 15px;
            background: var(--bg-primary);
            border-radius: 8px;
        }
        
        .price-info {
            display: flex;
            align-items: baseline;
            gap: 5px;
        }
        
        .price {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .currency {
            font-size: 14px;
            color: var(--text-secondary);
        }
        
        .seller-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        
        .seller-label {
            font-size: 12px;
            color: var(--text-secondary);
        }
        
        .seller-name {
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .payment-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        
        .payment-method {
            font-size: 14px;
            color: var(--text-primary);
        }
        
        .transaction-id {
            font-size: 12px;
            color: var(--text-secondary);
        }
        
        .purchase-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .review-status {
            color: var(--success-color);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-warning {
            background: var(--warning-color);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .btn-warning:hover {
            background: var(--warning-dark);
            transform: translateY(-2px);
        }
        
        .btn-info {
            background: var(--info-color);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .btn-info:hover {
            background: var(--info-dark);
            transform: translateY(-2px);
        }
        
        .badge-completed { background: var(--success-color); }
        .badge-pending { background: var(--warning-color); }
        .badge-disputed { background: var(--danger-color); }
        .badge-cancelled { background: var(--secondary-color); }
        
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
            width: 90%;
            max-width: 500px;
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
        
        .rating-input {
            display: flex;
            flex-direction: row-reverse;
            gap: 5px;
        }
        
        .rating-input input[type="radio"] {
            display: none;
        }
        
        .rating-input label {
            font-size: 24px;
            cursor: pointer;
            opacity: 0.3;
            transition: opacity 0.3s ease;
        }
        
        .rating-input input[type="radio"]:checked ~ label,
        .rating-input label:hover,
        .rating-input label:hover ~ label {
            opacity: 1;
        }
        
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            .header-stats {
                justify-content: center;
            }
            
            .product-info {
                flex-direction: column;
                text-align: center;
            }
            
            .purchase-details {
                grid-template-columns: 1fr;
            }
            
            .purchase-actions {
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