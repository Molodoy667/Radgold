/**
 * GameMarket Pro - Main JavaScript Application
 */

class App {
    constructor() {
        this.initializeComponents();
        this.bindEvents();
    }

    static init() {
        window.app = new App();
    }

    initializeComponents() {
        this.initThemeToggle();
        this.initMobileMenu();
        this.initUserMenu();
        this.initNotifications();
    }

    bindEvents() {
        // Глобальные обработчики событий
        document.addEventListener('click', (e) => {
            // Закрытие выпадающих меню при клике вне их
            this.closeDropdownsOnOutsideClick(e);
        });

        // CSRF токен для AJAX запросов
        this.setupAjaxCSRF();
    }

    // Переключатель темы
    initThemeToggle() {
        const themeToggle = document.getElementById('theme-toggle');
        if (!themeToggle) return;

        // Загружаем сохраненную тему
        const savedTheme = localStorage.getItem('theme') || 'dark';
        document.documentElement.setAttribute('data-theme', savedTheme);

        themeToggle.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            this.showNotification('Тема переключена', 'success');
        });
    }

    // Мобильное меню
    initMobileMenu() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        
        if (!mobileMenuButton || !mobileMenu) return;

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // Меню пользователя
    initUserMenu() {
        const userMenuButton = document.getElementById('user-menu-button');
        const userMenu = document.getElementById('user-menu');
        
        if (!userMenuButton || !userMenu) return;

        userMenuButton.addEventListener('click', (e) => {
            e.stopPropagation();
            userMenu.classList.toggle('hidden');
        });
    }

    // Закрытие выпадающих меню
    closeDropdownsOnOutsideClick(e) {
        const userMenu = document.getElementById('user-menu');
        const userMenuButton = document.getElementById('user-menu-button');
        
        if (userMenu && !userMenu.contains(e.target) && !userMenuButton.contains(e.target)) {
            userMenu.classList.add('hidden');
        }
    }

    // Система уведомлений
    initNotifications() {
        this.notificationsContainer = document.getElementById('notifications');
    }

    showNotification(message, type = 'info', duration = 3000) {
        if (!this.notificationsContainer) return;

        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <span class="notification-message">${message}</span>
                <button class="notification-close">&times;</button>
            </div>
        `;

        // Стили для уведомления
        notification.style.cssText = `
            background: ${this.getNotificationColor(type)};
            color: white;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transform: translateX(100%);
            transition: transform 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-width: 300px;
        `;

        // Добавляем в контейнер
        this.notificationsContainer.appendChild(notification);

        // Анимация появления
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 10);

        // Обработчик закрытия
        const closeButton = notification.querySelector('.notification-close');
        closeButton.addEventListener('click', () => {
            this.removeNotification(notification);
        });

        // Автоматическое удаление
        if (duration > 0) {
            setTimeout(() => {
                this.removeNotification(notification);
            }, duration);
        }
    }

    removeNotification(notification) {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }

    getNotificationColor(type) {
        const colors = {
            success: '#10b981',
            error: '#ef4444',
            warning: '#f59e0b',
            info: '#3b82f6'
        };
        return colors[type] || colors.info;
    }

    // CSRF для AJAX
    setupAjaxCSRF() {
        const token = document.querySelector('meta[name="csrf-token"]');
        if (token) {
            this.csrfToken = token.getAttribute('content');
        }
    }

    // AJAX helper
    async ajax(url, options = {}) {
        const defaultOptions = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...(this.csrfToken && { 'X-CSRF-TOKEN': this.csrfToken })
            }
        };

        const finalOptions = { ...defaultOptions, ...options };

        try {
            const response = await fetch(url, finalOptions);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('AJAX Error:', error);
            this.showNotification('Произошла ошибка при выполнении запроса', 'error');
            throw error;
        }
    }

    // Логаут
    async logout() {
        try {
            await this.ajax('/logout', {
                method: 'POST'
            });
            
            this.showNotification('Вы успешно вышли из системы', 'success');
            
            setTimeout(() => {
                window.location.href = '/';
            }, 1000);
        } catch (error) {
            this.showNotification('Ошибка при выходе из системы', 'error');
        }
    }
}

// Глобальные функции
window.logout = () => window.app?.logout();

// Инициализация при загрузке DOM
document.addEventListener('DOMContentLoaded', () => {
    App.init();
});

// Экспорт для модулей (если нужно)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = App;
}