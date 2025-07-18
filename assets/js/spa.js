/**
 * SPA (Single Page Application) функціонал для дошки оголошень
 * Реалізує навігацію без перезавантаження сторінки через AJAX
 */

class ClassifiedsSPA {
    constructor() {
        this.currentPage = 'home';
        this.contentContainer = document.getElementById('main-content');
        this.isLoading = false;
        this.cache = new Map();
        
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.setupAjaxForms();
        this.setupPushState();
        this.loadInitialPage();
    }
    
    // Налаштування слухачів подій
    setupEventListeners() {
        // Перехоплення кліків по посиланнях
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[data-spa]');
            if (link) {
                e.preventDefault();
                const url = link.getAttribute('href');
                this.navigateTo(url);
            }
        });
        
        // Обробка кнопки "Назад" браузера
        window.addEventListener('popstate', (e) => {
            if (e.state && e.state.page) {
                this.loadPage(e.state.page, false);
            }
        });
        
        // Глобальні AJAX події
        document.addEventListener('ajaxStart', () => this.showLoader());
        document.addEventListener('ajaxComplete', () => this.hideLoader());
    }
    
    // Налаштування AJAX форм
    setupAjaxForms() {
        document.addEventListener('submit', (e) => {
            const form = e.target.closest('form[data-ajax]');
            if (form) {
                e.preventDefault();
                this.submitForm(form);
            }
        });
    }
    
    // Налаштування History API
    setupPushState() {
        // Замінюємо початковий стан
        window.history.replaceState(
            { page: this.currentPage }, 
            document.title, 
            window.location.pathname
        );
    }
    
    // Завантаження початкової сторінки
    loadInitialPage() {
        const path = window.location.pathname;
        if (path === '/' || path === '/index.php') {
            this.currentPage = 'home';
        } else {
            this.currentPage = this.getPageFromPath(path);
        }
    }
    
    // Навігація до сторінки
    async navigateTo(url, addToHistory = true) {
        if (this.isLoading) return;
        
        const page = this.getPageFromPath(url);
        
        if (addToHistory) {
            window.history.pushState({ page }, '', url);
        }
        
        await this.loadPage(page);
    }
    
    // Завантаження сторінки
    async loadPage(page, updateHistory = true) {
        if (this.isLoading) return;
        
        this.isLoading = true;
        this.showLoader();
        
        try {
            // Перевіряємо кеш
            let content = this.cache.get(page);
            
            if (!content) {
                // Завантажуємо контент через AJAX
                content = await this.fetchPageContent(page);
                this.cache.set(page, content);
            }
            
            // Оновлюємо контент
            await this.updateContent(content);
            
            // Оновлюємо навігацію
            this.updateNavigation(page);
            
            // Оновлюємо заголовок
            this.updateTitle(content.title);
            
            this.currentPage = page;
            
            // Виконуємо JavaScript для нової сторінки
            this.executePageScripts(page);
            
        } catch (error) {
            console.error('Помилка завантаження сторінки:', error);
            this.showError('Помилка завантаження сторінки. Спробуйте ще раз.');
        } finally {
            this.isLoading = false;
            this.hideLoader();
        }
    }
    
    // Отримання контенту сторінки
    async fetchPageContent(page) {
        const response = await fetch(`ajax/load_page.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ page, timestamp: Date.now() })
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return await response.json();
    }
    
    // Оновлення контенту сторінки
    async updateContent(content) {
        return new Promise((resolve) => {
            // Анімація зникнення
            this.contentContainer.style.opacity = '0';
            this.contentContainer.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                // Оновлюємо HTML
                this.contentContainer.innerHTML = content.html;
                
                // Анімація появи
                this.contentContainer.style.opacity = '1';
                this.contentContainer.style.transform = 'translateY(0)';
                
                // Ініціалізуємо компоненти Bootstrap
                this.initBootstrapComponents();
                
                // Ініціалізуємо AOS анімації
                if (typeof AOS !== 'undefined') {
                    AOS.refresh();
                }
                
                resolve();
            }, 300);
        });
    }
    
    // Оновлення навігації
    updateNavigation(page) {
        // Видаляємо активні класи
        document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
            link.classList.remove('active');
        });
        
        // Додаємо активний клас для поточної сторінки
        const activeLink = document.querySelector(`[data-page="${page}"]`);
        if (activeLink) {
            activeLink.classList.add('active');
        }
    }
    
    // Оновлення заголовка
    updateTitle(title) {
        document.title = title;
        
        // Оновлюємо мета-тег опису, якщо потрібно
        const metaDescription = document.querySelector('meta[name="description"]');
        if (metaDescription && title) {
            metaDescription.setAttribute('content', `${title} - Дошка Оголошень`);
        }
    }
    
    // Виконання скриптів для сторінки
    executePageScripts(page) {
        switch (page) {
            case 'categories':
                this.initCategoriesPage();
                break;
            case 'search':
                this.initSearchPage();
                break;
            case 'add_ad':
                this.initAddAdPage();
                break;
            case 'profile':
                this.initProfilePage();
                break;
        }
    }
    
    // Ініціалізація компонентів Bootstrap
    initBootstrapComponents() {
        // Tooltip
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        
        // Popover
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));
        
        // Modal
        const modalElements = document.querySelectorAll('.modal');
        modalElements.forEach(modalEl => new bootstrap.Modal(modalEl));
    }
    
    // Відправка форми через AJAX
    async submitForm(form) {
        if (this.isLoading) return;
        
        this.isLoading = true;
        const submitButton = form.querySelector('[type="submit"]');
        const originalText = submitButton.innerHTML;
        
        // Показуємо стан завантаження
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Завантаження...';
        submitButton.disabled = true;
        
        try {
            const formData = new FormData(form);
            const action = form.getAttribute('action') || 'ajax/form_handler.php';
            
            const response = await fetch(action, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showSuccess(result.message || 'Операція виконана успішно');
                
                // Якщо потрібно перенаправити
                if (result.redirect) {
                    await this.navigateTo(result.redirect);
                }
                
                // Якщо потрібно оновити контент
                if (result.reload) {
                    await this.loadPage(this.currentPage, false);
                }
                
                // Очищаємо форму, якщо потрібно
                if (result.clearForm) {
                    form.reset();
                }
            } else {
                this.showError(result.message || 'Сталася помилка');
            }
            
        } catch (error) {
            console.error('Помилка відправки форми:', error);
            this.showError('Помилка з\'єднання. Спробуйте ще раз.');
        } finally {
            // Відновлюємо кнопку
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
            this.isLoading = false;
        }
    }
    
    // Отримання назви сторінки з URL
    getPageFromPath(path) {
        if (path === '/' || path === '/index.php' || path === '') {
            return 'home';
        }
        
        const matches = path.match(/\/pages\/(\w+)\.php/);
        return matches ? matches[1] : 'home';
    }
    
    // Показати завантажувач
    showLoader() {
        let loader = document.getElementById('spa-loader');
        if (!loader) {
            loader = this.createLoader();
        }
        loader.style.display = 'flex';
    }
    
    // Сховати завантажувач
    hideLoader() {
        const loader = document.getElementById('spa-loader');
        if (loader) {
            loader.style.display = 'none';
        }
    }
    
    // Створити завантажувач
    createLoader() {
        const loader = document.createElement('div');
        loader.id = 'spa-loader';
        loader.className = 'spa-loader';
        loader.innerHTML = `
            <div class="spa-loader-content">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Завантаження...</span>
                </div>
                <p class="mt-2 text-muted">Завантаження...</p>
            </div>
        `;
        document.body.appendChild(loader);
        return loader;
    }
    
    // Показати повідомлення про успіх
    showSuccess(message) {
        this.showNotification(message, 'success');
    }
    
    // Показати помилку
    showError(message) {
        this.showNotification(message, 'danger');
    }
    
    // Показати сповіщення
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show notification-toast`;
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        // Додаємо до контейнера сповіщень
        let container = document.getElementById('notifications-container');
        if (!container) {
            container = this.createNotificationsContainer();
        }
        
        container.appendChild(notification);
        
        // Автоматично видаляємо через 5 секунд
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
    
    // Створити контейнер для сповіщень
    createNotificationsContainer() {
        const container = document.createElement('div');
        container.id = 'notifications-container';
        container.className = 'notifications-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
        return container;
    }
    
    // Ініціалізація сторінки категорій
    initCategoriesPage() {
        console.log('Ініціалізація сторінки категорій');
        
        // Фільтрація категорій
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                
                const filter = btn.dataset.filter;
                this.applyFilter(filter);
            });
        });
        
        // Пошук в категоріях
        const searchInput = document.getElementById('categorySearch');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.searchCategories(e.target.value);
            });
        }
    }
    
    // Ініціалізація сторінки пошуку
    initSearchPage() {
        console.log('Ініціалізація сторінки пошуку');
        
        const searchForm = document.getElementById('searchForm');
        if (searchForm) {
            searchForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.performSearch();
            });
        }
    }
    
    // Ініціалізація сторінки додавання оголошення
    initAddAdPage() {
        console.log('Ініціалізація сторінки додавання оголошення');
        
        // Завантаження зображень
        const imageInputs = document.querySelectorAll('input[type="file"][accept*="image"]');
        imageInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                this.handleImagePreview(e.target.files, input);
            });
        });
    }
    
    // Ініціалізація сторінки профілю
    initProfilePage() {
        console.log('Ініціалізація сторінки профілю');
    }
    
    // Застосування фільтру
    applyFilter(filter) {
        const items = document.querySelectorAll('.filterable-item');
        items.forEach(item => {
            if (filter === 'all' || item.dataset.category === filter) {
                item.style.display = 'block';
                item.style.animation = 'fadeIn 0.5s ease';
            } else {
                item.style.display = 'none';
            }
        });
    }
    
    // Пошук в категоріях
    searchCategories(query) {
        const items = document.querySelectorAll('.filterable-item');
        const lowerQuery = query.toLowerCase();
        
        items.forEach(item => {
            const text = item.textContent.toLowerCase();
            if (text.includes(lowerQuery) || lowerQuery === '') {
                item.style.display = 'block';
                item.style.animation = 'fadeIn 0.5s ease';
            } else {
                item.style.display = 'none';
            }
        });
    }
    
    // Виконання пошуку
    async performSearch() {
        // Реалізація пошуку
        console.log('Виконання пошуку');
    }
    
    // Попередній перегляд зображень
    handleImagePreview(files, input) {
        const previewContainer = input.parentNode.querySelector('.image-preview') || 
                                this.createPreviewContainer(input);
        
        previewContainer.innerHTML = '';
        
        Array.from(files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const imageDiv = document.createElement('div');
                    imageDiv.className = 'preview-image position-relative d-inline-block m-2';
                    imageDiv.innerHTML = `
                        <img src="${e.target.result}" class="img-thumbnail" 
                             style="width: 100px; height: 100px; object-fit: cover;">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle" 
                                style="width: 20px; height: 20px; padding: 0; transform: translate(50%, -50%);"
                                onclick="this.parentNode.remove()">
                            <i class="fas fa-times" style="font-size: 10px;"></i>
                        </button>
                    `;
                    previewContainer.appendChild(imageDiv);
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Створення контейнера для попереднього перегляду
    createPreviewContainer(input) {
        const container = document.createElement('div');
        container.className = 'image-preview mt-2';
        input.parentNode.insertBefore(container, input.nextSibling);
        return container;
    }
}

// Ініціалізація SPA після завантаження DOM
document.addEventListener('DOMContentLoaded', () => {
    window.spa = new ClassifiedsSPA();
});

// CSS стилі для SPA
const spaStyles = `
    .spa-loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.9);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 10000;
    }
    
    .spa-loader-content {
        text-align: center;
    }
    
    .notification-toast {
        min-width: 300px;
        margin-bottom: 0.5rem;
        animation: slideInRight 0.3s ease;
    }
    
    .notifications-container {
        max-width: 400px;
    }
    
    #main-content {
        transition: opacity 0.3s ease, transform 0.3s ease;
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
`;

// Додаємо стилі до документа
const styleSheet = document.createElement('style');
styleSheet.textContent = spaStyles;
document.head.appendChild(styleSheet);