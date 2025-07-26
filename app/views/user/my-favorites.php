<?php $title = 'Избранное'; ?>

<div class="container">
    <div class="page-header">
        <h1>Избранное</h1>
        <span class="favorites-count"><?= count($favorites) ?> товаров</span>
    </div>

    <?php if (empty($favorites)): ?>
        <div class="empty-state">
            <i class="icon-heart"></i>
            <h2>В избранном пока нет товаров</h2>
            <p>Добавляйте понравившиеся товары в избранное!</p>
            <a href="/catalog" class="btn btn-primary">Перейти в каталог</a>
        </div>
    <?php else: ?>
        <div class="favorites-grid">
            <?php foreach ($favorites as $favorite): ?>
                <div class="product-card">
                    <div class="product-image">
                        <a href="/product/<?= $favorite['id'] ?>">
                            <?php if (!empty($favorite['images'])): ?>
                                <img src="<?= htmlspecialchars($favorite['images'][0]) ?>" alt="<?= htmlspecialchars($favorite['title']) ?>">
                            <?php else: ?>
                                <div class="no-image">
                                    <i class="icon-image"></i>
                                </div>
                            <?php endif; ?>
                        </a>
                        <button class="favorite-btn active" onclick="toggleFavorite(<?= $favorite['id'] ?>)">
                            <i class="icon-heart-fill"></i>
                        </button>
                    </div>
                    
                    <div class="product-info">
                        <div class="product-category"><?= htmlspecialchars($favorite['category_name'] ?? '') ?></div>
                        <h3 class="product-title">
                            <a href="/product/<?= $favorite['id'] ?>"><?= htmlspecialchars($favorite['title']) ?></a>
                        </h3>
                        <p class="product-description"><?= htmlspecialchars(substr($favorite['description'], 0, 100)) ?>...</p>
                        
                        <div class="product-meta">
                            <div class="product-price"><?= number_format($favorite['price'], 2) ?> ₽</div>
                            <div class="product-seller">
                                <a href="/user/<?= $favorite['seller_id'] ?>">
                                    <?= htmlspecialchars($favorite['seller_username']) ?>
                                </a>
                            </div>
                        </div>
                        
                        <div class="product-stats">
                            <span class="views">
                                <i class="icon-eye"></i> <?= $favorite['views'] ?? 0 ?>
                            </span>
                            <span class="added-date">
                                В избранном с <?= date('d.m.Y', strtotime($favorite['favorited_at'])) ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="product-actions">
                        <a href="/product/<?= $favorite['id'] ?>" class="btn btn-primary">Посмотреть</a>
                        <?php if ($favorite['status'] === 'active' && $favorite['seller_id'] != ($user['id'] ?? 0)): ?>
                            <button class="btn btn-accent" onclick="buyProduct(<?= $favorite['id'] ?>)">Купить</button>
                        <?php endif; ?>
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

.favorites-count {
    color: var(--text-secondary);
    font-size: 1rem;
}

.favorites-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.5rem;
}

.product-card {
    background: var(--card-bg);
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.product-card:hover {
    transform: translateY(-2px);
    border-color: var(--accent-color);
}

.product-image {
    position: relative;
    height: 220px;
    overflow: hidden;
}

.product-image a {
    display: block;
    width: 100%;
    height: 100%;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image img {
    transform: scale(1.05);
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

.favorite-btn {
    position: absolute;
    top: 0.75rem;
    right: 0.75rem;
    width: 40px;
    height: 40px;
    border: none;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.9);
    color: #ef4444;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.favorite-btn:hover {
    background: white;
    transform: scale(1.1);
}

.product-info {
    padding: 1.5rem;
}

.product-category {
    color: var(--accent-color);
    font-size: 0.8rem;
    text-transform: uppercase;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.product-title {
    margin: 0 0 0.75rem;
    font-size: 1.1rem;
}

.product-title a {
    color: var(--text-primary);
    text-decoration: none;
    transition: color 0.3s ease;
}

.product-title a:hover {
    color: var(--accent-color);
}

.product-description {
    color: var(--text-secondary);
    font-size: 0.9rem;
    line-height: 1.4;
    margin: 0 0 1rem;
}

.product-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.product-price {
    font-size: 1.3rem;
    font-weight: bold;
    color: var(--accent-color);
}

.product-seller a {
    color: var(--text-secondary);
    text-decoration: none;
    font-size: 0.9rem;
}

.product-seller a:hover {
    color: var(--accent-color);
    text-decoration: underline;
}

.product-stats {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.8rem;
    color: var(--text-secondary);
    margin-bottom: 1rem;
}

.views {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.product-actions {
    display: flex;
    gap: 0.75rem;
}

.product-actions .btn {
    flex: 1;
    text-align: center;
}

@media (max-width: 768px) {
    .favorites-grid {
        grid-template-columns: 1fr;
    }
    
    .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}
</style>

<script>
function toggleFavorite(productId) {
    fetch('/favorites/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': '<?= csrf_token() ?>'
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Удаляем карточку из избранного
            const card = event.target.closest('.product-card');
            card.style.animation = 'fadeOut 0.3s ease';
            setTimeout(() => {
                card.remove();
                // Обновляем счетчик
                const count = document.querySelector('.favorites-count');
                const currentCount = parseInt(count.textContent);
                count.textContent = `${currentCount - 1} товаров`;
                
                // Показываем пустое состояние если нет товаров
                if (currentCount - 1 === 0) {
                    location.reload();
                }
            }, 300);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function buyProduct(productId) {
    if (confirm('Вы хотите купить этот товар?')) {
        fetch('/purchases/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '<?= csrf_token() ?>'
            },
            body: JSON.stringify({ product_id: productId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = `/purchases/${data.purchase_id}`;
            } else {
                alert(data.message || 'Ошибка при покупке товара');
            }
        })
        .catch(error => {
            alert('Ошибка при покупке товара');
        });
    }
}
</script>

<style>
@keyframes fadeOut {
    from { opacity: 1; transform: scale(1); }
    to { opacity: 0; transform: scale(0.95); }
}
</style>