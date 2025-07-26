<?php
ob_start();
?>

<div class="min-h-screen bg-background">
    <!-- Хлебные крошки -->
    <div class="bg-card border-b border-border">
        <div class="container mx-auto px-4 py-4">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="/" class="text-muted-foreground hover:text-primary transition-colors">Главная</a>
                <span class="text-muted-foreground">•</span>
                <a href="/my-products" class="text-muted-foreground hover:text-primary transition-colors">Мои товары</a>
                <span class="text-muted-foreground">•</span>
                <span class="text-foreground font-medium">Добавить товар</span>
            </nav>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold mb-2">Добавить товар</h1>
                <p class="text-muted-foreground">Заполните информацию о вашем товаре или услуге</p>
            </div>

            <form id="create-product-form" class="space-y-8" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">

                <!-- Основная информация -->
                <div class="card p-6">
                    <h3 class="text-xl font-semibold mb-6 flex items-center">
                        <i class="icon-info mr-2"></i>
                        Основная информация
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Тип товара -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium">
                                Тип товара <span class="text-red-500">*</span>
                            </label>
                            <select name="type" class="input-field" required>
                                <option value="">Выберите тип</option>
                                <?php foreach ($productTypes as $key => $label): ?>
                                    <option value="<?= htmlspecialchars($key) ?>">
                                        <?= htmlspecialchars($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="error-message" data-field="type"></div>
                        </div>

                        <!-- Игра -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium">
                                Игра <span class="text-red-500">*</span>
                            </label>
                            <select name="game" class="input-field" required>
                                <option value="">Выберите игру</option>
                                <?php foreach ($games as $key => $label): ?>
                                    <option value="<?= htmlspecialchars($key) ?>">
                                        <?= htmlspecialchars($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="error-message" data-field="game"></div>
                        </div>
                    </div>

                    <!-- Название -->
                    <div class="space-y-2 mt-6">
                        <label class="block text-sm font-medium">
                            Название товара <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="title" 
                            class="input-field" 
                            placeholder="Введите название товара"
                            maxlength="255"
                            required
                        >
                        <div class="text-xs text-muted-foreground">Минимум 10 символов</div>
                        <div class="error-message" data-field="title"></div>
                    </div>

                    <!-- Краткое описание -->
                    <div class="space-y-2 mt-6">
                        <label class="block text-sm font-medium">
                            Краткое описание
                        </label>
                        <textarea 
                            name="short_description" 
                            class="input-field resize-none" 
                            rows="3"
                            placeholder="Краткое описание товара (будет использовано в каталоге)"
                            maxlength="200"
                        ></textarea>
                        <div class="text-xs text-muted-foreground">Максимум 200 символов (необязательно)</div>
                    </div>

                    <!-- Полное описание -->
                    <div class="space-y-2 mt-6">
                        <label class="block text-sm font-medium">
                            Полное описание <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            name="description" 
                            class="input-field resize-none" 
                            rows="6"
                            placeholder="Подробное описание товара, характеристики, что входит в комплект..."
                            required
                        ></textarea>
                        <div class="text-xs text-muted-foreground">Минимум 50 символов</div>
                        <div class="error-message" data-field="description"></div>
                    </div>
                </div>

                <!-- Цена и валюта -->
                <div class="card p-6">
                    <h3 class="text-xl font-semibold mb-6 flex items-center">
                        <i class="icon-dollar-sign mr-2"></i>
                        Стоимость
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Цена -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium">
                                Цена <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                name="price" 
                                class="input-field" 
                                placeholder="0"
                                min="1"
                                max="1000000"
                                step="1"
                                required
                            >
                            <div class="error-message" data-field="price"></div>
                        </div>

                        <!-- Валюта -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium">Валюта</label>
                            <select name="currency" class="input-field">
                                <?php foreach ($currencies as $code => $currency): ?>
                                    <option value="<?= htmlspecialchars($code) ?>" <?= $code === 'RUB' ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($currency['symbol'] . ' ' . $currency['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Старая цена (для скидки) -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium">Старая цена (необязательно)</label>
                            <input 
                                type="number" 
                                name="original_price" 
                                class="input-field" 
                                placeholder="Для показа скидки"
                                min="1"
                                max="1000000"
                                step="1"
                            >
                        </div>
                    </div>
                </div>

                <!-- Изображения -->
                <div class="card p-6">
                    <h3 class="text-xl font-semibold mb-6 flex items-center">
                        <i class="icon-image mr-2"></i>
                        Изображения
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium">
                                Загрузить изображения
                            </label>
                            <div 
                                id="image-drop-zone" 
                                class="border-2 border-dashed border-border rounded-lg p-8 text-center hover:border-primary transition-colors cursor-pointer"
                            >
                                <div class="space-y-4">
                                    <div class="text-4xl">📷</div>
                                    <div>
                                        <p class="text-lg font-medium">Перетащите изображения сюда</p>
                                        <p class="text-sm text-muted-foreground">или нажмите для выбора файлов</p>
                                    </div>
                                    <div class="text-xs text-muted-foreground">
                                        Поддерживаются: JPG, PNG, WebP. Максимум 5 файлов по 5MB
                                    </div>
                                </div>
                                <input 
                                    type="file" 
                                    name="images[]" 
                                    id="images-input"
                                    class="hidden" 
                                    multiple 
                                    accept="image/*"
                                >
                            </div>
                        </div>

                        <!-- Превью изображений -->
                        <div id="images-preview" class="grid grid-cols-2 md:grid-cols-4 gap-4 hidden"></div>
                    </div>
                </div>

                <!-- Характеристики -->
                <div class="card p-6">
                    <h3 class="text-xl font-semibold mb-6 flex items-center">
                        <i class="icon-list mr-2"></i>
                        Характеристики
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="text-sm text-muted-foreground mb-4">
                            Добавьте дополнительные характеристики товара (уровень, ранг, предметы и т.д.)
                        </div>
                        
                        <div id="specifications-container">
                            <div class="specification-row flex gap-4 mb-4">
                                <input 
                                    type="text" 
                                    name="spec_keys[]" 
                                    class="input-field flex-1" 
                                    placeholder="Характеристика (например: Уровень)"
                                >
                                <input 
                                    type="text" 
                                    name="spec_values[]" 
                                    class="input-field flex-1" 
                                    placeholder="Значение (например: 50)"
                                >
                                <button type="button" class="btn-outline remove-spec hidden">
                                    <i class="icon-trash"></i>
                                </button>
                            </div>
                        </div>
                        
                        <button type="button" id="add-specification" class="btn-secondary">
                            <i class="icon-plus mr-2"></i>
                            Добавить характеристику
                        </button>
                    </div>
                </div>

                <!-- Доставка -->
                <div class="card p-6">
                    <h3 class="text-xl font-semibold mb-6 flex items-center">
                        <i class="icon-truck mr-2"></i>
                        Доставка и выполнение
                    </h3>
                    
                    <div class="space-y-6">
                        <!-- Опции доставки -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div class="flex items-center space-x-3">
                                    <input 
                                        type="checkbox" 
                                        name="instant_delivery" 
                                        id="instant_delivery"
                                        class="checkbox"
                                    >
                                    <label for="instant_delivery" class="text-sm font-medium">
                                        Мгновенная доставка
                                    </label>
                                </div>
                                
                                <div class="flex items-center space-x-3">
                                    <input 
                                        type="checkbox" 
                                        name="auto_delivery" 
                                        id="auto_delivery"
                                        class="checkbox"
                                    >
                                    <label for="auto_delivery" class="text-sm font-medium">
                                        Автоматическая доставка
                                    </label>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <label class="block text-sm font-medium">Время доставки</label>
                                <input 
                                    type="text" 
                                    name="delivery_time" 
                                    class="input-field" 
                                    placeholder="например: до 24 часов"
                                >
                            </div>
                        </div>

                        <!-- Информация о доставке -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium">Информация о доставке</label>
                            <textarea 
                                name="delivery_info" 
                                class="input-field resize-none" 
                                rows="3"
                                placeholder="Дополнительная информация о процессе доставки..."
                            ></textarea>
                        </div>

                        <!-- Гарантии -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium">Гарантия (дни)</label>
                            <input 
                                type="number" 
                                name="warranty_days" 
                                class="input-field" 
                                placeholder="0"
                                min="0"
                                max="365"
                            >
                            <div class="text-xs text-muted-foreground">0 = без гарантии</div>
                        </div>

                        <!-- Количество в наличии -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium">Количество в наличии</label>
                            <input 
                                type="number" 
                                name="stock_quantity" 
                                class="input-field" 
                                placeholder="1"
                                min="1"
                                max="999"
                                value="1"
                            >
                        </div>
                    </div>
                </div>

                <!-- Теги -->
                <div class="card p-6">
                    <h3 class="text-xl font-semibold mb-6 flex items-center">
                        <i class="icon-tag mr-2"></i>
                        Теги
                    </h3>
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-medium">Теги для поиска</label>
                        <input 
                            type="text" 
                            name="tags" 
                            class="input-field" 
                            placeholder="Введите теги через запятую: топ, дешево, быстро"
                        >
                        <div class="text-xs text-muted-foreground">
                            Теги помогут покупателям найти ваш товар
                        </div>
                    </div>
                </div>

                <!-- Кнопки действий -->
                <div class="card p-6">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button type="submit" class="btn-primary flex-1" id="submit-btn">
                            <i class="icon-plus mr-2"></i>
                            Добавить товар
                        </button>
                        
                        <button type="button" class="btn-secondary" id="save-draft-btn">
                            <i class="icon-save mr-2"></i>
                            Сохранить черновик
                        </button>
                        
                        <a href="/my-products" class="btn-outline flex-1 text-center">
                            <i class="icon-arrow-left mr-2"></i>
                            Отмена
                        </a>
                    </div>
                    
                    <div class="mt-4 text-xs text-muted-foreground text-center">
                        Товар будет отправлен на модерацию перед публикацией
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('create-product-form');
    const imageDropZone = document.getElementById('image-drop-zone');
    const imagesInput = document.getElementById('images-input');
    const imagesPreview = document.getElementById('images-preview');
    const specificationsContainer = document.getElementById('specifications-container');
    const addSpecButton = document.getElementById('add-specification');
    
    let selectedImages = [];
    
    // Обработка загрузки изображений
    imageDropZone.addEventListener('click', () => imagesInput.click());
    
    imageDropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        imageDropZone.classList.add('border-primary');
    });
    
    imageDropZone.addEventListener('dragleave', () => {
        imageDropZone.classList.remove('border-primary');
    });
    
    imageDropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        imageDropZone.classList.remove('border-primary');
        handleFiles(e.dataTransfer.files);
    });
    
    imagesInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });
    
    function handleFiles(files) {
        const maxFiles = 5;
        const maxSize = 5 * 1024 * 1024; // 5MB
        
        if (selectedImages.length + files.length > maxFiles) {
            App.notification.show(`Максимум ${maxFiles} изображений`, 'error');
            return;
        }
        
        Array.from(files).forEach(file => {
            if (!file.type.startsWith('image/')) {
                App.notification.show('Можно загружать только изображения', 'error');
                return;
            }
            
            if (file.size > maxSize) {
                App.notification.show('Размер файла не должен превышать 5MB', 'error');
                return;
            }
            
            selectedImages.push(file);
            displayImagePreview(file);
        });
    }
    
    function displayImagePreview(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const imageDiv = document.createElement('div');
            imageDiv.className = 'relative group';
            
            imageDiv.innerHTML = `
                <img 
                    src="${e.target.result}" 
                    class="w-full h-24 object-cover rounded-lg"
                    alt="Preview"
                >
                <button 
                    type="button" 
                    class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 opacity-0 group-hover:opacity-100 transition-opacity remove-image"
                    data-index="${selectedImages.length - 1}"
                >
                    ×
                </button>
            `;
            
            imagesPreview.appendChild(imageDiv);
            imagesPreview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
    
    // Удаление изображений
    imagesPreview.addEventListener('click', (e) => {
        if (e.target.classList.contains('remove-image')) {
            const index = parseInt(e.target.dataset.index);
            selectedImages.splice(index, 1);
            e.target.closest('.relative').remove();
            
            if (selectedImages.length === 0) {
                imagesPreview.classList.add('hidden');
            }
            
            // Обновляем индексы
            document.querySelectorAll('.remove-image').forEach((btn, idx) => {
                btn.dataset.index = idx;
            });
        }
    });
    
    // Добавление характеристик
    addSpecButton.addEventListener('click', () => {
        const specRow = document.createElement('div');
        specRow.className = 'specification-row flex gap-4 mb-4';
        specRow.innerHTML = `
            <input 
                type="text" 
                name="spec_keys[]" 
                class="input-field flex-1" 
                placeholder="Характеристика"
            >
            <input 
                type="text" 
                name="spec_values[]" 
                class="input-field flex-1" 
                placeholder="Значение"
            >
            <button type="button" class="btn-outline remove-spec">
                <i class="icon-trash"></i>
            </button>
        `;
        
        specificationsContainer.appendChild(specRow);
        updateRemoveButtons();
    });
    
    // Удаление характеристик
    specificationsContainer.addEventListener('click', (e) => {
        if (e.target.closest('.remove-spec')) {
            e.target.closest('.specification-row').remove();
            updateRemoveButtons();
        }
    });
    
    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.specification-row');
        rows.forEach((row, index) => {
            const removeBtn = row.querySelector('.remove-spec');
            if (index === 0 && rows.length === 1) {
                removeBtn.classList.add('hidden');
            } else {
                removeBtn.classList.remove('hidden');
            }
        });
    }
    
    // Отправка формы
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(form);
        
        // Добавляем изображения
        selectedImages.forEach(file => {
            formData.append('images[]', file);
        });
        
        const submitBtn = document.getElementById('submit-btn');
        const originalText = submitBtn.innerHTML;
        
        try {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="icon-loader mr-2 animate-spin"></i>Добавление...';
            
            const response = await fetch('/products/create', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                App.notification.show(data.message, 'success');
                if (data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1500);
                }
            } else {
                if (data.errors) {
                    displayErrors(data.errors);
                } else {
                    App.notification.show(data.message, 'error');
                }
            }
            
        } catch (error) {
            console.error('Error creating product:', error);
            App.notification.show('Ошибка при создании товара', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
    
    function displayErrors(errors) {
        // Очищаем предыдущие ошибки
        document.querySelectorAll('.error-message').forEach(el => {
            el.textContent = '';
            el.classList.add('hidden');
        });
        
        // Показываем новые ошибки
        Object.entries(errors).forEach(([field, message]) => {
            const errorEl = document.querySelector(`[data-field="${field}"]`);
            if (errorEl) {
                errorEl.textContent = message;
                errorEl.classList.remove('hidden');
            }
        });
        
        // Прокручиваем к первой ошибке
        const firstError = document.querySelector('.error-message:not(.hidden)');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
    
    // Автосохранение черновика (можно реализовать позже)
    document.getElementById('save-draft-btn').addEventListener('click', () => {
        App.notification.show('Функция черновиков будет добавлена позже', 'info');
    });
});
</script>

<style>
.error-message {
    @apply text-red-500 text-xs mt-1 hidden;
}

.error-message:not(.hidden) {
    @apply block;
}

.checkbox {
    @apply w-4 h-4 text-primary border-border rounded focus:ring-primary;
}

.specification-row .remove-spec {
    @apply w-10 h-10 flex items-center justify-center;
}

.input-field:focus {
    @apply ring-2 ring-primary border-primary;
}

#image-drop-zone.border-primary {
    @apply border-primary bg-primary/5;
}

.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>

<?php
$content = ob_get_clean();
require_once __DIR__ . '/../layouts/main.php';
?>