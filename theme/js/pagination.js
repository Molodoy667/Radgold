/**
 * Marketplace - AJAX Пагинация
 * Пагинация без перезагрузки страницы
 */

'use strict';

const Pagination = {
    config: {
        container: '#pagination-container',
        contentContainer: '#content-container',
        itemsPerPage: 12,
        maxVisiblePages: 5,
        loadingClass: 'pagination-loading'
    },
    
    currentPage: 1,
    totalPages: 1,
    totalItems: 0,
    
    /**
     * Инициализация пагинации
     */
    init: function(options = {}) {
        this.config = { ...this.config, ...options };
        this.bindEvents();
        console.log('📄 Pagination initialized');
    },
    
    /**
     * Привязка событий
     */
    bindEvents: function() {
        // Клик по номеру страницы
        $(document).on('click', '.pagination-link', (e) => {
            e.preventDefault();
            const page = parseInt($(e.target).data('page'));
            if (page && page !== this.currentPage) {
                this.loadPage(page);
            }
        });
        
        // Кнопки "Предыдущая/Следующая"
        $(document).on('click', '.pagination-prev', (e) => {
            e.preventDefault();
            if (this.currentPage > 1) {
                this.loadPage(this.currentPage - 1);
            }
        });
        
        $(document).on('click', '.pagination-next', (e) => {
            e.preventDefault();
            if (this.currentPage < this.totalPages) {
                this.loadPage(this.currentPage + 1);
            }
        });
        
        // Кнопки "Первая/Последняя"
        $(document).on('click', '.pagination-first', (e) => {
            e.preventDefault();
            if (this.currentPage !== 1) {
                this.loadPage(1);
            }
        });
        
        $(document).on('click', '.pagination-last', (e) => {
            e.preventDefault();
            if (this.currentPage !== this.totalPages) {
                this.loadPage(this.totalPages);
            }
        });
        
        // Клавиатурная навигация
        $(document).on('keydown', (e) => {
            if (e.target.tagName.toLowerCase() === 'input') return;
            
            if (e.key === 'ArrowLeft' && this.currentPage > 1) {
                this.loadPage(this.currentPage - 1);
            } else if (e.key === 'ArrowRight' && this.currentPage < this.totalPages) {
                this.loadPage(this.currentPage + 1);
            }
        });
    },
    
    /**
     * Загрузка страницы
     */
    loadPage: function(page, updateHistory = true) {
        if (page < 1 || page > this.totalPages || page === this.currentPage) {
            return;
        }
        
        this.showLoading();
        
        // Получение текущих параметров
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('page', page);
        
        // AJAX запрос
        $.ajax({
            url: window.location.pathname,
            method: 'GET',
            data: urlParams.toString(),
            headers: {
                'X-Pagination-Request': 'true'
            },
            success: (response) => {
                this.handleSuccess(response, page, updateHistory);
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
    handleSuccess: function(response, page, updateHistory) {
        if (response.success) {
            // Обновление контента
            $(this.config.contentContainer).html(response.content).addClass('animate-fade-in');
            
            // Обновление данных пагинации
            this.currentPage = page;
            this.totalPages = response.totalPages || this.totalPages;
            this.totalItems = response.totalItems || this.totalItems;
            
            // Перерисовка пагинации
            this.render();
            
            // Обновление URL
            if (updateHistory) {
                const url = new URL(window.location);
                url.searchParams.set('page', page);
                history.pushState({ page: page }, '', url);
            }
            
            // Прокрутка к началу контента
            this.scrollToContent();
            
            // Инициализация lazy loading для новых изображений
            if (window.initLazyLoading) {
                window.initLazyLoading();
            }
            
            // Событие изменения страницы
            $(document).trigger('pageChanged', [page, response]);
            
        } else {
            this.showError(response.message || 'Ошибка загрузки страницы');
        }
    },
    
    /**
     * Обработка ошибки
     */
    handleError: function(error) {
        console.error('Pagination Error:', error);
        this.showError('Произошла ошибка при загрузке страницы');
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
     * Прокрутка к контенту
     */
    scrollToContent: function() {
        const $content = $(this.config.contentContainer);
        if ($content.length) {
            $('html, body').animate({
                scrollTop: $content.offset().top - 100
            }, 400, 'easeInOutCubic');
        }
    },
    
    /**
     * Показ индикатора загрузки
     */
    showLoading: function() {
        $(this.config.contentContainer).addClass(this.config.loadingClass);
        $(this.config.container).addClass('loading');
        
        // Добавление спиннера
        if (!$('.pagination-spinner').length) {
            $(this.config.container).append(`
                <div class="pagination-spinner">
                    <div class="loader-3d"></div>
                </div>
            `);
        }
    },
    
    /**
     * Скрытие индикатора загрузки
     */
    hideLoading: function() {
        $(this.config.contentContainer).removeClass(this.config.loadingClass);
        $(this.config.container).removeClass('loading');
        $('.pagination-spinner').remove();
    },
    
    /**
     * Обновление пагинации
     */
    update: function(currentPage, totalPages, totalItems) {
        this.currentPage = currentPage;
        this.totalPages = totalPages;
        this.totalItems = totalItems;
        this.render();
    },
    
    /**
     * Отрисовка пагинации
     */
    render: function() {
        const $container = $(this.config.container);
        if (!$container.length || this.totalPages <= 1) {
            $container.hide();
            return;
        }
        
        const paginationHtml = this.generateHTML();
        $container.html(paginationHtml).show();
    },
    
    /**
     * Генерация HTML пагинации
     */
    generateHTML: function() {
        const pages = this.getVisiblePages();
        let html = '<nav class="pagination-nav" aria-label="Навигация по страницам">';
        
        html += '<ul class="pagination pagination-3d justify-content-center">';
        
        // Первая страница
        if (this.currentPage > 1) {
            html += `
                <li class="page-item">
                    <a class="page-link pagination-first" href="#" aria-label="Первая страница">
                        <i class="fas fa-angle-double-left"></i>
                    </a>
                </li>
            `;
        }
        
        // Предыдущая страница
        html += `
            <li class="page-item ${this.currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link pagination-prev" href="#" aria-label="Предыдущая страница">
                    <i class="fas fa-angle-left"></i>
                </a>
            </li>
        `;
        
        // Номера страниц
        pages.forEach(page => {
            if (page === '...') {
                html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            } else {
                const isActive = page === this.currentPage;
                html += `
                    <li class="page-item ${isActive ? 'active' : ''}">
                        <a class="page-link pagination-link" 
                           href="#" 
                           data-page="${page}"
                           ${isActive ? 'aria-current="page"' : ''}>
                            ${page}
                        </a>
                    </li>
                `;
            }
        });
        
        // Следующая страница
        html += `
            <li class="page-item ${this.currentPage === this.totalPages ? 'disabled' : ''}">
                <a class="page-link pagination-next" href="#" aria-label="Следующая страница">
                    <i class="fas fa-angle-right"></i>
                </a>
            </li>
        `;
        
        // Последняя страница
        if (this.currentPage < this.totalPages) {
            html += `
                <li class="page-item">
                    <a class="page-link pagination-last" href="#" aria-label="Последняя страница">
                        <i class="fas fa-angle-double-right"></i>
                    </a>
                </li>
            `;
        }
        
        html += '</ul>';
        
        // Информация о странице
        const startItem = (this.currentPage - 1) * this.config.itemsPerPage + 1;
        const endItem = Math.min(this.currentPage * this.config.itemsPerPage, this.totalItems);
        
        html += `
            <div class="pagination-info text-center mt-3">
                <small class="text-muted">
                    Показано ${startItem}-${endItem} из ${this.totalItems} результатов
                    (страница ${this.currentPage} из ${this.totalPages})
                </small>
            </div>
        `;
        
        html += '</nav>';
        
        return html;
    },
    
    /**
     * Получение видимых номеров страниц
     */
    getVisiblePages: function() {
        const pages = [];
        const maxVisible = this.config.maxVisiblePages;
        const current = this.currentPage;
        const total = this.totalPages;
        
        if (total <= maxVisible) {
            // Показываем все страницы
            for (let i = 1; i <= total; i++) {
                pages.push(i);
            }
        } else {
            // Логика для большого количества страниц
            const sidePages = Math.floor(maxVisible / 2);
            
            if (current <= sidePages + 1) {
                // Начало списка
                for (let i = 1; i <= maxVisible - 1; i++) {
                    pages.push(i);
                }
                pages.push('...');
                pages.push(total);
            } else if (current >= total - sidePages) {
                // Конец списка
                pages.push(1);
                pages.push('...');
                for (let i = total - maxVisible + 2; i <= total; i++) {
                    pages.push(i);
                }
            } else {
                // Середина списка
                pages.push(1);
                pages.push('...');
                for (let i = current - sidePages; i <= current + sidePages; i++) {
                    pages.push(i);
                }
                pages.push('...');
                pages.push(total);
            }
        }
        
        return pages;
    }
};

// Экспорт для глобального использования
window.Pagination = Pagination;