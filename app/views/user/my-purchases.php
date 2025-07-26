<?php $title = 'Мои покупки'; ?>

<div class="container">
    <div class="page-header">
        <h1>Мои покупки</h1>
    </div>

    <?php if (empty($purchases)): ?>
        <div class="empty-state">
            <i class="icon-shopping-cart"></i>
            <h2>У вас пока нет покупок</h2>
            <p>Начните покупать товары в нашем маркетплейсе!</p>
            <a href="/catalog" class="btn btn-primary">Перейти в каталог</a>
        </div>
    <?php else: ?>
        <div class="purchases-list">
            <?php foreach ($purchases as $purchase): ?>
                <div class="purchase-card">
                    <div class="purchase-image">
                        <?php if (!empty($purchase['product_images'])): ?>
                            <img src="<?= htmlspecialchars($purchase['product_images'][0]) ?>" alt="<?= htmlspecialchars($purchase['product_title']) ?>">
                        <?php else: ?>
                            <div class="no-image">
                                <i class="icon-image"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="purchase-info">
                        <h3 class="product-title"><?= htmlspecialchars($purchase['product_title']) ?></h3>
                        <p class="seller">Продавец: <a href="/user/<?= $purchase['seller_id'] ?>"><?= htmlspecialchars($purchase['seller_username']) ?></a></p>
                        <p class="purchase-price">Цена: <?= number_format($purchase['total_amount'], 2) ?> ₽</p>
                        <p class="purchase-date">Дата покупки: <?= date('d.m.Y H:i', strtotime($purchase['created_at'])) ?></p>
                    </div>
                    
                    <div class="purchase-status">
                        <span class="status-badge status-<?= $purchase['status'] ?>">
                            <?= $purchase['status'] === 'completed' ? 'Завершена' : 
                               ($purchase['status'] === 'pending' ? 'В обработке' : 
                               ($purchase['status'] === 'cancelled' ? 'Отменена' : 'Активна')) ?>
                        </span>
                    </div>
                    
                    <div class="purchase-actions">
                        <a href="/purchases/<?= $purchase['id'] ?>" class="btn btn-sm btn-primary">Детали</a>
                        <a href="/product/<?= $purchase['product_id'] ?>" class="btn btn-sm btn-secondary">Товар</a>
                        <?php if ($purchase['status'] === 'completed' && empty($purchase['review_id'])): ?>
                            <a href="/reviews/create/<?= $purchase['product_id'] ?>" class="btn btn-sm btn-accent">Оставить отзыв</a>
                        <?php endif; ?>
                        <?php if ($purchase['status'] === 'active' || $purchase['status'] === 'pending'): ?>
                            <a href="/disputes/create/<?= $purchase['id'] ?>" class="btn btn-sm btn-warning">Спор</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.purchases-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.purchase-card {
    display: grid;
    grid-template-columns: 120px 1fr auto auto;
    gap: 1rem;
    padding: 1.5rem;
    background: var(--card-bg);
    border-radius: 12px;
    align-items: center;
}

.purchase-image {
    width: 120px;
    height: 80px;
    border-radius: 8px;
    overflow: hidden;
}

.purchase-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-image {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--border-color);
    color: var(--text-secondary);
    border-radius: 8px;
}

.purchase-info h3 {
    margin: 0 0 0.5rem;
    color: var(--text-primary);
}

.purchase-info p {
    margin: 0.25rem 0;
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.seller a {
    color: var(--accent-color);
    text-decoration: none;
}

.seller a:hover {
    text-decoration: underline;
}

.purchase-price {
    font-weight: bold;
    color: var(--text-primary) !important;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-completed {
    background: #22c55e;
    color: white;
}

.status-pending {
    background: #f59e0b;
    color: white;
}

.status-cancelled {
    background: #ef4444;
    color: white;
}

.status-active {
    background: #3b82f6;
    color: white;
}

.purchase-actions {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    min-width: 150px;
}

.btn-warning {
    background: #f59e0b;
    color: white;
    border: none;
}

.btn-warning:hover {
    background: #d97706;
}

.btn-accent {
    background: var(--accent-color);
    color: white;
    border: none;
}

.btn-accent:hover {
    background: #8b5cf6;
}

@media (max-width: 768px) {
    .purchase-card {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .purchase-actions {
        flex-direction: row;
        justify-content: center;
        flex-wrap: wrap;
    }
}
</style>