<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="windows-1251">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Диспуты - Game Marketplace</title>
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
                <h1>Диспуты</h1>
                <div class="header-stats">
                    <div class="stat-card">
                        <span class="stat-icon">⚖️</span>
                        <span class="stat-value"><?= $totalDisputes ?? 0 ?></span>
                        <span class="stat-label">Всего диспутов</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-icon">🔄</span>
                        <span class="stat-value"><?= $openDisputes ?? 0 ?></span>
                        <span class="stat-label">Открытых</span>
                    </div>
                    <div class="stat-card">
                        <span class="stat-icon">✅</span>
                        <span class="stat-value"><?= $resolvedDisputes ?? 0 ?></span>
                        <span class="stat-label">Решенных</span>
                    </div>
                </div>
            </div>
            
            <div class="disputes-actions">
                <button onclick="showCreateDisputeForm()" class="btn-primary">Создать диспут</button>
            </div>
            
            <div class="filters-section">
                <form class="filter-form" method="GET">
                    <div class="filter-group">
                        <select name="status" onchange="this.form.submit()">
                            <option value="">Все статусы</option>
                            <option value="open" <?= ($filters['status'] ?? '') === 'open' ? 'selected' : '' ?>>Открыт</option>
                            <option value="in_progress" <?= ($filters['status'] ?? '') === 'in_progress' ? 'selected' : '' ?>>В обработке</option>
                            <option value="resolved" <?= ($filters['status'] ?? '') === 'resolved' ? 'selected' : '' ?>>Решен</option>
                            <option value="closed" <?= ($filters['status'] ?? '') === 'closed' ? 'selected' : '' ?>>Закрыт</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <select name="type" onchange="this.form.submit()">
                            <option value="">Все типы</option>
                            <option value="refund" <?= ($filters['type'] ?? '') === 'refund' ? 'selected' : '' ?>>Возврат средств</option>
                            <option value="quality" <?= ($filters['type'] ?? '') === 'quality' ? 'selected' : '' ?>>Качество товара</option>
                            <option value="delivery" <?= ($filters['type'] ?? '') === 'delivery' ? 'selected' : '' ?>>Проблемы с доставкой</option>
                            <option value="other" <?= ($filters['type'] ?? '') === 'other' ? 'selected' : '' ?>>Другое</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <input type="text" name="search" placeholder="Поиск по теме..." 
                               value="<?= htmlspecialchars($filters['search'] ?? '') ?>" onchange="this.form.submit()">
                    </div>
                </form>
            </div>
            
            <div class="disputes-section">
                <div class="disputes-header">
                    <h3>Мои диспуты (<?= $totalItems ?? 0 ?>)</h3>
                    <?php if (($totalPages ?? 0) > 1): ?>
                        <div class="pagination-info">
                            Страница <?= $currentPage ?? 1 ?> из <?= $totalPages ?? 1 ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if (empty($disputes ?? [])): ?>
                    <div class="empty-disputes">
                        <div class="empty-icon">⚖️</div>
                        <h4>У вас пока нет диспутов</h4>
                        <p>Диспуты создаются для решения проблем с покупками</p>
                        <button onclick="showCreateDisputeForm()" class="btn-primary">Создать первый диспут</button>
                    </div>
                <?php else: ?>
                    <div class="disputes-list">
                        <?php foreach (($disputes ?? []) as $dispute): ?>
                            <div class="dispute-card">
                                <div class="dispute-header">
                                    <div class="dispute-id">
                                        <span class="id-label">Диспут #<?= $dispute['id'] ?></span>
                                        <span class="dispute-date"><?= date('d.m.Y H:i', strtotime($dispute['created_at'])) ?></span>
                                    </div>
                                    <div class="dispute-status">
                                        <span class="status-badge status-<?= $dispute['status'] ?>">
                                            <?= $statusLabels[$dispute['status']] ?? $dispute['status'] ?>
                                        </span>
                                        <?php if ($dispute['priority'] === 'urgent'): ?>
                                            <span class="priority-badge urgent">Срочно</span>
                                        <?php elseif ($dispute['priority'] === 'high'): ?>
                                            <span class="priority-badge high">Высокий</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="dispute-content">
                                    <div class="dispute-type">
                                        <span class="type-badge type-<?= $dispute['type'] ?>">
                                            <?= $typeLabels[$dispute['type']] ?? $dispute['type'] ?>
                                        </span>
                                    </div>
                                    
                                    <div class="dispute-subject">
                                        <h4><?= htmlspecialchars($dispute['subject']) ?></h4>
                                    </div>
                                    
                                    <div class="dispute-description">
                                        <p><?= htmlspecialchars(substr($dispute['description'], 0, 150)) ?>
                                        <?= strlen($dispute['description']) > 150 ? '...' : '' ?></p>
                                    </div>
                                    
                                    <?php if (isset($dispute['purchase'])): ?>
                                        <div class="purchase-info">
                                            <div class="purchase-details">
                                                <span class="purchase-label">Покупка:</span>
                                                <span class="product-title"><?= htmlspecialchars($dispute['purchase']['product_title']) ?></span>
                                                <span class="purchase-price"><?= number_format($dispute['purchase']['price'], 0, ',', ' ') ?> ₽</span>
                                            </div>
                                            <div class="purchase-date">
                                                <?= date('d.m.Y', strtotime($dispute['purchase']['created_at'])) ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($dispute['admin_response']): ?>
                                        <div class="admin-response">
                                            <div class="response-header">
                                                <span class="response-label">Ответ администратора:</span>
                                                <span class="response-date"><?= date('d.m.Y H:i', strtotime($dispute['updated_at'])) ?></span>
                                            </div>
                                            <div class="response-content">
                                                <p><?= htmlspecialchars($dispute['admin_response']) ?></p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($dispute['resolution']): ?>
                                        <div class="dispute-resolution">
                                            <div class="resolution-header">
                                                <span class="resolution-label">Решение:</span>
                                            </div>
                                            <div class="resolution-content">
                                                <p><?= htmlspecialchars($dispute['resolution']) ?></p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="dispute-actions">
                                    <button onclick="viewDispute(<?= $dispute['id'] ?>)" class="btn-secondary">Просмотр</button>
                                    <?php if ($dispute['status'] === 'open'): ?>
                                        <button onclick="editDispute(<?= $dispute['id'] ?>)" class="btn-secondary">Редактировать</button>
                                        <button onclick="closeDispute(<?= $dispute['id'] ?>)" class="btn-danger">Закрыть</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (($totalPages ?? 0) > 1): ?>
                        <div class="pagination">
                            <?php if (($currentPage ?? 1) > 1): ?>
                                <a href="?page=<?= ($currentPage ?? 1) - 1 ?>&<?= http_build_query(array_diff_key($filters ?? [], ['page' => ''])) ?>" class="page-link">← Назад</a>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, ($currentPage ?? 1) - 2); $i <= min($totalPages ?? 1, ($currentPage ?? 1) + 2); $i++): ?>
                                <a href="?page=<?= $i ?>&<?= http_build_query(array_diff_key($filters ?? [], ['page' => ''])) ?>" 
                                   class="page-link <?= $i === ($currentPage ?? 1) ? 'active' : '' ?>"><?= $i ?></a>
                            <?php endfor; ?>
                            
                            <?php if (($currentPage ?? 1) < ($totalPages ?? 1)): ?>
                                <a href="?page=<?= ($currentPage ?? 1) + 1 ?>&<?= http_build_query(array_diff_key($filters ?? [], ['page' => ''])) ?>" class="page-link">Вперед →</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <script>
        function showCreateDisputeForm() {
            alert('Функция создания диспута будет реализована в следующем обновлении');
        }
        
        function viewDispute(disputeId) {
            alert('Просмотр диспута #' + disputeId + ' будет реализован в следующем обновлении');
        }
        
        function editDispute(disputeId) {
            alert('Редактирование диспута #' + disputeId + ' будет реализовано в следующем обновлении');
        }
        
        function closeDispute(disputeId) {
            if (confirm('Вы уверены, что хотите закрыть этот диспут?')) {
                alert('Закрытие диспута #' + disputeId + ' будет реализовано в следующем обновлении');
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
        
        .disputes-actions {
            margin-bottom: 20px;
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
        
        .disputes-section {
            background: var(--bg-secondary);
            border-radius: 12px;
            overflow: hidden;
        }
        
        .disputes-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            background: var(--bg-primary);
        }
        
        .disputes-header h3 {
            margin: 0;
            color: var(--text-primary);
        }
        
        .pagination-info {
            font-size: 14px;
            color: var(--text-secondary);
        }
        
        .empty-disputes {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }
        
        .empty-disputes .empty-icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        
        .empty-disputes h4 {
            margin: 0 0 10px 0;
            color: var(--text-primary);
        }
        
        .empty-disputes p {
            margin: 0 0 20px 0;
            font-size: 14px;
        }
        
        .disputes-list {
            padding: 20px;
        }
        
        .dispute-card {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        
        .dispute-card:hover {
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .dispute-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .dispute-id {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .id-label {
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .dispute-date {
            font-size: 12px;
            color: var(--text-secondary);
        }
        
        .dispute-status {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-open {
            background: var(--warning-color);
            color: white;
        }
        
        .status-in_progress {
            background: var(--primary-color);
            color: white;
        }
        
        .status-resolved {
            background: var(--success-color);
            color: white;
        }
        
        .status-closed {
            background: var(--text-secondary);
            color: white;
        }
        
        .priority-badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .priority-badge.urgent {
            background: var(--danger-color);
            color: white;
        }
        
        .priority-badge.high {
            background: var(--warning-color);
            color: white;
        }
        
        .dispute-content {
            margin-bottom: 20px;
        }
        
        .dispute-type {
            margin-bottom: 10px;
        }
        
        .type-badge {
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .type-refund {
            background: var(--danger-color);
            color: white;
        }
        
        .type-quality {
            background: var(--warning-color);
            color: white;
        }
        
        .type-delivery {
            background: var(--info-color);
            color: white;
        }
        
        .type-other {
            background: var(--text-secondary);
            color: white;
        }
        
        .dispute-subject h4 {
            margin: 0 0 10px 0;
            color: var(--text-primary);
            font-size: 16px;
        }
        
        .dispute-description p {
            margin: 0 0 15px 0;
            color: var(--text-secondary);
            line-height: 1.5;
        }
        
        .purchase-info {
            background: var(--bg-secondary);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .purchase-details {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 5px;
        }
        
        .purchase-label {
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .product-title {
            color: var(--primary-color);
            font-weight: 500;
        }
        
        .purchase-price {
            color: var(--success-color);
            font-weight: 600;
        }
        
        .purchase-date {
            font-size: 12px;
            color: var(--text-secondary);
        }
        
        .admin-response,
        .dispute-resolution {
            background: var(--bg-secondary);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 4px solid var(--primary-color);
        }
        
        .response-header,
        .resolution-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .response-label,
        .resolution-label {
            font-weight: 600;
            color: var(--text-primary);
        }
        
        .response-date {
            font-size: 12px;
            color: var(--text-secondary);
        }
        
        .response-content p,
        .resolution-content p {
            margin: 0;
            color: var(--text-secondary);
            line-height: 1.5;
        }
        
        .dispute-actions {
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
            
            .dispute-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .dispute-status {
                align-self: flex-end;
            }
            
            .purchase-details {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
            
            .dispute-actions {
                flex-direction: column;
            }
        }
    </style>
</body>
</html>