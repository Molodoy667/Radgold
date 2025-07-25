<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="windows-1251">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить товар - Game Marketplace</title>
    <link rel="stylesheet" href="/assets/css/theme.css">
    <link rel="stylesheet" href="/assets/css/products.css">
    <link rel="stylesheet" href="/assets/css/forms.css">
    <script src="/assets/js/theme.js"></script>
</head>
<body>
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <h1>🎮 Game Marketplace</h1>
            </div>
            <nav class="nav">
                <a href="/products">Каталог</a>
                <a href="/profile">Личный кабинет</a>
                <a href="/chat">Сообщения</a>
                <a href="/logout">Выйти</a>
            </nav>
            <button onclick="toggleTheme()" class="btn-theme">🌙</button>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="page-header">
                <h1>Добавить новый товар</h1>
                <p>Заполните форму для создания нового объявления</p>
            </div>

            <div class="form-container">
                <form id="createProductForm" class="product-form">
                    <div class="form-group">
                        <label for="type">Тип товара *</label>
                        <select name="type" id="type" required>
                            <option value="">Выберите тип</option>
                            <option value="account">Аккаунт</option>
                            <option value="service">Услуга</option>
                            <option value="rent">Аренда</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="game">Игра *</label>
                        <select name="game" id="game" required>
                            <option value="">Выберите игру</option>
                            <?php foreach ($games as $game): ?>
                                <option value="<?= htmlspecialchars($game) ?>"><?= htmlspecialchars($game) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="custom_game" id="custom_game" placeholder="Или введите название игры" style="display:none;">
                    </div>

                    <div class="form-group">
                        <label for="title">Название товара *</label>
                        <input type="text" name="title" id="title" placeholder="Краткое описание товара" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Подробное описание *</label>
                        <textarea name="description" id="description" rows="6" placeholder="Опишите товар подробно..." required></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="price">Цена *</label>
                            <input type="number" name="price" id="price" placeholder="0" min="0" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="currency">Валюта</label>
                            <select name="currency" id="currency">
                                <option value="RUB">Рубли (₽)</option>
                                <option value="USD">Доллары ($)</option>
                                <option value="EUR">Евро (€)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="images">Изображения</label>
                        <input type="file" name="images[]" id="images" multiple accept="image/*">
                        <small>Можно загрузить до 10 изображений (JPG, PNG)</small>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Создать товар</button>
                        <a href="/my-products" class="btn-secondary">Отмена</a>
                    </div>
                </form>

                <div id="formError" class="error-message" style="display:none;"></div>
                <div id="formSuccess" class="success-message" style="display:none;"></div>
            </div>
        </div>
    </main>

    <script>
        // Переключение между списком игр и кастомным вводом
        document.getElementById('game').addEventListener('change', function() {
            const customGame = document.getElementById('custom_game');
            if (this.value === 'custom') {
                this.style.display = 'none';
                customGame.style.display = 'block';
                customGame.required = true;
            }
        });

        document.getElementById('custom_game').addEventListener('input', function() {
            if (this.value) {
                document.getElementById('game').value = this.value;
            }
        });

        // Обработка формы
        document.getElementById('createProductForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            // Валидация
            const price = parseFloat(formData.get('price'));
            if (price <= 0) {
                showError('Цена должна быть больше 0');
                return;
            }
            
            // Отправка формы
            fetch('/products/create', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess('Товар успешно создан! Перенаправление...');
                    setTimeout(() => {
                        window.location.href = '/my-products';
                    }, 2000);
                } else {
                    showError(data.error || 'Ошибка при создании товара');
                }
            })
            .catch(error => {
                showError('Ошибка сети');
            });
        });
        
        function showError(message) {
            const errorDiv = document.getElementById('formError');
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            document.getElementById('formSuccess').style.display = 'none';
        }
        
        function showSuccess(message) {
            const successDiv = document.getElementById('formSuccess');
            successDiv.textContent = message;
            successDiv.style.display = 'block';
            document.getElementById('formError').style.display = 'none';
        }
    </script>
</body>
</html>