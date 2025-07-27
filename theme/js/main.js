/**
 * Marketplace - Современный JavaScript
 * Функции: Переключение темы, Lazy Loading, Поиск с автоподсказками, AJAX, Анимации
 * Зависимости: jQuery 3.6+, Bootstrap 5+, Intersection Observer API
 */

'use strict';

// Глобальные переменные
const MarketplaceApp = {
    config: {
        theme: localStorage.getItem('theme') || 'light',
        searchDelay: 300,
        lazyLoadOffset: '50px',
        animationDuration: 300
    },
    cache: {
        $window: $(window),
        $document: $(document),
        $body: $('body'),
        searchTimer: null,
        observer: null
    }
};

$(document).ready(function() {
    console.log('🚀 Marketplace App initializing...');
    
    // Инициализация основных модулей
    initializeApp();
    
    console.log('✅ Marketplace App initialized successfully!');
});

/**
 * Основная инициализация приложения
 */
function initializeApp() {
    // Базовая настройка
    setupApp();
    
    // Инициализация модулей
    initTheme();
    initAjax();
    initSearch();
    initLazyLoading();
    initAnimations();
    initTooltips();
    initSmoothScroll();
    
    // Обработчики событий
    bindEvents();
    
    // Проверка поддержки функций
    checkBrowserSupport();
}

/**
 * Базовая настройка приложения
 */
function setupApp() {
    // Применение сохраненной темы
    document.documentElement.setAttribute('data-theme', MarketplaceApp.config.theme);
    
    // Настройка viewport для мобильных устройств
    if (!document.querySelector('meta[name="viewport"]')) {
        const viewport = document.createElement('meta');
        viewport.name = 'viewport';
        viewport.content = 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no';
        document.head.appendChild(viewport);
    }
    
    // Предотвращение FOUC (Flash of Unstyled Content)
    MarketplaceApp.cache.$body.removeClass('loading');
}

/**
 * Инициализация системы тем
 */
function initTheme() {
    console.log('🎨 Initializing theme system...');
    
    // Создание переключателя темы, если его нет
    if (!$('.theme-toggle').length) {
        const themeToggle = $('<div class="theme-toggle" title="Переключить тему"></div>');
        $('.navbar-nav').append($('<li class="nav-item">').append(themeToggle));
    }
    
    // Обработчик переключения темы
    $(document).on('click', '.theme-toggle', function() {
        toggleTheme();
    });
    
    // Применение системной темы при первом посещении
    if (!localStorage.getItem('theme')) {
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        MarketplaceApp.config.theme = prefersDark ? 'dark' : 'light';
        localStorage.setItem('theme', MarketplaceApp.config.theme);
        document.documentElement.setAttribute('data-theme', MarketplaceApp.config.theme);
    }
}

/**
 * Переключение темы
 */
function toggleTheme() {
    const newTheme = MarketplaceApp.config.theme === 'light' ? 'dark' : 'light';
    
    // Анимация переключения
    MarketplaceApp.cache.$body.addClass('theme-transition');
    
    setTimeout(() => {
        MarketplaceApp.config.theme = newTheme;
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        
        // Удаление класса анимации
        setTimeout(() => {
            MarketplaceApp.cache.$body.removeClass('theme-transition');
        }, MarketplaceApp.config.animationDuration);
    }, 50);
    
    // Отправка события изменения темы
    $(document).trigger('themeChanged', [newTheme]);
    
    console.log(`🎨 Theme switched to: ${newTheme}`);
}

/**
 * Настройка AJAX
 */
function initAjax() {
    console.log('📡 Initializing AJAX...');
    
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    
    $.ajaxSetup({
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        },
        beforeSend: function(xhr, settings) {
            // Показ индикатора загрузки
            showLoader();
        },
        complete: function(xhr, status) {
            // Скрытие индикатора загрузки
            hideLoader();
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            showNotification('Произошла ошибка при загрузке данных', 'error');
        }
    });
}

/**
 * Инициализация поиска с автоподсказками
 */
function initSearch() {
    console.log('🔍 Initializing search...');
    
    const $searchContainer = $('.search-container');
    const $searchInput = $('.search-input');
    const $searchSuggestions = $('.search-suggestions');
    
    if (!$searchInput.length) return;
    
    // Обработчик ввода в поиск
    $searchInput.on('input', function() {
        const query = $(this).val().trim();
        
        clearTimeout(MarketplaceApp.cache.searchTimer);
        
        if (query.length >= 2) {
            MarketplaceApp.cache.searchTimer = setTimeout(() => {
                fetchSearchSuggestions(query);
            }, MarketplaceApp.config.searchDelay);
        } else {
            hideSuggestions();
        }
    });
    
    // Обработчик клика по подсказке
    $(document).on('click', '.search-suggestion', function() {
        const suggestion = $(this).text();
        $searchInput.val(suggestion);
        hideSuggestions();
        performSearch(suggestion);
    });
    
    // Скрытие подсказок при клике вне поиска
    $(document).on('click', function(e) {
        if (!$searchContainer.is(e.target) && $searchContainer.has(e.target).length === 0) {
            hideSuggestions();
        }
    });
    
    // Обработчик Enter в поиске
    $searchInput.on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const query = $(this).val().trim();
            if (query) {
                hideSuggestions();
                performSearch(query);
            }
        }
    });
}

/**
 * Получение подсказок для поиска
 */
function fetchSearchSuggestions(query) {
    $.ajax({
        url: '/api/search/suggestions',
        method: 'GET',
        data: { q: query },
        success: function(response) {
            displaySuggestions(response.suggestions || []);
        },
        error: function() {
            console.warn('Failed to fetch search suggestions');
        }
    });
}

/**
 * Отображение подсказок поиска
 */
function displaySuggestions(suggestions) {
    const $suggestions = $('.search-suggestions');
    
    if (!suggestions.length) {
        hideSuggestions();
        return;
    }
    
    const html = suggestions.map(suggestion => 
        `<div class="search-suggestion">${escapeHtml(suggestion)}</div>`
    ).join('');
    
    $suggestions.html(html).fadeIn(200);
}

/**
 * Скрытие подсказок поиска
 */
function hideSuggestions() {
    $('.search-suggestions').fadeOut(200);
}

/**
 * Выполнение поиска
 */
function performSearch(query) {
    console.log('🔍 Performing search:', query);
    
    // Добавление в историю браузера
    const url = new URL(window.location);
    url.searchParams.set('search', query);
    history.pushState({}, '', url);
    
    // AJAX запрос поиска
    $.ajax({
        url: '/search',
        method: 'GET',
        data: { q: query },
        success: function(response) {
            $('#search-results').html(response).addClass('animate-fade-in');
            
            // Обновление breadcrumbs
            updateBreadcrumbs([
                { name: 'Главная', url: '/' },
                { name: `Поиск: "${query}"`, url: null }
            ]);
            
            // Инициализация lazy loading для новых изображений
            initLazyLoading();
        }
    });
}

/**
 * Инициализация Lazy Loading для изображений
 */
function initLazyLoading() {
    console.log('🖼️ Initializing lazy loading...');
    
    // Проверка поддержки Intersection Observer
    if (!('IntersectionObserver' in window)) {
        // Fallback для старых браузеров
        $('.lazy-img').each(function() {
            loadImage(this);
        });
        return;
    }
    
    // Отключение предыдущего observer
    if (MarketplaceApp.cache.observer) {
        MarketplaceApp.cache.observer.disconnect();
    }
    
    // Создание нового observer
    MarketplaceApp.cache.observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                loadImage(entry.target);
                MarketplaceApp.cache.observer.unobserve(entry.target);
            }
        });
    }, {
        rootMargin: MarketplaceApp.config.lazyLoadOffset
    });
    
    // Наблюдение за изображениями
    $('.lazy-img:not(.loaded)').each(function() {
        MarketplaceApp.cache.observer.observe(this);
    });
}

/**
 * Загрузка изображения
 */
function loadImage(img) {
    const $img = $(img);
    const src = $img.data('src');
    
    if (!src) return;
    
    const imageObj = new Image();
    
    imageObj.onload = function() {
        $img.attr('src', src)
            .addClass('loaded')
            .removeClass('lazy-img');
    };
    
    imageObj.onerror = function() {
        $img.attr('src', '/theme/images/placeholder.jpg')
            .addClass('loaded error')
            .removeClass('lazy-img');
    };
    
    imageObj.src = src;
}

/**
 * Инициализация анимаций
 */
function initAnimations() {
    console.log('✨ Initializing animations...');
    
    // Анимация появления элементов при скролле
    if ('IntersectionObserver' in window) {
        const animationObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const $element = $(entry.target);
                    const animation = $element.data('animation') || 'animate-fade-in';
                    $element.addClass(animation);
                    animationObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1
        });
        
        $('[data-animation]').each(function() {
            animationObserver.observe(this);
        });
    }
    
    // Анимация кнопок при наведении
    $(document).on('mouseenter', '.btn-3d', function() {
        $(this).addClass('animate-pulse');
        setTimeout(() => {
            $(this).removeClass('animate-pulse');
        }, 600);
    });
}

/**
 * Инициализация всплывающих подсказок
 */
function initTooltips() {
    console.log('💬 Initializing tooltips...');
    
    // Bootstrap tooltips
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        $('[data-bs-toggle="tooltip"]').each(function() {
            new bootstrap.Tooltip(this);
        });
    }
    
    // Кастомные tooltips
    $(document).on('mouseenter', '[title]:not([data-bs-toggle="tooltip"])', function() {
        const $this = $(this);
        const title = $this.attr('title');
        
        if (!title) return;
        
        const $tooltip = $('<div class="custom-tooltip">')
            .text(title)
            .appendTo('body');
        
        const offset = $this.offset();
        const tooltipWidth = $tooltip.outerWidth();
        const tooltipHeight = $tooltip.outerHeight();
        
        $tooltip.css({
            top: offset.top - tooltipHeight - 10,
            left: offset.left + ($this.outerWidth() / 2) - (tooltipWidth / 2)
        }).fadeIn(200);
        
        $this.data('tooltip', $tooltip);
        $this.attr('title', ''); // Временно убираем title
    });
    
    $(document).on('mouseleave', '[title], [data-original-title]', function() {
        const $this = $(this);
        const $tooltip = $this.data('tooltip');
        
        if ($tooltip) {
            $tooltip.fadeOut(200, function() {
                $(this).remove();
            });
            $this.removeData('tooltip');
        }
        
        // Восстанавливаем title
        const originalTitle = $this.data('original-title');
        if (originalTitle) {
            $this.attr('title', originalTitle);
        }
    });
}

/**
 * Инициализация плавного скролла
 */
function initSmoothScroll() {
    console.log('📜 Initializing smooth scroll...');
    
    $(document).on('click', 'a[href^="#"]', function(e) {
        const target = $(this.getAttribute('href'));
        
        if (target.length) {
            e.preventDefault();
            
            $('html, body').animate({
                scrollTop: target.offset().top - 100
            }, 600, 'easeInOutCubic');
        }
    });
}

/**
 * Привязка обработчиков событий
 */
function bindEvents() {
    console.log('🔗 Binding events...');
    
    // Обработчик формы поиска
    $(document).on('submit', '.search-form', function(e) {
        e.preventDefault();
        const query = $(this).find('.search-input').val().trim();
        if (query) {
            performSearch(query);
        }
    });
    
    // Обработчик добавления в корзину
    $(document).on('click', '.add-to-cart', function(e) {
        e.preventDefault();
        addToCart($(this));
    });
    
    // Обработчик фильтров
    $(document).on('change', '.filter-checkbox, .filter-select', function() {
        applyFilters();
    });
    
    // Обработчик пагинации
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        loadPage($(this).attr('href'));
    });
    
    // Обработчик изменения количества товара
    $(document).on('click', '.quantity-btn', function() {
        const $input = $(this).siblings('.quantity-input');
        const action = $(this).data('action');
        let value = parseInt($input.val()) || 1;
        
        if (action === 'increase') {
            value++;
        } else if (action === 'decrease' && value > 1) {
            value--;
        }
        
        $input.val(value);
        updateCartItem($input.closest('.cart-item'));
    });
    
    // Обработчик закрытия уведомлений
    $(document).on('click', '.notification-close', function() {
        $(this).closest('.notification').fadeOut(300, function() {
            $(this).remove();
        });
    });
    
    // Обработчик изменения размера окна
    let resizeTimer;
    MarketplaceApp.cache.$window.on('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            handleResize();
        }, 250);
    });
}

/**
 * Добавление товара в корзину
 */
function addToCart($button) {
    const productId = $button.data('product-id');
    const quantity = $button.closest('.product-card').find('.quantity-input').val() || 1;
    
    $button.addClass('loading').prop('disabled', true);
    
    $.ajax({
        url: '/cart/add',
        method: 'POST',
        data: {
            product_id: productId,
            quantity: quantity
        },
        success: function(response) {
            if (response.success) {
                showNotification('Товар добавлен в корзину!', 'success');
                updateCartCounter(response.cart_count);
                
                // Анимация кнопки
                $button.removeClass('loading')
                       .addClass('animate-bounce')
                       .html('<i class="fas fa-check"></i> Добавлено');
                
                setTimeout(() => {
                    $button.removeClass('animate-bounce')
                           .html('<i class="fas fa-shopping-cart"></i> В корзину')
                           .prop('disabled', false);
                }, 2000);
            } else {
                showNotification(response.message || 'Ошибка добавления товара', 'error');
                $button.removeClass('loading').prop('disabled', false);
            }
        },
        error: function() {
            showNotification('Ошибка добавления товара в корзину', 'error');
            $button.removeClass('loading').prop('disabled', false);
        }
    });
}

/**
 * Применение фильтров
 */
function applyFilters() {
    const filters = {};
    
    $('.filter-checkbox:checked').each(function() {
        const name = $(this).attr('name');
        if (!filters[name]) filters[name] = [];
        filters[name].push($(this).val());
    });
    
    $('.filter-select').each(function() {
        const name = $(this).attr('name');
        const value = $(this).val();
        if (value) filters[name] = value;
    });
    
    console.log('🔍 Applying filters:', filters);
    
    $.ajax({
        url: window.location.pathname,
        method: 'GET',
        data: filters,
        success: function(response) {
            $('#products-grid').html(response).addClass('animate-fade-in');
            initLazyLoading();
            
            // Обновление URL без перезагрузки
            const url = new URL(window.location);
            Object.keys(filters).forEach(key => {
                if (Array.isArray(filters[key])) {
                    url.searchParams.delete(key);
                    filters[key].forEach(value => {
                        url.searchParams.append(key, value);
                    });
                } else {
                    url.searchParams.set(key, filters[key]);
                }
            });
            history.replaceState({}, '', url);
        }
    });
}

/**
 * Загрузка страницы с пагинацией
 */
function loadPage(url) {
    $.ajax({
        url: url,
        method: 'GET',
        success: function(response) {
            $('#products-grid').html(response).addClass('animate-fade-in');
            initLazyLoading();
            
            // Прокрутка к началу результатов
            $('html, body').animate({
                scrollTop: $('#products-grid').offset().top - 100
            }, 600);
            
            // Обновление URL
            history.pushState({}, '', url);
        }
    });
}

/**
 * Обновление счетчика корзины
 */
function updateCartCounter(count) {
    $('.cart-counter').text(count).addClass('animate-pulse');
    
    setTimeout(() => {
        $('.cart-counter').removeClass('animate-pulse');
    }, 600);
}

/**
 * Обновление breadcrumbs
 */
function updateBreadcrumbs(breadcrumbs) {
    const $breadcrumb = $('.breadcrumb-3d ol');
    
    if (!$breadcrumb.length) return;
    
    const html = breadcrumbs.map((item, index) => {
        if (index === breadcrumbs.length - 1) {
            return `<li>${escapeHtml(item.name)}</li>`;
        } else {
            return `<li><a href="${item.url}">${escapeHtml(item.name)}</a></li>`;
        }
    }).join('');
    
    $breadcrumb.html(html);
}

/**
 * Показ уведомления
 */
function showNotification(message, type = 'info', duration = 5000) {
    const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle'
    };
    
    const $notification = $(`
        <div class="notification notification-${type} animate-slide-right">
            <i class="${icons[type]}"></i>
            <span class="notification-message">${escapeHtml(message)}</span>
            <button class="notification-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `);
    
    // Добавление в контейнер уведомлений
    if (!$('#notifications-container').length) {
        $('body').append('<div id="notifications-container"></div>');
    }
    
    $('#notifications-container').append($notification);
    
    // Автоматическое скрытие
    if (duration > 0) {
        setTimeout(() => {
            $notification.fadeOut(300, function() {
                $(this).remove();
            });
        }, duration);
    }
}

/**
 * Показ индикатора загрузки
 */
function showLoader() {
    if (!$('#global-loader').length) {
        $('body').append(`
            <div id="global-loader">
                <div class="loader-3d"></div>
            </div>
        `);
    }
    $('#global-loader').fadeIn(200);
}

/**
 * Скрытие индикатора загрузки
 */
function hideLoader() {
    $('#global-loader').fadeOut(200);
}

/**
 * Обработчик изменения размера окна
 */
function handleResize() {
    // Пересчет позиций элементов
    $('.custom-tooltip').remove();
    
    // Обновление lazy loading
    if (MarketplaceApp.cache.observer) {
        initLazyLoading();
    }
}

/**
 * Проверка поддержки браузера
 */
function checkBrowserSupport() {
    const features = {
        'CSS Custom Properties': CSS.supports('color', 'var(--fake-var)'),
        'Intersection Observer': 'IntersectionObserver' in window,
        'Local Storage': 'localStorage' in window,
        'Fetch API': 'fetch' in window
    };
    
    console.table(features);
    
    // Предупреждение для старых браузеров
    if (!features['CSS Custom Properties']) {
        showNotification('Ваш браузер устарел. Некоторые функции могут работать некорректно.', 'warning', 10000);
    }
}

/**
 * Экранирование HTML
 */
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    
    return text.replace(/[&<>"']/g, function(m) { 
        return map[m]; 
    });
}

/**
 * Утилиты для работы с куками
 */
const Cookie = {
    set: function(name, value, days = 7) {
        const expires = new Date();
        expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/`;
    },
    
    get: function(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    },
    
    delete: function(name) {
        this.set(name, "", -1);
    }
};

/**
 * Утилиты для работы с URL
 */
const URLUtils = {
    getParam: function(name) {
        const url = new URL(window.location);
        return url.searchParams.get(name);
    },
    
    setParam: function(name, value) {
        const url = new URL(window.location);
        url.searchParams.set(name, value);
        history.replaceState({}, '', url);
    },
    
    removeParam: function(name) {
        const url = new URL(window.location);
        url.searchParams.delete(name);
        history.replaceState({}, '', url);
    }
};

// Экспорт для глобального использования
window.MarketplaceApp = MarketplaceApp;
window.showNotification = showNotification;
window.toggleTheme = toggleTheme;
window.Cookie = Cookie;
window.URLUtils = URLUtils;