/**
 * Основной JavaScript файл для Marketplace
 * Зависимости: jQuery 3.6+, Bootstrap 5+
 */

$(document).ready(function() {
    // Инициализация
    initializeApp();
    
    // Обработчики событий
    bindEvents();
});

/**
 * Инициализация приложения
 */
function initializeApp() {
    // Настройка AJAX
    setupAjax();
    
    // Инициализация всплывающих подсказок
    initTooltips();
    
    // Проверка поддержки localStorage
    checkLocalStorage();
    
    console.log('Marketplace initialized');
}

/**
 * Настройка AJAX
 */
function setupAjax() {
    // Получение CSRF токена
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    
    // Настройка глобальных параметров AJAX
    $.ajaxSetup({
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        beforeSend: function() {
            showLoading();
        },
        complete: function() {
            hideLoading();
        },
        error: function(xhr, status, error) {
            handleAjaxError(xhr, status, error);
        }
    });
}

/**
 * Привязка обработчиков событий
 */
function bindEvents() {
    // Добавление в корзину
    $(document).on('click', '.btn-add-to-cart', handleAddToCart);
    
    // Удаление из корзины
    $(document).on('click', '.btn-remove-from-cart', handleRemoveFromCart);
    
    // Изменение количества в корзине
    $(document).on('change', '.cart-quantity', handleQuantityChange);
    
    // Поиск товаров
    $(document).on('input', '#search-input', debounce(handleSearch, 300));
    
    // Фильтрация товаров
    $(document).on('change', '.filter-checkbox', handleFilter);
    
    // Переключение избранного
    $(document).on('click', '.btn-favorite', handleFavorite);
    
    // Закрытие уведомлений
    $(document).on('click', '.alert .btn-close', function() {
        $(this).closest('.alert').fadeOut();
    });
}

/**
 * Добавление товара в корзину
 */
function handleAddToCart(e) {
    e.preventDefault();
    
    const $button = $(this);
    const productId = $button.data('product-id');
    const quantity = $button.data('quantity') || 1;
    
    if (!productId) {
        showNotification('Ошибка: не указан ID товара', 'error');
        return;
    }
    
    $button.prop('disabled', true).html('<span class="spinner"></span> Добавление...');
    
    $.ajax({
        url: '/api/cart/add',
        method: 'POST',
        data: {
            product_id: productId,
            quantity: quantity
        },
        success: function(response) {
            if (response.success) {
                showNotification('Товар добавлен в корзину', 'success');
                updateCartCounter(response.cart_count);
                $button.html('<i class="fas fa-check"></i> Добавлено');
                
                setTimeout(function() {
                    $button.html('<i class="fas fa-shopping-cart"></i> В корзину').prop('disabled', false);
                }, 2000);
            } else {
                showNotification(response.message || 'Ошибка при добавлении товара', 'error');
                $button.html('<i class="fas fa-shopping-cart"></i> В корзину').prop('disabled', false);
            }
        },
        error: function() {
            showNotification('Ошибка при добавлении товара', 'error');
            $button.html('<i class="fas fa-shopping-cart"></i> В корзину').prop('disabled', false);
        }
    });
}

/**
 * Удаление товара из корзины
 */
function handleRemoveFromCart(e) {
    e.preventDefault();
    
    const $button = $(this);
    const cartItemId = $button.data('cart-item-id');
    
    if (!confirm('Удалить товар из корзины?')) {
        return;
    }
    
    $.ajax({
        url: '/api/cart/remove',
        method: 'POST',
        data: {
            cart_item_id: cartItemId
        },
        success: function(response) {
            if (response.success) {
                $button.closest('.cart-item').fadeOut(function() {
                    $(this).remove();
                });
                updateCartTotal(response.cart_total);
                updateCartCounter(response.cart_count);
                showNotification('Товар удален из корзины', 'success');
            } else {
                showNotification(response.message || 'Ошибка при удалении товара', 'error');
            }
        }
    });
}

/**
 * Изменение количества товара в корзине
 */
function handleQuantityChange(e) {
    const $input = $(this);
    const cartItemId = $input.data('cart-item-id');
    const quantity = parseInt($input.val());
    
    if (quantity < 1) {
        $input.val(1);
        return;
    }
    
    $.ajax({
        url: '/api/cart/update',
        method: 'POST',
        data: {
            cart_item_id: cartItemId,
            quantity: quantity
        },
        success: function(response) {
            if (response.success) {
                updateCartTotal(response.cart_total);
                updateCartCounter(response.cart_count);
            } else {
                showNotification(response.message || 'Ошибка при обновлении количества', 'error');
            }
        }
    });
}

/**
 * Поиск товаров
 */
function handleSearch(e) {
    const query = $(this).val().trim();
    
    if (query.length < 2) {
        return;
    }
    
    $.ajax({
        url: '/api/search',
        method: 'GET',
        data: {
            q: query
        },
        success: function(response) {
            if (response.success) {
                displaySearchResults(response.products);
            }
        }
    });
}

/**
 * Фильтрация товаров
 */
function handleFilter(e) {
    const filters = {};
    
    $('.filter-checkbox:checked').each(function() {
        const filterType = $(this).data('filter-type');
        const filterValue = $(this).val();
        
        if (!filters[filterType]) {
            filters[filterType] = [];
        }
        filters[filterType].push(filterValue);
    });
    
    $.ajax({
        url: '/api/filter',
        method: 'GET',
        data: {
            filters: filters
        },
        success: function(response) {
            if (response.success) {
                displayFilterResults(response.products);
            }
        }
    });
}

/**
 * Переключение избранного
 */
function handleFavorite(e) {
    e.preventDefault();
    
    const $button = $(this);
    const productId = $button.data('product-id');
    const isFavorite = $button.hasClass('active');
    
    $.ajax({
        url: '/api/favorites/toggle',
        method: 'POST',
        data: {
            product_id: productId
        },
        success: function(response) {
            if (response.success) {
                if (response.is_favorite) {
                    $button.addClass('active').html('<i class="fas fa-heart"></i>');
                    showNotification('Товар добавлен в избранное', 'success');
                } else {
                    $button.removeClass('active').html('<i class="far fa-heart"></i>');
                    showNotification('Товар удален из избранного', 'info');
                }
            }
        }
    });
}

/**
 * Показать уведомление
 */
function showNotification(message, type = 'info') {
    const alertClass = `alert-${type === 'error' ? 'danger' : type}`;
    const icon = getNotificationIcon(type);
    
    const notification = $(`
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="${icon}"></i> ${message}
            <button type="button" class="btn-close" aria-label="Close"></button>
        </div>
    `);
    
    $('.main-content').prepend(notification);
    
    setTimeout(function() {
        notification.fadeOut(function() {
            $(this).remove();
        });
    }, 5000);
}

/**
 * Получить иконку для уведомления
 */
function getNotificationIcon(type) {
    const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle'
    };
    
    return icons[type] || icons.info;
}

/**
 * Обновить счетчик корзины
 */
function updateCartCounter(count) {
    $('.cart-counter').text(count);
    
    if (count > 0) {
        $('.cart-counter').show();
    } else {
        $('.cart-counter').hide();
    }
}

/**
 * Обновить общую сумму корзины
 */
function updateCartTotal(total) {
    $('.cart-total-amount').text(formatPrice(total));
}

/**
 * Форматирование цены
 */
function formatPrice(price) {
    return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'RUB'
    }).format(price);
}

/**
 * Показать индикатор загрузки
 */
function showLoading() {
    if (!$('.loading-overlay').length) {
        $('body').append('<div class="loading-overlay"><div class="spinner"></div></div>');
    }
}

/**
 * Скрыть индикатор загрузки
 */
function hideLoading() {
    $('.loading-overlay').remove();
}

/**
 * Обработка ошибок AJAX
 */
function handleAjaxError(xhr, status, error) {
    let message = 'Произошла ошибка при выполнении запроса';
    
    if (xhr.responseJSON && xhr.responseJSON.message) {
        message = xhr.responseJSON.message;
    } else if (xhr.status === 404) {
        message = 'Запрашиваемый ресурс не найден';
    } else if (xhr.status === 500) {
        message = 'Внутренняя ошибка сервера';
    }
    
    showNotification(message, 'error');
}

/**
 * Инициализация всплывающих подсказок
 */
function initTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Проверка поддержки localStorage
 */
function checkLocalStorage() {
    try {
        localStorage.setItem('test', 'test');
        localStorage.removeItem('test');
        return true;
    } catch (e) {
        console.warn('localStorage not supported');
        return false;
    }
}

/**
 * Debounce функция
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Отображение результатов поиска
 */
function displaySearchResults(products) {
    // Реализация отображения результатов поиска
    console.log('Search results:', products);
}

/**
 * Отображение результатов фильтрации
 */
function displayFilterResults(products) {
    // Реализация отображения результатов фильтрации
    console.log('Filter results:', products);
}