<?php $title = 'Панель администратора'; ?>

<div class="container">
    <div class="admin-header">
        <h1>Панель администратора</h1>
        <div class="admin-actions">
            <a href="/" class="btn btn-secondary">← На сайт</a>
        </div>
    </div>

    <div class="dashboard-content">
        <div class="stats-overview">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="icon-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?= number_format($stats['users'] ?? 0) ?></h3>
                    <p>Пользователей</p>
                    <small class="stat-change positive">+<?= $stats['new_users'] ?? 0 ?> за неделю</small>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="icon-package"></i>
                </div>
                <div class="stat-info">
                    <h3><?= number_format($stats['products'] ?? 0) ?></h3>
                    <p>Товаров</p>
                    <small class="stat-change positive">+<?= $stats['new_products'] ?? 0 ?> за неделю</small>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="icon-shopping-cart"></i>
                </div>
                <div class="stat-info">
                    <h3><?= number_format($stats['purchases'] ?? 0) ?></h3>
                    <p>Покупок</p>
                    <small class="stat-change positive">+<?= $stats['new_purchases'] ?? 0 ?> за неделю</small>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="icon-dollar-sign"></i>
                </div>
                <div class="stat-info">
                    <h3><?= number_format($stats['revenue'] ?? 0, 2) ?> ₽</h3>
                    <p>Доходы</p>
                    <small class="stat-change positive">+<?= number_format($stats['weekly_revenue'] ?? 0, 2) ?> ₽ за неделю</small>
                </div>
            </div>
        </div>

        <div class="dashboard-grid">
            <div class="recent-section">
                <h2>Последние действия</h2>
                <div class="recent-items">
                    <?php if (!empty($recent_activities)): ?>
                        <?php foreach ($recent_activities as $activity): ?>
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i class="icon-<?= $activity['type'] ?>"></i>
                                </div>
                                <div class="activity-info">
                                    <p><?= htmlspecialchars($activity['description']) ?></p>
                                    <small><?= date('d.m.Y H:i', strtotime($activity['created_at'])) ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="empty-state">Нет активности</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="quick-actions">
                <h2>Быстрые действия</h2>
                <div class="action-buttons">
                    <a href="/admin/users" class="action-btn">
                        <i class="icon-users"></i>
                        <span>Управление пользователями</span>
                    </a>
                    <a href="/admin/products" class="action-btn">
                        <i class="icon-package"></i>
                        <span>Модерация товаров</span>
                    </a>
                    <a href="/admin/disputes" class="action-btn">
                        <i class="icon-alert-circle"></i>
                        <span>Споры</span>
                    </a>
                    <a href="/admin/settings" class="action-btn">
                        <i class="icon-settings"></i>
                        <span>Настройки системы</span>
                    </a>
                </div>
            </div>

            <div class="pending-items">
                <h2>Требует внимания</h2>
                <div class="pending-list">
                    <?php if (!empty($pending_items)): ?>
                        <?php foreach ($pending_items as $item): ?>
                            <div class="pending-item">
                                <div class="pending-info">
                                    <span class="pending-type"><?= htmlspecialchars($item['type']) ?></span>
                                    <p><?= htmlspecialchars($item['description']) ?></p>
                                </div>
                                <div class="pending-count">
                                    <span class="count"><?= $item['count'] ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="empty-state">Все задачи выполнены ✅</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="system-info">
                <h2>Система</h2>
                <div class="system-stats">
                    <div class="system-item">
                        <span class="label">Версия PHP:</span>
                        <span class="value"><?= PHP_VERSION ?></span>
                    </div>
                    <div class="system-item">
                        <span class="label">Использование памяти:</span>
                        <span class="value"><?= round(memory_get_usage() / 1024 / 1024, 2) ?> MB</span>
                    </div>
                    <div class="system-item">
                        <span class="label">Время работы:</span>
                        <span class="value"><?= gmdate('H:i:s', time() - $_SERVER['REQUEST_TIME']) ?></span>
                    </div>
                    <div class="system-item">
                        <span class="label">База данных:</span>
                        <span class="value status-ok">Подключена</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.admin-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--border-color);
}

.stats-overview {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--card-bg);
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.stat-icon {
    width: 60px;
    height: 60px;
    background: var(--accent-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

.stat-info h3 {
    margin: 0;
    font-size: 2rem;
    color: var(--text-primary);
}

.stat-info p {
    margin: 0.25rem 0;
    color: var(--text-secondary);
}

.stat-change {
    font-size: 0.8rem;
    font-weight: 500;
}

.stat-change.positive {
    color: #22c55e;
}

.stat-change.negative {
    color: #ef4444;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}

.recent-section,
.quick-actions,
.pending-items,
.system-info {
    background: var(--card-bg);
    border-radius: 12px;
    padding: 1.5rem;
}

.recent-section h2,
.quick-actions h2,
.pending-items h2,
.system-info h2 {
    margin: 0 0 1rem;
    color: var(--text-primary);
    border-bottom: 2px solid var(--border-color);
    padding-bottom: 0.5rem;
}

.recent-items {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.activity-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: var(--bg-color);
    border-radius: 8px;
}

.activity-icon {
    width: 40px;
    height: 40px;
    background: var(--accent-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
}

.activity-info p {
    margin: 0 0 0.25rem;
    color: var(--text-primary);
    font-size: 0.9rem;
}

.activity-info small {
    color: var(--text-secondary);
    font-size: 0.8rem;
}

.action-buttons {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.action-btn {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1.5rem;
    background: var(--bg-color);
    border-radius: 8px;
    text-decoration: none;
    color: var(--text-primary);
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.action-btn:hover {
    border-color: var(--accent-color);
    transform: translateY(-2px);
    color: var(--text-primary);
}

.action-btn i {
    font-size: 2rem;
    color: var(--accent-color);
}

.action-btn span {
    font-size: 0.9rem;
    text-align: center;
}

.pending-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.pending-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: var(--bg-color);
    border-radius: 8px;
}

.pending-type {
    font-size: 0.8rem;
    color: var(--accent-color);
    font-weight: 500;
    text-transform: uppercase;
}

.pending-info p {
    margin: 0.25rem 0 0;
    color: var(--text-primary);
    font-size: 0.9rem;
}

.pending-count .count {
    background: var(--accent-color);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: bold;
}

.system-stats {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.system-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background: var(--bg-color);
    border-radius: 6px;
}

.system-item .label {
    color: var(--text-secondary);
    font-size: 0.9rem;
}

.system-item .value {
    color: var(--text-primary);
    font-weight: 500;
}

.status-ok {
    color: #22c55e !important;
}

.empty-state {
    text-align: center;
    color: var(--text-secondary);
    font-style: italic;
    padding: 2rem 0;
}

@media (max-width: 768px) {
    .stats-overview {
        grid-template-columns: 1fr;
    }
    
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        grid-template-columns: 1fr;
    }
    
    .admin-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
}
</style>