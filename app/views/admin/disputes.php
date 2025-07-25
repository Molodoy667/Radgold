<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Обработка диспутов - Админ панель</title>
    <link rel="stylesheet" href="/assets/css/theme.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
    <script src="/assets/js/theme.js"></script>
</head>
<body>
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h2>🎮 Админ панель</h2>
            </div>
            <nav class="sidebar-nav">
                <a href="/admin" class="nav-item">
                    <span class="nav-icon">📊</span>
                    <span class="nav-text">Дашборд</span>
                </a>
                <a href="/admin/users" class="nav-item">
                    <span class="nav-icon">👥</span>
                    <span class="nav-text">Пользователи</span>
                </a>
                <a href="/admin/products" class="nav-item">
                    <span class="nav-icon">📦</span>
                    <span class="nav-text">Товары</span>
                </a>
                <a href="/admin/disputes" class="nav-item active">
                    <span class="nav-icon">⚠️</span>
                    <span class="nav-text">Диспуты</span>
                </a>
                <a href="/admin/reviews" class="nav-item">
                    <span class="nav-icon">⭐</span>
                    <span class="nav-text">Отзывы</span>
                </a>
                <a href="/admin/settings" class="nav-item">
                    <span class="nav-icon">⚙️</span>
                    <span class="nav-text">Настройки</span>
                </a>
                <a href="/" class="nav-item">
                    <span class="nav-icon">🏠</span>
                    <span class="nav-text">На сайт</span>
                </a>
            </nav>
        </aside>
        
        <main class="admin-main">
            <header class="admin-header">
                <h1>Обработка диспутов</h1>
                <div class="header-actions">
                    <span class="admin-info">Админ: <?= htmlspecialchars($_SESSION['user']['login']) ?></span>
                </div>
            </header>
            
            <div class="admin-content">
                <div class="filters-section">
                    <form method="GET" class="filter-form">
                        <div class="filter-row">
                            <div class="filter-group">
                                <input type="text" name="search" placeholder="Поиск по теме..." 
                                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                            </div>
                            <div class="filter-group">
                                <select name="status">
                                    <option value="">Все статусы</option>
                                    <option value="open" <?= ($_GET['status'] ?? '') === 'open' ? 'selected' : '' ?>>Открытые</option>
                                    <option value="in_progress" <?= ($_GET['status'] ?? '') === 'in_progress' ? 'selected' : '' ?>>В обработке</option>
                                    <option value="resolved" <?= ($_GET['status'] ?? '') === 'resolved' ? 'selected' : '' ?>>Разрешенные</option>
                                    <option value="closed" <?= ($_GET['status'] ?? '') === 'closed' ? 'selected' : '' ?>>Закрытые</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <select name="type">
                                    <option value="">Все типы</option>
                                    <option value="refund" <?= ($_GET['type'] ?? '') === 'refund' ? 'selected' : '' ?>>Возврат средств</option>
                                    <option value="quality" <?= ($_GET['type'] ?? '') === 'quality' ? 'selected' : '' ?>>Качество товара</option>
                                    <option value="delivery" <?= ($_GET['type'] ?? '') === 'delivery' ? 'selected' : '' ?>>Проблемы с доставкой</option>
                                    <option value="fraud" <?= ($_GET['type'] ?? '') === 'fraud' ? 'selected' : '' ?>>Мошенничество</option>
                                    <option value="other" <?= ($_GET['type'] ?? '') === 'other' ? 'selected' : '' ?>>Другое</option>
                                </select>
                            </div>
                            <div class="filter-group">
                                <select name="priority">
                                    <option value="">Все приоритеты</option>
                                    <option value="low" <?= ($_GET['priority'] ?? '') === 'low' ? 'selected' : '' ?>>Низкий</option>
                                    <option value="medium" <?= ($_GET['priority'] ?? '') === 'medium' ? 'selected' : '' ?>>Средний</option>
                                    <option value="high" <?= ($_GET['priority'] ?? '') === 'high' ? 'selected' : '' ?>>Высокий</option>
                                    <option value="urgent" <?= ($_GET['priority'] ?? '') === 'urgent' ? 'selected' : '' ?>>Срочный</option>
                                </select>
                            </div>
                            <button type="submit" class="btn-primary">Фильтровать</button>
                            <a href="/admin/disputes" class="btn-secondary">Сбросить</a>
                        </div>
                    </form>
                </div>
                
                <div class="table-section">
                    <div class="table-header">
                        <h2>Диспуты (<?= $totalDisputes ?> всего)</h2>
                        <div class="table-actions">
                            <span class="pagination-info">
                                Страница <?= $currentPage ?> из <?= $totalPages ?>
                            </span>
                        </div>
                    </div>
                    
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Покупка</th>
                                <th>Пользователь</th>
                                <th>Тип</th>
                                <th>Тема</th>
                                <th>Приоритет</th>
                                <th>Статус</th>
                                <th>Админ</th>
                                <th>Дата</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($disputes as $dispute): ?>
                                <tr class="dispute-row priority-<?= $dispute['priority'] ?>">
                                    <td><?= $dispute['id'] ?></td>
                                    <td>
                                        <div class="purchase-info">
                                            <span class="purchase-id">#<?= $dispute['purchase_id'] ?></span>
                                            <span class="purchase-price"><?= number_format($dispute['purchase_price']) ?> <?= htmlspecialchars($dispute['purchase_currency']) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="user-info">
                                            <span class="user-login"><?= htmlspecialchars($dispute['user_login']) ?></span>
                                            <span class="user-email"><?= htmlspecialchars($dispute['user_email']) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $dispute['type'] ?>">
                                            <?= $dispute['type'] === 'refund' ? 'Возврат' :
                                               ($dispute['type'] === 'quality' ? 'Качество' :
                                               ($dispute['type'] === 'delivery' ? 'Доставка' :
                                               ($dispute['type'] === 'fraud' ? 'Мошенничество' : 'Другое'))) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="dispute-subject">
                                            <div class="subject-title"><?= htmlspecialchars($dispute['subject']) ?></div>
                                            <div class="subject-desc"><?= htmlspecialchars(substr($dispute['description'], 0, 50)) ?>...</div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-priority-<?= $dispute['priority'] ?>">
                                            <?= $dispute['priority'] === 'low' ? 'Низкий' :
                                               ($dispute['priority'] === 'medium' ? 'Средний' :
                                               ($dispute['priority'] === 'high' ? 'Высокий' : 'Срочный')) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $dispute['status'] ?>">
                                            <?= $dispute['status'] === 'open' ? 'Открыт' :
                                               ($dispute['status'] === 'in_progress' ? 'В обработке' :
                                               ($dispute['status'] === 'resolved' ? 'Разрешен' : 'Закрыт')) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($dispute['admin_login']): ?>
                                            <span class="admin-name"><?= htmlspecialchars($dispute['admin_login']) ?></span>
                                        <?php else: ?>
                                            <span class="no-admin">Не назначен</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d.m.Y H:i', strtotime($dispute['created_at'])) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button onclick="viewDispute(<?= $dispute['id'] ?>)" 
                                                    class="btn-action btn-info" title="Просмотреть">
                                                👁️
                                            </button>
                                            <?php if ($dispute['status'] === 'open'): ?>
                                                <button onclick="takeDispute(<?= $dispute['id'] ?>)" 
                                                        class="btn-action btn-primary" title="Взять в работу">
                                                    📝
                                                </button>
                                            <?php endif; ?>
                                            <?php if ($dispute['status'] === 'in_progress'): ?>
                                                <button onclick="resolveDispute(<?= $dispute['id'] ?>)" 
                                                        class="btn-action btn-success" title="Разрешить">
                                                    ✅
                                                </button>
                                                <button onclick="closeDispute(<?= $dispute['id'] ?>)" 
                                                        class="btn-action btn-warning" title="Закрыть">
                                                    🔒
                                                </button>
                                            <?php endif; ?>
                                            <button onclick="deleteDispute(<?= $dispute['id'] ?>)" 
                                                    class="btn-action btn-danger" title="Удалить">
                                                🗑️
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <?php if ($totalPages > 1): ?>
                        <div class="pagination">
                            <?php if ($currentPage > 1): ?>
                                <a href="?page=<?= $currentPage - 1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&status=<?= urlencode($_GET['status'] ?? '') ?>&type=<?= urlencode($_GET['type'] ?? '') ?>&priority=<?= urlencode($_GET['priority'] ?? '') ?>" 
                                   class="page-link">← Назад</a>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++): ?>
                                <a href="?page=<?= $i ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&status=<?= urlencode($_GET['status'] ?? '') ?>&type=<?= urlencode($_GET['type'] ?? '') ?>&priority=<?= urlencode($_GET['priority'] ?? '') ?>" 
                                   class="page-link <?= $i === $currentPage ? 'active' : '' ?>"><?= $i ?></a>
                            <?php endfor; ?>
                            
                            <?php if ($currentPage < $totalPages): ?>
                                <a href="?page=<?= $currentPage + 1 ?>&search=<?= urlencode($_GET['search'] ?? '') ?>&status=<?= urlencode($_GET['status'] ?? '') ?>&type=<?= urlencode($_GET['type'] ?? '') ?>&priority=<?= urlencode($_GET['priority'] ?? '') ?>" 
                                   class="page-link">Вперед →</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Модальное окно для просмотра диспута -->
    <div id="disputeModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Детали диспута</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body" id="disputeDetails">
                <!-- Содержимое будет загружено через AJAX -->
            </div>
        </div>
    </div>
    
    <script>
        function viewDispute(disputeId) {
            fetch(`/admin/disputes/${disputeId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('disputeDetails').innerHTML = data.html;
                        document.getElementById('disputeModal').style.display = 'block';
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
            document.getElementById('disputeModal').style.display = 'none';
        }
        
        function takeDispute(disputeId) {
            if (confirm('Взять этот диспут в работу?')) {
                fetch('/admin/disputes/take', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ dispute_id: disputeId })
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
        
        function resolveDispute(disputeId) {
            const resolution = prompt('Решение диспута:');
            if (resolution !== null) {
                fetch('/admin/disputes/resolve', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ 
                        dispute_id: disputeId,
                        resolution: resolution
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
        
        function closeDispute(disputeId) {
            if (confirm('Закрыть этот диспут?')) {
                fetch('/admin/disputes/close', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ dispute_id: disputeId })
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
        
        function deleteDispute(disputeId) {
            if (confirm('Удалить этот диспут навсегда?')) {
                fetch('/admin/disputes/delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ dispute_id: disputeId })
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
            const modal = document.getElementById('disputeModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>
    
    <style>
        .dispute-row.priority-urgent {
            background: rgba(255, 0, 0, 0.1);
            border-left: 4px solid var(--danger-color);
        }
        
        .dispute-row.priority-high {
            background: rgba(255, 165, 0, 0.1);
            border-left: 4px solid var(--warning-color);
        }
        
        .purchase-info {
            display: flex;
            flex-direction: column;
        }
        
        .purchase-id {
            font-weight: 600;
        }
        
        .purchase-price {
            font-size: 12px;
            color: var(--text-secondary);
        }
        
        .dispute-subject {
            max-width: 250px;
        }
        
        .subject-title {
            font-weight: 600;
            margin-bottom: 4px;
        }
        
        .subject-desc {
            font-size: 12px;
            color: var(--text-secondary);
        }
        
        .admin-name {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .no-admin {
            font-size: 12px;
            color: var(--text-secondary);
            font-style: italic;
        }
        
        .badge-refund { background: var(--primary-color); }
        .badge-quality { background: var(--warning-color); }
        .badge-delivery { background: var(--info-color); }
        .badge-fraud { background: var(--danger-color); }
        .badge-other { background: var(--secondary-color); }
        
        .badge-priority-low { background: var(--success-color); }
        .badge-priority-medium { background: var(--info-color); }
        .badge-priority-high { background: var(--warning-color); }
        .badge-priority-urgent { background: var(--danger-color); }
        
        .badge-open { background: var(--warning-color); }
        .badge-in_progress { background: var(--primary-color); }
        .badge-resolved { background: var(--success-color); }
        .badge-closed { background: var(--secondary-color); }
        
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
            width: 80%;
            max-width: 800px;
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
            max-height: 70vh;
            overflow-y: auto;
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
        
        @media (max-width: 768px) {
            .admin-table {
                font-size: 12px;
            }
            
            .dispute-subject {
                max-width: 150px;
            }
            
            .action-buttons {
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