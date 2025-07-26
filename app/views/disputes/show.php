<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Навигация -->
        <div class="mb-6">
            <a href="/disputes" class="text-primary hover:underline">
                ← Вернуться к спорам
            </a>
        </div>

        <!-- Заголовок спора -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-bold">Спор #<?= $dispute['id'] ?? '' ?></h1>
                <span class="status-badge status-<?= $dispute['status'] ?? 'pending' ?>">
                    <?= ucfirst($dispute['status'] ?? 'В обработке') ?>
                </span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="text-gray-600 dark:text-gray-400">Создан:</span>
                    <span class="font-medium"><?= date('d.m.Y H:i', strtotime($dispute['created_at'] ?? 'now')) ?></span>
                </div>
                <div>
                    <span class="text-gray-600 dark:text-gray-400">Причина:</span>
                    <span class="font-medium"><?= htmlspecialchars($dispute['reason'] ?? '') ?></span>
                </div>
                <div>
                    <span class="text-gray-600 dark:text-gray-400">Сумма спора:</span>
                    <span class="font-medium"><?= number_format($dispute['amount'] ?? 0, 2) ?> ₽</span>
                </div>
            </div>
        </div>

        <!-- Информация о покупке -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Информация о покупке</h3>
            <div class="flex items-center space-x-4">
                <?php if (!empty($dispute['purchase']['product']['image'])): ?>
                    <img src="<?= $dispute['purchase']['product']['image'] ?>" 
                         alt="<?= htmlspecialchars($dispute['purchase']['product']['title']) ?>" 
                         class="w-20 h-20 object-cover rounded">
                <?php endif; ?>
                <div>
                    <h4 class="font-medium text-lg"><?= htmlspecialchars($dispute['purchase']['product']['title'] ?? 'Товар') ?></h4>
                    <p class="text-gray-600 dark:text-gray-400">
                        Покупка #<?= $dispute['purchase']['id'] ?? '' ?> от <?= date('d.m.Y', strtotime($dispute['purchase']['created_at'] ?? 'now')) ?>
                    </p>
                    <p class="text-gray-600 dark:text-gray-400">
                        Продавец: <?= htmlspecialchars($dispute['purchase']['seller']['username'] ?? 'Неизвестен') ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Описание проблемы -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Описание проблемы</h3>
            <div class="prose dark:prose-invert max-w-none">
                <?= nl2br(htmlspecialchars($dispute['description'] ?? '')) ?>
            </div>
            
            <?php if (!empty($dispute['evidence'])): ?>
                <div class="mt-4">
                    <h4 class="font-medium mb-2">Приложенные доказательства:</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <?php foreach ($dispute['evidence'] as $evidence): ?>
                            <div class="border rounded-lg p-2">
                                <?php if (str_contains($evidence['type'], 'image')): ?>
                                    <img src="<?= $evidence['url'] ?>" alt="Доказательство" class="w-full h-20 object-cover rounded">
                                <?php else: ?>
                                    <div class="w-full h-20 bg-gray-100 dark:bg-gray-700 rounded flex items-center justify-center">
                                        <i class="icon-file text-2xl text-gray-400"></i>
                                    </div>
                                <?php endif; ?>
                                <p class="text-xs text-center mt-1 truncate"><?= $evidence['name'] ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- История сообщений -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">История обсуждения</h3>
            
            <div class="space-y-4">
                <?php if (!empty($dispute['messages'])): ?>
                    <?php foreach ($dispute['messages'] as $message): ?>
                        <div class="flex space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-gray-300 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                    <?= strtoupper(substr($message['sender']['username'] ?? 'U', 0, 1)) ?>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-1">
                                    <span class="font-medium"><?= htmlspecialchars($message['sender']['username'] ?? 'Пользователь') ?></span>
                                    <span class="text-xs text-gray-500"><?= date('d.m.Y H:i', strtotime($message['created_at'] ?? 'now')) ?></span>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                    <?= nl2br(htmlspecialchars($message['content'] ?? '')) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-600 dark:text-gray-400 text-center py-8">
                        Пока нет сообщений в этом споре
                    </p>
                <?php endif; ?>
            </div>

            <!-- Форма добавления сообщения -->
            <?php if (($dispute['status'] ?? '') === 'open'): ?>
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <form action="/disputes/<?= $dispute['id'] ?>/message" method="POST" class="space-y-4">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
                        <div>
                            <textarea name="message" rows="3" placeholder="Добавить сообщение к спору..." 
                                      class="input w-full" required></textarea>
                        </div>
                        <button type="submit" class="btn-primary">
                            Отправить сообщение
                        </button>
                    </form>
                </div>
            <?php endif; ?>
        </div>

        <!-- Действия -->
        <?php if (($dispute['status'] ?? '') === 'open' && ($user['role'] ?? '') === 'admin'): ?>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-4">Действия модератора</h3>
                <div class="flex space-x-4">
                    <form action="/disputes/<?= $dispute['id'] ?>/resolve" method="POST" class="inline">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
                        <input type="hidden" name="resolution" value="buyer">
                        <button type="submit" class="btn-success">
                            Решить в пользу покупателя
                        </button>
                    </form>
                    <form action="/disputes/<?= $dispute['id'] ?>/resolve" method="POST" class="inline">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
                        <input type="hidden" name="resolution" value="seller">
                        <button type="submit" class="btn-secondary">
                            Решить в пользу продавца
                        </button>
                    </form>
                    <form action="/disputes/<?= $dispute['id'] ?>/close" method="POST" class="inline">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
                        <button type="submit" class="btn-danger">
                            Закрыть спор
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>