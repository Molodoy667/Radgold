<?php $title = 'Редактирование товара'; ?>

<div class="container">
    <div class="page-header">
        <h1>Редактирование товара</h1>
        <a href="/product/<?= $product['id'] ?>" class="btn btn-secondary">← Вернуться к товару</a>
    </div>

    <form class="product-form" method="POST" action="/products/<?= $product['id'] ?>/update" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        
        <div class="form-sections">
            <div class="main-section">
                <div class="form-section">
                    <h2>Основная информация</h2>
                    
                    <div class="form-group">
                        <label for="title">Название товара *</label>
                        <input type="text" id="title" name="title" value="<?= htmlspecialchars($product['title']) ?>" required 
                               placeholder="Например: Аккаунт CS:GO Global Elite">
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Категория *</label>
                        <select id="category" name="category_id" required>
                            <option value="">Выберите категорию</option>
                            <?php foreach ($categories ?? [] as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= $category['id'] == $product['category_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Описание *</label>
                        <textarea id="description" name="description" rows="6" required 
                                  placeholder="Подробно опишите ваш товар..."><?= htmlspecialchars($product['description']) ?></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Цена и условия</h2>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="price">Цена (₽) *</label>
                            <input type="number" id="price" name="price" value="<?= $product['price'] ?>" 
                                   min="1" step="0.01" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="type">Тип товара</label>
                            <select id="type" name="type">
                                <option value="account" <?= $product['type'] === 'account' ? 'selected' : '' ?>>Игровой аккаунт</option>
                                <option value="boost" <?= $product['type'] === 'boost' ? 'selected' : '' ?>>Услуга бустинга</option>
                                <option value="item" <?= $product['type'] === 'item' ? 'selected' : '' ?>>Игровой предмет</option>
                                <option value="currency" <?= $product['type'] === 'currency' ? 'selected' : '' ?>>Игровая валюта</option>
                                <option value="service" <?= $product['type'] === 'service' ? 'selected' : '' ?>>Другая услуга</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Статус</label>
                        <select id="status" name="status">
                            <option value="active" <?= $product['status'] === 'active' ? 'selected' : '' ?>>Активен</option>
                            <option value="inactive" <?= $product['status'] === 'inactive' ? 'selected' : '' ?>>Неактивен</option>
                            <option value="sold" <?= $product['status'] === 'sold' ? 'selected' : '' ?>>Продан</option>
                        </select>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Изображения</h2>
                    
                    <div class="current-images">
                        <h3>Текущие изображения</h3>
                        <div class="images-grid" id="currentImages">
                            <?php if (!empty($product['images'])): ?>
                                <?php foreach ($product['images'] as $index => $image): ?>
                                    <div class="image-item" data-index="<?= $index ?>">
                                        <img src="<?= htmlspecialchars($image) ?>" alt="Изображение товара">
                                        <button type="button" class="remove-image" onclick="removeImage(<?= $index ?>)">
                                            <i class="icon-x"></i>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="no-images">Изображения не загружены</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="images">Добавить новые изображения</label>
                        <input type="file" id="images" name="images[]" multiple accept="image/*">
                        <small>Можно загрузить до 5 изображений. Форматы: JPG, PNG, GIF. Максимальный размер: 5MB</small>
                    </div>
                    
                    <div class="preview-container" id="imagePreview"></div>
                </div>

                <div class="form-section">
                    <h2>Дополнительная информация</h2>
                    
                    <div class="form-group">
                        <label for="game">Игра</label>
                        <input type="text" id="game" name="game" value="<?= htmlspecialchars($product['game'] ?? '') ?>" 
                               placeholder="Например: Counter-Strike: Global Offensive">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="server">Сервер/Регион</label>
                            <input type="text" id="server" name="server" value="<?= htmlspecialchars($product['server'] ?? '') ?>" 
                                   placeholder="Например: EU West, Россия">
                        </div>
                        
                        <div class="form-group">
                            <label for="level">Уровень/Ранг</label>
                            <input type="text" id="level" name="level" value="<?= htmlspecialchars($product['level'] ?? '') ?>" 
                                   placeholder="Например: Global Elite, 80 lvl">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="tags">Теги</label>
                        <input type="text" id="tags" name="tags" value="<?= htmlspecialchars($product['tags'] ?? '') ?>" 
                               placeholder="Разделяйте теги запятыми: cs:go, global, prime, много скинов">
                        <small>Теги помогают покупателям найти ваш товар</small>
                    </div>
                </div>
            </div>

            <div class="sidebar-section">
                <div class="form-section sticky">
                    <h3>Превью товара</h3>
                    <div class="product-preview" id="productPreview">
                        <div class="preview-image">
                            <?php if (!empty($product['images'])): ?>
                                <img src="<?= htmlspecialchars($product['images'][0]) ?>" alt="Превью" id="previewImage">
                            <?php else: ?>
                                <div class="no-preview-image">
                                    <i class="icon-image"></i>
                                    <span>Нет изображения</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="preview-content">
                            <h4 id="previewTitle"><?= htmlspecialchars($product['title']) ?></h4>
                            <p class="preview-price" id="previewPrice"><?= number_format($product['price'], 2) ?> ₽</p>
                            <p class="preview-description" id="previewDescription">
                                <?= htmlspecialchars(substr($product['description'], 0, 100)) ?>...
                            </p>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-large">
                            <i class="icon-save"></i> Сохранить изменения
                        </button>
                        <a href="/product/<?= $product['id'] ?>" class="btn btn-secondary btn-large">Отмена</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.product-form {
    max-width: 1200px;
    margin: 0 auto;
}

.form-sections {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 2rem;
}

.form-section {
    background: var(--card-bg);
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
}

.form-section h2,
.form-section h3 {
    margin: 0 0 1.5rem;
    color: var(--text-primary);
    border-bottom: 2px solid var(--border-color);
    padding-bottom: 0.5rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
    font-weight: 500;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    background: var(--bg-color);
    color: var(--text-primary);
    transition: border-color 0.3s ease;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: var(--accent-color);
}

.form-group small {
    display: block;
    margin-top: 0.25rem;
    color: var(--text-secondary);
    font-size: 0.85rem;
}

.current-images h3 {
    margin: 0 0 1rem;
    font-size: 1.1rem;
    border: none;
    padding: 0;
}

.images-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.image-item {
    position: relative;
    aspect-ratio: 1;
    border-radius: 8px;
    overflow: hidden;
}

.image-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.remove-image {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    width: 30px;
    height: 30px;
    background: rgba(239, 68, 68, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.3s ease;
}

.remove-image:hover {
    background: #dc2626;
}

.no-images {
    color: var(--text-secondary);
    font-style: italic;
    text-align: center;
    padding: 2rem;
    background: var(--bg-color);
    border-radius: 8px;
}

.preview-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.preview-item {
    aspect-ratio: 1;
    border-radius: 8px;
    overflow: hidden;
    border: 2px solid var(--border-color);
}

.preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.sidebar-section .form-section {
    position: sticky;
    top: 2rem;
}

.product-preview {
    border: 2px solid var(--border-color);
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 2rem;
}

.preview-image {
    height: 200px;
    background: var(--bg-color);
    display: flex;
    align-items: center;
    justify-content: center;
}

.preview-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-preview-image {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-secondary);
}

.no-preview-image i {
    font-size: 3rem;
}

.preview-content {
    padding: 1rem;
}

.preview-content h4 {
    margin: 0 0 0.5rem;
    color: var(--text-primary);
}

.preview-price {
    margin: 0 0 0.5rem;
    font-size: 1.2rem;
    font-weight: bold;
    color: var(--accent-color);
}

.preview-description {
    margin: 0;
    color: var(--text-secondary);
    font-size: 0.9rem;
    line-height: 1.4;
}

.form-actions {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.btn-large {
    padding: 1rem;
    font-size: 1.1rem;
    text-align: center;
}

@media (max-width: 768px) {
    .form-sections {
        grid-template-columns: 1fr;
    }
    
    .sidebar-section .form-section {
        position: static;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .images-grid {
        grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.getElementById('title');
    const priceInput = document.getElementById('price');
    const descriptionInput = document.getElementById('description');
    const imagesInput = document.getElementById('images');
    
    const previewTitle = document.getElementById('previewTitle');
    const previewPrice = document.getElementById('previewPrice');
    const previewDescription = document.getElementById('previewDescription');
    const previewImage = document.getElementById('previewImage');
    
    // Обновление превью при изменении полей
    titleInput.addEventListener('input', function() {
        previewTitle.textContent = this.value || 'Название товара';
    });
    
    priceInput.addEventListener('input', function() {
        const price = parseFloat(this.value) || 0;
        previewPrice.textContent = price.toLocaleString('ru-RU', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + ' ₽';
    });
    
    descriptionInput.addEventListener('input', function() {
        const text = this.value || 'Описание товара';
        previewDescription.textContent = text.length > 100 ? text.substring(0, 100) + '...' : text;
    });
    
    // Предпросмотр новых изображений
    imagesInput.addEventListener('change', function() {
        const files = Array.from(this.files);
        const previewContainer = document.getElementById('imagePreview');
        previewContainer.innerHTML = '';
        
        files.forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewItem = document.createElement('div');
                    previewItem.className = 'preview-item';
                    previewItem.innerHTML = `<img src="${e.target.result}" alt="Превью ${index + 1}">`;
                    previewContainer.appendChild(previewItem);
                    
                    // Обновляем главное изображение в превью
                    if (index === 0 && previewImage) {
                        previewImage.src = e.target.result;
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    });
});

function removeImage(index) {
    if (confirm('Удалить это изображение?')) {
        const imageItem = document.querySelector(`[data-index="${index}"]`);
        if (imageItem) {
            imageItem.remove();
            
            // Отправляем запрос на удаление изображения
            fetch(`/products/<?= $product['id'] ?>/remove-image`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': '<?= csrf_token() ?>'
                },
                body: JSON.stringify({ image_index: index })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    alert('Ошибка при удалении изображения');
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ошибка при удалении изображения');
            });
        }
    }
}
</script>