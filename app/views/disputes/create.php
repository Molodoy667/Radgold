<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-6">Создать спор</h2>
            
            <div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                <div class="flex items-start">
                    <i class="icon-alert-circle text-yellow-600 mr-3 mt-1"></i>
                    <div>
                        <h3 class="font-medium text-yellow-800 dark:text-yellow-200">Важная информация</h3>
                        <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                            Создавайте спор только в случае серьезных проблем с покупкой. 
                            Сначала попробуйте решить вопрос напрямую с продавцом.
                        </p>
                    </div>
                </div>
            </div>

            <form action="/disputes/create" method="POST" class="space-y-6">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
                <input type="hidden" name="purchase_id" value="<?= $purchase['id'] ?? '' ?>">

                <div>
                    <label class="block text-sm font-medium mb-2">Покупка</label>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="flex items-center space-x-4">
                            <?php if (!empty($purchase['product']['image'])): ?>
                                <img src="<?= $purchase['product']['image'] ?>" alt="<?= htmlspecialchars($purchase['product']['title']) ?>" class="w-16 h-16 object-cover rounded">
                            <?php endif; ?>
                            <div>
                                <h4 class="font-medium"><?= htmlspecialchars($purchase['product']['title'] ?? 'Товар') ?></h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Покупка #<?= $purchase['id'] ?? '' ?> от <?= date('d.m.Y', strtotime($purchase['created_at'] ?? 'now')) ?>
                                </p>
                                <p class="text-sm font-medium"><?= number_format($purchase['amount'] ?? 0, 2) ?> ₽</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="reason" class="block text-sm font-medium mb-2">Причина спора *</label>
                    <select name="reason" id="reason" required class="input w-full">
                        <option value="">Выберите причину</option>
                        <option value="product_not_received">Товар не получен</option>
                        <option value="product_not_as_described">Товар не соответствует описанию</option>
                        <option value="product_defective">Товар неисправен</option>
                        <option value="account_banned">Аккаунт заблокирован</option>
                        <option value="seller_unresponsive">Продавец не отвечает</option>
                        <option value="other">Другое</option>
                    </select>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium mb-2">Подробное описание проблемы *</label>
                    <textarea name="description" id="description" rows="6" required 
                              placeholder="Опишите подробно, что произошло..." 
                              class="input w-full"></textarea>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Минимум 50 символов. Чем подробнее описание, тем быстрее мы сможем решить спор.
                    </p>
                </div>

                <div>
                    <label for="evidence" class="block text-sm font-medium mb-2">Доказательства</label>
                    <input type="file" name="evidence[]" id="evidence" multiple 
                           accept="image/*,.pdf,.doc,.docx" 
                           class="input w-full">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Прикрепите скриншоты, переписку или другие документы (до 5 файлов, максимум 10 МБ каждый)
                    </p>
                </div>

                <div class="flex space-x-4">
                    <button type="submit" class="btn-primary">
                        <i class="icon-alert-circle mr-2"></i>
                        Создать спор
                    </button>
                    <a href="/my-purchases" class="btn-secondary">
                        Отменить
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('description').addEventListener('input', function() {
    const minLength = 50;
    const currentLength = this.value.length;
    const submitBtn = document.querySelector('button[type="submit"]');
    
    if (currentLength < minLength) {
        this.classList.add('border-red-500');
        submitBtn.disabled = true;
    } else {
        this.classList.remove('border-red-500');
        submitBtn.disabled = false;
    }
});
</script>