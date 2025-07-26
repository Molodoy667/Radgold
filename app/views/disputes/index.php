<?php $title = 'Споры'; ?>

<div class="container">
    <div class="page-header">
        <h1>Мои споры</h1>
        <span class="disputes-count"><?= count($disputes ?? []) ?> споров</span>
    </div>

    <?php if (empty($disputes)): ?>
        <div class="empty-state">
            <i class="icon-alert-circle"></i>
            <h2>У вас нет активных споров</h2>
            <p>Если у вас возникли проблемы с покупкой, вы можете открыть спор со страницы покупки.</p>
            <a href="/my-purchases" class="btn btn-primary">Мои покупки</a>
        </div>
    <?php else: ?>
        <div class="disputes-list">
            <?php foreach ($disputes as $dispute): ?>
                <div class="dispute-card">
                    <div class="dispute-header">
                        <div class="dispute-id">Спор #<?= $dispute['id'] ?></div>
                        <div class="dispute-status">
                            <span class="status-badge status-<?= $dispute['status'] ?>">
                                <?= $dispute['status'] === 'open' ? 'Открыт' : 
                                   ($dispute['status'] === 'in_progress' ? 'В работе' : 
                                   ($dispute['status'] === 'resolved' ? 'Решен' : 'Закрыт')) ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="dispute-info">
                        <h3>Покупка: <?= htmlspecialchars($dispute['product_title']) ?></h3>
                        <p class="dispute-reason"><strong>Причина:</strong> <?= htmlspecialchars($dispute['reason']) ?></p>
                        <p class="dispute-description"><?= htmlspecialchars($dispute['description']) ?></p>
                        
                        <div class="dispute-meta">
                            <span class="dispute-date">Создан: <?= date('d.m.Y H:i', strtotime($dispute['created_at'])) ?></span>
                            <span class="dispute-amount">Сумма: <?= number_format($dispute['amount'], 2) ?> ₽</span>
                        </div>
                    </div>
                    
                    <div class="dispute-actions">
                        <a href="/disputes/<?= $dispute['id'] ?>" class="btn btn-primary">Подробнее</a>
                        <?php if ($dispute['status'] === 'open'): ?>
                            <button class="btn btn-secondary" onclick="addMessage(<?= $dispute['id'] ?>)">Добавить сообщение</button>
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

.disputes-count {
    color: var(--text-secondary);
    font-size: 1rem;
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

.disputes-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.dispute-card {
    background: var(--card-bg);
    border-radius: 12px;
    padding: 1.5rem;
    transition: transform 0.3s ease;
}

.dispute-card:hover {
    transform: translateY(-2px);
}

.dispute-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--border-color);
}

.dispute-id {
    font-size: 1.1rem;
    font-weight: bold;
    color: var(--text-primary);
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-open {
    background: #f59e0b;
    color: white;
}

.status-in_progress {
    background: #3b82f6;
    color: white;
}

.status-resolved {
    background: #22c55e;
    color: white;
}

.status-closed {
    background: #6b7280;
    color: white;
}

.dispute-info h3 {
    margin: 0 0 1rem;
    color: var(--text-primary);
}

.dispute-reason {
    margin: 0.5rem 0;
    color: var(--text-primary);
}

.dispute-description {
    color: var(--text-secondary);
    margin: 0.5rem 0 1rem;
    line-height: 1.5;
}

.dispute-meta {
    display: flex;
    justify-content: space-between;
    font-size: 0.9rem;
    color: var(--text-secondary);
    margin-bottom: 1rem;
}

.dispute-amount {
    color: var(--accent-color);
    font-weight: bold;
}

.dispute-actions {
    display: flex;
    gap: 1rem;
}

@media (max-width: 768px) {
    .dispute-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .dispute-meta {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .dispute-actions {
        flex-direction: column;
    }
}
</style>

<script>
function addMessage(disputeId) {
    const message = prompt('Введите ваше сообщение:');
    if (message && message.trim()) {
        fetch(`/disputes/${disputeId}/message`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-Token': '<?= csrf_token() ?>'
            },
            body: JSON.stringify({ message: message.trim() })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Сообщение добавлено');
                location.reload();
            } else {
                alert('Ошибка при добавлении сообщения');
            }
        })
        .catch(error => {
            alert('Ошибка при добавлении сообщения');
        });
    }
}
</script>