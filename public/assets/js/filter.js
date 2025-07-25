// AJAX фильтрация товаров
function filterProducts(params) {
    const productsList = document.getElementById('productsList');
    const loadingDiv = document.createElement('div');
    
    // Показываем индикатор загрузки
    loadingDiv.innerHTML = `
        <div style="text-align: center; padding: 2rem; color: var(--text-secondary);">
            <div style="width: 40px; height: 40px; border: 3px solid var(--border); border-top: 3px solid var(--accent-primary); border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 1rem;"></div>
            Загрузка товаров...
        </div>
    `;
    
    if (productsList) {
        productsList.innerHTML = '';
        productsList.appendChild(loadingDiv);
    }
    
    // Создаем URL с параметрами
    const url = new URL('/products/filter', window.location.origin);
    Object.keys(params).forEach(key => {
        if (params[key]) {
            url.searchParams.append(key, params[key]);
        }
    });
    
    // Отправляем запрос
    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(products => {
        displayProducts(products);
    })
    .catch(error => {
        console.error('Ошибка фильтрации:', error);
        showFilterError('Произошла ошибка при загрузке товаров');
    });
}

// Отображение товаров
function displayProducts(products) {
    const productsList = document.getElementById('productsList');
    
    if (!productsList) return;
    
    if (products.length === 0) {
        productsList.innerHTML = `
            <div style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🔍</div>
                <h3>Товары не найдены</h3>
                <p>Попробуйте изменить параметры фильтрации</p>
            </div>
        `;
        return;
    }
    
    // Очищаем список
    productsList.innerHTML = '';
    
    // Добавляем товары с анимацией
    products.forEach((product, index) => {
        const productCard = createProductCard(product);
        productCard.style.animationDelay = `${index * 0.1}s`;
        productsList.appendChild(productCard);
    });
}

// Создание карточки товара
function createProductCard(product) {
    const card = document.createElement('div');
    card.className = 'product-card fade-in';
    
    const gameImage = product.game.toLowerCase().replace(/\s+/g, '-');
    
    card.innerHTML = `
        <div class="product-image">
            <img src="/assets/images/games/${gameImage}.jpg" 
                 alt="${escapeHtml(product.game)}" 
                 onerror="this.src='/assets/images/default-game.jpg'">
            <div class="product-type">${product.type.charAt(0).toUpperCase() + product.type.slice(1)}</div>
        </div>
        <div class="product-info">
            <h3>${escapeHtml(product.game)}</h3>
            <p>${escapeHtml(product.description.substring(0, 100))}...</p>
            <div class="product-meta">
                <span class="price">${formatPrice(product.price)} ₽</span>
                <span class="seller">${escapeHtml(product.seller_name)}</span>
            </div>
            <a href="/products/${product.id}" class="btn-view">Подробнее</a>
        </div>
    `;
    
    return card;
}

// Показать ошибку фильтрации
function showFilterError(message) {
    const productsList = document.getElementById('productsList');
    
    if (productsList) {
        productsList.innerHTML = `
            <div style="text-align: center; padding: 3rem; color: var(--error);">
                <div style="font-size: 3rem; margin-bottom: 1rem;">⚠️</div>
                <h3>Ошибка загрузки</h3>
                <p>${message}</p>
                <button onclick="location.reload()" class="btn-primary" style="margin-top: 1rem;">
                    Попробовать снова
                </button>
            </div>
        `;
    }
}

// Вспомогательные функции
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatPrice(price) {
    return new Intl.NumberFormat('ru-RU').format(price);
}

// CSS для анимации загрузки
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .fade-in {
        opacity: 0;
        animation: fadeIn 0.5s ease-out forwards;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(style);