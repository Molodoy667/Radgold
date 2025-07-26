<?php $title = 'Детали покупки #' . $purchase['id']; ?>

<div class="container">
    <div class="page-header">
        <h1>Покупка #<?= $purchase['id'] ?></h1>
        <div class="purchase-status">
            <span class="status-badge status-<?= $purchase['status'] ?>">
                <?= $purchase['status'] === 'completed' ? 'Завершена' : 
                   ($purchase['status'] === 'pending' ? 'В обработке' : 
                   ($purchase['status'] === 'cancelled' ? 'Отменена' : 'Активна')) ?>
            </span>
        </div>
    </div>

    <div class="purchase-details">
        <div class="purchase-info">
            <div class="info-section">
                <h2>Информация о покупке</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Дата покупки:</label>
                        <span><?= date('d.m.Y H:i', strtotime($purchase['created_at'])) ?></span>
                    </div>
                    <div class="info-item">
                        <label>Сумма:</label>
                        <span class="amount"><?= number_format($purchase['total_amount'], 2) ?> ₽</span>
                    </div>
                    <div class="info-item">
                        <label>Комиссия:</label>
                        <span><?= number_format($purchase['commission'] ?? 0, 2) ?> ₽</span>
                    </div>
                    <div class="info-item">
                        <label>Метод оплаты:</label>
                        <span><?= htmlspecialchars($purchase['payment_method'] ?? 'Баланс') ?></span>
                    </div>
                </div>
            </div>

            <div class="info-section">
                <h2>Товар</h2>
                <div class="product-info">
                    <div class="product-image">
                        <?php if (!empty($purchase['product_images'])): ?>
                            <img src="<?= htmlspecialchars($purchase['product_images'][0]) ?>" alt="<?= htmlspecialchars($purchase['product_title']) ?>">
                        <?php else: ?>
                            <div class="no-image">
                                <i class="icon-image"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="product-details">
                        <h3><a href="/product/<?= $purchase['product_id'] ?>"><?= htmlspecialchars($purchase['product_title']) ?></a></h3>
                        <p class="product-description"><?= htmlspecialchars($purchase['product_description']) ?></p>
                        <p class="product-price">Цена: <?= number_format($purchase['product_price'], 2) ?> ₽</p>
                    </div>
                </div>
            </div>

            <div class="info-section">
                <h2>Продавец</h2>
                <div class="seller-info">
                    <div class="seller-avatar">
                        <img src="<?= $purchase['seller_avatar'] ?? '/assets/images/default-avatar.svg' ?>" alt="Аватар продавца">
                    </div>
                    <div class="seller-details">
                        <h3><a href="/user/<?= $purchase['seller_id'] ?>"><?= htmlspecialchars($purchase['seller_username']) ?></a></h3>
                        <div class="seller-rating">
                            <span class="rating-stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="icon-star<?= $i <= ($purchase['seller_rating'] ?? 0) ? '-fill' : '' ?>"></i>
                                <?php endfor; ?>
                            </span>
                            <span class="rating-value"><?= number_format($purchase['seller_rating'] ?? 0, 1) ?></span>
                        </div>
                        <div class="seller-actions">
                            <a href="/messages/<?= $purchase['seller_id'] ?>" class="btn btn-sm btn-primary">
                                <i class="icon-mail"></i> Написать
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($purchase['purchase_data'])): ?>
                <div class="info-section">
                    <h2>Данные доступа</h2>
                    <div class="access-data">
                        <?php foreach ($purchase['purchase_data'] as $key => $value): ?>
                            <div class="data-item">
                                <label><?= htmlspecialchars(ucfirst($key)) ?>:</label>
                                <span class="data-value"><?= htmlspecialchars($value) ?></span>
                                <button class="copy-btn" onclick="copyToClipboard('<?= htmlspecialchars($value) ?>')">
                                    <i class="icon-copy"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="purchase-actions">
            <?php if ($purchase['status'] === 'completed' && empty($purchase['review_id'])): ?>
                <a href="/reviews/create/<?= $purchase['product_id'] ?>" class="btn btn-primary">
                    <i class="icon-star"></i> Оставить отзыв
                </a>
            <?php endif; ?>
            
            <?php if ($purchase['status'] === 'active' || $purchase['status'] === 'pending'): ?>
                <a href="/disputes/create/<?= $purchase['id'] ?>" class="btn btn-warning">
                    <i class="icon-alert-circle"></i> Открыть спор
                </a>
            <?php endif; ?>
            
            <a href="/my-purchases" class="btn btn-secondary">
                ← Вернуться к покупкам
            </a>
        </div>
    </div>

    <?php if (!empty($transactions)): ?>
        <div class="transactions-section">
            <h2>История транзакций</h2>
            <div class="transactions-list">
                <?php foreach ($transactions as $transaction): ?>
                    <div class="transaction-item">
                        <div class="transaction-info">
                            <span class="transaction-type"><?= htmlspecialchars($transaction['type']) ?></span>
                            <span class="transaction-date"><?= date('d.m.Y H:i', strtotime($transaction['created_at'])) ?></span>
                        </div>
                        <div class="transaction-amount">
                            <span class="amount"><?= number_format($transaction['amount'], 2) ?> ₽</span>
                            <span class="status status-<?= $transaction['status'] ?>"><?= $transaction['status'] ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
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

.purchase-details {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 2rem;
    margin-bottom: 2rem;
}

.info-section {
    background: var(--card-bg);
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
}

.info-section h2 {
    margin: 0 0 1rem;
    color: var(--text-primary);
    border-bottom: 2px solid var(--border-color);
    padding-bottom: 0.5rem;
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.info-item label {
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.info-item span {
    color: var(--text-primary);
    font-weight: 500;
}

.amount {
    color: var(--accent-color);
    font-weight: bold;
}

.product-info {
    display: flex;
    gap: 1rem;
}

.product-image {
    width: 100px;
    height: 100px;
    border-radius: 8px;
    overflow: hidden;
    flex-shrink: 0;
}

.product-image img {
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
}

.product-details h3 {
    margin: 0 0 0.5rem;
}

.product-details h3 a {
    color: var(--text-primary);
    text-decoration: none;
}

.product-details h3 a:hover {
    color: var(--accent-color);
}

.product-description {
    color: var(--text-secondary);
    font-size: 0.9rem;
    margin: 0.5rem 0;
}

.product-price {
    color: var(--accent-color);
    font-weight: bold;
    margin: 0;
}

.seller-info {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.seller-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
}

.seller-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.seller-details h3 {
    margin: 0 0 0.5rem;
}

.seller-details h3 a {
    color: var(--text-primary);
    text-decoration: none;
}

.seller-details h3 a:hover {
    color: var(--accent-color);
}

.seller-rating {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.rating-stars {
    color: #fbbf24;
}

.rating-value {
    font-weight: bold;
    color: var(--text-primary);
}

.access-data {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.data-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: var(--bg-color);
    border-radius: 8px;
}

.data-item label {
    color: var(--text-secondary);
    font-size: 0.9rem;
    min-width: 100px;
}

.data-value {
    flex: 1;
    font-family: monospace;
    background: var(--border-color);
    padding: 0.5rem;
    border-radius: 4px;
    color: var(--text-primary);
}

.copy-btn {
    background: var(--accent-color);
    color: white;
    border: none;
    padding: 0.5rem;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.copy-btn:hover {
    background: #8b5cf6;
}

.purchase-actions {
    background: var(--card-bg);
    padding: 1.5rem;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    gap: 1rem;
    height: fit-content;
}

.btn-warning {
    background: #f59e0b;
    color: white;
    border: none;
}

.btn-warning:hover {
    background: #d97706;
}

.transactions-section {
    background: var(--card-bg);
    padding: 1.5rem;
    border-radius: 12px;
}

.transactions-section h2 {
    margin: 0 0 1rem;
    color: var(--text-primary);
}

.transactions-list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.transaction-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: var(--bg-color);
    border-radius: 8px;
}

.transaction-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.transaction-type {
    font-weight: 500;
    color: var(--text-primary);
}

.transaction-date {
    font-size: 0.85rem;
    color: var(--text-secondary);
}

.transaction-amount {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.25rem;
}

@media (max-width: 768px) {
    .purchase-details {
        grid-template-columns: 1fr;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .product-info,
    .seller-info {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Показываем уведомление об успешном копировании
        if (window.App && window.App.notification) {
            App.notification.show('Скопировано в буфер обмена', 'success');
        } else {
            alert('Скопировано в буфер обмена');
        }
    }).catch(function(err) {
        console.error('Ошибка копирования: ', err);
        alert('Ошибка копирования');
    });
}
</script>