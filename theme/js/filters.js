/**
 * Marketplace - AJAX Фильтры
 * Фильтрация без перезагрузки страницы
 */

'use strict';

const Filters = {
    config: {
        container: '#filters-container',
        contentContainer: '#content-container',
        resetButton: '#filters-reset',
        applyButton: '#filters-apply',
        loadingClass: 'filters-loading',
        debounceDelay: 500
    },
    
    activeFilters: {},
    debounceTimer: null,
    
    /**
     * Инициализация фильтров
     */
    init: function(options = {}) {
        this.config = { ...this.config, ...options };
        this.loadFiltersFromURL();
        this.bindEvents();
        this.updateUI();
        console.log('🔍 Filters initialized');
    },
    
    /**
     * Привязка событий
     */
    bindEvents: function() {
        // Checkbox фильтры
        $(document).on('change', '.filter-checkbox', (e) => {
            this.handleCheckboxChange(e);
        });
        
        // Select фильтры
        $(document).on('change', '.filter-select', (e) => {
            this.handleSelectChange(e);
        });
        
        // Range фильтры (цена, рейтинг)
        $(document).on('input', '.filter-range', (e) => {
            this.handleRangeChange(e);
        });
        
        // Поиск в фильтрах
        $(document).on('input', '.filter-search', (e) => {
            this.handleSearchChange(e);
        });
        
        // Кнопка сброса
        $(document).on('click', this.config.resetButton, (e) => {
            e.preventDefault();
            this.resetFilters();
        });
        
        // Кнопка применения (если есть)
        $(document).on('click', this.config.applyButton, (e) => {
            e.preventDefault();
            this.applyFilters();
        });
        
        // Переключение секций фильтров
        $(document).on('click', '.filter-section-toggle', (e) => {
            this.toggleFilterSection(e);
        });
        
        // Закрытие тегов фильтров
        $(document).on('click', '.filter-tag-close', (e) => {
            this.removeFilterTag(e);
        });
    },
    
    /**
     * Загрузка фильтров из URL
     */
    loadFiltersFromURL: function() {
        const urlParams = new URLSearchParams(window.location.search);
        this.activeFilters = {};
        
        urlParams.forEach((value, key) => {
            if (key !== 'page') {
                if (this.activeFilters[key]) {
                    if (!Array.isArray(this.activeFilters[key])) {
                        this.activeFilters[key] = [this.activeFilters[key]];
                    }
                    this.activeFilters[key].push(value);
                } else {
                    this.activeFilters[key] = value;
                }
            }
        });
    },
    
    /**
     * Обработка изменения checkbox
     */
    handleCheckboxChange: function(e) {
        const $checkbox = $(e.target);
        const filterName = $checkbox.attr('name');
        const filterValue = $checkbox.val();
        const isChecked = $checkbox.is(':checked');
        
        if (!this.activeFilters[filterName]) {
            this.activeFilters[filterName] = [];
        }
        
        if (!Array.isArray(this.activeFilters[filterName])) {
            this.activeFilters[filterName] = [this.activeFilters[filterName]];
        }
        
        if (isChecked) {
            if (!this.activeFilters[filterName].includes(filterValue)) {
                this.activeFilters[filterName].push(filterValue);
            }
        } else {
            this.activeFilters[filterName] = this.activeFilters[filterName].filter(v => v !== filterValue);
            if (this.activeFilters[filterName].length === 0) {
                delete this.activeFilters[filterName];
            }
        }
        
        this.debouncedApply();
    },
    
    /**
     * Обработка изменения select
     */
    handleSelectChange: function(e) {
        const $select = $(e.target);
        const filterName = $select.attr('name');
        const filterValue = $select.val();
        
        if (filterValue && filterValue !== '') {
            this.activeFilters[filterName] = filterValue;
        } else {
            delete this.activeFilters[filterName];
        }
        
        this.debouncedApply();
    },
    
    /**
     * Обработка изменения range
     */
    handleRangeChange: function(e) {
        const $range = $(e.target);
        const filterName = $range.attr('name');
        const filterValue = $range.val();
        
        // Обновление отображения значения
        const $display = $range.siblings('.range-display');
        if ($display.length) {
            const formatted = this.formatRangeValue(filterName, filterValue);
            $display.text(formatted);
        }
        
        if (filterValue) {
            this.activeFilters[filterName] = filterValue;
        } else {
            delete this.activeFilters[filterName];
        }
        
        this.debouncedApply();
    },
    
    /**
     * Обработка поиска в фильтрах
     */
    handleSearchChange: function(e) {
        const $search = $(e.target);
        const query = $search.val().toLowerCase();
        const targetSelector = $search.data('target');
        
        if (targetSelector) {
            $(targetSelector).each(function() {
                const text = $(this).text().toLowerCase();
                const shouldShow = text.includes(query);
                $(this).toggle(shouldShow);
            });
        }
    },
    
    /**
     * Отложенное применение фильтров
     */
    debouncedApply: function() {
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(() => {
            this.applyFilters();
        }, this.config.debounceDelay);
    },
    
    /**
     * Применение фильтров
     */
    applyFilters: function() {
        this.showLoading();
        this.updateActiveTags();
        
        // Подготовка параметров
        const params = new URLSearchParams();
        
        Object.keys(this.activeFilters).forEach(key => {
            const value = this.activeFilters[key];
            if (Array.isArray(value)) {
                value.forEach(v => params.append(key, v));
            } else {
                params.set(key, value);
            }
        });
        
        // AJAX запрос
        $.ajax({
            url: window.location.pathname,
            method: 'GET',
            data: params.toString(),
            headers: {
                'X-Filters-Request': 'true'
            },
            success: (response) => {
                this.handleSuccess(response);
            },
            error: (xhr, status, error) => {
                this.handleError(error);
            },
            complete: () => {
                this.hideLoading();
            }
        });
    },
    
    /**
     * Обработка успешного ответа
     */
    handleSuccess: function(response) {
        if (response.success) {
            // Обновление контента
            $(this.config.contentContainer).html(response.content).addClass('animate-fade-in');
            
            // Обновление URL
            const url = new URL(window.location);
            url.search = '';
            
            Object.keys(this.activeFilters).forEach(key => {
                const value = this.activeFilters[key];
                if (Array.isArray(value)) {
                    value.forEach(v => url.searchParams.append(key, v));
                } else {
                    url.searchParams.set(key, value);
                }
            });
            
            history.replaceState({}, '', url);
            
            // Обновление счетчиков фильтров
            this.updateFilterCounts(response.filterCounts);
            
            // Инициализация lazy loading
            if (window.initLazyLoading) {
                window.initLazyLoading();
            }
            
            // Обновление пагинации
            if (window.Pagination && response.pagination) {
                window.Pagination.update(1, response.pagination.totalPages, response.pagination.totalItems);
            }
            
            // Событие применения фильтров
            $(document).trigger('filtersApplied', [this.activeFilters, response]);
            
        } else {
            this.showError(response.message || 'Ошибка применения фильтров');
        }
    },
    
    /**
     * Обработка ошибки
     */
    handleError: function(error) {
        console.error('Filters Error:', error);
        this.showError('Произошла ошибка при применении фильтров');
    },
    
    /**
     * Отображение ошибки
     */
    showError: function(message) {
        if (window.showNotification) {
            window.showNotification(message, 'error');
        } else {
            alert(message);
        }
    },
    
    /**
     * Сброс фильтров
     */
    resetFilters: function() {
        this.activeFilters = {};
        this.updateUI();
        this.applyFilters();
    },
    
    /**
     * Обновление UI фильтров
     */
    updateUI: function() {
        // Сброс всех checkbox
        $('.filter-checkbox').prop('checked', false);
        
        // Сброс всех select
        $('.filter-select').val('');
        
        // Сброс всех range
        $('.filter-range').each(function() {
            const $range = $(this);
            $range.val($range.attr('min') || 0);
            const $display = $range.siblings('.range-display');
            if ($display.length) {
                const formatted = Filters.formatRangeValue($range.attr('name'), $range.val());
                $display.text(formatted);
            }
        });
        
        // Применение активных фильтров
        Object.keys(this.activeFilters).forEach(key => {
            const value = this.activeFilters[key];
            
            if (Array.isArray(value)) {
                value.forEach(v => {
                    $(`.filter-checkbox[name="${key}"][value="${v}"]`).prop('checked', true);
                });
            } else {
                $(`.filter-select[name="${key}"]`).val(value);
                $(`.filter-range[name="${key}"]`).val(value);
                
                // Обновление отображения range
                const $range = $(`.filter-range[name="${key}"]`);
                const $display = $range.siblings('.range-display');
                if ($display.length) {
                    const formatted = this.formatRangeValue(key, value);
                    $display.text(formatted);
                }
            }
        });
        
        this.updateActiveTags();
    },
    
    /**
     * Обновление активных тегов фильтров
     */
    updateActiveTags: function() {
        const $container = $('.active-filters-tags');
        if (!$container.length) return;
        
        let html = '';
        
        Object.keys(this.activeFilters).forEach(key => {
            const value = this.activeFilters[key];
            const filterLabel = this.getFilterLabel(key);
            
            if (Array.isArray(value)) {
                value.forEach(v => {
                    const valueLabel = this.getFilterValueLabel(key, v);
                    html += this.createFilterTag(key, v, `${filterLabel}: ${valueLabel}`);
                });
            } else {
                const valueLabel = this.getFilterValueLabel(key, value);
                html += this.createFilterTag(key, value, `${filterLabel}: ${valueLabel}`);
            }
        });
        
        if (html) {
            $container.html(`
                <div class="mb-3">
                    <strong>Активные фильтры:</strong>
                    <div class="filter-tags mt-2">${html}</div>
                    <button class="btn btn-link btn-sm p-0 ms-2" id="clear-all-filters">
                        <i class="fas fa-times"></i> Очистить все
                    </button>
                </div>
            `).show();
        } else {
            $container.hide();
        }
    },
    
    /**
     * Создание тега фильтра
     */
    createFilterTag: function(key, value, label) {
        return `
            <span class="badge bg-primary me-2 mb-2 filter-tag" data-key="${key}" data-value="${value}">
                ${label}
                <button class="btn-close btn-close-white ms-2 filter-tag-close" aria-label="Удалить фильтр"></button>
            </span>
        `;
    },
    
    /**
     * Удаление тега фильтра
     */
    removeFilterTag: function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const $tag = $(e.target).closest('.filter-tag');
        const key = $tag.data('key');
        const value = $tag.data('value').toString();
        
        if (Array.isArray(this.activeFilters[key])) {
            this.activeFilters[key] = this.activeFilters[key].filter(v => v !== value);
            if (this.activeFilters[key].length === 0) {
                delete this.activeFilters[key];
            }
        } else {
            delete this.activeFilters[key];
        }
        
        this.updateUI();
        this.applyFilters();
    },
    
    /**
     * Переключение секции фильтров
     */
    toggleFilterSection: function(e) {
        const $toggle = $(e.target);
        const $section = $toggle.closest('.filter-section');
        const $content = $section.find('.filter-section-content');
        
        $content.slideToggle();
        $toggle.find('i').toggleClass('fa-chevron-down fa-chevron-up');
        $section.toggleClass('collapsed');
    },
    
    /**
     * Показ индикатора загрузки
     */
    showLoading: function() {
        $(this.config.contentContainer).addClass(this.config.loadingClass);
        $(this.config.container).addClass('loading');
    },
    
    /**
     * Скрытие индикатора загрузки
     */
    hideLoading: function() {
        $(this.config.contentContainer).removeClass(this.config.loadingClass);
        $(this.config.container).removeClass('loading');
    },
    
    /**
     * Обновление счетчиков фильтров
     */
    updateFilterCounts: function(counts) {
        if (!counts) return;
        
        Object.keys(counts).forEach(key => {
            const filterCounts = counts[key];
            Object.keys(filterCounts).forEach(value => {
                const count = filterCounts[value];
                const $checkbox = $(`.filter-checkbox[name="${key}"][value="${value}"]`);
                const $label = $checkbox.siblings('label');
                
                if ($label.length) {
                    const text = $label.text().replace(/\(\d+\)$/, '');
                    $label.text(`${text} (${count})`);
                }
            });
        });
    },
    
    /**
     * Получение подписи фильтра
     */
    getFilterLabel: function(key) {
        const $filter = $(`.filter-checkbox[name="${key}"], .filter-select[name="${key}"], .filter-range[name="${key}"]`).first();
        const $section = $filter.closest('.filter-section');
        
        if ($section.length) {
            const label = $section.find('.filter-section-title').text();
            if (label) return label;
        }
        
        return key.charAt(0).toUpperCase() + key.slice(1);
    },
    
    /**
     * Получение подписи значения фильтра
     */
    getFilterValueLabel: function(key, value) {
        const $checkbox = $(`.filter-checkbox[name="${key}"][value="${value}"]`);
        if ($checkbox.length) {
            const $label = $checkbox.siblings('label');
            if ($label.length) {
                return $label.text().replace(/\(\d+\)$/, '').trim();
            }
        }
        
        const $option = $(`.filter-select[name="${key}"] option[value="${value}"]`);
        if ($option.length) {
            return $option.text();
        }
        
        // Для range фильтров
        return this.formatRangeValue(key, value);
    },
    
    /**
     * Форматирование значения range
     */
    formatRangeValue: function(key, value) {
        if (key.includes('price')) {
            return `${value} ₽`;
        } else if (key.includes('rating')) {
            return `${value} ★`;
        } else if (key.includes('discount')) {
            return `${value}%`;
        }
        
        return value;
    }
};

// Обработчик очистки всех фильтров
$(document).on('click', '#clear-all-filters', function(e) {
    e.preventDefault();
    Filters.resetFilters();
});

// Экспорт для глобального использования
window.Filters = Filters;