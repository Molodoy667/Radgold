<?php $title = htmlspecialchars($user['username']); ?>

<div class="container">
    <div class="user-profile">
        <div class="user-header">
            <div class="user-avatar">
                <img src="<?= $user['avatar'] ?? '/assets/images/default-avatar.svg' ?>" alt="Аватар <?= htmlspecialchars($user['username']) ?>">
            </div>
            <div class="user-info">
                <h1><?= htmlspecialchars($user['username']) ?></h1>
                <p class="user-joined">На сайте с <?= date('d.m.Y', strtotime($user['created_at'])) ?></p>
                <div class="user-rating">
                    <span class="rating-stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="icon-star<?= $i <= ($user['rating'] ?? 0) ? '-fill' : '' ?>"></i>
                        <?php endfor; ?>
                    </span>
                    <span class="rating-value"><?= number_format($user['rating'] ?? 0, 1) ?></span>
                    <span class="rating-count">(<?= $user['reviews_count'] ?? 0 ?> отзывов)</span>
                </div>
            </div>
            <div class="user-actions">
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] !== $user['id']): ?>
                    <a href="/messages/<?= $user['id'] ?>" class="btn btn-primary">
                        <i class="icon-mail"></i> Написать
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="user-stats">
            <div class="stat-card">
                <h3>Товары</h3>
                <p class="stat-number"><?= $stats['products'] ?? 0 ?></p>
            </div>
            <div class="stat-card">
                <h3>Продажи</h3>
                <p class="stat-number"><?= $stats['sales'] ?? 0 ?></p>
            </div>
            <div class="stat-card">
                <h3>Успешных сделок</h3>
                <p class="stat-number"><?= $stats['completed_deals'] ?? 0 ?></p>
            </div>
        </div>

        <div class="user-products">
            <h2>Товары пользователя</h2>
            <?php if (empty($products)): ?>
                <div class="empty-state">
                    <p>У пользователя пока нет активных товаров</p>
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <div class="product-image">
                                <a href="/product/<?= $product['id'] ?>">
                                    <?php if (!empty($product['images'])): ?>
                                        <img src="<?= htmlspecialchars($product['images'][0]) ?>" alt="<?= htmlspecialchars($product['title']) ?>">
                                    <?php else: ?>
                                        <div class="no-image">
                                            <i class="icon-image"></i>
                                        </div>
                                    <?php endif; ?>
                                </a>
                            </div>
                            <div class="product-info">
                                <h3 class="product-title">
                                    <a href="/product/<?= $product['id'] ?>"><?= htmlspecialchars($product['title']) ?></a>
                                </h3>
                                <p class="product-price"><?= number_format($product['price'], 2) ?> ₽</p>
                                <p class="product-views">Просмотры: <?= $product['views'] ?? 0 ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.user-header {
    display: flex;
    align-items: center;
    gap: 2rem;
    margin-bottom: 2rem;
    padding: 2rem;
    background: var(--card-bg);
    border-radius: 12px;
}

.user-avatar img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
}

.user-info h1 {
    margin: 0 0 0.5rem;
    color: var(--text-primary);
}

.user-joined {
    color: var(--text-secondary);
    margin: 0.25rem 0;
}

.user-rating {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 1rem;
}

.rating-stars {
    color: #fbbf24;
}

.rating-value {
    font-weight: bold;
    color: var(--text-primary);
}

.rating-count {
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.user-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    padding: 1.5rem;
    background: var(--card-bg);
    border-radius: 12px;
    text-align: center;
}

.stat-card h3 {
    margin: 0 0 0.5rem;
    color: var(--text-secondary);
    font-size: 0.9rem;
    text-transform: uppercase;
}

.stat-number {
    margin: 0;
    font-size: 2rem;
    font-weight: bold;
    color: var(--accent-color);
}

.user-products h2 {
    margin-bottom: 1.5rem;
    color: var(--text-primary);
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
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
    height: 180px;
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
    font-size: 2.5rem;
}

.product-info {
    padding: 1rem;
}

.product-title {
    margin: 0 0 0.5rem;
    font-size: 1rem;
}

.product-title a {
    color: var(--text-primary);
    text-decoration: none;
}

.product-title a:hover {
    color: var(--accent-color);
}

.product-price {
    margin: 0 0 0.5rem;
    font-size: 1.1rem;
    font-weight: bold;
    color: var(--accent-color);
}

.product-views {
    margin: 0;
    color: var(--text-secondary);
    font-size: 0.85rem;
}

.empty-state {
    text-align: center;
    padding: 2rem;
    color: var(--text-secondary);
}
</style>