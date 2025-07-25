<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Отзывы - Game Marketplace</title>
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
                <h1>Отзывы</h1>
                <div class="header-stats">
                    <div class="stat-card">
                        <span class="stat-icon">⭐</span>
                        <span class="stat-value"><?= $totalReviews ?></span>
                        <span class="stat-label">Всего отзывов</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-icon">📝</span>
                        <span class="stat-value"><?= $pendingReviews ?></span>
                        <span class="stat-label">Ожидают отзыва</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-icon">✅</span>
                        <span class="stat-value"><?= $approvedReviews ?></span>
                        <span class="stat-label">Одобренных</span>
                    </div>
                </div>
            </div>
            
            <div class="filters-section">
                <form class="filter-form" method="GET">
                    <div class="filter-group">
                        <select name="status" onchange="this.form.submit()">
                            <option value="">Все статусы</option>
                            <option value="pending" <?= ($filters['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Ожидает модерации</option>
                            <option value="approved" <?= ($filters['status'] ?? '') === 'approved' ? 'selected' : '' ?>>Одобрен</option>
                            <option value="rejected" <?= ($filters['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Отклонен</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <select name="rating" onchange="this.form.submit()">
                            <option value="">Все оценки</option>
                            <option value="5" <?= ($filters['rating'] ?? '') === '5' ? 'selected' : '' ?>>5 звезд</option>
                            <option value="4" <?= ($filters['rating'] ?? '') === '4' ? 'selected' : '' ?>>4 звезды</option>
                            <option value="3" <?= ($filters['rating'] ?? '') === '3' ? 'selected' : '' ?>>3 звезды</option>
                            <option value="2" <?= ($filters['rating'] ?? '') === '2' ? 'selected' : '' ?>>2 звезды</option>
                            <option value="1" <?= ($filters['rating'] ?? '') === '1' ? 'selected' : '' ?>>1 звезда</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <input type="text" name="search" placeholder="Поиск по товару..." 
                               value="<?= htmlspecialchars($filters['search'] ?? '') ?>" onchange="this.form.submit()">
                    </div>
                </form>
            </div>
            
            <div class="reviews-section">
                <div class="reviews-header">
                    <h3>Мои отзывы (<?= $totalItems ?>)</h3>
                    <?php if ($totalPages > 1): ?>
                        <div class="pagination-info">
                            Страница <?= $currentPage ?> из <?= $totalPages ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if (empty($reviews)): ?>
                    <div class="empty-reviews">
                        <div class="empty-icon">⭐</div>
                        <h4>У вас пока нет отзывов</h4>
                        <p>Оставляйте отзывы о купленных товарах, чтобы помочь другим пользователям</p>
                        <a href="/my-purchases" class="btn-primary">Перейти к покупкам</a>
                    </div>
                <?php else: ?>
                    <div class="reviews-list">
                        <?php foreach ($reviews as $review): ?>
                            <div class="review-card">
                                <div class="review-header">
                                    <div class="review-product">
                                        <div class="product-image">
                                            <?php if ($review['product']['images']): ?>
                                                <?php $images = json_decode($review['product']['images'], true); ?>
                                                <img src="<?= htmlspecialchars($images[0]) ?>" 
                                                     alt="<?= htmlspecialchars($review['product']['title']) ?>">
                                            <?php else: ?>
                                                <div class="image-placeholder">🎮</div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="product-info">
                                            <h4><?= htmlspecialchars($review['product']['title']) ?></h4>
                                            <span class="product-game"><?= htmlspecialchars($review['product']['game']) ?></span>
                                            <span class="product-type"><?= htmlspecialchars($review['product']['type']) ?></span>
                                        </div>
                                    </div>
                                    <div class="review-status">
                                        <span class="status-badge status-<?= $review['status'] ?>">
                                            <?= $statusLabels[$review['status']] ?>
                                        </span>
                                        <span class="review-date"><?= date('d.m.Y H:i', strtotime($review['created_at'])) ?></span>
                                    </div>
                                </div>
                                
                                <div class="review-content">
                                    <div class="review-rating">
                                        <div class="stars">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <span class="star <?= $i <= $review['rating'] ? 'filled' : '' ?>">★</span>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="rating-text"><?= $review['rating'] ?>/5</span>
                                    </div>
                                    
                                    <?php if ($review['title']): ?>
                                        <div class="review-title">
                                            <h5><?= htmlspecialchars($review['title']) ?></h5>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="review-comment">
                                        <p><?= htmlspecialchars($review['comment']) ?></p>
                                    </div>
                                    
                                    <?php if ($review['status'] === 'rejected' && $review['admin_comment']): ?>
                                        <div class="admin-comment">
                                            <div class="comment-header">
                                                <span class="comment-label">Комментарий модератора:</span>
                                            </div>
                                            <div class="comment-content">
                                                <p><?= htmlspecialchars($review['admin_comment']) ?></p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="review-actions">
                                    <button onclick="viewReview(<?= $review['id'] ?>)" class="btn-secondary">Просмотр</button>
                                    <?php if ($review['status'] === 'pending'): ?>
                                        <button onclick="editReview(<?= $review['id'] ?>)" class="btn-secondary">Редактировать</button>
                                        <button onclick="deleteReview(<?= $review['id'] ?>)" class="btn-danger">Удалить</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if ($totalPages > 1): ?>
                        <div class="pagination">
                            <?php if ($currentPage > 1): ?>
                                <a href="?page=<?= $currentPage - 1 ?>&<?= http_build_query(array_diff_key($filters, ['page' => ''])) ?>" class="page-link">← Назад</a>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                                <a href="?page=<?= $i ?>&<?= http_build_query(array_diff_key($filters, ['page' => ''])) ?>" 
                                   class="page-link <?= $i === $currentPage ? 'active' : '' ?>"><?= $i ?></a>
                            <?php endfor; ?>
                            
                            <?php if ($currentPage < $totalPages): ?>
                                <a href="?page=<?= $currentPage + 1 ?>&<?= http_build_query(array_diff_key($filters, ['page' => ''])) ?>" class="page-link">Вперед →</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            
            <!-- Секция ожидающих отзывов -->
            <?php if (!empty($pendingPurchases)): ?>
                <div class="pending-reviews-section">
                    <div class="section-header">
                        <h3>Ожидают вашего отзыва</h3>
                        <p>Оставьте отзыв о купленных товарах</p>
                    </div>
                    
                    <div class="pending-purchases">
                        <?php foreach ($pendingPurchases as $purchase): ?>
                            <div class="pending-purchase-card">
                                <div class="purchase-product">
                                    <div class="product-image">
                                        <?php if ($purchase['product']['images']): ?>
                                            <?php $images = json_decode($purchase['product']['images'], true); ?>
                                            <img src="<?= htmlspecialchars($images[0]) ?>" 
                                                 alt="<?= htmlspecialchars($purchase['product']['title']) ?>">
                                        <?php else: ?>
                                            <div class="image-placeholder">🎮</div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-info">
                                        <h4><?= htmlspecialchars($purchase['product']['title']) ?></h4>
                                        <span class="product-game"><?= htmlspecialchars($purchase['product']['game']) ?></span>
                                        <span class="purchase-date">Куплено: <?= date('d.m.Y', strtotime($purchase['created_at'])) ?></span>
                                    </div>
                                </div>
                                <div class="purchase-actions">
                                    <button onclick="showReviewForm(<?= $purchase['id'] ?>)" class="btn-primary">Оставить отзыв</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <!-- Модальное окно для создания отзыва -->
    <div id="reviewModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Оставить отзыв</h3>
                <button onclick="closeModal()" class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <form id="reviewForm" class="review-form">
                    <input type="hidden" name="purchase_id" id="reviewPurchaseId">
                    
                    <div class="product-preview" id="productPreview">
                        <!-- Информация о товаре будет загружена через AJAX -->
                    </div>
                    
                    <div class="form-group">
                        <label>Оценка:</label>
                        <div class="rating-input">
                            <div class="stars-input">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="star-input" data-rating="<?= $i ?>">★</span>
                                <?php endfor; ?>
                            </div>
                            <span class="rating-text-input">Выберите оценку</span>
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Заголовок отзыва:</label>
                        <input type="text" name="title" placeholder="Краткое описание вашего опыта" maxlength="100">
                    </div>
                    
                    <div class="form-group">
                        <label>Комментарий:</label>
                        <textarea name="comment" placeholder="Подробно опишите ваш опыт использования товара..." required maxlength="1000" rows="5"></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" onclick="closeModal()" class="btn-secondary">Отмена</button>
                        <button type="submit" class="btn-primary">Опубликовать отзыв</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        let currentRating = 0;
        
        function showReviewForm(purchaseId) {
            document.getElementById('reviewPurchaseId').value = purchaseId;
            
            // Загружаем информацию о товаре
            fetch(`/get-purchase-info?id=${purchaseId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const product = data.purchase.product;
                        const images = JSON.parse(product.images || '[]');
                        
                        document.getElementById('productPreview').innerHTML = `
                            <div class="preview-product">
                                <div class="preview-image">
                                    ${images.length > 0 ? 
                                        `<img src="${images[0]}" alt="${product.title}">` : 
                                        '<div class="image-placeholder">🎮</div>'
                                    }
                                </div>
                                <div class="preview-info">
                                    <h4>${product.title}</h4>
                                    <span class="preview-game">${product.game}</span>
                                    <span class="preview-type">${product.type}</span>
                                </div>
                            </div>
                        `;
                        
                        document.getElementById('reviewModal').style.display = 'flex';
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
            // Сбрасываем форму
            document.getElementById('reviewForm').reset();
            currentRating = 0;
            updateRatingDisplay();
        }
        
        function viewReview(reviewId) {
            // Редирект на страницу просмотра отзыва
            window.location.href = `/review/${reviewId}`;
        }
        
        function editReview(reviewId) {
            // Редирект на страницу редактирования отзыва
            window.location.href = `/edit-review?id=${reviewId}`;
        }
        
        function deleteReview(reviewId) {
            if (confirm('Вы уверены, что хотите удалить этот отзыв?')) {
                fetch('/delete-review', {
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
        
        // Обработка рейтинга
        document.querySelectorAll('.star-input').forEach(star => {
            star.addEventListener('click', function() {
                const rating = parseInt(this.dataset.rating);
                currentRating = rating;
                updateRatingDisplay();
                document.getElementById('ratingInput').value = rating;
            });
            
            star.addEventListener('mouseenter', function() {
                const rating = parseInt(this.dataset.rating);
                highlightStars(rating);
            });
        });
        
        document.querySelector('.stars-input').addEventListener('mouseleave', function() {
            highlightStars(currentRating);
        });
        
        function highlightStars(rating) {
            document.querySelectorAll('.star-input').forEach((star, index) => {
                if (index < rating) {
                    star.classList.add('filled');
                } else {
                    star.classList.remove('filled');
                }
            });
        }
        
        function updateRatingDisplay() {
            const ratingText = currentRating > 0 ? `${currentRating}/5` : 'Выберите оценку';
            document.querySelector('.rating-text-input').textContent = ratingText;
        }
        
        // Создание отзыва
        document.getElementById('reviewForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (currentRating === 0) {
                alert('Пожалуйста, выберите оценку');
                return;
            }
            
            const formData = new FormData(this);
            const data = Object.fromEntries(formData);
            
            fetch('/create-review', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
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
        
        // Закрытие модального окна при клике вне его
        window.onclick = function(event) {
            const modal = document.getElementById('reviewModal');
            if (event.target === modal) {
                closeModal();
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
        
        .filters-section {
            background: var(--bg-secondary);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
        }
        
        .filter-form {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--bg-primary);
            color: var(--text-primary);
        }
        
        .reviews-section {
            background: var(--bg-secondary);
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 30px;
        }
        
        .reviews-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            background: var(--bg-primary);
        }
        
        .reviews-header h3 {
            margin: 0;
            color: var(--text-primary);
        }
        
        .pagination-info {
            font-size: 14px;
            color: var(--text-secondary);
        }
        
        .empty-reviews {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }
        
        .empty-reviews .empty-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        
        .empty-reviews h4 {
            margin: 0 0 10px 0;
            color: var(--text-primary);
        }
        
        .empty-reviews p {
            margin: 0 0 20px 0;
            font-size: 14px;
        }
        
        .reviews-list {
            padding: 20px;
        }
        
        .review-card {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        
        .review-card:hover {
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .review-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .review-product {
            display: flex;
            gap: 15px;
            flex: 1;
        }
        
        .product-image {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            overflow: hidden;
            flex-shrink: 0;
        }
        
        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .image-placeholder {
            width: 100%;
            height: 100%;
            background: var(--bg-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        
        .product-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .product-info h4 {
            margin: 0;
            color: var(--text-primary);
            font-size: 16px;
        }
        
        .product-game {
            color: var(--primary-color);
            font-size: 14px;
        }
        
        .product-type {
            color: var(--text-secondary);
            font-size: 12px;
            text-transform: uppercase;
        }
        
        .review-status {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 5px;
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-pending {
            background: var(--warning-color);
            color: white;
        }
        
        .status-approved {
            background: var(--success-color);
            color: white;
        }
        
        .status-rejected {
            background: var(--danger-color);
            color: white;
        }
        
        .review-date {
            font-size: 12px;
            color: var(--text-secondary);
        }
        
        .review-content {
            margin-bottom: 20px;
        }
        
        .review-rating {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .stars {
            display: flex;
            gap: 2px;
        }
        
        .star {
            font-size: 20px;
            color: var(--border-color);
            cursor: pointer;
        }
        
        .star.filled {
            color: #ffd700;
        }
        
        .rating-text {
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .review-title h5 {
            margin: 0 0 10px 0;
            color: var(--text-primary);
            font-size: 16px;
        }
        
        .review-comment p {
            margin: 0 0 15px 0;
            color: var(--text-secondary);
            line-height: 1.5;
        }
        
        .admin-comment {
            background: var(--bg-secondary);
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid var(--danger-color);
        }
        
        .comment-header {
            margin-bottom: 10px;
        }
        
        .comment-label {
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .comment-content p {
            margin: 0;
            color: var(--text-secondary);
            line-height: 1.5;
        }
        
        .review-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        
        .btn-secondary,
        .btn-danger {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .btn-secondary {
            background: var(--bg-primary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }
        
        .btn-secondary:hover {
            background: var(--bg-secondary);
        }
        
        .btn-danger {
            background: var(--danger-color);
            color: white;
        }
        
        .btn-danger:hover {
            background: var(--danger-dark);
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            padding: 20px;
            border-top: 1px solid var(--border-color);
        }
        
        .page-link {
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            text-decoration: none;
            color: var(--text-primary);
            transition: all 0.3s ease;
        }
        
        .page-link:hover {
            background: var(--bg-secondary);
        }
        
        .page-link.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        /* Секция ожидающих отзывов */
        .pending-reviews-section {
            background: var(--bg-secondary);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .section-header h3 {
            margin: 0 0 5px 0;
            color: var(--text-primary);
        }
        
        .section-header p {
            margin: 0;
            color: var(--text-secondary);
            font-size: 14px;
        }
        
        .pending-purchases {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .pending-purchase-card {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .purchase-product {
            display: flex;
            gap: 15px;
            align-items: center;
            flex: 1;
        }
        
        .purchase-date {
            color: var(--text-secondary);
            font-size: 12px;
        }
        
        .purchase-actions {
            flex-shrink: 0;
        }
        
        /* Модальные окна */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        
        .modal-content {
            background: var(--bg-primary);
            border-radius: 12px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .modal-header h3 {
            margin: 0;
            color: var(--text-primary);
        }
        
        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--text-secondary);
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .product-preview {
            background: var(--bg-secondary);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .preview-product {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .preview-image {
            width: 60px;
            height: 60px;
            border-radius: 6px;
            overflow: hidden;
            flex-shrink: 0;
        }
        
        .preview-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .preview-info h4 {
            margin: 0 0 5px 0;
            color: var(--text-primary);
            font-size: 16px;
        }
        
        .preview-game {
            color: var(--primary-color);
            font-size: 14px;
        }
        
        .preview-type {
            color: var(--text-secondary);
            font-size: 12px;
            text-transform: uppercase;
        }
        
        .review-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .form-group label {
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .form-group select,
        .form-group input,
        .form-group textarea {
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            background: var(--bg-secondary);
            color: var(--text-primary);
            font-size: 14px;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .rating-input {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .stars-input {
            display: flex;
            gap: 5px;
        }
        
        .star-input {
            font-size: 24px;
            color: var(--border-color);
            cursor: pointer;
            transition: color 0.3s ease;
        }
        
        .star-input:hover,
        .star-input.filled {
            color: #ffd700;
        }
        
        .rating-text-input {
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 20px;
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
            
            .filter-form {
                flex-direction: column;
            }
            
            .filter-group {
                min-width: auto;
            }
            
            .review-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .review-status {
                align-self: flex-end;
            }
            
            .pending-purchase-card {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .purchase-actions {
                align-self: stretch;
            }
            
            .review-actions {
                flex-direction: column;
            }
            
            .modal-content {
                width: 95%;
                margin: 20px;
            }
        }
    </style>
</body>
</html>