/**
 * GameMarket Pro - Основной JavaScript модуль
 * Современный игровой маркетплейс
 */
class GameMarketApp {
    constructor() {
        this.theme = localStorage.getItem('theme') || 'dark';
        this.initTheme();
        this.setupEventListeners();
        this.notification = new NotificationManager();
        this.api = new ApiManager();
    }

    init() {
        console.log('🎮 GameMarket Pro запущен');
        this.checkAuthentication();
        this.setupMobileMenu();
        this.setupUserMenu();
        this.loadUnreadMessages();
        this.setupAutoRefresh();
    }

    // ===== УПРАВЛЕНИЕ ТЕМОЙ =====
    initTheme() {
        document.documentElement.setAttribute('data-theme', this.theme);
        this.updateThemeIcon();
    }

    toggleTheme() {
        this.theme = this.theme === 'dark' ? 'light' : 'dark';
        localStorage.setItem('theme', this.theme);
        document.documentElement.setAttribute('data-theme', this.theme);
        this.updateThemeIcon();
        
        // Анимация переключения
        document.body.style.transition = 'background-color 0.3s ease, color 0.3s ease';
        setTimeout(() => {
            document.body.style.transition = '';
        }, 300);
    }

    updateThemeIcon() {
        const toggle = document.getElementById('theme-toggle');
        if (toggle) {
            const sunIcon = toggle.querySelector('.icon-sun');
            const moonIcon = toggle.querySelector('.icon-moon');
            
            if (this.theme === 'dark') {
                sunIcon?.classList.add('hidden');
                moonIcon?.classList.remove('hidden');
            } else {
                sunIcon?.classList.remove('hidden');
                moonIcon?.classList.add('hidden');
            }
        }
    }

    // ===== НАВИГАЦИЯ =====
    setupEventListeners() {
        // Переключатель темы
        document.addEventListener('click', (e) => {
            if (e.target.closest('#theme-toggle')) {
                e.preventDefault();
                this.toggleTheme();
            }
        });

        // Глобальные клавиши
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + K - поиск
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                this.openSearch();
            }
            
            // ESC - закрыть модальные окна
            if (e.key === 'Escape') {
                this.closeModals();
            }
        });
    }

    setupMobileMenu() {
        const mobileButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        
        if (mobileButton && mobileMenu) {
            mobileButton.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
            
            // Закрытие при клике вне меню
            document.addEventListener('click', (e) => {
                if (!mobileButton.contains(e.target) && !mobileMenu.contains(e.target)) {
                    mobileMenu.classList.add('hidden');
                }
            });
        }
    }

    setupUserMenu() {
        const userButton = document.getElementById('user-menu-button');
        const userMenu = document.getElementById('user-menu');
        
        if (userButton && userMenu) {
            userButton.addEventListener('click', () => {
                userMenu.classList.toggle('hidden');
            });
            
            // Закрытие при клике вне меню
            document.addEventListener('click', (e) => {
                if (!userButton.contains(e.target) && !userMenu.contains(e.target)) {
                    userMenu.classList.add('hidden');
                }
            });
        }
    }

    // ===== АУТЕНТИФИКАЦИЯ =====
    checkAuthentication() {
        // Проверяем валидность сессии
        const sessionTime = localStorage.getItem('session_time');
        if (sessionTime) {
            const now = Date.now();
            const sessionAge = now - parseInt(sessionTime);
            const maxAge = 24 * 60 * 60 * 1000; // 24 часа
            
            if (sessionAge > maxAge) {
                this.logout();
            }
        }
    }

    async logout() {
        try {
            await fetch('/logout', { method: 'POST' });
            localStorage.removeItem('session_time');
            window.location.href = '/';
        } catch (error) {
            console.error('Ошибка выхода:', error);
        }
    }

    // ===== СООБЩЕНИЯ =====
    async loadUnreadMessages() {
        try {
            const response = await this.api.get('/api/messages/unread');
            const data = await response.json();
            
            if (data.count > 0) {
                this.showUnreadBadge(data.count);
            }
        } catch (error) {
            console.error('Ошибка загрузки сообщений:', error);
        }
    }

    showUnreadBadge(count) {
        const badges = document.querySelectorAll('.unread-badge');
        badges.forEach(badge => {
            badge.textContent = count > 99 ? '99+' : count;
            badge.classList.remove('hidden');
        });
    }

    // ===== АВТООБНОВЛЕНИЕ =====
    setupAutoRefresh() {
        // Обновляем непрочитанные сообщения каждые 30 секунд
        setInterval(() => {
            if (this.isUserOnline()) {
                this.loadUnreadMessages();
            }
        }, 30000);
        
        // Проверяем активность пользователя
        this.lastActivity = Date.now();
        document.addEventListener('mousemove', () => {
            this.lastActivity = Date.now();
        });
        document.addEventListener('keypress', () => {
            this.lastActivity = Date.now();
        });
    }

    isUserOnline() {
        const now = Date.now();
        return (now - this.lastActivity) < 5 * 60 * 1000; // 5 минут неактивности
    }

    // ===== МОДАЛЬНЫЕ ОКНА =====
    openSearch() {
        // TODO: Реализовать глобальный поиск
        console.log('Открытие поиска');
    }

    closeModals() {
        // Закрываем все открытые модальные окна
        document.querySelectorAll('.modal').forEach(modal => {
            modal.classList.add('hidden');
        });
        
        // Закрываем меню
        document.getElementById('mobile-menu')?.classList.add('hidden');
        document.getElementById('user-menu')?.classList.add('hidden');
    }

    // ===== УТИЛИТЫ =====
    formatPrice(price, currency = 'RUB') {
        const symbols = {
            'RUB': '₽',
            'USD': '$',
            'EUR': '€'
        };
        
        return `${parseFloat(price).toLocaleString('ru-RU')} ${symbols[currency] || currency}`;
    }

    formatDate(date) {
        return new Date(date).toLocaleDateString('ru-RU', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    formatNumber(num) {
        return num.toLocaleString('ru-RU');
    }

    // ===== ОБРАБОТКА ОШИБОК =====
    handleError(error, context = '') {
        console.error(`Ошибка ${context}:`, error);
        this.notification.show(`Произошла ошибка: ${error.message}`, 'error');
    }
}

// ===== МЕНЕДЖЕР УВЕДОМЛЕНИЙ =====
class NotificationManager {
    constructor() {
        this.container = document.getElementById('notifications');
        if (!this.container) {
            this.container = this.createContainer();
        }
    }

    createContainer() {
        const container = document.createElement('div');
        container.id = 'notifications';
        container.className = 'fixed top-4 right-4 z-50 space-y-2';
        document.body.appendChild(container);
        return container;
    }

    show(message, type = 'info', duration = 5000) {
        const notification = this.createNotification(message, type);
        this.container.appendChild(notification);

        // Автоматическое скрытие
        setTimeout(() => {
            this.hide(notification);
        }, duration);

        // Клик для закрытия
        notification.addEventListener('click', () => {
            this.hide(notification);
        });

        return notification;
    }

    createNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        
        const icons = {
            success: '✓',
            error: '✗',
            warning: '⚠',
            info: 'ℹ'
        };

        notification.innerHTML = `
            <div class="flex items-center">
                <span class="text-lg mr-3">${icons[type] || icons.info}</span>
                <span class="flex-1">${message}</span>
                <button class="ml-3 text-lg opacity-70 hover:opacity-100">×</button>
            </div>
        `;

        return notification;
    }

    hide(notification) {
        notification.style.animation = 'slideOut 0.3s ease-out forwards';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }

    success(message, duration) {
        return this.show(message, 'success', duration);
    }

    error(message, duration) {
        return this.show(message, 'error', duration);
    }

    warning(message, duration) {
        return this.show(message, 'warning', duration);
    }

    info(message, duration) {
        return this.show(message, 'info', duration);
    }
}

// ===== МЕНЕДЖЕР API =====
class ApiManager {
    constructor() {
        this.baseUrl = '';
        this.defaultHeaders = {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };
    }

    async request(url, options = {}) {
        const config = {
            headers: { ...this.defaultHeaders, ...options.headers },
            ...options
        };

        try {
            const response = await fetch(this.baseUrl + url, config);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            return response;
        } catch (error) {
            console.error('API запрос не выполнен:', error);
            throw error;
        }
    }

    async get(url, headers = {}) {
        return this.request(url, { method: 'GET', headers });
    }

    async post(url, data = {}, headers = {}) {
        return this.request(url, {
            method: 'POST',
            body: JSON.stringify(data),
            headers
        });
    }

    async put(url, data = {}, headers = {}) {
        return this.request(url, {
            method: 'PUT',
            body: JSON.stringify(data),
            headers
        });
    }

    async delete(url, headers = {}) {
        return this.request(url, { method: 'DELETE', headers });
    }

    // Загрузка файлов
    async upload(url, formData, onProgress = null) {
        return new Promise((resolve, reject) => {
            const xhr = new XMLHttpRequest();
            
            if (onProgress) {
                xhr.upload.addEventListener('progress', (e) => {
                    if (e.lengthComputable) {
                        const percentComplete = (e.loaded / e.total) * 100;
                        onProgress(percentComplete);
                    }
                });
            }

            xhr.addEventListener('load', () => {
                if (xhr.status >= 200 && xhr.status < 300) {
                    resolve(xhr.response);
                } else {
                    reject(new Error(`Upload failed: ${xhr.statusText}`));
                }
            });

            xhr.addEventListener('error', () => {
                reject(new Error('Upload failed'));
            });

            xhr.open('POST', this.baseUrl + url);
            xhr.send(formData);
        });
    }
}

// ===== УТИЛИТЫ ДЛЯ ФОРМ =====
class FormValidator {
    static rules = {
        required: (value) => value.trim() !== '',
        email: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
        min: (value, length) => value.length >= length,
        max: (value, length) => value.length <= length,
        pattern: (value, regex) => new RegExp(regex).test(value)
    };

    static validate(field, rules) {
        const value = field.value;
        const errors = [];

        for (const [rule, param] of Object.entries(rules)) {
            if (this.rules[rule]) {
                if (!this.rules[rule](value, param)) {
                    errors.push(this.getErrorMessage(rule, param));
                }
            }
        }

        return errors;
    }

    static getErrorMessage(rule, param) {
        const messages = {
            required: 'Поле обязательно для заполнения',
            email: 'Введите корректный email',
            min: `Минимум ${param} символов`,
            max: `Максимум ${param} символов`,
            pattern: 'Неверный формат'
        };

        return messages[rule] || 'Ошибка валидации';
    }
}

// ===== ИНИЦИАЛИЗАЦИЯ =====
// Создаем глобальный экземпляр приложения
const App = new GameMarketApp();

// Добавляем анимацию slideOut для уведомлений
const style = document.createElement('style');
style.textContent = `
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Экспортируем для использования в других модулях
window.App = App;
window.NotificationManager = NotificationManager;
window.ApiManager = ApiManager;
window.FormValidator = FormValidator;