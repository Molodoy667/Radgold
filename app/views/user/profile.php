<?php $title = 'Мой профиль'; ?>

<div class="container">
    <div class="profile-header">
        <div class="profile-avatar">
            <img src="<?= $user['avatar'] ?? '/assets/images/default-avatar.svg' ?>" alt="Аватар">
        </div>
        <div class="profile-info">
            <h1><?= htmlspecialchars($user['username']) ?></h1>
            <p class="profile-email"><?= htmlspecialchars($user['email']) ?></p>
            <p class="profile-joined">Регистрация: <?= date('d.m.Y', strtotime($user['created_at'])) ?></p>
            <div class="profile-balance">
                <span class="balance-label">Баланс:</span>
                <span class="balance-amount"><?= number_format($user['balance'] ?? 0, 2) ?> ₽</span>
            </div>
        </div>
        <div class="profile-actions">
            <a href="/profile/edit" class="btn btn-primary">
                <i class="icon-edit"></i> Редактировать профиль
            </a>
        </div>
    </div>

    <div class="profile-stats">
        <div class="stat-card">
            <h3>Товары</h3>
            <p class="stat-number"><?= $stats['products'] ?? 0 ?></p>
        </div>
        <div class="stat-card">
            <h3>Покупки</h3>
            <p class="stat-number"><?= $stats['purchases'] ?? 0 ?></p>
        </div>
        <div class="stat-card">
            <h3>Продажи</h3>
            <p class="stat-number"><?= $stats['sales'] ?? 0 ?></p>
        </div>
        <div class="stat-card">
            <h3>Рейтинг</h3>
            <p class="stat-number"><?= number_format($stats['rating'] ?? 0, 1) ?></p>
        </div>
    </div>

    <div class="profile-tabs">
        <div class="tabs-nav">
            <a href="/my-products" class="tab-link">Мои товары</a>
            <a href="/my-purchases" class="tab-link">Мои покупки</a>
            <a href="/my-favorites" class="tab-link">Избранное</a>
        </div>
    </div>
</div>

<style>
.profile-header {
    display: flex;
    align-items: center;
    gap: 2rem;
    margin-bottom: 2rem;
    padding: 2rem;
    background: var(--card-bg);
    border-radius: 12px;
}

.profile-avatar img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
}

.profile-info h1 {
    margin: 0 0 0.5rem;
    color: var(--text-primary);
}

.profile-email {
    color: var(--text-secondary);
    margin: 0.25rem 0;
}

.profile-joined {
    color: var(--text-secondary);
    font-size: 0.9rem;
    margin: 0.25rem 0;
}

.profile-balance {
    margin-top: 1rem;
    padding: 0.75rem;
    background: var(--accent-color);
    border-radius: 8px;
    color: white;
}

.balance-amount {
    font-weight: bold;
    font-size: 1.2rem;
}

.profile-stats {
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

.tabs-nav {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
}

.tab-link {
    padding: 0.75rem 1.5rem;
    background: var(--card-bg);
    color: var(--text-primary);
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.tab-link:hover {
    background: var(--accent-color);
    color: white;
}
</style>