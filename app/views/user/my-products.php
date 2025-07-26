<?php $title = 'Мои товары'; ?>

<div class="container">
    <div class="page-header">
        <h1>Мои товары</h1>
        <a href="/products/create" class="btn btn-primary">
            <i class="icon-plus"></i> Добавить товар
        </a>
    </div>

    <?php if (empty($products)): ?>
        <div class="empty-state">
            <i class="icon-package"></i>
            <h2>У вас пока нет товаров</h2>
            <p>Создайте свой первый товар и начните продавать!</p>
            <a href="/products/create" class="btn btn-primary">Создать товар</a>
        </div>
    <?php else: ?>
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php if (!empty($product['images'])): ?>
                            <img src="<?= htmlspecialchars($product['images'][0]) ?>" alt="<?= htmlspecialchars($product['title']) ?>">
                        <?php else: ?>
                            <div class="no-image">
                                <i class="icon-image"></i>
                            </div>
                        <?php endif; ?>
                        <div class="product-status status-<?= $product['status'] ?>">
                            <?= $product['status'] === 'active' ? 'Активен' : 
                               ($product['status'] === 'sold' ? 'Продан' : 'Неактивен') ?>
                        </div>
                    </div>
                    
                    <div class="product-info">
                        <h3 class="product-title"><?= htmlspecialchars($product['title']) ?></h3>
                        <p class="product-price"><?= number_format($product['price'], 2) ?> ₽</p>
                        <p class="product-views">Просмотры: <?= $product['views'] ?? 0 ?></p>
                        <div class="product-dates">
                            <small>Создан: <?= date('d.m.Y', strtotime($product['created_at'])) ?></small>
                        </div>
                    </div>
                    
                    <div class="product-actions">
                        <a href="/product/<?= $product['id'] ?>" class="btn btn-sm btn-secondary">Просмотр</a>
                        <a href="/products/<?= $product['id'] ?>/edit" class="btn btn-sm btn-primary">Редактировать</a>
                        <button class="btn btn-sm btn-danger" onclick="deleteProduct(<?= $product['id'] ?>)">Удалить</button>
                    </div>
                </div>
            <?php endforeach; ?>
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

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: var(--card-bg);
    border-radius: 12px;
}

.empty-state i {
    font-size: 4rem;
    color: var(--text-secondary);
    margin-bottom: 1rem;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.product-card {
    background: var(--card-bg);
    border-radius: 12px;
    overflow: hidden;
    transition: transform 0.3s ease;
}

.product-card:hover {
    transform: translateY(-2px);
}

.product-image {
    position: relative;
    height: 200px;
    overflow: hidden;
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

.no-image i {
    font-size: 3rem;
}

.product-status {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-active {
    background: #22c55e;
    color: white;
}

.status-sold {
    background: #f59e0b;
    color: white;
}

.status-inactive {
    background: #6b7280;
    color: white;
}

.product-info {
    padding: 1rem;
}

.product-title {
    margin: 0 0 0.5rem;
    font-size: 1.1rem;
    color: var(--text-primary);
}

.product-price {
    margin: 0 0 0.5rem;
    font-size: 1.2rem;
    font-weight: bold;
    color: var(--accent-color);
}

.product-views {
    margin: 0 0 0.5rem;
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.product-dates small {
    color: var(--text-secondary);
}

.product-actions {
    padding: 1rem;
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
}
</style>

<script>
function deleteProduct(id) {
    if (confirm('Вы уверены, что хотите удалить этот товар?')) {
        fetch(`/products/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '<?= csrf_token() ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Ошибка при удалении товара');
            }
        })
        .catch(error => {
            alert('Ошибка при удалении товара');
        });
    }
}
</script>