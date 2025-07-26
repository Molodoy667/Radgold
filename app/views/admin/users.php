<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold">Управление пользователями</h1>
        <a href="/admin" class="btn-secondary">
            ← Назад в админ-панель
        </a>
    </div>

    <!-- Фильтры и поиск -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
        <form action="/admin/users" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" name="search" placeholder="Поиск по имени, email..." 
                       value="<?= htmlspecialchars($search ?? '') ?>" class="input w-full">
            </div>
            <div>
                <select name="role" class="input w-full">
                    <option value="">Все роли</option>
                    <option value="user" <?= ($role ?? '') === 'user' ? 'selected' : '' ?>>Пользователь</option>
                    <option value="admin" <?= ($role ?? '') === 'admin' ? 'selected' : '' ?>>Администратор</option>
                </select>
            </div>
            <div>
                <select name="status" class="input w-full">
                    <option value="">Все статусы</option>
                    <option value="active" <?= ($status ?? '') === 'active' ? 'selected' : '' ?>>Активные</option>
                    <option value="banned" <?= ($status ?? '') === 'banned' ? 'selected' : '' ?>>Заблокированные</option>
                </select>
            </div>
            <div>
                <button type="submit" class="btn-primary w-full">Поиск</button>
            </div>
        </form>
    </div>

    <!-- Статистика -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-500 bg-opacity-10 rounded-lg">
                    <i class="icon-users text-blue-500"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold"><?= number_format($stats['total'] ?? 0) ?></h3>
                    <p class="text-gray-600 dark:text-gray-400">Всего пользователей</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-500 bg-opacity-10 rounded-lg">
                    <i class="icon-user-check text-green-500"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold"><?= number_format($stats['active'] ?? 0) ?></h3>
                    <p class="text-gray-600 dark:text-gray-400">Активных</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-500 bg-opacity-10 rounded-lg">
                    <i class="icon-user-x text-red-500"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold"><?= number_format($stats['banned'] ?? 0) ?></h3>
                    <p class="text-gray-600 dark:text-gray-400">Заблокированных</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-500 bg-opacity-10 rounded-lg">
                    <i class="icon-calendar text-purple-500"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold"><?= number_format($stats['new_today'] ?? 0) ?></h3>
                    <p class="text-gray-600 dark:text-gray-400">Новых сегодня</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Таблица пользователей -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Пользователь
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Роль
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Статус
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Баланс
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Регистрация
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Действия
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user_item): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <?php if (!empty($user_item['avatar'])): ?>
                                                <img class="h-10 w-10 rounded-full" src="<?= $user_item['avatar'] ?>" alt="">
                                            <?php else: ?>
                                                <div class="h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-600 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                        <?= strtoupper(substr($user_item['username'], 0, 1)) ?>
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                <?= htmlspecialchars($user_item['username']) ?>
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                <?= htmlspecialchars($user_item['email']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        <?= $user_item['role'] === 'admin' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' ?>">
                                        <?= ucfirst($user_item['role']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="status-badge status-<?= $user_item['status'] ?>">
                                        <?= ucfirst($user_item['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <?= number_format($user_item['balance'], 2) ?> ₽
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <?= date('d.m.Y', strtotime($user_item['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="/user/<?= $user_item['id'] ?>" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">
                                            Просмотр
                                        </a>
                                        <?php if ($user_item['status'] === 'active'): ?>
                                            <button onclick="banUser(<?= $user_item['id'] ?>)" 
                                                    class="text-red-600 hover:text-red-900 dark:text-red-400">
                                                Заблокировать
                                            </button>
                                        <?php else: ?>
                                            <button onclick="unbanUser(<?= $user_item['id'] ?>)" 
                                                    class="text-green-600 hover:text-green-900 dark:text-green-400">
                                                Разблокировать
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                Пользователи не найдены
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Пагинация -->
        <?php if (($pagination['total_pages'] ?? 1) > 1): ?>
            <div class="bg-white dark:bg-gray-800 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                <div class="flex items-center justify-between">
                    <div class="flex-1 flex justify-between sm:hidden">
                        <?php if ($pagination['current_page'] > 1): ?>
                            <a href="?page=<?= $pagination['current_page'] - 1 ?>" 
                               class="btn-secondary">Предыдущая</a>
                        <?php endif; ?>
                        <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                            <a href="?page=<?= $pagination['current_page'] + 1 ?>" 
                               class="btn-secondary">Следующая</a>
                        <?php endif; ?>
                    </div>
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Показано <span class="font-medium"><?= ($pagination['current_page'] - 1) * $pagination['per_page'] + 1 ?></span>
                                - <span class="font-medium"><?= min($pagination['current_page'] * $pagination['per_page'], $pagination['total_users']) ?></span>
                                из <span class="font-medium"><?= $pagination['total_users'] ?></span> пользователей
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                <a href="?page=<?= $i ?>" 
                                   class="<?= $i === $pagination['current_page'] ? 'btn-primary' : 'btn-secondary' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function banUser(userId) {
    if (confirm('Вы уверены, что хотите заблокировать этого пользователя?')) {
        // AJAX запрос на бан пользователя
        App.notification.show('Функция будет реализована в следующих обновлениях', 'info');
    }
}

function unbanUser(userId) {
    if (confirm('Вы уверены, что хотите разблокировать этого пользователя?')) {
        // AJAX запрос на разбан пользователя
        App.notification.show('Функция будет реализована в следующих обновлениях', 'info');
    }
}
</script>