<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="windows-1251">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление пользователями - Админ панель</title>
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
                <a href="/admin/users" class="nav-item active">
                    <span class="nav-icon">👥</span>
                    <span class="nav-text">Пользователи</span>
                </a>
                <a href="/admin/products" class="nav-item">
                    <span class="nav-icon">📦</span>
                    <span class="nav-text">Товары</span>
                </a>
                <a href="/admin/disputes" class="nav-item">
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
                <a href="/profile" class="nav-item">
                    <span class="nav-icon">👤</span>
                    <span class="nav-text">Профиль</span>
                </a>
                <a href="/logout" class="nav-item">
                    <span class="nav-icon">🚪</span>
                    <span class="nav-text">Выйти</span>
                </a>
            </nav>
        </aside>

        <main class="admin-main">
            <header class="admin-header">
                <div class="header-content">
                    <h1>👥 Управление пользователями</h1>
                    <div class="header-actions">
                        <button onclick="toggleTheme()" class="btn-theme">🌙</button>
                    </div>
                </div>
            </header>

            <div class="admin-content">
                <!-- Фильтры -->
                <div class="filters-section">
                    <form method="GET" class="admin-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="search">Поиск</label>
                                <input type="text" id="search" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Логин или email">
                            </div>
                            <div class="form-group">
                                <label for="role">Роль</label>
                                <select id="role" name="role">
                                    <option value="">Все роли</option>
                                    <option value="user" <?= $role === 'user' ? 'selected' : '' ?>>Пользователь</option>
                                    <option value="seller" <?= $role === 'seller' ? 'selected' : '' ?>>Продавец</option>
                                    <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Администратор</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="status">Статус</label>
                                <select id="status" name="status">
                                    <option value="">Все статусы</option>
                                    <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Активные</option>
                                    <option value="banned" <?= $status === 'banned' ? 'selected' : '' ?>>Заблокированные</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn-primary">Применить</button>
                                <a href="/admin/users" class="btn-secondary">Сбросить</a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Таблица пользователей -->
                <div class="table-section">
                    <div class="table-header">
                        <h3>Пользователи (<?= $totalUsers ?>)</h3>
                        <div class="table-actions">
                            <span>Страница <?= $page ?> из <?= $totalPages ?></span>
                        </div>
                    </div>

                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Логин</th>
                                <th>Email</th>
                                <th>Роль</th>
                                <th>Статус</th>
                                <th>Баланс</th>
                                <th>Рейтинг</th>
                                <th>Дата регистрации</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td>
                                        <div class="user-info">
                                            <div class="user-avatar">
                                                <img src="<?= $user['avatar'] ?? '/assets/images/default-avatar.png' ?>" alt="Аватар">
                                            </div>
                                            <div class="user-details">
                                                <div class="user-name"><?= htmlspecialchars($user['login']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <span class="role-badge role-<?= $user['role'] ?>">
                                            <?= ucfirst($user['role']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?= $user['status'] ?>">
                                            <?= $user['status'] === 'active' ? 'Активен' : 'Заблокирован' ?>
                                        </span>
                                    </td>
                                    <td><?= number_format($user['balance'], 2) ?> ₽</td>
                                    <td>
                                        <?php if ($user['rating']): ?>
                                            ⭐ <?= number_format($user['rating'], 1) ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d.m.Y H:i', strtotime($user['created_at'])) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <?php if ($user['status'] === 'active'): ?>
                                                <button onclick="banUser(<?= $user['id'] ?>)" class="btn-action btn-ban">Заблокировать</button>
                                            <?php else: ?>
                                                <button onclick="unbanUser(<?= $user['id'] ?>)" class="btn-action btn-unban">Разблокировать</button>
                                            <?php endif; ?>
                                            
                                            <select onchange="changeRole(<?= $user['id'] ?>, this.value)" class="role-select">
                                                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>Пользователь</option>
                                                <option value="seller" <?= $user['role'] === 'seller' ? 'selected' : '' ?>>Продавец</option>
                                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Администратор</option>
                                            </select>
                                            
                                            <a href="/admin/user/<?= $user['id'] ?>" class="btn-action btn-secondary">Подробнее</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Пагинация -->
                    <?php if ($totalPages > 1): ?>
                        <div class="pagination">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($role) ?>&status=<?= urlencode($status) ?>" class="page-link">← Предыдущая</a>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($role) ?>&status=<?= urlencode($status) ?>" 
                                   class="page-link <?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&role=<?= urlencode($role) ?>&status=<?= urlencode($status) ?>" class="page-link">Следующая →</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script>
        function banUser(userId) {
            if (!confirm('Вы уверены, что хотите заблокировать этого пользователя?')) {
                return;
            }
            
            fetch(`/admin/users/${userId}/ban`, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Ошибка при блокировке');
                }
            })
            .catch(error => {
                alert('Ошибка сети');
            });
        }

        function unbanUser(userId) {
            if (!confirm('Вы уверены, что хотите разблокировать этого пользователя?')) {
                return;
            }
            
            fetch(`/admin/users/${userId}/unban`, {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Ошибка при разблокировке');
                }
            })
            .catch(error => {
                alert('Ошибка сети');
            });
        }

        function changeRole(userId, role) {
            if (!confirm(`Вы уверены, что хотите изменить роль пользователя на "${role}"?`)) {
                return;
            }
            
            fetch(`/admin/users/${userId}/role`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `role=${role}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Ошибка при смене роли');
                }
            })
            .catch(error => {
                alert('Ошибка сети');
            });
        }
    </script>

    <style>
        .user-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            overflow: hidden;
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-name {
            font-weight: 500;
        }

        .role-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .role-user {
            background: #e5e7eb;
            color: #374151;
        }

        .role-seller {
            background: #dbeafe;
            color: #1e40af;
        }

        .role-admin {
            background: #fef3c7;
            color: #92400e;
        }

        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-active {
            background: #dcfce7;
            color: #166534;
        }

        .status-banned {
            background: #fee2e2;
            color: #dc2626;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .role-select {
            padding: 0.25rem 0.5rem;
            border: 1px solid var(--border-color);
            border-radius: 0.25rem;
            background: var(--input-bg);
            color: var(--text-primary);
            font-size: 0.8rem;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .page-link {
            padding: 0.5rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: 0.25rem;
            text-decoration: none;
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .page-link:hover {
            background: var(--hover-bg);
        }

        .page-link.active {
            background: var(--accent-color);
            color: white;
            border-color: var(--accent-color);
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .table-header h3 {
            margin: 0;
            color: var(--text-primary);
        }

        .table-actions {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
    </style>
</body>
</html>